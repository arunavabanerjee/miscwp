<?php
/**
 * Plugin Name: Custom table example
 * Description: example plugin to demonstrate wordpress capatabilities
 * Plugin URI: http://mac-blog.org.ua/
 * Author URI: http://mac-blog.org.ua/
 * Author: Marchenko Alexandr
 * License: Public Domain
 * Version: 1.1
 * Modification: 25/10/2018.
 */
use setasign\Fpdi\Fpdi;
require_once( __DIR__.'/lib/fpdf181/fpdf.php');
require_once( __DIR__.'/lib/fpdi211/src/autoload.php');

/**
 * $custom_table_example_db_version - holds current database version
 * and used on plugin update to sync database tables
 */
global $custom_table_example_db_version;
$custom_table_example_db_version = '1.1'; // version changed from 1.0 to 1.1

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
     * [OPTIONAL] this is example, how to render custom filters on top of the table,
     * 
     * @return links for custom filter
     */
    protected function get_views() { //var_dump($_REQUEST);  
      $custom_filter_links = array(
        "new"       => __("<a href='".esc_url(add_query_arg(array('page'=>'applications','s'=>'new'), admin_url('/admin.php')))."'>New</a>",'custom_table_example'),
        "pending"   => __("<a href='".esc_url(add_query_arg(array('page'=>'applications','s'=>'pending'), admin_url('/admin.php')))."'>Pending</a>",'custom_table_example'),
        "completed" => __("<a href='".esc_url(add_query_arg(array('page'=>'applications','s'=>'completed'), admin_url('/admin.php')))."'>Completed</a>",'custom_table_example'),
      );
      return $custom_filter_links;
    }

    /**
     * [OPTIONAL] places filter at the top and bottom of the table,
     * 
     * @param $which - position whether top or bottom
     * @return HTML
     */
    protected function extra_tablenav( $which ) {
        global $wpdb, $tablename; 
	    $table_name = 'ins_policy_applicants';
	    $move_on_url = '&s=';
    	if ( $which == "top" ){ ?>
        <div class="alignleft actions bulkactions">
	    <?php if( $_REQUEST['s'] != '' &&  $_REQUEST['s'] != 'Work_Injury' ) { ?>
	      <?php if( $_REQUEST['s'] != 'completed' ) { ?>
          <?php 
	       if(strstr($_REQUEST['s'],'Maid_Embassy')){
             $total_items_n = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'S%' AND `export_type` = 'new' AND payment_approved=1");
	         $total_items_p = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'S%' AND `export_type` = 'pending'");
           }
	       if(strstr($_REQUEST['s'],'Foreign_Worker')){
             $total_items_n = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' AND `export_type` = 'new' AND payment_approved=1");
	         $total_items_p = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' AND `export_type` = 'pending'");
           }
	       if(strstr($_REQUEST['s'],'Work_Injury')){
             $total_items_n = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' AND `export_type` = 'new' AND payment_approved=1");
	         $total_items_p = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' AND `export_type` = 'pending'");
           }
	       if(strstr($_REQUEST['s'],'new')){
             $total_items_n = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `export_type` = 'new' AND payment_approved=1");
	         $total_items_p = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `export_type` = 'pending'");
	       } 
	       if(strstr($_REQUEST['s'],'pending')){
             $total_items_n = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `export_type` = 'new' AND payment_approved=1");
	         $total_items_p = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `export_type` = 'pending'");
	       }    
	   ?>
	   <button class="button" id='bulkactionbtnExpN' name='bulkactionbtnExpN' value='export_new' <?php if($total_items_n == 0){ echo "disabled='disabled'"; } ?>>Export New (<?php echo $total_items_n; ?>)</button>
	   <button class="button" id='bulkactionbtnExpP' name='bulkactionbtnExpP' value='export_pending' <?php if($total_items_p == 0){ echo "disabled='disabled'"; } ?>>Export Pending (<?php echo $total_items_p; ?>)</button> 
	   <?php }} ?>
        <?php /*$insclasses = $wpdb->get_results('select distinct ins_class from '.$table_name.' where ins_class in ("FWMI","S5A","S5B","S5C","S5D","S5AP","S5BP","S5CP","S5DP","S2A","S2D","WCS") order by 1 asc', ARRAY_A);*/
		  //$insclasses = array('Maid_Embassy', 'Foreign_Worker', 'Work_Injury');
		  /*$insclasses = array('Maid_Embassy'=>'Maid (Maid & Philippine Embassy)', 'Foreign_Worker'=>FWMI, 'Work_Injury' => WIC);
		  if( $insclasses ){ ?>
            <select id="inscl-filter" name="inscl-filter">
                <option value="" selected="selected">Filter by Class</option>
                <?php foreach( $insclasses as $key => $insclass ){ $selected = ''; 
		  		 /*if($insclass['ins_class'] == ''){ continue; }  
                  if($_REQUEST['s'] == $insclass['ins_class']){ $selected = ' selected = "selected"'; ?>
                    <option value="<?php echo $move_on_url .$insclass['ins_class']; ?>" <?php echo $selected; ?>><?php echo $insclass['ins_class']; ?></option>
                <?php } else { ?>
                    <option value="<?php echo $move_on_url .$insclass['ins_class']; ?>"><?php echo $insclass['ins_class']; ?></option>
				<?php } * ?>
				<?php if($_REQUEST['s'] == $insclass){ $selected = ' selected = "selected"'; ?>
                    <option value="<?php echo $move_on_url .$key; ?>" <?php echo $selected; ?>><?php echo $insclass; ?></option>
                <?php } else { ?>
                    <option value="<?php echo $move_on_url .$key; ?>"><?php echo $insclass; ?></option>
				<?php } ?>
			   <?php } //end foreach ?>
            </select>
         <?php } */ ?>
        </div>
        <?php /* ?><script>
        jQuery("#inscl-filter").change(function(){ 
   	      var input = this; //console.log(input.value); 
	      var selvalue = input.value;
	      var url = "<?php echo admin_url('admin.php?page='.$_REQUEST['page']); ?>"; 
	      url += selvalue; //alert(url); console.log(url);
	      window.location.assign(url);
	    });
	    </script><?php */ ?>
        <?php } //endif top ?>
	    <?php //places at the bottom 
          if ( $which == "bottom" ){ } ?>
	<?php }
		
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
     * [REQUIRED] payment approved column renderer
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_payment_approved($item)
    {
        if($item['payment_approved'] == 0){ //return '0';
            return '<img src="'.get_template_directory_uri().'/images/download-red-512.jpg" alt="Failed Image" width="12" height="12" />';
        } else{ //return '1';
            return '<img src="'.get_template_directory_uri().'/images/tick-green-512.png" alt="Success Image" width="12" height="12" />';
        }
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
            'Total_premium_amt' => __('Amount', 'custom_table_example'),
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
        //$table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
	    $table_name = 'ins_policy_applicants';

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)){ $ids = implode(',', $ids); }
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
	    if ('download' === $this->current_action()) {
          $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
          if (is_array($ids)){ $ids = implode(',', $ids); }
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
			//will not print completed items
		   if($row['export_type'] == 'completed'){ continue; } 
		   //update the export type of the row to pending
		   $where = array('id' => $row['id']); $data = array('export_type' => 'pending'); $format = array('%s');
           $updt_result = $wpdb->update($table_name, $data, $where, $format);
           
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
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';
        
	    if($search == '' || $search == NULL ){
        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

	    } else { 
	      // Trim Search Term
          $search = trim($search);
	      if($search == 'Maid_Embassy'){ 
            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'S%'"); 
	        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'S%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A); 

	      } elseif($search == 'Foreign_Worker'){ 
            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'FW%'"); 
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A); 

	      } elseif($search == 'Work_Injury'){ 
            $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'WC%'"); 
            $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A); 

	      } elseif(strstr($search,',')){ 
            $strings = explode(',',$search); $strings[0] = trim($strings[0]); $strings[1] = trim($strings[1]); 
            if( ($strings[0] == 'new') || ($strings[0] == 'pending') || ($strings[0] == 'completed') ){
              if($strings[1] == 'Maid_Embassy'){ 
               $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'S%' AND `export_type` LIKE '%%%s%%'",$strings[0]);
               $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'S%' AND `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d",$strings[0], $per_page, $paged), ARRAY_A);   
	          }
              if($strings[1] == 'Foreign_Worker'){
               $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' AND `export_type` LIKE '%%%s%%'",$strings[0]);
               $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' AND `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d",$strings[0], $per_page, $paged), ARRAY_A);   
	          }
              if($strings[1] == 'Work_Injury'){
               $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' AND `export_type` LIKE '%%%s%%'",$strings[0]);
               $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' AND `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d",$strings[0], $per_page, $paged), ARRAY_A);   
	          }
            }
            if( ($strings[1] == 'new') || ($strings[1] == 'pending') || ($strings[1] == 'completed') ){
              if($strings[0] == 'Maid_Embassy'){
               $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'S%' AND `export_type` LIKE '%%%s%%'",$strings[1]);
               $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'S%' AND `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d",$strings[1], $per_page, $paged), ARRAY_A);   
	          }
              if($strings[0] == 'Foreign_Worker'){
               $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' AND `export_type` LIKE '%%%s%%'",$strings[1]);
               $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'FW%' AND `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d",$strings[1], $per_page, $paged), ARRAY_A);   
	          }
              if($strings[0] == 'Work_Injury'){
               $total_items = $wpdb->get_var("SELECT COUNT(id) FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' AND `export_type` LIKE '%%%s%%'",$strings[1]);
               $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' AND `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d",$strings[1], $per_page, $paged), ARRAY_A);   
	          }
            }
            //$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `ins_class` LIKE 'WC%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);  
	      } else{
            $total_items = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM ".$table_name." WHERE `id` LIKE '%%%s%%' OR `export_type` LIKE '%%%s%%'",$search, $search)); 
	        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$table_name." WHERE `id` LIKE '%%%s%%' OR `export_type` LIKE '%%%s%%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $search, $search, $per_page, $paged), ARRAY_A); 
	      }
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
 *
 * admin_menu hook implementation, will add pages to list persons and to add new one
 */
