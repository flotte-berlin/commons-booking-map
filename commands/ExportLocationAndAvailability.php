<?php

namespace CommonsBookingMap\Command;

use CommonsBookingMap\AvailabilityMap;
use CommonsBookingMap\LocationAvailabilityCache;

class ExportLocationAndAvailability
{
    public function exportLocationAndAvailability()
    {
        $cbMapId = 4160;
        $locations_with_availability = AvailabilityMap::get_locations_with_availability(
            $cbMapId,
            \CB_Map_Admin::get_option($cbMapId, 'cb_items_preset_categories'),
            1,
        );

        (new LocationAvailabilityCache())->write_to_cache(json_encode($locations_with_availability, true), 1);
    }
}
