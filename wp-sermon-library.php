<?php
  /*
  Plugin Name: WP Sermon Library
  Plugin URI: https://github.com/jamesdoc/wp-sermon-library
  Description: Very simple sermon libary for your church's WordPress site.
  Version: 0.0.1
  Author: James Doc
  Author URI: http://www.jamesdoc.com
  */

  if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

  if(!class_exists('SL_Sermon_Library')) {
    class SL_Sermon_Library {

      public function __construct() {

      }

      public function activate() {

      }

      public static function deactivate() {

      }

    }
  }

  if(class_exists('SL_Sermon_Library')) {
    register_activation_hook(
        __FILE__,
        array('SL_Sermon_Library', 'activate'));
    register_deactivation_hook(
        __FILE__,
        array('SL_Sermon_Library', 'deactivate'));
    $wp_plugin_template = new SL_Sermon_Library();
  }
