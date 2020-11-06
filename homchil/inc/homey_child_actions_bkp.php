<?php 

/**
 * Search Results for properties, not to display private properties.
 */
add_filter('homey_search_filter', 'homey_listing_search');
function homey_listing_search($search_query)
{

        $tax_query = array();
        $meta_query = array();
        $allowed_html = array();
        $query_ids = '';
        $homey_search_type = homey_search_type();

        $arrive = isset($_GET['arrive']) ? $_GET['arrive'] : '';
        $depart = isset($_GET['depart']) ? $_GET['depart'] : '';
        $start = isset($_GET['start']) ? $_GET['start'] : '';
        $end = isset($_GET['end']) ? $_GET['end'] : '';
        $guests = isset($_GET['guest']) ? $_GET['guest'] : '';
        $pets = isset($_GET['pets']) ? $_GET['pets'] : '';
        $bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '';
        $rooms = isset($_GET['rooms']) ? $_GET['rooms'] : '';
        $room_size = isset($_GET['room_size']) ? $_GET['room_size'] : '';
        $search_country = isset($_GET['search_country']) ? $_GET['search_country'] : '';
        $search_city = isset($_GET['search_city']) ? $_GET['search_city'] : '';
        $search_area = isset($_GET['search_area']) ? $_GET['search_area'] : '';
        $listing_type = isset($_GET['listing_type']) ? $_GET['listing_type'] : '';
        $lat = isset($_GET['lat']) ? $_GET['lat'] : '';
        $lng = isset($_GET['lng']) ? $_GET['lng'] : '';
        $search_radius = isset($_GET['radius']) ? $_GET['radius'] : 20;

        $country = isset($_GET['country']) ? $_GET['country'] : '';
        $state = isset($_GET['state']) ? $_GET['state'] : '';
        $city = isset($_GET['city']) ? $_GET['city'] : '';
        $area = isset($_GET['area']) ? $_GET['area'] : '';
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

        $arrive = homey_search_date_format($arrive);
        $depart = homey_search_date_format($depart);
        

        if( homey_option('enable_radius') ) {

            if($homey_search_type == 'per_hour') {
                $available_listings_ids = apply_filters('homey_check_hourly_search_availability_filter', $search_query, $arrive, $start, $end);
            } else {
                $available_listings_ids = apply_filters('homey_check_search_availability_filter', $search_query, $arrive, $depart);
            }
            $radius_ids = apply_filters('homey_radius_filter', $search_query, $lat, $lng, $search_radius);

            if(!empty($available_listings_ids) && !empty($radius_ids)) {
                $query_ids =  array_intersect($available_listings_ids, $radius_ids);

                if(empty($query_ids)) {
                    $query_ids = array(0);
                }

            } elseif(empty($available_listings_ids)) {
                $query_ids = $radius_ids;

            } elseif(empty($radius_ids)) {
                $query_ids = $available_listings_ids;
            }

            if(!empty($query_ids)) {
                $search_query['post__in'] = $query_ids;
            }

        } else {

            if($homey_search_type == 'per_hour') {
                $search_query = apply_filters('homey_check_hourly_search_availability_filter', $search_query, $arrive, $start, $end);
            } else {
                $search_query = apply_filters('homey_check_search_availability_filter', $search_query, $arrive, $depart);
            }

            if(!empty($search_city) || !empty($search_area)) {
                $_tax_query = Array();

                if(!empty($search_city) && !empty($search_area)) {
                    $_tax_query['relation'] = 'AND';
                }

                if(!empty($search_city)) {
                    $_tax_query[] = array(
                        'taxonomy' => 'listing_city',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($search_city)
                    );
                }

                if(!empty($search_area)) {
                    $_tax_query[] = array(
                        'taxonomy' => 'listing_area',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($search_area)
                    );
                }

                $tax_query[] = $_tax_query;
            }

            if(!empty($search_country)) {
                $tax_query[] = array(
                    'taxonomy' => 'listing_country',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($search_country)
                );
            }

        }


        $keyword = trim($keyword);
        if (!empty($keyword)) {
            $search_query['s'] = $keyword;
        }
        

        $beds_baths_rooms_search = homey_option('beds_baths_rooms_search');
        $search_criteria = '=';
        if( $beds_baths_rooms_search == 'greater') {
            $search_criteria = '>=';
        } elseif ($beds_baths_rooms_search == 'lessthen') {
            $search_criteria = '<=';
        }

        if(!empty($listing_type)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_type',
                'field' => 'slug',
                'terms' => sanitize_text_field($listing_type)
            );
        }

        if(!empty($country)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_country',
                'field' => 'slug',
                'terms' => sanitize_text_field($country)
            );
        }

        if(!empty($state)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_state',
                'field' => 'slug',
                'terms' => sanitize_text_field($state)
            );
        }

        if(!empty($city)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_city',
                'field' => 'slug',
                'terms' => sanitize_text_field($city)
            );
        }

        if(!empty($area)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_area',
                'field' => 'slug',
                'terms' => sanitize_text_field($area)
            );
        }

        // min and max price logic
        if (isset($_GET['min-price']) && !empty($_GET['min-price']) && $_GET['min-price'] != 'any' && isset($_GET['max-price']) && !empty($_GET['max-price']) && $_GET['max-price'] != 'any') {
            $min_price = doubleval(homey_clean($_GET['min-price']));
            $max_price = doubleval(homey_clean($_GET['max-price']));

            if ($min_price > 0 && $max_price > $min_price) {
                $meta_query[] = array(
                    'key' => 'homey_night_price',
                    'value' => array($min_price, $max_price),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }
        } else if (isset($_GET['min-price']) && !empty($_GET['min-price']) && $_GET['min-price'] != 'any') {
            $min_price = doubleval(homey_clean($_GET['min-price']));
            if ($min_price > 0) {
                $meta_query[] = array(
                    'key' => 'homey_night_price',
                    'value' => $min_price,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        } else if (isset($_GET['max-price']) && !empty($_GET['max-price']) && $_GET['max-price'] != 'any') {
            $max_price = doubleval(homey_clean($_GET['max-price']));
            if ($max_price > 0) {
                $meta_query[] = array(
                    'key' => 'homey_night_price',
                    'value' => $max_price,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        if(!empty($guests)) {
            $meta_query[] = array(
                'key' => 'homey_guests',
                'value' => intval($guests),
                'type' => 'NUMERIC',
                'compare' => '>=',
            );
        }

        if(!empty($pets) && $pets != '0') {
            $meta_query[] = array(
                'key' => 'homey_pets',
                'value' => sanitize_text_field($pets),
                'type' => 'NUMERIC',
                'compare' => '=',
            );
        }

        if (!empty($bedrooms)) {
            $bedrooms = sanitize_text_field($bedrooms);
            $meta_query[] = array(
                'key' => 'homey_listing_bedrooms',
                'value' => $bedrooms,
                'type' => 'CHAR',
                'compare' => $search_criteria,
            );
        }

        if (!empty($rooms)) {
            $rooms = sanitize_text_field($rooms);
            $meta_query[] = array(
                'key' => 'homey_listing_rooms',
                'value' => $rooms,
                'type' => 'CHAR',
                'compare' => $search_criteria,
            );
        }


        if (isset($_GET['area']) && !empty($_GET['area'])) {
            if (is_array($_GET['area'])) {
                $areas = sanitize_text_field($_GET['area']);

                foreach ($areas as $area):
                    $tax_query[] = array(
                        'taxonomy' => 'listing_area',
                        'field' => 'slug',
                        'terms' => $area
                    );
                endforeach;
            }
        }

        if (isset($_GET['amenity']) && !empty($_GET['amenity'])) {
            if (is_array($_GET['amenity'])) {
                $amenities = $_GET['amenity'];

                foreach ($amenities as $amenity):
                    $tax_query[] = array(
                        'taxonomy' => 'listing_amenity',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($amenity)
                    );
                endforeach;
            }
        }

        if (isset($_GET['facility']) && !empty($_GET['facility'])) {
            if (is_array($_GET['facility'])) {
                $facilities = $_GET['facility'];

                foreach ($facilities as $facility):
                    $tax_query[] = array(
                        'taxonomy' => 'listing_facility',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($facility)
                    );
                endforeach;
            }
        }

        if(!empty($room_size)) {
            $tax_query[] = array(
                'taxonomy' => 'room_type',
                'field' => 'slug',
                'terms' => sanitize_text_field($room_size)
            );
        }

	$meta_listing_query[] = array(
    		'key' => 'homey_typeofpropertylisting',
    		'value' => array('public'),
    		'compare' => 'IN'
	);
	$meta_listing_query[] = array(
    		'key' => 'homey_typeofpropertylisting',
    		'compare' => 'NOT EXISTS',
	);
	$meta_listing_query['relation'] = 'OR'; 

	$meta_query[] = $meta_listing_query;


        $meta_count = count($meta_query);

        if( $meta_count > 1 ) {
            $meta_query['relation'] = 'AND';
        }
        if( $meta_count > 0 ){
            $search_query['meta_query'] = $meta_query;
        }


        $tax_count = count( $tax_query );

        if( $tax_count > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if( $tax_count > 0 ){
            $search_query['tax_query'] = $tax_query;
        }
        
        //print_r($search_query); //exit;
        return $search_query;
}

add_action( 'wp_ajax_nopriv_homey_half_map', 'homey_half_map' );
add_action( 'wp_ajax_homey_half_map', 'homey_half_map' );
function homey_half_map() {

        global $homey_prefix, $homey_local;

        $homey_prefix = 'homey_';
        $homey_local = homey_get_localization();

        $homey_search_type = homey_search_type();

        $rental_text = $homey_local['rental_label'];
        
        check_ajax_referer('homey_map_ajax_nonce', 'security');

        $tax_query = array();
        $meta_query = array();
        $allowed_html = array();
        $query_ids = '';

        $cgl_meta = homey_option('cgl_meta');
        $cgl_beds = homey_option('cgl_beds');
        $cgl_baths = homey_option('cgl_baths');
        $cgl_guests = homey_option('cgl_guests');
        $cgl_types = homey_option('cgl_types');
        $price_separator = homey_option('currency_separator');

        $arrive = isset($_POST['arrive']) ? $_POST['arrive'] : '';
        $depart = isset($_POST['depart']) ? $_POST['depart'] : '';
        $guests = isset($_POST['guest']) ? $_POST['guest'] : '';
        $pets = isset($_POST['pets']) ? $_POST['pets'] : '';
        $bedrooms = isset($_POST['bedrooms']) ? $_POST['bedrooms'] : '';
        $rooms = isset($_POST['rooms']) ? $_POST['rooms'] : '';
        $start_hour = isset($_POST['start_hour']) ? $_POST['start_hour'] : '';
        $end_hour = isset($_POST['end_hour']) ? $_POST['end_hour'] : '';
        $room_size = isset($_POST['room_size']) ? $_POST['room_size'] : '';
        $search_country = isset($_POST['search_country']) ? $_POST['search_country'] : '';
        $search_city = isset($_POST['search_city']) ? $_POST['search_city'] : '';
        $search_area = isset($_POST['search_area']) ? $_POST['search_area'] : '';
        $listing_type = isset($_POST['listing_type']) ? $_POST['listing_type'] : '';
        $search_lat = isset($_POST['search_lat']) ? $_POST['search_lat'] : '';
        $search_lng = isset($_POST['search_lng']) ? $_POST['search_lng'] : '';
        $search_radius = isset($_POST['radius']) ? $_POST['radius'] : 20;

        $paged = isset($_POST['paged']) ? ($_POST['paged']) : '';
        $sort_by = isset($_POST['sort_by']) ? ($_POST['sort_by']) : '';
        $layout = isset($_POST['layout']) ? ($_POST['layout']) : 'list';
        $num_posts = isset($_POST['num_posts']) ? ($_POST['num_posts']) : '9';

        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $state = isset($_POST['state']) ? $_POST['state'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $area = isset($_POST['area']) ? $_POST['area'] : '';
        $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

        $arrive = homey_search_date_format($arrive);
        $depart = homey_search_date_format($depart);

        $beds_baths_rooms_search = homey_option('beds_baths_rooms_search');
        $search_criteria = '=';
        if( $beds_baths_rooms_search == 'greater') {
            $search_criteria = '>=';
        } elseif ($beds_baths_rooms_search == 'lessthen') {
            $search_criteria = '<=';
        }

        $query_args = array(
            'post_type' => 'listing',
            'posts_per_page' => $num_posts,
            'post_status' => 'publish',
            'paged' => $paged,
        );

        $keyword = trim($keyword);
        if (!empty($keyword)) {
            $query_args['s'] = $keyword;
        }
        
        if( !empty( $_POST["optimized_loading"] ) ) {
            $north_east_lat = sanitize_text_field($_POST['north_east_lat']);
            $north_east_lng = sanitize_text_field($_POST['north_east_lng']);
            $south_west_lat = sanitize_text_field($_POST['south_west_lat']);
            $south_west_lng = sanitize_text_field($_POST['south_west_lng']);

            $query_args = apply_filters('homey_optimized_filter', $query_args, $north_east_lat, $north_east_lng, $south_west_lat, $south_west_lng );
        }
        

        if( homey_option('enable_radius') ) {
            if($homey_search_type == 'per_hour') {
                $available_listings_ids = apply_filters('homey_check_hourly_search_availability_filter', $query_args, $arrive, $start_hour, $end_hour);
            } else {
                $available_listings_ids = apply_filters('homey_check_search_availability_filter', $query_args, $arrive, $depart);
            }

            $radius_ids = apply_filters('homey_radius_filter', $query_args, $search_lat, $search_lng, $search_radius);

            if(!empty($available_listings_ids) && !empty($radius_ids)) {
                $query_ids =  array_intersect($available_listings_ids, $radius_ids);

                if(empty($query_ids)) {
                    $query_ids = array(0);
                }

            } elseif(empty($available_listings_ids)) {
                $query_ids = $radius_ids;

            } elseif(empty($radius_ids)) {
                $query_ids = $available_listings_ids;
            }

            if(!empty($query_ids)) {
                $query_args['post__in'] = $query_ids;
            }
        } else {

            if($homey_search_type == 'per_hour') {
                $query_args = apply_filters('homey_check_hourly_search_availability_filter', $query_args, $arrive, $start_hour, $end_hour);
            } else {
                $query_args = apply_filters('homey_check_search_availability_filter', $query_args, $arrive, $depart);
            }

            if(!empty($search_city) || !empty($search_area)) {
                $_tax_query = Array();

                if(!empty($search_city) && !empty($search_area)) {
                    $_tax_query['relation'] = 'AND';
                }

                if(!empty($search_city)) {
                    $_tax_query[] = array(
                        'taxonomy' => 'listing_city',
                        'field' => 'slug',
                        'terms' => $search_city
                    );
                }

                if(!empty($search_area)) {
                    $_tax_query[] = array(
                        'taxonomy' => 'listing_area',
                        'field' => 'slug',
                        'terms' => $search_area
                    );
                }

                $tax_query[] = $_tax_query;
            }

            if(!empty($search_country)) {
                $tax_query[] = array(
                    'taxonomy' => 'listing_country',
                    'field' => 'slug',
                    'terms' => homey_traverse_comma_string($search_country)
                );
            }

        }


        if(!empty($listing_type)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_type',
                'field' => 'slug',
                'terms' => homey_traverse_comma_string($listing_type)
            );
        }

        if(!empty($country)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_country',
                'field' => 'slug',
                'terms' => $country
            );
        }

        if(!empty($state)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_state',
                'field' => 'slug',
                'terms' => $state
            );
        }

        if(!empty($city)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_city',
                'field' => 'slug',
                'terms' => $city
            );
        }

        if(!empty($area)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_area',
                'field' => 'slug',
                'terms' => $area
            );
        }

        // min and max price logic
        if (isset($_POST['min-price']) && !empty($_POST['min-price']) && $_POST['min-price'] != 'any' && isset($_POST['max-price']) && !empty($_POST['max-price']) && $_POST['max-price'] != 'any') {
            $min_price = doubleval(homey_clean($_POST['min-price']));
            $max_price = doubleval(homey_clean($_POST['max-price']));

            if ($min_price > 0 && $max_price > $min_price) {
                $meta_query[] = array(
                    'key' => 'homey_night_price',
                    'value' => array($min_price, $max_price),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN',
                );
            }
        } else if (isset($_POST['min-price']) && !empty($_POST['min-price']) && $_POST['min-price'] != 'any') {
            $min_price = doubleval(homey_clean($_POST['min-price']));
            if ($min_price > 0) {
                $meta_query[] = array(
                    'key' => 'homey_night_price',
                    'value' => $min_price,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                );
            }
        } else if (isset($_POST['max-price']) && !empty($_POST['max-price']) && $_POST['max-price'] != 'any') {
            $max_price = doubleval(homey_clean($_POST['max-price']));
            if ($max_price > 0) {
                $meta_query[] = array(
                    'key' => 'homey_night_price',
                    'value' => $max_price,
                    'type' => 'NUMERIC',
                    'compare' => '<=',
                );
            }
        }

        if(!empty($guests)) {
            $meta_query[] = array(
                'key' => 'homey_guests',
                'value' => $guests,
                'type' => 'NUMERIC',
                'compare' => '>=',
            );
        }

        if(!empty($pets) && $pets != '0') {
            $meta_query[] = array(
                'key' => 'homey_pets',
                'value' => $pets,
                'type' => 'NUMERIC',
                'compare' => '=',
            );
        }

        if (!empty($bedrooms)) {
            $bedrooms = sanitize_text_field($bedrooms);
            $meta_query[] = array(
                'key' => 'homey_listing_bedrooms',
                'value' => $bedrooms,
                'type' => 'CHAR',
                'compare' => $search_criteria,
            );
        }

        if (!empty($rooms)) {
            $rooms = sanitize_text_field($rooms);
            $meta_query[] = array(
                'key' => 'homey_listing_rooms',
                'value' => $rooms,
                'type' => 'CHAR',
                'compare' => $search_criteria,
            );
        }


        if (isset($_POST['area']) && !empty($_POST['area'])) {
            if (is_array($_POST['area'])) {
                $areas = $_POST['area'];

                foreach ($areas as $area):
                    $tax_query[] = array(
                        'taxonomy' => 'listing_area',
                        'field' => 'slug',
                        'terms' => homey_traverse_comma_string($area)
                    );
                endforeach;
            }
        }

        if (isset($_POST['amenity']) && !empty($_POST['amenity'])) {
            if (is_array($_POST['amenity'])) {
                $amenities = $_POST['amenity'];

                foreach ($amenities as $amenity):
                    $tax_query[] = array(
                        'taxonomy' => 'listing_amenity',
                        'field' => 'slug',
                        'terms' => $amenity
                    );
                endforeach;
            }
        }

        if (isset($_POST['facility']) && !empty($_POST['facility'])) {
            if (is_array($_POST['facility'])) {
                $facilities = $_POST['facility'];

                foreach ($facilities as $facility):
                    $tax_query[] = array(
                        'taxonomy' => 'listing_facility',
                        'field' => 'slug',
                        'terms' => $facility
                    );
                endforeach;
            }
        }
        if(!empty($room_size)) {
            $tax_query[] = array(
                'taxonomy' => 'room_type',
                'field' => 'slug',
                'terms' => homey_traverse_comma_string($room_size)
            );
        }

        if ( $sort_by == 'a_price' ) {
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = 'homey_night_price';
            $query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_price' ) {
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = 'homey_night_price';
            $query_args['order'] = 'DESC';
        } else if ( $sort_by == 'a_rating' ) {
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = 'listing_total_rating';
            $query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_rating' ) {
            $query_args['orderby'] = 'meta_value_num';
            $query_args['meta_key'] = 'listing_total_rating';
            $query_args['order'] = 'DESC';
        } else if ( $sort_by == 'featured' ) {
            $query_args['meta_key'] = 'homey_featured';
            $query_args['meta_value'] = '1';
        } else if ( $sort_by == 'a_date' ) {
            $query_args['orderby'] = 'date';
            $query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_date' ) {
            $query_args['orderby'] = 'date';
            $query_args['order'] = 'DESC';
        } else if ( $sort_by == 'featured_top' ) {
            $query_args['orderby'] = 'meta_value';
            $query_args['meta_key'] = 'homey_featured';
            $query_args['order'] = 'DESC';
        }

	$meta_listing_query[] = array(
    		'key' => 'homey_typeofpropertylisting',
    		'value' => array('public'),
    		'compare' => 'IN'
	);
	$meta_listing_query[] = array(
    		'key' => 'homey_typeofpropertylisting',
    		'compare' => 'NOT EXISTS',
	);
	$meta_listing_query['relation'] = 'OR'; 

	$meta_query[] = $meta_listing_query;

        $meta_count = count($meta_query);

        if( $meta_count > 1 ) {
            $meta_query['relation'] = 'AND';
        }
        if( $meta_count > 0 ){
            $query_args['meta_query'] = $meta_query;
        }

        $tax_count = count( $tax_query );

        if( $tax_count > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if( $tax_count > 0 ){
            $query_args['tax_query'] = $tax_query;
        }

        $query_args = new WP_Query( $query_args );

        $listings = array();

        ob_start();

        $total_listings = $query_args->found_posts;

        if($total_listings > 1) {
            $rental_text = $homey_local['rentals_label'];
        }

        while( $query_args->have_posts() ): $query_args->the_post();

            $listing_id = get_the_ID();
            $address        = get_post_meta( get_the_ID(), $homey_prefix.'listing_address', true );
            $bedrooms       = get_post_meta( get_the_ID(), $homey_prefix.'listing_bedrooms', true );
            $guests         = get_post_meta( get_the_ID(), $homey_prefix.'guests', true );
            $beds           = get_post_meta( get_the_ID(), $homey_prefix.'beds', true );
            $baths          = get_post_meta( get_the_ID(), $homey_prefix.'baths', true );
            $night_price          = get_post_meta( get_the_ID(), $homey_prefix.'night_price', true );
            $location = get_post_meta( get_the_ID(), $homey_prefix.'listing_location',true);
            $lat_long = explode(',', $location);

            $listing_price = homey_get_price_by_id($listing_id);

            $listing_type = wp_get_post_terms( get_the_ID(), 'listing_type', array("fields" => "ids") );

            if($cgl_beds != 1) {
                $bedrooms = '';
            }

            if($cgl_baths != 1) {
                $baths = '';
            }

            if($cgl_guests != 1) {
                $guests = '';
            }

            $lat = $long = '';
            if(!empty($lat_long[0])) {
                $lat = $lat_long[0];
            }

            if(!empty($lat_long[1])) {
                $long = $lat_long[1];
            }

            $listing = new stdClass();

            $listing->id = $listing_id;
            $listing->title = get_the_title();
            $listing->lat = $lat;
            $listing->long = $long;
            $listing->price = homey_formatted_price($listing_price, true, true).'<sub>'.esc_attr($price_separator).homey_get_price_label_by_id($listing_id).'</sub>';
            $listing->address = $address;
            $listing->bedrooms = $bedrooms;
            $listing->guests = $guests;
            $listing->beds = $beds;
            $listing->baths = $baths;
            
            if($cgl_types != 1) {
                $listing->listing_type = '';
            } else {
                $listing->listing_type = homey_taxonomy_simple('listing_type');
            }
            $listing->thumbnail = get_the_post_thumbnail( $listing_id, 'homey-listing-thumb',  array('class' => 'img-responsive' ) );
            $listing->url = get_permalink();

            $listing->icon = get_template_directory_uri() . '/images/custom-marker.png';

            $listing->retinaIcon = get_template_directory_uri() . '/images/custom-marker.png';

            if(!empty($listing_type)) {
                foreach( $listing_type as $term_id ) {

                    $listing->term_id = $term_id;

                    $icon_id = get_term_meta($term_id, 'homey_marker_icon', true);
                    $retinaIcon_id = get_term_meta($term_id, 'homey_marker_retina_icon', true);

                    $icon = wp_get_attachment_image_src( $icon_id, 'full' );
                    $retinaIcon = wp_get_attachment_image_src( $retinaIcon_id, 'full' );

                    if( !empty($icon['0']) ) {
                        $listing->icon = $icon['0'];
                    } 
                    if( !empty($retinaIcon['0']) ) {
                        $listing->retinaIcon = $retinaIcon['0'];
                    } 
                }
            }

            array_push($listings, $listing);

            if($layout == 'card') {
                get_template_part('template-parts/listing/listing-card');
            } else {
                get_template_part('template-parts/listing/listing-item');
            }

        endwhile;

        wp_reset_postdata();

        homey_pagination_halfmap( $query_args->max_num_pages, $paged, $range = 2 );

        $listings_html = ob_get_contents();
        ob_end_clean();

        if( count($listings) > 0 ) {
            echo json_encode( array( 'getListings' => true, 'listings' => $listings, 'total_results' => $total_listings.' '.$rental_text, 'listingHtml' => $listings_html ) );
            exit();
        } else {
            echo json_encode( array( 'getListings' => false, 'total_results' => $total_listings.' '.$rental_text ) );
            exit();
        }
        die();
}

/**
 * Submission of Contact forms
 */
/** Action for submission of contact form for hosts */
add_action( 'wp_ajax_nopriv_homey_child_contact_list_host_submission', 'homey_child_contact_list_host_submission' );  
add_action( 'wp_ajax_homey_child_contact_list_host_submission', 'homey_child_contact_list_host_submission' );  
function homey_child_contact_list_host_submission() { 
    $response = ''; $user_id = 2; //default guest user as per system.
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); 
    
    //create a lead and enter into db
    $listingId = explode('=',$form_details[4]); $postcode = explode('=',$form_details[5]);
    $pname = explode('=',$form_details[7]); $pemail = explode('=',$form_details[8]);
    $pphone = explode('=',$form_details[9]); $pmessage = explode('=',$form_details[10]);
    $contactdetails = 'Name: '.$pname[1].PHP_EOL.'Email: '.$pemail[1].PHP_EOL.
		      'Phone: '.$pphone[1].PHP_EOL.'Message: '.$pmessage[1].PHP_EOL;
    $querytitle = 'Lead: Property ID: '.$listingId[1].' PostCode: '.$postcode[1];
    $homelistingpost = get_post($listingId[1]); //var_dump($homelistingpost); 
    $listingpostcode = get_post_meta($homelistingpost->ID, 'homey_zip', true); 
    $broker_id = 0; 
    $user_args  = array( 'role__in' => ['homey_brokers'], 'orderby' => 'display_name' ); 
    $brokers = get_users($user_args); 
    foreach($brokers as $broker){  //echo $broker->ID; 
       $brokerpostcode = get_user_meta($broker->ID,'postalcode',true);
       if($listingpostcode == $brokerpostcode){ $broker_id = $broker->ID; break; }
    }
    // no brokers exist, then assign to admin
    if($broker_id == 0){ $broker_id = 0; }

    $lead = array(
	'post_author'	=> $user_id, 
	'post_title'	=> $querytitle,
        'post_type' 	=> 'homey_reservation', 
        'post_status'	=> 'publish',
        'post_parent'	=> 0,
        'post_excerpt'  => $contactdetails,	
    );
    $lead_id =  wp_insert_post($lead);
    update_post_meta($lead_id, 'reservation_listing_id', $listingId[1]);
    update_post_meta($lead_id, 'listing_owner', $homelistingpost->post_author);
    update_post_meta($lead_id, 'listing_renter', $user_id);
    update_post_meta($lead_id, 'reservation_checkin_date', date('Y-m-d'));
    update_post_meta($lead_id, 'reservation_status', 'under_review');
    update_post_meta($lead_id, 'is_hourly', 'no');
    update_post_meta($lead_id, 'slide_template', 'default');
    update_post_meta($lead_id, 'homey_brokers', $broker_id);
    update_post_meta($lead_id, 'homey_lead_type', 'soft_lead');

    $response = 'Success: Thanks For Contacting Us. Our Agent Will Get In Touch.';
    echo json_encode( array('success' => true, 'message' => $response) );
    wp_die();
}

/** Action for submission of contact form for hosts from Agent Dashboard */
add_action( 'wp_ajax_nopriv_homey_child_agent_contact_host_submission', 'homey_child_agent_contact_host_submission' );  
add_action( 'wp_ajax_homey_child_agent_contact_host_submission', 'homey_child_agent_contact_host_submission' );  
function homey_child_agent_contact_host_submission() { 
    $local = homey_get_localization(); $response = '';
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); exit;

    $security = explode('=',$form_details[1]); $nonce = $security[1];
    if ( ! wp_verify_nonce( $nonce, 'host-contact-nonce' ) ) {
       echo json_encode( array( 'success' => false, 'message' => $local['security_check_text'] ) );
       wp_die();
    }

    //get the email of the user from listing.
    $listingId = explode('=',$form_details[4]); $propertyLink = explode('=',$form_details[2]); 
    $propertyPCode = explode('=',$form_details[5]); $propertyTitle =  explode('=',$form_details[3]);
    $homelistingpost = get_post($listingId[1]); //var_dump($homelistingpost);  
    $author_id = get_post_field ('post_author', $listingId);
    $author_email = get_the_author_meta( 'user_email', $author_id ); 
    $agent_message = explode('=',$form_details[10]); $agent_name = explode('=',$form_details[7]);
    $agent_email = explode('=',$form_details[8]); $agent_ph = explode('=',$form_details[9]); 
    $rservation_id = $_POST['reservationid'];

    // send a message to broker about creation of the user
    $message = esc_html__('Availability Check On Property:', 'homey-child' ) . "<br/><br/>"; 
    $message .= esc_html__('Please Let Us Know If the Property Is Available From The Following Dates.', 'homey-child' ) . "<br/><br/>"; 
    //$message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), $propertyLink[1]) . "<br/><br/>";    
    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), $propertyLink[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Message From Agent: %s', 'homey-child'), $agent_message[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name[1]) . "<br/><br/>";    
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email[1]) . "<br/><br/>";
    $message .= sprintf(esc_html__('Agent Phone: %s', 'homey-child'), $agent_ph[1]) . "<br/><br/>";
    $message .= esc_html__('Please Let Us Know By Replying To The Email.', 'homey-child') . "<br/><br/>";
    $title = esc_html__('Availability Check On Property. Property Id: '.$listingId[1], 'homey-child') . "\n";
    $result = homey_send_emails($author_email, wp_specialchars_decode( $title ), $message);

    //after an email is sent to the client, status would be modified to be in progress.
    //also the selected properties will be assigned.
    update_post_meta($rservation_id, 'reservation_status', 'in_progress'); 
    $selected_props = get_post_meta($rservation_id, 'reservation_selections', true); 
    if(empty($selected_props)){
      update_post_meta($rservation_id, 'reservation_selections', $listingId[1]); 
    }else{
      $addlistings = $selected_props.','.$listingId[1];
      update_post_meta($rservation_id, 'reservation_selections', $addlistings);
    }

    //update comments
    $agent = get_user_by( 'email', $agent_email[1]);
    $args = array(
	'comment_agent' => 'form:availablity_check',
	'comment_approved' => 1, 'comment_author' => $agent_name[1],
	'comment_author_email' => $agent_email[1], 'comment_author_IP' => '',
	'comment_author_url' => '', 'comment_content' => $message, 
	'comment_date' => date('Y-m-d H:i:s'),
	'comment_date_gmt' => date('Y-m-d H:i:s'),
	'comment_karma' => 0, 'comment_parent' => 0, 
	'comment_post_ID' => $listingId[1], 
	'comment_type' => 'availablity_check',
	'user_id' => $agent->ID,
	'comment_meta' => array(
	   '_wxr_import_user' => $agent->ID,
	   'reservation_id' => $rservation_id,
	),
    ); 
    $commentid = wp_insert_comment($args);

    //return the result
    if($result){ 
      $response = 'Email Sent To Host.';
      echo json_encode( array('success' => true, 'msg' => $response) ); 
    } else{ 
      $response = 'Email To Host Could Not Be Sent.';
      echo json_encode( array('success' => false, 'msg' => $response) ); 
    }
    wp_die();
}

