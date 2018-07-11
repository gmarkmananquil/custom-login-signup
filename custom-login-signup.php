<?php
/**
 * Plugin Name: Custom Login Signup
 * Author: Glen Mark Mananquil
 * Description: Custom login for campstravel
 *
 **/

//start session
session_start();

if(!defined("BASEPATH")) define("BASEPATH", dirname(__FILE__));

if(!defined("DS")) define("DS", DIRECTORY_SEPARATOR);

if(!defined("BASEURL")) define("BASEURL",   plugins_url("/custom-login-signup"));

define("CUSTOM_LOGIN_PAGE", "custom-login-page");
define("CUSTOM_SIGNUP_PAGE", "custom-signup-page");

define("CUSTOM_LOGIN_SIGNUP_SESSION",   "custom-login-signup-session");
define("CUSTOM_LOGIN_SIGNUP_FLASH_SESSION",   "custom-login-signup-flash-session");

require_once BASEPATH . DS . "helper-functions.php";

require_once BASEPATH . DS . "custom-login.php";

require_once BASEPATH . DS . "custom-signup.php";

if(!function_exists("custom_login_enqueue_script")){
    add_action("wp_enqueue_scripts",    "custom_login_enqueue_script");
    function custom_login_enqueue_script(){
        wp_enqueue_script("jquery");

        wp_enqueue_script("custom-login-script", BASEURL . "/media/js/custom-login-script.js");

        wp_enqueue_style("custom-login-style",  BASEURL . "/media/css/custom-login-style.css");
        wp_enqueue_style("custom-signup-style",  BASEURL . "/media/css/custom-signup-style.css");

    }
}
