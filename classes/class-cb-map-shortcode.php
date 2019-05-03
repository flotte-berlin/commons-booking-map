<?php

class CB_Map_Shortcode {

  /**
  * the shortcode handler
  **/
  public static function handle($atts) {
    /*
    $a = shortcode_atts( array(
  		'foo' => 'something',
  		'bar' => 'something else',
  	), $atts );
    */

    //leaflet
    wp_enqueue_style('cb_map_leaflet_css', CB_MAP_ASSETS_URL . 'leaflet/leaflet.css');
    wp_enqueue_script( 'cb_map_leaflet_js', CB_MAP_ASSETS_URL . 'leaflet/leaflet-src.js' ); //TODO: change to leaflet.js

    //leaflet markercluster plugin
    wp_enqueue_style('cb_map_leaflet_markercluster_css', CB_MAP_ASSETS_URL . 'leaflet-markercluster/MarkerCluster.css');
    wp_enqueue_style('cb_map_leaflet_markercluster_default_css', CB_MAP_ASSETS_URL . 'leaflet-markercluster/MarkerCluster.Default.css');
    wp_enqueue_script( 'cb_map_leaflet_markercluster_js', CB_MAP_ASSETS_URL . 'leaflet-markercluster/leaflet.markercluster.js' );

    //leaflet spin & dependencies
    wp_enqueue_style( 'cb_map_spin_css', CB_MAP_ASSETS_URL . 'spin-js/spin.css' );
    wp_enqueue_script( 'cb_map_spin_js', CB_MAP_ASSETS_URL . 'spin-js/spin.min.js' );
    wp_enqueue_script( 'cb_map_leaflet_spin_js', CB_MAP_ASSETS_URL . 'leaflet-spin/leaflet.spin.min.js' );

    //cb map shortcode js
    wp_register_script( 'cb_map_shortcode_js', CB_MAP_ASSETS_URL . 'js/cb-map-shortcode.js');
    wp_add_inline_script( 'cb_map_shortcode_js', 'cb_map.settings=' . json_encode(self::get_settings()) .';' );
    wp_add_inline_script( 'cb_map_shortcode_js', 'cb_map.translation=' . json_encode(self::get_translation()) .';' );
    wp_enqueue_script( 'cb_map_shortcode_js' );

    $map_height = CB_Map_Settings::get_option('map_height');
    return '<div id="cb-map" style="width: 100%; height: ' . $map_height . 'px;"></div>';
  }

  public static function get_settings() {
    $settings = [
      'data_url' => get_site_url(null, '', null) . '/wp-admin/admin-ajax.php',
      'marker_icon' => null,
      'filter_cb_item_categories' => []
    ];
    $options = CB_Map_Settings::get_options();

    $pass_through = ['zoom_min', 'zoom_max', 'zoom_start', 'lat_start', 'lon_start', 'max_cluster_radius', 'show_location_contact'];

    $icon_size = [$options['marker_icon_width'], $options['marker_icon_height']];
    $icon_anchor = [$options['marker_icon_anchor_x'], $options['marker_icon_anchor_y']];

    foreach ($options as $key => $value) {
      if(in_array($key, $pass_through)) {
        $settings[$key] = $value;
      }
      else if($key == 'custom_marker_media_id') {
        if($value != null) {
          $settings['marker_icon'] = [
            'iconUrl'       => wp_get_attachment_url($options['custom_marker_media_id']),
            //'shadowUrl'     => 'leaf-shadow.png',

            'iconSize'      => $icon_size, //[27, 35], // size of the icon
            //'shadowSize'    => [50, 64], // size of the shadow
            'iconAnchor'    => $icon_anchor, //[13.5, 0], // point of the icon which will correspond to marker's location
            //'shadowAnchor'  => [4, 62],  // the same for the shadow
            //'popupAnchor'   => [-3, -76] // point from which the popup should open relative to the iconAnchor
          ];
        }
      }
      else if ($key == 'cb_items_available_categories') {
        $terms = get_terms([
          'taxonomy' => 'cb_items_category',
          'hide_empty' => false
        ]);
        //var_dump($terms);
        foreach ($terms as $term) {
          if(in_array($term->term_id, $value)) {
            $settings['filter_cb_item_categories'][] = $term;
          }
        }
      }

    }

    return $settings;
  }

  public static function get_translation() {
    $translation = [
      'OPENING_HOURS' => cb_map\__('OPENING_HOURS', 'commons-booking-map', 'opening hours'),
      'CONTACT' => cb_map\__('CONTACT', 'commons-booking-map', 'contact')
    ];

    return $translation;
  }

  /**
  * the ajax request callback for locations
  **/
  public static function get_locations($data) {

    //pre-filter based on settings
    $apply_filters = CB_Map_Settings::get_option('cb_items_preset_categories');
    $available_filters = CB_Map_Settings::get_option('cb_items_available_categories');

    if(isset($_POST['filters'])) {
      foreach($_POST['filters'] as $filter) {
        if(in_array($filter, $available_filters)) {
          $apply_filters[] = $filter;
        }
      }
    }

    require_once( CB_MAP_PATH . 'classes/class-cb-map.php' );
    $locations = CB_Map::get_locations_by_timeframes($apply_filters);

    echo json_encode($locations);

    wp_die();
  }
}
?>
