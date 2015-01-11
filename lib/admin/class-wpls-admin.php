<?php
/**
 * Create setting page for WP Lucky Search
 * 
 * @package WP Lucky Search
 * @subpackage WPLS_Admin
 *
 * @author Harish Chaudhari
 */
class WPLS_Admin {
    
    /**
     * @var string Store plugin's text domain in WPLS_Admin context
     */
    public $text_domain;

    /**
     * Class Constructor
     * Adds necessary actions and filters
     * 
     * @since 1.0
     */
    function __construct() {
        global $wpls_main;
        
        $this->text_domain = $wpls_main->wpls_textdomain;

        /**
         * Add WP Lucky Search setting page
         */
        add_action( 'admin_menu', array( $this, 'wpls_admin_add_page' ) );
        add_action( 'admin_init', array( $this, 'wpls_admin_init' ) );
        
        /**
         * Enqueue admin scripts
         * 
         * For future use, disabled for now.
         */
        //add_action( 'admin_enqueue_scripts', array( $this, 'wpls_admin_scripts' ) );
    }

    /**
     * Add Admin page
     * 
     * @since 1.0
     */
    public function wpls_admin_add_page() {
        add_options_page('WP Lucky Search Settings', 'WP Lucky Search', 'manage_options', 'wpls', array( $this, 'wpls_settings_page' ) );
    }

    /**
     * Print admin page content
     * 
     * @since 1.0
     */
    public function wpls_settings_page() { ?>
        <div class="wrap">
            
            <h2>WP Lucky Search <?php _e( 'Settings', $this->text_domain ); ?></h2>

            <form method="post" action="options.php"><?php
                settings_fields( 'wpls_option_group' );
                do_settings_sections( 'wpls' );
                submit_button( __( 'Save Changes', $this->text_domain ), 'primary', 'submit', false );
                echo '&nbsp;&nbsp;';
                submit_button( __( 'Reset to default', $this->text_domain ), 'secondary', 'reset', false ); ?>
            </form>
            
        </div><?php
    }

    /**
     * Add Section settings and settings fields
     * 
     * @since 1.0
     */
    public function wpls_admin_init() {

        /**
         *  Register WP Lucky Search Settings
         */
        register_setting( 'wpls_option_group', 'wpls_options', array( $this, 'wpls_save' ) );

        /**
         *  Add Section
         */
        add_settings_section( 'wpls_section', __( 'Select Fields', $this->text_domain ), array( $this, 'wpls_section_content' ), 'wpls' );

        /**
         *  Add fields
         */
        add_settings_field( 'wpls_show_hide_control', __( 'Form behaviour controls', $this->text_domain ), array( $this, 'wpls_control_checkbox' ), 'wpls', 'wpls_section' );
        add_settings_field( 'wpls_lucky_caption', __( 'Lucky Button caption', $this->text_domain ), array( $this, 'wpls_lucky_caption' ), 'wpls', 'wpls_section' );
        add_settings_field( 'wpls_search_caption', __( 'Search Button caption', $this->text_domain ), array( $this, 'wpls_search_caption' ), 'wpls', 'wpls_section' );
        add_settings_field( 'wpls_messages', __( 'Alert messages', $this->text_domain ), array( $this, 'wpls_messages' ), 'wpls', 'wpls_section' );
        add_settings_field( 'wpls_list_post_types', __( 'Select Post Types' , $this->text_domain ), array( $this, 'wpls_post_types_settings' ), 'wpls', 'wpls_section' );
        
    }
    
    /**
     * Enqueue admin style and scripts
     * 
     * @since 1.0
     */
    public function wpls_admin_scripts() {
        wp_enqueue_style('wpls_admin_css', WPLS_ASSETS_URL . 'admin/css/wpls-admin.css');
    }

    /**
     * Validate input settings
     * 
     * @since 1.0
     * 
     * @global object $wpls_main Main class object
     * @param array $input input array by user
     * @return array validated input for saving
     */
    public function wpls_save( $input ) {
        global $wpls_main;
        $settings = $wpls_main->wpls_settings;
        
        if ( ! isset($input['wpls_post_types'] ) || empty( $input['wpls_post_types'] ) ) {
            add_settings_error( 'wpls_error', 'wpls_error_post_type', __( 'Select atleast one post type!', $this->text_domain ) );
            return $settings;
        }
        
        if ( empty( $input['wpls_lucky_caption'] ) ) {
            add_settings_error('wpls_error', 'wpls_error_all_empty', __( 'Button caption is required.', $this->text_domain ) );
            return $settings;
        }
        
        if ( isset( $_POST['reset'] ) ) {
            add_settings_error( 'wpls_error', 'wpls_error_reset', __( 'Your settings has been changed to WP Lucky Search default settings.', $this->text_domain ), 'updated' );
            return $wpls_main->wpls_default_options();
        }
        
        return $input;
    }

    /**
     * Section content before display fields
     * 
     * @since 1.0
     */
    public function wpls_section_content(){ ?>
        <em><?php _e( 'Select post types which you want to use for random redirection.', $this->text_domain ); ?></em><?php
    }

