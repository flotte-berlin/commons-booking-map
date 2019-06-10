<?php

class CB_Map {

  /**
  * register cb_map as custom post type
  **/
  public static function register_cb_map_post_type() {
    $labels = array(
      'name'                  => cb_map\__( 'POST_LABELS_NAME', 'commons-booking-map', 'Commons Booking maps'),
      'singular_name'         => cb_map\__( 'POST_LABELS_SINGULAR_NAME', 'commons-booking-map', 'Commons Booking map'),
      'add_new'               => cb_map\__( 'POST_LABELS_ADD_NEW', 'commons-booking-map', 'create CB map'),
      'add_new_item'          => cb_map\__( 'POST_LABELS_ADD_NEW_ITEM', 'commons-booking-map', 'create Commons Booking map'),
      'edit_item'             => cb_map\__( 'POST_LABELS_EDIT_ITEM', 'commons-booking-map', 'edit Commons Booking map'),
      'new_item'              => cb_map\__( 'POST_LABELS_NEW_ITEM', 'commons-booking-map', 'create CB map'),
      'view_item'             => cb_map\__( 'POST_LABELS_VIEW_ITEM', 'commons-booking-map', 'view CB map'),
      'search_items'          => cb_map\__( 'POST_LABELS_SEARCH_ITEMS', 'commons-booking-map', 'search CB maps'),
      'not_found'             => cb_map\__( 'POST_LABELS_NOT_FOUND', 'commons-booking-map', 'no Commons Booking map found'),
      'not_found_in_trash'    => cb_map\__( 'POST_LABELS_NOT_FOUND_IN_TRASH', 'commons-booking-map', 'no Commons Booking map found in the trash'),
      'parent_item_colon'     => cb_map\__( 'POST_LABELS_PARENT_ITEM_COLON', 'commons-booking-map', 'parent CB maps'),
    );

    $supports = array(
      'title',
      'author'
    );

    $args = array(
      'labels' => $labels,
      'hierarchical' => false,
      'description' => cb_map\__( 'POST_TYPE_DESCRIPTION', 'commons-booking-map', 'Maps to show Commons Booking Locations and their Items'),
      'supports' => $supports,
      'public' => false,
      'show_ui' => true,
      'show_in_menu' => true,
      'menu_position' => 5, // below posts
      'menu_icon' => 'dashicons-location',
      'show_in_nav_menus' => true,
      'publicly_queryable' => false,
      'exclude_from_search' => false,
      'has_archive' => false,
      'query_var' => false,
      'can_export' => false,
      'delete_with_user' => false,
      'capability_type' => 'post'
    );

    register_post_type( 'cb_map', $args );
  }

  /**
  * plugin activation handler
  **/
  public static function activate() {
    //schedule daily event to update import data
    $date_time = new DateTime();
    $date_time->setTime(23, 00);
    wp_schedule_event( $date_time->getTimestamp(), 'daily', 'cb_map_import');
  }

  /**
  * plugin deactivation handler
  */
  public static function deactivate() {
    //clear the scheduled import data event
    wp_clear_scheduled_hook('cb_map_import');
  }

  /**
  * load all timeframes from db (that end in the future)
  **/
  public static function get_timeframes() {
    global $wpdb;

    $result = [];

    $now = new DateTime();
    $min_date_end = $now->format('Y-m-d');

    $table_name = $wpdb->prefix . 'cb_timeframes';
    $sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE date_end >= %s", $min_date_end );
    $timeframes = $wpdb->get_results($sql, ARRAY_A);

    foreach($timeframes as $key => $timeframe) {
      $item_desc = get_post_meta($timeframe['item_id'], 'commons-booking_item_descr', true);
      $item = get_post($timeframe['item_id']);

      $thumbnail = get_the_post_thumbnail_url($item, 'thumbnail');

      $result[] = [
        'location_id' => $timeframe['location_id'],
        'item' => [
          'id' => $item->ID,
          'name' => $item->post_title,
          'short_desc' => $item_desc,
          'link' => get_permalink($item),
          'thumbnail' => $thumbnail ? $thumbnail : null
        ],
        'date_start' => $timeframe['date_start'],
        'date_end' => $timeframe['date_end']
      ];
    }

    return $result;
  }

