<?php
/**
 Template Name: player
 */

get_header(); ?>

<div class="match_bg">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 player_list">

            <div id="horizontalTab">
               <?php $field_name = "field_5a7c0581a4450"; 
                     $field = get_field_object($field_name); //var_dump($field['choices']);
		?>
                     
             <ul class="resp-tabs-list col-sm-4 pull-right text-right">
                    
                       <?php
                    //get all terms (e.g. categories or post tags), then display all posts in each term
                    $taxonomy = 'category';//  e.g. post_tag, category
                    $param_type = 'category__in'; //  e.g. tag__in, category__in
                    $term_args=array(
                      'orderby' => 'name',
                      'order' => 'ASC'
                    );

                    $categories = get_categories($term_args);
              foreach ($categories as $cat) {
                  if($cat->name!='Uncategorized') {
                  ?>
                        <li><?php echo  $cat->name;?> </li>
                  <?php } } ?>        
            </ul> 

            <div class="resp-tabs-container clearfix">    
            <?php $terms = get_terms($taxonomy,$term_args); ?>
	    <?php if ($terms){ ?>
            <?php foreach( $terms as $term ) { ?>
	    <div>
		<?php foreach($field['choices'] as $key=>$value){
                        $args=array(
                          "$param_type" => array($term->term_id),
                          'post_type' => 'post',
                          'post_status' => 'publish',
                          'posts_per_page' => -1,
                          'caller_get_posts'=> 1, 
                          'meta_key' => 'select-player-types',
                          'meta_value' => $key,
                          );
                        $my_query = new WP_Query($args); ?>
		  <?php if( $my_query->have_posts() ) { ?>
                     <div class="row">
                       <h3><?php echo $key; ?></h3>

		       <?php while($my_query->have_posts()) : $my_query->the_post(); 
                       $banner_image = wp_get_attachment_url( get_post_thumbnail_id($loop->ID) ); ?>
                       <div class="col-sm-4 player">
                                    <div class="image_area"> <img src="<?php echo $banner_image;?>" alt="player" /> </div>
                                    <div class="content_sec">
                                        <span><?php the_title(); ?></span>
                                        <em>Age: 13 | <?php echo get_post_meta($post->ID, 'select-player-types', true); ?>, SB</em>
                                    </div>
                        </div>
                        <?php endwhile; ?>
                     </div>		
		<?php }} //endforeach key ?>
	    </div>
	    <?php }
		wp_reset_query();  // Restore global post data stomped by the_post().
	     } //endif terms ?>

	    </div><!-- end resp-tabs-container -->
	    </div><!-- end horizontal tab -->
            </div>
        </div>
    </div>
</div>

<?php get_footer();
