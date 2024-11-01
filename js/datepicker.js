/* global jQuery:true */

/**
 * Initialize datepickers.
 *
 * Note that the date needs to be set in a pretty unfriendly format. To make this nicer,
 * we hide the date field and creates a new datepicker field with a human readable format.
 * The true, hidden field is then set as the altField which is updated with the real date.
 */
window.soDatePickerInit = function( $container ) {

	var $ = jQuery;

	if ( ! $container ) {
		$container = $( 'body' );
	}

	var formatReadableDate = function( date ) {
		if ( typeof( date ) !== "undefined" && date !== "" ) {
			return $.datepicker.formatDate( 'd M yy', date );
		} else {
			return "";
		}
	};

	$( '.squareoffs-date', $container ).each( function() {
		var $el        = $( this );
		var $dateField = $el.find( '.squareoffs-date-input' );
		var $timeField = $el.find( '.squareoffs-time-input' ).attr("readonly","readonly");
		var dateIso    = $dateField.attr( 'data-date-iso' );
		var date       = "";

		if ( typeof( dateIso ) !== "undefined" && dateIso !== "" ) {
			date = new Date( dateIso );
		}

		var $visibleField = $( '<input />', {
			type:     'text',
			class:    'squareoffs-date-input squareoffs-date-input-alt',
			readonly: 'readonly',
			value:    formatReadableDate( date ),
		});

		$dateField.attr( 'type', 'hidden' );
		$visibleField.insertAfter( $dateField );

		// Swap the field ID to the new visible field.
		$visibleField.attr( 'id', $dateField.attr( 'id' ));
		$dateField.removeAttr( 'id' );

		$visibleField.datepicker({
			altField:    $dateField,
			altFormat:   'yy-mm-dd',
			dateFormat:  'd M yy',
			defaultDate: date,
		});

		$timeField.timepicker({
			timeFormat:  'h:mm p',
			minTime:     '12:00 AM',
			maxTime:     '11:59pm',
			interval:    60,
			dynamic:     false,
			dropdown:    true,
			scrollbar:   true,
			defaultTime: date,
			change:      function() {
				$timeField.trigger( 'change' );
			},
		});
	});
};
