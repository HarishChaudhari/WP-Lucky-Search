<?php

/**
 * Main class of WP Lucky Search
 * 
 * @package WP Lucky Search
 * @subpackage WPLS_Main
 * 
 * @author Harish Chaudhari
 * 
 * @since 1.0
 */
class WPLS_Main {

    /**
     *
     * @var string WP Lucky Search text domain
     */
    public $wpls_textdomain;
    
    /**
     *
     * @var array WP Lucky Search settings in array format
     */
    public $wpls_settings;
            
    /**
     * Class constructor
     * Adds plugin's actions and filters
     * 
     * @since 1.0
     */
    function __construct() {
        
        /**
         * Define textdomain for WP Lucky Search
         * 
         * @var string Set 'wpls' as text domain for WP Lucky Search
         */
        $this->wpls_textdomain = 'wpls';
        
        /**
         * Get settings of WP Lucky Search
         * 
         * @var string Store setting array of WP Lucky Search
         */
        $this->wpls_settings = $this->wpls_get_settings();
        
        /**
         * Return if show button is unchecked, do not load anything
         */
        if( !$this->wpls_settings['wpls_show_lucky_button'] ) {
            return;
        }
        
        /**
         * Override default wordpress search form
         * Hook on little later, just in case
         */
        add_filter( 'get_search_form', array( $this, 'wpls_search_form' ), 99 );
        
        /**
         * Hook wpls_get_random_post on ajax
         */
        add_action('wp_ajax_wpls_get_random_post', array( $this, 'wpls_get_random_post' ) );
        add_action('wp_ajax_nopriv_wpls_get_random_post', array( $this, 'wpls_get_random_post' ) );
        
        /**
         * Enqueue frontend script
         */
        add_action( 'wp_enqueue_scripts', array( $this, 'wpls_frontend_scripts' ) );
    }

    /**
     * Get Defualt options
     * 
     * @since 1.0
     */
    public function wpls_default_options() {
        $settings = array(
                'wpls_show_lucky_button'    =>  true,
                'wpls_show_search_button'   =>  false,
                'wpls_show_alert_msg'       =>  true,
            
                'wpls_lucky_caption'        =>  __( 'I\'m feeling lucky', $this->wpls_textdomain ),
                'wpls_search_caption'       =>  __( 'Search', $this->wpls_textdomain ),
            
                'wpls_error_msg'            =>  __( 'Something went wrong...', $this->wpls_textdomain ),
                'wpls_not_found_msg'        =>  __( 'You might not be lucky today!', $this->wpls_textdomain ),
            
                'wpls_post_types'           =>  array( 'post', 'page' )
            );
        return $settings;
    }

    /**
     * Get plugin options
     * 
     * @since 1.0
     */
    public function wpls_get_settings() {
        $db_settings = get_option( 'wpls_options' );
        $settings = wp_parse_args( $db_settings, $this->wpls_default_options() );
        
        return $settings;
    }
    
    /**
     * New Search form, includes lucky button
     * 
     * @since 1.0
     */
    public function wpls_search_form() {
        $search_btn = '';
        
        /**
         * Show / hide search button as per settings
         */
        if( $this->wpls_settings['wpls_show_search_button'] ) {
            $search_btn = '<input type="submit" id="searchsubmit" value="' . esc_attr ( $this->wpls_settings['wpls_search_caption'] ) . '" />';
        }
        
        $form = '
            <form role="search" method="get" id="searchform" class="searchform" action="' . home_url('/') . '" >
	        <div>
                    <label class="screen-reader-text" for="s">' . __( 'Search for:' ) . '</label>
    	            <input type="text" value="' . get_search_query() . '" name="s" id="s" />
	            ' . $search_btn  . '
	            <input type="submit" id="wpls-search" value="' . esc_attr ( $this->wpls_settings['wpls_lucky_caption'] ) . '" />
                    ' . wp_nonce_field( 'wpls_random_redirect', 'wpls_nonce', false ) . '
	        </div>
	    </form>';

        return $form;
    }
    
    /**
     * Find random post from search query entered by user
     * 
     * @global type $query_string
     * 
     * @since 1.0
     * 
     * @todo add blank search settings
     */
    public function wpls_get_random_post() {
        if ( ! isset( $_POST['wpls_nonce'] ) || ! wp_verify_nonce( $_POST['wpls_nonce'], 'wpls_random_redirect' ) ) {
            echo 'nonce_failed';
        } else {
            if( isset( $_POST['search_query'] ) ) {
                $wpls_settings = get_option( 'wpls_options' );
                $search_query = array();
                
                $search_query['s'] = esc_attr( stripslashes( strip_tags( $_POST['search_query'] ) ) );
                $search_query['posts_per_page'] = -1;
                $search_query['post_status'] = 'publish';
                if( isset( $wpls_settings['wpls_post_types'] ) && count( $wpls_settings['wpls_post_types'] ) > 0 ) {
                    $search_query['post_type'] = $wpls_settings['wpls_post_types'];
                }
            
                $search = new WP_Query( $search_query );
                
                if( isset( $search ) && isset( $search->found_posts ) ) {
                    $random = rand( 0, ( $search->found_posts - 1 ) );
                    
                    if( isset( $search->posts[$random] ) ) {
                        $random_post_link = get_permalink( $search->posts[$random]->ID );
                        echo $random_post_link ;
                    } else {
                        echo 'not_found';
                    }
                }
            }
        }
        die();
    }
    
    /**
     * Enqueue frontend scripts
     * 
     * @since 1.0
     */
    public function wpls_frontend_scripts() {
        wp_enqueue_script( 'wpls_script', WPLS_ASSETS_URL . '/js/wpls-script.js', array( 'jquery' ), true );
        wp_localize_script( 'wpls_script', 'wpls_ajaxobj', array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'show_alerts' => $this->wpls_settings['wpls_show_alert_msg'] ? 'true' : 'false',
            'wpls_error_msg' => $this->wpls_settings['wpls_error_msg'],
            'wpls_not_found_msg' => $this->wpls_settings['wpls_not_found_msg']
        ) );
    }
}

/**
 * Instantiate the plugin's main class
 */
if( class_exists( 'WPLS_Main' ) ) {
    global $wpls_main;
    $wpls_main = new WPLS_Main();
}