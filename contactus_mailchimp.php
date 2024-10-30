<?php
/*
  Plugin Name: MailChimp Form by ContactUs
  Plugin URI: http://help.contactus.com/hc/en-us/sections/200214566-MailChimp-Plugin-by-ContactUs-com
  Description: The MailChimp Form Plugin by ContactUs
  Author: contactus.com
  Version: 3.0
  Author URI: http://www.contactus.com/
  License: GPLv2 or later
*/

/*
  Copyright 2014  ContactUs.com  ( help.contacus.com )
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//INCLUDE WP HOOKS ACTIONS & FUNCTIONS
require_once( dirname(__FILE__) . '/mailchimp_form_conf.php' );

//INCLUDE WP HOOKS ACTIONS & FUNCTIONS
require_once( dirname(__FILE__) . '/helpers/mailchimp_form_functions.php' );

/*
 * Method in charge to render plugin layout
 * @since 1.0
 * @return string Render HTML layout into WP admin
 */
if (!function_exists('cUsMC_menu_render')) {

    function cUsMC_menu_render() {

        $aryUserCredentials = get_option('cUsMC_settings_userCredentials'); //get the values, wont work the first time

        ?>
        <div id="cu_plugin-container">
            <?php
                /*
                * PLUGIN HEADER
                * @since 5.0
                */
                require_once( cUsMC_DIR . 'views/header.php');
            ?>

            <?php
                if(!empty($aryUserCredentials) && is_array($aryUserCredentials)) {
                    require_once( cUsMC_DIR . 'views/priv-uix.php');
                }else{
                    require_once( cUsMC_DIR . 'views/pub-uix.php');
                }
            ?>

        </div>

    <?php
    } //cUsMC_menu_render ends

} // END IF FUNCTION RENDER