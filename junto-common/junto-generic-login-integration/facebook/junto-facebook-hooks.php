<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dwiding
 * Date: 2/15/12
 * Time: 6:50 PM
 * To change this template use File | Settings | File Templates.
 */

//Adds the js from the fb channel
add_action('login_footer','sfc_add_base_js',20);
// same as above to basic XFBML load into footer of every page
add_action('wp_footer','sfc_add_base_js',20); // 20, to put it at the end of the footer insertions. sub-plugins should use 30 for their code

//adds the fb js channel
add_action('init','sfc_channel_file');

//adds the login button to pertinent pages
add_action('junto_login_form','junto_mod_sfc_register_add_login_button');
add_action('login_form','junto_mod_sfc_register_add_login_button');

//adds javascript to update/remove fb association from user settings
//add_action('wp_ajax_update_fbuid', 'sfc_login_ajax_update_fbuid');

//adds the filter to authenticate the login on login pages
add_filter('authenticate','sfc_login_check',90);

//gets the avatar if found from fb
add_filter('get_avatar','sfc_login_avatar', 10, 5);

//Adds a step to the logout process to handle fb
//todo clear the fb cookie?
add_action('wp_logout','junto_logout_fb');


//for user admin footer
add_action('wp_head', 'sfc_login_update_js');

//adds the ajax action to remove associations
add_action('wp_ajax_update_fbuid', 'sfc_login_ajax_update_fbuid');