  /**
  * get geo data from location metadata
  */
  public static function get_locations($cb_map_id) {
    global $wpdb;
    $locations = [];

    $show_location_contact = CB_Map_Admin::get_option($cb_map_id, 'show_location_contact');
    $show_location_opening_hours = CB_Map_Admin::get_option($cb_map_id, 'show_location_opening_hours');

    $args = [
      'post_type'	=> 'cb_locations',
      'posts_per_page' => -1,
      'meta_query' => [
        [
          'key' => 'cb-map_latitude',
          'meta_compare' => 'EXISTS'
        ]/*,
        [
          'key' => 'cb-map_longitude',
          'meta_compare' => 'EXISTS'
        ]*/
      ]
    ];

    $query = new WP_Query( $args );

    foreach($query->posts as $post) {
      $location_meta = get_post_meta($post->ID, null, true);

      //set serialized empty array if not set
      $closed_days = isset($location_meta['commons-booking_location_closeddays']) ? $location_meta['commons-booking_location_closeddays'][0] : 'a:0:{}';

      $locations[$post->ID] = [
        'lat' => (float) $location_meta['cb-map_latitude'][0],
        'lon' => (float) $location_meta['cb-map_longitude'][0],
        'location_name' => $post->post_title,
        'closed_days' => unserialize($closed_days),
        'address' => [
          'street' => $location_meta['commons-booking_location_adress_street'][0],
          'city' => $location_meta['commons-booking_location_adress_city'][0],
          'zip' => $location_meta['commons-booking_location_adress_zip'][0]
        ],
        'items' => []
      ];

      if($show_location_contact) {
        $locations[$post->ID]['contact'] = $location_meta['commons-booking_location_contactinfo_text'][0];
      }

      if($show_location_opening_hours) {
        $locations[$post->ID]['opening_hours'] = $location_meta['commons-booking_location_openinghours'][0];
      }
    }

    return $locations;
  }

  /**
  * get all the locations of the map with provided id that belong to timeframes and filter by given categories
  **/
  public static function get_locations_by_timeframes($cb_map_id, $filter_categories = []) {
    //var_dump($filter_categories);

    $result = [];
    $timeframes = self::get_timeframes();
    $locations = self::get_locations($cb_map_id);

    foreach ($timeframes as $timeframe) {
      $location_id = $timeframe['location_id'];
      $item = $timeframe['item'];

      //check if item categories (terms) match the filters
      if(count($filter_categories) > 0) {
        $is_valid_item = false;
        $terms = wp_get_post_terms( $item['id'], 'cb_items_category');

        //var_dump($terms);
        $matched_terms = 0;
        foreach ($terms as $term) {
          if(in_array($term->term_id, $filter_categories)) {
            $matched_terms++;
          }
        }

        if($matched_terms == count($filter_categories)) {
          $is_valid_item = true;
        }

      }
      else {
        $is_valid_item = true;
      }

      if($is_valid_item) {

        //if a location exists, that is allowed to be shown on map
        if(isset($locations[$location_id])) {

          //if location is not present in result yet, add it
          if(!isset($result[$location_id])) {
            $result[$location_id] = $locations[$location_id];
          }
          //add item to location
          if(!isset($result[$location_id]['items'][$timeframe['item']['id']])) {
            $item['timeframes'] = [];
            $item['timeframe_hints'] = [];
            $result[$location_id]['items'][$timeframe['item']['id']] = $item;
          }

          //add timeframe to item
          $result[$location_id]['items'][$timeframe['item']['id']]['timeframes'][] = [
            'date_start' => $timeframe['date_start'],
            'date_end' => $timeframe['date_end']
          ];

          //add timeframe hint
          $now = new DateTime();

          $date_start = new DateTime();
          $date_start->setTimestamp(strtotime($timeframe['date_start']));

          $date_end = new DateTime();
          $date_end->setTimestamp(strtotime($timeframe['date_end']));
          $diff_end = $date_end->diff($now)->format("%a");

          $cb_data = new CB_Data();

          //show hint if timeframe starts in the future
          if($date_start > $now) {
            $result[$location_id]['items'][$timeframe['item']['id']]['timeframe_hints'][] = ['type' => 'from', 'timestamp' => strtotime($timeframe['date_start'])];
          }

          //show hint for near end of timeframe if it's before the last possible day to book (CB settings)
          if($diff_end <= $cb_data->daystoshow) {
            $result[$location_id]['items'][$timeframe['item']['id']]['timeframe_hints'][] = ['type' => 'until', 'timestamp' => strtotime($timeframe['date_end'])];
          }

        }
      }
    }

    //convert items to nummeric array
    foreach ($result as &$location) {
      $location['items'] = array_values($location['items']);
    }

    return $result;
  }