/**
 * Lead Submission From Frontend
 */
/** Action for submission of enquire now form for unregistered user */
add_action( 'wp_ajax_nopriv_homey_child_add_reservation', 'homey_child_add_reservation' );  
add_action( 'wp_ajax_homey_child_add_reservation', 'homey_child_add_reservation' ); 
function homey_child_add_reservation() {
	global $current_user; $pauthor_id = 1; //default authors to admin
        $current_user = wp_get_current_user();
        $userID       = $current_user->ID;
        $local = homey_get_localization();
        $allowded_html = array();
        $reservation_meta = array(); 

	// -------------------------
	// sanitize all variables 
	// -------------------------
	$username = $_POST['name'];  
	if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){ $useremail = $_POST['email']; 
	}else{ 
            echo json_encode( array( 'success' => false, 'message' => esc_html__('A Valid Email Is Required','homey-child') ) );
            wp_die();
	}
	if($_POST['phone'] != ''){
	if( preg_match('/^[0-9]{10}+$/', $_POST['phone']) ) { 
	    $userphone = $_POST['phone'];
	}else{
            echo json_encode( array( 'success' => false, 'message' => esc_html__('Phone Number Would Need To Be Of 10 digits','homey-child') ) );
            wp_die();
	}}
        if($_POST['bedrooms'] != ''){ 
	if( preg_match('/^[1-9]{1}+$/', $_POST['bedrooms']) ) { 
	  $bedrooms = $_POST['bedrooms']; 
	}else{ 
            echo json_encode( array( 'success' => false, 'message' => esc_html__('Bedrooms Range Between 1 to 9','homey-child') ) );
            wp_die();
	}}
        if($_POST['bathrooms'] != ''){ 
	if( preg_match('/^[1-9]{1}+$/', $_POST['bathrooms']) ) { 
	  $bathrooms = $_POST['bathrooms']; 
	}else{
            echo json_encode( array( 'success' => false, 'message' => esc_html__('Bathrooms Range Between 1 to 9','homey-child') ) );
            wp_die();
	}}

        $listing_id = intval($_POST['listing_id']); 
	$postcode = get_post_meta($listing_id, 'homey_zip', true); 
        $listing_owner_id  =  get_post_field( 'post_author', $listing_id );
        $check_in_date     =  wp_kses ( $_POST['check_in_date'], $allowded_html );
        $check_out_date    =  wp_kses ( $_POST['check_out_date'], $allowded_html );
        $extra_options    =  $_POST['extra_options'];
        $guest_message = stripslashes ( $_POST['guest_message'] );
        $guests   =  intval($_POST['guests']);
        //$title = $local['reservation_text']; 
	$title = 'Lead: Property ID: '.$listing_id.' PostCode: '.$postcode;
        $owner = homey_usermeta($listing_owner_id);
        $owner_email = $owner['email'];

	// ---------------------
	// check for errors 
	// ---------------------
        //if ( !is_user_logged_in() || $userID === 0 ) {   
        //    echo json_encode( array( 'success' => false, 'message' => $local['login_for_reservation'] ) );
        //    wp_die();
        //}

        /*if(!homey_is_renter()) {
        //    echo json_encode( array( 'success' => false, 'message' => $local['host_user_cannot_book'] ) );
        //    wp_die();
        }*/

        if($userID == $listing_owner_id) {
            echo json_encode( array( 'success' => false, 'message' => $local['own_listing_error'] ) );
            wp_die();
        }

        //check security
        $nonce = $_REQUEST['security'];
        if ( ! wp_verify_nonce( $nonce, 'reservation-security-nonce' ) ) {
             echo json_encode( array( 'success' => false, 'message' => $local['security_check_text'] ) );
             wp_die();
        }

	//assign a broker based on postcode
    	$broker_id = 0; $user_args  = array('role__in' => ['homey_brokers'], 'orderby' => 'display_name'); 
    	$brokers = get_users($user_args); 
    	foreach($brokers as $broker){  //echo $broker->ID; 
       	   $brokerpostcode = get_user_meta($broker->ID,'postalcode',true);
       	   if($postcode == $brokerpostcode){ $broker_id = $broker->ID; break; }
        }
    	// no brokers exist, then assign to admin
    	if($broker_id == 0){ $broker_id = 0; }

	//assign an agent based on postcode
    	$agent_id = 0; $user_args  = array('role__in' => ['homey_agents'], 'orderby' => 'display_name'); 
    	$agents = get_users($user_args); 
    	foreach($agents as $agent){  //echo $broker->ID; 
       	   $agentpostcode = get_user_meta($agent->ID,'postalcode',true);
       	   if($postcode == $agentpostcode){ $agent_id = $agent->ID; break; }
        }
    	// no agents exist, then assign to none
    	if($agent_id == 0){ $agent_id = 0; }

    	$contactdetails = 'Name: '.$username.PHP_EOL.'Email: '.$useremail.PHP_EOL.
		          'Phone: '.$userphone.PHP_EOL.'Message: '.$guest_message.PHP_EOL;

        $check_availability = check_booking_availability($check_in_date, $check_out_date, $listing_id, $guests);
        $is_available = $check_availability['success'];
        $check_message = $check_availability['message'];

        if($is_available) {
            $prices_array = homey_get_prices($check_in_date, $check_out_date, $listing_id, $guests, $extra_options);
            $reservation_meta['no_of_days'] = $prices_array['days_count'];
            //$reservation_meta['additional_guests'] = $prices_array['additional_guests'];
            //$upfront_payment = $prices_array['upfront_payment'];
            //$balance = $prices_array['balance'];
            //$total_price = $prices_array['total_price'];
            //$price_per_night = $prices_array['price_per_night'];
            //$nights_total_price = $prices_array['nights_total_price'];
            //$cleaning_fee = $prices_array['cleaning_fee'];
            //$city_fee = $prices_array['city_fee'];
            //$services_fee = $prices_array['services_fee'];
            //$days_count = $prices_array['days_count'];
            //$period_days = $prices_array['period_days'];
            //$taxes = $prices_array['taxes'];
            //$taxes_percent = $prices_array['taxes_percent'];
            //$security_deposit = $prices_array['security_deposit'];
            //$additional_guests = $prices_array['additional_guests'];
            //$additional_guests_price = $prices_array['additional_guests_price'];
            //$additional_guests_total_price = $prices_array['additional_guests_total_price'];
            //$booking_has_weekend = $prices_array['booking_has_weekend'];
            //$booking_has_custom_pricing = $prices_array['booking_has_custom_pricing'];

            $reservation_meta['check_in_date'] = $check_in_date;
            $reservation_meta['check_out_date'] = $check_out_date;
            $reservation_meta['guests'] = $guests;
            $reservation_meta['listing_id'] = $listing_id;
            //$reservation_meta['upfront'] = $upfront_payment;
            //$reservation_meta['balance'] = $balance;
            //$reservation_meta['total'] = $total_price;
            //$reservation_meta['price_per_night'] = $price_per_night;
            //$reservation_meta['nights_total_price'] = $nights_total_price;
            //$reservation_meta['cleaning_fee'] = $cleaning_fee;
            //$reservation_meta['city_fee'] = $city_fee;
            //$reservation_meta['services_fee'] = $services_fee;
            //$reservation_meta['period_days'] = $period_days;
            //$reservation_meta['taxes'] = $taxes;
            //$reservation_meta['taxes_percent'] = $taxes_percent;
            //$reservation_meta['security_deposit'] = $security_deposit;
            //$reservation_meta['additional_guests_price'] = $additional_guests_price;
            //$reservation_meta['additional_guests_total_price'] = $additional_guests_total_price;
            //$reservation_meta['booking_has_weekend'] = $booking_has_weekend;
            //$reservation_meta['booking_has_custom_pricing'] = $booking_has_custom_pricing;
            if($_POST['bedrooms'] != ''){ $reservation_meta['no_of_bedrooms'] = $bedrooms; }
            if($_POST['bathrooms'] != ''){ $reservation_meta['no_of_bathrooms'] = $bathrooms; }	
            $reservation_meta['location'] = $postcode;	

            $reservation = array(
                'post_title'    => $title,
                'post_status'   => 'publish', 
                'post_type'     => 'homey_reservation' ,
                'post_author'   => $pauthor_id,
	        'post_excerpt'  => $contactdetails,
            );
            $reservation_id =  wp_insert_post($reservation );  
            
            /*$reservation_update = array(
                'ID'         => $reservation_id,
                'post_title' => $title.' '.$reservation_id
            );
            wp_update_post( $reservation_update );*/

            update_post_meta($reservation_id, 'reservation_listing_id', $listing_id);
            update_post_meta($reservation_id, 'listing_owner', $listing_owner_id);
            update_post_meta($reservation_id, 'reservation_checkin_date', $check_in_date);
            update_post_meta($reservation_id, 'reservation_checkout_date', $check_out_date);
            update_post_meta($reservation_id, 'reservation_guests', $guests);
            update_post_meta($reservation_id, 'reservation_meta', $reservation_meta);
            update_post_meta($reservation_id, 'reservation_status', 'under_review');
            update_post_meta($reservation_id, 'is_hourly', 'no');
    	    update_post_meta($reservation_id, 'slide_template', 'default');
    	    update_post_meta($reservation_id, 'homey_brokers', $broker_id);
    	    update_post_meta($reservation_id, 'homey_agents', $agent_id);
	
	    //client lead and soft lead
	    if($userID == 0){
              update_post_meta($reservation_id, 'listing_renter', $userID);
    	      update_post_meta($reservation_id, 'homey_lead_type', 'soft_lead');
	    }else{
              update_post_meta($reservation_id, 'listing_renter', $userID);
    	      update_post_meta($reservation_id, 'homey_lead_type', 'client_lead');
	    }

            //update_post_meta($reservation_id, 'extra_options', $extra_options);
            //update_post_meta($reservation_id, 'reservation_upfront', $upfront_payment);
            //update_post_meta($reservation_id, 'reservation_balance', $balance);
            //update_post_meta($reservation_id, 'reservation_total', $total_price);

            $pending_dates_array = homey_get_booking_pending_days($listing_id);      
            update_post_meta($listing_id, 'reservation_pending_dates', $pending_dates_array); 
          
            //$message_link = homey_thread_link_after_reservation($reservation_id);
            //$email_args = array(
            //    'reservation_detail_url' => reservation_detail_link($reservation_id),
            //    'guest_message' => $guest_message,
            //    'message_link' => $message_link
            //);
            //homey_email_composer( $owner_email, 'new_reservation', $email_args ); 

	    // send a message to broker about creation of the user
	    $message = esc_html__('Lead Request Placed On Property:', 'homey-child' ) . "<br/><br/>"; 
	    $message .= esc_html__('Lead Details In Notification.', 'homey-child' ) . "<br/>"; 
    	    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($listing_id)) . "<br/>";    
    	    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($listing_id)) . "<br/>";
    	    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $postcode) . "<br/>";
    	    $message .= sprintf(esc_html__('Customer Message: %s', 'homey-child'), $guest_message) . "<br/>";
    	    $message .= sprintf(esc_html__('Checkin Date: %s', 'homey-child'), $check_in_date) . "<br/>";    
    	    $message .= sprintf(esc_html__('Checkout Date: %s', 'homey-child'), $check_out_date) . "<br/>";
    	    $message .= sprintf(esc_html__('Guests: %s', 'homey-child'), $guests) . "<br/>";
    	    $message .= esc_html__('Notification Sent To Respective Agent, Will Be In Touch Soon.', 'homey-child') . "<br/><br/>";
    	    $title = esc_html__('New Lead Request Placed On Property. Property Id: '.$listing_id, 'homey-child') . "\n";

	    //users to send emails
	    $broker = get_userdata($broker_id); $agent = get_userdata($agent_id);
	    $res1 = homey_send_emails($useremail, wp_specialchars_decode( $title ), $message);
	    $res2 = homey_send_emails($broker->user_email, wp_specialchars_decode( $title ), $message);
	    $res3 = homey_send_emails($agent->user_email, wp_specialchars_decode( $title ), $message);

            echo json_encode( array( 'success' => true, 'message' => $local['request_sent'] ) );
            //do_action('homey_create_messages_thread', $guest_message, $reservation_id);
            wp_die();

         } else { // end $check_availability
            echo json_encode( array( 'success' => false, 'message' => $check_message ) );
            wp_die();
         }

} 


