
/**
 * Adding a custom search widget
 */
class Custom_Search_Widget extends WP_Widget{

function __construct() {
  parent::__construct('custom_search_widget', 
                      __('Custom Search Widget', 'custom_widget'), 
                      array( 'description' => __( 'Custom Search Widget', 'custom_widget' ), ) 
                     );
}

public function widget( $args, $instance ) {

echo $args['before_widget'];

  echo __( 'Custom Search!', 'custom_widget' );  ?>
  <div class="">
  <form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
    <label>Airport</label> 
    <input class="search-field" type="text" name="airport" placeholder="<?php _e('Airport','trizzy') ?>" value=""/>
    <label>Destinations</label> 
    <input class="search-field" type="text" name="destinations" placeholder="<?php _e('Destinations','trizzy') ?>" value=""/>
    <label>Earliest Arrivals</label> 
    <input class="search-field" type="text" name="earliest_arrivals" placeholder="<?php _e('Earliest Arrivals','trizzy') ?>" value=""/>
    <label>Latest Returns</label> 
    <input class="search-field" type="text" name="latest_returns" placeholder="<?php _e('Latest Returns','trizzy') ?>" value=""/>
    <label>Rice Season</label> 
    <input class="search-field" type="text" name="rice_season" placeholder="<?php _e('Rice Season','trizzy') ?>" value=""/>
    <label>Adult</label> 
    <input class="search-field" type="text" name="adult" placeholder="<?php _e('Adult','trizzy') ?>" value=""/>
    <label>Age Of First Child</label> 
    <input class="search-field" type="text" name="age_of_child1" placeholder="<?php _e('Age Of Child','trizzy') ?>" value=""/>
    <label>Age Of Second Child</label> 
    <input class="search-field" type="text" name="age_of_child2" placeholder="<?php _e('Age Of Child','trizzy') ?>" value=""/>
    <label>Age of Third Child</label> 
    <input class="search-field" type="text" name="age_of_child3" placeholder="<?php _e('Age Of Child','trizzy') ?>" value=""/>
    <input type="hidden" value="72" name="cat" id="scat" />     
    <input type="hidden" value="post" name="post_type" id="ptype" />
    <button class="search-btn" type="submit"><i class="fa fa-search"></i></button>
  </form>
  </div>

<?php
echo $args['after_widget'];

}
}
