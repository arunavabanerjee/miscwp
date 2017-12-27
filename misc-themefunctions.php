
-----------
<?php $theme_option = get_option('theme_option'); ?>
<?php $frontpage_id = get_option( 'page_on_front' );
	if ($frontpage_id == '407') {
		wp_redirect( site_url());
    exit;
	} 
?>