/** Action for submission of add new agent from brokers */
add_action( 'wp_ajax_nopriv_homey_child_add_new_agent_submission', 'homey_child_add_new_agent_submission' );  
add_action( 'wp_ajax_homey_child_add_new_agent_submission', 'homey_child_add_new_agent_submission' );  
function homey_child_add_new_agent_submission() { 
    $response = ''; 
    $form_data = urldecode($_POST['form_data']); $form_details = explode('&', $form_data); 
    //var_dump($form_details); exit;

    //generate the variables
    $broker_email = explode('=',$form_details[0]);
    $username = explode('=',$form_details[2]); $agent_email = explode('=',$form_details[3]);
    $password = wp_generate_password(12, true ); 
    $firstname = explode('=',$form_details[4]); $lastname = explode('=',$form_details[5]);
    $agent_address = explode('=',$form_details[6]); $agent_city = explode('=',$form_details[7]);
    $agent_state = explode('=',$form_details[8]); $agent_pcode = explode('=',$form_details[9]);

    // check if user exists in the database
    if( null == username_exists( $agent_email[1] )){ 
      //create user with all the details
      $userdata = array(
	'user_pass'	=> $password, 
	'user_login'	=> $username[1],
        'user_nicename' => $firstname[1].' '.$lastname[1], 
        'user_email'	=> $agent_email[1],
        'display_name'	=> $firstname[1].' '.$lastname[1],
        'nickname'  	=> $username[1],
	'first_name' 	=> $firstname[1], 
	'last_name' 	=> $lastname[1], 
	'user_registered' => date('Y-m-d H:i:s'), 
	'show_admin_bar_front' => false, 
	'role' => 'homey_agents',
      );
      $user_id = wp_insert_user( $userdata );

      //update user meta with the address details.
      update_user_meta( $user_id, 'address', $agent_address[1] ); 
      update_user_meta( $user_id, 'city', $agent_city[1] ); 
      update_user_meta( $user_id, 'postalcode', $agent_pcode[1] ); 
      update_user_meta( $user_id, 'state', $agent_state[1] );

      //send email to admin and user after creation of the user
      homey_wp_new_user_notification($user_id, $password);

      // send a message to broker about creation of the user
      $message = esc_html__('A new agent has been set up:', 'homey-child' ) . "\r\n\r\n";
      $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username[1]) . "\r\n\r\n";
      $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
      $message .= esc_html__('Agent Will Now Be Able To Login Using The Password Received.', 'homey-child') . "\r\n\r\n";
      $title = esc_html__('Agent Setup Message.', 'homey-child') . "\n";
      homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

      $response = 'Agent Has Been Created. Email Sent Successfully!';
      echo json_encode( array('success' => true, 'message' => $response) );
      wp_die();

    }else{
      $response = 'Error: Username Already Exists In Database.';
      echo json_encode( array('success' => false, 'message' => $response) );
      wp_die();
    }
}

