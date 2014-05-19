<?php
/**
 * Helper File
 **/

function ca_rsv_online_get_package_name($package)
{
	global $wpdb;
	global $q_config;

	$current_lang = $q_config['language'];

	/*
	$query = "SELECT post_title 
	          FROM wp_posts
	          WHERE post_type = 'packages' 
	          		AND post_name = '$package'
	          		AND post_status = 'publish'
	          LIMIT 1";
	*/
	$query = "SELECT post_title 
			  FROM wp_posts 
			  WHERE post_type = 'packages' 
				  AND id IN (SELECT post_id
				                  FROM wp_postmeta 
				                  WHERE meta_key = '_qts_slug_{$current_lang}'
				                        AND meta_value = '{$package}')
				  AND post_status = 'publish' LIMIT 1";

	$result = $wpdb->get_results($query);
	//echo $wpdb->last_query; exit;
	//print_r($result); exit;
	if($result)
		return qtrans_use($current_lang, $result[0]->post_title, true);
	else
		return null;
}

/**
 * Print Error Message
 **/
function ca_rsv_online_print_message($type,$message)
{
	if($type == 'error')
		return '<div class="alert alert-danger">'.$message.'</div>';
	elseif($type == 'success')
		return '<div class="alert alert-success">'.$message.'</div>';
}

/**
 * Formatting Date
 **/
function ca_rsv_online_format_date($date)
{
	$date = new DateTime($date);
	return $date->format("d M Y");
}