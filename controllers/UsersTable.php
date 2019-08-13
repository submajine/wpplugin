<?php
namespace adminmeta;

class UsersTable extends WP_List_Table{

    public function __construct()
    {

        parent::__construct($args = array(
            'plural'=>'users',
            'singular'=>'user',
            'ajax'=>false
        ));

    }
    public function column_default($item, $column_name)
    {
       switch ($column_name){
           case 'id':
           case 'display_name':
               return $item[$column_name];
           default:
               return print_r($item,true);

       }
    }
    public function column_display_name($item){
        $actions = [
            'showinfo' => sprintf('<a href="?page=%s&action=%s&user=%s">View</a>',
            $_REQUEST['page'],'show', $item['display_name'])
        ];
        return sprintf('%1$s 
        %2$s', $item['display_name'],$this->row_actions($actions)
        );
    }

    public function get_columns(){
        $usersTableColumns = [
                'id'=>'id',
                'display_name'=>'Users'];
        return $usersTableColumns;
    }
    public function prepare_items(){
        global $wpdb;
        $per_page=3;
        $userlist = $wpdb->get_results("
        select ID,display_name
        from $wpdb->users",
            ARRAY_A);
        $columns = $this->get_columns();
        $hidden = ['id'];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns,
            $hidden,
            $sortable];


        $current_page = $this->get_pagenum();
        $total_items = count($userlist);
        $data = array_slice($userlist,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ] );
    }


    public function no_items()
    {
        _e('Something goes wrong!=(');
    }




}