/**
 * Loading More Listings for Suggested / Available Houses.
 */
/** Action for loading more listings in suggested houses */
add_action( 'wp_ajax_nopriv_homey_child_loadmore_listings', 'homey_child_loadmore_listings' );
add_action( 'wp_ajax_homey_child_loadmore_listings', 'homey_child_loadmore_listings' );
function homey_child_loadmore_listings() {
        global $post, $homey_prefix, $homey_local;
        $homey_prefix = 'homey_';
        $homey_local = homey_get_localization();

        $fake_loop_offset = 0; 
        $tax_query = array();

        $searchText = sanitize_text_field($_POST['searchterm']);
	$searchType = $_POST['stype'];
	$postcodeToLook = sanitize_text_field($_POST['postcode']);
        $type = sanitize_text_field($_POST['type']);
        $roomtype = sanitize_text_field($_POST['roomtype']);
        $country = sanitize_text_field($_POST['country']);
        $state = sanitize_text_field($_POST['state']);
        $city = sanitize_text_field($_POST['city']);
        $area = sanitize_text_field($_POST['area']);
        $nbeds = sanitize_text_field($_POST['beds']);
        $nbaths = sanitize_text_field($_POST['baths']);

        $listing_style = sanitize_text_field($_POST['style']);
        $listing_type = homey_traverse_comma_string($type);
        $listing_roomtype = homey_traverse_comma_string($roomtype);
        $listing_country = homey_traverse_comma_string($country);
        $listing_state = homey_traverse_comma_string($state);
        $listing_city = homey_traverse_comma_string($city);
        $listing_area = homey_traverse_comma_string($area);
        $featured = sanitize_text_field($_POST['featured']);
        $posts_limit = sanitize_text_field($_POST['limit']);
        $sort_by = sanitize_text_field($_POST['sort_by']);
        $offset = sanitize_text_field($_POST['offset']);
        $paged = sanitize_text_field($_POST['paged']);
        $author = sanitize_text_field($_POST['author']);
        $authorid = sanitize_text_field($_POST['authorid']);

        $wp_query_args = array(
            'ignore_sticky_posts' => 1
        );

	if(!empty($searchText)){ 
	   $keywords = '';
	   if(strstr($searchText,',')){
	    $keyword_array = explode(',',$searchText);
	    foreach($keyword_array as $keyword){ $keywords .= trim($keyword).' '; }
	   } else{ $keywords .= $searchText; }
	   $wp_query_args['s'] = $keywords;
	}

        if (!empty($listing_type)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_type',
                'field' => 'slug',
                'terms' => $listing_type
            );
        }

        if (!empty($listing_roomtype)) {
            $tax_query[] = array(
                'taxonomy' => 'room_type',
                'field' => 'slug',
                'terms' => $listing_roomtype
            );
        }

        if (!empty($listing_country)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_country',
                'field' => 'slug',
                'terms' => $listing_country
            );
        }
        if (!empty($listing_state)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_state',
                'field' => 'slug',
                'terms' => $listing_state
            );
        }
        if (!empty($listing_city)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_city',
                'field' => 'slug',
                'terms' => $listing_city
            );
        }
        if (!empty($listing_area)) {
            $tax_query[] = array(
                'taxonomy' => 'listing_area',
                'field' => 'slug',
                'terms' => $listing_area
            );
        }

        if($author == 'yes') {
            $wp_query_args['author'] = $authorid;
        }

        if ( $sort_by == 'a_price' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'homey_night_price';
            $wp_query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_price' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'homey_night_price';
            $wp_query_args['order'] = 'DESC';
        } else if ( $sort_by == 'a_rating' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'listing_total_rating';
            $wp_query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_rating' ) {
            $wp_query_args['orderby'] = 'meta_value_num';
            $wp_query_args['meta_key'] = 'listing_total_rating';
            $wp_query_args['order'] = 'DESC';
        } else if ( $sort_by == 'featured' ) {
            $wp_query_args['meta_key'] = 'homey_featured';
            $wp_query_args['meta_value'] = '1';
        } else if ( $sort_by == 'a_date' ) {
            $wp_query_args['orderby'] = 'date';
            $wp_query_args['order'] = 'ASC';
        } else if ( $sort_by == 'd_date' ) {
            $wp_query_args['orderby'] = 'date';
            $wp_query_args['order'] = 'DESC';
        } else if ( $sort_by == 'featured_top' ) {
            $wp_query_args['orderby'] = 'meta_value';
            $wp_query_args['meta_key'] = 'homey_featured';
            $wp_query_args['order'] = 'DESC';
        }

        if (!empty($featured)) {
            if( $featured == "yes" ) {
                $wp_query_args['meta_key'] = 'homey_featured';
                $wp_query_args['meta_value'] = '1';
            } else {
                $wp_query_args['meta_key'] = 'homey_featured';
                $wp_query_args['meta_value'] = '0';
            }
        }

	if(!empty($postcodeToLook)){ 
	  $wp_query_args['meta_key'] = 'homey_zip';
          $wp_query_args['meta_value'] = $postcodeToLook;
	}

	if(!empty($nbeds)){ 
	  $wp_query_args['meta_key'] = 'homey_beds';
          $wp_query_args['meta_value'] = $nbeds;
	  $wp_query_args['compare'] = '>=';
	}

	if(!empty($nbaths)){ 
	  $wp_query_args['meta_key'] = 'homey_zip';
          $wp_query_args['meta_value'] = $nbaths;
	  $wp_query_args['compare'] = '>=';
	}

        $tax_count = count( $tax_query );
        if( $tax_count > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        if( $tax_count > 0 ){
            $wp_query_args['tax_query'] = $tax_query;
        }

        $wp_query_args['post_status'] = 'publish';

        if (empty($posts_limit)) {
            $posts_limit = get_option('posts_per_page');
        }
        $wp_query_args['posts_per_page'] = $posts_limit;

        if (!empty($paged)) {
            $wp_query_args['paged'] = $paged;
        } else {
            $wp_query_args['paged'] = 1;
        }

        if (!empty($offset) and $paged > 1) {
            $wp_query_args['offset'] = $offset + ( ($paged - 1) * $posts_limit) ;
        } else {
            $wp_query_args['offset'] = $offset ;
        }

        $fake_loop_offset = $offset;
        $wp_query_args['post_type'] = 'listing'; 
	//var_dump($wp_query_args); exit;
        $the_query = new WP_Query($wp_query_args);

	$html = '';
        if ($the_query->have_posts()) :
	$html .= '<div class="table-block dashboard-listing-table dashboard-table">';
        $html .= '<table class="table table-hover">';
	if($searchType == 'suggested-houses'){ 
	  $html .= '<tbody id="suggested_houses_listings_ajax">';
	} else { $html .= '<tbody id="available_houses_listings_ajax">'; }	
            while ($the_query->have_posts()) : $the_query->the_post();
		$address = get_post_meta( get_the_ID(), 'homey_listing_address', true );
                //if($listing_style == 'card') {
                //    $html .= get_template_part('template-parts/listing/listing-card');
                //} else {
                //    $html .= get_template_part('template-parts/listing/listing-item');
                //}
		$html .= '<tr id="tr-id-'.$post->ID.'">';
		$html .= '<td data-label="'.esc_html__('thumbnail').'" width="11%">';
        	$html .= '<a href="'.get_the_permalink().'">';
		if( has_post_thumbnail( $post->ID ) ) {
            	  $html .= get_the_post_thumbnail( $post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
        	}else{ 
		  $html .= homey_image_placeholder( 'homey-listing-thumb' ); 
		} 
        	$html .= '</a></td>';
		$html .= '<td data-label="'.esc_html__('title-address').'">';
		$html .= '<div style="float:left;width:26%">';
		$html .= '<a href="'.get_the_permalink().'"><strong>'.get_the_title().'</strong></a>';
       		if(!empty($address)) { 
		   $html .= '<address>'.esc_attr($address).'</address>';
        	} 
		$html .= '</div>';
		$html .= '<div style="float:left;width:10%">';
		$html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
		$html .= '<button type="button" data-toggle="modal" data-target="#modal-contact-host" class="btn btn-full-width openMessageModal" style="font-size:10px;border: 1px solid #d0c8c8;">'.esc_html__('Check Availability').'</button>';
		$html .= '<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i>'.esc_html__('Message Host', 'homey-child').'</div></div></td>';
		$html .= '</tr>';
            endwhile;
	    $html .= '</tbody></table></div>';
            wp_reset_postdata();
	    echo $html; 
        else:
            echo '<p> No Properties Obtained In Search </p>';
        endif;
        wp_die();
}


/**
 * Agent Selection For Brokers
 */
/** Action for submission of agent selection from brokers for leads */
/*add_action( 'wp_ajax_nopriv_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
add_action( 'wp_ajax_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
function homey_child_agent_allocation() { 
    $response = ''; 
    $data = $_POST['data']; $details = explode(',', $data); 
    //var_dump($details); exit; 

    $listingpost = get_post($details[2]); //var_dump($listingpost); 
    // update post meta
    update_post_meta($listingpost->ID, 'homey_agents', $details[0]); 

    //get userdata from userid
    // agent will receive an email.
    $agent_info = get_userdata($details[0]); 
    $useremail = $agent_info->user_email; 
    $userdisplayname = $agent_info->display_name; //var_dump($userdisplayname);
    $username = $agent_info->user_login;
    $message = esc_html__('A new lead has been added to profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= esc_html__('Please login to the dashboard with username and password to view the lead.', 'homey-child') . "\r\n\r\n";
    $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $title = esc_html__('Notification For A Lead Assignment.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message);

    // broker will receive an email.
    $broker_info = get_userdata($details[1]);
    $brokeremail = $broker_info->user_email; //var_dump($brokeremail);
    $message = esc_html__('A new lead has been assigned to agent\'s profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $userdisplayname) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $message .= esc_html__('Agent has been notified of the same.', 'homey-child') . "\r\n\r\n";
    $title = esc_html__('Notification Of A Lead Assignment To An Agent.', 'homey-child') . "\n";
    homey_send_emails($brokeremail, $title, $message);

    $response = $userdisplayname;
    echo json_encode( array('success' => true, 'message' => $response) );
    wp_die();
}*/

/** Action for submission of agent selection from brokers for properties */
add_action( 'wp_ajax_nopriv_homey_child_property_agent_allocation', 'homey_child_property_agent_allocation' );  
add_action( 'wp_ajax_homey_child_property_agent_allocation', 'homey_child_property_agent_allocation' );  
function homey_child_property_agent_allocation() { 
    $response = ''; 
    $data = $_POST['data']; $details = explode(',', $data); 
    //var_dump($details); exit; 

    $listingpost = get_post($details[2]); //var_dump($listingpost); 
    // update post meta
    update_post_meta($listingpost->ID, 'homey_assignedagent', $details[0]); 

    //get userdata from userid
    // agent will receive an email.
    $agent_info = get_userdata($details[0]); 
    $useremail = $agent_info->user_email; 
    $userdisplayname = $agent_info->display_name; //var_dump($userdisplayname);
    $username = $agent_info->user_login;
    $message = esc_html__('A new property has been added to profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= esc_html__('Please login to the dashboard with username and password to view the lead.', 'homey-child') . "\r\n\r\n";
    $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $title = esc_html__('Notification For A Property Assignment.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message);

    // broker will receive an email.
    $broker_info = get_userdata($details[1]);
    $brokeremail = $broker_info->user_email; //var_dump($brokeremail);
    $message = esc_html__('A new property has been assigned to agent\'s profile.', 'homey-child' ) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $userdisplayname) . "\r\n\r\n";
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    $message .= esc_html__('Agent has been notified of the same.', 'homey-child') . "\r\n\r\n";
    $title = esc_html__('Notification Of A Property Assignment To An Agent.', 'homey-child') . "\n";
    homey_send_emails($brokeremail, $title, $message);

    $response = $userdisplayname;
    echo json_encode( array('success' => true, 'message' => $response) );
    wp_die();
}

/**
 * Misc Actions for Dashboards
 */
/** Action for display of a modal for contacting host */
add_action( 'wp_ajax_nopriv_homey_child_display_host_contact_modal', 'homey_child_display_host_contact_modal' );  
add_action( 'wp_ajax_homey_child_display_host_contact_modal', 'homey_child_display_host_contact_modal' );  
function homey_child_display_host_contact_modal() { 
    global $post, $homey_prefix, $homey_local;
    $homey_prefix = 'homey_'; $response = ''; 
    $homey_local = homey_get_localization();

    $post = get_post($_POST['listingid']); //var_dump($post); 
    get_template_part('single-listing/contact-host'); 
    $response .= ob_get_contents();
    ob_end_clean();
    wp_reset_postdata(); 

    if($response != ''){ echo $response;
    }else{ echo '<p>No Modal - Could Not Create Modal</p>'; }
    wp_die();
}


/** Action for change reservation status from agent dashboard */
add_action( 'wp_ajax_nopriv_homey_child_change_reservation_status', 'homey_child_change_reservation_status' );  
add_action( 'wp_ajax_homey_child_change_reservation_status', 'homey_child_change_reservation_status' );  
function homey_child_change_reservation_status() { 
    $status_val = $_POST['statusval']; $reservation_Id = $_POST['reservationid'];

    //after an email is sent to the client, status would be modified to be in progress.
    update_post_meta($reservation_Id, 'reservation_status', $status_val);

    // send a message to broker about creation of the user
    //$message = esc_html__('Availability Check On Property:', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= esc_html__('Please Let Us Know If the Property Is Available From The Following Dates.', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Message From Agent: %s', 'homey-child'), $agent_message[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Phone: %s', 'homey-child'), $agent_ph[1]) . "\r\n\r\n";
    //$message .= esc_html__('Please Let Us Know By Replying To The Email.', 'homey-child') . "\r\n\r\n";
    //$title = esc_html__('Availability Check On Property. Property Id: '.$listingId, 'homey-child') . "\n";
    //$result = homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);
    //if($result){ 
    //  $response = 'Email Sent To Host.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //} else{ 
    //  $response = 'Email To Host Could Not Be Sent.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //}

    $response = 'Status Changed For The Lead!!!';
    echo json_encode( array('success' => true, 'message' => $response) ); 
    wp_die();
}

/** Action for change selected houses in agent and broker dashboard */
add_action( 'wp_ajax_nopriv_homey_child_fetch_selected_houses', 'homey_child_fetch_selected_houses' );  
add_action( 'wp_ajax_homey_child_fetch_selected_houses', 'homey_child_fetch_selected_houses' );  
function homey_child_fetch_selected_houses() { 
    global $post; $html='';
    $reservation_Id = $_POST['reservationid'];
    $selected_props = get_post_meta($reservation_Id, 'reservation_selections', true);
    //----------------------------------------------
    // generate query for selected houses listings
    //----------------------------------------------
    $no_of_listing   =  '9';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $selectedPropsArray = explode(',',$selected_props);
    $selected_qargs = array(
       'post_type'        =>  'listing',
       //'author'           =>  $userID,
       'paged'             => $paged,
       'posts_per_page'    => $no_of_listing,
       'post_status'      =>  'publish', 
       'post__in' => $selectedPropsArray
    );
    $selected_qargs = homey_listing_sort ($selected_qargs);
    $selected_props_qry = new WP_Query($selected_qargs);
    $meta_query = []; 

    if(isset($selected_props_qry)){ 
      if($selected_props_qry->have_posts()): 
	$html .= '<div class="table-block dashboard-listing-table dashboard-table">';
        $html .= '<table class="table table-hover"><tbody id="selected_houses_listings">';
	while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	  $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	  $html .= '<tr><td data-label="'.esc_html__('thumbnail').'">';
          $html .= '<a href="'.get_the_permalink($post->ID).'">';
          if( has_post_thumbnail( $post->ID ) ) {
            $html .= get_the_post_thumbnail($post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
          }else{ $html .= homey_image_placeholder( 'homey-listing-thumb' ); } 
          $html .= '</a></td>';
          $html .= '<td data-label="'.esc_html__('title-address').'">';
	  $html .= '<div style="float:left;width:100%">';
          $html .= '<a href="'.get_the_permalink($post->ID).'"><strong>'.get_the_title($post->ID).'</strong></a>';
          if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
	  $html .= '</div>';
          //$html .= '<div style="float:left;width:100%">';
          //$html .= '<button type="button" class="btn btn-full-width accepted" 
	  //		  style="font-size:10px;border:1px solid #d0c8c8;background:#85c341;width:16%;float:left;line-height:25px;margin-right:3px;">';
	  //$html .= esc_html__('Accepted').'</button>';
	  //$html .= '<button type="button" class="btn btn-full-width denied" 
	  //		  style="font-size:10px;border:1px solid #d0c8c8;background:#f15e75;width:16%;float:left;line-height:25px;">';
	  //$html .= esc_html__('Denied').'</button>';
	  //$html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
	  //$html .= '</div>';
	  $html .= '<div class="text-center text-small" style="font-size:10px;float:left;"><i class="fa fa-info-circle"></i>';
	  $html .= esc_html__(' Confirm/Deny Will Be Mentioned By Host ', 'homey-child').'</div></td></tr>';
	endwhile;
	$html .= '</tbody></table>';
	$html .= '</div>';
	$html .= homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); 
     else:
        $html .= '<div class="">'; 
        $html .= esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); 
	$html .= '</div>';      
     endif; 
   } 
   wp_reset_postdata();
   echo $html;
   wp_die();
}

/** Action for change accepted and declined response from client */
add_action( 'wp_ajax_nopriv_homey_child_reserv_modification_via_client_response', 'homey_child_reserv_modification_via_client_response' );  
add_action( 'wp_ajax_homey_child_reserv_modification_via_client_response', 'homey_child_reserv_modification_via_client_response' );  
function homey_child_reserv_modification_via_client_response() { 
    global $post; $html='';
    $reservation_Id = $_POST['reservationid'];
    $listing_Id = $_POST['listingid'];

    // accepted response from client
    if($_POST['type'] == 'accepted'){
	//update the postmeta values for the reservation
    	$approved_props = get_post_meta($reservation_Id, 'approved_reservations', true); 
    	if(empty($approved_props)){
      	   update_post_meta($reservation_Id, 'approved_reservations', $listing_Id);
    	}else{
      	   $addlistings = $approved_props.','.$listing_Id;
           update_post_meta($reservation_Id, 'approved_reservations', $addlistings);
        }
	
	//create the html to replace the listings
    	$selected_props = get_post_meta($reservation_Id, 'approved_reservations', true);
    	//----------------------------------------------
    	// generate query for selected houses listings
    	//----------------------------------------------
    	$no_of_listing   =  '9';
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$selectedPropsArray = explode(',',$selected_props);
    	$selected_qargs = array(
       	   'post_type'        =>  'listing',
       	   //'author'           =>  $userID,
       	   'paged'             => $paged,
       	   'posts_per_page'    => $no_of_listing,
       	   'post_status'      =>  'publish', 
       	   'post__in' => $selectedPropsArray
    	);
    	$selected_qargs = homey_listing_sort ($selected_qargs);
    	$selected_props_qry = new WP_Query($selected_qargs);
    	$meta_query = []; 

    	if(isset($selected_props_qry)){ 
      	if($selected_props_qry->have_posts()): 
	   $html .= '<div class="table-block dashboard-listing-table dashboard-table">';
           $html .= '<table class="table table-hover"><tbody id="approval_houses_listings">';
	   while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	      $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	      $html .= '<tr><td data-label="'.esc_html__('thumbnail').'">';
              $html .= '<a href="'.get_the_permalink($post->ID).'">';
              if( has_post_thumbnail( $post->ID ) ) {
                 $html .= get_the_post_thumbnail($post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
              }else{ $html .= homey_image_placeholder( 'homey-listing-thumb' ); } 
              $html .= '</a></td>';
              $html .= '<td data-label="'.esc_html__('title-address').'">';
	      $html .= '<div style="float:left;width:75%">';
              $html .= '<a href="'.get_the_permalink($post->ID).'"><strong>'.get_the_title($post->ID).'</strong></a>';
              if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
	      $html .= '</div>';
	      $html .= '<div style="float:left;width:25%">';
	      $html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
	      //$html .= '<button type="button" class="btn btn-full-width" style="font-size:10px;border: 1px solid #d0c8c8;">';
	      //$html .= esc_html__('Notify Customer').'</button>';
	      //$html .= '<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i>';
	      //$html .= esc_html__('Send To Customer', 'homey-child').'</div></div>';
	      $html .= '<button type="button" name="agent-broker-confirmation" class="btn btn-full-width" 
						style="font-size:9px;border:1px solid #d0c8c8;background-color:#dfdcdc;">';
	      $html .= esc_html__('Customer To Confirm').'</button>';
	      $html .= '<div class="text-center text-small" style="font-size:10px;"><i class="fa fa-info-circle"></i>'; 
	      $html .= esc_html__('Customer To Confirm Lead', 'homey-child').'</div>';
	      $html .= '</td></tr>';
	   endwhile;
	   $html .= '</tbody></table>';
	   $html .= '</div>';
	   $html .= homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); 
        else:
           $html .= '<div class="">'; 
           $html .= esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); 
	   $html .= '</div>';      
        endif; 
        } 
        wp_reset_postdata();
        echo $html;
    }

    // denial response from client
    if($_POST['type'] == 'denied'){
	//update the postmeta values for the reservation
    	$denied_props = get_post_meta($reservation_Id, 'denied_reservations', true); 
    	if(empty($approved_props)){
      	   update_post_meta($reservation_Id, 'denied_reservations', $listing_Id);
    	}else{
      	   $addlistings = $denied_props.','.$listing_Id;
           update_post_meta($reservation_Id, 'denied_reservations', $addlistings);
        }

	//create the html to replace the listings
    	$selected_props = get_post_meta($reservation_Id, 'denied_reservations', true);
    	//----------------------------------------------
    	// generate query for selected houses listings
    	//----------------------------------------------
    	$no_of_listing   =  '9';
    	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    	$selectedPropsArray = explode(',',$selected_props);
    	$selected_qargs = array(
       	   'post_type'        =>  'listing',
       	   //'author'           =>  $userID,
       	   'paged'             => $paged,
       	   'posts_per_page'    => $no_of_listing,
       	   'post_status'      =>  'publish', 
       	   'post__in' => $selectedPropsArray
    	);
    	$selected_qargs = homey_listing_sort ($selected_qargs);
    	$selected_props_qry = new WP_Query($selected_qargs);
    	$meta_query = []; 

    	if(isset($selected_props_qry)){ 
      	if($selected_props_qry->have_posts()): 
	   $html .= '<div class="table-block dashboard-listing-table dashboard-table">';
           $html .= '<table class="table table-hover"><tbody id="denial_houses_listings">';
	   while ($selected_props_qry->have_posts()): $selected_props_qry->the_post(); 
	      $address = get_post_meta( get_the_ID(), 'homey_listing_address', true ); 
	      $html .= '<tr><td data-label="'.esc_html__('thumbnail').'">';
              $html .= '<a href="'.get_the_permalink($post->ID).'">';
              if( has_post_thumbnail( $post->ID ) ) {
                 $html .= get_the_post_thumbnail($post->ID, 'homey-listing-thumb',  array('class' => 'img-responsive dashboard-listing-thumbnail' ) );
              }else{ $html .= homey_image_placeholder( 'homey-listing-thumb' ); } 
              $html .= '</a></td>';
              $html .= '<td data-label="'.esc_html__('title-address').'">';
	      $html .= '<div style="float:left;width:100%">';
              $html .= '<a href="'.get_the_permalink($post->ID).'"><strong>'.get_the_title($post->ID).'</strong></a>';
              if(!empty($address)) { $html .= '<address>'.esc_attr($address).'</address>'; } 
	      $html .= '</div>';
              $html .= '<div style="float:left;width:100%">';
	      $html .= '<input type="hidden" name="listingid" value="'.$post->ID.'"/>';
	      $html .= '</div>';
	      $html .= '</div></td></tr>';
	   endwhile;
	   $html .= '</tbody></table>';
	   $html .= '</div>';
	   $html .= homey_pagination( $selected_props_qry->max_num_pages, $range = 2 ); 
        else:
           $html .= '<div class="">'; 
           $html .= esc_html__('No Houses Currently Sent To Check Availability', 'homey-child'); 
	   $html .= '</div>';      
        endif; 
        } 
        wp_reset_postdata();
        echo $html;
    }
    wp_die();
}

