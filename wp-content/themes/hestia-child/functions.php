<?php
function my_theme_enqueue_styles() {

    $parent_style = 'hestia-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'hestia-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


function wpdocs_dequeue_script() {
   wp_dequeue_script( 'hestia_scripts' );
}
add_action( 'wp_print_scripts', 'wpdocs_dequeue_script', 100 );


add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts', 100);
function my_theme_enqueue_scripts() {
  wp_dequeue_script( 'hestia_scripts' );
  wp_enqueue_script('child_theme_script_handle', get_stylesheet_directory_uri().'/scripts/scripts.js', array('material'));
}
?>
