/* global jQuery:true soDatePickerInit:true */

( function( $ ) {

	var initColorPickers = function() {
		$( '.squareoffs-color-picker' ).wpColorPicker();
	};

	var init = function() {
		initColorPickers();
		soDatePickerInit();
		try{
			window.soDtablePage = 1;
			var paramid = jQuery('table.squareoffs').attr('param-id');
			window.dtable = jQuery('table.squareoffs').DataTable( {
		        "ajax": ajaxurl+'?action=soGetSquareOffs&page=1&per_page=90',
		        columns: [
				    { 
				    	data: 'title',
			    	 	render: function(data, type, row) {
	                        var htm = '<strong>'
	                        	+'<a class="row-title" href="?page=squareoffs&action=squareoffs-edit&id='+row.uuid+'" aria-label="'+data+'">'+data+'</a>'
	                        +'</strong>'
	                        +'<div class="row-actions"><span class="squareoffs-edit">'
	                        	+'<a href="/wp-admin/?page=squareoffs&id='+row.uuid+'&action=squareoffs-edit">Edit</a> | '
	                        	+'</span><span class="squareoffs-details">'
	                        	+'<a href="/wp-admin/?page=squareoffs&id='+row.uuid+'&action=squareoffs-details">Details</a> | '
	                        	+'</span><span class="squareoffs-delete"><a href="/wp-admin/?page=squareoffs&id='+row.uuid+'&action=squareoffs-delete&nonce='+paramid+'">Delete</a>'
	                        	+'</span></div>';
	 
	                        return htm;
		                }
				    },
				    { data: 'squareoffs_side-1' },
				    { data: 'squareoffs_side-2' },
				    { data: 'squareoffs_user' },
				    { data: 'squareoffs_category' },
				    { data: 'squareoffs_comments' },
				    { data: 'external_id' },
				    { data: 'created_at' },
				],
				"order": [[ 7, "desc" ]],
		    });

			function loadNextPage(){
				if(window.soDtablePage == 0 ) return;
				if(window.soDtablePage == 1 ) window.soDtablePage = 2;
				if(!window.soDTablePages) window.soDTablePages = [];
				if((window.soDTablePages).includes(window.soDtablePage)) return;
				(window.soDTablePages).push(window.soDtablePage);
				jQuery.get( ajaxurl+'?action=soGetSquareOffs&page='+window.soDtablePage+'&per_page=90',{}, function(r){
					var rdata = JSON.parse(r);
// 					console.log('r',rdata.data);
					if(!rdata.meta.next_page) window.soDtablePage = 0;
					else window.soDtablePage = rdata.meta.next_page;
					window.dtable.rows.add(rdata.data).draw();
				});
			}

		    window.dtable.on( 'draw', function () {
			    loadNextPage();
			} );

		}catch(e){};
	};

	$( document ).ready( init );

})( jQuery );
