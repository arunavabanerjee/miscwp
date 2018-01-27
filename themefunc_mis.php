//Gets post cat slug and looks for single-[cat slug].php and applies it
add_filter('single_template', create_function(
 '$the_template',
 'foreach( (array) get_the_category() as $cat ) {
 if ( file_exists(TEMPLATEPATH . "/single-{$cat->slug}.php") )
 return TEMPLATEPATH . "/single-{$cat->slug}.php"; }
 return $the_template;' )
);

//category in URL
One thing to note though, it's suggested to add a number at the beginning of your permalinks 
to reduce the number of rewrite rules WordPress has to generate to resolve all of your URLs.
The permalink strings for your examples would be:
/%category%/%postname%
/%category%/%post_id%
/%category%/%post_id%/%postname%

//order posts by category
//category arranged from latest 
<tbody>
<?php
$p_category = 31;
//$categories = get_categories('orderby=term_id&child_of='.$p_category.'&hide_empty=0');
$categories = get_categories('orderby=ID&order=DESC&child_of='.$p_category.'&hide_empty=1');
foreach( $categories as $category ) { 
    $caid=$category->term_id;
    $cname=$category->name;
    ?>
<tr>
<td><strong><?php echo $cname; ?></strong></td>
<td></td>
</tr>
<?php query_posts('cat='.$caid.''.'&order=ASC&showposts=50'); 
while (have_posts()) : the_post(); 
$excontent = get_the_content(); 
$title = get_the_title(); 
//$key_acc_values = get_post_meta($post->ID, 'achievement_year'); ?>
<tr>
<td><?php echo $title; ?></td>
<td><?php echo do_shortcode($excontent); ?></td>
</tr>
<?php endwhile; ?>
<?php } ?>
</tbody>

//========================================================================

//order posts by category
//category arranged from latest 
<tbody>
<?php
$p_category = 31;
$categories = get_categories('orderby=term_id&child_of='.$p_category.'&hide_empty=0');
foreach( $categories as $category ) { 
    $caid=$category->term_id;
    $cname=$category->name;
    ?>
<tr>
<td><strong><?php echo $cname; ?></strong></td>
<td></td>
</tr>
<?php query_posts('cat='.$caid.''.'&order=ASC&showposts=50'); 
while (have_posts()) : the_post(); 
$excontent = get_the_content(); 
$title = get_the_title(); 
//$key_acc_values = get_post_meta($post->ID, 'achievement_year'); ?>
<tr>
<td><?php echo $title; ?></td>
<td><?php echo do_shortcode($excontent); ?></td>
</tr>
<?php endwhile; ?>
<?php } ?>

</tbody>

