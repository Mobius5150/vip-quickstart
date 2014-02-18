jQuery( document ).ready( function () {
	jQuery( '.phpinfo_selector' ).click(function () {
		jQuery( '.phpinfo_selector' ).removeClass( 'button-primary' );
		jQuery( this ).addClass( 'button-primary' );
		jQuery( '.spinner' ).show();
		jQuery( '#phpinfo-window' ).attr( 'src', jQuery( '#phpinfo-baseurl' ).val() + '&type=' + jQuery(this).val());
	});

	jQuery( '#phpinfo-window' ).load( function() {
		jQuery( '.spinner' ).hide();
	} );
	
	// Styling JS is bad, but iframes don't size nice
	jQuery( window ).on( 'load resize', resize_iframe );
	
	function resize_iframe() {
		$window = jQuery( window );
		jQuery( '#phpinfo-window' ).height( function() {
			return $window.height() - jQuery( this ).offset().top - parseInt( jQuery( '#wpbody-content' ).css( 'padding-bottom' ) );
		} );
	}
	
	resize_iframe();
} );