<?php
if( ! class_exists( 'WP_List_Table' ))
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

//reusing reservation table as wp_list_table
class reservationTable extends WP_List_Table
{

	public $found_data;

	public function get_columns()
	{
		return array(
					//'cb'        		=> '<input type="checkbox">',
					'name' 				=> 'Name',
					'email' 			=> 'Email Address',
					'phone' 			=> 'Phone',
					'date_of_arrival' 	=> 'Date of Arrival',
					'guest'				=> 'Guests',
					'package'			=> 'Package',
					'message'			=> 'Message'
			   );
	}

	public function get_sortable_columns()
	{
		return array(
					'name'	=> array('name',false),
					'date_of_arrival' => array('date_of_arrival',false),
					'package'	=> array('package',false)
			   );
	}

	function usort_reorder( $a, $b ) 
	{
	  // If no sort, default to title
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'date_of_arrival';
	  // If no order, default to asc
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
	  // Determine sort order
	  $result = strcmp( $a[$orderby], $b[$orderby] );
	  // Send final sort direction to usort
	  return ( $order === 'asc' ) ? $result : -$result;
	}

	public function prepare_items()
	{
		global $wpdb;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns,$hidden,$sortable);
		//usort($data, array(&$this, 'usort_reorder'));

		//pagination
		$per_page = 15;
		$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
		//Page Number
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ) $paged = 1;

		$offset = ($paged-1) * $per_page;

		//get number of data
		$query = "SELECT name,email,phone,date_of_arrival,guest,package,message
		          FROM wp_rsv_online";

		//searched query
		$s = $_POST['s'];
		if($s != "")
		{
			$query .= " WHERE name LIKE '%{$s}%'
							  OR phone LIKE '%{$s}%'
							  OR package LIKE '%{$s}%'
							  OR message LIKE '%{$s}%'";
		}

		$total_items = $wpdb->query($query);

		
        //How many pages do we have in total?
        $total_pages = ceil($total_items/$per_page);

        //Parameters that are going to be used to order the result
       	$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
       	$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
       	if(!empty($orderby) & !empty($order))
       	{ 
       		$query .= " ORDER BY {$orderby} {$order}"; 
       	}
       	else
       	{
       		//order default
       		$query .= " ORDER BY date_of_arrival DESC";
       	}

		//adjust the query to take pagination into account
       	if(!empty($paged) && !empty($per_page))
       	{
          	$offset = ($paged-1)*$per_page;
         	$query .= ' LIMIT '.(int)$offset.','.(int)$per_page;
       	}

       	//echo $query; exit;

		$this->set_pagination_args(array(
		    'total_items' => $total_items,                  
		    'per_page'    => $per_page,
		    'total_pages' => $total_pages
		));

		//$this->items = $this->found_data;
		$this->items = $wpdb->get_results($query, ARRAY_A);
	}	


	function column_cb($item) 
	{
        return sprintf(
            '<input type="checkbox" name="book[]" value="%s">', $item['id']
        );    
    }

	function column_default($item,$column_name) 
	{
	  switch( $column_name ) { 
	    case 'name':
	    case 'email':
	    case 'phone':
	    case 'guest': 
	    case 'message':
	      return $item[$column_name];
	    case 'date_of_arrival':
	      return ca_rsv_online_format_date($item[$column_name]);
	    case 'package':
	      return ca_rsv_online_get_package_name($item[$column_name]);
	    default:
	      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}
}
?>

<div class="wrap">
	<h2 style>All Reservations</h2>
	<?php
		//display
		$myReservationTable = new reservationTable();
		$myReservationTable->prepare_items();
	?>
		<form method="post">
  			<input type="hidden" name="page" value="ca_rsv_online_list_reservations">
  			<?php $myReservationTable->search_box('Search', 'search_id'); ?>
		</form>
	<?php
		$myReservationTable->display();
  	?>
</div>