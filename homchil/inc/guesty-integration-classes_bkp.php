<?php 
/**
 * Classes for WP List Table For Guesty
 */

if( is_admin()) { 

function sample_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
    </div>
    <?php
}
//add_action( 'admin_notices', 'sample_admin_notice__success' );*/

 /** Defining Custom Table */
 if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
 }

 /** guesty accounts list table */
 class Guesty_Accounts_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_account', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_accounts', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}

/** guesty users list table */

 class Guesty_Users_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_user', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_users', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();

		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}

/** guesty contacts list table */
 class Guesty_Contacts_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_contact', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_contacts', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}


/** guesty owners list table */
 class Guesty_Owners_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_owner', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_owners', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}


/** guesty integrations list table */
 class Guesty_Integrations_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_integration', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_integrations', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}

/** guesty listing list table */
 class Guesty_Listing_List_Table extends WP_List_Table { 
	//private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
	//				'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
	//				'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' );
	private $listing_columns = array( 'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'title' => 'Title', 'thumb' => 'Thumbnail', 'fulladdress' => 'Address In Full', 
					'type' => 'Type', 'propertyType' => 'Property Type', 'roomType' => 'Room Type', 
					'price' => 'Price'); 
	//private $items_per_page = 3;
	private $items_per_page = 25;

  	//using curl to fetch all objects from guesty db. 
  	private $mainurl = 'https://api.guesty.com/api/v2';
  	//private $apikey = base64_encode('2ed0010c5ce1a8c46a6dd9c292ac412a');
  	//private $secretkey = base64_encode('ee62df719388c3b1af76b151da5e63c1'); 
  	private $apikey = '2ed0010c5ce1a8c46a6dd9c292ac412a';
  	private $secretkey = 'ee62df719388c3b1af76b151da5e63c1'; 

	public function __construct() { 
	   parent::__construct([
		'singular' => __( 'guesty_listing', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_listings', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		//$columns = $this->demo_columns;
		$columns = $this->listing_columns;
	        return $columns;
	}

	function column_default($item, $column_name) {
		switch( $column_name ) {
		case 'thumb': 
                        /*if(isset($item["pictures"]) && !empty($item["pictures"])){
			  echo '<div id="gallery-div" style="display:none;">';
			  foreach($item["pictures"] as $picture){
			    echo '<a title="'.$picture["_id"].'" class="thickbox" rel="gallery">';
			    echo '<img src="'.$picture["regular"].'" width="500" height="550" /></a>';
			  }			    
			  echo '</div>';
			}
			if(isset($item["pictures"]) && !empty($item["pictures"])){
			  echo '<a href="#TB_inline?&width=600&height=550&inlineId=gallery-div" class="thickbox">';
			} else {  echo '<a href="javascript:void(0)">'; }*/
			echo '<a href="javascript:void(0)">';
			echo '<img src="'.$item['picture']['thumbnail'].'" width="120" height="100" /></a>'; 
			break; 
		case 'fulladdress': 
			if(isset($item["address"]["full"])){ echo $item["address"]["full"]; } 
			break;
		case 'price' : 
			if(isset($item["prices"]["basePriceUSD"])){
			  echo 'US $'.$item["prices"]["basePriceUSD"]; 
			} else{  echo 'US $'.$item["prices"]["basePrice"]; }
			break;
		default:
    			return '<em>'.$item[$column_name].'</em>'; 
			break;
		}
	}

	function column_cb($item) {
        	//return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['_id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   //$fulldata = $this->demo_data_wp_list_table(); 
		   //foreach($fulldata  as $item){ 
		   //  if(strstr($item['title'], $_REQUEST['s'])){ array_push($data, $item); }
		   //}
		   $apiresult = $this->fetch_guesty_data_from_api($_REQUEST['paged'], [], $_REQUEST['s']); 
		   //var_dump($apiresult); exit;
		   $apiresultarray = json_decode($apiresult, true); 
		   //var_dump($apiresultarray); exit;
		   $data = $apiresultarray['results'];

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   //$totalItems = count($data); 
		   $totalItems = $apiresultarray['count']; 

		} else {		
		   /** setup data and pagination */
        	   //$data = $this->demo_data_wp_list_table();
		   //usort( $data, array( &$this, 'sort_data' ) ); 
		   if(isset($_REQUEST['paged']) && $_REQUEST['paged'] > 1){
			$apiresult = $this->fetch_guesty_data_from_api($_REQUEST['paged']); 
		   	//var_dump($apiresult); exit;
		   	$apiresultarray = json_decode($apiresult, true); //var_dump($apiresultarray); exit;
		   	$data = $apiresultarray['results']; 
		   } else {
		   	$apiresult = $this->fetch_guesty_data_from_api(); 
		   	//var_dump($apiresult); exit;
		   	$apiresultarray = json_decode($apiresult, true); //var_dump($apiresultarray); exit;
		   	$data = $apiresultarray['results']; 
		   }

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();

        	   //$totalItems = count($data); 
		   $totalItems = $apiresultarray['count']; 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	));
		//$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;
        	global $current_user; $prefix = 'homey_';

        	wp_get_current_user(); $userID = $current_user->ID;

        	if ('import' === $this->current_action()) { var_dump($_REQUEST); //exit;
		   //if the rowids contain data, retrieve the details from api.
		   if(isset($_REQUEST["rowids"]) && !empty($_REQUEST["rowids"])){ 
			$importResult = $this->fetch_guesty_data_from_api($_REQUEST["paged"], $_REQUEST["rowids"]);
			//var_dump($importResult); exit;
			$importResultArray = json_decode($importResult, true); 
			echo '<br/>*** Import Started For Guesty Property Ids: '.json_encode($_REQUEST["rowids"]).'</br>';
			foreach($importResultArray['results'] as $item){ //var_dump($item); //exit; 
				echo '*** Import For Id: '.$item["_id"].'<br/>'; flush(); ob_flush();
				// create a query to check data 
				$qry = new WP_Query( array( 
				    'post_type' => 'listing', 
				    'meta_query' => array( 
					array('key' => 'homey_guesty_id', 'value' => $item["_id"], 'compare' => '='),  
				     ),
				));
				if( $qry->found_posts > 0){ break; } 
				// else start the new listing
				$s_time = microtime(true); 
				$new_listing = array( 'post_type' => 'listing' );
				// Title 
        			if( isset( $item['title'] ) ) {
            			  $new_listing['post_title'] = $item['title'];
       				} elseif( isset( $item["nickname"] ) ) {
            			  $new_listing['post_title'] = $item["nickname"];
				}
				// Description
        			if( isset( $item["publicDescription"] ) ) {
				  if( isset( $item["publicDescription"]["summary"] ) ){ 
				    $new_listing['post_content'] = wp_kses_post( $item["publicDescription"]["summary"] );
				    $new_listing['post_excerpt'] = wp_kses_post( $item["publicDescription"]["summary"] );
				  }
				  if( isset( $item["publicDescription"]["space"] ) ){ 				  
				    $new_listing['post_content'] = wp_kses_post( $item["publicDescription"]["space"] );
				    if( isset($item["publicDescription"]["houseRules"]) ){
				      $new_listing['post_content'] .= wp_kses_post( $item["publicDescription"]["houseRules"] );
				    }
				  }
        			}
				// Author & status
        			$new_listing['post_author'] = $userID; 
				$listing_id = 0; $new_listing['post_status'] = 'draft'; 
				$listing_id = wp_insert_post( $new_listing ); 

				//generate all meta content for the listing. 
				update_post_meta( $listing_id, $prefix.'guesty_id', $item["_id"] );				
				update_post_meta( $listing_id, $prefix.'instant_booking', 0 );
                		update_post_meta( $listing_id, $prefix.'guests', $item["accommodates"] ); 

				// currently bedrooms = number of rooms
				if(isset($item["bedrooms"])){ 
				  update_post_meta( $listing_id, $prefix.'listing_bedrooms', $item["bedrooms"] ); 
				  update_post_meta( $listing_id, $prefix.'listing_rooms', $item["bedrooms"] );
				}
				if(isset($item["beds"])){ update_post_meta( $listing_id, $prefix.'beds', $item["beds"] ); }
				if(isset($item["bathrooms"])){ update_post_meta( $listing_id, $prefix.'baths', $item["bathrooms"] ); }
			
				//pricing - nightly and weekends
				if( isset($item["prices"]["basePriceUSD"]) ){
				   update_post_meta( $listing_id, $prefix.'night_price', $item["prices"]["basePriceUSD"] );
				   update_post_meta( $listing_id, $prefix.'weekends_price', $item["prices"]["basePriceUSD"] );
				   update_post_meta( $listing_id, $prefix.'price_postfix', 'per night' );
				} elseif( isset($item["prices"]["basePrice"]) ){
				   update_post_meta( $listing_id, $prefix.'night_price', $item["prices"]["basePrice"] ); 
				   update_post_meta( $listing_id, $prefix.'weekends_price', $item["prices"]["basePrice"] );
				   update_post_meta( $listing_id, $prefix.'price_postfix', 'per night' );
				}
				// weekend price
				if( isset($item["prices"]["monthlyPriceFactor"]) && isset($item["prices"]["basePriceUSD"]) ){
				   $weekly_price = $item["prices"]["basePriceUSD"] * $item["prices"]["monthlyPriceFactor"];
				   $monthly_price = $item["prices"]["basePriceUSD"] * $item["prices"]["monthlyPriceFactor"];
				   //update_post_meta( $listing_id, $prefix.'weekends_price', $weekend_price ); 
		                   update_post_meta( $listing_id, $prefix.'priceWeek', $weekly_price ); 
                		   update_post_meta( $listing_id, $prefix.'priceMonthly', $monthly_price );
				}elseif( isset($item["prices"]["monthlyPriceFactor"]) && isset($item["prices"]["basePrice"]) ){
				   $weekly_price = $item["prices"]["basePrice"] * $item["prices"]["monthlyPriceFactor"]; 
				   $monthly_price = $item["prices"]["basePrice"] * $item["prices"]["monthlyPriceFactor"];
				   //update_post_meta( $listing_id, $prefix.'weekends_price', $weekend_price ); 
		                   update_post_meta( $listing_id, $prefix.'priceWeek', $weekly_price );
                		   update_post_meta( $listing_id, $prefix.'priceMonthly', $monthly_price );
				}
				// allow additional guests
				if( isset($item["prices"]["guestsIncludedInRegularFee"]) ){ 
				   if($item["prices"]["guestsIncludedInRegularFee"] == 1){
                		      update_post_meta( $listing_id, $prefix.'allow_additional_guests', 'yes' ); 
				   }
				}
				// cleaning fee 
				if( isset($item["prices"]["cleaningFee"]) ){ 
                		   update_post_meta( $listing_id, $prefix.'cleaning_fee', $item["prices"]["cleaningFee"] );
                		   update_post_meta( $listing_id, $prefix.'cleaning_fee_type', 'per_stay' );
				}
				// security fee
				if( isset($item["prices"]["securityDepositFee"]) ){
				   update_post_meta( $listing_id, $prefix.'security_deposit', $item["prices"]["securityDepositFee"] );
				}
				// account tax rate
				if( isset($item["accountTaxes"]) && !empty($item["accountTaxes"]) ){
				   if( $item["accountTaxes"][0]["units"] == "PERCENTAGE"){
				     update_post_meta( $listing_id, $prefix.'tax_rate', $item["accountTaxes"][0]["amount"] ); 
				   }
				}
				// weekend days 
				update_post_meta( $listing_id, $prefix.'weekends_days', 'fri_sat_sun' );

				// listing address 
				if( isset($item["address"]) && !empty($item["address"]) ){ $address = '';
				  //update_post_meta( $listing_id, $prefix.'listing_address', $item["address"]["full"] ); 
				  if(isset($item["address"]["street"])){ $address .= $item["address"]["street"].','; }
				  if(isset($item["address"]["city"])){ $address .= $item["address"]["city"].','; }
				  if(isset($item["address"]["state"])){ $address .= $item["address"]["state"].' '; }
				  if(isset($item["address"]["zipcode"])){ $address .= $item["address"]["zipcode"]; }
				  if(empty($address)){ $address .= $item["address"]["full"]; }				  
				  update_post_meta( $listing_id, $prefix.'listing_address', $address ); 				  
				}
				if( isset($item["address"]["zipcode"]) ){
				  update_post_meta( $listing_id, $prefix.'zip', $item["address"]["zipcode"] );
				}
            			// Country
            			if( isset($item["address"]["country"]) ){
                		   $country_id = wp_set_object_terms( $listing_id, $item["address"]["country"], 'listing_country' );
            			}
            			// State
            			if( isset( $item["address"]["state"] ) ) {
				   $state_id = wp_set_object_terms( $listing_id, $item["address"]["state"], 'listing_state' );
				   $homey_meta = array();
                		   $homey_meta['parent_country'] = isset( $item["address"]["country"] ) ? $item["address"]["country"] : '';
                		   if( !empty( $state_id) ) {
                    			update_option('_homey_listing_state_' . $state_id[0], $homey_meta);
                		   }
            			}
				// City
            			if( isset($item["address"]["city"] ) ) {
                		   $city_id = wp_set_object_terms( $listing_id, $item["address"]["city"], 'listing_city' );
				   $homey_meta = array();
                		   $homey_meta['parent_state'] = isset( $item["address"]["state"] ) ? $item["address"]["state"] : '';
                		   if( !empty( $city_id) ) {
                    		     update_option('_homey_listing_city_' . $city_id[0], $homey_meta);
                		   }
            			}
            			// Area
            			if( isset( $item["address"]["neighborhood"] ) ) {
				   $area_id = wp_set_object_terms( $listing_id, $item["address"]["neighborhood"], 'listing_area' );
				   $homey_meta = array();
                		   $homey_meta['parent_city'] = isset( $item["address"]["city"] ) ? $item["address"]["city"] : '';
                		   if( !empty( $area_id) ) {
                    		     update_option('_homey_listing_area_' . $area_id[0], $homey_meta);
                		   }
            			}

				// cancellation policy and max and min nights
				if( isset($item["terms"]["cancellation"]) ){ 
				  update_post_meta( $listing_id, $prefix.'cancellation_policy', $item["terms"]["cancellation"] );
				}
				if( isset($item["terms"]["minNights"]) ){ 
                		  update_post_meta( $listing_id, $prefix.'min_book_days', $item["terms"]["minNights"] );
				}
				if( isset($item["terms"]["maxNights"]) ){ 
                		  update_post_meta( $listing_id, $prefix.'max_book_days', $item["terms"]["maxNights"] );
				}

				// default checkout, checkin time
				if( isset($item["defaultCheckInTime"]) ){
                		  update_post_meta( $listing_id, $prefix.'checkin_after', $item["defaultCheckInTime"] );
				}
				if( isset($item["defaultCheckOutTime"]) ){
                		  update_post_meta( $listing_id, $prefix.'checkout_before', $item["defaultCheckOutTime"] );
				}

				// all other allowances
				if( isset($item["publicDescription"]["guestControls"]) ){
				  if(isset($item["publicDescription"]["guestControls"]["allowsSmoking"])){ 
				    if($item["publicDescription"]["guestControls"]["allowsSmoking"] == false){
				     update_post_meta( $listing_id, $prefix.'smoke', 0 );
				    } else{
               			     update_post_meta( $listing_id, $prefix.'smoke', $item["publicDescription"]["guestControls"]["allowsSmoking"] );
				    }
				  }
				  if(isset($item["publicDescription"]["guestControls"]["allowsChildren"])){ 
				    if($item["publicDescription"]["guestControls"]["allowsChildren"] == false){
				     update_post_meta( $listing_id, $prefix.'children', 0 );
				    } else{
                		      update_post_meta( $listing_id, $prefix.'children', $item["publicDescription"]["guestControls"]["allowsChildren"] );
				    }
				  }
				  if(isset($item["publicDescription"]["guestControls"]["allowsPets"])){ 
				    if($item["publicDescription"]["guestControls"]["allowsPets"] == false){
				     update_post_meta( $listing_id, $prefix.'pets', 0 );
				    } else{
                		     update_post_meta( $listing_id, $prefix.'pets', $item["publicDescription"]["guestControls"]["allowsPets"] );
				    }
				  }
				  if(isset($item["publicDescription"]["guestControls"]["allowsEvents"])){ 
				    if($item["publicDescription"]["guestControls"]["allowsEvents"] == false){
				     update_post_meta( $listing_id, $prefix.'party', 0 );
				    } else{
               			     update_post_meta( $listing_id, $prefix.'party', $item["publicDescription"]["guestControls"]["allowsEvents"] );
				    }
				  }
				}
				if( isset($item["publicDescription"]["houseRules"]) ){
                		  update_post_meta( $listing_id, $prefix.'additional_rules', $item["publicDescription"]["houseRules"] );
				}

				// opening hours and featured
		                update_post_meta( $listing_id, $prefix.'mon_fri_closed', 0 );	
		                update_post_meta( $listing_id, $prefix.'sat_closed', 0 );
                                update_post_meta( $listing_id, $prefix.'sun_closed', 0 );
                		update_post_meta( $listing_id, 'homey_featured', 0 );

				// map params
				if( ( isset($item["address"]["lat"]) && !empty($item["address"]["lat"]) ) 
					&& (  isset($item["address"]["lng"]) && !empty($item["address"]["lng"])  ) ) {
				   $lat = $item["address"]["lat"]; $lng = $item["address"]["lng"];
                		   $lat_lng = $lat.','.$lng;
                		   update_post_meta( $listing_id, $prefix.'geolocation_lat', $lat );
                		   update_post_meta( $listing_id, $prefix.'geolocation_long', $lng );
                		   update_post_meta( $listing_id, $prefix.'listing_location', $lat_lng );
                		   update_post_meta( $listing_id, $prefix.'listing_map', '1' );   
                		   update_post_meta( $listing_id, $prefix.'show_map', '1' );               
                		   homey_insert_lat_long($lat, $lng, $listing_id); 
				}
					
				// set room type and listing type
				if( isset($item["roomType"]) ){
				   wp_set_object_terms( $listing_id, $item["roomType"], 'room_type' );
				}
				if( isset($item["propertyType"]) ){
               			   wp_set_object_terms( $listing_id, $item["propertyType"], 'listing_type' );
				}

				// Amenities
            			if( isset( $item["amenities"] ) && !empty( $item["amenities"] ) ) { 
				   $amenities_array = array(); 
				   foreach( $item["amenities"] as $amenity ){ $amenities_array[] = $amenity; }
                		   wp_set_object_terms( $listing_id, $amenities_array, 'listing_amenity' );
            			}
				echo '*** Listing Generated For Id: '.$item["_id"].' Done. Started Images.<br/>'; flush(); ob_flush();

				//upload featured image
				if(isset($item['picture']) && !empty($item['picture'])){ $featured = ''; 
			     	  if(isset($item['picture']["large"])){ $featured = $item['picture']["large"]; }
			     	  elseif(isset($item['picture']["regular"])){ $featured = $item['picture']["regular"]; }
			     	  else{ $featured = $item['picture']["thumbnail"]; } 
		    	     	  $imgfile = basename($featured); 
				  if(strstr($featured,'https:')){ $remoteImageUrl = $featured; }
				  else{ $remoteImageUrl = 'https:'.$featured; } var_dump($remoteImageUrl); exit;
			     	  $upload = wp_upload_bits($imgfile , null, file_get_contents($remoteImageUrl, FILE_USE_INCLUDE_PATH));
			     	  $image_file = $upload['file'];  $file_type = wp_check_filetype($image_file, null);
			     	  $attachment = array(
					'post_mime_type' => $file_type['type'],
					'post_title' => sanitize_file_name($imgfile),
					'post_content' => '',
					'post_status' => 'inherit'
			     	  );
			     	  $attachment_id = wp_insert_attachment( $attachment, $image_file );
			     	  $attachment_data = wp_generate_attachment_metadata( $attachment_id, $image_file);
			     	  wp_update_attachment_metadata( $attachment_id, $attachment_data );
 				  update_post_meta( $listing_id, '_thumbnail_id', $attachment_id );
 				  update_post_meta( $listing_id, 'homey_homeslider', 'yes' );
 				  update_post_meta( $listing_id, 'homey_slider_image', $attachment_id );
			   	}
				echo '*** Featured Image For Id: '.$item["_id"].' Done. Other Images.<br/>'; flush(); ob_flush();
				// upload other images
				if( isset($item['pictures']) && !empty($item['pictures']) ){ 
				  foreach($item['pictures'] as $picture){ $selimage=''; 
			     	     if(isset($picture["large"])){ $selimage = $picture["large"]; }
			     	     elseif(isset($picture["regular"])){ $selimage = $picture["regular"]; }
			     	     else{ $selimage = $picture["thumbnail"]; } 
		    	     	     $imgfile = basename($selimage); 
				     if(strstr($featured,'https:')){ $remoteImageUrl = $selimage; }
				     else{ $remoteImageUrl = 'https:'.$selimage; } 
			     	     $upload = wp_upload_bits($imgfile , null, file_get_contents($remoteImageUrl, FILE_USE_INCLUDE_PATH));
			     	     $image_file = $upload['file'];  $file_type = wp_check_filetype($image_file, null);
			     	     $attachment = array(
					 'post_mime_type' => $file_type['type'],
					 'post_title' => sanitize_file_name($imgfile),
					 'post_content' => '',
					 'post_status' => 'inherit'
			     	     );
			     	     $attachment_id = wp_insert_attachment( $attachment, $image_file );
			     	     $attachment_data = wp_generate_attachment_metadata( $attachment_id, $image_file);
			     	     wp_update_attachment_metadata( $attachment_id, $attachment_data );
				     add_post_meta($listing_id, 'homey_listing_images', $attachment_id);
	 			  }
				}
				$e_time = microtime(true); $elapsed = $e_time - $s_time;
				echo '*** Gallery Images For Id: '.$item["_id"].' Done.<br/>'; flush(); ob_flush();
				echo '<div class="notice notice-success is-dismissible">';
				echo '<p>'._e('Listing Created From Guesty, Property ID: '.$item["_id"].' Time: '.$elapsed, 'homey-child' ).'</p>';
				echo '</div>';

				break;
			} // end foreach

		   } // if isset rowids 
		   //redirect back to the listing page
		   echo '<meta http-equiv="refresh" content="5;url='.admin_url('admin.php?page=list-guesty-listings').'">';
        	} // end current action == import
    	}

	function fetch_guesty_data_from_api($paged = 1, $ids=array(), $search=''){  
		//generate a GET request for searching listings
  		//$data = array('q'=>null,'type'=>'listings','limit'=>25,'skip'=>0);
  		//$searchUrl = $this->mainurl.'/search';
  		$searchUrl = $this->mainurl.'/listings';
  		$base64enc = base64_encode($this->apikey.':'.$this->secretkey);
  		$authorization = "Basic $base64enc";  
		if($paged == 1){ $limit = 25; $skip = 0; } 
		else { $limit = 25; $skip = $limit * ($paged - 1); }

		if(empty($ids) && empty($search)){
		 if($paged == 1){ $searchUrlMod = $searchUrl; }
		 if($paged > 1){ $searchUrlMod = $searchUrl.'?'.'skip='.$skip; }
		 $fields = "_id address instantBookable picture type active prices propertyType title";
  		 $data = array('limit' => $limit, 'skip' => $skip); 
		}elseif(!empty($ids) && empty($search)){
		 $idsString = ''; $cnt=1; 
		 foreach($ids as $id){ 
		   if($cnt == count($ids)){ $idsString .= "$id"; }
		   else{ $idsString .= "$id".','; }
		   $cnt++;
		 }
		 $searchUrlMod = $searchUrl.'?ids='.$idsString;
		 $data = array('ids' => $idsString, 'limit' => $limit, 'skip' => $skip); 
		}elseif(empty($ids) && !empty($search)){ 
		  //$searchUrl = $searchUrl.'?q='.urlencode($search); echo $searchUrl;
		  $data = array('q' => $search, 'limit' => $limit, 'skip' => $skip); 
		  if($paged == 1){ $searchUrlMod = $searchUrl.'?q='.urlencode($search); }
		  if($paged > 1){ $searchUrlMod = $searchUrl.'?q='.urlencode($search).'&skip='.$skip; }
		}
  		$data_string = json_encode($data); //var_dump($data_string); //exit;
		//echo $searchUrlMod; //exit; //$s_time = microtime(true);
  		$ch = curl_init($searchUrlMod); 
  		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			"WWW-Authenticate: Basic",
    			"Authorization: $authorization",
    			'Content-Type: application/json',
    			'Content-Length: '.strlen($data_string),
  		));
  		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  		$result = curl_exec($ch);  
  		if(curl_error($ch)) { 
		   echo "==============================================================<br/>";
		   echo '** Error : Unable To Connect To Guesty API -'; curl_error($ch);
		   echo "<br/>============================================================<br/>"; 
		   curl_close($ch); exit;
		}
		curl_close($ch);
  		//echo 'Listings:'; var_dump($result); exit; 
  		//$e_time = microtime(true); $elapsed = $e_time - $s_time; 
  		//echo 'Time: '.round($elapsed,3).' secs'; echo '<br/><br/>';
		return $result; 
	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}

