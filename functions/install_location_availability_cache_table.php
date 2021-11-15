<?php

global $location_availability_cache_db_version;
$location_availability_cache_db_version = '1.0';

function location_availability_cache_install() {
    global $wpdb;
    global $location_availability_cache_db_version;

    $table_name = $wpdb->prefix . 'location_availability_cache';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		map_type smallint(1) NOT NULL,
		updated_at datetime NOT NULL,
		cache LONGTEXT NOT NULL,
		PRIMARY KEY  (map_type)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'location_availability_cache_db_version', $location_availability_cache_db_version );
}