<?php
/*
 * Plugin Name: BNE Gallery Extended
 * Version: 1.1.1
 * Description:  Adds a new shortcode attribute, "display" to the WP [gallery] shortcode allowing to display the gallery as a 3D carousel or masonry grid.
 * Author: Kerry Kline
 * Author URI: http://www.bnecreative.com
 * Requires at least: 4.5
 * Text Domain: bne-gallery-extended
 * License: GPL2

    Copyright 2017 BNE Creative

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/


// Exit if accessed directly
if( !defined('ABSPATH') ) exit;



if( !class_exists( 'BNE_GALLERY_EXTENDED' ) ) {


	/*
	 * 	The BNE Gallery Extended Class
	 *
	 * 	@since 	v1.0
	 *
	*/
	class BNE_GALLERY_EXTENDED {
	
		
	    /*
	     * 	Constructor
	     *
	     *	@since v1.0
	     *
	    */
		function __construct() {
			
			// Set Constants
			define( 'BNE_GALLERY_EXTENDED_VERSION', '1.1.1' );
			define( 'BNE_GALLERY_EXTENDED_URI', plugins_url( '', __FILE__ ) );
			
			// Filter to hijack the [galler] shortcode
			add_filter( 'post_gallery', array( $this, 'gallery_shortcode_hijack'), 10, 3 );
			
			// Action to include css into the <head>
			add_action( 'wp_head', array( $this, 'gallery_css' ) );
			
			// Action to add a new custom field the WP Gallery Settings modal window.
			add_action( 'print_media_templates', array( $this, 'gallery_media_settings' ) );
			
		}

		
		
		/*
		 * 	Gallery Shortcode Hijack
		 *
		 *	Hijacks the default WordPress [gallery] shortcode and
		 *	extends it to allow a display attribute. If used and
		 *	and matches a condition, the gallery will output as
		 *	one of the new displays.
		 *
		 *	$output			The current output - the WordPress core passes an empty string.
		 *	$atts			The attributes from the gallery shortcode.
		 *	$instance		Unique numeric ID of this gallery shortcode instance.
		 *
		 *	NEW OPTIONS
		 *	@atts[display]	carousel or carrousel - Uses the roundabout.js
		 *					script as a slider with next/prev navigation.
		 *
		 *	@atts[display]	masonry - Uses the build in masonry.js from WP
		 *					script and displays as a mosaic grid. Accepts
		 *					column attribute as well.
		 *	
		 *	@since		v1.0
		 *	@updated	v1.1.1
		 *
		*/
		function gallery_shortcode_hijack( $output = '', $atts, $instance ) {
			
			
			/*
			 *	Checks if display attribute exist, if not set to "null" to prevent
			 *	a php index error.
			*/
			$atts['display'] = isset( $atts['display'] ) ? esc_html( $atts['display'] ) : null;
		
			
			/*
			 *	Carousel Display
			 *	
			 *	Check for both spellings of "carousel", if match
			 *	enqueue the roundabout.js script and hijack the gallery output.
			 *
			*/
			if( $atts['display'] == 'carousel' || $atts['display'] == 'carrousel' ) {
		
				// Enqueue roundabout.js
				wp_enqueue_script( 'roundabout', BNE_GALLERY_EXTENDED_URI . '/assets/js/roundabout.min.js', array('jquery'), BNE_GALLERY_EXTENDED_VERSION, true );

				// Shortcode Fallbacks
				$atts['size'] = isset( $atts['size'] ) ? sanitize_html_class( $atts['size'] ) : 'medium';
				$atts['caption'] = isset( $atts['caption'] ) ? esc_html( $atts['caption'] ) : 'false';
				$atts['shadow'] = isset( $atts['shadow'] ) ? intval( $atts['shadow'] ) : '';
				$autoplay = isset( $atts['autoplay'] ) ? intval( $atts['autoplay'] ) : '';

				// Setup Autoplay settings
				if( $autoplay ) {
					$autoplay = 'autoplay: true,'; // Enable Autoplay
					$autoplay .= 'autoplayPauseOnHover: true,'; // Pause roundabout when mouse hovers
					$autoplay .= 'autoplayDuration: '.$atts['autoplay'].','; // The time (milliseconds) focused on a slide
					//$autoplay .= 'duration: '.$atts['autoplay'] / 2 .',';  // The time (milliseconds) between slides
				}
				
				// Allow multiple instances on a page.
				$rand_id = rand( 0,1000 );
				
				// Output the gallery
				$output .= '<div id="bne-carousel-'.$rand_id.'" class="bne-gallery-extended bne-gallery-carousel-wrapper clearfix">';
		
					// Initiate roundabout.js on our gallery instance
					$output .= '<script type="text/javascript">
									jQuery(document).ready(function($) {
										$(window).load(function() {
											$("#bne-carousel-'.$rand_id.' .carousel-slider").roundabout({
												btnNext: "#bne-carousel-'.$rand_id.' .next",
								     			btnPrev: "#bne-carousel-'.$rand_id.' .prev",
								     			responsive: true,
								     			'.$autoplay.'
								     			minOpacity: 0.1,
								     			minScale: 0.1
											}).parent().find(".bne-gallery-loader").fadeOut();
											$("#bne-carousel-'.$rand_id.' .carousel-slider .slide").fadeIn();
										});
									});
								</script>';
					
					$output .= '<div class="slider-inner">';
						
						// Loading Indicator
						$output .= '<div class="bne-gallery-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
						
						// Carousel Nav
						$output .= '<div class="roundabout-nav">';
							$output .= '<a href="#" title="Previous" class="prev"><i class="fa fa-arrow-circle-left"></i></a>';
							$output .= '<a href="#" title="Next" class="next"><i class="fa fa-arrow-circle-right"></i></a>';
						$output .= '</div>';
						
						// Slides
						$output .= '<ul class="carousel-slider gallery">';
		
							// Grabs the image ID's in the [gallery] shortcode
							$image_ids = explode( ',', $atts['ids'] );
							
							// Check if orderby is set to "rand", if so shuffle the stack
							if( $atts['orderby'] == 'rand' ) { shuffle( $image_ids ); }
							
							// Loop through Image Id's
							foreach( $image_ids as $id ) {
								$output .= '<li class="slide gallery-item image-id-'.$id.'">';
									
									// Link: File
									if( !empty( $atts['link'] ) && 'file' === $atts['link'] ) {
										$output .= wp_get_attachment_link( $id, $atts['size'], false, false, false );
									// Link: None
									} elseif( !empty( $atts['link'] ) && 'none' === $atts['link'] ) {
										$output .= wp_get_attachment_image( $id, $atts['size'], false );
									// Link: Attachment
									} else {
										$output .= wp_get_attachment_link( $id, $atts['size'], true, false, false );
									}

									// Shadow
									if( $atts['shadow'] ) {
										$output .= '<div class="content-shadow shadow-type'.$atts['shadow'].'"></div>';
									}

									// Caption
									if( $atts['caption'] == 'true' ) {
										$meta = wp_prepare_attachment_for_js( $id );
										if( $meta['caption'] ) {
											$output .= '<div class="caption-overlay">';
												$output .= '<div class="caption">';
													$output .= ( $meta['caption'] ) ? '<span class="caption-description">'.$meta['caption'].'</span>' : '';
												$output .= '</div>';
											$output .= '</div>';
										}
									}

								$output .= '</li>';
							}
						
						$output .= '</ul><!-- .carousel-slider (end) -->';
					$output .= '</div><!-- .slider-inner (end) -->';
				$output .= '</div><!-- #bne-carousel-'.$rand_id.' (end) -->';
		
			}
		
		
		
			/*
			 *	Masonry Display
			 *	
			 *	Check for "masonry" If selected, enqueue the masonry.js script
			 *	from WordPress and hijack the gallery output.
			 *
			*/
			if( $atts['display'] == 'masonry' ) {
		
				// Shortcode Fallbacks
				$atts['size'] = isset( $atts['size'] ) ? esc_html( $atts['size'] ) : 'large';
				$atts['columns'] = isset( $atts['columns'] ) ? intval( $atts['columns'] ) : '3';
				$atts['gutter'] = isset( $atts['gutter'] ) ? intval( $atts['gutter'] ) : '5';
				$atts['responsive'] = isset( $atts['responsive'] ) ? esc_html( $atts['responsive'] ) : 'true';
				$atts['caption'] = isset( $atts['caption'] ) ? esc_html( $atts['caption'] ) : 'false';
					
				// Allow multiple instances on a page.
				$rand_id = rand( 0,1000 );
				
				// Define the number of gutters needed. Gutters are only between image columns; therefore, we minus 1.
				// Ex: If 3 columns, we only need 2 gutters
				$gutters = $atts['columns'] - 1;
				
				// Define the total amount of space needed for all gutters by taking the number of gutters and multiplying by the size of gutters required from the [gallery] shortcode.
				$gutter_space = $gutters * $atts['gutter'].'px';
				
				// Define the final column width as 100% minus gutters (px) divided by the number of image columns to display.
				$grid_col_width = 'calc( ( 100% - '.$gutter_space.' ) / '.$atts['columns'].' )' ;
				
					
				// Enqueue masonry.js from WP core
				wp_enqueue_script( 'masonry' );
				
				wp_add_inline_script( 'masonry', 
					'jQuery(document).ready(function($){
						$(window).load(function() {
							$("#bne-gallery-masonry-'.$rand_id.'").masonry({
								itemSelector: ".gallery-single",
								columnWidth: ".gallery-single",
								gutter: '.$atts['gutter'].',
								//transitionDuration: 0,
								percentPosition: true
							}).parent().find(".bne-gallery-loader").fadeOut();
							$("#bne-gallery-masonry-'.$rand_id.' .gallery-single").css("opacity", "1");
						});
					});'
				);

				// Responsive CSS - Resize the columns down based on viewport. Yes I know, this is a <style> in the body document.
				if( 'true' == $atts['responsive'] ) {
					$responsive_css = '<style type="text/css">';
					if( $atts['columns'] > 5 ) {
						// Reduce 6+ columns down to 5
						$gutter_space = 4 * $atts['gutter'].'px';
						$responsive_css .= '@media (max-width: 1200px) { #bne-gallery-masonry-'.$rand_id.' .col-'.$atts['columns'].'-masonry { width: calc( ( 100% - '.$gutter_space.' ) / 5 ) !important; }  }';
					}
					if( $atts['columns'] > 4 ) {
						// Reduce 5+ columns down to 4
						$gutter_space = 3 * $atts['gutter'].'px';
						$responsive_css .= '@media (max-width: 980px)  { #bne-gallery-masonry-'.$rand_id.' .col-'.$atts['columns'].'-masonry { width: calc( ( 100% - '.$gutter_space.' ) / 4 ) !important; }  }';
					}
					if( $atts['columns'] > 3 ) {
						// Reduce 4+ columns down to 3
						$gutter_space = 2 * $atts['gutter'].'px';
						$responsive_css .= '@media (max-width: 768px)  { #bne-gallery-masonry-'.$rand_id.' .col-'.$atts['columns'].'-masonry { width: calc( ( 100% - '.$gutter_space.' ) / 3 ) !important; }  }';
					}
					if( $atts['columns'] > 2 ) {
						// Reduce 3+ columns down to 2
						$gutter_space = 1 * $atts['gutter'].'px';
						$responsive_css .= '@media (max-width: 480px)  { #bne-gallery-masonry-'.$rand_id.' .col-'.$atts['columns'].'-masonry { width: calc( ( 100% - '.$gutter_space.' ) / 2 ) !important; }  }';
					}
					$responsive_css .= '</style>';
					$output .= $responsive_css;
				}


				// Output the gallery
				$output .= '<div id="bne-gallery-masonry-'.$rand_id.'" class="bne-gallery-extended bne-gallery-masonry-wrapper gallery">';
					
					// Loading Indicator
					$output .= '<div class="bne-gallery-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';
					
					// Grabs the image ID's in the [gallery] shortcode
					$image_ids = explode( ',', $atts['ids'] );
	
					// Check if orderby is set to "rand", if so shuffle the stack
					if( $atts['orderby'] == 'rand' ) { shuffle( $image_ids ); }
					
							
					// Loop through Image Id's
					foreach( $image_ids as $id ) {
						$output .= '<div class="gallery-single gallery-item image-id-'.$id.' col-'.$atts['columns'].'-masonry" style="margin-bottom: '.$atts['gutter'].'px; width: '.$grid_col_width.';">';
							
							// Link: File
							if( !empty( $atts['link'] ) && 'file' === $atts['link'] ) {
								$output .= wp_get_attachment_link( $id, $atts['size'], false, false, false );
							// Link: None
							} elseif( !empty( $atts['link'] ) && 'none' === $atts['link'] ) {
								$output .= wp_get_attachment_image( $id, $atts['size'], false );
							// Link: Attachment
							} else {
								$output .= wp_get_attachment_link( $id, $atts['size'], true, false, false );
							}

							// Caption
							if( $atts['caption'] == 'true' ) {
								$meta = wp_prepare_attachment_for_js( $id );
								$caption = null;
								if( $meta['caption'] ) {
									$caption = '<div class="caption-overlay">';
										$caption .= '<div class="caption">';
											$caption .= ( $meta['caption'] ) ? '<span class="caption-description">'.$meta['caption'].'</span>' : '';
										$caption .= '</div>';
									$caption .= '</div>';

									// Link: File
									if( !empty( $atts['link'] ) && 'file' === $atts['link'] ) {
										$output .= wp_get_attachment_link( $id, false, false, false, $caption );
									// Link: None
									} elseif( !empty( $atts['link'] ) && 'none' === $atts['link'] ) {
										$output .= $caption;
									// Link: Attachment
									} else {
										$output .= wp_get_attachment_link( $id, false, true, false, $caption );
									}

								}

							}

						$output .= '</div>';
					}
		
				$output .= '</div>';
			
			}
				
			return $output;
		
		}

		
		
		/*
		 * 	Gallery Extended CSS
		 *
		 *	Adds the needed CSS for the carousel and masonry to the wp_head().
		 *
		 *	@since		v1.0
		 *	@updated	v1.1
		 *
		*/
		function gallery_css() {
		
			?>
			<!-- BNE Gallery Extended CSS -->
			<style type="text/css">
				
				/* == General Captions == */
				.bne-gallery-extended .caption-title,
				.bne-gallery-extended .caption-description {
					display: block;
				}
				.bne-gallery-extended .caption-title {
					font-weight: bold;
				}

				/* == Carousel == */
				.bne-gallery-carousel-wrapper {
				    position: relative;
				    height: 18em;
				    margin: 50px auto;
				}
				.bne-gallery-carousel-wrapper .carousel-slider {
				    height: 18em;
				    width: 90%;
				    margin: 0 auto;
				    padding: 0;
				    list-style: none;
				}
				@media only screen and (max-width:768px) {
					.bne-gallery-carousel-wrapper .carousel-slider {
						width: 75%;
					}
				}
				.bne-gallery-carousel-wrapper .slide {
				    display: none; /* Will show via js */
				}
				.bne-gallery-carousel-wrapper .gallery-item img {
					padding: 0;
					border: none;
					box-shadow: none;
					border-radius: 0px;
				}
				.bne-gallery-carousel-wrapper .roundabout-moveable-item img {
				    display: block;
				    max-width: 100%;
				    cursor: pointer;
				}
				.bne-gallery-carousel-wrapper .roundabout-nav a {
					position: absolute;
				    display: block;
				    width: 30px;
				    height: 30px;
				    z-index: 998;
					top: 50%;
					transform: translateY(-50%);
				    color: #999;
				    line-height: 30px;
				    font-size: 25px;
				    outline: 0;
				    border: none;
				    box-shadow: none;
				}
				.bne-gallery-carousel-wrapper .roundabout-nav a:hover,
				.bne-gallery-carousel-wrapper .roundabout-nav a:focus {
				    color: #666
				}
				.bne-gallery-carousel-wrapper .roundabout-nav a.prev { 
					left: 5px; 
				}
				.bne-gallery-carousel-wrapper .roundabout-nav a.next { 
					right: 5px;
				}
				.bne-gallery-carousel-wrapper .roundabout-in-focus img { 
					cursor: auto;
				}
				.bne-gallery-carousel-wrapper .caption { 
					opacity: 0; 
					text-align: center; 
					padding: 8px; 
					font-size: 12px; 
					transition: opacity .3s ease;
				}
				.bne-gallery-carousel-wrapper .roundabout-in-focus .caption { 
					opacity: 1;
				}
				
				/* == Masonry == */
				.bne-gallery-masonry-wrapper { 
					margin-bottom: 10px;
				}
				.bne-gallery-masonry-wrapper .gallery-single {
					position: relative;
					padding: 0px;
					margin: 0px;
					margin-bottom: 5px;
					opacity: 0;
					transition: opacity 1s ease;
					overflow: hidden;
				}
				.bne-gallery-masonry-wrapper .gallery-single img {
					width: 100%;
					padding: 0;
					border: none;
					box-shadow: none;
					border-radius: 0px;
				}
				.bne-gallery-masonry-wrapper .caption-overlay {
					position: absolute;
					opacity: 0;
					left: 0;
					right: 0;
					height: 100%;					
					bottom: -100%;
					text-align: center;
					font-size: 14px;
					background: rgba(0, 0, 0, .6);
					transition: all .3s ease;
				}
				.bne-gallery-masonry-wrapper .gallery-single:hover .caption-overlay {
					opacity: 1;
					bottom: 0;
					height: 100%;					
				}
				.bne-gallery-masonry-wrapper .caption {
					position: absolute;
					top: 50%;
					bottom: auto;
					left: 0;
					right: 0;
					transform: translateY(-50%);
					padding: 5%;
					text-align: center;
					color: white;
				}

				/* == Loading Indicator == */
				.bne-gallery-loader {
					margin: 100px auto 0;
					width: 70px;
					text-align: center;
				}
				.bne-gallery-carousel-wrapper .bne-gallery-loader {
					margin: -25px -25px 0 0;
					position: absolute;
					top: 50%;
					right: 50%;
					z-index: 999;
				}
				.bne-gallery-loader > div {
					width: 18px;
					height: 18px;
					background-color: #333;
					border-radius: 100%;
					display: inline-block;
					-webkit-animation: bne-bouncedelay 1.4s infinite ease-in-out both;
					animation: bne-bouncedelay 1.4s infinite ease-in-out both;
				}
				.bne-gallery-loader .bounce1 {
					-webkit-animation-delay: -0.32s;
					animation-delay: -0.32s;
				}
				
				.bne-gallery-loader .bounce2 {
					-webkit-animation-delay: -0.16s;
					animation-delay: -0.16s;
				}
				@-webkit-keyframes bne-bouncedelay {
					0%, 80%, 100% { -webkit-transform: scale(0) }
					40% { -webkit-transform: scale(1.0) }
				}
				@keyframes bne-bouncedelay {
					0%, 80%, 100% { -webkit-transform: scale(0); transform: scale(0); } 
					40% { -webkit-transform: scale(1.0); transform: scale(1.0); }
				}
			</style>	
			<?php
		}


		
		/*
		 * 	Gallery Extended Media Settings
		 *
		 *	Adds a custom field to the Media Settings popup
		 *	to allow selecting the new display option.
		 *
		 *	@since		v1.0
		 *	@updated	v1.0.2
		 *
		*/
		function gallery_media_settings() {
		
			
			?>
			<!-- Display Type Option -->
			<p id="tmpl-bne-gallery-extended-setting">
				<label class="setting">
					<span><?php _e( 'Display', 'bne-gallery-extended' ); ?></span>
					<select name="display" data-setting="display">
						<option value="default"><?php _e( 'Default Grid', 'bne-gallery-extended' ); ?></option>
						<option value="carousel"><?php _e( '3D Carousel', 'bne-gallery-extended' ); ?></option>
						<option value="masonry"><?php _e( 'Masonry', 'bne-gallery-extended' ); ?></option>
					</select>
				</label>
			</p>
		
			<!-- Add the option into wp.media.gallery -->
			<script type="text/javascript">
		
				jQuery(document).ready(function(){

					// Add your shortcode attribute and its default value to the gallery settings list
					_.extend(wp.media.gallery.defaults, {
						display: 'default'
					});
					
					// Join default gallery settings template with ours
					if (!wp.media.gallery.templates) wp.media.gallery.templates = ['gallery-settings'];
					wp.media.gallery.templates.push('bne-gallery-extended-setting');
					
					// loop through list -- allowing for other templates/settings
					wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
						template: function (view) {
							var output = '';
							for (var i in wp.media.gallery.templates) {
								output += wp.media.template(wp.media.gallery.templates[i])(view);
							}
							return output;
						}
					});
					
				});
				
		  	</script>
		  <?php

		}
	
	}
		
	// Initiate the Class
	$BNE_gallery_extended = new BNE_GALLERY_EXTENDED();

}