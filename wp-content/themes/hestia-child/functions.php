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

function hesta_bottom_footer_content( $is_callback = false ) {
    if ( ! $is_callback ) {
    ?>
      <div class="hestia-bottom-footer-content">
      <?php
    }
    $menu_class                 = 'pull-left';
    $copyright_class            = 'pull-right';
    switch ( $hestia_copyright_alignment ) {
      case 'left':
        $menu_class      = 'pull-right';
        $copyright_class = 'pull-left';
        break;
      case 'center':
        $menu_class      = 'hestia-center';
        $copyright_class = 'hestia-center';
    }
    wp_nav_menu(
      array(
        'theme_location' => 'footer',
        'depth'          => 1,
        'container'      => 'ul',
        'menu_class'     => 'footer-menu ' . esc_attr( $menu_class ),
      )
    );
      ?>
    <?php if ( ! empty( $hestia_general_credits ) || is_customize_preview() ) : ?>
    <?php endif; ?>
    <?php
    if ( ! $is_callback ) {
    ?>
      </div>
      <?php
    }
  }
?>
