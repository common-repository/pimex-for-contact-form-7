<?php
/**
 * @package Pimex for contact form 7
 */

/*
* Plugin Name: Pimex for contact form 7
* Plugin URI: https://dev.pimex.co/cms/wordpress
* Description: Pimex integration for contact form 7
* Version: 4.3.2
* Author: Pimex Inc
* Author URI: https://pimex.co
* License: ECPT_LICENSE
* Domain Path: /lang
* Text Domain: pimex-contact-form-7
*/

require( dirname( __FILE__ ) . '/lib/pimex.php');

$pimexAsync = new PimexAsync();
$pimexAsync->init();
