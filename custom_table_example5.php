<?php
/*
Plugin Name: Custom table example
Description: example plugin to demonstrate wordpress capatabilities
Plugin URI: http://mac-blog.org.ua/
Author URI: http://mac-blog.org.ua/
Author: Marchenko Alexandr
License: Public Domain
Version: 1.1
Modification: 25/10/2018.
*/

/**
 * $custom_table_example_db_version - holds current database version
 * and used on plugin update to sync database tables
 */
global $custom_table_example_db_version;
$custom_table_example_db_version = '1.1'; //version changed from 1.0 to 1.1

/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */
function custom_table_example_install()
{
    global $wpdb;
    global $custom_table_example_db_version;
    // save current database version for later use (on upgrade)
    add_option('custom_table_example_db_version', $custom_table_example_db_version);

    $installed_ver = get_option('custom_table_example_db_version');
    if ($installed_ver != $custom_table_example_db_version) {
        // notice that we are updating option, rather than adding it
        update_option('custom_table_example_db_version', $custom_table_example_db_version);
    }
}

register_activation_hook(__FILE__, 'custom_table_example_install');

/**
 * Trick to update plugin database, see docs
 */
function custom_table_example_update_db_check()
{
    global $custom_table_example_db_version;
    if (get_site_option('custom_table_example_db_version') != $custom_table_example_db_version) {
        custom_table_example_install();
    }
}

add_action('plugins_loaded', 'custom_table_example_update_db_check');

