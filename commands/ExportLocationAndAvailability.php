<?php

namespace CommonsBookingMap\Command;

use CB_Map_Admin;
use CommonsBookingMap\AvailabilityMap;
use CommonsBookingMap\LocationAvailabilityCache;

class ExportLocationAndAvailability
{

    public function write_location_and_availability_to_cache()
    {
        $cbMapId = 4160;
        $locations_with_availability = AvailabilityMap::get_locations_with_availability(
            $cbMapId,
            \CB_Map_Admin::get_option($cbMapId, 'cb_items_preset_categories'),
        );

        (new LocationAvailabilityCache())->write_to_cache($locations_with_availability, 1);
    }

    public function write_location_and_availability_for_export_to_cache(int $cb_map_id)
    {
        $locations_with_availability = AvailabilityMap::get_locations_with_availability_for_export(
            $cb_map_id,
            CB_Map_Admin::get_option($cb_map_id, 'cb_items_preset_categories')
        );

        (new LocationAvailabilityCache())->write_to_cache(json_encode($locations_with_availability, true), 3);
    }
}
