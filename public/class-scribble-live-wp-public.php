<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.scribblelive.com/
 * @since      1.0.0
 *
 * @package    Scribble_Live_Wp
 * @subpackage Scribble_Live_Wp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Scribble_Live_Wp
 * @subpackage Scribble_Live_Wp/public
 * @author     Scribble Live <scribblelive@reshiftmedia.com>
 */
class Scribble_Live_Wp_Public {

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
     * TTL for transient cache.
     *
     * @since    1.0.0
     * @access   private
     * @var string  $ttl  TTL for transient cache in seconds, default (3600) = 1 HR
     */
    private $ttl = '600';

    /**
     * Scribble current API endpoint.
     *
     * @since 1.0.0
     * @access private
     * @var string
     */
    private $scrbbl_endpoint = 'https://apiv1.scribblelive.com';

    /**
     * Authentication token
     *
     * @since 1.0.0
     * @access private
     * @var string
     */
    private $token;

    /**
     * Api call post order.
     *
     * @since 1.0.0
     * @access private
     * @var string
     */
    private $order = 'asc';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        $this->token = get_option( 'scrbbl-apikey' );
        if( empty( $this->token ) ){
            add_action( 'admin_notices', array( $this, 'require_api_key' ) );
        }

        $this->order = get_option( 'scrbbl-order', $this->order );