/**
 * Defining Custom Table List
 * ============================================================================
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
class Custom_Table_Example_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;
        parent::__construct(array( 'singular' => 'application', 'plural' => 'applications', ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return '<em>'. $item[$column_name] .'</em>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_id($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
        $actions = array(
            'view' => sprintf('<a href="?page=applicant_details&id=%s">%s</a>', $item['id'], __('View', 'custom_table_example')),
            //'edit' => sprintf('<a href="?page=persons_form&id=%s">%s</a>', $item['id'], __('Edit', 'custom_table_example')),
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'custom_table_example')),
        );
        return sprintf('%s %s', $item['id'], $this->row_actions($actions));
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
	    'id' => __('ID', 'custom_table_example'),
            //'userID' => __('UID', 'custom_table_example'),
            'entry_date' => __('Entry Date', 'custom_table_example'),
	    'ins_party_nric' => __('NRIC Of Appln', 'custom_table_example'),
	    'ins_party_nm' => __('Name Of Appln', 'custom_table_example'),
	    //'eff_date' => __('DateEffective', 'custom_table_example'),
            //'exp_date' => __('DateExpiry', 'custom_table_example'),
            'ins_person' => __('Person Name', 'custom_table_example'),	    
	    'country' => __('Country', 'custom_table_example'),
            'contact_person' => __('Contact', 'custom_table_example'),
            'ph_no' => __('Phone', 'custom_table_example'),
            'email' => __('E-mail', 'custom_table_example'),
	    'ins_class' => __('Plan Selected', 'custom_table_example'),
            'Total_premium_amount' => __('Amount', 'custom_table_example'),
            'payment_approved' => __('Payment Status', 'custom_table_example'),
            'export_type' => __('Export Status', 'custom_table_example'), 
	    'guarantee_no' => __('Policy No', 'custom_table_example'),
        );
        return $columns;
    }


    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
	    'id' => array('id', true),
            'userID' => array('userID', true),
            'entry_date' => array('entry_date', true),
        );
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
	    'download' => 'Download'
        );
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
	$table_name = 'ins_policy_applicants';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }

	if ('download' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
		$items = $wpdb->get_results("SELECT * FROM $table_name WHERE id IN($ids)", ARRAY_A);
		ob_end_clean();
		$out = fopen('php://output', 'w'); 
		$filename="web.txt"; //$filename="web.csv"; 
		$field_names = array(); $fields_nos = array(); 
		//$filename = wp_upload_dir()['path'].'/web.csv'; //echo $filename;
		//$out = fopen($filename, 'w');
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$filename.";" );
		header("Content-Transfer-Encoding: binary");
		//header('Content-Type: application/csv; charset=UTF-8');
		//header('Content-Disposition: attachment; filename="'.$filename.'";'); 
		//print the keys to output 
		$row = $items[0]; $keys = array_keys($row); 
		foreach($keys as $key=>$value){ 
		   if($value == 'id'){ array_push($field_names,'Sno'); array_push($fields_nos,$key); }
		   if($value == 'entry_date'){ array_push($field_names,'Entry_Dt'); array_push($fields_nos, $key); }
		   if($value == 'bill_to'){ array_push($field_names,'Bill_To'); array_push($fields_nos, $key); }
		   if($value == 'ins_class'){ array_push($field_names,'Ins_C'); array_push($fields_nos, $key); }
		   if($value == 'er_cpfno'){ array_push($field_names,'ER_CPFN'); array_push($fields_nos, $key); }
		   if($value == 'wp_no'){ array_push($field_names,'WP_No'); array_push($fields_nos, $key); }
		   if($value == 'eff_date'){ array_push($field_names,'Eff_Dt'); array_push($fields_nos, $key); }
		   if($value == 'exp_date'){ array_push($field_names,'Exp_Dt'); array_push($fields_nos, $key); }
		   if($value == 'pai_date'){ array_push($field_names,'PAI_Dt'); array_push($fields_nos, $key); }
		   if($value == 'title'){ array_push($field_names,'Title'); array_push($fields_nos, $key); }
		   if($value == 'ins_party_nm'){ array_push($field_names,'Ins_Party_Nm'); array_push($fields_nos, $key); }
		   if($value == 'ins_party_nric'){ array_push($field_names,'Ins_Party_Nric'); array_push($fields_nos, $key); }
		   if($value == 'addr1'){ array_push($field_names,'Addr1'); array_push($fields_nos, $key); }
		   if($value == 'addr2'){ array_push($field_names,'Addr2'); array_push($fields_nos, $key); }
		   if($value == 'country'){ array_push($field_names,'Country'); array_push($fields_nos, $key); }
		   if($value == 'postal'){ array_push($field_names,'Postal'); array_push($fields_nos, $key); }
		   if($value == 'contact_person'){ array_push($field_names,'Contact_Person'); array_push($fields_nos, $key); }
		   if($value == 'ph_no'){ array_push($field_names,'Ph_No'); array_push($fields_nos, $key); }
		   if($value == 'fax_no'){ array_push($field_names,'Fax_No'); array_push($fields_nos, $key); }
		   if($value == 'ins_person'){ array_push($field_names,'Ins_Person'); array_push($fields_nos, $key); }
		   if($value == 'dob'){ array_push($field_names,'DOB'); array_push($fields_nos, $key); }
		   if($value == 'nationality'){ array_push($field_names,'Nationality'); array_push($fields_nos, $key); }
		   if($value == 'passport'){ array_push($field_names,'Passport'); array_push($fields_nos, $key); }
		}
		/*fputcsv($out, $field_names, "\t"); //fwrite($out, "\r\n"); 
		//print the respective items to output
 		foreach ($items as $row) { $field_values = array();
		   $counter = 0; foreach($row as $item){ 
		      if(in_array($counter, $fields_nos)){ array_push($field_values, $item); }
		      $counter++;
		   }
		   fputcsv($out, $field_values, "\t"); //fwrite($out, "\r\n"); 
		}*/
		/*$counter = 0; foreach($field_names as $name){ 
		  if($counter == (sizeof($field_names)-1)){ fputs($out, $name."\r\n"); }
		  else{ fputs($out, $name."\t"); }
		  $counter++;
		}
 		foreach ($items as $row) { $field_values = array();  
		   $counter = 0; foreach($row as $item){ 
			if(in_array($counter, $fields_nos)){ array_push($field_values, $item); }
			$counter++;
		   }
		   $cnt = 0; foreach($field_values as $value){  
		      if($cnt == (sizeof($field_values)-1)){ fputs($out, $value."\r\n"); $cnt++; }
		      else{ fputs($out, $value."\t"); $cnt++; }
		   }
		}*/
		/*$counter = 0; foreach($field_names as $name){ 
		  if($counter == (sizeof($field_names)-1)){ fputs($out, $name."\r\n"); }
		  else{ fputs($out, $name."    "); }
		  $counter++;
		}
 		foreach ($items as $row) { $field_values = array();  
		   $counter = 0; foreach($row as $item){ 
			if(in_array($counter, $fields_nos)){ array_push($field_values, $item); }
			$counter++;
		   }
		   $cnt = 0; foreach($field_values as $value){  
		      if($cnt == (sizeof($field_values)-1)){ fputs($out, $value."\r\n"); $cnt++; }
		      else{ fputs($out, $value."    "); $cnt++; }
		   }
		}*/
		/* fixed length file generation */
		$counter = 0; foreach($field_names as $name){ 
		  if($counter == (sizeof($field_names)-1)){ fputs($out, (str_pad(substr($name,0,20),20)."\r\n")); }
		  else{ 
			if($counter == 10 ||  $counter == 12 || $counter == 13 || $counter == 16 || $counter == 19){
			  fputs($out, str_pad(substr($name,0,50),50)); 
			}
			else if($counter == 11 || $counter == 14 || $counter == 17 || $counter == 18 || $counter == 21){
			  fputs($out, str_pad(substr($name,0,20),20)); 
			}
			else if($counter == 4){ fputs($out, str_pad(substr($name,0,7),7)); }
			else if($counter == 3 || $counter == 9 ){ fputs($out, str_pad(substr($name,0,5),5)); }
			else{ fputs($out, str_pad(substr($name,0,10),10)); }
		  }
		  $counter++;
		}
 		foreach ($items as $row) { $field_values = array();  
		   $counter = 0; foreach($row as $item){ 
			if(in_array($counter, $fields_nos)){ array_push($field_values, $item); }
			$counter++;
		   }
		   $cnt = 0; foreach($field_values as $value){  
		      if($cnt == (sizeof($field_values)-1)){ fputs($out, (str_pad(substr($value,0,20),20)."\r\n")); $cnt++; }
		      else{ 
			   if($cnt == 10 ||  $cnt == 12 || $cnt == 13 || $cnt == 16 || $cnt == 19){
			      fputs($out, str_pad(substr($value,0,50),50)); 
			   }
			   else if($cnt == 11 || $cnt == 14 || $cnt == 17 || $cnt == 18 || $cnt == 21){
			      fputs($out, str_pad(substr($value,0,20),20)); 
			   }
			   else if($cnt == 4){ fputs($out, str_pad(substr($value,0,7),7)); }
			   else if($cnt == 3 || $cnt == 9){ fputs($out, str_pad(substr($value,0,5),5)); } 
			   else{ 
				if($cnt == 1 || $cnt == 6 || $cnt== 7 || $cnt == 8 || $cnt == 20){
				   $rplVal = str_replace('-','',$value); fputs($out, str_pad(substr($rplVal,0,10),10));
				} else { fputs($out, str_pad(substr($value,0,10),10)); }
			   }    
		      }
		      $cnt++;
		   }
		}
		fclose($out); exit();
            }
        }

    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items($search = NULL)
    {
        global $wpdb;
        //$table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
	$table_name = 'ins_policy_applicants';

        $per_page = 20; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? ($per_page * max(0, intval($_REQUEST['paged']) - 1)) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

	if($search == '' || $search == NULL ){
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

	} else {
	  // Trim Search Term
          $search = trim($search);

          $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".$table_name." WHERE `id` LIKE '%%%s%%'",$search));

          /* Notice how you can search multiple columns for your search term easily, and return one data set */
          $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `id` LIKE '%%%s%%'", $search), ARRAY_A);

	}

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }

} //end class - Custom_Table_Example_List_Table


