<?php

namespace CommonsBookingMap;

class LocationAvailabilityCache
{
    public function write_to_cache(array $locations_with_availabilities, int $map_type) : void
    {
        global $wpdb;
        $location_availabilities_string= json_encode($locations_with_availabilities, true);
        $location_availabilities = $wpdb->_real_escape($location_availabilities_string);

        $updated_at = date("Y-m-d H:i:s");

        $str = "REPLACE INTO {$wpdb->prefix}location_availability_cache (map_type, updated_at, cache) VALUES ($map_type, '$updated_at', '$location_availabilities')";
        $wpdb->prepare($str);
        $wpdb->get_results($str);
    }

    /**
     * @throws \Exception
     */
    public function load_from_cache(int $map_type) : array
    {
        global $wpdb;
        $results = $wpdb->get_results("SELECT cache FROM {$wpdb->prefix}location_availability_cache WHERE map_type = $map_type", ARRAY_A);
        if (count($results) === 0) {
            throw new \Exception("cache miss for map_type ".$map_type);
        }
        return json_decode($results[0]['cache'], true);
    }
}