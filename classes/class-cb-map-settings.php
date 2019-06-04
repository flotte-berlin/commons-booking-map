<?php

/**
 *
**/
class CB_Map_Settings {

  const OPTION_KEYS = [
    'map_type',
    'export_code',
    'import_sources',
    'map_height', 'zoom_min', 'zoom_max', 'zoom_start', 'lat_start', 'lon_start',
    'marker_map_bounds_initial', 'marker_map_bounds_filter',
    'max_cluster_radius',
    'custom_marker_media_id', 'marker_icon_width', 'marker_icon_height', 'marker_icon_anchor_x', 'marker_icon_anchor_y',
    'show_location_contact', 'label_location_contact', 'show_location_opening_hours', 'label_location_opening_hours',
    'custom_marker_cluster_media_id', 'marker_cluster_icon_width', 'marker_cluster_icon_height',
    'cb_items_available_categories', 'cb_items_available_categories_custom_markup',
    'cb_items_preset_categories'];

  const EXPORT_CODE_VALUE_MIN_LENGTH = 10;
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

  const MAP_TYPE_DEFAULT = 1;
  const EXPORT_CODE_DEFAULT = "";
  const IMPORT_SOURCES_DEFAULT = [];
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
  const CUSTOM_MARKER_CLUSTER_MEDIA_ID_DEFAULT = null;
  const MARKER_CLUSTER_ICON_WIDTH_DEFAULT = 0;
  const MARKER_CLUSTER_ICON_HEIGHT_DEFAULT = 0;
  const SHOW_LOCATION_CONTACT_DEFAULT = false;
  const LABEL_LOCATION_CONTACT_DEFAULT = "";
  const SHOW_LOCATION_OPENING_HOURS_DEFAULT = false;
  const LABEL_LOCATION_OPENING_HOURS_DEFAULT = "";
  const CB_ITEMS_AVAILABLE_CATEGORIES_DEFAULT = [];
  const CB_ITEMS_AVAILABLE_CATEGORIES_CUSTOM_MARKUP_DEFAULT = [];
  const CB_ITEMS_PRESET_CATEGORIES_DEFAULT = [];

  //const MARKER_POPUP_CONTENT_DEFAULT = "'<b>' + location.location_name + '</b><br>' + location.address.street + '<br>' + location.address.zip + ' ' + location.address.city + '<p>' + location.opening_hours + '</p>'";

  public static $options;