/**
 * Admin page
 * =================================================================================
 * admin_menu hook implementation, will add pages to list persons and to add new one
 */
function custom_table_example_admin_menu()
{
    add_menu_page(__('Applications', 'custom_table_example'), __('Applications', 'custom_table_example'), 'activate_plugins', 'applications', 'custom_table_example_persons_page_handler');
    add_submenu_page('applications', __('Applications', 'custom_table_example'), __('Applications', 'custom_table_example'), 'activate_plugins', 'applications', 'custom_table_example_persons_page_handler');
    add_submenu_page('applications', __('Upload Data', 'custom_table_example'), __('Upload Data', 'custom_table_example'), 'activate_plugins', 'upload_form', 'custom_table_example_upload_form_page_handler');
    // add new will be described in next part
    //add_submenu_page('Applications', __('Add new', 'custom_table_example'), __('Add new', 'custom_table_example'), 'activate_plugins', 'persons_form', 'custom_table_example_persons_form_page_handler');
}

add_action('admin_menu', 'custom_table_example_admin_menu');

/**
 * List page handler
 * This function renders our custom table
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
function custom_table_example_persons_page_handler()
{
    global $wpdb;
    $table = new Custom_Table_Example_List_Table();
    if( isset($_REQUEST['s']) ){ $table->prepare_items($_REQUEST['s']);
    } else { $table->prepare_items(); }

    $message = '';

    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'custom_table_example'), count($_REQUEST['id'])) . '</p></div>';
    } 
    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Applications', 'custom_table_example')?> 
	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=upload_form');?>">
	    <?php _e('Upload Desktop Data', 'custom_table_example'); ?>
	</a>
	<!--<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=persons_form');?>">
		<?php _e('Add new', 'custom_table_example')?>
	</a>-->
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
	<?php $table->search_box('search', 'search_id'); ?>
        <?php $table->display() ?>
    </form>

</div>
<?php
}

/**
 * Form for uploding the document provided from desktop appln
 * ===========================================================
 */
