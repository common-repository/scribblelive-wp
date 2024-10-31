<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.scribblelive.com/
 * @since      1.0.0
 *
 * @package    Scribble_Live_Wp
 * @subpackage Scribble_Live_Wp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Scribble_Live_Wp
 * @subpackage Scribble_Live_Wp/admin
 * @author     Scribble Live <scribblelive@reshiftmedia.com>
 */
class Scribble_Live_Wp_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_action( 'admin_menu', array( $this, 'add_scrbbl_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'scrbbl_admin_init' ) );
        add_action( 'admin_head', array( $this, 'scrbbl_mce_button_admin_init' ) );

	}

    /**
     * Add ScribbleLive optiuons page to admin menu
     *
     * @since   1.0.0
     */
    public function add_scrbbl_admin_menu() {
        add_options_page( 'ScribbleLive', 'ScribbleLive', 'manage_options', $this->plugin_name, array( $this, 'display_scrbbl_options_page' ) );
    }

    /**
     * Display Admin options panel
     *
     * @since   1.0.0
     */
    public function display_scrbbl_options_page() {
    ?>
    <div class="wrap">
        <h2><?php esc_html_e( 'ScribbleLive WP', $this->plugin_name ); ?></h2>
        <form action="options.php" method="POST">
            <?php settings_fields( 'scrbbl-settings-grp' ); ?>
            <?php do_settings_sections( $this->plugin_name ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    }

    /**
     * Register Settings Groups, Sections, and fields
     *
     * @since 1.0.0
     */
    public function scrbbl_admin_init() {

        // Account Section
        add_settings_section( 'scrbbl-account', esc_html__( 'ScribbleLive Account Settings', $this->plugin_name ), array( $this, 'scrbbl_account_section_callback' ), $this->plugin_name );
        // SEO Section
        add_settings_section( 'scrbbl-seo', esc_html__( 'ScribbleLive SEO Settings', $this->plugin_name ), array( $this, 'scrbbl_seo_section_callback' ), $this->plugin_name );

        // Fields
        register_setting( 'scrbbl-settings-grp', 'scrbbl-apikey' );
        add_settings_field( 'scrbbl-apikey-field', esc_html__( 'API Key', $this->plugin_name ), array( $this, 'display_option_input_text' ), $this->plugin_name, 'scrbbl-account', array( 'option' => 'scrbbl-apikey' ) );

        // Fields
        register_setting( 'scrbbl-settings-grp', 'scrbbl-nofollow' );
        add_settings_field( 'scrbbl-nofollow-field', esc_html__( 'Link rel=nofollow attribute mode', $this->plugin_name ), array( $this, 'scrbbl_nofollow_select_callback' ), $this->plugin_name, 'scrbbl-seo' );

        register_setting( 'scrbbl-settings-grp', 'scrbbl-nofollow-regex' );
        add_settings_field( 'scrbbl-nofollow-regex-field', esc_html__( 'nofollow REGEX Rule', $this->plugin_name ), array( $this, 'display_nofollow_regex_option' ), $this->plugin_name, 'scrbbl-seo' );

    }

    /**
     * Scribble Live Account Section callback
     *
     * @since 1.0.0
     */
    public  function scrbbl_account_section_callback(){
        esc_html_e( "ScribbleLive account access and communication settings. This plugin communicates with ScribbleLive using the http://apiv1.scribblelive.com endpoint.", $this->plugin_name );
    }

    /**
     * Scribble Live SEO Section callback
     *
     * @since 1.0.0
     */
    public  function scrbbl_seo_section_callback(){
        esc_html_e( "ScribbleLive advanced SEO settings provide more control over internal and external links rel=nofollow attribute.", $this->plugin_name );
    }

    /**
     * Generic Text input display function
     *
     * @param $args array expects $args['option'] which contains the option name
     * @since 1.0.0
     */
    public function display_option_input_text( $args = array() ) {

        if( isset( $args['option'] ) ){

            $name = $args['option'];
            $value = get_option( $name );
            echo "<input type='text' name='" . esc_attr( $name ) . "' value='" . esc_attr( $value ) . "' />";

        }

    }


    /**
     * nofollow select display
     *
     * @since 1.0.0
     */
    function scrbbl_nofollow_select_callback() {

        $scrbbl_nofollow = get_option( 'scrbbl-nofollow' );

        $html = '<select id="scrbbl-nofollow" name="scrbbl-nofollow">';
        $html .= '<option value="notset" ' . selected( $scrbbl_nofollow, 'notset', false ) . '>' . esc_html__( "Do not set ( follow all links and ignore regex rule )", $this->plugin_name ) . '</option>';
        $html .= '<option value="whitelist" ' . selected( $scrbbl_nofollow, 'whitelist', false ) . '>' . esc_html__( "Whitelist  ( Set nofollow on ALL links except for regex rule match )", $this->plugin_name ) . '</option>';
        $html .= '</select>';

        $allowed_html =  array(
            'select' => array(
                'id' => array(),
                'name' => array()
            ),
            'option' => array(
                'value' => array(),
                'selected' => array()
            )
        );

        echo wp_kses( $html, $allowed_html );

    }

    /**
     * nofollow regex input display function
     *
     * @since 1.0.0
     */
    public function display_nofollow_regex_option() {

        $name = 'scrbbl-nofollow-regex';
        $value = get_option( $name );
        echo "<input type='text'  name='" . esc_attr( $name ) . "' value='" . esc_attr( $value ) . "' style='width: 100%;' />";

    }

    /**
     * Registers tinyMCE button when in rich editing mode
     *
     * @since 1.0.1
     */
    public function scrbbl_mce_button_admin_init() {

        if ( ! current_user_can( 'edit_post', get_the_ID() ) )
            return;

        if( function_exists( 'get_user_attribute' ) ) {

            if ( get_user_attribute( get_current_user_id(), 'rich_editing' ) == 'true' && user_can_richedit() ) {

                add_filter( 'mce_external_plugins', array( $this, 'add_scribble_tinymce_plugin' ) );
                add_filter( 'mce_buttons', array( $this, 'register_scribble_button' ) );

            }

        }else{

            if ( get_user_option( 'rich_editing' ) == true ) {

                add_filter( 'mce_external_plugins', array( $this, 'add_scribble_tinymce_plugin' ) );
                add_filter( 'mce_buttons', array( $this, 'register_scribble_button' ) );

            }

        }

    }

    /**
     * Adds the embed code button to tinyMCE
     *
     * @param  $buttons
     * @return mixed
     * @since  1.0.1
     */
    public function register_scribble_button( $buttons ) {

        array_push( $buttons, "scribblebutton" );
        return $buttons;

    }

    /**
     * Registers the javascript to be run when tinyMCE button is clicked
     *
     * @param  $plugin_array
     * @return mixed
     * @since  1.0.1
     */
    public function add_scribble_tinymce_plugin( $plugin_array ) {

        $plugin_array[ 'scribblebutton' ] = plugin_dir_url( __FILE__ ) . '/js/scribble_button.js';
        return $plugin_array;

    }
}
