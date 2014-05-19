/**
 * Reserve online Javascript
 *
 * @author		Sandi Andrian
 * @version		0.1
 * @package		ca_rsv_online
 **/

 jQuery(document).ready(function()
 {
 	jQuery('#date_of_arrival').datepicker({
 		dateFormat: 'yy-mm-dd',
 		minDate: "0"
 	});
 });