<?php

namespace CommonsBookingMap;

class LocationAvailabilityCache
{
    public function write_to_cache(string $location_availabilities, int $map_type) : void
    {
        global $wpdb;
        $updated_at = date("Y-m-d H:i:s");
        $location_availabilities = $wpdb->_real_escape($location_availabilities);
        $str = "REPLACE INTO {$wpdb->prefix}location_availability_cache (map_type, updated_at, cache) VALUES ($map_type, '$updated_at', '$location_availabilities')";
        $wpdb->prepare($str);
        $wpdb->get_results($str);
    }

    public function load_from_cache(int $map_type) : string
    {
        global $wpdb;
        $results = $wpdb->get_results("SELECT cache FROM {$wpdb->prefix}location_availability_cache WHERE map_type = $map_type", ARRAY_A);
        return $results[0]['cache'];
    }
}