  private static function load_options($cb_map_id = null, $force_reload = false) {
    if(!isset(self::$options) || $force_reload) {
      if($cb_map_id) {
        $options = get_post_meta( $cb_map_id, 'cb_map_options', true );

        if(!is_array($options)) {
          $options = [];
        }
      }
      else {
        $options = [];
      }

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
  public static function get_option($cb_map_id = null, $key) {
    self::load_options($cb_map_id);

    return self::$options[$key];
  }

  /**
  *
  **/
  public static function get_options($cb_map_id = null, $force_reload = false) {
    self::load_options($cb_map_id, $force_reload);

    return self::$options;
  }

  private static function get_option_default($option_name) {

    $default_name = strtoupper($option_name) . '_DEFAULT';

    $const_value = constant("self::$default_name");

    return isset($const_value) ? $const_value : null;
  }

  public static function strip_script_tags($input) {
    return preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $input );
  }

  /**
  * sanitize and validate the options provided by input array
  **/
  public static function validate_options($cb_map_id) {
    self::load_options($cb_map_id);

    $validated_input = self::populate_option_defaults([]);

    if(isset($_POST['cb_map_options'])) {
      $input = $_POST['cb_map_options'];
    }

    //map_type
    if(isset($input['map_type']) && (int) $input['map_type'] >= 1 && $input['map_type'] <= 3) {
      $validated_input['map_type'] = (int) $input['map_type'];
    }

    //export_code
    if(isset($input['export_code']) && ctype_alnum ($input['export_code']) && strlen($input['export_code']) >= self::EXPORT_CODE_VALUE_MIN_LENGTH) {
      $validated_input['export_code'] = $input['export_code'];
    }

    if(isset($input['import_sources'])) {
      if(is_array($input['import_sources']['urls']) && is_array($input['import_sources']['codes'])) {
        $validated_input['import_sources']['urls'] = [];
        $validated_input['import_sources']['codes'] = [];
        foreach ($input['import_sources']['urls'] as $key => $url) {
          $validated_input['import_sources']['urls'][] = sanitize_text_field($url);
          $validated_input['import_sources']['codes'][] = ctype_alnum ($input['export_code']) && strlen($input['export_code']) >= self::EXPORT_CODE_VALUE_MIN_LENGTH ? $input['import_sources']['codes'][$key] : '';
        }
      }
    }

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

    //show_location_opening_hours
    if(isset($input['show_location_opening_hours'])) {
      $validated_input['show_location_opening_hours'] = true;
    }
    else {
      $validated_input['show_location_opening_hours'] = false;
    }

    //label_location_opening_hours
    if(isset($input['label_location_opening_hours']) && strlen($input['label_location_opening_hours']) > 0) {
      $validated_input['label_location_opening_hours'] = sanitize_text_field($input['label_location_opening_hours']);
    }

    // custom_marker_cluster_media_id
    if(isset($input['custom_marker_cluster_media_id'])) {
      $validated_input['custom_marker_cluster_media_id'] = abs((int) $input['custom_marker_cluster_media_id']);
    }

    //label_location_contact
    if(isset($input['label_location_contact']) && strlen($input['label_location_contact']) > 0) {
      $validated_input['label_location_contact'] = sanitize_text_field($input['label_location_contact']);
    }

    //marker_cluster_icon_width
    if(isset($input['marker_cluster_icon_width'])) {
      $validated_input['marker_cluster_icon_width'] = abs((float) $input['marker_cluster_icon_width']);
    }

    //marker_cluster_icon_height
    if(isset($input['marker_cluster_icon_height'])) {
      $validated_input['marker_cluster_icon_height'] = abs((float) $input['marker_cluster_icon_height']);
    }

    //cb_items_available_categories && cb_items_available_categories_custom_markup
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

    //cb_items_available_categories_custom_markup
    if(isset($input['cb_items_available_categories_custom_markup'])) {
      foreach ($input['cb_items_available_categories_custom_markup'] as $cb_items_category_id => $markup) {
        $validated_input['cb_items_available_categories_custom_markup'][$cb_items_category_id] = self::strip_script_tags($markup);
      }

    }

    //cb_items_preset_categories
    if(isset($input['cb_items_preset_categories'])) {
      foreach($input['cb_items_preset_categories'] as $cb_items_category_id) {
        if(in_array((int) $cb_items_category_id, $valid_term_ids)) {
          $validated_input['cb_items_preset_categories'][] = $cb_items_category_id;
        }
      }
    }

    update_post_meta($cb_map_id, 'cb_map_options', $validated_input);

    if($validated_input['map_type'] == 2) {
      self::start_import_from_all_sources_of_map($cb_map_id);
    }

    return $validated_input;
  }

  /**
  * asynchronously import locations from all sources of given map
  **/
  public static function start_import_from_all_sources_of_map($cb_map_id) {
    $url = get_site_url(null, '', null) . '/wp-admin/admin-ajax.php';
    $auth_code = CB_Map::create_import_auth_code();

    update_post_meta( $cb_map_id, 'cb_map_import_auth_code', $auth_code );

    $args = [
      'blocking' => false,
      'body' => [
        'action' => 'cb_map_location_import_of_map',
        'cb_map_id' => $cb_map_id,
        'auth_code' => $auth_code
      ]
    ];

    wp_safe_remote_post($url, $args);

  }

  public function render_options_page($post) {
    //wp_nonce_field( basename( __FILE__ ), 'product_post_type_price_meta_box_nonce' );

    $cb_map_id = $post->ID;

    wp_enqueue_media();

    //load image upload script
    $script_path = CB_MAP_ASSETS_URL . 'js/cb-map-marker-upload.js';
    echo '<script src="' . $script_path . '"></script>';

    //map translation
    $translation = [
      'SELECT_IMAGE' => cb_map\__('SELECT_IMAGE', 'commons-booking-map', 'Select an image'),
      'SAVE' => cb_map\__('SAVE', 'commons-booking-map', 'save'),
      'MARKER_IMAGE_MEASUREMENTS' => cb_map\__('MARKER_IMAGE_MEASUREMENTS', 'commons-booking-map', 'measurements')
    ];
    echo '<script>cb_map_marker_upload.translation = ' . json_encode($translation) . ';</script>';

    $available_categories_args = [
      'taxonomy' => 'cb_items_category',
      'echo' => false,
      'checked_ontop' => false,
      'selected_cats' => self::get_option($cb_map_id, 'cb_items_available_categories')
    ];
    $available_categories_checklist_markup = wp_terms_checklist( 0, $available_categories_args);
    $available_categories_checklist_markup = str_replace('name="tax_input[cb_items_category]', 'class="cb_items_available_category" name="cb_map_options[cb_items_available_categories]', $available_categories_checklist_markup);

    $preset_categories_args = [
      'taxonomy' => 'cb_items_category',
      'echo' => false,
      'checked_ontop' => false,
      'selected_cats' => self::get_option($cb_map_id, 'cb_items_preset_categories')
    ];
    $preset_categories_checklist_markup = wp_terms_checklist( 0, $preset_categories_args);
    $preset_categories_checklist_markup = str_replace('name="tax_input[cb_items_category]', 'name="cb_map_options[cb_items_preset_categories]', $preset_categories_checklist_markup);

    $data_export_base_url = get_site_url(null, '', null) . '/wp-admin/admin-ajax.php';

    include_once( CB_MAP_PATH . 'templates/settings-page-template.php');

  }

}

?>
