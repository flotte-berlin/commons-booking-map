<?php

namespace CommonsBookingMap;

class LocationAvailabilityCache
{
    public function write_to_cache(array $locations_with_availabilities, int $map_type, int $cb_map_id) : void
    {
        global $wpdb;
        $location_availabilities_string= json_encode($locations_with_availabilities, true);
        $location_availabilities = $wpdb->_real_escape($location_availabilities_string);

        $updated_at = date("Y-m-d H:i:s");

        $args = [$map_type, $cb_map_id, $updated_at, $location_availabilities];
        $str = "REPLACE INTO {$wpdb->prefix}cb_map_cache (map_type, cb_map_id, updated_at, cache) VALUES (%d, %d, %s, %s)";
        $str = $wpdb->prepare($str, $args);
        $wpdb->query($str);
    }

    /**
     * @throws \Exception
     */
    public function load_from_cache(int $map_type, int $cb_map_id) : array
    {
        global $wpdb;
        $results = $wpdb->get_results("SELECT cache FROM {$wpdb->prefix}cb_map_cache WHERE map_type = $map_type AND cb_map_id = $cb_map_id", ARRAY_A);
        if (count($results) === 0) {
            throw new \Exception("cache miss for map_type ".$map_type." and cb_map_id: ".$cb_map_id);
        }
        
        return json_decode(stripslashes($results[0]['cache']), true);
    }

    static public function addAvailabilityMapCacheRefreshInterval($schedules)
    {
        $customerInterval = (new \CB_Map_Settings())->get_option('cb_map_cache_interval_in_minutes');
        if (!isset($schedules["everyNMinutes"])) {
            $schedules["everyNMinutes"] = [
                'interval' => $customerInterval * 60,
                'display' => __('Once every N minutes')];
        }
        return $schedules;
    }

    static public function scheduleRecurringEvent()
    {
        wp_schedule_event(time(), 'everyNMinutes', 'cb_map_cache_hook');
    }

    static public function exportLocationAvailabilitiesAction()
    {
        $exportLocationAvailability = new Command\ExportLocationAndAvailability();

        //find maps with corresponding code
        $args = [
            'post_type' => 'cb_map',
            'numberposts' => -1
        ];
        $cb_maps = get_posts($args);

        foreach ($cb_maps as $cb_map) {
            \CB_Map_Admin::load_options($cb_map->ID, true);
            $map_type = \CB_Map_Admin::get_option($cb_map->ID, 'map_type');
            $show_item_availability = \CB_Map_Admin::get_option($cb_map->ID, 'show_item_availability');

            if ($map_type == 1 && $show_item_availability) {
                $exportLocationAvailability->write_location_and_availability_to_cache($cb_map->ID);
            }
        }
    }
}