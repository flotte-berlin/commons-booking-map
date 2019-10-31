<?php

class CB_Map_Filter {

  public static function filter_locations($locations, $cb_map_id, $filter_configurations) {
    //trigger_error('filter_locations: ' . count($filter_configurations));

    foreach($filter_configurations as $filter_type => $filter_configuration) {
      //trigger_error('$filter_configuration: ' . json_encode($filter_configuration));
      $params = [];

      switch($filter_type) {
        case 'timeframes_and_categories':
          $params = [$locations, $cb_map_id, $filter_configuration['preset_categories'], $filter_configuration['user_categories']];
        break;
        case 'item_availability':
          $params = [$locations, $filter_configuration['day_count']];
        break;
      }

      $locations = call_user_func_array(['CB_Map_Filter', 'filter_locations_by_' . $filter_type], $params);
    }

    return $locations;
  }

  /**
  * get all the locations of the map with provided id that belong to timeframes and filter by given categories
  **/
  public static function filter_locations_by_timeframes_and_categories($locations, $cb_map_id, $preset_categories = [], $user_categories = []) {
    //trigger_error('filter_locations_by_timeframes_and_categories');
    //var_dump($preset_categories);

    $cb_data = new CB_Data();
    require_once( CB_MAP_PATH . 'classes/class-cb-map-item-availability.php' );

    $result = [];
    $timeframes = CB_Map::get_timeframes();

    //$category_tree = CB_Map::get_structured_cb_items_category_tree();
    $preset_category_groups = CB_Map::get_cb_items_category_groups($preset_categories);

    $user_category_groups = CB_Map::get_user_category_groups($cb_map_id, $user_categories);

    foreach ($timeframes as $timeframe) {
      $location_id = $timeframe['location_id'];
      $item = $timeframe['item'];
      $is_valid_item = true;
      $item_terms = wp_get_post_terms( $item['id'], 'cb_items_category');

      if(count($preset_category_groups) > 0) {
        $is_valid_item = self::check_item_terms_against_categories($item_terms, $preset_category_groups);
      }

      if($is_valid_item && count($user_category_groups) > 0) {
        $is_valid_item = self::check_item_terms_against_categories($item_terms, $user_category_groups);
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

  protected static function filter_locations_by_item_availability($locations, $day_count) {
    //trigger_error('filter_locations_by_item_availability');
    $result = [];

    foreach ($locations as $location_id => $location) {
      foreach ($location['items'] as $item_id => &$item) {

        $max_free_days_in_row = 0;
        if(isset($locations[$location_id]['items'][$item_id]['availability'])) {
          //check if number of days (in a row) where item is available is at least as high as day count - otherwise remove item
          $max_free_days_in_row = self::get_max_free_days_in_row($locations[$location_id]['items'][$item_id]['availability'], $item);
        }

        //trigger_error($item['name'] . ': ' . $max_free_days_in_row);

        if($max_free_days_in_row < $day_count) {
          unset($locations[$location_id]['items'][$item_id]);
        }

      }
    }

    foreach ($locations as $location_id => $location) {
      //if location still has items, add to result
      if(count($location['items']) > 0) {
        $result[$location_id] = $location;
      }
    }

    return $result;
  }

  /**
  * convert availability to sequences to ease up counting of free days in a row
  */
  protected static function calc_availability_sequences($availability, $item) {

    $availability_sequences = [];

    foreach ($availability as $status) {
      if(count($availability_sequences) == 0) {
        $availability_sequences[] = [
          'status' => $status,
          'count' => 1
        ];
      }
      else {
        if($availability_sequences[count($availability_sequences) - 1]['status'] == $status) {
          $availability_sequences[count($availability_sequences) - 1]['count']++;
        }
        else {
          $availability_sequences[] = [
            'status' => $status,
            'count' => 1
          ];
        }
      }
    }

    //trigger_error($item['name'] . ': ' . json_encode($availability_sequences));

    return $availability_sequences;
  }

  protected static function get_max_free_days_in_row($availability, $item) {

    $availability_sequences = self::calc_availability_sequences($availability, $item);

    //trigger_error($item['name'] . ': ' . json_encode($availability_sequences));

    $max_free_days_in_row = 0;
    $current_free_days_in_row = 0;
    foreach ($availability_sequences as $seq_id => $availability_sequence) {
      if($availability_sequence['status'] == CB_Map_Item_Availability::ITEM_AVAILABLE) {
        $current_free_days_in_row += $availability_sequence['count'];
      }

      //closing days (value == 1) count only if days before & after are free (value == 0)
      if($availability_sequence['status'] == CB_Map_Item_Availability::LOCATION_CLOSED) {
        if(isset($availability_sequences[$seq_id - 1]) && $availability_sequences[$seq_id - 1]['status'] == CB_Map_Item_Availability::ITEM_AVAILABLE && isset($availability_sequences[$seq_id + 1]) && $availability_sequences[$seq_id + 1]['status'] == CB_Map_Item_Availability::ITEM_AVAILABLE) {
          $current_free_days_in_row += $availability_sequence['count'];
        }
      }

      //a row of free days end with a sequence status > LOCATION_CLOSED or end of $availability_sequences
      if($availability_sequence['status'] > CB_Map_Item_Availability::LOCATION_CLOSED || $seq_id == count($availability_sequences) - 1) {
        if($max_free_days_in_row < $current_free_days_in_row) {
          $max_free_days_in_row = $current_free_days_in_row;
        }
        $current_free_days_in_row = 0;
      }

    }

    return $max_free_days_in_row;
  }

  protected static function check_item_terms_against_categories($item_terms, $category_groups) {
    $valid_groups_count = 0;

    foreach ($category_groups as $group) {
      foreach ($item_terms as $term) {
        if(in_array($term->term_id, $group)) {
          $valid_groups_count++;
          break;
        }
      }
    }

    return $valid_groups_count == count($category_groups);
  }
}

?>