        $this->add_shortcodes();

	}

    /**
     * Require API Key Notice
     *
     * @since 1.0.0
     */
    public function require_api_key(){

        $plugin_options_link = admin_url( 'options-general.php?page=scribble-live-wp' );
        echo '<div class="error"><p>' . sprintf( wp_kses( __( 'The ScribbleLive WP plugin requires an API key to be set in order for it to function and work properly. Please visit the <a href="%s" >plugin settings page</a> to set it.', $this->plugin_name ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $plugin_options_link ) ) . '</p></div>';

    }

    /**
     * Register scribble live embedcode shortcode handler
     *
     * @since 1.0.0
     */
    public function add_shortcodes() {

        add_shortcode( 'scribble', array( $this, 'scribble_shortcode_handler' ) );
        add_shortcode( 'ScribbleLive', array( $this, 'scribble_shortcode_handler' ) );

    }

    /**
     * scribble shortcode handler
     *
     * @since 1.0.0
     * @param $atts
     * @return string
     */
    public function scribble_shortcode_handler( $atts ) {

        $a = shortcode_atts( array(
            'id' => '',
            'type' => 'board',
            'theme' => '',
            'src' => ''
        ), $atts );

        $src_array = $this->extract_src_attribute( $a['src'] );
        if( is_array( $src_array ) ){
            $a = $src_array;
        }

        if( empty( $a['id'] ) )
            return '';

        $cache_key = 'scrbbl_' . md5( serialize( $atts ) );
        $embedcode = $this->get_embedcode( $a['id'], $a['type'], $a['theme'], $cache_key );

        return wp_kses( $embedcode, array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'rel' => array()
            ),
            'div' => array(
                'class' => array(),
                'data-src' => array()
            ),
            'script' => array(
                'type' => array(),
                'src' => array()
            ),
            'noscript' => array(),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'b' => array(),
            'i' => array(),
            'h2' => array(),
            'h3' => array(),
            'h4' => array(),
            'h5' => array(),
        ) );

    }

    /**
     * Validate and extract data-src attribute
     *
     * @param string $data_src
     * @return array|null
     */
    public  function extract_src_attribute( $data_src = '' ){

        $data_src = trim( $data_src, ' /');

        if( empty( $data_src ) )
            return null;

        $src_array = explode( '/', $data_src, 3 );

        // Return if there is only one attribute passed
        if( count( $src_array ) < 2 )
            return null;

        return array(
            'type' => ( isset( $src_array[0] ) ) ? $src_array[0] : '',
            'id' => ( isset( $src_array[1] ) ) ? $src_array[1] : '',
            'theme' => ( isset( $src_array[2] ) ) ? $src_array[2] : ''
        );

    }

    /**
     * Get Scribble Live embedcode
     *
     * @since 1.0.0
     * @param string $id Scribble Live Event ID
     * @param string $type
     * @param string $theme
     * @param string $cache_key
     *
     * @return string
     */
    public function get_embedcode( $id = '', $type = 'board', $theme = '', $cache_key = null ) {

        $id = (int) $id;

        if( $cache_key == null )
            $cache_key = 'scrbbl_' . md5( $id . $type . $theme );

        //Even if only basic is requested, if extended is cached, function will return extended
        //Under most installs this will be non-persistant, but can exploit APC, etc. if configured
        if ( $cache = wp_cache_get( $cache_key, 'scribble' ) )
            return $cache;

        $embed_script = $this->get_embedcode_script( $id, $type, $theme );
        $embed_noscript = $this->get_embedcode_noscript( $id, $type );

        $output = $embed_script . $embed_noscript;

        $ttl = apply_filters( 'scribble_ttl', $this->ttl, 'get_embedcode' );

        wp_cache_set( $cache_key, $output, 'scribble', (int) $ttl  );

        return $output;

    }

    /**
     * Get embedcode script tag.
     *
     * @since 1.0.0
     * @param string $id
     * @param string $type
     * @param string $theme
     *
     * @return string
     */
    private function get_embedcode_script( $id = '', $type = 'board', $theme = '', $data_src = '' ) {

        // Check if it is an article and return legacy article embedCode
        if( $type == 'article' && !empty( $id ) ){
            $thread_id = '';
            if( !empty( $theme ) ){
                $thread_id = '&ThreadId=' . $theme;
            }
            return '<script src="' . esc_url( '//embed.scribblelive.com/js/LiveArticleEmbed.aspx?Id=' . $id . $thread_id ) . '" type="text/javascript"></script>';
        }

        if( empty( $data_src ) ){

            $data_src = '/' . $type . '/' . $id;

            if( !empty( $theme ) )
                $data_src = $data_src . '/' . $theme;

        }

        return '<div class="scrbbl-embed" data-src="' . esc_url( $data_src ) . '"></div>
        <script>(function(d, s, id) {var js,ijs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src="//embed.scribblelive.com/widgets/embed.js";ijs.parentNode.insertBefore(js, ijs);}(document, "script", "scrbbl-js"));</script>';

    }

    /**
     * Get Static HTML version wrapped in noscript tags for SEO.
     *
     * @since 1.0.0
     * @param int $id
     * @param string $type
     * @return string
     */
    private function get_embedcode_noscript( $id = 0, $type = 'board' ) {

        $api_query = $this->build_api_query( $id, $type );
        $scrbbl_data = $this->api_call( $api_query );

        $noscript_html = '';

        if( isset( $scrbbl_data['Posts'] ) ){
            $noscript_html = $this->get_noscript_html( $scrbbl_data['Posts'] );
        }
        // Check if single post and Content attribute is set, wrap post data with an array.
        elseif( $type == 'post' && isset( $scrbbl_data['Content'] ) ){
            $noscript_html = $this->get_noscript_html( array( $scrbbl_data ) );
        }
        // Check if Article and Html attribute is set, wrap with custom mock array.
        elseif( $type == 'article' && isset( $scrbbl_data['Html'] ) ){
            $noscript_html = $this->get_noscript_html(
                array(
                    array(
                        'Content' => $scrbbl_data['Html']
                    )
                )
            );
        }

        //get nofollow settings option
        $scrbbl_nofollow = get_option( 'scrbbl-nofollow' );

        if( $scrbbl_nofollow == 'whitelist' ){
            $noscript_html = $this->preg_replace_nofollow_whitelist( $noscript_html );
        }

        return "<noscript>" . wp_kses_post( $noscript_html ) . "</noscript>";

    }

    /**
     * Loop over posts and render HTML
     *
     * @since 1.0.0
     * @param array $scrbbl_posts
     * @return string
     */
    private function get_noscript_html( $scrbbl_posts = array() ) {

        $output = '';

        if( empty( $scrbbl_posts ) )
            return $output;

        foreach( $scrbbl_posts as $spost ){

            if( isset( $spost['Content'] ) )
                $output .= $spost['Content'];

        }

        return wp_kses_post( $output );

    }

    /**
     * Build API Query from parameters.
     *
     * @since 1.0.0
     * @param string $id
     * @param string $type
     * @param int $max
     * @return string
     */
    private function build_api_query( $id = '', $type = 'board', $max = 75 ) {

        $id = (int) $id;
        $max = (int) $max;

        $api_query = '';

        if( $type == 'board' || $type == 'timeline' || $type == 'event' ){
            $api_query = $this->scrbbl_endpoint . '/event/' . $id . '/all?Token=' . $this->token . '&Max=' . $max . '&format=json';
        }
        // Build query for single post.
        elseif( $type == 'post' ){
            $api_query = $this->scrbbl_endpoint . '/post/' . $id . '?Token=' . $this->token . '&format=json';
        }
        // Build query for article.
        elseif( $type == 'article' ){
            $api_query = $this->scrbbl_endpoint . '/article/' . $id . '/revision/published/latest?Token=' . $this->token . '&format=json';
        }

        return $api_query;

    }

    /**
     * Performs an API query, handles errors, and json decodes the data
     *
     * @since 1.0.0
     * @param string $query the url to query
     * @param int $ttl cache time for data
     * @param bool $decode whether to json_decode the data
     *
     * @return array the json decoded data
     */
    private function api_call( $query, $ttl = null, $decode = true ) {

        if( empty( $query ) )
            return false;

        if( $ttl == null )
            $ttl = apply_filters( 'scribble_ttl', $this->ttl, 'api_call' );

        $cache_key = 'scribble_api_' . md5( $query );

        if( $data = get_transient( $cache_key ) )
            return ( $decode ) ? json_decode( $data, true ) : $data;

        if( function_exists( 'vip_safe_wp_remote_get' ) ){
            $data = vip_safe_wp_remote_get( $query );
        }
        // Not on VIP load with wp_remote_get
        else{
            $data = wp_remote_get( $query );
        }

        if( is_wp_error( $data ) )
            return false;

        $data = wp_remote_retrieve_body( $data );

        //if it's plain HTML, cache the raw HTML and return, no need for additional checks
        if( !$decode ) {
            set_transient( $cache_key, $data, (int) $ttl );
            return $data;
        }

        // Set transient with new data
        set_transient( $cache_key, $data, (int) $ttl );

        //if it's JSON, verify that it's valid and that it is not an error before caching/returning
        $decoded = json_decode( $data, true );

        if( !$decoded || isset( $decoded['error'] ) )
            return false;

        return $decoded;

    }

    /**
     * Extract links from html content and replace with rel=nofollow attr.
     *
     * @since 1.0.0
     * @param string $content
     * @return mixed|string
     */
    public function preg_replace_nofollow_whitelist( $content = '' ) {
        $content = preg_replace_callback( '~<(a\s[^>]+)>~isU', array( $this, 'nofollow_whitelist_callback' ), $content );
        return $content;
    }

    /**
     * Evaluate tag and return rel=nofollow for external links
     * @since 1.0.0
     * @param $match
     * @return string
     */
    public function nofollow_whitelist_callback( $match ) {

        list( $original, $tag ) = $match;   // regex match groups

        // Return original if relative link
        if( !preg_match( "/http:\/\/|https:\/\//i", $tag ) ){
            return $original;
        }

        // Get site and blog URLs then extract domain search regex
        $home_site_url = get_home_url( null, null, "http://" ) . get_site_url( null, null, "http://" );
        $home_site_domain = preg_replace( "/http:\/\/|https:\/\//i", "|", $home_site_url );
        $home_site_domain = preg_replace( "/\//", "", $home_site_domain );

        //Check if link is nofollow or blog domain and return original
        if(  preg_match( "/nofollow" . $home_site_domain . "/i", $tag ) ) {
            return $original;
        }

        $whitelist_option = get_option( 'scrbbl-nofollow-regex', '' );
        $whitelist_match = sanitize_text_field( $whitelist_option );
        if( !empty( $whitelist_match ) && preg_match( $whitelist_match, $tag ) ) {
            return $original;
        }

        return "<$tag rel='nofollow'>";

    }


}