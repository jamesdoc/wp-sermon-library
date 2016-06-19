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
        global $sl_name;
        global $sl_version;
        global $wpdb;

        $sl_version = '0.0.1';
        $sl_name = array(
          'name_data' => 'wp-sermon-library',
          'name_human' => 'Wordpress Sermon Library');

        add_action( 'init', array(&$this, 'sl_register_cpt') );
        add_action( 'init', array(&$this, 'sl_register_taxonomies') );
        add_action( 'admin_init', array(&$this, 'sl_register_custom_fields'));
        add_action( 'save_post', array(&$this, 'sl_save_custom_fields') );

        add_action( 'admin_enqueue_scripts', array(&$this, 'sl_add_admin_scripts'), 10, 1 );

        // Add custom fields to sermon series taxonomy
        // TODO: check if Wordpress at least 4.4 before loading this in
        add_action( 'sermon_series_add_form_fields', array(&$this, 'sl_add_feature_group_field'), 10, 2 );
        add_action( 'created_sermon_series', array(&$this, 'sl_save_sermon_series_meta'), 10, 2 );
        add_action( 'sermon_series_edit_form_fields', array(&$this, 'sl_edit_feature_group_field'), 10, 2 );
        add_action( 'edited_sermon_series', array(&$this, 'sl_update_sermon_series_meta'), 10, 2 );
        add_filter( 'manage_edit-sermon_series_columns', array(&$this, 'sl_add_sermon_series_column') );
        add_filter( 'manage_sermon_series_custom_column', array(&$this, 'add_sermon_series_column_content'), 10, 3 );

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
          'supports' => array( 'title', 'editor', 'author' , 'comments', 'revisions'),
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
        flush_rewrite_rules();
      }

      public function sl_register_taxonomies() {

        $labels = array(
          'name' => _x( 'Sermon Series', 'sermons' ),
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

      public function sl_register_custom_fields() {
        add_meta_box("sl_audio_upload_form", "Upload Audio", array(&$this, 'sl_upload_audio_form'), "sermon", "normal", "high");
      }

      public function sl_save_custom_fields( $post ) {
        // TODO: Server side compress audio if setting checked
        // TODO: Generate ogg/mp3
        if (!isset($_POST["sl_sermon_audio"])) {
          return;
        }
        update_post_meta($post->ID, "sermon_audio_media_id", $_POST["sl_sermon_audio"]);
      }

      public function sl_upload_audio_form( $post ) {
        $custom_fields = get_post_custom($post->ID);
        if (isset($custom_fields["sermon_audio_media_id"])){
          $media_id = $custom_fields["sermon_audio_media_id"][0];
          $audio = wp_get_attachment_metadata($media_id);
          $audio['url'] = wp_get_attachment_url($media_id);
        } else {
          $audio['title'] = 'No audio uploaded';
          $audio['length_formatted'] = '#:##';
        }
        include_once('templates/tpl_upload_form.php');
      }

      public function sl_add_admin_scripts( $hook ) {
        global $post;
        $screen = get_current_screen();

        if ( $screen->post_type === 'sermon' ) {
          wp_enqueue_script( 'sl_admin_script', plugins_url('js/wp-sermon-library-admin.js', __FILE__) );

          if ($screen->taxonomy === 'sermon_series') {
            wp_enqueue_media();
            wp_enqueue_style( 'thickbox' );
          }
        }

      }

      public function sl_add_feature_group_field($taxonomy) {
        // TODO: Push this out into the template folder (and tidy UI)
        ?>
        <div class="form-field term-group">
          <label for="sl_sermon_taxonomy_image" class="js-sl_taxonomy_image_label"><?php _e('Sermon image', 'sermons'); ?></label>
          <input id="js-sl_sermon_taxonomy_image" type="hidden" name="sl_sermon_taxonomy_image" value="" />
          <p>
            <button class="js-sl_sermon_series_image_button button" data-uploader_title="Upload Image" data-uploader_button_text="Add Sermon Series Image">
              Select sermon series image
            </button>
          </p>
          <div>
            <img class="js-sl_sermon_series_image_preview" src="http://placehold.it/200x200" alt="" width="100" />
          </div>
        </div><?php
      }

      public function sl_edit_feature_group_field( $term, $taxonomy ){
        $series_image_url = "http://placehold.it/200x200";
        $series_image_id = get_term_meta( $term->term_id, 'sl_sermon_taxonomy_image', true );
        if($series_image_id) {
          $series_image_url = wp_get_attachment_url($series_image_id);
        }
        ?>
        <tr class="form-field term-group-wrap">
          <th scope="row"><label><?php _e( 'Sermon image', 'sermons' ); ?></label></th>
          <td>
            <input id="js-sl_sermon_taxonomy_image" type="hidden" name="sl_sermon_taxonomy_image" value="<?php echo $series_image_id ?>" />
            <div>
              <img class="js-sl_sermon_series_image_preview" src="<?php echo $series_image_url ?>" alt="" width="200" />
            </div>
            <p>
              <button class="js-sl_sermon_series_image_button button" data-uploader_title="Upload Image" data-uploader_button_text="Add Sermon Series Image">
                Replace sermon series image
              </button>
            </p>
          </td>
        </tr>
        <?php
      }

      public function sl_add_sermon_series_column( $columns ){
        $columns['sermon_image'] = __( 'Image', 'sermon' );
        return $columns;
      }

      public function add_sermon_series_column_content( $content, $column_name, $term_id ){
        if( $column_name !== 'sermon_image' ) { return $content; }

        $series_image_url = "http://placehold.it/200x200";

        $term_id = absint( $term_id );
        $series_image_id = get_term_meta( $term_id, 'sl_sermon_taxonomy_image', true );

        if($series_image_id) {
          $series_image_url = wp_get_attachment_image_url($series_image_id, 'thumbnail', false);
        }

        return "<img src='" . $series_image_url . "' alt='' width='100%' />";
      }

      public function sl_save_sermon_series_meta( $term_id, $tt_id ){
        if( isset( $_POST['sl_sermon_taxonomy_image'] ) ){
          $group = sanitize_title( $_POST['sl_sermon_taxonomy_image'] );
          add_term_meta( $term_id, 'sl_sermon_taxonomy_image', $group, true );
        }
      }

      public function sl_update_sermon_series_meta( $term_id, $tt_id ){
        if( isset( $_POST['sl_sermon_taxonomy_image'] ) ){
          $group = sanitize_title( $_POST['sl_sermon_taxonomy_image'] );
          update_term_meta( $term_id, 'sl_sermon_taxonomy_image', $group );
        }
      }

    } // End SL_Sermon_Library
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