function custom_table_example_admin_menu()
{
    add_menu_page(__('Applications', 'custom_table_example'), __('Applications', 'custom_table_example'), 'activate_plugins', 'applications', 'custom_table_example_persons_page_handler', '', 50);
    add_submenu_page('applications', __('Applications', 'custom_table_example'), __('Applications', 'custom_table_example'), 'activate_plugins', 'applications', 'custom_table_example_persons_page_handler');
	/* listings for class */
	add_submenu_page('applications', __('Applications', 'custom_table_example'), __('Maid + Embassy Insurance', 'custom_table_example'), 'activate_plugins', 'applications&s=Maid_Embassy', 'custom_table_example_persons_page_handler');
	add_submenu_page('applications', __('Applications', 'custom_table_example'), __('Foreign Worker Insurance', 'custom_table_example'), 'activate_plugins', 'applications&s=Foreign_Worker', 'custom_table_example_persons_page_handler');
	add_submenu_page('applications', __('Applications', 'custom_table_example'), __('Work Injury Insurance', 'custom_table_example'), 'activate_plugins', 'applications&s=Work_Injury', 'custom_table_example_persons_page_handler');
	/* end listing for class */
    add_submenu_page('applications', __('Upload Data', 'custom_table_example'), __('Upload Data (Stat/IBMS)', 'custom_table_example'), 'activate_plugins', 'upload_form', 'custom_table_example_upload_form_page_handler');
    // add new will be described in next part
    //add_submenu_page('applications', __('Add new', 'custom_table_example'), __('Add new', 'custom_table_example'), 'activate_plugins', 'persons_form', 'custom_table_example_persons_form_page_handler');
    add_submenu_page(null, __('View/Upload Data', 'custom_table_example'), __('View/Upload Data', 'custom_table_example'), 'activate_plugins', 'applicant_details', 'custom_table_example_application_viewform_page_handler');
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
	
    if(isset($_REQUEST['s'])){ 
      //new and pending entries for Maid_Embassy
      if(isset($_REQUEST['bulkactionbtnExpN']) && strstr($_REQUEST['s'],'Maid_Embassy')){ 
        export_applications('Maid_Embassy', 'new');  
      }
      if(isset($_REQUEST['bulkactionbtnExpP']) && strstr($_REQUEST['s'],'Maid_Embassy')){ 
        export_applications('Maid_Embassy', 'pending');    
      }
      //new and pending entries for Foreign_Worker
      if(isset($_REQUEST['bulkactionbtnExpN']) && strstr($_REQUEST['s'],'Foreign_Worker')){ 
        export_applications('Foreign_Worker', 'new');     
      }
      if(isset($_REQUEST['bulkactionbtnExpP']) && strstr($_REQUEST['s'],'Foreign_Worker')){ 
        export_applications('Foreign_Worker', 'pending');    
      }
      //new and pending entries for Work_Injury
      if(isset($_REQUEST['bulkactionbtnExpN']) && strstr($_REQUEST['s'],'Work_Injury')){ 
        export_applications('Work_Injury', 'new');   
      }
      if(isset($_REQUEST['bulkactionbtnExpP']) && strstr($_REQUEST['s'],'Work_Injury')){ 
        export_applications('Work_Injury', 'pending');   
      }
      $table->prepare_items($_REQUEST['s']);
    } 

    if($_REQUEST['s'] == ''){ //export the new records
      $table->prepare_items(); 
    }
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
	<!--<a class="add-new-h2" href="<?php //echo get_admin_url(get_current_blog_id(), 'admin.php?page=persons_form');?>">
		<?php //_e('Add new', 'custom_table_example')?>
	</a>-->
    </h2>
    <?php echo $message; ?>

    <form id="persons-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $table->views(); ?>
	    <?php $table->search_box('search', 'search_id'); ?> 
        <?php if($_REQUEST['s'] == 'new'){ echo '<h2 style="float:left;position:absolute;margin-top:-4px;font-size:13px;"> New Entries: </h2>'; }?> 
        <?php if($_REQUEST['s'] == 'pending'){ echo '<h2 style="float:left;position:absolute;margin-top:-4px;font-size:13px;"> Pending Entries: </h2>'; }?> 
        <?php if($_REQUEST['s'] == 'completed'){ echo '<h2 style="float:left;position:absolute;margin-top:-4px;font-size:13px;"> Completed Entries: </h2>'; }?>  		
        <?php $table->display() ?>
    </form>

</div>
<script>
  jQuery('#bulkactionbtnExpN').click(function(){ 
    var reply = confirm('Do you want to export ? (Cancel/OK)');
    if(reply != true){ return false; }
  }); 
  jQuery('#bulkactionbtnExpP').click(function(){ 
    var reply = confirm('Do you want to export ? (Cancel/OK)');
    if(reply != true){ return false; }
  }); 
</script>
<?php
}

/**
 * =========================================================
 * Function for exporting the new and pending applications
 */
function export_applications($insclass, $export_type)
{
   global $wpdb;
   //$table_name = $wpdb->prefix . 'cte'; 
   $table_name = 'ins_policy_applicants';
   if($insclass == 'Maid_Embassy'){
     $items = $wpdb->get_results("SELECT * FROM $table_name WHERE `ins_class` LIKE 'S%' AND `export_type` ='".$export_type."' AND payment_approved=1 ORDER BY id DESC", ARRAY_A);
   } elseif($insclass == 'Foreign_Worker'){
     $items = $wpdb->get_results("SELECT * FROM $table_name WHERE `ins_class` LIKE 'FW%' AND `export_type` ='".$export_type."' AND payment_approved=1 ORDER BY id DESC", ARRAY_A);
   } elseif($insclass == 'Work_Injury'){
     $items = $wpdb->get_results("SELECT * FROM $table_name WHERE `ins_class` LIKE 'WC%' AND `export_type` ='".$export_type."' AND payment_approved=1 ORDER BY id DESC", ARRAY_A);
   }
   ob_end_clean();
   $out = fopen('php://output', 'w');

   if($insclass == 'Foreign_Worker'){ $filename = "fwlweb.txt"; }
   elseif($insclass == 'Work_Injury'){ $filename = "fwlweb.txt"; }
   else{ $filename = "web.txt"; } 
   //$filename="web.csv";
  
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
     //will not print completed items
     if($row['export_type'] == 'completed'){ continue; } 
     //update the export type of the row to pending
     $where = array('id' => $row['id']); $data = array('export_type' => 'pending'); $format = array('%s');
     $updt_result = $wpdb->update($table_name, $data, $where, $format);
           
     $counter = 0; foreach($row as $item){ 
	   if(in_array($counter, $fields_nos)){ array_push($field_values, $item); }
	   $counter++;
     }
     $cnt = 0; foreach($field_values as $value){  
       if($cnt == (sizeof($field_values)-1)){ 
		   fputs($out, (str_pad(substr($value,0,20),20)."\r\n")); $cnt++; 		
	   }
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
		   $rplVal = str_replace('-','',$value); if($rplVal == '00000000'){ $rplVal = ''; }
		   fputs($out, str_pad(substr($rplVal,0,10),10));
		} else { fputs($out, str_pad(substr($value,0,10),10)); }
	   }    
      }
      $cnt++;
     }
   }  
   fclose($out); exit();
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

    //generate error if any file missing or not uploaded
    if (wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) { //var_dump($_FILES); exit;
    /*if($_FILES['upload_web_stat']['error'] || $_FILES['upload_web_ibms']['error']){ 
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
    }*/

    //if web_stat file has been uploaded
    //do the necessary actions - policy number and invoice number to db
    if(isset($_FILES['upload_web_stat']['name']) && ($_FILES['upload_web_stat']['error'] != UPLOAD_ERR_NO_FILE)){
      $webstat_tmp_file_name = $_FILES['upload_web_stat']['tmp_name']; 
	  $webstatfp = fopen($webstat_tmp_file_name, 'r'); 
	  //check if all records exist in db
	  $lineNo = 0; 
	  while(true) { $lineNo++;
	    if(($bufferwebstat = fgets($webstatfp)) === false){ break; }
	    if(trim($bufferwebstat) == ''){ continue; }
	    $wstatsysID = trim(substr($bufferwebstat,0,7)); $otherID = trim(substr($bufferwebstat,7,18));
	    $wstatguarantee_no = trim(substr($bufferwebstat,25,21)); $wstatmaster_policy_no = trim(substr($bufferwebstat,46,12));  
	    $results_id = $wpdb->get_results("SELECT * FROM $table_name WHERE id = '$wstatsysID'", ARRAY_N); //var_dump($results_id); 
	    if(count($results_id) == 0){ 
	      $errmessage = '<div class="error" style="background:#f3123acc;">';		   
	      $errmessage .= '<h3 style="color:#000;font-size:13px;">';
	      $errmessage .= 'Error: Web Stat File Do Not Match Serial Number In DB. <br/>'; 
	      $errmessage .= 'Web Stat Line No: '.$lineNo.'<br/></h3></div>'; 
	      echo $errmessage;
	      echo '<a class="button" href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=upload_form').'">';
	      echo _e('Back To Upload', 'custom_table_example'); echo '</a>';
	      wp_die();
	    }
	  }
	  rewind($webstatfp);
	  //check if all records exist in db
	  $lineNo = 0; 
	  while(true) { $lineNo++;
	    if(($bufferwebstat = fgets($webstatfp)) === false){ break; }
	    if(trim($bufferwebstat) == ''){ continue; }
	    //get values from web stat file
	    $wstatsysID = trim(substr($bufferwebstat,0,7)); $otherID = trim(substr($bufferwebstat,7,18)); //echo $wstatsysID;
	    $wstatguarantee_no = trim(substr($bufferwebstat,25,21)); $wstatmaster_policy_no = trim(substr($bufferwebstat,46,12)); 
	    //get row results 
	    $results_id = $wpdb->get_results("SELECT * FROM $table_name WHERE id = '$wstatsysID'", ARRAY_N); //var_dump($results_id);
	    $data = array('guarantee_no'=>$wstatguarantee_no,'master_policy_no'=>$wstatmaster_policy_no,'export_type'=>'completed'); 
        $where = array('id' => $wstatsysID); 
        $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);
        if($results_id[0][0] != '') { 
	       $message .= __('Item Updated: '.$results_id[0][0].'<br/>', 'custom_table_example'); 
		   $insclass =  $results_id[0][5];
           if($insclass == 'WCS'){ $format = 'wic'; } else { $format = 'standard'; }
           generatePolicyPDFs($results_id[0][0], 'web_stat');
		   //sendEmail($format, $results_id[0][0]); 
		   if($insclass != 'FWMI'){
		     sendEmailWithAttachment($format, $results_id[0][0]);
		   }  
	     } 
	  }
    } // end of upload web_stat.txt
    
    //when ibms file was uploaded
    if(isset($_FILES['upload_web_ibms']['name']) && ($_FILES['upload_web_ibms']['error'] != UPLOAD_ERR_NO_FILE)){
	  //if both files have same lines, then process the data
      $webibms_tmp_file_name = $_FILES['upload_web_ibms']['tmp_name']; 
	  $webibmsfp = fopen($webibms_tmp_file_name, 'r'); $lineNo = 0;
	  while(true) { $lineNo++;
	   if(($bufferwebibms = fgets($webibmsfp)) === false){ break; }
	   if(trim($bufferwebibms) == ''){ continue; }  
	   //var_dump($bufferwebstat); var_dump($bufferwebibms); exit;
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
	   $results_id = $wpdb->get_results("SELECT * FROM $table_name WHERE guarantee_no = '$guarantee_no'", ARRAY_N); //var_dump($results_id);
	   if(count($results_id) != 0){
	      $errmessage = '<div class="error" style="background:#f3123acc;">';		   
	      $errmessage .= '<h3 style="color:#000;font-size:13px;">';
	      $errmessage .= 'Error: Web IBMS File Matches Record In DB, Policy ID cannot be duplicate.<br/>'; 
	      $errmessage .= 'Web IBMS Line No: '.$lineNo.'<br/></h3></div>'; 
	      echo $errmessage;
	      echo '<a class="button" href="'.get_admin_url(get_current_blog_id(), 'admin.php?page=upload_form').'">';
	      echo _e('Back To Upload', 'custom_table_example'); echo '</a>';
	      wp_die();
	   }
	  } 
	  rewind($webibmsfp); 
	  $lineNo =0; 
	  while(true) { $lineNo++;
	    if(($bufferwebibms = fgets($webibmsfp)) === false){ break; }
	    if(trim($bufferwebibms) == ''){ continue; }
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
	    //insert the details into db
	    $data = array( 'userID'=>0, 'entry_date' => $entry_date, 'ins_class' => $ins_class,'er_cpfno' => $er_cpfno,
	                'wp_no' => $wp_no, 'eff_date'=> $eff_date, 'exp_date' => $exp_date, 'ins_party_nm' => $ins_party_nm, 
	                'ins_party_nric' => $ins_party_nric, 'addr1' => $addr1, 'addr2' => $addr2, 'contact_person' => $contact_person, 
	                'ph_no' => $ph_no, 'ins_person' => $ins_person, 'dob' => $dob, 'nationality' => $nationality, 
	                'passport' => $passport, 'guarantee_no' => $guarantee_no, 'master_policy_no' => $master_policy_no, 
	                'export_type'=>'completed' );
	    $format = array( '%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s' ); 
	    $wpdb->insert($table_name,$data,$format); //$wpdb->print_error(); //echo $wpdb->last_query;
	    $inserted_Id = $wpdb->insert_id; //var_dump($data); var_dump($format);  
	    //send email after insert
	    if($inserted_Id != '') { 
	      $message .= __('Item Updated: '.$guarantee_no.' - Allocated Serial No: '.$inserted_Id.'<br/>', 'custom_table_example'); 
		  $insclass =  $ins_class;
          if($insclass == 'WCS'){ $format = 'wic'; } else { $format = 'standard'; }
          generatePolicyPDFs($inserted_Id, 'web_ibms');
		  //sendEmail($format, $inserted_Id);
		  if($insclass != 'FWMI'){
		    sendEmailWithAttachment($format, $inserted_Id); 
		  }  
	    }
	  } //end while
    } //if ibms file upload
   } //end if verify nonce