/** Action for submission of add new registered user from agent dashboard */
add_action( 'wp_ajax_nopriv_homey_child_agent_register_customer', 'homey_child_agent_register_customer' );  
add_action( 'wp_ajax_homey_child_agent_register_customer', 'homey_child_agent_register_customer' );  
function homey_child_agent_register_customer() { 
    $response = ''; $customeruname = ''; 
    //generate the variables
    $customername = trim(urldecode($_POST['customername']));
    $customeremail = trim(urldecode($_POST['customeremail']));
    $customerphone = trim(urldecode($_POST['customerphone']));
    $reservationid = trim(urldecode($_POST['reservationid']));   
    //var_dump($customername); var_dump($customeremail); 
    //var_dump($customerphone); var_dump($reservationid); exit;
    $password = wp_generate_password(12, true ); 

    if(strpos($customername, ' ') !== false){ 
	$namearray = explode(' ', $customername); 
	$customeruname = strtolower($namearray[0]).strtolower($namearray[1]); 
	$customerfname = $namearray[0];
	$customerlname = '';
	foreach($namearray as $indx => $name){
	   if($indx > 0){ $customerlname .= $name; }
	}
    } else { 
	$customeruname = strtolower($customername); 
	$customerfname = $namearray[0];
	$customerlname = '';
    }
    //var_dump($customeruname); exit;

    // check if user exists in the database
    if( (null == username_exists( $customeruname )) && (null == email_exists( $customeremail )) ){ 
      //create user with all the details
      $userdata = array(
	'user_pass'	=> $password, 
	'user_login'	=> $customeruname,
        'user_nicename' => $customername, 
        'user_email'	=> $customeremail,
        'display_name'	=> $customername,
        'nickname'  	=> $customeruname,
	'first_name' 	=> $customerfname, 
	'last_name' 	=> $customerlname, 
	'user_registered' => date('Y-m-d H:i:s'), 
	'show_admin_bar_front' => false, 
	'role' => 'homey_renter',
      );
      $user_id = wp_insert_user( $userdata ); 

      //update user meta with the address details.
      //update_user_meta( $user_id, 'address', $agent_address[1] ); 
      //update_user_meta( $user_id, 'city', $agent_city[1] ); 
      //update_user_meta( $user_id, 'postalcode', $agent_pcode[1] ); 
      //update_user_meta( $user_id, 'state', $agent_state[1] );

      //update listing author using user id
      //$arg = array( 'ID' => $reservationid, 'post_author' => $user_id, );
      //wp_update_post( $arg );
      update_post_meta( $reservationid, 'listing_renter', $user_id );
      update_post_meta( $reservationid, 'homey_lead_type', 'client_lead' );

      //send email to admin and user after creation of the user
      homey_wp_new_user_notification($user_id, $password);

      //send a message to broker about creation of the user
      //$message = esc_html__('A new agent has been set up:', 'homey-child' ) . "\r\n\r\n";
      //$message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username[1]) . "\r\n\r\n";
      //$message .= sprintf(esc_html__('Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
      //$message .= esc_html__('Agent Will Now Be Able To Login Using The Password Received.', 'homey-child') . "\r\n\r\n";
      //$title = esc_html__('Agent Setup Message.', 'homey-child') . "\n";
      //homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

      $response = 'User Created And Notified!!!';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
    }else{
      $response = 'Error: User Already Exists In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }
}

/** Action for change reservation status from customer dashboard on clicking confirm button */
add_action( 'wp_ajax_nopriv_homey_child_confirm_modification_via_customer_response', 'homey_child_confirm_modification_via_customer_response' );  
add_action( 'wp_ajax_homey_child_confirm_modification_via_customer_response', 'homey_child_confirm_modification_via_customer_response' );  
function homey_child_confirm_modification_via_customer_response() { 
    $listing_Id = trim($_POST['listingid']);
    $reservation_Id = trim($_POST['reservationid']);

    //update the postmeta with the following
    update_post_meta($reservation_Id, 'reservation_confirmed', $listing_Id);
    update_post_meta($reservation_Id, 'reservation_status', 'confirmed');

    // send a message to broker about creation of the user
    //$message = esc_html__('Availability Check On Property:', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= esc_html__('Please Let Us Know If the Property Is Available From The Following Dates.', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Message From Agent: %s', 'homey-child'), $agent_message[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Phone: %s', 'homey-child'), $agent_ph[1]) . "\r\n\r\n";
    //$message .= esc_html__('Please Let Us Know By Replying To The Email.', 'homey-child') . "\r\n\r\n";
    //$title = esc_html__('Availability Check On Property. Property Id: '.$listingId, 'homey-child') . "\n";
    //$result = homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);
    //if($result){ 
    //  $response = 'Email Sent To Host.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //} else{ 
    //  $response = 'Email To Host Could Not Be Sent.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //}

    $response = 'Confirmed, Awaiting Agent Confirmation!!!';
    echo json_encode( array('success' => true, 'msg' => $response) ); 
    wp_die();
}


/** Action for change final reservation status from agent dashboard after response from Renter */
add_action( 'wp_ajax_nopriv_homey_child_final_confirmation_agent_response', 'homey_child_final_confirmation_agent_response' );  
add_action( 'wp_ajax_homey_child_final_confirmation_agent_response', 'homey_child_final_confirmation_agent_response' );  
function homey_child_final_confirmation_agent_response() { 
    $listing_Id = trim($_POST['listingid']);
    $reservation_Id = trim($_POST['reservationid']);

    //update the postmeta with the following
    //update_post_meta($reservation_Id, 'reservation_confirmed', $listing_Id);
    update_post_meta($reservation_Id, 'reservation_status', 'booked'); 
    update_post_meta($reservation_Id, 'reservation_booked', $listing_Id); 

    //get required variables for processing
    $renterId = get_post_meta( $reservation_Id, 'listing_renter', true ); 
    $renter = get_userdata($renterId);
    $brokerId = get_post_meta( $reservation_Id, 'homey_brokers', true ); 
    $broker = get_userdata($brokerId);
    $agentId = get_post_meta( $reservation_Id, 'homey_agents', true );  
    $agent = get_userdata($agentId);   
    //$homelistingpost = get_post($listing_Id);
    $propertyPCode = get_post_meta($listing_Id, 'homey_zip', true); 
    $agent_name = $agent->display_name; $agent_email = $agent->user_email; 
    $renter_name = $renter->display_name; $renter_email = $renter->user_email;
    $broker_name = $broker->display_name; $broker_email = $broker->user_email;

    // send a message about the booking to the related persons
    $message = esc_html__('The Property Has Been Booked. Please Find the Details Attached.', 'homey-child' ) . "<br/><br/>"; 
    $message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), get_the_title($listing_Id)) . "<br/>";    
    $message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), get_permalink($listing_Id)) . "<br/>";
    $message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode) . "<br/><br/>";
    $message .= esc_html__('Agent Details (Booked By):', 'homey-child' ) . "<br/>"; 
    $message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name) . "<br/>";    
    $message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email) . "<br/><br/>";
    $message .= esc_html__('Customer Details (Booked For):', 'homey-child' ) . "<br/>"; 
    $message .= sprintf(esc_html__('Customer Name: %s', 'homey-child'), $renter_name) . "<br/>";    
    $message .= sprintf(esc_html__('Customer Email: %s', 'homey-child'), $renter_email) . "<br/><br/>";
    $title = esc_html__('Booking Confirmation On Property. Property Id: '.$listingId, 'homey-child') . "\n";

    //send emails to the related persons
    $res1 = homey_send_emails($renter_email, wp_specialchars_decode( $title ), $message);
    $res2 = homey_send_emails($agent_email, wp_specialchars_decode( $title ), $message);
    $res3 = homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);

    if($res1 && $res2 && $res3){ 
        $response = 'Booking Confirmed. Emails Sent.';
        echo json_encode( array('success' => true, 'msg' => $response) ); 
    } else{ 
       $response = 'Booking Confirmed. All Emails Could Not Be Sent.';
       echo json_encode( array('success' => true, 'msg' => $response) ); 
    }
    wp_die();
}


