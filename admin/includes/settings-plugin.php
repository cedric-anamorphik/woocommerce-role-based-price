<?php
    
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



class WC_RBP_PLUGINS extends WP_List_Table {
    
    /** ************************************************************************
     * Normally we would be querying data from a database and manipulating that
     * for use in your list table. For this example, we're going to simplify it
     * slightly and create a pre-built array. Think of this as the data that might
     * be returned by $wpdb->query()
     * 
     * In a real-world scenario, you would make your own custom query inside
     * this class' prepare_items() method.
     * 
     * @var array 
     **************************************************************************/
    
    var $example_data = array(
            array(
                'title'     => 'WP All Importer Intergation',
                'description'    => 'Adds Option To Import Products With Role Based Pricing In WP All Importer <br/>
<a href="http://www.wpallimport.com/" >Go To Plugin Website -> </a> ',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => 'WP All Import - WooCommerce Add-On Pro',
                'actions' => 'wpai-woocommerce-add-on/wpai-woocommerce-add-on.php',
                'update' => '',
                'file' => 'class-wp-all-import-pro-intergation.php',
                'slug' => 'wpallimport'
            ),
            array(
                'title'     => 'Aelia Currency Switcher Intergation',
                'description'    => 'Adds Option Set Product Price Based On Currency Choosen <br/> <a href="https://aelia.co/shop/currency-switcher-woocommerce/" >Go To Plugin Website -> </a>',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => 'Aelia Currency Switcher for WooCommerce',
                'actions' => 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php',
                'update' => '',
                'file' => 'class-aelia-currencyswitcher-intergation.php',
                'slug' => 'aeliacurrency'
            ),
            array(
                'title'     => 'Aelia Currency Switcher Intergation [WP ALL Import]',
                'description'    => 'Intergates Aelia Currency Switcher With WP All Import Plugin',
                'author'  => '<a href="http://varunsridharan.in">  Varun Sridharan</a>',
                'required' => array('Aelia Currency Switcher for WooCommerce','WP All Import - WooCommerce Add-On Pro'),
                'actions' => array('woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php','wpai-woocommerce-add-on/wpai-woocommerce-add-on.php'),
                'update' => '',
                'file' => 'class-wc-rbp-wp-all-import-aelia-intergation.php',
                'slug' => 'aeliacurrency_wpallimport'
            )
        );

    
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }


    function column_default($item, $column_name){ 
        switch($column_name){
                
            case 'rating':
            case 'director':
                return $item[$column_name];
            default:
                //return print_r($item,true); //Show the whole array for troubleshooting purposes
                return $item[$column_name];
        }
    }

    function column_title($item){
        return sprintf('<strong> %1$s </strong>',
        /*$1%s*/ $item['title']
        );
    } 

    function column_required($item){
        $name = '';
        if(is_array($item['required'])){
            $name = implode(', <br/>',$item['required']);
        } else {
            $name = $item['required'];
        }
        return sprintf('<strong> %1$s </strong>',
        /*$1%s*/ $name
        );
    }
    
    function column_actions($item){
        $action = '<span style="color:red"> <strong> Install The Required Plugin First</strong> </span>';
        if(is_array($item['actions'])){
            $active_file = 0;
            foreach($item['actions'] as $plugin_file){
                if(is_plugin_active($plugin_file)){ 
                   $active_file++;  
                } else { 
                    $action = '<span style="color:red"> <strong> Install The Required Plugin First </strong> </span>';
                }
            }
            
            if($active_file == count($item['actions'])){
                
                if(in_array($item['file'],WC_RBP()->get_activated_plugin())){
                    $action = '<a href="'.admin_url('admin.php?page=wc-settings&tab='.pp_key.'&section=plugin&action=deactivate_plugin&plugin-key='.$item['file'].'&ps='.$item['slug']).'" class="button"> De-Activate </a>';
                } else {
                    $action = '<a href="'.admin_url('admin.php?page=wc-settings&tab='.pp_key.'&section=plugin&action=activate_plugin&plugin-key='.$item['file'].'&ps='.$item['slug']).'" class="button button-primary">Activate </a>';
                }       
            }
            
        } else {
            if(is_plugin_active($item['actions'])){ $action =  $this->check_plugin_action($item); } 
            else { $action = '<span style="color:red"> <strong>  Install The Required Plugin First </strong> </span>'; }
        }
        return $action;
    }
    

    
    function check_plugin_action($item){
        $action = '';
        if(in_array($item['file'],WC_RBP()->get_activated_plugin())){
            
            $action = '<a href="'.admin_url('admin.php?page=wc-settings&tab='.pp_key.'&section=plugin&action=deactivate_plugin&plugin-key='.$item['file'].'&ps='.$item['slug']).'" class="button"> De-Activate </a>';
        } else {
            $action = '<a href="'.admin_url('admin.php?page=wc-settings&tab='.pp_key.'&section=plugin&action=activate_plugin&plugin-key='.$item['file'].'&ps='.$item['slug']).'" class="button button-primary">Activate </a>';
        }
        return $action;
    }
    

    function get_columns(){
        $columns = array( 
            'title'     => 'Title',
            'description'    => 'Description',
            'author'  => 'Author',
            'required' => 'Required Plugins',
            'update' => 'Last Update',
            'actions' => 'Actions'
        );
  
        return $columns;
    }


    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',true),     //true means it's already sorted
            'status'    => array('status',false),
        );
        return $sortable_columns;
    }


    
    function get_bulk_actions() {
        $actions = array();
        return $actions;
    }

    function process_bulk_action() {
        if('activate_plugin' === $this->current_action()){
            $activate_plugin = WC_RBP()->get_activated_plugin();
            
            if(! isset($activate_plugin[$_REQUEST['ps']])){
                $activate_plugin[$_REQUEST['ps']] = $_REQUEST['plugin-key'];
            } 
           
            update_option(rbp_key.'activated_plugin',$activate_plugin);
        }
        
        if('deactivate_plugin' ===  $this->current_action()){
            $activate_plugin = WC_RBP()->get_activated_plugin();
            $i = 0;
            $count = count($activate_plugin); 
            
            if(isset($activate_plugin[$_REQUEST['ps']])){
                unset($activate_plugin[$_REQUEST['ps']]);
            } 
            
            update_option(rbp_key.'activated_plugin',$activate_plugin);
        }
        
        //Detect when a bulk action is being triggered...
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
        
    }

 
    function prepare_items() {
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = $this->example_data;
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);        $this->items = $data;
 
    }


}
 
    
    $testListTable = new WC_RBP_PLUGINS();
    $testListTable->prepare_items();

    ?>
    <div class="wrap">
            <?php $testListTable->display() ?>
    </div> 
<style>
    div.tablenav {display:none;}
</style>