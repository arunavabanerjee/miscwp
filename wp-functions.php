
<?php

/**
 * Filter the upload size limit for non-administrators.
 *
 * @param string $size Upload size limit (in bytes).
 * @return int (maybe) Filtered size limit.
 */
function filter_site_upload_size_limit( $size ) {
    // Set the upload size limit to 60 MB for users lacking the 'manage_options' capability.
    if ( ! current_user_can( 'manage_options' ) ) {
        // 60 MB.
        $size = 60 * 1024 * 1024;
    }

    if ( current_user_can( 'manage_options' ) ) {
        // 60 MB.
        $size = 100 * 1024 * 1024;
    }

    return $size;
}
add_filter( 'upload_size_limit', 'filter_site_upload_size_limit', 20 );

