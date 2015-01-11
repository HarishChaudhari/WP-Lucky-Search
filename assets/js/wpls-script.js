/* 
 * WP Lucky Search Scripts
 * 
 */

jQuery( 'document' ).ready( function() {
    if( jQuery( '#wpls-search' ).length > 0 && typeof wpls_ajaxobj == 'object' ) {
        jQuery( '#wpls-search' ).click( function(event) {
            event.preventDefault();
            
            var searchQuery = jQuery('#s').val();
            var wplsNonce = jQuery('#wpls_nonce').val();
            
            var wplsAjaxData = {
                action : 'wpls_get_random_post',
                search_query : searchQuery,
                wpls_nonce : wplsNonce
            };
            
            jQuery.post( wpls_ajaxobj.ajaxurl, wplsAjaxData, function( response ) {
                /**
                 * Show alerts if allowed
                 */
                if( wpls_ajaxobj.show_alerts == 'true' && response == 'nonce_failed' ) {
                    alert( wpls_ajaxobj.wpls_error_msg );
                } else if( wpls_ajaxobj.show_alerts == 'true' && response == 'not_found' ) {
                    alert( wpls_ajaxobj.wpls_not_found_msg );
                } else if( response.indexOf('http') !== -1 ) {
                    window.location = response;
                }
            } );
        } );
    }
} );