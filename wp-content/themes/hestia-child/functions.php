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

function my_theme_enqueue_scripts() {

    $parent_style = 'hestia_scripts'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_script( $parent_style, get_template_directory_uri() . '/scripts.js' );
    wp_enqueue_script( 'hestia_scripts',
        get_stylesheet_directory_uri() . '/scripts.js',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_scripts' );

remove_filter( 'hestia_filter_features', array($files_to_load, 'sections/hestia-contact-section') );
remove_filter( 'hestia_filter_features', array($files_to_load, 'sections/hestia-blog-section') );

?>
