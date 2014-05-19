<?php
/**
 * Admin Module
 *
 * @author      Sandi Andrian
 * @since       May 19, 2014
 * @version     0.1
 * @link        http://coderalliance.com
 **/

function ca_rsv_online_register_admin_menu_page()
{
    add_menu_page(
    	'Reservation', 
    	'Reservation', 
    	'manage_options', 
    	'ca-rsv-online/admin/admin.php', 
    	'', 
    	plugins_url('ca-rsv-online/images/ico-rsv-online.png'), 
    	23
    );
    add_submenu_page( 
        'ca-rsv-online/admin/admin.php',
        'All Reservations',
        'All Reservations',
        'manage_options',
        'ca-rsv-online/admin/admin.php',
        ''
    );
    add_submenu_page( 
        'ca-rsv-online/admin/admin.php',
        'Settings',
        'Settings',
        'manage_options',
        'ca-rsv-online/admin/setting.php',
        ''
    );
}
add_action('admin_menu', 'ca_rsv_online_register_admin_menu_page');

function ca_rsv_online_admin_settings_init()
{
    register_setting(
        'ca_rsv_online_settings', //option group
        'ca_rsv_online_settings', //option name
        ''
    );

    add_settings_section(
        'ca_rsv_online_settings_mail_group',
        'Mail Settings', 
        '',
        'ca-rsv-online/admin/setting.php'
    );

    add_settings_field(
        'ca_rsv_online_settings_mail_host', //id
        'Mail Host:',  //title
        'ca_rsv_online_settings_mail_host_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_mail_group' //section
    );

    add_settings_field(
        'ca_rsv_online_settings_mail_outgoing_port', //id
        'Outgoing Port:',  //title
        'ca_rsv_online_settings_mail_outgoing_port_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_mail_group' //section
    );

    add_settings_field(
        'ca_rsv_online_settings_mail_username', //id
        'Username:',  //title
        'ca_rsv_online_settings_mail_username_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_mail_group' //section
    );

    add_settings_field(
        'ca_rsv_online_settings_mail_password', //id
        'Password:',  //title
        'ca_rsv_online_settings_mail_password_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_mail_group' //section
    );

    add_settings_field(
        'ca_rsv_online_settings_mail_admin_email', //id
        'Admin Email:',  //title
        'ca_rsv_online_settings_mail_admin_email_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_mail_group' //section
    );

    add_settings_section(
        'ca_rsv_online_settings_recaptcha_group',
        'reCaptcha Settings', 
        'ca_rsv_online_settings_recaptcha_group_desc',
        'ca-rsv-online/admin/setting.php'
    );

    add_settings_field(
        'ca_rsv_online_settings_recaptcha_public_key', //id
        'Public Key:',  //title
        'ca_rsv_online_settings_recaptcha_public_key_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_recaptcha_group' //section
    );

    add_settings_field(
        'ca_rsv_online_settings_recaptcha_private_key', //id
        'Private Key:',  //title
        'ca_rsv_online_settings_recaptcha_private_key_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_recaptcha_group' //section
    );

    add_settings_section(
        'ca_rsv_online_settings_message_group',
        'Message Settings', 
        'ca_rsv_online_settings_message_group_desc',
        'ca-rsv-online/admin/setting.php'
    );

    add_settings_field(
        'ca_rsv_online_settings_message', //id
        'Message:',  //title
        'ca_rsv_online_settings_message_field', //callback
        'ca-rsv-online/admin/setting.php', //page
        'ca_rsv_online_settings_message_group' //section
    );
}
add_action('admin_init','ca_rsv_online_admin_settings_init');

/**
 * Field Callback
 **/
$ca_rsv_online_settings = get_option('ca_rsv_online_settings');
function ca_rsv_online_settings_mail_host_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[mail_host]" class="regular-text" value="%s">'),esc_attr($ca_rsv_online_settings['mail_host']));
}

function ca_rsv_online_settings_mail_outgoing_port_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[outgoing_port]" class="small-text" value="%s">'),esc_attr($ca_rsv_online_settings['outgoing_port']));
}

function ca_rsv_online_settings_mail_username_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[username]" class="regular-text" value="%s" autocomplete="off">'),esc_attr($ca_rsv_online_settings['username']));
    echo '<span style="margin-left: 10px"><small>example: john.doe@google.com</small></span>';
}

function ca_rsv_online_settings_mail_password_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[password]" class="regular-text" value="%s" autocomplete="off">'),esc_attr($ca_rsv_online_settings['password']));
}

function ca_rsv_online_settings_mail_admin_email_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[admin_email]" class="regular-text" value="%s" autocomplete="off">'),esc_attr($ca_rsv_online_settings['admin_email']));
}

function ca_rsv_online_settings_recaptcha_group_desc()
{
    echo 'You can get your public key and private key for your website at <a href="http://google.com/recaptcha/" target="_blank">http://google.com/recaptcha/</a>';
}

function ca_rsv_online_settings_recaptcha_public_key_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[recaptcha_public_key]" class="regular-text" value="%s" autocomplete="off">'),esc_attr($ca_rsv_online_settings['recaptcha_public_key']));
}

function ca_rsv_online_settings_recaptcha_private_key_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<input type="text" name="ca_rsv_online_settings[recaptcha_private_key]" class="regular-text" value="%s" autocomplete="off">'),esc_attr($ca_rsv_online_settings['recaptcha_private_key']));
}

function ca_rsv_online_settings_message_group_desc()
{
    echo 'To override message notification to user at front end, use this setting below';
}

function ca_rsv_online_settings_message_field()
{
    global $ca_rsv_online_settings;
    echo sprintf(__('<textarea name="ca_rsv_online_settings[message]" cols="50" rows="6">%s</textarea>'),esc_attr($ca_rsv_online_settings['message']));
}

