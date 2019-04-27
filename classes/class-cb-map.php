<?php

class CB_Map {

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

      $result[] = [
        'location_id' => $timeframe['location_id'],
        'item' => [
          'id' => $item->ID,
          'name' => $item->post_title,
          'short_desc' => $item_desc,
          'link' => get_permalink($item),
          'thumbnail' => get_the_post_thumbnail_url($item, 'thumbnail')
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
  public static function get_locations() {
    global $wpdb;
    $locations = [];

    $args = [
      'post_type'	=> 'cb_locations',
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
        'opening_hours' => $location_meta['commons-booking_location_openinghours'][0],
        'contact' => $location_meta['commons-booking_location_contactinfo_text'][0],
        'closed_days' => unserialize($closed_days),
        'address' => [
          'street' => $location_meta['commons-booking_location_adress_street'][0],
          'city' => $location_meta['commons-booking_location_adress_city'][0],
          'zip' => $location_meta['commons-booking_location_adress_zip'][0]
        ],
        'items' => []
      ];
    }

    return $locations;
  }

  public static function get_locations_by_timeframes() {

    $result = [];
    $timeframes = self::get_timeframes();
    $locations = self::get_locations();

    foreach ($timeframes as $timeframe) {
      $location_id = $timeframe['location_id'];

      //if a location exists, that is allowed to be shown on map
      if(isset($locations[$location_id])) {

        //if location is not present in result yet, add it
        if(!isset($result[$location_id])) {
          $result[$location_id] = $locations[$location_id];
        }
        //add item to location
        if(!isset($result[$location_id]['items'][$timeframe['item']['id']])) {
          $item = $timeframe['item'];
          $item['timeframes'] = [
            [
              'date_start' => $timeframe['date_start'],
              'date_end' => $timeframe['date_end']
            ]
          ];
          $result[$location_id]['items'][$timeframe['item']['id']] = $item;
        }
        else {
          $result[$location_id]['items'][$timeframe['item']['id']]['timeframes'][] = [
            'date_start' => $timeframe['date_start'],
            'date_end' => $timeframe['date_end']
          ];
        }
      }
    }

    //convert items to nummeric array
    foreach ($result as &$location) {
      $location['items'] = array_values($location['items']);
    }

    return $result;
  }
}

?>