?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Upload Desktop Data', 'custom_table_example')?>
	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=applications');?>"><?php _e('back to list', 'custom_table_example')?></a>
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
 * This function renders send email out to customer 
 *
 * @param $item
 */
function sendEmail($format, $id){
global $wpdb;
$table_name = 'ins_policy_applicants';
$record = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id", ARRAY_N); //var_dump($record);
$emailTo = $record[0][31]; 
if($emailTo == ''){ $emailTo = 'jon.lau@windev.com.sg'; }
/**
 * creation of the email templates for standard and wic formats
 */
if($format == 'standard'){
   $headers[] = 'From: Windev - Jon Lau <jon.lau@windev.com.sg>';
   $headers[] = 'Cc: arunava@sketchwebsolutions.com, mj.techizer@gmail.com, jon.lau@windev.com.sg,jonlau9882@gmail.com'."\r\n";
   $headers[] = "BCC: arunava@sketchwebsolutions.com \r\n";
   $headers[] = 'Content-Type: text/html; charset=UTF-8';
   $to = $emailTo; 
   //$to = 'arunava@sketchwebsolutions.com';
   $subject = 'Insurance With Policy Is Ready - Appln ID: '.$record[0][0];
   $body = '<p><strong> Subject: </strong> Policy successfully created. Policy no. ['.$record[0][27].'] | Insured - ['.$record[0][21].']</p>';
   $body .= '<p><strong>Thank you for placing your insurance with us.</strong></p><br/><br/><br/>';
   $body .= '<p><table>';
   $body .= '<tr><td>Policy Number:</td><td>'.$record[0][27].'</td></tr>';
   $body .= '<tr><td>Insured For:</td><td>'.$record[0][21].'</td></tr>';
   $body .= '</table></p>';
   $body .= '<p>To view or print the insurance policy:</p>';
   $files = explode(',',$record[0][32]);  //var_dump($files);	
   foreach($files as $file){ 
   $name = str_replace('.pdf','',$file);
   //$orname=trim($name," 0..9" );
   //$orname=trim($name," 0..9" );
	 $body .= '<a href="'.wp_upload_dir()['baseurl'].'/policy_uploads/'.$file.'">'.$name.'</a>'; $body .= '<br/>';
   } 
   $body .= '<p>To view or print the invoice <a href="'.get_bloginfo('url').'/account">click here</a></p>';
   $body .= '<p>For enquiries, please contact 65336113 from Monday to Friday between 9:00AM to 6:00PM.</p>';
   $body .= '<p>Yours sincerely,</p><p>insureAsia</p>';
   $body .= '<hr/>';
   //$body .= '<p><img src="'.wp_upload_dir('2018/07', false, false)['url'].'/logo.png" alt="Logo Image" width="100" height="100"/></p>';
   $body .= '<p>&nbsp;</p><p>&nbsp;</p>';
}

if($format == 'wic'){
   $headers[] = 'From: Windev - Jon Lau <jon.lau@windev.com.sg>';
   $headers[] = 'Cc: arunava@sketchwebsolutions.com, mj.techizer@gmail.com, jon.lau@windev.com.sg,jonlau9882@gmail.com'."\r\n";
   $headers[] = "BCC: arunava@sketchwebsolutions.com \r\n";
   $headers[] = 'Content-Type: text/html; charset=UTF-8';
   $to = $emailTo;
   //$to = 'arunava@sketchwebsolutions.com';   
   $subject = 'WIC Insurance With Policy Is Ready - Appln ID: '.$record[0][0];
   $body = '<p><strong> Subject: </strong> Work Injury Compensation Insurance - '.$record[0][21].' | '.$record[0][12].'</p>';
   $body .= '<p><strong>Thank you for placing your insurance with us. ';
   $body .= 'We are pleased to provide the policy details for your reference.</strong></p><br/><br/>';
   $body .= '<p><table>';
   $body .= '<tr><td>Insurer:</td><td>'.$record[0][12].'</td></tr>';
   $body .= '<tr><td>Period of Insurance:</td><td>'.$record[0][8].' - '.$record[0][9].'</td>';
   $body .= '<tr><td>Policy Number:</td><td>'.$record[0][27].'</td></tr>';
   $body .= '</table></p>';
   $body .= '<p>Policy will be send out by post once it is ready, processing time will takes approximately 2 weeks.</p>';
   $body .= '<p>Thank you</p>';
   $body .= '<hr/>';
   $body .= '<p>insureAsia</p><p>Agency Pte Ltd</p><p>Tel: 65336113</p><p>Fax: 65334002/3</p>';
   $body .= '<p>***Kindly check MOM\'s Work Permit Online (WPOL) for successful bond transmission before arranging ';
   $body .= 'for your worker arrival to Singapore.</p>';
   $body .= '<p>***For customers doing bank transfer, we seek your kind cooperation to transfer the EXACT PREMIUM payable.</p>';
   $body .= '<p>Travel Insurance<p><p>25% Discount specially arranged for you</p><p>Click insureAsia to apply now!</p>';
   $body .= '<hr/>';
   //$body .= '<p><img src="'.wp_upload_dir('2018/07', false, false)['url'].'/logo.png" alt="Logo Image" width="100" height="100"/></p>';
   $body .= '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>';
}
wp_mail( $to, $subject, $body, $headers );
}


/**
 * This function renders send email out to customer 
 *
 * @param $item
 */
