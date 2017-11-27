
First, copy the  woocommerce.php file to your child theme and replace the content with the following code:

<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package zerif
 */
get_header(); ?>
<div class="clear"></div>
</header> <!-- / END HOME SECTION  -->
<div id="content" class="site-content">
	<div class="container">
		<div class="content-left-wrap col-md-9">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
					<?php woocommerce_content(); ?>
				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- .content-left-wrap -->
		<div class="sidebar-wrap col-md-3 content-left-wrap">
			<?php get_sidebar(); ?>
		</div>
	</div><!-- .container -->
<?php get_footer(); ?>
Now copy also the  page.php file to your child theme, and replace its content with this:

<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package zerif
 */
get_header(); ?>
<div class="clear"></div>
</header> <!-- / END HOME SECTION  -->
<div id="content" class="site-content">
	<div class="container">
		<div class="content-left-wrap col-md-9">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
					<?php 
					while ( have_posts() ) : 
						the_post();
						get_template_part( 'content', 'page' );
						/* If comments are open or we have at least one comment, load up the comment template */
						if ( comments_open() || '0' != get_comments_number() ) :
							comments_template();
						endif;
					endwhile;
				?>
				</main><!-- #main -->
			</div><!-- #primary -->
		</div>
		<div class="sidebar-wrap col-md-3 content-left-wrap">
			<?php get_sidebar(); ?>
		</div>
	</div><!-- .container -->
<?php get_footer(); ?>

---------------------------------------------------------------------
	
	  global $post;
  // get categories
  $terms = wp_get_post_terms( $post->ID, 'product_cat' );
  foreach ( $terms as $term ) $cats_array[] = $term->term_id;
  $query_args = array( 'post__not_in' => array( $post->ID ), 'posts_per_page' => 5, 'no_found_rows' => 1, 'post_status' => 'publish', 'post_type' => 'product', 'tax_query' => array( 
    array(
      'taxonomy' => 'product_cat',
      'field' => 'id',
      'terms' => $cats_array
    )));
  $r = new WP_Query($query_args);
		
  if ($r->have_posts()) {
    ?>
    <ul class="product_list_widget">
      <?php while ($r->have_posts()) : $r->the_post(); global $product; ?>
        <li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
    	<?php if (has_post_thumbnail()) the_post_thumbnail('shop_thumbnail'); else echo '<img src="'. woocommerce_placeholder_img_src() .'" alt="Placeholder" width="'.$woocommerce->get_image_size('shop_thumbnail_image_width').'" height="'.$woocommerce->get_image_size('shop_thumbnail_image_height').'" />'; ?>
    	<?php if ( get_the_title() ) the_title(); else the_ID(); ?>
        </a> <?php echo $product->get_price_html(); ?></li>
      <?php endwhile; ?>
    </ul>
    <?php
    // Reset the global $the_post as this query will have stomped on it
    wp_reset_query();
  }
	
	
	
	
------------------------------------------------------------------

WooCommerce Single Product Page Default add_actions

// These are actions you can unhook/remove!
 
add_action( 'woocommerce_before_single_product', 'wc_print_notices', 10 );
 
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
 
add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
 
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
 
add_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
add_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
add_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
add_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
add_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
 
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
 
add_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );
add_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating', 10 );
add_action( 'woocommerce_review_meta', 'woocommerce_review_display_meta', 10 );
add_action( 'woocommerce_review_comment_text', 'woocommerce_review_display_comment_text', 10 );
