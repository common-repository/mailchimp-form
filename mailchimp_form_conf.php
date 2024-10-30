<?php
/**
 *
 *  MAILCHIMP FORM BY CONTACTUS.COM
 * 
 * Initialization MailChimp Form Config File
 * @since 1.0 First time this was introduced into MailChimp Form plugin.
 * @author ContactUs.com <support@contactus.com>
 * @copyright 2014 ContactUs.com Inc.
 * Company      : contactus.com
 * Updated  	: 20140311
 **/

//PLUGIN NAME
$cUs_plug_name = 'MailChimp Form Plugin v.3.0';

//DEBUG MODE OFF
error_reporting(0);
error_reporting(E_ERROR);

$cus_dirbase = trailingslashit(basename(dirname(__FILE__)));
$cus_dir = trailingslashit(WP_PLUGIN_DIR) . $cus_dirbase;
$cus_url = trailingslashit(WP_PLUGIN_URL) . $cus_dirbase;

//LIVE ENVIROMENT
$cus_env_url = '//cdn.contactus.com/cdn/forms/';
$cus_par_url = 'https://admin.contactus.com/partners';
$cus_api_enviroment = 'https://api.contactus.com/api2.php';

$cus_api_form_template = 'genericNewsletterTemplate1';
$cus_api_tab_template = 'ntab1';

//WP KEYS
$cus_api_ApiAccountKey = 'AC132f1ca7ff5040732b787564996a02b46cc4b58d';
$cus_api_ApiKey = 'cd690cf4f450950e857b417710b656923cf4b579';

//DEFINE GLOBAL ENVIROMENT VARS
define('cUsMC_DIR', $cus_dir);
define('cUsMC_URL', $cus_url);
define('cUsMC_ENV_URL', $cus_env_url);
define('cUsMC_PARTNER_URL', $cus_par_url);
define('cUsMC_API_ENV', $cus_api_enviroment);
define('cUsMC_API_ACC', $cus_api_ApiAccountKey);
define('cUsMC_API_AKY', $cus_api_ApiKey);
define('cUsMC_PLUGINNAME', $cUs_plug_name);
define('cUsMC_FORM_TPL', $cus_api_form_template);
define('cUsMC_TAB_TPL', $cus_api_tab_template);