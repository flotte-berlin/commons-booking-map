<?php

/*
Plugin Name:  Commons Booking Map
Plugin URI:   https://github.com/flotte-berlin/commons-booking-map
Description:  Ein Plugin in Ergänzung zu Commons Booking, das die Einbindung einer Karte von verfügbaren Artikeln erlaubt
Version:      0.2.0
Author:       poilu
Author URI:   https://github.com/poilu
License:      GPLv2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

define( 'CB_MAP_PATH', plugin_dir_path( __FILE__ ) );
define( 'CB_MAP_ASSETS_URL', plugins_url( 'assets/', __FILE__ ));
define( 'CB_MAP_LANG_PATH', dirname( plugin_basename( __FILE__ )) . '/languages/' );

require_once( CB_MAP_PATH . 'functions/translate.php' );

load_plugin_textdomain( 'commons-booking-map', false, CB_MAP_LANG_PATH );

require_once( CB_MAP_PATH . 'classes/class-cb-map-shortcode.php' );

add_action( 'wp_ajax_cb_map_locations', 'CB_Map_Shortcode::get_locations' );
add_action( 'wp_ajax_nopriv_cb_map_locations', 'CB_Map_Shortcode::get_locations' );

add_shortcode( 'cb_map', 'CB_Map_Shortcode::handle' );

// settings
require_once( CB_MAP_PATH . 'classes/class-cb-map-settings.php' );
$cb_map_settings = new CB_Map_Settings();
$cb_map_settings->prepare_settings();
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), array($cb_map_settings, 'add_settings_link') );

//location admininstration
require_once( CB_MAP_PATH . 'classes/class-cb-map-admin.php' );
$cb_map_admin = new CB_Map_Admin();
add_action( 'plugins_loaded', array($cb_map_admin, 'load_special_days_admin'));
