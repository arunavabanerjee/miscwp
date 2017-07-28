<?php
/**
* Template Name: User Page
*
* @package WordPress
* @subpackage Twenty_Fourteen
* @since Twenty Fourteen 1.0
*/

get_header(); ?>

<div id=”main-content”>

<?php
if ( is_front_page() && twentyfourteen_has_featured_posts() ) {
// Include the featured content template.
get_template_part( ‘featured-content’ );
}
?>

<div id=”primary”>
<div id=”content” role=”main”>
<?php
global $current_user;
get_currentuserinfo();

$current_user_id = $current_user->ID;

if (isset($_POST[‘submit’])){

require_once(ABSPATH . ‘/wp-load.php’);
require_once(ABSPATH . “wp-admin” . ‘/includes/file.php’);
require_once(ABSPATH . “wp-admin” . ‘/includes/image.php’);

$filename =  $_FILES[‘userProfileImage’][‘name’];

$uploaddir = wp_upload_dir(); // get wordpress upload directory
$myDirPath = $uploaddir[‘path’];
$myDirUrl = $uploaddir[‘url’];

$MyImage = rand(0,5000).$_FILES[‘userProfileImage’][‘name’];
$image_path = $myDirPath.’/’.$MyImage;
move_uploaded_file($_FILES[‘userProfileImage’][‘tmp_name’],$image_path);

$file_array = array(
‘name’ => $_FILES[‘userProfileImage’][‘name’],
‘type’    => $_FILES[‘userProfileImage’][‘type’],
‘tmp_name’    => $_FILES[‘userProfileImage’][‘tmp_name’],
‘error’    => $_FILES[‘userProfileImage’][‘error’],
‘size’    => $_FILES[‘userProfileImage’][‘size’],
);

//$uploaded_file = wp_handle_upload( $file_array, $upload_overrides );

$file = $MyImage;
$uploadfile = $myDirPath.’/’ . basename( $file );
$filename = basename( $uploadfile );
//$wp_filetype = wp_check_filetype( basename( $uploaded_file[‘file’] ), null );
$wp_filetype = wp_check_filetype(basename($filename), null );
$attachment = array(
‘post_mime_type’ => $wp_filetype[‘type’],
‘post_title’ => preg_replace(‘/\.[^.]+$/’, ”, $_FILES[‘userProfileImage’][‘name’]),
‘post_content’ => ”,
‘post_status’ => ‘inherit’
);
//$uploadfile = $uploads[‘path’].’/’ . basename( $filename );

//$attach_id = wp_insert_attachment( $attachment, $uploadfile );
$attachment_id = wp_insert_attachment( $attachment, $uploadfile );

//$attach_data = wp_generate_attachment_metadata( $attachment_id, $uploadfile );
$attach_data = wp_generate_attachment_metadata( $attachment_id, $uploadfile );

$attachimage_url = $uploads[‘url’].’/’.basename( $filename ) ;
wp_update_attachment_metadata( $attachment_id, $attach_data );

//echo ‘update_field( “profile_image”, ‘.$attachment_id.’, “user_”‘.$current_user_id.’ )’    ;
update_field( ‘profile_image’, $attachment_id, “user_”.$current_user_id );

//echo json_encode(array($attachimage_url));

}
?>
<?php

global $current_user;
get_currentuserinfo();

/*echo “<pre>”;
print_r($current_user);
echo “</pre>”;*/

$current_user_id = $current_user->ID;
$profile_image = get_field( ‘profile_image’,’user_’.$current_user_id );

/*echo “<pre>”;
print_r($profile_image);
echo “</pre>”;*/

$current_display_name = $current_user->data->display_name;
$current_user_email = $current_user->data->user_email;

?>
<form name=”artist_basic_profile” id=”artist_basic_profile” action=”” method=”post” enctype=”multipart/form-data”>
<img src=”<?php echo $profile_image[‘sizes’][‘thumbnail’];?>”>
<?php if($current_user_id){ ?>
<input type=”file” id=”userProfileImage” name=”userProfileImage” >
<input id=”submit” type=”submit” name=”submit” value=”Save” >
<?php } ?>
</form>
</div><!– #content –>
</div><!– #primary –>
</div><!– #main-content –>

<?php
get_sidebar();
get_footer();

https://ravigadhiya.wordpress.com/2014/02/05/upload-user-profile-image-from-front-end/