function sendEmailWithAttachment($format, $id){
global $wpdb;
$table_name = 'ins_policy_applicants';
$record = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $id", ARRAY_N); //var_dump($record);
$emailTo = $record[0][31]; 
if($emailTo == ''){ $emailTo = 'jon.lau@windev.com.sg'; }
/**
 * creation of the email templates for standard and wic formats
 */
if($format == 'standard'){
   //boundary 
   $semi_rand = md5(uniqid(time()));
   $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
    
   $headers[] = 'From: Windev - Jon Lau <jon.lau@windev.com.sg>';
   $headers[] = 'Cc: arunava@sketchwebsolutions.com, mj.techizer@gmail.com, jon.lau@windev.com.sg,jonlau9882@gmail.com'."\r\n";
   $headers[] = "BCC: arunava@sketchwebsolutions.com \r\n";
   //$headers[] = 'Content-Type: text/html; charset=UTF-8'; 
   
   //headers for attachment 
   $headers[] = "MIME-Version: 1.0\r\n"; 
   $headers[] = "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\"\r\n\r\n"; 
   
   $to = $emailTo; //$to = 'arunava@sketchwebsolutions.com';   
   $subject = 'Insurance With Policy Is Ready - Appln ID: '.$record[0][0];
   
   //multipart boundary 
   $body = "--{$mime_boundary}\r\n"; 
   $body .= "Content-Type: text/html; charset=\"UTF-8\" \r\n";
   $body .= "Content-Transfer-Encoding: 7bit \r\n\r\n"; 
   
   $body .= '<p><strong> Subject: </strong> Policy successfully created. Policy no. ['.$record[0][27].'] | Insured - ['.$record[0][21].']</p>';
   $body .= '<p><strong>Thank you for placing your insurance with us.</strong></p><br/><br/><br/>';
   $body .= '<p><table>';
   $body .= '<tr><td>Policy Number:</td><td>'.$record[0][27].'</td></tr>';
   $body .= '<tr><td>Insured For:</td><td>'.$record[0][21].'</td></tr>';
   $body .= '</table></p>';
   $body .= '<p>To view or print the insurance policy:</p>';
   $files = explode(',',$record[0][32]); //var_dump($files); 
   foreach($files as $file){ $name = str_replace('.pdf','',$file); $name = str_replace('_',' ',$name); $name=trim($name," 0..9" );
	 $body .= '<a href="'.wp_upload_dir()['baseurl'].'/policy_uploads/'.$file.'">'.$name.'</a>'; $body .= '<br/>';
   } 
   $body .= '<p>To view or print the invoice <a href="'.get_bloginfo('url').'/account">click here</a></p>';
   $body .= '<p>For enquiries, please contact 65336113 from Monday to Friday between 9:00AM to 6:00PM.</p>';
   $body .= '<p>Yours sincerely,</p><p>insureAsia</p>';
   $body .= '<hr/>';
   //$body .= '<p><img src="'.wp_upload_dir('2018/07', false, false)['url'].'/logo.png" alt="Logo Image" width="100" height="100"/></p>';
   $body .= '<p>&nbsp;</p><p>&nbsp;</p>'; 
   $body .= "\r\n\r\n";
   
   //for attachment
   if(strstr($record[0][32],',')){ $files = explode(',',$record[0][32]); //var_dump($files);
   } else { $files = array($record[0][32]); } 
   
   foreach($files as $file){ 
	 $attachFile = wp_upload_dir()['basedir'].'/policy_uploads/'.$file; //echo $attachFile;
	 $fp = fopen($attachFile,"rb"); $data = fread($fp,filesize($attachFile)); fclose($fp);
     $data = chunk_split(base64_encode($data)); 

	 //$content = file_get_contents( $attachFile ); $content = chunk_split(base64_encode($content));

	 $body .= "--{$mime_boundary}\r\n";
     $body .= "Content-Type: application/octet-stream; name=\"".$file."\" \r\n"; 
     $body .= "Content-Transfer-Encoding: base64 \r\n";
     $body .= "Content-Disposition: attachment; filename=\"".$file."\" \r\n"; 
     $body .=  $data."\r\n\r\n";
   }
   
   $body .= "--{$mime_boundary}--";
}

if($format == 'wic'){
   //boundary 
   $semi_rand = md5(uniqid(time()));
   $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
        
   $headers[] = 'From: Windev - Jon Lau <jon.lau@windev.com.sg>';
   $headers[] = 'Cc: arunava@sketchwebsolutions.com, mj.techizer@gmail.com, jon.lau@windev.com.sg'."\r\n";
   $headers[] = "BCC: arunava@sketchwebsolutions.com \r\n";
   //$headers[] = 'Content-Type: text/html; charset=UTF-8';
   
   //headers for attachment 
   $headers[] = "MIME-Version: 1.0\r\n"; 
   $headers[] = "Content-Type: multipart/mixed; boundary=\"{$mime_boundary}\"\r\n\r\n";    
   
   $to = $emailTo;
   //$to = 'arunava@sketchwebsolutions.com';   
   $subject = 'WIC Insurance With Policy Is Ready - Appln ID: '.$record[0][0];

   //multipart boundary 
   $body = "--{$mime_boundary}\r\n"; 
   $body .= "Content-Type: text/html; charset=\"UTF-8\" \r\n";
   $body .= "Content-Transfer-Encoding: 7bit \r\n\r\n";    
   
   $body .= '<p><strong> Subject: </strong> Work Injury Compensation Insurance - '.$record[0][21].' | '.$record[0][12].'</p>';
   $body .= '<p><strong>Thank you for placing your insurance with us. ';
   $body .= 'We are pleased to provide the policy details for your reference.</strong></p><br/><br/>';
   $body .= '<p><table>';
   $body .= '<tr><td>Insurer:</td><td>'.$record[0][12].'</td></tr>';
   $body .= '<tr><td>Period of Insurance:</td><td>'.$record[0][8].' - '.$record[0][9].'</td>';
   $body .= '<tr><td>Policy Number:</td><td>'.$record[0][27].'</td></tr>';
   $body .= '</table></p>';
   $body .= '<p>Policy will be send out by post once it is ready, processing time will takes approximately 2 weeks.</p>';
   $body .= '<p>Thank you</p>';
   $body .= '<hr/>';
   $body .= '<p>insureAsia</p><p>Agency Pte Ltd</p><p>Tel: 65336113</p><p>Fax: 65334002/3</p>';
   $body .= '<p>***Kindly check MOM\'s Work Permit Online (WPOL) for successful bond transmission before arranging ';
   $body .= 'for your worker arrival to Singapore.</p>';
   $body .= '<p>***For customers doing bank transfer, we seek your kind cooperation to transfer the EXACT PREMIUM payable.</p>';
   $body .= '<p>Travel Insurance<p><p>25% Discount specially arranged for you</p><p>Click insureAsia to apply now!</p>';
   $body .= '<hr/>';
   //$body .= '<p><img src="'.wp_upload_dir('2018/07', false, false)['url'].'/logo.png" alt="Logo Image" width="100" height="100"/></p>';
   $body .= '<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>';
   $body .= "\r\n\r\n";
   
   //for attachment
   if(strstr($record[0][32],',')){ $files = explode(',',$record[0][32]); //var_dump($files);
   } else { $files = array($record[0][32]); } 
   
   foreach($files as $file){ 
	 $attachFile = wp_upload_dir()['basedir'].'/policy_uploads/'.$file; //echo $attachFile;
	 $fp = fopen($attachFile,"rb"); $data = fread($fp,filesize($attachFile)); fclose($fp);
     $data = chunk_split(base64_encode($data)); 

	 //$content = file_get_contents( $attachFile ); $content = chunk_split(base64_encode($content));

	 $body .= "--{$mime_boundary}\r\n";
     $body .= "Content-Type: application/octet-stream; name=\"".$file."\" \r\n"; 
     $body .= "Content-Transfer-Encoding: base64 \r\n";
     $body .= "Content-Disposition: attachment; filename=\"".$file."\" \r\n"; 
     $body .=  $data."\r\n\r\n";
   }
   $body .= "--{$mime_boundary}--";   
}
wp_mail( $to, $subject, $body, $headers );
}



/**
 * Function to generate pdfs
 * 
 * @param applicationID
 * @param importType as 'web_stat'/'web_ibms'
 */
