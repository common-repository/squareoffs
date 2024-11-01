/**
 * Add images to SquareOffs
 */

/* global jQuery:true, wp:true, squareoffs_upload_image:true */

/**
 * Add the squareoffs Embed tinyMCE plugin
 */
jQuery( document ).ready( function( $ ) {
	$("body").on("keyup click",".soInputText textarea, .soInputText input", function(){
		let chars =  $(this).val();
		let max = $(this).attr("maxlength");
		if(!chars){
			$("+span",this).text(max+"/"+max);
		}else{
			let len = parseInt(max) - chars.length;
			$("+span",this).text(len+"/"+max);
		}
	});
	/**
	 * Cache modals for reuse.
	 *
	 * @type {Object}
	 */
	window.mediaUploaders = {};
	window.customSOImageEditor = false;

	window.SO = window.SO || {};

	/**
	 * Create modal and open.
	 *
	 * @param  string id Unique ID.
	 * @param  function selectCallback Called when image is selected.
	 */
	var initMediaUploader = window.SO.initMediaUploader = function( id, selectCallback ) {

		if ( id in mediaUploaders ) {
			mediaUploaders[ id ].open();
			return;
		}

		window.customSOImageEditor = true;

		mediaUploaders[ id ] = wp.media.frames.file_frame = wp.media({
			title:    squareoffs_upload_image.title,
			button:   { text: squareoffs_upload_image.button },
			multiple: false,
			type:"image"
		});

		mediaUploaders[ id ].on( 'select', selectCallback );
		mediaUploaders[ id ].on( 'select', function() {
			$( '#' + id ).blur();
		});

		// mediaUploaders[ id ].on( 'activate', function(a,b,c){
		// 	window.customSOImageEditor = true;			
		
		// 	window.imageEdit.handleCropToolClick= function( postid, nonce, cropButton ) {

		//         var img = $( '#image-preview-' + postid ),
		//             selection = this.iasapi.getSelection();

		//         // Ensure selection is available, otherwise reset to full image.
		//         if ( isNaN( selection.x1 ) ) {
		//             this.setCropSelection( postid, { 'x1': 0, 'y1': 0, 'x2': img.innerWidth(), 'y2': img.innerHeight(), 'width': img.innerWidth(), 'height': img.innerHeight() } );
		//             selection = this.iasapi.getSelection();
		//         }

		//         // If we don't already have a selection, select the entire image.
		//         if ( 0 === selection.x1 && 0 === selection.y1 && 0 === selection.x2 && 0 === selection.y2 ) {
		//             this.iasapi.setSelection( 0, 0, img.innerWidth(), img.innerHeight(), true );
		//             this.iasapi.setOptions( { show: true } );
		//             this.iasapi.update();
		//         } else {

		//             // Otherwise, perform the crop.
		//             imageEdit.crop( postid, nonce , cropButton );
		//         }

		//         if(window.customSOImageEditor == true){
		//         	try{
		//         		console.log("Setting Default Aspect Ratio.");
		//         		$("#imgedit-crop-width-"+postid).val(16);
		// 				$("#imgedit-crop-height-"+postid).val(9).keyup();
		//         	}catch(err){}
		//         }
		//     }

		// } );
		// mediaUploaders[ id ].on( 'router:render', function(){
		// 	$(".media-button").text(squareoffs_upload_image.button).addClass("button-primary");
		// });
		mediaUploaders[ id ].open();

	};

	/**
	 * Define our media buttons.
	 * Map of buttonIds and inputIds.
	 *
	 * @type {Array}
	 */
	var buttons = [
		{
			buttonId:  'squareoffs-image-side-1',
			inputId:   'squareoffs-url-side-1',
			previewId: 'squareoffs-image-side-1-preview',
		},
		{
			buttonId:  'squareoffs-image-side-2',
			inputId:   'squareoffs-url-side-2',
			previewId: 'squareoffs-image-side-2-preview',
		},
		{
			buttonId:  'squareoffs-image-cover',
			inputId:   'squareoffs-url-cover',
			previewId: 'squareoffs-url-cover-preview',
		},
		{
			buttonId:  'squareoffs-settings-account-avatar-select',
			inputId:   'squareoffs-settings-account-avatar',
			previewId: 'squareoffs-settings-account-avatar-preview',
		},
	];

	/**
	 * Loop through defined buttons, and create modal.
	 * Callback to insert URL into the corresponding input.
	 *
	 * @param  int Counter
	 * @param  {{object}} Button args.
	 */
	$.each( buttons, function( i, args ) {

		$( '#' + args.buttonId ).click( function( e ) {

			e.preventDefault();

			initMediaUploader( args.buttonId, function() {
				var attachment = mediaUploaders[ args.buttonId ].state().get( 'selection' ).first().toJSON();
				$( '#' + args.inputId ).val( attachment.url );

				if ( 'previewId' in args ) {

					var $el = $( '#' + args.previewId );
					var url = attachment.url;

					if ( 'medium' in attachment.sizes ) {
						url = attachment.sizes.medium.url;
					}

					$el.html( $( '<img />', {
						src: url,
						alt: 'SquareOffs Avatar',
					}));

				}
			});
		});
	});

});
