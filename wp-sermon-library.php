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
        global $sl_version;
        global $wpdb;

        $sl_version = '0.0.1';

        add_action( 'init', array(&$this, 'sl_register_cpt') );
        add_action( 'init', array(&$this, 'sl_register_taxonomies') );

      }

      public function activate() {

      }

      public function deactivate() {

      }

      public function sl_register_cpt() {
        $labels = array(
          'name' => _x( 'Sermons', 'sermons' ),
          'singular_name' => _x( 'Sermon', 'sermons' ),
          'add_new' => _x( 'Add New', 'sermons' ),
          'add_new_item' => _x( 'Add New Sermon', 'sermons' ),
          'edit_item' => _x( 'Edit Sermon', 'sermons' ),
          'new_item' => _x( 'New Sermon', 'sermons' ),
          'view_item' => _x( 'View sermon', 'sermons' ),
          'search_items' => _x( 'Search sermons', 'sermons' ),
          'not_found' => _x( 'No sermons found', 'sermons' ),
          'not_found_in_trash' => _x( 'No sermons found in Trash', 'sermons' ),
          'parent_item_colon' => _x( 'Sermon series:', 'sermons' ),
          'menu_name' => _x( 'Sermons', 'sermons' ),
        );

        $args = array(
          'labels' => $labels,
          'hierarchical' => true,
          'description' => 'Sermons filterable by series',
          'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes' ),
          'taxonomies' => array( 'sermon_series' ),
          'public' => true,
          'show_ui' => true,
          'show_in_menu' => true,
          'menu_position' => 5,
          'menu_icon' => 'dashicons-controls-volumeon',
          'show_in_nav_menus' => true,
          'publicly_queryable' => true,
          'exclude_from_search' => false,
          'has_archive' => true,
          'query_var' => true,
          'can_export' => true,
          'rewrite' => true,
          'capability_type' => 'post'
        );

        register_post_type( 'sermon', $args );
      }

      public function sl_register_taxonomies() {

        // TODO: Add in ability to upload a sermon series image

        $labels = array(
          'name' => _x( 'Sermon series', 'sermons' ),
          'singular_name' => _x( 'Sermon series', 'sermons' ),
          'all_items' => _x( 'All sermon series\'', 'sermons' ),
          'edit_item' => _x( 'Edit sermon series', 'sermons' ),
          'view_item' => _x( 'View sermon series', 'sermons' ),
          'update_item' => _x( 'Update sermon series', 'sermons' ),
          'add_new_item' => _x( 'Add new sermon series', 'sermons' ),
          'parent_item' => _x( 'Parent sermon series', 'sermons' ),
          'parent_item_colon' => _x( 'Parent sermon series:', 'sermons' ),
          'search_items' => _x( 'Search sermon series', 'sermons' ),
          'popular_items' => _x( 'Popular sermon series\'', 'sermons' ),
          'not_found' => _x( 'No sermon series found', 'sermons' ),
        );

        $args = array(
          'hierarchical' => false,
          'labels' => $labels,
          'query_var' => true,
          'rewrite' => array(
            'slug' => 'series',
            'with_front' => false
          )
        );

        register_taxonomy('sermon_series', 'sermon', $args);
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