function generatePolicyPDFs($applicationID, $importType)
{ 
   global $wpdb;
   $table_name = 'ins_policy_applicants';
   //$applicationID = '177551'; $applicationID = '177552'; 
   
   //basedir and policydir 
   $mainUploadDir = wp_upload_dir(); 
   $baseDir = $mainUploadDir['basedir'];
   $policyDir = $baseDir .'/policy_uploads/'; 
   //pdf sourcefiles directory
   $srcfileDirectory = __DIR__.'/lib/insureasiaForms'; 

   if($applicationID == NULL){ //generate test pdf 
      $pdf = new FPDF(); $pdf->AddPage();
      $pdf->SetFont('Arial','B',16); $pdf->Cell(40,10,'Hello World!');
      $pdffile = $policyDir.'test.pdf'; $pdf->Output('F',$pdffile); 
      //message to generate 
      $message = __('Test PDF Generated: '.$pdffile.'<br/>', 'custom_table_example');

   }else{ //generate policy details
      $record = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N); 
      //var_dump($record); echo $record[0][5];

      //domestic maid insurance - Maid Insurance Proposal Form
      if(($record[0][5] == 'S5A') || ($record[0][5] == 'S5B') || ($record[0][5] == 'S5C') || ($record[0][5] == 'S5D') || 
         ($record[0][5] == 'S5AP') || ($record[0][5] == 'S5BP') || ($record[0][5] == 'S5CP') || ($record[0][5] == 'S5DP')){ 
		//policy jacket
		$sourcefile = $srcfileDirectory.'/_Maid_Jacket.pdf';
		$pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
		$pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
		$pdf->AddPage(); $tplIdx2 = $pdf->importPage(2); $pdf->useTemplate($tplIdx2, 0, 0, 200); 
		$pdf->AddPage(); $tplIdx3 = $pdf->importPage(3); $pdf->useTemplate($tplIdx3, 0, 0, 200); 
		$pdf->AddPage(); $tplIdx4 = $pdf->importPage(4); $pdf->useTemplate($tplIdx4, 0, 0, 200); 
        $pdffile = $policyDir.$record[0][0].time().'_Maid_Jacket.pdf'; 
		$pdffileUrl = $record[0][0].time().'_Maid_Jacket.pdf';
        $pdf->Output('F',$pdffile);

		//completed proposal form
        $sourcefile = $srcfileDirectory.'/_Maid_Insurance_Proposal_Form.pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate employer's particulars 
        $pdf->Text(15,56,$record[0][12]); $pdf->Text(93,56,$record[0][13]); 
        $pdf->Text(15,64,$record[0][14]); $pdf->Text(145,61,$record[0][19]); $pdf->Text(140,64,$record[0][31]);
        $pdf->Text(15,72,$record[0][6]); $pdf->Text(55,72,$record[0][16]); 
        //generate maid's particulars
        $pdf->Text(15,87,$record[0][21]); $pdf->Text(130,87,$record[0][3]); 
        $pdf->Text(15,95,$record[0][24]); $pdf->Text(75,95,$record[0][23]); $pdf->Text(130,95,$record[0][7]);
        //period of insurance
        $pdf->Text(60,102,$record[0][8]); $pdf->Text(130,102,$record[0][9]); 
        //add the selection particulars to form
        if(($record[0][5] == 'S5A') || ($record[0][5] == 'S5AP')){ $pdf->Text(112,113,'x'); $pdf->Text(112,117,'x'); } 
        if(($record[0][5] == 'S5B') || ($record[0][5] == 'S5BP')){ $pdf->Text(127,113,'x'); $pdf->Text(112,117,'x'); }
        if(($record[0][5] == 'S5C') || ($record[0][5] == 'S5CP')){ $pdf->Text(137,113,'x'); $pdf->Text(112,117,'x'); }
        if(($record[0][5] == 'S5D') || ($record[0][5] == 'S5DP')){ $pdf->Text(147,113,'x'); $pdf->Text(112,117,'x'); }
        $pdf->AddPage(); $tplIdx2 = $pdf->importPage(2); $pdf->useTemplate($tplIdx2, 0, 0, 200);
        $pdffile = $policyDir.$record[0][0].time().'_Maid_Insurance_Proposal_Form.pdf'; 
	    $pdffileUrl .= ','.$record[0][0].time().'_Maid_Insurance_Proposal_Form.pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile); 
        
		//completed bond form
		$sourcefile = $srcfileDirectory.'/_Maid_Policy_(Bond).pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate employer's particulars 
        $dtarray = explode('-', $record[0][3]); 
        $entrydate = date('d/m/Y', mktime(0,0,0,rtrim($dtarray[1],'0'),$dtarray[2],$dtarray[0]));
        $pdf->Text(159,46,$entrydate); 
        $pdf->Text(53,58,$record[0][27]); $pdf->Text(53,74,strtoupper($record[0][12]));
        $pdf->Text(51,81,$record[0][24]); $pdf->Text(16,87,strtoupper($record[0][14])); 
        $pdf->Text(80,91,$entrydate);  
        //last date fields
        $dt1array = explode('-', $record[0][9]); 
        $expdate = date('d/m/Y', mktime(0,0,0,rtrim($dt1array[1],'0'),$dt1array[2],$dt1array[0]));
        $pdf->Text(140,190,$expdate);  $pdf->Text(66,198,$entrydate);
        $pdffile = $policyDir.$record[0][0].time().'_Maid_Policy_(Bond).pdf'; 
	    $pdffileUrl .= ','.$record[0][0].time().'_Maid_Policy_(Bond).pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile);     
        
		//completed schedule form
		$sourcefile = $srcfileDirectory.'/_Maid_Policy_(Schedule).pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate insured particulars 
        $pdf->Text(150,50,$record[0][27]); 
        $pdf->Text(65,55,strtoupper($record[0][12])); $pdf->Text(65,60,strtoupper($record[0][14].','.$record[0][15]));
        //generated insured person details
        $pdf->Text(65,73,strtoupper($record[0][21])); 
        $dt2array = explode('-', $record[0][9]); 
        $dob = date('d/m/Y', mktime(0,0,0,rtrim($dt2array[1],'0'),$dt2array[2],$dt2array[0]));        
        $pdf->Text(65,79,$dob); 
        $pdf->Text(65,83,$record[0][24]); $pdf->Text(140,83,strtoupper($record[0][23]));
        $pdf->Text(65,88,$entrydate); $pdf->Text(94,88,$expdate);
        $pdffile = $policyDir.$record[0][0].time().'_Maid_Policy_(Schedule).pdf'; 
	    $pdffileUrl .= ','.$record[0][0].time().'_Maid_Policy_(Schedule).pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile);            
		  
		/*//for agent - premium advice
		if($record[0][4] != '17045'){ 
		//completed premium advice
		$sourcefile = $srcfileDirectory.'/_Maid_Policy_Prem_Adv.pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate employer's particulars 
        $pdf->Text(15,56,$record[0][12]); $pdf->Text(93,56,$record[0][13]); 
        $pdf->Text(15,64,$record[0][14]); $pdf->Text(145,61,$record[0][19]); $pdf->Text(140,64,$record[0][31]);
        $pdf->Text(15,72,$record[0][6]); $pdf->Text(55,72,$record[0][16]); 
        //generate maid's particulars
        $pdf->Text(15,87,$record[0][21]); $pdf->Text(130,87,$record[0][3]); 
        $pdf->Text(15,95,$record[0][24]); $pdf->Text(75,95,$record[0][23]); $pdf->Text(130,95,$record[0][7]);
        //period of insurance
        $pdf->Text(60,102,$record[0][8]); $pdf->Text(130,102,$record[0][9]); 
        //add the selection particulars to form
        if(($record[0][5] == 'S5A') || ($record[0][5] == 'S5AP')){ $pdf->Text(112,113,'x'); $pdf->Text(112,117,'x'); } 
        if(($record[0][5] == 'S5B') || ($record[0][5] == 'S5BP')){ $pdf->Text(127,113,'x'); $pdf->Text(112,117,'x'); }
        if(($record[0][5] == 'S5C') || ($record[0][5] == 'S5CP')){ $pdf->Text(137,113,'x'); $pdf->Text(112,117,'x'); }
        if(($record[0][5] == 'S5D') || ($record[0][5] == 'S5DP')){ $pdf->Text(147,113,'x'); $pdf->Text(112,117,'x'); }
        //$pdf->AddPage(); $tplIdx2 = $pdf->importPage(2); $pdf->useTemplate($tplIdx2, 0, 0, 200);
        $pdffile = $policyDir.$record[0][0].time().'_Maid_Policy_Prem_Adv.pdf'; 
	    $pdffileUrl .= ','.$record[0][0].time().'_Maid_Policy_Prem_Adv.pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile);
		}*/
		  
		//insert record into db
        $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N);
	    $pdf_filename .= $pdffileUrl; 
        $data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $interimrecord[0][0]); 
        $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);  
      }

      //philippines embassy bond - Philippines Embassy 2000 Bond Proposal Form
      //philippines embassy bond - Philippines Embassy 7000 Bond Proposal Form      
      if(($record[0][5] == 'S2A') || ($record[0][5] == 'S2D')){ 
        if($record[0][5] == 'S2D'){ 
            $sourcefile = $srcfileDirectory.'/_Philippine-Embassy-7000-Bond-Proposal-Form.pdf';
            $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
            $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
            $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
            //generate employer's particulars 
            $pdf->Text(12,63,$record[0][12]); $pdf->Text(98,63,$record[0][13]); 
            $pdf->Text(12,72,$record[0][14]); $pdf->Text(145,68,$record[0][19]); $pdf->Text(140,75,$record[0][31]);
            //generate maid's particulars
            $pdf->Text(12,98,$record[0][21]); $pdf->Text(99,98,$record[0][3]); $pdf->Text(147,98,$record[0][24]); 
            //period of insurance
            $pdf->Text(60,119,$record[0][8]); $pdf->Text(130,119,$record[0][9]); 
            $pdffile = $policyDir.$record[0][0].time().'_Philippine-Embassy-7000-Bond-Proposal-Form.pdf'; 
	        $pdffileUrl = $record[0][0].time().'_Philippine-Embassy-7000-Bond-Proposal-Form.pdf';
            $pdf->Output('F', $pdffile);
	        //insert record into db
            $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N);  
	        $pdf_filename = $pdffileUrl;
            $data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $interimrecord[0][0]); 
            $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);
        } else {
            $sourcefile = $srcfileDirectory.'/_Philippine-Embassy-2000-Bond-Proposal-Form.pdf';
            $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
            $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
            $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
            //generate employer's particulars 
            $pdf->Text(10,63,$record[0][12]); $pdf->Text(95,63,$record[0][13]); 
            $pdf->Text(10,72,$record[0][14]); $pdf->Text(140,68,$record[0][19]); $pdf->Text(140,75,$record[0][31]);
            //generate maid's particulars
            $pdf->Text(10,98,$record[0][21]); $pdf->Text(98,98,$record[0][3]); $pdf->Text(147,98,$record[0][24]); 
            //period of insurance
            $pdf->Text(60,119,$record[0][8]); $pdf->Text(130,119,$record[0][9]); 
            $pdffile = $policyDir.$record[0][0].time().'_Philippine-Embassy-2000-Bond-Proposal-Form.pdf'; 
	        $pdffileUrl = $record[0][0].time().'_Philippine-Embassy-2000-Bond-Proposal-Form.pdf';
            $pdf->Output('F', $pdffile);
	        //insert record into db
            $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N);  
	        $pdf_filename = $pdffileUrl;
            $data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $interimrecord[0][0]); 
            $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);
        }
        
        //completed bond form
		$sourcefile = $srcfileDirectory.'/_Philippine_Bond_Policy.pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate employer's particulars 
        $dtarray = explode('-', $record[0][3]); 
        $entrydate = date('d/m/Y', mktime(0,0,0,rtrim($dtarray[1],'0'),$dtarray[2],$dtarray[0]));
        $pdf->Text(18,40,$entrydate); 
        //employer details
        $pdf->Text(53,79,$record[0][27]); $pdf->Text(53,89,strtoupper($record[0][12]));
        $pdf->Text(51,93,$record[0][13]); $pdf->Text(16,100,strtoupper($record[0][14])); 
        //employee details
        $pdf->Text(63,190,$record[0][21]); $pdf->Text(35,198,$record[0][24]);        
        $dt1array = explode('-', $record[0][22]); 
        $dob = date('d/m/Y', mktime(0,0,0,rtrim($dt1array[1],'0'),$dt1array[2],$dt1array[0]));
        $pdf->Text(125,194,$dob);  
        $pdffile = $policyDir.$record[0][0].time().'_Philippine_Bond_Policy.pdf'; 
	    $pdffileUrl .= ','.$record[0][0].time().'_Philippine_Bond_Policy.pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile); 
        
		/*//for agent - premium advice
		if($record[0][4] != '17045'){ 
		//completed premium advice
		$sourcefile = $srcfileDirectory.'/_Maid_Policy_Prem_Adv.pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate employer's particulars 
        $pdf->Text(15,56,$record[0][12]); $pdf->Text(93,56,$record[0][13]); 
        $pdf->Text(15,64,$record[0][14]); $pdf->Text(145,61,$record[0][19]); $pdf->Text(140,64,$record[0][31]);
        $pdf->Text(15,72,$record[0][6]); $pdf->Text(55,72,$record[0][16]); 
        //generate maid's particulars
        $pdf->Text(15,87,$record[0][21]); $pdf->Text(130,87,$record[0][3]); 
        $pdf->Text(15,95,$record[0][24]); $pdf->Text(75,95,$record[0][23]); $pdf->Text(130,95,$record[0][7]);
        //period of insurance
        $pdf->Text(60,102,$record[0][8]); $pdf->Text(130,102,$record[0][9]); 
        //add the selection particulars to form
        if(($record[0][5] == 'S5A') || ($record[0][5] == 'S5AP')){ $pdf->Text(112,113,'x'); $pdf->Text(112,117,'x'); } 
        if(($record[0][5] == 'S5B') || ($record[0][5] == 'S5BP')){ $pdf->Text(127,113,'x'); $pdf->Text(112,117,'x'); }
        if(($record[0][5] == 'S5C') || ($record[0][5] == 'S5CP')){ $pdf->Text(137,113,'x'); $pdf->Text(112,117,'x'); }
        if(($record[0][5] == 'S5D') || ($record[0][5] == 'S5DP')){ $pdf->Text(147,113,'x'); $pdf->Text(112,117,'x'); }
        //$pdf->AddPage(); $tplIdx2 = $pdf->importPage(2); $pdf->useTemplate($tplIdx2, 0, 0, 200);
        $pdffile = $policyDir.$record[0][0].time().'_Maid_Policy_Prem_Adv.pdf'; 
	    $pdffileUrl .= ','.$record[0][0].time().'_Maid_Policy_Prem_Adv.pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile);
		}*/
      } 
         
      //foreign worker medical insurance
      if( $record[0][5] == 'FWMI' ){ 
		$related_records = $wpdb->get_results("SELECT * FROM $table_name WHERE insumem_ID = $applicationID", ARRAY_N); 
		 
	    //writing out the pdf file - work injury compensation insurance 
	    //jacket file
        $sourcefile = $srcfileDirectory.'/_FWMI_Jacket.pdf';
		$pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
		$pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
		$pdf->AddPage(); $tplIdx2 = $pdf->importPage(2); $pdf->useTemplate($tplIdx2, 0, 0, 200); 
		$pdf->AddPage(); $tplIdx3 = $pdf->importPage(3); $pdf->useTemplate($tplIdx3, 0, 0, 200); 
		$pdf->AddPage(); $tplIdx4 = $pdf->importPage(4); $pdf->useTemplate($tplIdx4, 0, 0, 200); 
		//$pdf->AddPage(); $tplIdx5 = $pdf->importPage(5); $pdf->useTemplate($tplIdx5, 0, 0, 200); 
		//$pdf->AddPage(); $tplIdx6 = $pdf->importPage(6); $pdf->useTemplate($tplIdx6, 0, 0, 200); 
        $pdffile = $policyDir.$record[0][0].time().'_FWMI_Jacket.pdf'; 
		$pdffileUrl = $record[0][0].time().'_FWMI_Jacket.pdf';
        $pdf->Output('F',$pdffile);
	    //insert record into db
        $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N);  
	    $pdf_filename = $pdffileUrl;
        $data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $interimrecord[0][0]); 
        $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);	
		if($related_records){
		  $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE insumem_ID = $applicationID", ARRAY_N); 	
		  $pdf_filename = $pdffileUrl;	
		  foreach($interimrecord as $irecord){ 
			$data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $irecord[0]); 
            $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);	  
		  }	
		}
		
		//policy file
        $sourcefile = $srcfileDirectory.'/_FWMI_Policy.pdf';
	    $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
	    $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);	
        //generate particulars 
        $pdf->Text(153,79,$record[0][28]); 
        $pdf->Text(53,85,$record[0][12]); $pdf->Text(53,92,strtoupper($record[0][14]));
        $pdf->Text(53,105,$record[0][21]); $pdf->Text(53,110,strtoupper($record[0][7]));
        $dtarray = explode('-', $record[0][3]); 
        $entrydate = date('d/m/Y', mktime(0,0,0,rtrim($dtarray[1],'0'),$dtarray[2],$dtarray[0]));
        $pdf->Text(53,125,$entrydate); 
        $dt1array = explode('-', $record[0][9]); 
        $expdate = date('d/m/Y', mktime(0,0,0,rtrim($dt1array[1],'0'),$dt1array[2],$dt1array[0]));
        $pdf->Text(80,125,$expdate); $pdf->Text(53,135,ucwords($record[0][16]));
        
        $pdffile = $policyDir.$record[0][0].time().'_FWMI_Policy.pdf'; 
	    $pdffileUrl = $record[0][32].','.$record[0][0].time().'_FWMI_Policy.pdf'; //echo $pdffileUrl;
        $pdf->Output('F',$pdffile); 
        //update db
        $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N);  
	    $pdf_filename = $pdffileUrl;
        $data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $interimrecord[0][0]); 
        $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);	
        
      }
      
      //work injury compensation insurance - _WCI_ERGO_CV_LETTER
      if( $record[0][5] == 'WCS' ){  
	  	 $related_records = $wpdb->get_results("SELECT * FROM $table_name WHERE insumem_ID = $applicationID", ARRAY_N); 
		 
	    //writing out the pdf file - work injury compensation insurance
        $sourcefile = $srcfileDirectory.'/_WCI_ERGO_CV_LETTER.pdf';
        $pdf = new Fpdi(); $pdf->setSourceFile($sourcefile); 
        $pdf->AddPage(); $tplIdx1 = $pdf->importPage(1); $pdf->useTemplate($tplIdx1, 0, 0, 200); 
        $pdf->SetFont("helvetica","B",7); $pdf->SetTextColor(0, 0, 0);
        //generate employer's particulars 
        $pdf->Text(69,79,$record[0][12]); $pdf->Text(69,88,$record[0][14]); $pdf->Text(69,97,$record[0][40]);
        //$pdf->Text(145,68,$record[0][19]); $pdf->Text(140,75,$record[0][31]);
        //period of insurance
        $pdf->Text(69,105,$record[0][8]); $pdf->Text(130,105,$record[0][9]); 
        //generate worker's particulars
        $pdf->Text(50,162,$record[0][21]); $pdf->Text(140,162,$record[0][7]); 
        $premium = $record[0][43];
        if($related_records){	 
          foreach($related_records as $related){ //var_dump($related);
	        $pdf->Text(50,170,$related[21]); $pdf->Text(140,170,$related[7]); 
            $premium += $related[43];
          }
        } 
        $pdf->Text(75,'140.5',$premium); 
        // write on the pdf file 
        $pdffile = $policyDir.$record[0][0].'_WCI_ERGO_CV_LETTER.pdf'; 
		$pdffileUrl = $record[0][0].'_WCI_ERGO_CV_LETTER.pdf'; 
        $pdf->Output('F',$pdffile); 
		  
	    //insert record into db
        $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $applicationID", ARRAY_N);  
	    $pdf_filename = $pdffileUrl;
        $data = array('pdf_filename'=>$pdf_filename); $where = array('id' => $interimrecord[0][0]); 
        $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);	
		if($related_records){
		  $data = array('pdf_filename'=>$pdf_filename); $where = array('insumem_ID' => $interimrecord[0][0]); 
          $updt_result = $wpdb->update($table_name, $data, $where); //var_dump($updt_result);	
		}		  
	  }
	   	         
      //message to generate 
      //$message = __('Test PDF Generated: '.$pdffile.'<br/>', 'custom_table_example');   
   }
}