/*------------------------------------------------------------------------------*/
// Register New User From "Register" Menu
/*------------------------------------------------------------------------------*/
add_action( 'wp_ajax_nopriv_homey_child_register', 'homey_child_register' );
add_action( 'wp_ajax_homey_child_register', 'homey_child_register' );
function homey_child_register() { //var_dump($_POST); exit;
        //$local = homey_get_localization();
        check_ajax_referer('homey_register_nonce', 'homey_register_security');
        $allowed_html = array();

        //$usermane = trim( sanitize_text_field( wp_kses( $_POST['username'], $allowed_html ) ));

        $userFname = trim( sanitize_text_field( wp_kses($_POST['userfname'], $allowed_html) ));
        $userLname = trim( sanitize_text_field( wp_kses($_POST['userlname'], $allowed_html) ));
        $email     = trim( sanitize_text_field( wp_kses($_POST['useremail'], $allowed_html) ));
        $term_condition  = trim( $_POST['term_condition'] );
        $enable_password = homey_option('enable_password');
        $response = $_POST["g-recaptcha-response"];

        $user_role = get_option( 'default_role' );  

        if( isset( $_POST['role'] ) && $_POST['role'] != '' ){
            $user_role = isset( $_POST['role'] ) ? sanitize_text_field( wp_kses( $_POST['role'], $allowed_html ) ) : $user_role;
        } else {
            $user_role = $user_role;
        }

        $term_condition = ( $term_condition == 'on') ? true : false;

	$username = strtolower($userFname).strtolower($userLname);

	$userdisplayname = $userFname.' '.$userLname; 

        if( !$term_condition ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('You need to agree with terms & conditions.', 'homey-login-register') ) );
            wp_die();
        }

        /*if( empty( $usermane ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The username field is empty.', 'homey-login-register') ) );
            wp_die();
        }
        if( strlen( $usermane ) < 3 ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Minimum 3 characters required', 'homey-login-register') ) );
            wp_die();
        }
        if (preg_match("/^[0-9A-Za-z_]+$/", $usermane) == 0) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid username (do not use special characters or spaces)!', 'homey-login-register') ) );
            wp_die();
        }*/

        if( username_exists( $username ) ) { 
	    if( email_exists( $email ) ) {
              echo json_encode( array( 'success' => false, 'msg' => esc_html__('User Is Already Registered.', 'homey-login-register') ) );
              wp_die();
	    }else{
		$usersalt = substr(md5(time()), 0, 6); 
		$username = strtolower($userFname).strtolower($userLname).'_'.$usersalt;
	    }
        }

        if( empty( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('The email field is empty.', 'homey-login-register') ) );
            wp_die();
        }

        if( email_exists( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Email Address Is Already Registered.', 'homey-login-register') ) );
            wp_die();
        }

        if( !is_email( $email ) ) {
            echo json_encode( array( 'success' => false, 'msg' => esc_html__('Invalid Email Address.', 'homey-login-register') ) );
            wp_die();
        }

        if( $enable_password == 'yes' ){
            $user_pass         = trim( sanitize_text_field( wp_kses( $_POST['register_pass'] ,$allowed_html) ) );
            $user_pass_retype  = trim( sanitize_text_field( wp_kses( $_POST['register_pass_retype'] ,$allowed_html) ) );

            if ($user_pass == '' || $user_pass_retype == '' ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('One of the password field is empty!', 'homey-login-register') ) );
                wp_die();
            }

            if ($user_pass !== $user_pass_retype ) {
                echo json_encode( array( 'success' => false, 'msg' => esc_html__('Passwords do not match', 'homey-login-register') ) );
                wp_die();
            }
        }

        $enable_forms_gdpr = homey_option('enable_forms_gdpr');

        if( $enable_forms_gdpr != 0 ) {
            $privacy_policy = isset($_POST['privacy_policy']) ? $_POST['privacy_policy'] : '';
            if ( empty($privacy_policy) ) {
                echo json_encode(array(
                    'success' => false,
                    'msg' => homey_option('forms_gdpr_validation')
                ));
                wp_die();
            }
        }

        homey_google_recaptcha_callback();

        if($enable_password == 'yes' ) {
            $user_password = $user_pass;
        } else {
            $user_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
        }

        //$user_id = wp_create_user( $usermane, $user_password, $email );
        $userdata = array(
	   'user_pass' => $user_password, 
	   'user_login' => $username,
           'user_nicename' => $userdisplayname, 
           'user_email' => $email,
           'display_name' => $userdisplayname,
           'nickname' => $username,
	   'first_name' => $userFname, 
	   'last_name' => $userLname, 
	   'user_registered' => date('Y-m-d H:i:s'), 
	   'show_admin_bar_front' => false, 
      	);
      	$user_id = wp_insert_user( $userdata ); 

        if ( is_wp_error($user_id) ) {
            echo json_encode( array( 'success' => false, 'msg' => $user_id ) );
            wp_die();
        } else {
            wp_update_user( array( 'ID' => $user_id, 'role' => $user_role ) );

            if( $enable_password =='yes' ) {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Your account was created and you can login now! UserID: '.$username, 'homey-login-register') ) );
            } else {
                echo json_encode( array( 'success' => true, 'msg' => esc_html__('Registration complete. Please check your email!', 'homey-login-register') ) );
            }
            homey_wp_new_user_notification( $user_id, $user_password );
        }
        wp_die();
}

