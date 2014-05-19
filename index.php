<?php
/**
 * Plugin Name: Reserve Online Form for Wordpress
 * Plugin URI: http://coderalliance.com
 * Description: Build reserve online form for your Wordpress sites.
 * Version: 0.1
 * Author: Sandi Andrian
 * Author URI: http://coderalliance.com
 * License: A "Slug" license name e.g. GPL2
 *
 */

define('PLUGIN_DIR', dirname(__FILE__).'/');  

//include library
include_once 'recaptchalib.php';

//front end module
include('helper.php');
include('shortcode.php');
include('rsv_online_widget.php');

//admin module
include('admin/index.php');

//get option settings
$ca_rsv_online_settings = get_option('ca_rsv_online_settings'); 
define('RECAPTCHA_PUBLIC_KEY',$ca_rsv_online_settings['recaptcha_public_key']);
define('RECAPTCHA_PRIVATE_KEY',$ca_rsv_online_settings['recaptcha_private_key']);

/**
 * Create table
 **/
function ca_rsv_online_install()
{
	global $wpdb;
	global $ca_rsv_online_db_version;

   	$table_name = $wpdb->prefix . "rsv_online";
      
	$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				email TEXT NOT NULL,
				phone varchar(255) NULL DEFAULT '',
				date_of_arrival date DEFAULT '0000-00-00' NOT NULL,
				guest tinyint(2) NOT NULL,
				package TEXT NOT NULL,
				message TEXT NOT NULL,
				created_at timestamp DEFAULT CURRENT_TIMESTAMP,
				UNIQUE KEY id (id)
			);";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
 
   add_option( "ca_rsv_online_db_version", $ca_rsv_online_db_version );
}
register_activation_hook(__FILE__,'ca_rsv_online_install');

function ca_rsv_online_process()
{
	global $wpdb;
	global $ca_rsv_online_settings;

  	$resp = recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, 
  							       $_SERVER["REMOTE_ADDR"], 
  							       $_POST["recaptcha_challenge_field"], 
  							       $_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) 
	{
		// What happens when the CAPTCHA was entered incorrectly
		echo ca_rsv_online_print_message('error','Code Verification is wrong. Please try again.');
		ca_rsv_online_form_output();
	} 
	else 
	{
		$insert = $wpdb->insert($wpdb->prefix.'rsv_online',
								array(
									'name' => $_POST['name'],
									'email' => $_POST['email'],
									'phone' => $_POST['phone'],
									'date_of_arrival' => $_POST['date_of_arrival'],
									'guest' => $_POST['guest'],
									'package' => $_POST['package'],
									'message' => $_POST['message']
								));
		
		if($insert)
		{	
			//send email
			ca_rsv_online_send_email($_POST);
			if($ca_rsv_online_settings['message'] != '')
				echo $ca_rsv_online_settings['message'];
			else
				echo ca_rsv_online_print_message('success','Thank you. We will contact you shortly');
		}
		else
		{
			echo ca_rsv_online_print_message('error','There was an error with your request. Please try again');
			ca_rsv_online_form_output($_POST);
		}
	}
}

function ca_rsv_online_enqueue_scripts()
{
	global $wp_scripts; 
	wp_enqueue_style("jquery-ui-css", "http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.min.css");
	wp_enqueue_script('jquery-ui');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('ca-rsv-online', plugin_dir_url(__FILE__).'js/ca_rsv_online.js', array('jquery'), NULL);

}
add_action('wp_enqueue_scripts', 'ca_rsv_online_enqueue_scripts');

function ca_rsv_online_send_email()
{
	global $ca_rsv_online_settings;

	// SMTP email sent
	require_once ABSPATH . WPINC . '/class-phpmailer.php';
	require_once ABSPATH . WPINC . '/class-smtp.php';
	
	//get package tile
	$package = ca_rsv_online_get_package_name($_POST['package']);

	$ca_rsv_online_admin_email = ($ca_rsv_online_settings['admin_email'] != "") ? $ca_rsv_online_settings['admin_email'] : $ca_rsv_online_settings['username'];

	$mail = new PHPMailer(); // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug 	= 1; // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth 	= true; // authentication enabled
	$mail->SMTPSecure 	= ($ca_rsv_online_settings['outgoing_port'] == '465') ? 'ssl' : 'tls'; // secure transfer enabled REQUIRED for GMail
	$mail->Host 		= $ca_rsv_online_settings['mail_host'];
	$mail->Port 		= $ca_rsv_online_settings['outgoing_port']; // or 587
	$mail->IsHTML(true);

	$mail->Username 	= $ca_rsv_online_settings['username'];
	$mail->Password 	= $ca_rsv_online_settings['password'];

	//send email
	$mail->SetFrom($ca_rsv_online_settings['username']);
	$mail->Subject 		= "New Reservation Online";
	$mail->Body 		= "Dear Admin, <br><br><br>
						   There's a request for reservation online from your website, you can following the detail below: <br>
						   <br>
						   Name: ".$_POST['name']."<br>
						   Email: ".$_POST['email']."<br>
						   Phone: ".$_POST['phone']."<br>
						   Date of Arrival: ".$_POST['date_of_arrival']."<br>
						   Guest: ".$_POST['guest']."<br>
						   Package: ".$package."<br>
						   Message: ".$_POST['message'];
	$mail->AddAddress($ca_rsv_online_admin_email);
 	
 	if($ca_rsv_online_settings['mail_host'] != "")
 		if(!$mail->Send())
    		echo "Mailer Error: " . $mail->ErrorInfo;
}

function ca_rsv_online()
{
	if($_POST)
		ca_rsv_online_process();
	else
		ca_rsv_online_form_output();
}
add_shortcode('rsv-online-form','ca_rsv_online');