/**
 * Form for viewing personal details
 * ============================================================================
 *
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function custom_table_example_application_viewform_page_handler()
{
    global $wpdb;
    //$table_name = $wpdb->prefix . 'cte'; // do not forget about tables prefix
    $table_name = 'ins_policy_applicants';
    $message = ''; $notice = '';
    // this is default $item which will be used for new records
    $default = array( 'id' => 0, 'name' => '', 'email' => '', 'age' => null, );
    // here we are verifying does this request is post back 
    // and have correct nonce
    //var_dump($_REQUEST); var_dump($_FILES);

    //insert toggle details in db
    if($_REQUEST['a_exp_type'] != ''){ 
      $data = array('export_type'=>$_REQUEST['a_exp_type']); 
      $where = array('id' => $_REQUEST['id']);
      $updt_result = $wpdb->update($table_name, $data, $where); 
      echo 'Updated'; 
	  header("location: ".$_SERVER['PHP_SELF']);	
	  exit();
    }
    if($_REQUEST['a_p_status'] != ''){
		$data = array('payment_approved'=>$_REQUEST['a_p_status']);
		$where = array('id' => $_REQUEST['id']);
		$updt_result = $wpdb->update($table_name, $data, $where);
		 echo 'Updated'; 
	  header("location: ".$_SERVER['PHP_SELF']);	
	  exit();
	}
	
	//insert master policy no in db
    if($_REQUEST['a_master_pno'] != ''){ 
	  //update main record	
      $data = array('master_policy_no'=>$_REQUEST['a_master_pno']); 
      $where = array('id' => $_REQUEST['id']);
      $updt_result = $wpdb->update($table_name, $data, $where); 
	  //update related records	
      $data = array('guarantee_no'=>$_REQUEST['a_policy_no']); 
      $where = array('insumem_ID' => $_REQUEST['id']);
      $updt_result = $wpdb->update($table_name, $data, $where);		
      echo 'Updated'; 
	  if($_REQUEST['id']){ $appID = $_REQUEST['id'];
		 $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $appID", ARRAY_N); 
		 if(($interimrecord[0][27] != '') && ($interimrecord[0][28] != '')){ 
			 generatePolicyPDFs($appID, 'web_stat'); sendEmail('wic', $appID);
		 }
	  }	
	  $message = __('Item Updated: '.$_REQUEST['id'], 'custom_table_example');			
	  header("location: ".$_SERVER['PHP_SELF']); 
	  exit();
    }	
	
	//insert master policy no in db
    if($_REQUEST['a_policy_no'] != ''){ //var_dump($_REQUEST); exit;
	  //update main record	
      $data = array('guarantee_no'=>$_REQUEST['a_policy_no']); 
      $where = array('id' => $_REQUEST['id']);
      $updt_result = $wpdb->update($table_name, $data, $where);
	  //update related records	
      $data = array('guarantee_no'=>$_REQUEST['a_policy_no']); 
      $where = array('insumem_ID' => $_REQUEST['id']);
      $updt_result = $wpdb->update($table_name, $data, $where); 
      echo 'Updated'; 
	  if($_REQUEST['id']){ $appID = $_REQUEST['id'];
		 $interimrecord = $wpdb->get_results("SELECT * FROM $table_name WHERE id = $appID", ARRAY_N);			  
		 if(($interimrecord[0][27] != '') && ($interimrecord[0][28] != '')){ 
			 generatePolicyPDFs($appID, 'web_stat'); sendEmail('wic', $appID);
		 }
	  }
	  $message = __('Item Updated: '.$_REQUEST['id'], 'custom_table_example');	
	  header("location: ".$_SERVER['PHP_SELF']);
	  exit();
    }
	
    //upload file and enter details in db
    if($_FILES['a_p_upload']['name']){ //var_dump($_FILES); exit;
	  if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
      }
      $uploadedfile = $_FILES['a_p_upload'];
      $upload_overrides = array( 'test_form' => false );
      $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
      if ( $movefile && ! isset( $movefile['error'] ) ) { 
        //var_dump( $movefile ); 
        //update table
        $data = array('pdf_filename'=>$movefile['url']); 
        $where = array('id' => $_REQUEST['id']);
	    $updt_result = $wpdb->update($table_name, $data, $where); 
	    
        $notice .= __('File was successfully uploaded.\n', 'custom_table_example');
        //echo("<meta http-equiv='refresh' content='0'>");
        
      } else {
       /**
        * Error generated by _wp_handle_upload()
        * @see _wp_handle_upload() in wp-admin/includes/file.php
        */
       $notice .= __($movefile['error'], 'custom_table_example');        
       //echo $movefile['error'];
      }
    } 	
    
    //upload form data
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
                if ($result) { $message = __('Item was successfully saved', 'custom_table_example');
                } else { $notice = __('There was an error while saving item', 'custom_table_example'); }
            } else {
                $result = $wpdb->update($table_name, $item, array('id' => $item['id']));
                if ($result) { $message = __('Item was successfully updated', 'custom_table_example');
                } else {  $notice = __('There was an error while updating item', 'custom_table_example'); }
            }
        } else { // if $item_valid not true it contains error message(s)
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
    add_meta_box('application_viewform_meta_box', 'Application data', 'custom_table_example_application_viewform_meta_box_handler', 'application', 'normal', 'default');
    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Application Details', 'custom_table_example')?> 
	<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=applicaions');?>">
	   <?php _e('back to list', 'custom_table_example')?></a>
    </h2>
    <?php if (!empty($notice)): ?><div id="notice" class="error"><p><?php echo $notice ?></p></div><?php endif;?>
    <?php if (!empty($message)): ?><div id="message" class="updated"><p><?php echo $message ?></p></div><?php endif;?>

    <form id="view-form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                <?php /* And here we call our custom meta box */ ?>
                <?php do_meta_boxes('application', 'normal', $item); ?>
                <!--<input type="submit" value="<?php //_e('Save', 'custom_table_example')?>" id="submit" name="submit"
			class="button-primary">-->
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
function custom_table_example_application_viewform_meta_box_handler($item) { ?>
<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
<tbody>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_id"><?php _e('ID', 'custom_table_example')?></label></th>
        <td><input id="a_id" name="a_id" type="text" style="width: 85%" value="<?php echo esc_attr($item['id'])?>"
                   size="50" class="code" placeholder="<?php _e('ID', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_id"><?php _e('Billing To', 'custom_table_example')?></label></th>
        <td><input id="a_bill_to" name="a_bill_t0" type="text" style="width: 85%" value="<?php echo esc_attr($item['bill_to'])?>"
                   size="50" class="code" placeholder="<?php _e('Billing To Account', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_e_date"><?php _e('Entry Date', 'custom_table_example')?></label></th>
        <td><input id="a_e_date" name="a_e_date" type="text" style="width: 85%" value="<?php echo esc_attr($item['entry_date'])?>"
                   size="50" class="code" placeholder="<?php _e('Entry Date', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_class"><?php _e('Appln Class', 'custom_table_example')?></label></th>
        <td><input id="a_class" name="a_class" type="text" style="width: 85%" value="<?php echo esc_attr($item['ins_class'])?>"
                   size="50" class="code" placeholder="<?php _e('Appln Class', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_wp_no"><?php _e('Work Permit No', 'custom_table_example')?></label></th>
        <td><input id="a_wp_no" name="a_wp_no" type="text" style="width: 85%" value="<?php echo esc_attr($item['wp_no'])?>"
                   size="50" class="code" placeholder="<?php _e('Work Permit No', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_eff_date"><?php _e('Effective Date', 'custom_table_example')?></label></th>
        <td><input id="a_eff_date" name="a_eff_date" type="text" style="width: 85%" value="<?php echo esc_attr($item['eff_date'])?>"
                   size="50" class="code" placeholder="<?php _e('Effective Date', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_exp_date"><?php _e('Expiry Date', 'custom_table_example')?></label></th>
        <td><input id="a_exp_date" name="a_exp_date" type="text" style="width: 85%" value="<?php echo esc_attr($item['exp_date'])?>"
                   size="50" class="code" placeholder="<?php _e('Expiry Date', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_pai_date"><?php _e('Pai Date', 'custom_table_example')?></label></th>
        <td><input id="a_pai_date" name="a_pai_date" type="text" style="width: 85%" value="<?php echo esc_attr($item['pai_date'])?>"
                   size="50" class="code" placeholder="<?php _e('Pai Date', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_party_name"><?php _e('Party Name', 'custom_table_example')?></label></th>
        <td><input id="a_party_name" name="a_party_name" type="text" style="width: 85%" value="<?php echo esc_attr($item['ins_party_nm'])?>"
                   size="50" class="code" placeholder="<?php _e('Party Name', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_party_nric"><?php _e('Party Nric', 'custom_table_example')?></label></th>
        <td><input id="a_party_nric" name="a_party_nric" type="text" style="width: 85%" value="<?php echo esc_attr($item['ins_party_nric'])?>"
                   size="50" class="code" placeholder="<?php _e('Party Nric', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_addr1"><?php _e('Address 1', 'custom_table_example')?></label></th>
        <td><input id="a_addr1" name="a_addr1" type="text" style="width: 85%" value="<?php echo esc_attr($item['addr1'])?>"
                   size="50" class="code" placeholder="<?php _e('Address 1', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_addr2"><?php _e('Address 2', 'custom_table_example')?></label></th>
        <td><input id="a_addr2" name="a_addr2" type="text" style="width: 85%" value="<?php echo esc_attr($item['addr2'])?>"
                   size="50" class="code" placeholder="<?php _e('Address 2', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_occup_text"><?php _e('Unit No', 'custom_table_example')?></label></th>
        <td><input id="a_unit_no" name="a_unit_no" type="text" style="width: 85%" value="<?php echo esc_attr($item['unit_no'])?>"
                   size="50" class="code" placeholder="<?php _e('Unit No', 'custom_table_example')?>" required></td>
    </tr>      	
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_country"><?php _e('Country', 'custom_table_example')?></label></th>
        <td><input id="a_country" name="a_country" type="text" style="width: 85%" value="<?php echo esc_attr($item['country'])?>"
                   size="50" class="code" placeholder="<?php _e('Country', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_postal"><?php _e('Postal', 'custom_table_example')?></label></th>
        <td><input id="a_postal" name="a_postal" type="text" style="width: 85%" value="<?php echo esc_attr($item['postal'])?>"
                   size="50" class="code" placeholder="<?php _e('Postal', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_c_person"><?php _e('Contact Person', 'custom_table_example')?></label></th>
        <td><input id="a_c_person" name="a_c_person" type="text" style="width: 85%" value="<?php echo esc_attr($item['contact_person'])?>"
                   size="50" class="code" placeholder="<?php _e('Contact Person', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_ph"><?php _e('Phone No', 'custom_table_example')?></label></th>
        <td><input id="a_ph" name="a_ph" type="text" style="width: 85%" value="<?php echo esc_attr($item['ph_no'])?>"
                   size="50" class="code" placeholder="<?php _e('Phone No', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_fax"><?php _e('Fax No', 'custom_table_example')?></label></th>
        <td><input id="a_fax" name="a_fax" type="text" style="width: 85%" value="<?php echo esc_attr($item['fax_no'])?>"
                   size="50" class="code" placeholder="<?php _e('Fax No', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_email"><?php _e('Email', 'custom_table_example')?></label></th>
        <td><input id="a_email" name="a_email" type="text" style="width: 85%" value="<?php echo esc_attr($item['email'])?>"
                   size="50" class="code" placeholder="<?php _e('Email', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_person"><?php _e('Person In Appln', 'custom_table_example')?></label></th>
        <td><input id="a_person" name="a_person" type="text" style="width: 85%" value="<?php echo esc_attr($item['ins_person'])?>"
                   size="50" class="code" placeholder="<?php _e('Person In Appln', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_p_dob"><?php _e('Date Of Birth', 'custom_table_example')?></label></th>
        <td><input id="a_p_dob" name="a_p_dob" type="text" style="width: 85%" value="<?php echo esc_attr($item['dob'])?>"
                   size="50" class="code" placeholder="<?php _e('Date Of Birth', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_nationality"><?php _e('Nationality', 'custom_table_example')?></label></th>
        <td><input id="a_nationality" name="a_nationality" type="text" style="width: 85%" value="<?php echo esc_attr($item['nationality'])?>"
                   size="50" class="code" placeholder="<?php _e('Nationality', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_passport"><?php _e('Passport', 'custom_table_example')?></label></th>
        <td><input id="a_passport" name="a_passport" type="text" style="width: 85%" value="<?php echo esc_attr($item['passport'])?>"
                   size="50" class="code" placeholder="<?php _e('Passport', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_policy_no"><?php _e('Policy No', 'custom_table_example')?></label></th>
        <td><input id="a_policy_no" name="a_policy_no" type="text" style="width: 85%" value="<?php echo esc_attr($item['guarantee_no'])?>"
                   size="50" class="code" placeholder="<?php _e('Policy No', 'custom_table_example')?>" required>
			<?php if( ($item['ins_class'] == 'WCS') && ($item['insumem_ID'] == '0') ) { ?>
			<p><a style="text-decoration:none;padding:5px;background:#EFEFEF;color:#000;border:1px solid #AAA;" href="#" id="btnSavePolicyNo" 
				  name="btnSavePolicyNo">Save Policy No</a></p>
			<?php } ?> 
		</td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_master_pno"><?php _e('Master Policy No', 'custom_table_example')?></label></th>
        <td><input id="a_master_pno" name="a_master_pno" type="text" style="width: 85%" value="<?php echo esc_attr($item['master_policy_no'])?>"
                   size="50" class="code" placeholder="<?php _e('Master Policy No', 'custom_table_example')?>" required> 
			<?php if( ($item['ins_class'] == 'WCS') && ($item['insumem_ID'] == '0') ){ ?>
			  <p><a style="text-decoration:none;padding:5px;background:#EFEFEF;color:#000;border:1px solid #AAA;" href="#" id="btnSaveMasterPolicyNo" 
					name="btnSaveMasterPolicyNo">Save Master Policy No</a></p>
		    <?php } ?> 
		</td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_exp_type"><?php _e('Export Type', 'custom_table_example')?></label></th>
        <td>
		<select id="a_exp_type" name="a_exp_type" style="width: 85%">
	  		<option value="" <?php if($item['export_type'] == ''){ echo 'selected="selected"'; } ?>>Toggle Export</option>
	  		<option value="new" <?php if($item['export_type'] == 'new'){ echo 'selected="selected"'; } ?>>New</option>
	  		<option value="pending" <?php if($item['export_type'] == 'pending'){ echo 'selected="selected"'; } ?>>Pending</option>
	  		<option value="completed" <?php if($item['export_type'] == 'completed'){ echo 'selected="selected"'; } ?>>Completed</option>
		</select>			
		<!--<input id="a_exp_type" name="a_exp_type" type="text" style="width: 85%" value="<?php //echo esc_attr($item['export_type'])?>" 
		            size="50" class="code" placeholder="<?php //_e('Export Type', 'custom_table_example')?>" required>-->
		</td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_p_file"><?php _e('Policy File', 'custom_table_example')?></label></th>
        <td>
		<?php if(isset($item['pdf_filename'])){ 
		  $files =  explode(',' , $item['pdf_filename']); 
		  foreach($files as $plfile){ $name = str_replace('.pdf', '', $plfile); ?>  
		  <p><a target=_blank href="<?php echo wp_upload_dir()['baseurl']; ?>/policy_uploads/<?php echo $plfile;?>"><?php echo $name; ?></a></p> 
	   <?php }} ?>				
		<!--<input id="a_p_file" name="a_p_file" type="text" style="width: 85%" value="<?php //echo esc_attr($item['pdf_filename'])?>" size="50" class="code" placeholder="<?php //_e('Policy File', 'custom_table_example')?>" required>
        <input id="a_p_upload" name="a_p_upload" type="file" style="width: 40%" accept=".pdf">-->
       </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_occupation"><?php _e('Occupation', 'custom_table_example')?></label></th>
        <td><input id="a_occupation" name="a_occupation" type="text" style="width: 85%" value="<?php echo esc_attr($item['occupation'])?>"
                   size="50" class="code" placeholder="<?php _e('Occupation', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_occup_text"><?php _e('Occupation Text', 'custom_table_example')?></label></th>
        <td><input id="a_occup_text" name="a_occup_text" type="text" style="width: 85%" value="<?php echo esc_attr($item['occupation_text'])?>"
                   size="50" class="code" placeholder="<?php _e('Occupation Text', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_bus_nature"><?php _e('Business Nature', 'custom_table_example')?></label></th>
        <td><input id="a_bus_nature" name="a_bus_nature" type="text" style="width: 85%" value="<?php echo esc_attr($item['business_nature'])?>"
                   size="50" class="code" placeholder="<?php _e('Business Nature', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_p_select"><?php _e('Plan Selection', 'custom_table_example')?></label></th>
        <td><input id="a_p_select" name="a_p_select" type="text" style="width: 85%" value="<?php echo esc_attr($item['plan_selection'])?>"
                   size="50" class="code" placeholder="<?php _e('Plan Selection', 'custom_table_example')?>" required></td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row"><label for="a_total"><?php _e('Total Premium Amt', 'custom_table_example')?></label></th>
        <td><input id="a_total" name="a_total" type="text" style="width: 85%" value="<?php echo esc_attr($item['Total_premium_amt'])?>"
                   size="50" class="code" placeholder="<?php _e('Total Premium Amt', 'custom_table_example')?>" required></td>
    </tr>
     <tr class="form-field">
        <th valign="top" scope="row"><label for="a_p_status"><?php _e('Payment Status', 'custom_table_example')?></label></th>
        <td>
        <?php 
        $status=$item['export_type'];
        if($status=="new"){
        ?>
        	<select id="a_p_status" name="a_p_status" style="width: 85%">
	  		<option value="0" <?php if($item['payment_approved'] == '0'){ echo 'selected="selected"'; } ?>>Pending</option>
	  		<option value="1" <?php if($item['payment_approved'] == '1'){ echo 'selected="selected"'; } ?>>Completed</option>
		</select>
		<?php }else{ ?>
		<input id="pstatus" name="pstatus" type="text" style="width: 85%" value="Completed" size="50" class="code" disabled="disabled" >
		<?php } ?>
        </td>
    </tr>
</tbody>
</table>
<script>
jQuery("#a_exp_type").change(function(){ 
 var selected = this; //console.log(selected.value);
 var formdata = new FormData();
 if(selected.value != ''){ formdata.append('a_exp_type',selected.value); }
 if(selected.value != ''){ 
  jQuery.ajax({
    url: "<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id']); ?>",
    data: formdata,
    type: 'POST',
    dataType:"JSON",
    cache: false,
    contentType: false,
    processData: false,
    success:function(response){ 
	 //console.log(response); 
	 window.location.reload();
    },
  });
 }//end if
});
// For Payment status
jQuery("#a_p_status").change(function(){ 
//alert(2);
var selected = this;
var formdata = new FormData();
if(selected.value != ''){ formdata.append('a_p_status',selected.value); }
if(selected.value != ''){ 
jQuery.ajax({
    url: "<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id']); ?>",
    data: formdata,
    type: 'POST',
    dataType:"JSON",
    cache: false,
    contentType: false,
    processData: false,
    success:function(response){ 
	 //console.log(response); 
	 window.location.reload();
    },
  });
}
//alert(selected.value);
});	
jQuery("#a_p_upload").change(function(){ 
   //jQuery('#view-form').submit();
   var input = this; //var url = input.value; 
   //console.log(input); //console.log(input.files);
   formdata = new FormData();
   if(input.files.length > 0){
     file = input.files[0]; console.log(file);
     formdata.append('a_p_upload',file);
   }
   jQuery.ajax({
      url: "<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id']); ?>",
      data:formdata,
      type: 'POST',
      dataType:"JSON",
      cache: false,
      contentType: false,
      processData: false,
      success:function(data){ 
        if(data.success == true){ // if true (1)
            setTimeout(function(){// wait for 5 secs(2)
                location.reload(); // then reload the page.(3)
            }, 5000); 
        }
      },
   });
});
	
jQuery("#btnSaveMasterPolicyNo").click(function(){
 var masterpolicyno = jQuery('#a_master_pno').val();	
 var formdata = new FormData();
 if(masterpolicyno != ''){ formdata.append('a_master_pno',masterpolicyno); }
 if(masterpolicyno != ''){ 
  jQuery.ajax({
    url: "<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id']); ?>",
    data: formdata,
    type: 'POST',
    dataType:"JSON",
    cache: false,
    contentType: false,
    processData: false,
    success:function(response){ 
	  //console.log(response); 	
	  window.location.reload();
    },
  });
 }//end if	
});

jQuery("#btnSavePolicyNo").click(function(){
 var policyno = jQuery('#a_policy_no').val(); //alert(policyno);
 var formdata = new FormData();
 if(policyno != ''){ formdata.append('a_policy_no',policyno); }
 if(policyno != ''){ 
  jQuery.ajax({
    url: "<?php echo admin_url('admin.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id']); ?>",
    data: formdata,
    type: 'POST',
    dataType:"JSON",
    cache: false,
    contentType: false,
    processData: false,
    success:function(response){ 
	  //console.log(response); 
	  //console.log('success');
	  window.location.reload();	
    },
  });
 }//end if		
});
</script>
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
    <h2><?php _e('Application', 'custom_table_example')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=persons');?>"><?php _e('back to list', 'custom_table_example')?></a>
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
{ ?>
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
