<?php
/**
 * Plugin Name:     Front Form Expresstechsoftwares
 * Plugin URI:      https://github.com/younes-dro/
 * Description:     Front Form Expresstechsoftwares test.
 * Author:          Younes DRO
 * Author URI:      https://github.com/younes-dro/
 * Text Domain:     front-form-expresstechsoftwares
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Front_Form_Expresstechsoftwares
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Front_Form_Expresstechsoftwares' )){
    
    class Front_Form_Expresstechsoftwares {
        
        private static $instance;

        protected $templates = array();
        
        public static function start() {
            
            if ( NULL === self::$instance){
                self::$instance = new self( );
            }

            return self::$instance;
        }
        
        public function __construct(){
            
            add_action( 'plugins_loaded' , array( $this , 'load_textdomain' ) );
            add_filter( 'theme_page_templates' , array( $this, 'add_frontform_template' ) );
            add_filter( 'wp_insert_post_data' ,  array( $this, 'register_frontform_template' ) );
            add_filter( 'template_include' ,  array( $this, 'view_frontform_template' ) );
            $this->templates = array(
		'frontform-template.php' => 'Frontend Post Submission (no-sidebar)',
                );
            add_action( 'wp_enqueue_scripts', array( $this , 'frontfrom_scripts' ) );
            add_action('template_redirect', array( $this , 'processing_form' ) );
            
            add_action( 'wp_ajax_dro_ajax_request', array ( $this , 'dro_ajax_request' ) );
            add_action( 'wp_ajax_nopriv_dro_ajax_request', array ( $this , 'dro_ajax_request' ) );
            
        }
	public function add_frontform_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
                
		return $posts_templates;
	}
        public function register_frontform_template( $atts ){
            
            $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

            $templates = wp_get_theme()->get_page_templates();
            if ( empty( $templates ) ) {
                    $templates = array();
            }
            wp_cache_delete( $cache_key , 'themes');
            $templates = array_merge( $templates, $this->templates );
            wp_cache_add( $cache_key, $templates, 'themes', 1800 );

            return $atts;            
        }
        public function view_frontform_template( $template ){
            global $post;

            if ( ! $post ) {
                    return $template;
            }

            if ( !isset( $this->templates[get_post_meta( 
                    $post->ID, '_wp_page_template', true 
            )] ) ) {
                    return $template;
            } 

            $file = plugin_dir_path(__FILE__). 'templates/' . get_post_meta( 
                    $post->ID, '_wp_page_template', true
            );

            if ( file_exists( $file ) ) {
                    return $file;
            } else {
                    echo $file;
            }

            return $template;
            
        }
        
        public function frontfrom_scripts (){
            global $template;
            
            /*
             * Load scripts only for our custom page template
             */
            if( basename( $template ) === 'frontform-template.php' ){
                wp_register_style( 'expresstechsoftwares-custom-form' , plugin_dir_url(__FILE__) . '/assets/css/custom-form.css', array(), time());
                wp_enqueue_style( 'expresstechsoftwares-custom-form' );
                wp_register_script( 'expresstechsoftwares-custom-form-js' , plugin_dir_url(__FILE__) . '/assets/js/dro-ajax.js', array( 'jquery' ), time());
                wp_enqueue_script( 'expresstechsoftwares-custom-form-js' );
                wp_localize_script(
                    'expresstechsoftwares-custom-form-js',
                    'dro_ajax_obj',
                    array(
                            'ajaxurl' => admin_url( 'admin-ajax.php' ),
                            'nonce' => wp_create_nonce( 'ajax-nonce' )
                    )
                );
            }
        }
        public function dro_ajax_request (){
            
            $postid       = esc_attr( $_POST['post'] );
            wp_delete_post( $postid );
            
            die();
        
        }
        public function processing_form(){
            if ( isset( $_POST['save-custom-form'] ) && isset( $_POST['nonce-check'] ) ) {
                
                if ( wp_verify_nonce( $_POST['nonce-check'], 'expresstechsoftwares-custom-form' )  ){                    
                    
                    $custom_form_data =  array(
                        'post_title' => $_POST['custom-title'],
                        'post_content' => $_POST['custom-description'],
                        'post_status' => $_POST['custom-status']
                    );
                    $post_id = wp_insert_post( $custom_form_data );
                    
                    if( ! is_wp_error( $post_id ) ){ 
                        
                        //the post is valid
                       
                          
                    } else {
                        
                      //there was an error in the post insertion
                       $url = add_query_arg('erreur', $post_id->get_error_message() , wp_get_referer() );
                       wp_safe_redirect($url);
                       
                       exit();
                      
                    }
                    //print_r($_POST);
                   
                } else {
                       esc_html_e ( 'Sorry, your nonce did not verify ! ' , 'front-form-expresstechsoftwares' );
                       
                       exit;
                }
                
                
            }
        }

        public function load_textdomain(){
            
            load_plugin_textdomain( 'front-form-expresstechsoftwares', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }
        
    
    }   
}

/**
 * Returns the main instance.
 */
function Front_Form_Expresstechsoftwares_init(){
    
    return Front_Form_Expresstechsoftwares::start( );
    
}
add_action( 'plugins_loaded', 'Front_Form_Expresstechsoftwares_init' );
    
    
    
