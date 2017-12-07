=== BNE Gallery Extended ===
Contributors: bluenotes
Tags: WordPress gallery, gallery, masonry gallery, carousel gallery
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Simple add-on to the default WordPress gallery shortcode, [gallery], to include a 3D Carousel and Masonry display option.


== Description ==

The default WordPress [gallery] shortcode only displays your images in a traditional column grid. BNE Gallery Extended adds a new option called "display" allowing you to also show the images as a 3D carousel or masonry grid.

When adding a new image gallery or editing an existing gallery in your page, a new option will show on the gallery settings to either set the display to 3D Carousel, Masonry, or Default Grid. The default grid is what you have been used to and is the default behavior of WordPress. 3D Carousel adds a jquery rotation to your images and Masonry is similar to the default grid but allows different sizes and stacks them. The Masonry grid will also automatically reduce the number of columns for tablet and mobile screens.



== Installation ==
1. Upload "bne-gallery-extended" folder to the "wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. A new option will be added to the WP gallery modal window to select the display type. Otherwise add display="carousel" or display="masonry" to the [gallery] shortcode.


== Frequently Asked Questions ==

= Display as a 3D carousel =

Add display="carousel" or display="carrousel" to the [gallery] shortcode. Example: [gallery display="carousel" ids="2,5,22,13,2"]


= Display as Masonry Grid =

Add display="masonry" to the [gallery] shortcode. Use "columns" to set the number of columns within the grid and "gutter" to set the distance between each image. The number of columns will reduce based on screen size. Example: [gallery display="masonry" gutter="4" ids="2,5,22,13,2"]


= New Shortcode attributes added to [gallery] =

display

* Options: carousel or masonry
* Description: Sets the display type for the WordPress [gallery] shortcode.
* Example: [gallery display="carousel" gutter="4" ids="2,5,22,13,2"] or [gallery display="masonry" ids="2,5,22,13,2"]

caption

* Options: true or false (default: false)
* Description: shows the image caption below the centered image.
* Example: [gallery display="carousel" caption="true" ids="2,5,22,13,2"]

gutter

* Options: Any numerical value, ex: 5 or 3. (default: 5)
* Description: Masonry only - sets the distance surrounding each image in the grid layout. If you use a high number, this may negatively impact the column sizes on mobile. Stay between 5 and 15 for best results.
* Example: [gallery display="masonry" gutter="4" ids="2,5,22,13,2"]

responsive

* Options: true or false. (default: true)
* Description: Masonry only - When used, the number of columns will reduce based on the window width or viewport. For example, 6+ columns will go down to 5 columns at < 1200 viewport, 5+ columns down to 4 columns at < 980 viewport, 4+ columns down to 3 at < 768 viewport, and finally 3+ columns down to 2 at < 480 viewport and smaller.
* Example: [gallery display="masonry" responsive="true" columns="6" ids="2,5,22,13,2"]


autoplay

* Options: Any numerical value, ex: 4000 or 2000. (default: none)
* Description: Carousel only - Sets the carousel to play automatically. The number represents the time each image is focused on in milliseconds (4000 is equal to 4 seconds). 
* Example: [gallery display="carousel" autoplay="4000" ids="2,5,22,13,2"]


== Screenshots ==
1. Display Options in the WP Gallery Window
2. 3D Carousel Display
3. Masonry Display
4. Traditional Grid



== Changelog ==

= 1.1.1 November 8, 2017 =
* Fix: Adjust masonry caption if using the link option in [gallery] to respect either file, none, or attachment.


= 1.1 November 6, 2017 =
* New: Captions are now available with the masonry display while the image is hovered.
* New: Image titles are now included in the carrousel.
* Enhancement: Caption meta title and caption fields are separated for greater css customization.


= 1.0.4 June 17, 2017 =
* Notice: Now requires WP 4.5+
* Fix: Masonry display items would sometimes overlap one another.
* Enhancement: Move masonry inline js to the footer.
* New: Add a loading indicator to masonry gallery


= 1.0.3 May 15, 2016 =
* Enhancement: Better calculation of columns with any gutter size for the masonry display.
* New: Add autoplay option to carrousel by adding autoplay="4000" within the gallery shortcode. 4000 (in milliseconds) is equal to 4 seconds.
* New: Option to disable masonry responsive columns by adding responsive="false" to the gallery shortcode. Default behavior will reduce 6+ columns down to 5 columns at < 1200 viewport, 5+ columns down to 4 columns at < 980 viewport, 4+ columns down to 3 at < 768 viewport, and finally 3+ columns down to 2 at < 480 viewport and smaller.


= 1.0.2 March 27, 2016 =
* Clean up and adjust css.


= 1.0.1 January 22, 2016 =
* Add caption option for carousel


= 1.0 December 17, 2015 =
* First Public Release. *