function custom_table_example_upload_form_page_handler()
{
    global $wpdb;
    //$table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
    $table_name = 'ins_policy_applicants';
    $message = ''; $notice = '';

    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) { //var_dump($_FILES); exit;
      //generate error if any file missing or not uploaded
      if($_FILES['upload_web_stat']['error'] || $_FILES['upload_web_ibms']['error']){ 
	$errmessage = ''; 
	if($_FILES['upload_web_stat']['error']){
	  if($_FILES['upload_web_stat']['error'] == UPLOAD_ERR_INI_SIZE){
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
		$errmessage .= 'Error: Web Stat File Upload : Size of the uploaded file larger than specified in upload_max_filesize.'; 
		$errmessage .= '</h3>';
	  }
	  if($_FILES['upload_web_stat']['error'] == UPLOAD_ERR_PARTIAL){ 
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
		$errmessage .= 'Error: Web Stat File Upload : Partial File Have been received.'; 
		$errmessage .= '</h3>';
	  }
	  if($_FILES['upload_web_stat']['error'] == UPLOAD_ERR_NO_FILE){ 
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
		$errmessage .= 'Error: Web Stat File Upload : No File Uploaded.'; 
		$errmessage .= '</h3>';
	  }
	  if($_FILES['upload_web_stat']['error'] == UPLOAD_ERR_NO_TMP_DIR){ 
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">'; 
		$errmessage .= 'Error: Web Stat File Upload : No Temp Directory Present For Upload.'; 
		$errmessage .= '</h3>';		
	  }
	} else {
	  if($_FILES['upload_web_ibms']['error'] == UPLOAD_ERR_INI_SIZE){
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
		$errmessage .= 'Error: Web IBMS File Upload : Size of the uploaded file larger than specified in upload_max_filesize.'; 
		$errmessage .= '</h3>';
	  }
	  if($_FILES['upload_web_ibms']['error'] == UPLOAD_ERR_PARTIAL){ 
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
		$errmessage .= 'Error: Web IBMS File Upload : Partial File Have been received.'; 
		$errmessage .= '</h3>';
	  }
	  if($_FILES['upload_web_ibms']['error'] == UPLOAD_ERR_NO_FILE){ 
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
		$errmessage .= 'Error: Web IBMS File Upload : No File Uploaded.'; 
		$errmessage .= '</h3>';
	  }
	  if($_FILES['upload_web_ibms']['error'] == UPLOAD_ERR_NO_TMP_DIR){ 
		$errmessage .= '<h3 style="background:#f3123acc;color:#000;font-size:13px;">'; 
		$errmessage .= 'Error: Web IBMS File Upload : No Temp Directory Present For Upload.'; 
		$errmessage .= '</h3>';		
	  }
	}
	echo $errmessage; 
	echo '<a class="button" href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=upload_form').'">';
	echo _e('Back To Upload', 'custom_table_example'); echo '</a>';
	wp_die();
      }

      //on upload of the files do the necessary actions
      if($_FILES['upload_web_stat']['name'] && $_FILES['upload_web_ibms']['name']){ 
	 //check whether both files have same number of lines 
	 $linesInStatFile = sizeof(file($_FILES['upload_web_stat']['tmp_name'])); 
	 $linesInIBMSFile = sizeof(file($_FILES['upload_web_ibms']['tmp_name'])); 
	 if(($linesInStatFile-1) != $linesInIBMSFile){ 
	   $errmessage = '<h3 style="background:#f3123acc;color:#000;font-size:13px;">';
	   $errmessage .= 'Error: Web Stat & Web IBMS File do not have same number of records.'; 
	   $errmessage .= '</h3>';
	   echo $errmessage;
	   echo '<a class="button" href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=upload_form').'">';
	   echo _e('Back To Upload', 'custom_table_example'); echo '</a>';
	   wp_die();
	 }

	 //if both files have same lines, then process the data
    	 $webstat_tmp_file_name = $_FILES['upload_web_stat']['tmp_name']; 
    	 $webibms_tmp_file_name = $_FILES['upload_web_ibms']['tmp_name']; 
	 $webstatfp = fopen($webstat_tmp_file_name, 'r');
	 $webibmsfp = fopen($webibms_tmp_file_name, 'r');
	 while(true) {
	   if(($bufferwebstat = fgets($webstatfp)) === false){ break; }
	   if(($bufferwebibms = fgets($webibmsfp)) === false){ break; }
	   if(trim($bufferwebstat) == ''){ $bufferwebstat = fgets($webstatfp); }
	   //var_dump($bufferwebstat); var_dump($bufferwebibms); exit;

	   //get values from web stat file
	   $wstatsysID = trim(substr($bufferwebstat,0,7)); $otherID = trim(substr($bufferwebstat,7,18));
	   $wstatguarantee_no = trim(substr($bufferwebstat,25,21)); 
	   $wstatmaster_policy_no = trim(substr($bufferwebstat,46,12));
	   //get values from web ibms file 
     	   $sysID = trim(substr($bufferwebibms,0,10)); $entry_date = trim(substr($bufferwebibms,10,10)); 
	   $bill_to = trim(substr($bufferwebibms,20,10)); $ins_class = trim(substr($bufferwebibms,30,5)); 
	   $er_cpfno = trim(substr($bufferwebibms,35,9)); $wp_no = trim(substr($bufferwebibms,44,17));
	   $eff_date = trim(substr($bufferwebibms,61,10)); $exp_date = trim(substr($bufferwebibms,71,16)); 
	   $ins_party_nm = trim(substr($bufferwebibms,87,50)); $ins_party_nric = trim(substr($bufferwebibms,137,20));
	   $addr1 = trim(substr($bufferwebibms,157,50)); $addr2 = trim(substr($bufferwebibms,207,80));
	   $contact_person = trim(substr($bufferwebibms,287,50)); $ph_no = trim(substr($bufferwebibms,337,40));
	   $ins_person = trim(substr($bufferwebibms,377,50)); $dob = trim(substr($bufferwebibms,427,10));	
	   $nationality = trim(substr($bufferwebibms,437,20)); $passport = trim(substr($bufferwebibms,457,20));			
	   $guarantee_no = trim(substr($bufferwebibms,477,20)); $master_policy_no = trim(substr($bufferwebibms,497, 13)); 

	   /*echo 'SysId:'.$sysID.'<br/>'; echo 'EDate:'.$entry_date.'<br/>'; echo 'Bill To:'.$bill_to.'<br/>';
	   echo 'Ins Class:'.$ins_class.'<br/>'; echo 'ER_cpfno:'.$er_cpfno.'<br/>'; echo 'Wp_no:'.$wp_no.'<br/>';
	   echo 'EffDate:'.$eff_date.'<br/>'; echo 'Exp date:'.$exp_date.'<br/>'; echo 'Ins Party Nm:'.$ins_party_nm.'<br/>';
	   echo 'Ins Part Nric:'.$ins_party_nric.'<br/>'; echo 'Addr1:'.$addr1.'<br/>'; echo 'Addr2:'.$addr2.'<br/>';        
	   echo 'Contact Person:'.$contact_person.'<br/>'; echo 'Ph No:'.$ph_no.'<br/>'; echo 'Ins_person:'.$ins_person.'<br/>'; 
	   echo 'Dob:'.$dob.'<br/>'; echo 'Nationality:'.$nationality.'<br/>'; echo 'Passport:'.$passport.'<br/>';
	   echo 'Guarrantee No:'.$guarantee_no.'<br/>';  echo 'Master Policy No:'.$master_policy_no.'<br/>'; 
	   echo '--------------------------<br/>';*/

	   //do not process if records do not match
	   if(($guarantee_no != $wstatguarantee_no) || ($master_policy_no != $wstatmaster_policy_no)){
	     $errmessage = '<p style="background:#f3123acc;color:#000;font-size:13px;">';
	     $errmessage .= 'Error: Details In Record Do Not Match.<br/>';
	     $errmessage .= 'Guarantee No In Web Stat: '.$wstatguarantee_no.' ... Guarantee No In Web IBMS: '.$guarantee_no.'<br/>';
	     $errmessage .= 'Master Policy No In Web Stat: '.$wstatmaster_policy_no.' ... Master Policy No In Web IBMS: '.$master_policy_no.'<br/>';		 
	     $errmessage .= '</p>';
	     echo $errmessage;
	     echo '<a class="button" href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=upload_form').'">';
	     echo _e('Back To Upload', 'custom_table_example'); echo '</a>';
	     wp_die();
	   }

	   //match the records and enter details
	   $results_id = $wpdb->get_results("SELECT * FROM $table_name WHERE id = '$wstatsysID'", ARRAY_N); //var_dump($results_id);
	   if(count($results_id) == 1){  
	     $data = array('guarantee_no'=>$guarantee_no,'master_policy_no'=>$master_policy_no,
	                   'exp_date' => $exp_date, 'pai_date' => $exp_date, 'export_type'=>'completed'); 
             $where = array('id' => $wstatsysID);
	     $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);
	     if($results_id[0][0] != '') { $notice .= __('Item Updated: '.$results_id[0][0].'<br/>', 'custom_table_example'); }
	   } else {
	      $notice .= __('Error: Item Not Found In DB - ID : '.$wstatsysID.'<br/>', 'custom_table_example');	
	   }
	} //end while
     } // end if file upload	
   } //end if verify nonce