  /**
  * handler for location import test
  **/
  public static function handle_location_import_test() {

    $import_result = self::fetch_locations((int) $_POST['cb_map_id'], $_POST['url'], $_POST['code']);

    if(!$import_result) {
      wp_send_json_error(null, 400);
    }

    wp_die();
  }

  /**
  * basic check if the given string is valid JSON
  **/
  public static function is_json($string) {
   json_decode($string);
   return (json_last_error() == JSON_ERROR_NONE);
  }

  /**
  * validates the given JSON string against the location import schema
  **/
  public static function validate_json($string) {

    if(self::is_json($string)) {
      require_once CB_MAP_PATH . 'libs/vendor/autoload.php';

      $data = json_decode($string);

      $validator = new JsonSchema\Validator;
      $schema_file_path = CB_MAP_PATH . 'schemas/locations-import.json';
      $validator->validate($data, (object)['$ref' => 'file://' . $schema_file_path], JsonSchema\Constraints\Constraint::CHECK_MODE_COERCE_TYPES);

      //trigger_error($string);

      if ($validator->isValid()) {
          return $string;
      } else {
          $errors = '';
          foreach ($validator->getErrors() as $error) {
              $errors .= sprintf("[%s] %s\n", $error['property'], $error['message']);
          }

          trigger_error("JSON does not validate. Violations: " . $errors);

          return false;
      }
    }
    else {
      return false;
    }

  }

  /**
  * execute an ajax post request to fetch locations from a remote source
  **/
  public static function fetch_locations($cb_map_id, $url, $code) {

    $post = get_post($cb_map_id);

    if($post && $post->post_type == 'cb_map') {
      $map_type = CB_Map_Admin::get_option($cb_map_id, 'map_type');

      if($map_type == 2) {
        $args = [
          'body' => [
            'action' => 'cb_map_locations',
            'code' => $code
          ]
        ];

        $data = wp_safe_remote_post($url, $args);

        if(is_wp_error($data)) {
            trigger_error($data->get_error_message());
            return false;
        }
        else {
          if($data['response']['code'] == 200) {
            //validate against json schema
            return self::validate_json($data['body']);

          }
          else {
            return false;
          }
        }
      }
      else {
        return false;
      }
    }
    else {
      return false;
    }

  }

  /**
  * create an import id (md5 hash) based on given url and import code
  **/
  public static function create_import_id($url, $code) {
    $url_hash = hash('md5', $url);
    return $url_hash . '_' . $code;
  }

  /**
  * create a temporary random authentication code for a background location import
  **/
  public static function create_import_auth_code() {
    $random_string_length = 24;
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $string = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $random_string_length; $i++) {
         $string .= $characters[mt_rand(0, $max)];
    }

