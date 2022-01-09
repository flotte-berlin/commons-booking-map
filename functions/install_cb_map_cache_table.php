<?php

namespace cb_map;

global $cb_map_cache_db_version;
$cb_map_cache_db_version = '1.0';

function cb_map_cache_install() {
    global $wpdb;
    global $cb_map_cache_db_version;

    $table_name = $wpdb->prefix . 'cb_map_cache';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        cb_map_id bigint(20) NOT NULL,
		map_type smallint(1) NOT NULL,
		updated_at datetime NOT NULL,
		cache LONGTEXT NOT NULL,
		PRIMARY KEY  (cb_map_id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( 'cb_map_cache_db_version', $cb_map_cache_db_version );
}