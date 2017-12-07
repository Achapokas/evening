<?php

function post_per_page_simple($check_spp){

  if (is_numeric($check_spp) && $check_spp > 0) : 

       $check_spp; 

  else:   

    $check_spp = get_option('posts_per_page') ; 

  endif; 

  return $check_spp;

}


// pagination genertaion function 

function simple_masonry_pagination($numpages = '', $pagerange = '', $paged='') {

  if (empty($pagerange)) {
    $pagerange = 2;
  }

  /**
   * This first part of our function is a fallback
   * for custom pagination inside a regular loop that
   * uses the global $paged and global $wp_query variables.
   * 
   * It's good because we can now override default pagination
   * in our theme, and use this function in default queries
   * and custom queries.
   */
  global $paged;

  if (empty($paged)) {
   
    if ( get_query_var('paged') ) {
          $paged = get_query_var('paged');
      } elseif ( get_query_var('page') ) {
          $paged = get_query_var('page');
      } else {
          $paged = 1;
      }
     
  }

  if ($numpages == '') {
    global $wp_query;
    $numpages = $wp_query->max_num_pages;
    if(!$numpages) {
        $numpages = 1;
    }
  }

  /** 
   * We construct the pagination arguments to enter into our paginate_links
   * function. 
   */
  $pagination_args = array(
    'base'            => get_pagenum_link(1) . '%_%',
    'format'          => 'page/%#%',
    'total'           => $numpages,
    'current'         => $paged,
    'show_all'        => False,
    'end_size'        => 1,
    'mid_size'        => $pagerange,
    'prev_next'       => True,
    'prev_text'       => __('<i class="fa fa-chevron-left"></i>'),
    'next_text'       => __('<i class="fa fa-chevron-right"></i>'),
    'type'            => 'plain',
    'add_args'        => false,
    'add_fragment'    => ''
  );

  $paginate_links = paginate_links($pagination_args);

  if ($paginate_links) {
    echo "<nav class='sm-pagination'>";
      echo $paginate_links;
    echo "</nav>";
  }


}

function simple_masonry_shortcode($atts, $content = null) {

        ob_start();

        extract(shortcode_atts(array(
                
                'sm_post_type'        => 'post',
                'gallery'             => 'no',
                'sm_category_name'    => ''
               
                 ), $atts));


        $paged = get_query_var( 'paged' ) ?: ( get_query_var( 'page' ) ?: 1 );


        $sm_args = array(
                    'posts_per_page'   => intval(post_per_page_simple(get_option('simple_post_per_page'))),
                    'orderby'          => get_option('simple_post_orderby'),
                    'order'            => get_option('simple_post_order'),
                    'post_type'        => $sm_post_type,
                    'category_name'    => $sm_category_name,
                    'suppress_filters' => false,
                    'post_status'      => 'publish',
                    'paged'            => $paged,
                    'meta_key'         => $gallery == 'yes' ? '_thumbnail_id' : ''
              );

        $wp_query = new WP_Query($sm_args);

        global $post;

        $sm_post = get_posts($sm_args);
       
     ?>

    <div class="smblog_masonry_numcol" >
     
        <div class="sm-grid sm-effect" id="sm-grid-layout">  

          <?php 
         
           foreach ($sm_post as $post) : 

                  setup_postdata($post);

                $thumbnail = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); 
                $sm_date =  get_the_date( get_option('date_format'), $post->ID );
            ?>
  
            <?php if ($gallery == 'no' || $thumbnail) { ?>
            <div class="grid-sm-boxes-in post-<?php echo $post->ID;?>">
               <div class="grid-sm-border">
                  <?php

                    if($thumbnail) :

                     if (get_option('simple_post_darkbox')) : 

                       echo '<img class="img-responsive" src="' . $thumbnail . '" data-darkbox="'. $thumbnail .'"
                        data-darkbox-description="<b>' .get_the_title().'</b>"> ';

                      else:

                        echo '<a href ="'.get_the_permalink().'"><img class="img-responsive" src="' . $thumbnail . '"></a>';

                     endif;

                    endif;
                    ?>

             <?php if ($gallery == 'yes') : ?>  

               <?php if (get_option('sm_post_title')) : ?>

                <div class="sm-gallery-title"> 
                  <a href="<?php the_permalink();?>">                  
                     <span class="sm-gallery-textPart"><?php the_title();?></span>
                     <span class="sm-gallery-arrow"><?php echo '<img src="' . plugins_url( '../images/arrow.png', __FILE__ ) . '" > ';?></span> 
                  </a>                 
                </div>

              <?php endif; ?>

            <?php endif ?>
               
               <?php if ($gallery == 'no') { ?>
                <div class="sm-grid-boxes-caption">
                    <div class="sm-post-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></div>                
                    <div class="sm-list-inline sm-grid-boxes-news">
                       <div class="sm-meta"> 
                        <span class="sm-meta-part">
                      <?php if (get_option('simple_post_author')) : ?>
                          <span class="sm-meta-poster">
                          <i class="sm-icon-author"></i><a href="<?php echo get_author_posts_url( get_the_author_meta($post->post_author), get_the_author_meta( 'user_nicename', $post->post_author ) ); ?>"><?php  esc_attr(the_author_meta( 'display_name', $post->post_author ));?></a></span> 
                      <?php endif;?>
                         <span class="sm-meta-date"> <i class="sm-icon-date"> 
                        </i><a href="<?php echo esc_url(get_day_link(get_post_time('Y','',$post->ID), get_post_time('m','',$post->ID), get_post_time('j','',$post->ID))); ?>"><?php echo esc_attr($sm_date); ?></a> </span> 
                       <?php if (get_option('sm_post_comment')) : ?>
                         <span class="sm-meta-likes"><i class="sm-icon-comments"></i><?php echo get_comments_number($post->ID);?></span> 
                       <?php endif;?>
                      </span>
                     </div>
                    </div>  
                   <div class="sm-grid-boxes-quote">
                   <?php if (has_excerpt ($post->ID)) the_excerpt(); else echo '<p>' . wp_trim_words( get_the_content(), 20 ) . '</p>'; ?>
                   </div>
                </div>
              <?php } ?>
                
              </div>
          </div>
            <?php }?>

    <?php endforeach ; ?>
 
        </div>
      
    <?php if (function_exists('simple_masonry_pagination')) {
                simple_masonry_pagination($wp_query->max_num_pages,"",$paged);
          } 
    ?>
    
    <?php wp_reset_postdata(); ?>
 
    </div>


<?php

    return ob_get_clean();

}
add_shortcode("simple_masonry", "simple_masonry_shortcode");