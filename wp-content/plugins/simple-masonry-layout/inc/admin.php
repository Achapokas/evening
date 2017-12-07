<?php 

function simple_masonry_settings(){

?>
     <div class="wrap">
        <h1>Simple Masonry Layout Settings</h1>
        <form action='options.php' method='post'>
            <?php
                settings_fields("simple_masonry_settings_section");
                do_settings_sections("simple_masonry_options");      
                submit_button(); 
            ?>          
        </form>
     </div>
  <?php
 
}

function simple_masonry_admin()
{
    add_submenu_page("options-general.php","Simple Masonry Layout", "Simple Masonry Layout", "manage_options", "simple_masonry_layout", "simple_masonry_settings");
  
}

add_action("admin_menu", "simple_masonry_admin");

function sm_layout_shortcode(){
    ?>
      <ul>
        <li><strong>All Shortcodes Outputs Masonry Layout according to settings Below.</strong></li>
        <li><strong>Masonry Layout of Default posts : </strong>[simple_masonry]</li>
        <li><strong>Masonry Layout of Default posts from Specific Category  : </strong>[simple_masonry sm_category_name="catergory_slug"]. For example if catergory_slug is travel, shortcode will be [simple_masonry sm_category_name="travel"]</li>
        <li><strong>Masonry Layout of posts of your choice : </strong>[simple_masonry sm_post_type="post_type"]. For example if post_type is testimonial, shortcode will be [simple_masonry sm_post_type="testimonial"]</li>
        <li><strong>Masonry Layout of posts as gallery : </strong>[simple_masonry gallery="yes"]</li>
        <li><strong>Masonry Layout of posts of your choice as gallery : </strong>[simple_masonry sm_post_type="post_type" gallery="yes"] For example if post_type is testimonial, shortcode will be [simple_masonry sm_post_type="testimonial" gallery="yes"]</li>
         <br/>
        

      </ul> 
    <?php
}


function sm_post_per_field(){
    ?>
        <input type="number" name="simple_post_per_page" id="simple_post_per_page" value="<?php echo post_per_page_simple(get_option('simple_post_per_page'));?>"  min="1" step="1" /><br/><i>Limit number of posts to be displayed. Default : Settings -> Reading.</i>
    <?php
}


function sm_post_orderby_field(){

  $simple_order_by = array(

               'none'          => 'None',
               'ID'            => 'ID',
               'author'        => 'Author',
               'title'         => 'Title',
               'date'          => 'Date',
               'modified'      => 'Modified',
               'parent'        => 'Parent',
               'rand'          => 'Random',
               'comment_count' => 'Comment Count',
               'menu_order'    => 'Menu Order'
               
              
              );
 ?> 
          <select name="simple_post_orderby">
            
              <?php foreach ($simple_order_by as $simple_order_by_key => $simple_order_by_value ) { ?>
              <option value="<?php echo $simple_order_by_key; ?>" <?php if ( get_option('simple_post_orderby') == $simple_order_by_key) echo 'selected="selected"'; ?>><?php echo $simple_order_by_value; ?></option>
  <?php } ?>
              </select>

<?php
}


function sm_post_order_field(){

  $simple_order = array(
              'ASC'         => 'Ascending',
              'DESC'        => 'Descending'
              
              );
 ?> 
          <select name="simple_post_order">
            
                  <?php foreach ($simple_order as $simple_order_key => $simple_order_value ) { ?>
    <option value="<?php echo $simple_order_key; ?>" <?php if ( get_option('simple_post_order') == $simple_order_key) echo 'selected="selected"'; ?>><?php echo $simple_order_value; ?></option>
  <?php } ?>
              </select>

<?php
}


function sm_post_darkbox_field(){
  ?>
    <input type="checkbox" name="simple_post_darkbox" value="1" <?php checked(1, get_option('simple_post_darkbox'), true); ?> /> 
  <?php
}


function sm_post_author_field(){
  ?>
    <input type="checkbox" name="simple_post_author" value="1" <?php checked(1, get_option('simple_post_author'), true); ?> /> 
  <?php
}




function sm_post_comment_field(){
  ?>
    <input type="checkbox" name="sm_post_comment" value="1" <?php checked(1, get_option('sm_post_comment'), true); ?> /> 
  <?php
}


function sm_post_title_field(){
  ?>
    <input type="checkbox" name="sm_post_title" value="1" <?php checked(1, get_option('sm_post_title'), true); ?> /> 
    <i>This setting is only used for Masonry Layout gallery of Posts and Custom Post Type .</i>
  <?php
}




function simple_masonry_admin_settings()
{
    add_settings_section("simple_masonry_settings_section", " ", null, "simple_masonry_options");
    add_settings_field("simple_masonay_shortcode", "Shortcodes", "sm_layout_shortcode", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("simple_post_per_page", "Posts To Display", "sm_post_per_field", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("simple_post_orderby", "Posts Order By", "sm_post_orderby_field", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("simple_post_order", "Posts Order", "sm_post_order_field", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("simple_post_darkbox", "Check if you want to display Darkbox Gallery Popup ", "sm_post_darkbox_field", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("simple_post_author", "Check if you want to display  Post Author ", "sm_post_author_field", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("sm_post_comment", "Check if you want to display  Post Comments ", "sm_post_comment_field", "simple_masonry_options", "simple_masonry_settings_section");
    add_settings_field("sm_post_title", "Check if you want to display Post Title URL in gallery ", "sm_post_title_field", "simple_masonry_options", "simple_masonry_settings_section");


    register_setting("simple_masonry_settings_section", "simple_masonay_shortcode");
    register_setting("simple_masonry_settings_section", "simple_post_per_page");
    register_setting("simple_masonry_settings_section", "simple_post_orderby");
    register_setting("simple_masonry_settings_section", "simple_post_order");
    register_setting("simple_masonry_settings_section", "simple_post_darkbox");
    register_setting("simple_masonry_settings_section", "simple_post_author");
    register_setting("simple_masonry_settings_section", "sm_post_comment");
    register_setting("simple_masonry_settings_section", "sm_post_title");
}

add_action("admin_init", "simple_masonry_admin_settings");

