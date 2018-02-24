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

add_action( 'wp_head', 'remove_hestia_the_header_content_action', 100 );
function remove_hestia_the_header_content_action(){
  remove_action( 'hestia_do_header', 'hestia_the_header_content' );
}

add_action( 'hestia_do_header', 'add_hestia_child_theme_the_header_content' );
function add_hestia_child_theme_the_header_content() {
  $navbar_class = '';

  hestia_before_header_trigger();

  $hestia_header_alignment = get_theme_mod( 'hestia_header_alignment', 'left' );
  if ( ! empty( $hestia_header_alignment ) ) {
    $navbar_class .= ' hestia_' . $hestia_header_alignment;
  }

  $hestia_full_screen_menu = get_theme_mod( 'hestia_full_screen_menu', false );
  $navbar_class           .= (bool) $hestia_full_screen_menu === true ? ' full-screen-menu' : '';

  $hide_top_bar = get_theme_mod( 'hestia_top_bar_hide', true );
  if ( (bool) $hide_top_bar === false ) {
    $navbar_class .= ' header-with-topbar';
  }

  if ( ! is_home() && ! is_front_page() ) {
    $navbar_class .= ' navbar-not-transparent';
  }

  $navbar_class = apply_filters( 'hestia_header_classes', $navbar_class );

  hestia_the_header_top_bar();
  ?>
  <nav class="navbar navbar-default navbar-fixed-top <?php echo esc_attr( $navbar_class ); ?>">
    <?php hestia_before_header_content_trigger(); ?>
    <div class="entry-social">
      <a target="_blank" href="https://www.facebook.com/evehoyt"><i class="fa fa-lg fa-facebook"></i></a>
      <a target="_blank" rel="tooltip" href="https://www.instagram.com/eveningneon/"><i class="fa fa-lg fa-instagram"></i></a>
    </div>
    <div class="container">
      <div class="navbar-header">
        <div class="title-logo-wrapper">
          <a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>"><?php echo hestia_logo(); ?></a>
        </div>
      </div>
      <?php
      if ( $hestia_header_alignment === 'right' && is_active_sidebar( 'header-sidebar' ) ) {
        ?>
      <div class="header-sidebar-wrapper">
        <div class="header-widgets-wrapper">
      <?php
        dynamic_sidebar( 'header-sidebar' );
        ?>
        </div>
      </div>
      <?php
      } elseif ( $hestia_header_alignment === 'right' && is_customize_preview() ) {
        hestia_sidebar_placeholder( 'hestia-sidebar-header', 'header-sidebar', 'no-variable-width' );
      }

      wp_nav_menu(
        array(
          'theme_location'  => 'primary',
          'container'       => 'div',
          'container_class' => 'collapse navbar-collapse',
          'container_id'    => 'main-navigation',
          'menu_class'      => 'nav navbar-nav navbar-right',
          'fallback_cb'     => 'hestia_bootstrap_navwalker::fallback',
          'walker'          => new hestia_bootstrap_navwalker(),
          'items_wrap'      => ( function_exists( 'hestia_after_primary_navigation' ) ) ? hestia_after_primary_navigation() : '<ul id="%1$s" class="%2$s">%3$s</ul>',
        )
      );
      ?>
      <?php if ( has_nav_menu( 'primary' ) || current_user_can( 'manage_options' ) ) : ?>
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navigation">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="sr-only"><?php esc_html_e( 'Toggle Navigation', 'hestia' ); ?></span>
      </button>
      <?php endif; ?>
  </div>

    <?php hestia_after_header_content_trigger(); ?>
  </nav>
  <?php
  hestia_after_header_trigger();
}

add_action( 'wp_head', 'remove_hestia_setup_theme', 100 );
function remove_hestia_setup_theme(){
  add_action( 'after_setup_theme', 'hestia_setup_theme' );
}

add_action( 'hestia_do_header', 'hestia_setup_child_theme' );

  function hestia_setup_child_theme() {
    // Using this feature you can set the maximum allowed width for any content in the theme, like oEmbeds and images added to posts.  https://codex.wordpress.org/Content_Width
    global $content_width;
    if ( ! isset( $content_width ) ) {
      $content_width = 750;
    }

    // Takes care of the <title> tag. https://codex.wordpress.org/Title_Tag
    add_theme_support( 'title-tag' );

    // Add theme support for custom logo. https://codex.wordpress.org/Theme_Logo
    add_theme_support(
      'custom-logo', array(
        'flex-width'  => true,
        'flex-height' => true,
        'height'      => 100,
      )
    );

    // Loads texdomain. https://codex.wordpress.org/Function_Reference/load_theme_textdomain
    load_theme_textdomain( 'hestia', get_template_directory() . '/languages' );

    // Add automatic feed links support. https://codex.wordpress.org/Automatic_Feed_Links
    add_theme_support( 'automatic-feed-links' );

    // Add post thumbnails support. https://codex.wordpress.org/Post_Thumbnails
    add_theme_support( 'post-thumbnails' );

    // Add custom header support. https://codex.wordpress.org/Custom_Headers
    $header_settings = apply_filters(
      'hestia_custom_header_settings', array(// Height
        'height'      => 2000,
        // Flex height
        'flex-height' => true,
        // Header text
        'header-text' => false,
      )
    );
    add_theme_support( 'custom-header', $header_settings );

    // Add selective Widget refresh support
    add_theme_support( 'customize-selective-refresh-widgets' );

    // Add support for html5
    add_theme_support( 'html5', array( 'search-form' ) );

    // This theme uses wp_nav_menu(). https://codex.wordpress.org/Function_Reference/register_nav_menu
    register_nav_menus(
      array(
        'primary'      => esc_html__( 'Primary Menu', 'hestia' ),
        'footer'       => esc_html__( 'Footer Menu', 'hestia' ),
        'top-bar-menu' => esc_html__( 'Very Top Bar', 'hestia' ) . ' ' . esc_html__( 'Menu', 'hestia' ),
      )
    );

    // Adding image sizes. https://developer.wordpress.org/reference/functions/add_image_size/
    add_image_size( 'hestia-blog', 360, 240, true );

    if ( class_exists( 'woocommerce' ) ) {
      add_image_size( 'hestia-shop', 230, 350, true );
      add_image_size( 'hestia-shop-2x', 460, 700, true );
    }

    // Add Portfolio Image size if Jetpack Portfolio CPT is enabled.
    if ( class_exists( 'Jetpack' ) ) {
      if ( Jetpack::is_module_active( 'custom-content-types' ) ) {
        add_image_size( 'hestia-portfolio', 360, 300, true );
      }
    }

    // Added WooCommerce support.
    if ( class_exists( 'woocommerce' ) ) {
      add_theme_support( 'woocommerce' );
    }

    // Added Jetpack Portfolio Support.
    if ( class_exists( 'Jetpack' ) ) {
      add_theme_support( 'jetpack-portfolio' );
    }

    /* Customizer upsell. */
    $info_path = HESTIA_PHP_INCLUDE . 'customizer-info/class/class-hestia-customizer-info-singleton.php';
    if ( file_exists( $info_path ) ) {
      require_once( $info_path );
    }

    /* WooCommerce support for latest gallery */
    if ( class_exists( 'WooCommerce' ) ) {
      add_theme_support( 'wc-product-gallery-zoom' );
      add_theme_support( 'wc-product-gallery-lightbox' );
      add_theme_support( 'wc-product-gallery-slider' );
    }

    add_editor_style();
  }