/** Action for submission of add new registered user from agent dashboard */
add_action( 'wp_ajax_nopriv_homey_child_renter_to_host_conversion', 'homey_child_renter_to_host_conversion' );  
add_action( 'wp_ajax_homey_child_renter_to_host_conversion', 'homey_child_renter_to_host_conversion' );  
function homey_child_renter_to_host_conversion() { 
    $response = ''; 
    //get the variables from the post data
    $renterId = $_POST['renterid'];
 
    //check if user exists in DB
    $wp_user = get_user_by('ID', $renterId); 
    if(!$wp_user){
      $response = 'Error: User Does Not Exist In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();

    } else {
      //update user to host
      $userdata = array(
	'ID'   => $renterId, 
	'role' => 'homey_host',
      );
      $user_id = wp_update_user( $userdata ); 

      //send a message to user about change of role
      $message = esc_html__('User Has Been Changed From Renter to Host.:', 'homey-child' ) . "<br/><br/>";
      $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $wp_user->display_name) . "<br/>";
      $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $wp_user->user_email) . "<br/>";
      $message .= esc_html__('User Will Now Be Able To Login Using Same Username and Password.', 'homey-child') . "<br/><br/>";
      $title = esc_html__('User Has Been Changed From Renter to Host.', 'homey-child') . "\n";
      homey_send_emails($wp_user->user_email, wp_specialchars_decode( $title ), $message);

      $response = 'User Updated And Notified!!!. Please Re-Login To View The Modified Dashboard.';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
    }
}