?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Upload Desktop Data', 'custom_table_example')?>
	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=applications');?>">
	<?php _e('back to list', 'custom_table_example')?></a>
    </h2>
    <?php if (!empty($notice)): ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php endif;?>
    <?php if (!empty($message)): ?><div id="message" class="updated"><p><?php echo $message ?></p></div><?php endif;?>

    <form id="upload_form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
		  <p><strong>Upload Web Stat Data : </strong><input name="upload_web_stat" id="upload_web_stat" type="file" accept=".csv, .txt, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /></p>
		  <p><strong>Upload Web IBMS Data : </strong><input name="upload_web_ibms" id="upload_web_ibms" type="file" accept=".csv, .txt, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" /></p>
		  <p><input class="add-new-h2" id="btnSubmit" type="submit" value="Upload Data To Web" /></p>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * Form for viewing personal details
 * ============================================================================
 *
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function custom_table_example_application_form_page_handler()
{
    global $wpdb;
    //$table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
    $table_name = 'ins_policy_applicants';

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'name' => '',
        'email' => '',
        'age' => null,
    );

    // here we are verifying does this request is post back and have correct nonce
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = custom_table_example_validate_person($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'custom_table_example');
                } else {
                    $notice = __('There was an error while saving item', 'custom_table_example');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'custom_table_example');
                } else {
                    $notice = __('There was an error while updating item', 'custom_table_example');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'custom_table_example');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('application_form_meta_box', 'Application data', 'custom_table_example_application_form_meta_box_handler', 'application', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Application', 'custom_table_example')?> 
	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=applicaions');?>">
		<?php _e('back to list', 'custom_table_example')?></a>
    </h2>
    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('person', 'normal', $item); ?>
                    <!--<input type="submit" value="<?php _e('Save', 'custom_table_example')?>" id="submit" class="button-primary" name="submit">-->
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function custom_table_example_application_form_meta_box_handler($item)
{
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('ID', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="appid" name="appid" type="text" style="width: 95%" value="<?php echo esc_attr($item['id'])?>"
                    size="50" class="code" placeholder="<?php _e('Your ID', 'custom_table_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                    size="50" class="code" placeholder="<?php _e('Your name', 'custom_table_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="email"><?php _e('E-Mail', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                    size="50" class="code" placeholder="<?php _e('Your E-Mail', 'custom_table_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="age"><?php _e('Age', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['age'])?>"
                    size="50" class="code" placeholder="<?php _e('Your age', 'custom_table_example')?>" required>
        </td>
    </tr>
    </tbody>
</table>
<?php
}


/**
 * Form for adding andor editing row
 * ============================================================================
 *
 * In this part you are going to add admin page for adding andor editing items
 * You cant put all form into this function, but in this example form will
 * be placed into meta box, and if you want you can split your form into
 * as many meta boxes as you want
 *
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
 */

/**
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function custom_table_example_persons_form_page_handler()
{
    global $wpdb;
    //$table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
    $table_name = 'ins_policy_applicants';

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id' => 0,
        'name' => '',
        'email' => '',
        'age' => null,
    );

    // here we are verifying does this request is post back and have correct nonce
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = custom_table_example_validate_person($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert($table_name, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Item was successfully saved', 'custom_table_example');
                } else {
                    $notice = __('There was an error while saving item', 'custom_table_example');
                }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) {
                    $message = __('Item was successfully updated', 'custom_table_example');
                } else {
                    $notice = __('There was an error while updating item', 'custom_table_example');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'custom_table_example');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('persons_form_meta_box', 'Person data', 'custom_table_example_persons_form_meta_box_handler', 'person', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Person', 'custom_table_example')?> 
	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=persons');?>">
		<?php _e('back to list', 'custom_table_example')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('person', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'custom_table_example')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function custom_table_example_persons_form_meta_box_handler($item)
{
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Name', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                    size="50" class="code" placeholder="<?php _e('Your name', 'custom_table_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="email"><?php _e('E-Mail', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                    size="50" class="code" placeholder="<?php _e('Your E-Mail', 'custom_table_example')?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="age"><?php _e('Age', 'custom_table_example')?></label>
        </th>
        <td>
            <input id="age" name="age" type="number" style="width: 95%" value="<?php echo esc_attr($item['age'])?>"
                    size="50" class="code" placeholder="<?php _e('Your age', 'custom_table_example')?>" required>
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function custom_table_example_validate_person($item)
{
    $messages = array();

    if (empty($item['name'])) $messages[] = __('Name is required', 'custom_table_example');
    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('E-Mail is in wrong format', 'custom_table_example');
    if (!ctype_digit($item['age'])) $messages[] = __('Age in wrong format', 'custom_table_example');
    //if(!empty($item['age']) && !absint(intval($item['age'])))  $messages[] = __('Age can not be less than zero');
    //if(!empty($item['age']) && !preg_match('/[0-9]+/', $item['age'])) $messages[] = __('Age must be number');
    //...

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

/**
 * Do not forget about translating your plugin, use __('english string', 'your_uniq_plugin_name') to retrieve translated string
 * and _e('english string', 'your_uniq_plugin_name') to echo it
 * in this example plugin your_uniq_plugin_name == custom_table_example
 *
 * to create translation file, use poedit FileNew catalog...
 * Fill name of project, add "." to path (ENSURE that it was added - must be in list)
 * and on last tab add "__" and "_e"
 *
 * Name your file like this: [my_plugin]-[ru_RU].po
 *
 * http://codex.wordpress.org/Writing_a_Plugin#Internationalizing_Your_Plugin
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 */
function custom_table_example_languages()
{
    load_plugin_textdomain('custom_table_example', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'custom_table_example_languages');
