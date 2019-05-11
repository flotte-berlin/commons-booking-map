<?php

/**
 *
**/
class CB_Map_Settings {

  const OPTION_KEYS = ['map_height', 'zoom_min', 'zoom_max', 'zoom_start', 'lat_start', 'lon_start', 'marker_map_bounds_initial', 'marker_map_bounds_filter', 'max_cluster_radius', 'custom_marker_media_id', 'marker_icon_width', 'marker_icon_height', 'marker_icon_anchor_x', 'marker_icon_anchor_y', 'show_location_contact', 'cb_items_available_categories', 'cb_items_preset_categories'];

  const MAP_HEIGHT_VALUE_MIN = 100;
  const MAP_HEIGHT_VALUE_MAX = 5000;
  const ZOOM_VALUE_MIN = 1;
  const ZOOM_VALUE_MAX = 19;
  const LAT_VALUE_MIN = -90;
  const LAT_VALUE_MAX = 90;
  const LON_VALUE_MIN = -180;
  const LON_VALUE_MAX = 180;
  const MAX_CLUSTER_RADIUS_VALUE_MIN = 0;
  const MAX_CLUSTER_RADIUS_VALUE_MAX = 1000;

  const MAP_HEIGHT_DEFAULT = 400;
  const ZOOM_MIN_DEFAULT = 8;
  const ZOOM_MAX_DEFAULT = 19;
  const ZOOM_START_DEFAULT = 8;
  const LAT_START_DEFAULT = 52.49333;
  const LON_START_DEFAULT = 13.37933;
  const MARKER_MAP_BOUNDS_INITIAL_DEFAULT = false;
  const MARKER_MAP_BOUNDS_FILTER_DEFAULT = true;
  const MAX_CLUSTER_RADIUS_DEFAULT = 80;
  const CUSTOM_MARKER_MEDIA_ID_DEFAULT = null;
  const MARKER_ICON_WIDTH_DEFAULT = 0;
  const MARKER_ICON_HEIGHT_DEFAULT = 0;
  const MARKER_ICON_ANCHOR_X_DEFAULT = 0;
  const MARKER_ICON_ANCHOR_Y_DEFAULT = 0;
  const SHOW_LOCATION_CONTACT_DEFAULT = true;
  const CB_ITEMS_AVAILABLE_CATEGORIES_DEFAULT = [];
  const CB_ITEMS_PRESET_CATEGORIES_DEFAULT = [];

  //const MARKER_POPUP_CONTENT_DEFAULT = "'<b>' + location.location_name + '</b><br>' + location.address.street + '<br>' + location.address.zip + ' ' + location.address.city + '<p>' + location.opening_hours + '</p>'";

  public static $options;

  public function prepare_settings() {

    add_action('admin_menu', function() {
        add_options_page( cb_map\__('SETTINGS_TITLE', 'commons-booking-map', 'Settings for Commons Booking Map'), cb_map\__('SETTINGS_MENU', 'commons-booking-map', 'Commons Booking Map' ), 'manage_options', 'commons-booking-map', array($this, 'render_options_page') );
    });

    add_action( 'admin_init', function() {
      register_setting( 'cb-map-settings', 'cb_map_options', array($this, 'validate_options') );
    });

  }

  private static function load_options() {
    if(!isset(self::$options)) {
      $options = get_option('cb_map_options', array());
      self::$options = self::populate_option_defaults($options);
    }
  }

  public static function populate_option_defaults($options) {
    //var_dump($options);

    foreach (self::OPTION_KEYS as $key) {
      if(!isset($options[$key])) {
        $options[$key] = self::get_option_default($key);
      }
    }

    return $options;
  }

  /**
  * option getter
  **/
  public static function get_option($key) {
    self::load_options();

    return self::$options[$key];
  }

  /**
  *
  **/
  public static function get_options($public = false) {
    self::load_options();

    //TODO: filter public

    return self::$options;
  }

  private static function get_option_default($option_name) {

    $default_name = strtoupper($option_name) . '_DEFAULT';

    $const_value = constant("self::$default_name");

    return isset($const_value) ? $const_value : null;
  }

