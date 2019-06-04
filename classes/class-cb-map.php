<?php

class CB_Map {

  public static function register_cb_map_post_type() {
    $labels = array(
      'name'                  => cb_map\__( 'POST_LABELS_NAME', 'commons-booking-map', 'Commons Booking maps'),
      'singular_name'         => cb_map\__( 'POST_LABELS_SINGULAR_NAME', 'commons-booking-map', 'Commons Booking map'),
      'add_new'               => cb_map\__( 'POST_LABELS_ADD_NEW', 'commons-booking-map', 'create CB map'),
      'add_new_item'          => cb_map\__( 'POST_LABELS_ADD_NEW_ITEM', 'commons-booking-map', 'create Commons Booking map'),
      'edit_item'             => cb_map\__( 'POST_LABELS_EDIT_ITEM', 'commons-booking-map', 'edit Commons Booking map'),
      'new_item'              => cb_map\__( 'POST_LABELS_NEW_ITEM', 'commons-booking-map', 'create CB map'),
      'view_item'             => cb_map\__( 'POST_LABELS_VIEW_ITEM', 'commons-booking-map', 'view CB map'),
      //'view_items'            => cb_map\__( 'POST_LABELS_VIEW_ITEMS', 'commons-booking-map', 'view CB maps'),
      'search_items'          => cb_map\__( 'POST_LABELS_SEARCH_ITEMS', 'commons-booking-map', 'search CB maps'),
      'not_found'             => cb_map\__( 'POST_LABELS_NOT_FOUND', 'commons-booking-map', 'no Commons Booking map found'),
      'not_found_in_trash'    => cb_map\__( 'POST_LABELS_NOT_FOUND_IN_TRASH', 'commons-booking-map', 'no Commons Booking map found in the trash'),
      'parent_item_colon'     => cb_map\__( 'POST_LABELS_PARENT_ITEM_COLON', 'commons-booking-map', 'parent CB maps'),
      //'all_items'             => cb_map\__( 'POST_LABELS_ALL_ITEMS', 'commons-booking-map', 'all maps'),
      //'archives'              => cb_map\__( 'POST_LABELS_ARCHIVES', 'commons-booking-map', 'CB map archive'),
      //'attributes'            => cb_map\__( 'POST_LABELS_ATTRIBUTES', 'commons-booking-map', 'CB maps attributes'),
      //'insert_into_item'      => cb_map\__( 'POST_LABELS_INSERT_INTO_ITEM', 'commons-booking-map', 'insert into to CB map'),
      //'uploaded_to_this_item' => cb_map\__( 'POST_LABELS_UPLOADED_TO_THIS_ITEM', 'commons-booking-map', 'uploaded to this CB map'),
      //'featured_image'        => cb_map\__( 'POST_LABELS_FEATURED_IMAGE', 'commons-booking-map', 'featured image of CB map'),
      //'set_featured_image'    => cb_map\__( 'POST_LABELS_SET_FEATURED_IMAGE', 'commons-booking-map', 'set featured image of CB map'),
      //'remove_featured_image' => cb_map\__( 'POST_LABELS_REMOVE_FEATURED_IMAGE', 'commons-booking-map', 'remove featured image of CB map'),
      //'use_featured_image'    => cb_map\__( 'POST_LABELS_USE_FEATURED_IMAGE', 'commons-booking-map', 'use as featured image of CB map'),
      //'menu_name'             => cb_map\__( 'POST_LABELS_MENU_NAME', 'commons-booking-map', 'Commons Booking Maps'),
    );

    $supports = array(
      'title',
      //'editor', //content
      //'excerpt',
      'author',
      //'thumbnail', // featured image
      //'trackbacks',
      //'custom-fields',
      //'revisions',
      //'page-attributes',
      //'comments'
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

  public static function add_meta_boxes() {
    self::add_settings_meta_box('cb_map_settings', cb_map\__( 'CB_MAP_SETTINGS_METABOX_TITLE', 'commons-booking-map', 'Map Configuration'));
  }

  public static function add_settings_meta_box($meta_box_id, $meta_box_title) {
    global $post;

    require_once( CB_MAP_PATH . 'classes/class-cb-map-settings.php' );
    $cb_map_settings = new CB_Map_Settings();

    $plugin_prefix = 'cb_map_post_type_';

    $html_id_attribute = $plugin_prefix . $meta_box_id . '_meta_box';
    $callback = array($cb_map_settings, 'render_options_page');
    $show_on_post_type = 'cb_map';
    $box_placement = 'normal';
    $box_priority = 'high';

    add_meta_box(
        $html_id_attribute,
        $meta_box_title,
        $callback,
        $show_on_post_type,
        $box_placement,
        $box_priority
    );
  }

  public static function activate() {
    $date_time = new DateTime();
    $date_time->setTime(23, 00);
    wp_schedule_event( $date_time->getTimestamp(), 'daily', 'cb_map_import');
  }

  public static function deactivate() {
    wp_clear_scheduled_hook('cb_map_import');
  }

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

    $show_location_contact = CB_Map_Settings::get_option($cb_map_id, 'show_location_contact');
    $show_location_opening_hours = CB_Map_Settings::get_option($cb_map_id, 'show_location_opening_hours');

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

  public static function handle_location_import_test() {

    $import_result = self::fetch_locations((int) $_POST['cb_map_id'], $_POST['url'], $_POST['code']);

    if(!$import_result) {
      wp_send_json_error(null, 400);
    }

    wp_die();
  }

  public static function is_json($string) {
   json_decode($string);
   return (json_last_error() == JSON_ERROR_NONE);
  }

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

  public static function fetch_locations($cb_map_id, $url, $code) {

    $post = get_post($cb_map_id);

    if($post && $post->post_type == 'cb_map') {
      $map_type = CB_Map_Settings::get_option($cb_map_id, 'map_type');

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

  public static function create_import_id($url, $code) {
    $url_hash = hash('md5', $url);
    return $url_hash . '_' . $code;
  }

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
  * for usage with cronjob
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

  public static function handle_location_import_of_map() {
    $cb_map_id = (int) $_POST['cb_map_id'];
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

    $import_sources = CB_Map_Settings::get_option($cb_map_id, 'import_sources');

    foreach ($import_sources['urls'] as $key => $url) {
      $code = $import_sources['codes'][$key];
      $import_id = self::create_import_id($url, $code);

      $locations = self::fetch_locations($cb_map_id, $url, $code);

      if($locations) {
        $new_map_imports[$import_id] = base64_encode($locations);
      }
      else {
        if(isset($map_imports[$import_id])) {
            $new_map_imports[$import_id] = $map_imports[$import_id];
        }
      }

    }

    update_post_meta($cb_map_id, 'cb_map_imports', $new_map_imports);
  }

}

?>