    /**
     * Default settings checkbox
     * 
     * @since 1.0
     * @global object $wpls_main
     */
    public function wpls_control_checkbox(){ 
        global $wpls_main;
        $settings = $wpls_main->wpls_settings; ?>

        <input type="hidden" name="wpls_options[wpls_show_lucky_button]" value="0" />
        <input <?php checked( $settings['wpls_show_lucky_button'] ); ?> type="checkbox" id="wpls_show_lucky_button" name="wpls_options[wpls_show_lucky_button]" value="1" />&nbsp;
        <label for="wpls_show_lucky_button"><?php _e( 'Show Lucky button', $this->text_domain ); ?></label>
        
        <br/><br/>
        
        <input type="hidden" name="wpls_options[wpls_show_search_button]" value="0" />
        <input <?php checked( $settings['wpls_show_search_button'] ); ?> type="checkbox" id="wpls_show_search_button" name="wpls_options[wpls_show_search_button]" value="1" />&nbsp;
        <label for="wpls_show_search_button"><?php _e( 'Show Search button', $this->text_domain ); ?></label>
        
        <br/><br/>
        
        <input type="hidden" name="wpls_options[wpls_show_alert_msg]" value="0" />
        <input <?php checked( $settings['wpls_show_alert_msg'] ); ?> type="checkbox" id="wpls_show_alert_msg" name="wpls_options[wpls_show_alert_msg]" value="1" />&nbsp;
        <label for="wpls_show_alert_msg"><?php _e( 'Show alert messages in browser', $this->text_domain ); ?></label><?php
    }
    
    /**
     * Lucky button caption
     * 
     * @since 1.0
     * @global object $wpls_main
     */
    public function wpls_lucky_caption(){ 
        global $wpls_main;
        $settings = $wpls_main->wpls_settings; ?>

        <input type="text" id="wpls_lucky_caption" name="wpls_options[wpls_lucky_caption]" value="<?php echo $settings['wpls_lucky_caption']; ?>" /><?php
    }

    /**
     * Search button caption
     * 
     * @since 1.0
     * @global object $wpls_main
     */
    public function wpls_search_caption(){ 
        global $wpls_main;
        $settings = $wpls_main->wpls_settings; ?>

        <input type="text" id="wpls_search_caption" name="wpls_options[wpls_search_caption]" value="<?php echo $settings['wpls_search_caption']; ?>" /><?php
    }

    /**
     * WPLS alert messages
     * 
     * @since 1.0
     * @global object $wpls_main
     */
    public function wpls_messages(){ 
        global $wpls_main;
        $settings = $wpls_main->wpls_settings; ?>

        <input type="text" id="wpls_error_msg" name="wpls_options[wpls_error_msg]" value="<?php echo $settings['wpls_error_msg']; ?>" />&nbsp;
        <label for="wpls_error_msg"><?php _e( 'Error message, just in case', $this->text_domain ) ?></label>
        <br /><br />
        <input type="text" id="wpls_not_found_msg" name="wpls_options[wpls_not_found_msg]" value="<?php echo $settings['wpls_not_found_msg']; ?>" />&nbsp;
        <label for="wpls_not_found_msg"><?php _e( 'Post not found message', $this->text_domain ) ?></label><?php
    }
    
    /**
     * Post type checkboexes
     * 
     * @since 1.0
     * 
     * @global object $wpls_main
     */
    public function wpls_post_types_settings() {
        global $wpls_main;

        /**
         * Filter post type arguments
         * @since 1.0
         * @param array arguments array
         */
        $wpls_all_post_types = get_post_types( apply_filters( 'wpls_post_types_args', array(
            'show_ui' => TRUE,
            'public' => TRUE
        ) ), 'objects' );
        
        if ( is_array( $wpls_all_post_types ) && ! empty( $wpls_all_post_types ) ) {
            foreach ( $wpls_all_post_types as $post_name => $post_obj ) { ?>
                <input <?php echo $this->wpls_checked( $post_name, $wpls_main->wpls_settings['wpls_post_types']); ?> type="checkbox" value="<?php echo $post_name; ?>" id="<?php echo 'wpls_' . $post_name; ?>" name="wpls_options[wpls_post_types][]" />&nbsp;
                <label for="<?php echo 'wpls_' . $post_name; ?>"><?php echo isset( $post_obj->labels->name ) ? $post_obj->labels->name : $post_name; ?></label><br /><br /><?php
            }
        } else { ?>
            <em><?php _e( 'No public post type found.', $this->text_domain ); ?></em><?php
        }
    }
    
    /**
     * Return checked if value exist in array
     * 
     * @since 1.0
     * 
     * @param mixed $value value to check against array
     * @param array $array haystack array
     * 
     * @return string checked="checked" or blank string
     */
    public function wpls_checked( $value = false, $array = array() ) {
        if ( in_array( $value, $array, true ) ) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }
        
        return $checked;
    }
}

/**
 * Instantiate the plugin's main class
 */
if( class_exists( 'WPLS_Admin' ) ) {
    global $wpls_admin;
    $wpls_admin = new WPLS_Admin();
}