// =======================================
//  wP List Table 
// =======================================
class Kv_subscribers_list extends WP_List_Table {

function __construct(){
    global $status, $page;                

    parent::__construct( array(
        'singular'  => 'notification',  
        'plural'    => 'notifications',   
        'ajax'      => false      
    ) );        
}

protected function get_views() { 
    $status_links = array(
        "all"       => __("<a href='#'>All</a>",'my-plugin-slug'),
        "published" => __("<a href='#'>Published</a>",'my-plugin-slug'),
        "trashed"   => __("<a href='#'>Trashed</a>",'my-plugin-slug')
    );
    return $status_links;
}

function column_default($item, $column_name){
    switch($column_name){
        case 'email':
        case 'date':
        case 'common':          
        case 'unit_id':          
            return $item[$column_name];
        default:
            return print_r($item,true); //Show the whole array for troubleshooting purposes
    }
}

function column_email($item){       
    $actions = array(
        'email'     => sprintf('<a href="?page=%s&action=%s&email_id=%s">E-mail</a>',$_REQUEST['page'],'email',$item['id']),
        'delete'    => sprintf('<a href="?page=%s&action=%s&delete_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
    );

    //Return the title contents
    return sprintf('%1$s %2$s',
        /*$1%s*/ $item['email'],          
        /*$2%s*/ $this->row_actions($actions)
    );
}

function column_cb($item){
    return sprintf(
        '<input type="checkbox" name="%1$s[]" value="%2$s" />',
        /*$1%s*/ $this->_args['singular'],  
        /*$2%s*/ $item['id']                
    );
}

function get_columns(){
    $columns = array(
        'cb'        => '<input type="checkbox" />', 
        'email'=>__('Email'),  
        'date'=>__('Date'),  
        'common'=>__('Common Alert'),           
        'unit_id'=>__('Unique ID')
    );
    return $columns;
}
public function get_sortable_columns() {
    $sortable_columns = array(
        'email'   => array('wp_user_id',false),     //true means it's already sorted
        'date'    => array('date',false),
        'common'  => array('common',false)
    );
    return $sortable_columns;
}
public function get_bulk_actions() {
    $actions = array(
        'delete'    => 'Delete',
        'email'     => 'Email'
    );
    return $actions;
}

public function process_bulk_action() {

    global $wpdb; 
    $notifications_tbl = $wpdb->prefix.'newsletter';

    if( 'delete'===$this->current_action() ) {
        foreach($_POST['notification'] as $single_val){
            $wpdb->delete( $notifications_tbl, array( 'id' =>    (int)$single_val ) );             
        }
        $redirect_url =  get_admin_url( null, 'admin.php?page=subscribers' );
        wp_safe_redirect($redirect_url); 
        wp_die('Items deleted (or they would be if we had items to delete)!');
    } 
    if( 'email'===$this->current_action() ) {           
            $result_email_ar = implode("-",$_POST['notification']);
        $redirect_url =  get_admin_url( null, 'admin.php?page=kvcodes&ids='.$result_email_ar  );
        wp_safe_redirect($redirect_url);        

        wp_die('  ');
    }       
}

function prepare_items() {
    global $wpdb; //This is used only if making any database queries
    $database_name = $wpdb->prefix.'newsletter' ;
    $per_page = 10;
    $query = "SELECT * FROM $database_name ORDER BY id DESC";

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();

    $this->_column_headers = array($columns, $hidden, $sortable);        

    $this->process_bulk_action();

    $data =  $wpdb->get_results($query, ARRAY_A );

    function usort_reorder($a,$b){
        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
        $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
        return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
    }
   // usort($data, 'usort_reorder');        

    $current_page = $this->get_pagenum();        

    $total_items = count($data);

    $data = array_slice($data,(($current_page-1)*$per_page),$per_page); 

    $this->items = $data;

    $this->set_pagination_args( array(
        'total_items' => $total_items,                  //WE have to calculate the total number of items
        'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
        'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
    ) );
}
}//class

// ==============================================================================================================
//admin page rendering
//========================
function o_add_menu_items(){
    add_menu_page('Plugin List Table', 'Sub', 'activate_plugins', 'subscribers', 'o_render_list_page');
} 
add_action('admin_menu', 'o_add_menu_items');

function o_render_list_page() {
    $mydownloads = new Kv_subscribers_list();
    $title = __("Subscribers","my_plugin_slug");
    ?>
    <div class="wrap">
        <h1>
            <?php echo esc_html( $title );?>
             <!-- Check edit permissions -->
             <a href="<?php echo admin_url( 'admin.php?page=subscribers&add_new=true' ); ?>" class="page-title-action">
                <?php echo esc_html_x('Add New', 'my-plugin-slug'); ?>
            </a>
            <?php
            ?>
        </h1>
    </div>
    <?php $mydownloads->views(); ?>
    <form method="post">
    <?php 

        $mydownloads->prepare_items(); 
        $mydownloads->display();
    ?>
    </form>
<?php
}

====================================================
// view methods
//=========================
add_filter('views_toplevel_page_subscribers','my_plugin_slug_status_links',10, 1);
function my_plugin_slug_status_links($views) {
   $views['scheduled'] =  "<a href='#'>Scheduled</a>";
   return $views;
}


