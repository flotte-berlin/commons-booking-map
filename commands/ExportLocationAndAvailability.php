<?php

namespace CommonsBookingMap\Command;

use CommonsBookingMap\AvailabilityMap;

class ExportLocationAndAvailability
{
    public function exportLocationAndAvailability()
    {
        $cbMapId = 4160;
        file_put_contents('locationWithAvailabilites.out', json_encode(AvailabilityMap::getLocationsWithAvailability(
            $cbMapId,
            \CB_Map_Admin::get_option($cbMapId, 'cb_items_preset_categories'),
            1,
        ), true));
    }
}
