<?php

class CB_Map_Settings {

  const OPTION_KEYS = ['booking_page_link_replacement'];

  const BOOKING_PAGE_LINK_REPLACEMENT_DEFAULT = true;

  public static $options;

  /**
  * prepare the plugin's settings and add settings page
  **/
  public function prepare_settings() {

    add_action('admin_menu', function() {
        add_options_page( cb_map\__('SETTINGS_TITLE', 'commons-booking-map', 'Settings for Commons Booking Map'), cb_map\__('SETTINGS_MENU', 'commons-booking-map', 'Commons Booking Map' ), 'manage_options', 'commons-booking-map', array($this, 'render_settings_page') );
    });

    add_action( 'admin_init', function() {
      register_setting( 'cb-map-settings', 'cb_map_options', array($this, 'validate_options') );
    });

  }

  /**
  * load CB Map settings options
  **/
  private static function load_options() {
    if(!isset(self::$options)) {
      $options = get_option('cb_map_options', array());
      self::$options = self::populate_option_defaults($options);
    }
  }

  /**
  * populate the default values to the options
  **/
  public static function populate_option_defaults($options) {
    //var_dump($options);

    foreach (self::OPTION_KEYS as $key) {
      if(!isset($options[$key])) {
        $options[$key] = self::get_option_default($key);
      }
    }

    return $options;
  }

  /**
  * option getter
  **/
  public static function get_option($key) {
    self::load_options();

    return self::$options[$key];
  }

  /**
  * get the default value for the option with given name
  **/
  private static function get_option_default($option_name) {

    $default_name = strtoupper($option_name) . '_DEFAULT';

    $const_value = constant("self::$default_name");

    return isset($const_value) ? $const_value : null;
  }

  /**
  * sanitize and validate the options provided by input array
  **/
  public function validate_options($input = array()) {
    //var_dump($input);
    self::load_options();

    $validated_input = self::populate_option_defaults([]);

    $validated_input['booking_page_link_replacement'] = isset($input['booking_page_link_replacement']) ? true : false;

    return $validated_input;
  }

  /**
  * add the the link to settings page
  **/
  public function add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=commons-booking-map">' . __( 'Settings') . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
  }

  /**
  * render the settings page
  **/
  public function render_settings_page() {
    wp_enqueue_style('cb_map_admin_css', CB_MAP_ASSETS_URL . 'css/cb-map-admin.css');

    include_once( CB_MAP_PATH . 'templates/settings-page-template.php');
  }
}

?>