/** Action for submission of agent deletion from broker dashboard */
add_action( 'wp_ajax_nopriv_homey_child_broker_dashboard_agent_deletion', 'homey_child_broker_dashboard_agent_deletion' );  
add_action( 'wp_ajax_homey_child_broker_dashboard_agent_deletion', 'homey_child_broker_dashboard_agent_deletion' );  
function homey_child_broker_dashboard_agent_deletion() { 
    $response = ''; 
    //get the variables from the post data
    $agentId = $_POST['agentid']; $brokerId = $_POST['brokerid']; 

    $wp_user = get_user_by('ID', $agentId); 
    if(!$wp_user){
      $response = 'Error: User Does Not Exist In Database.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();

    } else {

      //list all listings for properties and update to none
      $largs = array( 'post_type' => 'listing', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
      $lmeta_query[] = array( 'key' => 'homey_assignedagent', 'value' => $agentId, 'compare' => '=' );
      $largs['meta_query'] = $lmeta_query; $lqry_res = new WP_Query($largs); 
      if ( $lqry_res->have_posts() ){
        foreach($lqry_res->posts as $prop){ 
	  update_post_meta($prop->ID, 'homey_assignedagent', 0);					    
        }
      } 
      wp_reset_postdata(); $lmeta_query=[]; 

      //list all listings for reservations and update to none
      $rargs = array( 'post_type' => 'homey_reservation', 'posts_per_page' => -1, 'post_status' => 'publish' ); 
      $rmeta_query[] = array( 'key' => 'homey_agents', 'value' => $agentId, 'compare' => '=' );
      $rargs['meta_query'] = $rmeta_query; $rqry_res = new WP_Query($rargs); //var_dump($rqry_res);
      if ( $rqry_res->have_posts() ){
        foreach($rqry_res->posts as $res1){ 
	  update_post_meta($res1->ID, 'homey_agents', 0);	
        }
      }
      wp_reset_postdata(); $rmeta_query=[];
 
      $result = wp_delete_user( $agentId, null );
      //send a message to user about change of role
      //$message = esc_html__('User Has Been Changed From Renter to Host.:', 'homey-child' ) . "<br/><br/>";
      //$message .= sprintf(esc_html__('Username: %s', 'homey-child'), $wp_user->display_name) . "<br/>";
      //$message .= sprintf(esc_html__('Email: %s', 'homey-child'), $wp_user->user_email) . "<br/>";
      //$message .= esc_html__('User Will Now Be Able To Login Using Same Username and Password.', 'homey-child') . "<br/><br/>";
      //$title = esc_html__('User Has Been Changed From Renter to Host.', 'homey-child') . "\n";
      //homey_send_emails($wp_user->user_email, wp_specialchars_decode( $title ), $message);

      $response = 'Agent Deleted And All Assignment Nullified.';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
    }
}

/** Action for new lead when rejected by agent */
add_action( 'wp_ajax_nopriv_homey_child_new_lead_agent_denial', 'homey_child_new_lead_agent_denial' );  
add_action( 'wp_ajax_homey_child_new_lead_agent_denial', 'homey_child_new_lead_agent_denial' );  
function homey_child_new_lead_agent_denial() { 
    global $current_user; $response = ''; 
    $current_user = wp_get_current_user(); 
    //get the variables from the post data
    $reservationId = $_POST['reservationid']; 
    //get post meta of the reservation id 
    $agentId = get_post_meta($reservationId, 'homey_agents', true);
    $cstatus = get_post_meta($reservationId, 'reservation_status', true);

    //if the status is not as "under_review", then rejection not possible. 
    if($cstatus != 'under_review'){
      $response = 'Error: Lead Is Not New Lead, Rejection Not Possible.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();	
    } elseif($current_user->ID != $agentId){ //if another user pressed reject, then discard
      $response = 'Error: Lead Is Assigned To Another Agent, Cannot Reject.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();	
    } else{
      //agent has rejected the lead change status
      update_post_meta($reservationId, 'reservation_status_agent', 'rejected');
      update_post_meta($reservationId, 'homey_agents', 0);

      $routedAgentIds = get_post_meta($reservationId, 'homey_agents_routed', true);
      if(empty($routedAgentIds)){
	update_post_meta($reservationId, 'homey_agents_routed', $agentId);
      }else{
	$agentdenied = $routedAgentIds.','.$agentId;
        update_post_meta($reservationId, 'homey_agents_routed', $agentdenied);
      }

      // genearte a comment for rejection of the reservation. 
      $agent = get_user_by( 'ID', $agentId);
      $message = 'Lead Rejected';
      $args = array(
	'comment_agent' => 'lead reject button',
	'comment_approved' => 1, 'comment_author' => $agent->display_name,
	'comment_author_email' => $agent->user_email, 'comment_author_IP' => '',
	'comment_author_url' => '', 'comment_content' => $message, 
	'comment_date' => date('Y-m-d H:i:s'),
	'comment_date_gmt' => date('Y-m-d H:i:s'),
	'comment_karma' => 0, 'comment_parent' => 0, 
	'comment_post_ID' => $reservationId, 
	'comment_type' => 'lead_rejection',
	'user_id' => $agent->ID,
	'comment_meta' => array(
	   '_wxr_import_user' => $agent->ID,
	   'reservation_id' => $rservation_id,
	),
      ); 
      $commentid = wp_insert_comment($args);

      $response = 'New Lead Rejected.';
      echo json_encode( array('success' => true, 'msg' => $response) );
      wp_die();
   }
}

/** Action for new lead when accepted by agent */
add_action( 'wp_ajax_nopriv_homey_child_new_lead_agent_accept', 'homey_child_new_lead_agent_accept' );  
add_action( 'wp_ajax_homey_child_new_lead_agent_accept', 'homey_child_new_lead_agent_accept' );  
function homey_child_new_lead_agent_accept() { 
    global $current_user; $response = ''; 
    $current_user = wp_get_current_user(); 
    //get the variables from the post data
    $reservationId = $_POST['reservationid']; 
    //get post meta of to check the agentid.
    $agentIds = get_post_meta($reservationId, 'homey_agents_notified', true);
    if(!empty($agentIds)){
      if(strstr($agentIds,',')){
         $checkAgIds = explode(',',$agentIds);
      }else{ 
	$checkAgIds = array($agentIds);
      }
    }else{
      $response = 'Error: Not A Rejected Lead.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }

    if(!in_array($current_user->ID, $checkAgIds)){
      $response = 'Error: Agent Not In Notified.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }else{
	//update the rejected status 
	update_post_meta($reservationId, 'reservation_status_agent', 'accepted');
	update_post_meta($reservationId, 'homey_agents', $current_user->ID);
	//remove all agents notified. 
	delete_post_meta($reservationId, 'homey_agents_notified');
        // genearte a comment for rejection of the reservation. 
        $agent = get_user_by( 'ID', $current_user->ID);
        $message = 'Lead Accepted';
        $args = array(
	  'comment_agent' => 'lead accept button',
	  'comment_approved' => 1, 'comment_author' => $agent->display_name,
	  'comment_author_email' => $agent->user_email, 'comment_author_IP' => '',
	  'comment_author_url' => '', 'comment_content' => $message, 
	  'comment_date' => date('Y-m-d H:i:s'),
	  'comment_date_gmt' => date('Y-m-d H:i:s'),
	  'comment_karma' => 0, 'comment_parent' => 0, 
	  'comment_post_ID' => $reservationId, 
	  'comment_type' => 'lead_acceptance',
	  'user_id' => $agent->ID,
	  'comment_meta' => array(
	    '_wxr_import_user' => $agent->ID,
	    'reservation_id' => $rservation_id,
	  ),
        ); 
        $commentid = wp_insert_comment($args);
        $response = 'Agent Assigned.';
        echo json_encode( array('success' => true, 'msg' => $response) );
        wp_die();
    }
}

/**
 * Agent Selection For Brokers For Lead Checkout. 
 * This happens when an agent rejects the lead routed to them.
 */
add_action( 'wp_ajax_nopriv_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
add_action( 'wp_ajax_homey_child_agent_allocation', 'homey_child_agent_allocation' );  
function homey_child_agent_allocation() { 
    $response = ''; 
    $data = $_POST['data']; $details = explode(',', $data); 
    //var_dump($details); exit; 

    $reservationpost = get_post($details[2]); //var_dump($listingpost); 

    //generate the agents list based on the input. 
    $listagents = get_post_meta($reservationpost->ID, 'homey_agents_notified', true);   
    if(empty($listagents)){
	update_post_meta($reservationpost->ID, 'homey_agents_notified', $details[0]);
    } else {
	$check_agents = explode(',',$listagents); 
	if(!in_array($details[0], $check_agents)){
	  $new_agents = $listagents.','.$details[0]; 
	  update_post_meta($reservationpost->ID, 'homey_agents_notified', $new_agents); 
	} else { 
    	  $response = 'Agent Already Notified';
    	  echo json_encode( array('success' => false, 'msg' => $response) );
    	  wp_die();	  
	}
    } 

    //agent will receive an email.
    $agent_info = get_userdata($details[0]); 
    $useremail = $agent_info->user_email; 
    $userdisplayname = $agent_info->display_name; //var_dump($userdisplayname);
    $username = $agent_info->user_login;
    $message = esc_html__('A new lead has been added to profile.', 'homey-child' ) . "<br/>";
    $message .= esc_html__('Please login to the dashboard with username and password to view the lead.', 'homey-child') . "<br/><br/>";
    $message .= sprintf(esc_html__('Username: %s', 'homey-child'), $username) . "<br/>";
    $message .= sprintf(esc_html__('Email: %s', 'homey-child'), $useremail) . "<br/>";
    $title = esc_html__('Notification For A Lead Assignment.', 'homey-child') . "\n";
    homey_send_emails($useremail, $title, $message);

    //broker will receive an email.
    //$broker_info = get_userdata($details[1]);
    //$brokeremail = $broker_info->user_email; //var_dump($brokeremail);
    //$message = esc_html__('A new lead has been assigned to agent\'s profile.', 'homey-child' ) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $userdisplayname) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $useremail) . "\r\n\r\n";
    //$message .= esc_html__('Agent has been notified of the same.', 'homey-child') . "\r\n\r\n";
    //$title = esc_html__('Notification Of A Lead Assignment To An Agent.', 'homey-child') . "\n";
    //homey_send_emails($brokeremail, $title, $message);

    if(isset($new_agents) && !empty($new_agents)){
	$c_agents = explode(',',$new_agents); $c_agentStr = '';
	foreach($c_agents as $agId){ 
	  $agent_info = get_userdata($agId); 
	  $c_agentStr .= $agent_info->display_name."\n";
	}
    } else {
	$agent_info = get_userdata($details[0]); $c_agentStr = ''; 
	$c_agentStr = $agent_info->display_name."\n";
    }
    $response = $c_agentStr;
    echo json_encode(array('success' => true, 'msg' => nl2br($response)));
    wp_die();
}

/**
 * Generate Routing Rules from Broker Dashboard. 
 */
add_action( 'wp_ajax_nopriv_homey_child_generate_routing_rules', 'homey_child_generate_routing_rules' );  
add_action( 'wp_ajax_homey_child_generate_routing_rules', 'homey_child_generate_routing_rules' );  
function homey_child_generate_routing_rules() { 
    global $current_user; $response = ''; 
    $current_user = wp_get_current_user(); 
    $form_data = $_POST['form_data']; $form_details = explode('&', $form_data);  
    //var_dump($form_details); exit; 

    //generate routing variables 
    $r_state = explode('=',$form_details[0]); $r_city = explode('=',$form_details[1]); 
    $r_pcode = explode('=',$form_details[2]); $r_agentId = explode('=',$form_details[3]); 
    $r_prange = explode('=',$form_details[4]); $r_keywords = explode('=',$form_details[5]); 
    $r_brokerId = $current_user->ID;  

    //if state, city and postcode is empty, generate error
    if($r_state[1] == '*' || $r_city[1] == '*' || $r_pcode[1] == '*'){ 
      $response = 'Error: State, City And Postcode Are Mandatory !!!.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }

    //check all rules for same postcode, if exists donot add
    $args = array('post_type'  => 'homey_routing', 'posts_per_page' => -1,);
    $meta_query[] = array('key' => 'homey_routing_pcode','value' => $r_pcode[1],'compare' => '=');
    $args['meta_query'] = $meta_query;   
    $pcodeRules = new WP_Query($args); $match = 0;     
    if($pcodeRules->have_posts()){
      foreach($pcodeRules->posts as $pcodeRule){
	$pcodeRuleState = get_post_meta($pcodeRule->ID,'homey_routing_state',true);
	$pcodeRuleCity = get_post_meta($pcodeRule->ID,'homey_routing_city',true);
	$pcodeRuleAgent = get_post_meta($pcodeRule->ID,'homey_routing_agent',true);
	$pcodeRulePRange = get_post_meta($pcodeRule->ID,'homey_routing_prange',true);
	$pcodeRuleKeywords = get_post_meta($pcodeRule->ID,'homey_routing_keywords',true); 
	if($pcodeRuleState == $r_state[1] && $pcodeRuleCity == $r_city[1] 
	   && $pcodeRuleAgent == $r_agentId[1] && $pcodeRulePRange == $r_prange[1] 
	   && $pcodeRuleKeywords == $r_keywords[1]){ $match = 1; break; }
      }
    }
    //if match occurs then return
    if($match==1){ 
      $response = 'Error: Rule Already Exists For Agent !!!.';
      echo json_encode( array('success' => false, 'msg' => $response) );
      wp_die();
    }

    //generate agent and broker
    $agent = get_userdata($r_agentId[1]); $broker = get_userdata($r_brokerId); 

    //title for the routing rules
    $r_rule_title = 'Routing Rule By Broker. Id-'.$r_brokerId; 
    $r_rule_details = 'State: '.$r_state[1].PHP_EOL.'City: '.$r_city[1].PHP_EOL.
		      'PCode: '.$r_pcode[1].PHP_EOL.'Agent: '.$agent->display_name.PHP_EOL. 
		      'Price Range: '.$r_prange[1].PHP_EOL.'Keywords: '.$r_keywords[1].PHP_EOL.
		      'Generated By: '.$broker->display_name.PHP_EOL;

    //generation of routing rule
    $rule = array(
	'post_author'	=> $r_brokerId, 
	'post_title'	=> $r_rule_title,
        'post_type' 	=> 'homey_routing', 
        'post_status'	=> 'publish',
        'post_parent'	=> 0,
        'post_excerpt'  => $r_rule_details,	
	//'post_content' => $r_rule_details,
	'post_name' => 'routing-rule-brokerid-'.$r_brokerId,
    );
    $rule_id =  wp_insert_post($rule);
    update_post_meta($rule_id, 'homey_routing_status', 'disabled');
    update_post_meta($rule_id, 'homey_routing_state', $r_state[1]);
    update_post_meta($rule_id, 'homey_routing_city', $r_city[1]);
    update_post_meta($rule_id, 'homey_routing_pcode', $r_pcode[1]);
    update_post_meta($rule_id, 'homey_routing_agent', $r_agentId[1]);
    update_post_meta($rule_id, 'homey_routing_prange', $r_prange[1]);
    update_post_meta($rule_id, 'homey_routing_keywords', $r_keywords[1]);

    $response = 'Success: Rule Generated!!!.';
    echo json_encode( array('success' => true, 'msg' => $response) );
    wp_die();
}

/** Action for change of status of the rule from broker dashboard */
add_action( 'wp_ajax_nopriv_homey_child_rulestatus_change_broker_dashboard', 'homey_child_rulestatus_change_broker_dashboard' );  
add_action( 'wp_ajax_homey_child_rulestatus_change_broker_dashboard', 'homey_child_rulestatus_change_broker_dashboard' );  
function homey_child_rulestatus_change_broker_dashboard() { 
    $response = ''; 
    $rule_Id = trim($_POST['ruleid']); $status = trim($_POST['status']);

    //update the postmeta with the following
    update_post_meta($rule_Id, 'homey_routing_status', $status);

    // send a message to broker about creation of the user
    //$message = esc_html__('Availability Check On Property:', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= esc_html__('Please Let Us Know If the Property Is Available From The Following Dates.', 'homey-child' ) . "\r\n\r\n"; 
    //$message .= sprintf(esc_html__('Property Title: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Property Link: %s', 'homey-child'), $propertyLink[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Property Postcode: %s', 'homey-child'), $propertyPCode[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Message From Agent: %s', 'homey-child'), $agent_message[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Name: %s', 'homey-child'), $agent_name[1]) . "\r\n\r\n";    
    //$message .= sprintf(esc_html__('Agent Email: %s', 'homey-child'), $agent_email[1]) . "\r\n\r\n";
    //$message .= sprintf(esc_html__('Agent Phone: %s', 'homey-child'), $agent_ph[1]) . "\r\n\r\n";
    //$message .= esc_html__('Please Let Us Know By Replying To The Email.', 'homey-child') . "\r\n\r\n";
    //$title = esc_html__('Availability Check On Property. Property Id: '.$listingId, 'homey-child') . "\n";
    //$result = homey_send_emails($broker_email, wp_specialchars_decode( $title ), $message);
    //if($result){ 
    //  $response = 'Email Sent To Host.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //} else{ 
    //  $response = 'Email To Host Could Not Be Sent.';
    //  echo json_encode( array('success' => true, 'message' => $response) ); 
    //}

    if($status == 'enabled'){
	$response = 'Rule Enabled !!!';
    	echo json_encode( array('success' => true, 'msg' => $response) ); 
    	wp_die();
    } else {
	$response = 'Rule Disabled !!!';
    	echo json_encode( array('success' => true, 'msg' => $response) ); 
    	wp_die();
    }
}

