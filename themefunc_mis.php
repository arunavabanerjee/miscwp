
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

