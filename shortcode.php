<?php
/**
 * Shortcode for Reserve Online for Wordpress
 * @author		Sandi Andrian
 * @version		0.1
 * @package		ca_rsv_online
 * @todo		add email notification
 **/

//include 'recaptchalib.php';

 /**
 * Reserve Online Form
 **/ 
function ca_rsv_online_form_output($value = array())
{
	$form_content  = '<form method="POST" class="form-horizontal rsv-online-form">';

	//name
	$form_content .= '<div class="control-group">
						<label for="name" class="control-label">Name <span class="important">*</span></label>
						<div class="controls">
							<input type="text" name="name" value="'.$value['name'].'">
						</div>
					  </div>';

	//email
	$form_content .= '<div class="control-group">
						<label for="email" class="control-label">Email <span class="important">*</span></label>
						<div class="controls">
							<input type="text" name="email" value="'.$value['email'].'">
						</div>
					  </div>';

	//phone
	$form_content .= '<div class="control-group">
						<label for="phone" class="control-label">Phone</label>
						<div class="controls">
							<input type="text" name="phone" value="'.$value['phone'].'">
						</div>
					  </div>';

	//phone
	$form_content .= '<div class="control-group">
						<label for="date_of_arrival" class="control-label">Date of Arrival <span class="important">*</span></label>
						<div class="controls">
							<input type="text" name="date_of_arrival" value="" id="date_of_arrival" value="'.$value['date_of_arrival'].'">
						</div>
					  </div>';

	//phone
	$form_content .= '<div class="control-group">
						<label for="guest" class="control-label">Number of Guest(s) <span class="important">*</span></label>
						<div class="controls">
							<input type="text" name="guest" value="'.$value['guest'].'">
						</div>
					  </div>';

	//package
	//get the package from DB
	$packages = new WP_Query(
						array(
							'post_type' => 'packages'
						)
					);
	$sl_package = $_GET['package'];
	$form_content .= '<div class="control-group">
						<label for="guest" class="control-label">Package <span class="important">*</span></label>
						<div class="controls">
							<select name="package">';
	$form_content .= 			'<option value=""';
	$form_content .= ($sl_package == '') ? 'selected' : '';
	$form_content .= '>Choose Package</option>';

	//looping the packages
	while($packages->have_posts()):
		$packages->the_post();

		$slug 	   	   = basename(get_permalink(get_the_ID()));
		$form_content .= '<option value="'.$slug.'"';
		$form_content .= ($sl_package == $slug) ? 'selected' : '';
		$form_content .= '>'.get_the_title().'</option>';

	endwhile;

	$form_content .= '		<select>
						</div>
					  </div>';

	//message
	$form_content .= '<div class="control-group">
						<label for="guest" class="control-label">Your Message <span class="important">*</span></label>
						<div class="controls">
							<textarea name="message">'.$value['message'].'</textarea>
						</div>
					  </div>';

	//recaptcha
	$form_content .= '<div class="control-group">
						<div class="controls">
							'.recaptcha_get_html(RECAPTCHA_PUBLIC_KEY).'
						</div>
					  </div>';

	//submit button
	$form_content .= '<div class="control-group last">
						<div class="controls">
							<input type="submit" name="submit" value="Submit" class="btn btn-warning">
						</div>
					  </div>';

	//close the form
	$form_content .= '</form>';

	echo $form_content;
}