  /**
  * sanitize and validate the options provided by input array
  **/
  public function validate_options($input = array()) {
    //var_dump($input);
    self::load_options();

    $validated_input = self::populate_option_defaults([]);

    //map_height
    if(isset($input['map_height']) && (int) $input['map_height'] >= self::MAP_HEIGHT_VALUE_MIN && $input['map_height'] <= self::MAP_HEIGHT_VALUE_MAX) {
      $validated_input['map_height'] = (int) $input['map_height'];
    }

    //zoom_min
    if(isset($input['zoom_min']) && (int) $input['zoom_min'] >= self::ZOOM_VALUE_MIN && $input['zoom_min'] <= self::ZOOM_VALUE_MAX) {
      $validated_input['zoom_min'] = (int) $input['zoom_min'];
    }

    //zoom_max
    if(isset($input['zoom_max']) && (int) $input['zoom_max'] >= self::ZOOM_VALUE_MIN && $input['zoom_max'] <= self::ZOOM_VALUE_MAX) {
      if((int) $input['zoom_max'] >= $validated_input['zoom_min']) {
        $validated_input['zoom_max'] = (int) $input['zoom_max'];
      }
      else {
        $validated_input['zoom_max'] = $validated_input['zoom_min'];
      }
    }

    //zoom_start
    if(isset($input['zoom_start']) && (int) $input['zoom_start'] >= self::ZOOM_VALUE_MIN && $input['zoom_start'] <= self::ZOOM_VALUE_MAX) {
      if((int) $input['zoom_start'] >= $validated_input['zoom_min'] && (int) $input['zoom_start'] <= $validated_input['zoom_max']) {
        $validated_input['zoom_start'] = (int) $input['zoom_start'];
      }
      else {
        $validated_input['zoom_start'] = $validated_input['zoom_min'];
      }
    }

    //lat_start
    if(isset($input['lat_start']) && (float) $input['lat_start'] >= self::LAT_VALUE_MIN && (float) $input['lat_start'] <= self::LAT_VALUE_MAX) {
      $validated_input['lat_start'] = (float) $input['lat_start'];
    }

    //lon_start
    if(isset($input['lon_start']) && (float) $input['lon_start'] >= self::LON_VALUE_MIN && (float) $input['lon_start'] <= self::LON_VALUE_MAX) {
      $validated_input['lon_start'] = (float) $input['lon_start'];
    }

    //marker_map_bounds_initial
    $validated_input['marker_map_bounds_initial'] = isset($input['marker_map_bounds_initial']) ? true : false;

    //marker_map_bounds_filter
    $validated_input['marker_map_bounds_filter'] = isset($input['marker_map_bounds_filter']) ? true : false;

    //max_cluster_radius
    if(isset($input['max_cluster_radius']) && (int) $input['max_cluster_radius'] >= self::MAX_CLUSTER_RADIUS_VALUE_MIN && $input['max_cluster_radius'] <= self::MAX_CLUSTER_RADIUS_VALUE_MAX) {
      $validated_input['max_cluster_radius'] = (int) $input['max_cluster_radius'];
    }

    // custom_marker_media_id
    if(isset($input['custom_marker_media_id'])) {
      $validated_input['custom_marker_media_id'] = abs((int) $input['custom_marker_media_id']);
    }

    //marker_icon_width
    if(isset($input['marker_icon_width'])) {
      $validated_input['marker_icon_width'] = abs((float) $input['marker_icon_width']);
    }

    //marker_icon_height
    if(isset($input['marker_icon_height'])) {
      $validated_input['marker_icon_height'] = abs((float) $input['marker_icon_height']);
    }

    //marker_icon_anchor_x
    if(isset($input['marker_icon_anchor_x'])) {
      $validated_input['marker_icon_anchor_x'] = (float) $input['marker_icon_anchor_x'];
    }

    //marker_icon_anchor_y
    if(isset($input['marker_icon_anchor_y'])) {
      $validated_input['marker_icon_anchor_y'] = (float) $input['marker_icon_anchor_y'];
    }

    //show_location_contact
    if(isset($input['show_location_contact'])) {
      $validated_input['show_location_contact'] = true;
    }
    else {
      $validated_input['show_location_contact'] = false;
    }

    //cb_items_available_categories
    $category_terms = get_terms([
      'taxonomy' => 'cb_items_category',
      'hide_empty' => false
    ]);
    $valid_term_ids = [];
    foreach($category_terms as $category_term) {
      $valid_term_ids[] = $category_term->term_id;
    }

    if(isset($input['cb_items_available_categories'])) {
      foreach($input['cb_items_available_categories'] as $cb_items_category_id) {
        if(in_array((int) $cb_items_category_id, $valid_term_ids)) {
          $validated_input['cb_items_available_categories'][] = $cb_items_category_id;
        }
      }
    }

    if(isset($input['cb_items_preset_categories'])) {
      foreach($input['cb_items_preset_categories'] as $cb_items_category_id) {
        if(in_array((int) $cb_items_category_id, $valid_term_ids)) {
          $validated_input['cb_items_preset_categories'][] = $cb_items_category_id;
        }
      }
    }

    return $validated_input;
  }

  public function add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=commons-booking-map">' . __( 'Settings') . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
  }

  public function render_options_page() {
    wp_enqueue_media();

    //TODO: enqueue image upload script (don't insert script in DOM)

    $available_categories_args = [
      'taxonomy' => 'cb_items_category',
      'echo' => false,
      'checked_ontop' => false,
      'selected_cats' => self::get_option('cb_items_available_categories')
    ];
    $available_categories_checklist_markup = wp_terms_checklist( 0, $available_categories_args);
    $available_categories_checklist_markup = str_replace('name="tax_input[cb_items_category]', 'name="cb_map_options[cb_items_available_categories]', $available_categories_checklist_markup);

    $preset_categories_args = [
      'taxonomy' => 'cb_items_category',
      'echo' => false,
      'checked_ontop' => false,
      'selected_cats' => self::get_option('cb_items_preset_categories')
    ];
    $preset_categories_checklist_markup = wp_terms_checklist( 0, $preset_categories_args);
    $preset_categories_checklist_markup = str_replace('name="tax_input[cb_items_category]', 'name="cb_map_options[cb_items_preset_categories]', $preset_categories_checklist_markup);

    include_once( CB_MAP_PATH . 'templates/settings-page-template.php');
  }

}

?>
