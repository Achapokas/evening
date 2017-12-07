<?php
/*

 * Plugin Name: Simple Masonry Layout

 * Plugin URI: http://wordpress.org/plugins/simple-masonry-layout/

 * Description: With simple shortcode, Masonry Layout in action.

 * Author: Raju Tako

 * Version: 1.3

 * Author URI: https://profiles.wordpress.org/razzu

 * 

 */



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



//Enque script and style



function simple_masonry_enqueue_scripts()

{   



    global $post;

    if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'simple_masonry') ) {

        	wp_register_style( 'sm-style', plugin_dir_url( __FILE__ ) . 'css/sm-style.css');

            wp_register_style( 'darkbox-style', plugin_dir_url( __FILE__ ) . 'css/darkbox.css');

            wp_register_style( 'font-awesome', ("http:////netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css"));

            wp_enqueue_style( 'sm-style');

            wp_enqueue_style( 'darkbox-style');

            wp_enqueue_style( 'font-awesome');


            wp_enqueue_script( 'jquery' );
            
            wp_enqueue_script( 'modernizr-script' );

            wp_enqueue_script( 'jquery-masonry' );  

            wp_register_script( 'modernizr-script', plugin_dir_url( __FILE__ ) . 'js/modernizr.custom.js', array('jquery'), '', false );

            wp_register_script( 'imagesloaded-script', plugin_dir_url( __FILE__ ) . 'js/imagesloaded.js', array('jquery'), '', true );

            wp_register_script( 'classie-script', plugin_dir_url( __FILE__ ) . 'js/classie.js', array('jquery'), '', true );

            wp_register_script( 'AnimOnScroll-script', plugin_dir_url( __FILE__ ) . 'js/AnimOnScroll.js', array('modernizr-script'), '', true );

            wp_register_script( 'main-script', plugin_dir_url( __FILE__ ) . 'js/main.js', array('AnimOnScroll-script'), '', true );

            wp_register_script( 'darkbox-script', plugin_dir_url( __FILE__ ) . 'js/darkbox.js', array('jquery'), '', true );

            wp_enqueue_script( 'imagesloaded-script' );

            wp_enqueue_script( 'classie-script' );

            wp_enqueue_script( 'AnimOnScroll-script' );

            wp_enqueue_script( 'main-script' );

            wp_enqueue_script( 'darkbox-script' );

    }

}



add_action( 'wp_enqueue_scripts', 'simple_masonry_enqueue_scripts' );


include('inc/admin.php'); //admin option settings

include('inc/front.php'); // frontend shortcode 