    return $string;
  }

  /**
  * import all locations - for usage with cronjob
  */
  public static function import_all_locations() {

    //find maps of type import
    $args = [
      'post_type' => 'cb_map'
    ];
    $cb_maps = get_posts($args);

    foreach ($cb_maps as $cb_map) {
      $options = get_post_meta( $cb_map->ID, 'cb_map_options', true );

      if($options['map_type'] == 2) {
        self::import_all_locations_of_map($cb_map->ID);
      }
    }
  }

  /**
  * handles the request for location import
  **/
  public static function handle_location_import_of_map() {
    $cb_map_id = (int) $_POST['cb_map_id'];

    //get the temporary import authentication code
    $import_auth_code = get_post_meta( $cb_map_id, 'cb_map_import_auth_code', true );

    if($import_auth_code == $_POST['auth_code']) {
      delete_post_meta($cb_map_id, 'cb_map_import_auth_code');
      self::import_all_locations_of_map($cb_map_id);
    }

    wp_die();
  }

  /**
  * import all locations from remote sources of the given map
  **/
  public static function import_all_locations_of_map($cb_map_id) {

    $map_imports = get_post_meta( $cb_map_id, 'cb_map_imports', true );

    if(!is_array($map_imports)) {
      $map_imports = [];
    }

    $new_map_imports = [];

    $import_sources = CB_Map_Admin::get_option($cb_map_id, 'import_sources');

    foreach ($import_sources['urls'] as $key => $url) {
      $code = $import_sources['codes'][$key];
      $import_id = self::create_import_id($url, $code);

      $locations_json = self::fetch_locations($cb_map_id, $url, $code);

      $locations = CB_Map::cleanup_location_data(json_decode($locations_json, true), '<br>', 2);

      if($locations) {
        $new_map_imports[$import_id] = base64_encode(json_encode($locations, JSON_UNESCAPED_UNICODE));
      }
      else {
        if(isset($map_imports[$import_id])) {
            $new_map_imports[$import_id] = $map_imports[$import_id];
        }
      }

    }

    update_post_meta($cb_map_id, 'cb_map_imports', $new_map_imports);
  }

  /**
  * recursive clean up of location data entries
  **/
  public static function cleanup_location_data_entry($key, $value, $linebreak_replacement, $map_type) {

    if(is_string($value)) {
      $value = preg_replace('/(\r\n)|\n|\r/', $linebreak_replacement, $value); //replace linebreaks
      $value = preg_replace('/<.*(.*?)/', '', $value); //strip off everything that smell's like HTML
    }

    if(is_array($value)) {
      foreach ($value as $child_key => &$child_value) {
        $child_value = self::cleanup_location_data_entry($child_key, $child_value, $linebreak_replacement, $map_type);
      }
    }

    //URL encoding/decoding of thumbnail url
    if($key === 'thumbnail' && is_string($value)) {
      if($map_type == 3) {
        $url_array = explode ( '/' , $value);
        foreach ($url_array as $index => &$url_part) {
          if($index > 0) {
            $url_part = rawurlencode($url_part);
          }
        }
        $value = implode('/', $url_array);
      }

      if($map_type == 2) {
        $value = rawurldecode($value);
      }
    }

    return $value;
  }

  /**
  * clean up the location data
  **/
  public static function cleanup_location_data($locations, $linebreak_replacement, $map_type) {
    foreach ($locations as $key => &$location) {
      $location = self::cleanup_location_data_entry($key, $location, $linebreak_replacement, $map_type);
    }

    return $locations;
  }

  /**
  * enforce the replacement of the original (google maps) link target on cb_item booking pages
  **/
  public static function replace_map_link_target() {
    global $post;
  	$cb_item = 'cb_items';
  	if ( $post->post_type == $cb_item) {
  		$itemId = $post->ID;

      //get timeframes of item
      $cb_data = new CB_Data();
      $date_start = date('Y-m-d'); // current date
      $timeframes = $cb_data->get_timeframes( $post->ID, $date_start );

      $geo_coordinates = [];
      if($timeframes) {
          foreach ($timeframes as $timeframe) {
            $location =
            $geo_coordinates[$timeframe['id']] = [
              'lat' => get_post_meta($timeframe['location_id'], 'cb-map_latitude', true),
              'lon' => get_post_meta($timeframe['location_id'], 'cb-map_longitude', true)
            ];
          }
      }

      wp_register_script( 'cb_map_replace_map_link_js', CB_MAP_ASSETS_URL . 'js/cb-map-replace-link.js' );

      wp_add_inline_script( 'cb_map_replace_map_link_js',
      "cb_map_timeframes_geo = " . json_encode($geo_coordinates) . ";");

      wp_enqueue_script( 'cb_map_replace_map_link_js' );
    }
  }

}

?>
