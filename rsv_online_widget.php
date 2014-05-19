<?php
/**
 * Widgets for Reserve Online for Wordpress
 * @author		Sandi Andrian
 * @version		0.1
 * @package		ca_rsv_online
 **/

// Creating the widget 
class rsv_online_widget extends WP_Widget 
{

	public $current_language;

	function __construct() 
	{
		parent::__construct(
			// Base ID of your widget
			'rsv_online_widget', 

			// Widget name will appear in UI
			__('Reserve Online Widget', TEXTDOMAIN), 

			// Widget description
			array('description' => __('Reserve Online Widget', 'rsv_online_widget_domain'),) 
		);

		$this->current_language = qtrans_getLanguage();
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget($args, $instance) 
	{

		$page 	 		= get_post($instance['rsv_online_page_id']);
		$page_slug 		= basename(get_permalink($instance['rsv_online_page_id']));
		$page_uri 		= get_page_uri($instance['rsv_online_page_id']); 
		$title 			= get_field("widget_settings_title",$instance['rsv_online_page_id']);
		$description 	= get_field("widget_settings_description",$instance['rsv_online_page_id']);
		$image 			= get_field("widget_settings_image",$instance['rsv_online_page_id']);
		$section_style	= ($image != "") ? 'style="background-image: url('.$image.')"' : '';

		//render the widgets
		echo $args['before_widget'];

		//
		echo '<a href="'.get_site_url().'/'.$page_slug.'" class="btn btn-block btn-warning rsv-online-btn-small">'.$instance['rsv_online_text'].'</a>';

		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) 
	{
		//get pages
		$pages = get_pages();

		if(isset( $instance['rsv_online_page_id'])) 
			$title = $instance['rsv_online_page_id'];
		else 
			$title = __('Page', 'wpb_widget_domain');
		// Widget admin form
	?>
		<p>
			<label for="<?php echo $this->get_field_id('rsv_online_page_id'); ?>"><?php _e('Select Page:'); ?></label> 
			<select name="<?php echo $this->get_field_name('rsv_online_page_id'); ?>" style="width: 100%">
				<option value="" <?php echo ($instance['rsv_online_page_id'] == "") ? 'selected="selected"' : ''; ?>>-- Choose One --</option>
				<?php foreach($pages as $p): ?>
					<option value="<?php echo $p->ID; ?>" <?php echo ($instance['rsv_online_page_id'] == $p->ID) ? 'selected="selected"' : ''; ?>><?php echo $p->post_title; ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('rsv_online_text'); ?>"><?php _e('Text Button:'); ?></label> 
			<input type="text" name="<?php echo $this->get_field_name('rsv_online_text'); ?>" value="<?php echo ($instance['rsv_online_text']) ? $instance['rsv_online_text'] : ''; ?>">
		</p>
	<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) 
	{
		$instance = array();
		$instance['rsv_online_page_id'] = ( ! empty( $new_instance['rsv_online_page_id'] ) ) ? strip_tags($new_instance['rsv_online_page_id']) : '';
		$instance['rsv_online_text'] = ( ! empty( $new_instance['rsv_online_text'] ) ) ? strip_tags($new_instance['rsv_online_text']) : '';
		return $instance;
	}
} // Class wpb_widget ends here

// Register and load the widget
function rsv_online_load_widget() 
{
	register_widget('rsv_online_widget');
}
add_action('widgets_init', 'rsv_online_load_widget');