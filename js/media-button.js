/**
 * Add SquareOff Modal.
 */

/* global soData:true, wp:true, jQuery:true, Backbone:true, _:true, tinyMCE:true, send_to_editor:true */

( function( window, $, Backbone, _ ) {

	/**
	 * SquareOff Model
	 */
	var SoSquareOff = Backbone.Model.extend({

		url: window.soData.endpoints.squareoffs,

		defaults: function() {
			return {
				'uuid':           '',
				'external_id':    '',
				'question':       '',
				'size':           'wide',
				'side_1_title':   '',
				'side_2_title':   '',
				'side_1_defense': '',
				'side_2_defense': '',
				'side_1_photo':   '',
				'side_2_photo':   '',
				'cover_photo':    '',
				'end_date':       '',
				'category_uuid':  '',
				'margin':  '0',
			};
		},

		sync: function( method, collection, options ) {
			options = options || {};
			options.beforeSend = function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', soData.nonce );
			};
			return Backbone.Model.prototype.sync.apply( this, arguments );
		},

		getShortcode: function() {
			// Exit if no ID.
			if ( ! this.get( 'external_id' )) {
				return;
			}
			var template = wp.media.template( 'squareoffs-tinymce-preview' );
			var data = {
				id:      this.get( 'external_id' ) || '',
				size:     this.get( 'size' ) || '',
				iconSrc: soData.iconSrc,
				margin: this.get('margin'),
			};

			var fakeShortCode = template( data );
			var allowedSizes = [ 'small', 'medium', 'wide' ],
				// template     = '[squareoffs_embed id="{{ id }}" size="{{ size }}"]',
				template = fakeShortCode,
				shortcode;

			// Validate size.
			if ( allowedSizes.indexOf( this.get( 'size' ) ) < 0 ) {
				this.set( 'size', 'wide' );
			}

			// Update shortcode template with data.
			shortcode = template
				.replace( '{{ id }}', this.get( 'external_id' ) )
				.replace( '{{ size }}', this.get( 'size' ) )
				.replace( '{{ margin }}', this.get( 'margin' ) );

			return shortcode;

		},

		/**
		 * Validate. Is ready for submit.
		 *
		 *
		 */
		isReady: function() {
			console.log("isReady SIZE: ",this.get('size'));
			var ready         = true;
			var requiredProps = [ 'question', 'side_1_title', 'side_2_title', 'category_uuid' ];

			_.each( requiredProps, function( prop ) {
				if ( ! this.get( prop ) || this.get( prop ).length < 1 ) {
					ready = false;
				}
			}.bind( this ));

			return ready;
		},
	});

	/**
	 * SquareOff Collection.
	 */
	var SoSquareOffs = Backbone.Collection.extend({

		model:       SoSquareOff,
		url:         window.soData.endpoints.squareoffs,
		fetchedOnce: false, // Track whether the first fetch has been triggered to avoid dupes.

		/**
		 * Custom sync method.
		 *
		 * Required to set nonce auth header.
		 */
		sync: function( method, collection, options ) {
			options = options || {};
			options.beforeSend = function( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', soData.nonce );
			};
			return Backbone.Model.prototype.sync.apply( this, arguments );
		},

	});

	/**
	 * Modal Model.
	 *
	 * This is the modal controller. Manages options, state and strings.
	 */
	var SoModal = Backbone.Model.extend({

		/**
		 * Set the defaults attributes.
		 *
		 * Notes:
		 * Editor ID is the ID of the TinyMCE editor that this button corresponds to.
		 * state can be 'new' or 'existing' or the default state will be used.
		 *
		 * @return object Attributes.
		 */
		defaults: function() {
			return {
				// TinyMCE instance ID.
				editorId:         'content',
				// State. Options: 'new' or 'existing'
				state:            null,
				// Squareoff to insert. Instance of SoSquareOff.
				squareoff:        null,
				// Strings.
				title:            'Insert SquareOff',
				buttonCloseText:  'Close insert SquareOff modal',
				buttonSubmitText: 'Insert into post',
				buttonNewText:    'Create new SquareOff',
				buttonSelectText: 'Select existing SquareOff',
				// Functions
				insertCallback:   function( squareoff ) {
					send_to_editor( squareoff.getShortcode() );
				},
			};
		},
	});

	/**
	 * The SO Modal View.
	 */
	var SoModalView = wp.Backbone.View.extend({

		template: wp.template( 'squareoffs-modal-insert' ),
		model:    SoModal,

		events: {
			'click .media-modal-close': 'close',
			'keydown':                  'handleKeydown',
		},

		/**
		 * Init.
		 */
		initialize: function() {
			this.listenTo( this.model, 'destroy', this.remove );
			this.listenTo( this.model, 'change:state', this.renderContent );
		},

		/**
		 * Render.
		 *
		 * Note that the content section is a subview, handled by this.renderContent();
		 */
		render: function() {

			var data = this.model.toJSON();
			data.cid = this.cid;

			// Hide, render.
			this.$el.hide()
				.attr( 'aria-hidden', true )
				.html( this.template( data ) );

			// Render content section.
			this.renderContent();

			return this;

		},

		/**
		 * Render the content section subview.
		 *
		 * The content view rendered depends on the current state.
		 */
		renderContent: function() {

			var contentView,
				state = this.model.get( 'state' );

			if ( state === 'new' ) {
				contentView = new SoModalContentViewNew( { model: this.model } );
				contentView.on( 'submit', this.insertShortcode.bind( this ) );
			} else if ( state === 'existing' ) {
				contentView = new SoModalContentViewExisting( { model: this.model } );
				contentView.on( 'submit', this.insertShortcode.bind( this ) );
			} else if ( state === 'login' ) {
				contentView = new SoModalContentViewLogin( { model: this.model } );
			} else {
				if(window.soVars.api_connected){
					contentView = new SoModalContentViewDefault( { model: this.model } );	
				}else{
					contentView = new SoModalContentViewDisconnected( { model: this.model } );
				}
			}

			this.views.set( '.media-frame-content', contentView );

			// Ensure modal keeps focus.
			this.$( '.media-modal' ).focus();

		},

		/**
		 * Open the modal.
		 */
		open: function() {

			this.$el.show()
				.attr( 'aria-hidden', 'false' );

			this.$( '.media-modal' ).focus();

		},

		/**
		 * Close the modal.
		 */
		close: function() {

			this.$el.hide()
				.attr( 'aria-hidden', 'true' );

			this.model.set({
				'state':     null,
				'squareoff': null,
			});
		},

		handleKeydown: function( e ) {

			// Close on escape.
			if ( e.which === 27 ) {
				this.close();
			}

			// Look for the tab key.
			if ( e.keyCode !== 9 ) {
				return;
			}

			// Skip the file input added by Plupload.
			var tabbables = this.$( ':tabbable' );

			// Keep tab focus within media modal while it's open
			if ( tabbables.last()[0] === e.target && ! e.shiftKey ) {
				tabbables.first().focus();
				return false;
			} else if ( tabbables.first()[0] === e.target && e.shiftKey ) {
				tabbables.last().focus();
				return false;
			}
		},

		/**
		 * Insert the shortcode current SquareOff.
		 */
		insertShortcode: function() {
			var squareoff = this.model.get( 'squareoff' );
			var callback  = this.model.get( 'insertCallback' ) || this.model.defaults().insertCallback;

			if ( squareoff && callback ) {
				callback( squareoff );
			}

			this.close();
		},

	});

	/**
	 * Generic modal content view.
	 *
	 * Content views should extend this.
	 */
	var SoModalContentView = wp.Backbone.View.extend({

		model: SoModal,

		render: function() {
			var data = this.model.toJSON();
			this.$el.html( this.template( data ) );
			return this;
		},

	});

	/**
	 * Default modal content view.
	 *
	 * Allows the user to select between adding
	 * an existing SquareOff or creating a new one
	 */
	var SoModalContentViewDefault = SoModalContentView.extend({

		template:  wp.template( 'squareoffs-modal-insert-content-default' ),
		className: 'squareoffs-modal-insert-content-default',

		events: {
			'click .squareoffs-select-new':       'setStateNew',
			'click .squareoffs-select-squareoff': 'setStateExisting',
		},

		/**
		 * Set the modal state to 'new'
		 */
		setStateNew: function( e ) {
			e.preventDefault();
			this.model.set( 'state', 'new' );
		},

		/**
		 * Set the modal state to 'existing'
		 */
		setStateExisting: function( e ) {
			e.preventDefault();
			this.model.set( 'state', 'existing' );
		},

	});
	/**
	 * Disconnected modal content view.
	 *
	 * Allows the user to select between Login or Create an account
	 */
	var SoModalContentViewDisconnected = SoModalContentView.extend({

		template:  wp.template( 'squareoffs-modal-insert-content-disconnected' ),
		className: 'squareoffs-modal-insert-content-default',

		events: {
			'click .squareoffs-select-login':  'setStateLogin',
			'click .squareoffs-select-create': 'setStateCreate',
		},

		/**
		 * Set the modal state to 'new'
		 */
		setStateLogin: function( e ) {
			e.preventDefault();
			this.model.set( 'state', 'login' );
		},

	});
	/**
	 * Login modal content view.
	 *
	 * Allows the user to connect to SquareOffs
	 */
	var SoModalContentViewLogin = SoModalContentView.extend({

		template:  wp.template( 'squareoffs-modal-insert-content-login' ),
		className: 'squareoffs-modal-insert-content-default',

		events: {
			'click .squareoffs-select-loginSubmit':  'submitLogin',
			'click .squareoffs-select-loginCancel': 'setStateLoginCancel',
		},

		submitLogin: async function( e ) {
			e.preventDefault();
			var form_data = new FormData();
	    	let em = jQuery("#soEmail").val();
	    	let pa = jQuery("#soPassword").val();
	    	if(!em || !pa) return false;
		    form_data.append('soEmail', em);
		    form_data.append('soPassword', pa);
			form_data.append('action', 'soConnect');
	    	console.log("connecting...");
	    	await fetch( window.ajaxurl, {
    	            method: 'POST',
    	            body: form_data
    	        })
		    	.then(response => response.json())
	  			.then(data => {
	  				console.log(data);
	  				if(data.success == 1){
	    				window.soVars.api_connected = true;
	    				jQuery("div#notif").html('<div class="notice notice-success"><p>Successfully connected to SquareOffs...</p></div>');
	    				jQuery("#logincont").hide();
	  					setTimeout(
							function() {
								this.model.set( 'state', '' );
							}
							.bind(this),
							1500
						);
	  				}else{
	  					window.soVars.api_connected = false;
	  					jQuery("div#notif").html('<div class="notice notice-error"><p><b>Uh oh!</b> Invalid email or password. Go to <a href="https://squareoffs.com/" target="_blank">SquareOffs.com</a> to reset your password or to create an account.</p></div>');
	  				}
	  			});
		},

		setStateLoginCancel: function( e ) {
			e.preventDefault();
			this.model.set( 'state', '' );
		},

	});

	function doneConnectingSquareOffs(data){

	}

	/**
	 * Create new SquareOff View.
	 */
	var SoModalContentViewNew = SoModalContentView.extend({

		template: wp.template( 'squareoffs-modal-insert-content-new' ),

		currentSection: 0,

		events: {
			'click .squareoffs-form-back': 'prevFormSection',
			'click .squareoffs-form-next': 'nextFormSection',
			'keyup input':                 'updateSquareOff',
			'change input':                'updateSquareOff',
			'change input.soIMargin':      'marginChanged',
			'change select':               'updateSquareOff',
			'change textarea':             'updateSquareOff',
			'click #clearEndDate':         'clearEndDate',
			'click .modal-submit':         'submit',
		},

		initialize: function() {
			this.listenTo( this.model, 'change:squareoff', this.updateSubmitButtonState );
		},

		clearEndDate: function() {
			jQuery("#clearEndDate").parent().find("input").val("");
		},

		marginChanged: function() {
			jQuery("input.soIMargin").each(function(i,e){
				var mv = jQuery(this).val();
				var iv = parseInt(mv);
				console.log('iv', iv);
				if(!iv) iv = 0;
				if(mv.indexOf('%')>0) iv += '%';
				else iv += 'px';
				jQuery(this).val(iv); 
			});
			this.updateSquareOff();

			let soMargins = {};
			soMargins.top = jQuery("input.soIMargin[name='marginTop']").val();
			soMargins.right = jQuery("input.soIMargin[name='marginRight']").val();
			soMargins.bottom = jQuery("input.soIMargin[name='marginBottom']").val();
			soMargins.left = jQuery("input.soIMargin[name='marginLeft']").val();
			console.log('soMargins',soMargins);
			window.soVars.soMargins = soMargins;
			var form_data = new FormData();
			form_data.append('action', 'soUpdateMargins');
			form_data.append('soMargins', JSON.stringify(soMargins));
    		fetch( window.ajaxurl, {
	            method: 'POST',
	            body: form_data
	        });

		},

		render: function() {

			var data = this.model.toJSON();

			data.cid        = this.cid;
			data.categories = soData.categories || [];
			data.selectedSize = 'wide';
			if(window.soVars.hasOwnProperty('soMargins')){
				data.marginTop = (window.soVars.soMargins.top?window.soVars.soMargins.top:'0px');
				data.marginRight = (window.soVars.soMargins.right?window.soVars.soMargins.right:'0px');
				data.marginBottom = (window.soVars.soMargins.bottom?window.soVars.soMargins.bottom:'0px');
				data.marginLeft = (window.soVars.soMargins.left?window.soVars.soMargins.left:'0px');
			}


			this.$el.html( this.template( data ) );

			this.updateCurrentSection();
			this.initImageButtons();

			if ( 'soDatePickerInit' in window ) {
				window.soDatePickerInit( this.$el );
			}

			return this;
		},

		submit: function() {

			var $loading = this.$( '.squareoffs-form-controls .squareoffs-loading' );
			var $controls = this.$el.find( '.squareoffs-form-controls' ).find( '.squareoffs-form-back, squareoffs-form-next, .modal-submit' );

			$loading.toggleClass( 'is-active', true );
			$controls.hide();

			this.model.get( 'squareoff' ).save( null, {

				/**
				 * On submit, trigger modal submit action.
				 */
				success: function() {
					this.trigger( 'submit', this.model.get( 'squareoff' ) );
				}.bind( this ),

				/**
				 * On error, handle messages.
				 */
				error: function( squareoff, response ) {

					// Remove old errors.
					this.$el.find( '.notice' ).remove();

					// If new errors, Show them.
					if ( response && ( 'responseJSON' in response ) && ( 'message' in response.responseJSON )) {
						var $p      = $( '<p />', { text: response.responseJSON.message } );
						var $notice = $( '<div />', { class: 'notice error inline' } );
						this.$el.prepend( $notice.append( $p ) );
					}

					// Toggle controls.
					$loading.toggleClass( 'is-active', false );
					$controls.show();

				}.bind( this ),
			});
		},

		/**
		 * Refresh the form view to reflect the current Section.
		 *
		 * Sections are shown/hidden.
		 */
		updateCurrentSection: function() {

			var squareoff = this.model.get( 'squareoff' );
			var $sections = this.$el.children( '.squareoffs-form-section' );

			if ( this.currentSection < 0 ) {
				this.currentSection = 0;
			} else if ( this.currentSection >= $sections.length ) {
				this.currentSection = $sections.length;
			}

			$sections.not( ':eq( ' + this.currentSection + ' )' )
				.hide()
				.attr( 'aria-hidden', 'true' );

			$sections.eq( this.currentSection )
				.show()
				.attr( 'aria-hidden', 'false' );

			// Update state of buttons.
			$( '.squareoffs-form-back', this.$el ).toggle( this.currentSection !== 0 );
			$( '.squareoffs-form-next', this.$el ).toggle( this.currentSection !== ( $sections.length - 1 ) );
			$( '.modal-submit', this.$el ).toggle( this.currentSection === ( $sections.length - 1 ) );

			// Show error message.
			if ( squareoff && ! squareoff.isReady() && ( this.currentSection === ( $sections.length - 1 ) ) ) {
				$( '.squareoffs-form-error', this.$el ).show();
			} else {
				$( '.squareoffs-form-error', this.$el ).hide();
			}

			// Render help text.
			if ( squareoff ) {

				var title1 = squareoff.get( 'side_1_title' );
				var title2 = squareoff.get( 'side_2_title' );

				if ( title1.length > 0 ) {
					$( '.squareoffs-new-defend-1-desc', this.$el )
						.text( 'Defend "' + title1 + '"' );
				}

				if ( title2.length > 0 ) {
					$( '.squareoffs-new-defend-2-desc', this.$el )
						.text( 'Defend "' + title2 + '"' );
				}

			}

			// Keyboard/accessibility.
			$sections.eq( this.currentSection )
				.find( '.button, input, select, textarea' )
				.first()
				.focus();
		},

		initImageButtons: function() {
			var me = this;
				$.each( this.$el.find( '.squareoffs-image' ), function() {

				var $el = $( this );

				$el.on( 'click', function() {
					var $target  = $el.closest( '.squareoffs-form-row' ).find( 'input[type=hidden]' );
					var $preview = $el.closest( '.squareoffs-form-row' ).find( '.squareoffs-form-image-preview' );
					var id       = $el.attr( 'id' );

					window.SO.initMediaUploader( id, function() {
						window.customSOImageEditor = false;
						var attachment = this.first().get( 'selection' ).first().toJSON();
						$preview.html( "" );
						$preview.html( $( '<img />', {
							src: attachment.url,
							alt: '',
							load : function(){
								if($preview.attr("id") == "cover"){
									if(attachment.mime == "image/gif"){
										$target.val( attachment.url ).trigger( 'change' );
									}else{
										const cropper = new Cropper(this, {
											aspectRatio: 16 / 9,
											viewMode:1,
											crop(event) {
												let croppped = cropper.getCroppedCanvas().toDataURL(attachment.mime);
												$target.val(croppped).trigger("change");
											},
										});
									}
								}else{
									$target.val( attachment.url ).trigger( 'change' );
								}
							}
						}));
					});
				});

			});
		},

		/**
		 * Go back to the previous section.
		 */
		prevFormSection: function() {
			this.currentSection -= 1;
			this.updateCurrentSection();
		},

		/**
		 * Advance to the next section.
		 */
		nextFormSection: function() {
			this.currentSection += 1;
			this.updateCurrentSection();
		},

		/**
		 * Update the current squareoff with data from the form.
		 */
		updateSquareOff: function() {

			var properties = [
				'question',
				'size',
				'side_1_title',
				'side_2_title',
				'side_1_defense',
				'side_2_defense',
				'side_1_photo',
				'side_2_photo',
				'category_uuid',
				'tag_list',
				'cover_photo',
				'margin',
			];

			var args = {};

			_.each( properties, function( property ) {
				var $field = this.$el.find( '[name="' + property + '"]' );
				if ( property == 'size' ) {
					args[ property ] = this.$el.find( '[name="' + property + '"]:checked' ).val();
				}else if ( property == 'margin' ) {
					args[ property ] = this.$el.find( '[name="marginTop"]').val() +' '+ this.$el.find( '[name="marginRight"]').val() +' '+ this.$el.find( '[name="marginBottom"]').val() +' '+ this.$el.find( '[name="marginLeft"]').val();
				}else if ( $field && $field.length ) {
					args[ property ] = $field.val();
				}
			}.bind( this ));

			var date = this.$el.find( '[name="end_date[date]"]' ).val();
			var time = this.$el.find( '[name="end_date[time]"]' ).val();
			if ( typeof( date ) !== "undefined" && date !== "" ) {
				try {
					args.end_date = new Date( date + ' ' + time ).toISOString();
				} catch ( error ) {
					var _date = new Date();
					_date.setFullYear( new Date().getFullYear() + 1 );
					args.end_date = _date.toISOString();
				}
			}

			if ( ! this.model.get( 'squareoff' )) {
				this.model.set( 'squareoff', new SoSquareOff( args ) );
			} else {
				this.model.get( 'squareoff' ).set( args );
			}

			this.updateSubmitButtonState();

		},

		/**
		 * Update the state of the submit button.
		 * Can only be submitted if a valid SquareOff is selected.
		 */
		updateSubmitButtonState: function() {

			var squareoff = this.model.get( 'squareoff' );

			if ( squareoff && squareoff.isReady()) {
				$( '.modal-submit', this.$el ).text(this.model.get('buttonSubmitText')).removeAttr( 'disabled' );
			} else {
				$( '.media-submit', this.$el ).text(this.model.get('buttonSubmitText')).attr( 'disabled', 'disabled' );
			}

		},

	});

	/**
	 * Select existing SquareOff View.
	 */
	var SoModalContentViewExisting = SoModalContentView.extend({

		template:  wp.template( 'squareoffs-modal-insert-content-existing' ),
		className: 'squareoffs-modal-insert-content-existing',

		events: {
			'keyup input.squareoffs-input-id':      'updateSquareOff',
			// 'change select.squareoffs-select-size': 'updateSquareOff',
			// 'change input[name="size"]': 			'updateSquareOff',
			'change input': 	           			'updateSquareOff',
			'change input.soIMargin': 			    'marginChanged',
			'click .modal-submit':                  'submit',
		},

		/**
		 * Init Select Existing Modal content view.
		 */
		initialize: function() {

			this.squareoffs = squareOffs || new SoSquareOffs();

			this.model.set( 'loading', false );

			this.listenTo( this.squareoffs, 'add', this.addSquareOff );
			this.listenTo( this.model, 'change:loading', this.toggleLoading );
			this.listenTo( this.model, 'change:squareoff', this.updateSubmitButtonState );

		},

		marginChanged: function() {
			jQuery("input.soIMargin").each(function(i,e){
				var mv = jQuery(this).val();
				var iv = parseInt(mv);
				console.log('iv', iv);
				if(!iv) iv = 0;
				if(mv.indexOf('%')>0) iv += '%';
				else iv += 'px';
				jQuery(this).val(iv); 
			});
			this.updateSquareOff();

			let soMargins = {};
			soMargins.top = jQuery("input.soIMargin[name='marginTop']").val();
			soMargins.right = jQuery("input.soIMargin[name='marginRight']").val();
			soMargins.bottom = jQuery("input.soIMargin[name='marginBottom']").val();
			soMargins.left = jQuery("input.soIMargin[name='marginLeft']").val();
			console.log('soMargins',soMargins);
			window.soVars.soMargins = soMargins;
			var form_data = new FormData();
			form_data.append('action', 'soUpdateMargins');
			form_data.append('soMargins', JSON.stringify(soMargins));
    		fetch( window.ajaxurl, {
	            method: 'POST',
	            body: form_data
	        });
		},

		/**
			this.listenTo( this.model, 'change:squareoff', this.updateSubmitButtonState );
		 * Submit the form.
		 *
		 * This form doens't do anything on subnmit,
		 * only trigger the submit event, which allows
		 * parent views to handle any actions.
		 */
		submit: function() {
			this.trigger( 'submit', this.model.get( 'squareoff' ) );
		},

		/**
		 * Update the current squareoff with data from the form.
		 */
		updateSquareOff: function() {
			var $soInput    = this.$( 'input.squareoffs-input-id', this.$el ),
				$sizeSelect = this.$( 'input[name="size"]:checked', this.$el );

			if ( ! this.model.get( 'squareoff' )) {
				this.model.set( 'squareoff', new SoSquareOff() );
			}

			if ( $soInput.length ) {
				this.model.get( 'squareoff' ).set( 'external_id', $soInput.val() );
			}

			if ( $sizeSelect.length ) {
				this.model.get( 'squareoff' ).set( 'size', $sizeSelect.val() );
			}

			let margin  = this.$el.find( '[name="marginTop"]').val() +' '+ this.$el.find( '[name="marginRight"]').val() +' '+ this.$el.find( '[name="marginBottom"]').val() +' '+ this.$el.find( '[name="marginLeft"]').val();

			this.model.get( 'squareoff' ).set( 'margin', margin);

			this.updateSubmitButtonState();
		},

		render: function() {

			var data = this.model.toJSON();

			data.cid          = this.cid;
			data.squareoffs   = this.squareoffs.toJSON();
			data.selectedSize = 'wide';

			if ( this.model.get( 'squareoff' )) {
				data.selectedSize = this.model.get( 'squareoff' ).get( 'size' );
				data.margin = this.model.get( 'squareoff' ).get( 'margin' );
			}

			if(window.soVars.hasOwnProperty('soMargins')){
				data.marginTop = (window.soVars.soMargins.top?window.soVars.soMargins.top:'0px');
				data.marginRight = (window.soVars.soMargins.right?window.soVars.soMargins.right:'0px');
				data.marginBottom = (window.soVars.soMargins.bottom?window.soVars.soMargins.bottom:'0px');
				data.marginLeft = (window.soVars.soMargins.left?window.soVars.soMargins.left:'0px');
			}

			this.$el.html( this.template( data ) );
			this.updateSubmitButtonState();

			return this;
		},

		/**
		 * Toggle the loading state of the squareoff select field.
		 */
		toggleLoading: function() {
			var $spinner = this.$( '.squareoffs-loading' );
			$spinner.toggleClass( 'is-active', this.model.get( 'loading' ) );
		},

		/**
		 * Update the state of the submit button.
		 * Can only be submitted if a valid SquareOff is selected.
		 */
		updateSubmitButtonState: function() {
			if ( this.model.get( 'squareoff' )) {
				if(this.model.get( 'squareoff' ).get('external_id') && this.model.get( 'squareoff' ).get('size')){
					$( '.modal-submit', this.$el ).removeAttr( 'disabled' );
				}else{
					$( '.modal-submit', this.$el ).attr( 'disabled', 'disabled' );
				}
			} else {
				$( '.modal-submit', this.$el ).attr( 'disabled', 'disabled' );
			}
		},

	});

	var modals     = [],
		squareOffs = new SoSquareOffs;

	$( '.squareoffs-insert' ).click( function( e ) {

		var editorId = this.getAttribute( 'data-editor' );

		e.preventDefault();

		if ( ! ( editorId in modals )) {

			var modelArgs = { editorId: editorId, insertCallback: false },
				viewArgs  = { model: new SoModal( modelArgs ) };

			modals[ editorId ] = new SoModalView( viewArgs );

			$( 'body' ).append( modals[ editorId ].render().$el );
		}

		modals[ editorId ].open();

	});

	// Store the edit modal.
	var editModal;

	/**
	 * TinyMCE Preview View.
	 * Should be passed to wp.mce.views.register,
	 *
	 * @type {Object}
	 */
	var soMceView = {

		template: wp.media.template( 'squareoffs-tinymce-preview' ),

		initialize: function() {

			var data = {
				id:      this.shortcode.attrs.named.id || '',
				size:    this.shortcode.attrs.named.size || '',
				iconSrc: soData.iconSrc,
			};

			this.content = this.template( data );

		},

		edit: function( shortcodeString, update ) {

			var squareoff = new SoSquareOff({
				external_id: this.shortcode.attrs.named.id || '',
				size:        this.shortcode.attrs.named.size || '',
			});

			if ( ! editModal ) {
				editModal = new SoModalView({
					model: new SoModal({
						editorId:         tinyMCE.activeEditor.id,
						squareoff:        squareoff,
						state:            'existing',
						title:            'Edit SquareOff',
						buttonSubmitText: 'Update SquareOff',
						insertCallback:   function( _squareoff ) {
							update( _squareoff.getShortcode() );
						},
					}),
				});

				$( 'body' ).append( editModal.render().$el );

			} else {
				editModal.model.set({
					editorId:       tinyMCE.activeEditor.id,
					state:          'existing',
					squareoff:      squareoff,
					insertCallback: function( _squareoff ) {
						update( _squareoff.getShortcode() );
					},
				});
				editModal.render();
			}

			editModal.open();

		},
	};

	wp.mce.views.register( 'squareoffs_embed', soMceView );

}( window, jQuery, Backbone, _ ));
