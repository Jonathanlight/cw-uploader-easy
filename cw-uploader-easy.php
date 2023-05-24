<?php
/*
 Plugin Name: Manager Upload Stock
 Plugin URI: https://localhost.dev/
 Description: Upload un fichier pour l'alimentation en base de donnée 
 Version: 4.1.1
 Author: Jonathan Kablan
 Author URI: https://localhost.dev
 Text Domain: Upload de fichier
 */

/*  Copyright 2020	Jonathan Kablan  (email : jonathank.light@gmail.com)*/

if (!defined('ABSPATH')) {
	exit;
}

if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
    require_once dirname(__FILE__).'/vendor/autoload.php';
}

// Version of the plugin
define('CW_CURRENT_VERSION', '1.0.0' );

function activate() {
    Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate');

function deactivate() {
    Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'desactivate');

if (class_exists('Inc\\Init')) {
    Inc\Init::register_services();
}