/** guesty bookings list table */
 class Guesty_Booking_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_booking', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_bookings', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}

/** guesty views list table */
 class Guesty_Views_List_Table extends WP_List_Table { 
	private $demo_columns = array(  'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
					'id' => 'ID', 'title' => 'Title', 'description' => 'Description', 
					'year' => 'Year', 'director' => 'Director', 'rating' => 'Rating' ); 
	private $items_per_page = 3;

	public function __construct() { 
	   parent::__construct( [
		'singular' => __( 'guesty_view', 'homey_child' ), //singular name 
		'plural'   => __( 'guesty_views', 'homey_child' ), //plural name 
		'ajax'     => false,
	   ]);
	}

	function get_columns(){
    		$columns = $this->demo_columns;
	        return $columns;
	}

	public function column_default($item, $column_name) {
    		return '<em>'.$item[$column_name].'</em>';
	}

	function column_cb($item) {
        	return sprintf( '<input type="checkbox" name="rowids[]" value="%s" />', $item['id'] );
        }

	function prepare_items() { 
		/** setup top and bottom headers */
		$headers = array();
        	$columns = $this->get_columns(); 
		array_push($headers, $columns);

		// assign hidden and sortable columns
		if($this->get_hidden_columns() != false){
		  $hidden = $this->get_hidden_columns();
		}
		if($this->get_sortable_columns() != false){
		  $sortable = $this->get_sortable_columns();
		}

		if(isset($hidden) && !empty($hidden)){ array_push($headers, $hidden); }
		if(isset($sortable) && !empty($sortable)){ array_push($headers, $sortable); }
        	$this->_column_headers = $headers;

		/**call bulk actions if available*/
		$this->process_bulk_action();
		
		if(!empty($_REQUEST['s'])){
		   $data = array();
		   /** setup data and pagination */
        	   $fulldata = $this->demo_data_wp_list_table(); 
		   foreach($fulldata  as $item){ 
		      if(strstr($item['title'], $_REQUEST['s'])){			
			 array_push($data, $item);
		      }
		   }
        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 

		} else {		
		   /** setup data and pagination */
        	   $data = $this->demo_data_wp_list_table(); 
        	   usort( $data, array( &$this, 'sort_data' ) );

        	   $perPage = $this->items_per_page;
        	   $currentPage = $this->get_pagenum();
        	   $totalItems = count($data); 
		}

        	$this->set_pagination_args( array(
            		'total_items' => $totalItems,
            		'per_page'    => $perPage
        	) );
		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
        	$this->items = $data;
    	}

	/*protected function get_views() { //var_dump($_REQUEST);  
	      	$custom_filter_links = array(
            		"new" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'homey_child'),
            		"pending" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'homey_child'),
            		"completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'guesty_accounts','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'homey_child'),
      		);
      		return $custom_filter_links;
    	}*/

	/**
     	 * Return array of bult actions if has any
     	 * @return array
     	 */
    	function get_bulk_actions() {
        	$actions = array(
            		'import' => 'Import',
            		'download' => 'Download'
        	);
        	return $actions;
    	}

  	function process_bulk_action() { 
        	global $wpdb;

        	if ('delete' === $this->current_action()) {
            		foreach ($_GET['wp_list_event'] as $event) {
                		// $wpdb->delete($wpdb->prefix.'atb_events', array('id' => $event));
            		}
        	}

        	if ('import' === $this->current_action()) {
			echo 'have hit import action'; die;
            		add_action( 'admin_notices', 'sample_admin_notice__success' );
        	}
    	}

	/**
 	 * Get the table demo data
 	 * @return Array
 	 */
	function demo_data_wp_list_table(){
        	$data = array();
        	$data[] = array(
                    'id'          => 1,
                    'title'       => 'The Shawshank Redemption',
                    'description' => 'Two imprisoned men bond over a number of years, finding solace and eventual redemption through acts of common decency.',
                    'year'        => '1994',
                    'director'    => 'Frank Darabont',
                    'rating'      => '9.3' );
	        $data[] = array(
                    'id'          => 2,
                    'title'       => 'The Godfather',
                    'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
                    'year'        => '1972',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.2' );
	        $data[] = array(
                    'id'          => 3,
                    'title'       => 'The Godfather: Part II',
                    'description' => 'The early life and career of Vito Corleone in 1920s New York is portrayed while his son, Michael, expands and tightens his grip on his crime syndicate stretching from Lake Tahoe, Nevada to pre-revolution 1958 Cuba.',
                    'year'        => '1974',
                    'director'    => 'Francis Ford Coppola',
                    'rating'      => '9.0' );
		$data[] = array(
                    'id'          => 4,
                    'title'       => 'Pulp Fiction',
                    'description' => 'The lives of two mob hit men, a boxer, a gangster\'s wife, and a pair of diner bandits intertwine in four tales of violence and redemption.',
                    'year'        => '1994',
                    'director'    => 'Quentin Tarantino',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 5,
                    'title'       => 'The Good, the Bad and the Ugly',
                    'description' => 'A bounty hunting scam joins two men in an uneasy alliance against a third in a race to find a fortune in gold buried in a remote cemetery.',
                    'year'        => '1966',
                    'director'    => 'Sergio Leone',
                    'rating'      => '9.0' );
	        $data[] = array(
                    'id'          => 6,
                    'title'       => 'The Dark Knight',
                    'description' => 'When Batman, Gordon and Harvey Dent launch an assault on the mob, they let the clown out of the box, the Joker, bent on turning Gotham on itself and bringing any heroes down to his level.',
                    'year'        => '2008',
                    'director'    => 'Christopher Nolan',
                    'rating'      => '9.0' );
	       $data[] = array(
                    'id'          => 7,
                    'title'       => '12 Angry Men',
                    'description' => 'A dissenting juror in a murder trial slowly manages to convince the others that the case is not as obviously clear as it seemed in court.',
                    'year'        => '1957',
                    'director'    => 'Sidney Lumet',
                    'rating'      => '8.9' );
	        $data[] = array(
                    'id'          => 8,
                    'title'       => 'Schindler\'s List',
                    'description' => 'In Poland during World War II, Oskar Schindler gradually becomes concerned for his Jewish workforce after witnessing their persecution by the Nazis.',
                    'year'        => '1993',
                    'director'    => 'Steven Spielberg',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 9,
                    'title'       => 'The Lord of the Rings: The Return of the King',
                    'description' => 'Gandalf and Aragorn lead the World of Men against Sauron\'s army to draw his gaze from Frodo and Sam as they approach Mount Doom with the One Ring.',
                    'year'        => '2003',
                    'director'    => 'Peter Jackson',
                    'rating'      => '8.9' );
		$data[] = array(
                    'id'          => 10,
                    'title'       => 'Fight Club',
                    'description' => 'An insomniac office worker looking for a way to change his life crosses paths with a devil-may-care soap maker and they form an underground fight club that evolves into something much, much more...',
                    'year'        => '1999',
                    'director'    => 'David Fincher',
                    'rating'      => '8.8' );
        	return $data;
	}

}


} // end if admin

