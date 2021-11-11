<?php

use CommonsBookingMap\CBMapItemAvailability;

class AvailabilityMapTest extends \PHPUnit\Framework\TestCase
{
    public function testAvailabilitiesWithZeroReservations()
    {
        $locations = json_decode(file_get_contents(__DIR__ . '/assets/locations.json'), true);
        $endDate = "2021-12-07";
        $startDate = "2021-11-07";
        $expectedItemAvailabilites = json_decode(file_get_contents(__DIR__ . '/assets/expectedLocationAvailabilitesWithZeroBookings.json'), true);
        self::assertSame(
            $expectedItemAvailabilites,
            CBMapItemAvailability::create_items_availabilities(
                $locations,
                $startDate,
                $endDate
            )
        );

    }
}