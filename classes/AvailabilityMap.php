<?php

namespace CommonsBookingMap;

use CB_Map;
use CB_Map_Admin;
use CB_Map_Filter;
use CB_Map_Shortcode;

class AvailabilityMap
{
    /**
     * @param int $cb_map_id
     * @param mixed $preset_categories
     * @param mixed $map_type
     * @return mixed
     */
    public static function get_locations_with_availability(int $cb_map_id, array $preset_categories, int $map_type): array
    {
        $locations = CB_Map::get_locations($cb_map_id);
        $locations = CB_Map_Filter::filter_locations_by_timeframes_and_categories($locations, $cb_map_id, $preset_categories);

        $settings = CB_Map_Shortcode::get_settings($cb_map_id);
        $default_date_start = $settings['filter_availability']['date_min'];
        $default_date_end = $settings['filter_availability']['date_max'];

        //create availabilities
        $show_item_availability = CB_Map_Admin::get_option($cb_map_id, 'show_item_availability');
        if ($show_item_availability) {
            $locations = CBMapItemAvailability::create_items_availabilities($locations, $default_date_start, $default_date_end);
        }

        $locations = CBMapItemAvailability::availability_to_indexed_array($locations);
        $locations = array_values($locations); //locations to indexed array
        $locations = CB_Map::cleanup_location_data($locations, '<br>', $map_type);
        return $locations;
    }
}