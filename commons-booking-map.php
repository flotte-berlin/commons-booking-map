<?php

/*
Plugin Name:  Commons Booking Map
Plugin URI:   https://github.com/flotte-berlin/commons-booking-map
Description:  Ein Plugin in Ergänzung zu Commons Booking, das die Einbindung einer Karte von verfügbaren Artikeln erlaubt
Version:      0.11.3
Author:       <a href="https://github.com/poilu">poilu</a>, <a href="https://github.com/thrillfall">thrillfall</a>
License:      GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

use CommonsBookingMap\Command\ExportLocationAndAvailability;

define('CB_MAP_PATH', plugin_dir_path(__FILE__));
define('CB_MAP_ASSETS_URL', plugins_url('assets/', __FILE__));
define('CB_MAP_LANG_PATH', dirname(plugin_basename(__FILE__)) . '/languages/');
define('CB_MAP_PLUGIN_DATA', get_file_data(__FILE__, array('Version' => 'Version'), false));

require_once(CB_MAP_PATH . 'functions/is-plugin-active.php');
require_once(CB_MAP_PATH . 'functions/get-active-plugin-directory.php');
require_once(CB_MAP_PATH . 'functions/install_cb_map_cache_table.php');

if (cb_map\is_plugin_active('commons-booking.php')) {

    require_once(CB_MAP_PATH . 'functions/translate.php');
    load_plugin_textdomain('commons-booking-map', false, CB_MAP_LANG_PATH);

    require_once(CB_MAP_PATH . 'classes/class-cb-map.php');
    require_once(CB_MAP_PATH . 'classes/class-cb-map-filter.php');
    require_once(CB_MAP_PATH . 'classes/class-cb-map-settings.php');
    require_once(CB_MAP_PATH . 'classes/LocationAvailabilityCache.php');

    $cb_map_settings = new CB_Map_Settings();
    $cb_map_settings->prepare_settings();
    add_filter("plugin_action_links_" . plugin_basename(__FILE__), array($cb_map_settings, 'add_settings_link'));

    require_once(CB_MAP_PATH . 'classes/class-cb-map-admin.php');
    add_action('init', 'CB_Map::register_cb_map_post_type');
    add_action('save_post_cb_map', 'CB_Map_Admin::validate_options', 10, 3);
    add_action('add_meta_boxes_cb_map', 'CB_Map_Admin::add_meta_boxes');

    require_once(CB_MAP_PATH . 'classes/class-cb-map-shortcode.php');
    add_action('wp_ajax_cb_map_locations', 'CB_Map_Shortcode::get_locations');
    add_action('wp_ajax_nopriv_cb_map_locations', 'CB_Map_Shortcode::get_locations');
    add_action('wp_ajax_cb_map_geo_search', 'CB_Map_Shortcode::geo_search');
    add_action('wp_ajax_nopriv_cb_map_geo_search', 'CB_Map_Shortcode::geo_search');
    add_shortcode('cb_map', 'CB_Map_Shortcode::execute');

    add_action('wp_ajax_cb_map_import_source_test', 'CB_Map::handle_location_import_test');
    add_action('wp_ajax_nopriv_cb_map_import_source_test', 'CB_Map::handle_location_import_test');

    add_action('wp_ajax_cb_map_location_import_of_map', 'CB_Map::handle_location_import_of_map');
    add_action('wp_ajax_nopriv_cb_map_location_import_of_map', 'CB_Map::handle_location_import_of_map');

    //location map administration
    require_once(CB_MAP_PATH . 'classes/class-cb-location-map-admin.php');
    $cb_map_admin = new CB_Location_Map_Admin();
    add_action('plugins_loaded', array($cb_map_admin, 'load_location_map_admin'));

    add_action('cb_map_import', 'CB_Map::import_all_locations');
    register_activation_hook(__FILE__, 'CB_Map::activate');
    register_deactivation_hook(__FILE__, 'CB_Map::deactivate');

    register_activation_hook(__FILE__, 'cb_map\cb_map_cache_install');

    if ($cb_map_settings->get_option('booking_page_link_replacement')) {
        //add_filter( 'wp_enqueue_scripts', 'CB_Map::replace_map_link_target');
        add_action('wp_enqueue_scripts', 'CB_Map::replace_map_link_target', 11);
    }

    require_once dirname(__FILE__) . "/vendor/autoload.php";

    //add custom intervals for caching of maps (with item availability)
    add_filter('cron_schedules', 'CommonsBookingMap\LocationAvailabilityCache::addAvailabilityMapCacheRefreshInterval');
    register_activation_hook(__FILE__, 'CommonsBookingMap\LocationAvailabilityCache::scheduleRecurringEvent');
    register_deactivation_hook(__FILE__, 'CommonsBookingMap\LocationAvailabilityCache::unscheduleRecurringEvent');
    add_action('cb_map_cache_hook', 'CommonsBookingMap\LocationAvailabilityCache::exportLocationAvailabilitiesAction');

}
