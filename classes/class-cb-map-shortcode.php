<?php

class CB_Map_Shortcode {

  /**
  * the shortcode handler
  **/
  public static function execute($atts) {

    $a = shortcode_atts( array(
  		'id' => 0
  	), $atts );

    if((int) $a['id']) {
      $post = get_post($a['id']);

      if($post && $post->post_type == 'cb_map') {
        $cb_map_id = $post->ID;

        $map_type = CB_Map_Admin::get_option($cb_map_id, 'map_type');

        if($post->post_status == 'publish') {
          if($map_type == 1 || $map_type == 2) {
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

            wp_add_inline_script( 'cb_map_shortcode_js',
              "jQuery(document).ready(function ($) {
                var cb_map = new CB_Map();
                cb_map.settings = " . json_encode(self::get_settings($cb_map_id)) . ";
                cb_map.translation = " . json_encode(self::get_translation($cb_map_id)) . ";
                console.log('cb_map.settings: ', cb_map.settings);
                cb_map.init_filters($);
                cb_map.init_map();
            });");

            wp_enqueue_script( 'cb_map_shortcode_js' );

            $map_height = CB_Map_Admin::get_option($cb_map_id, 'map_height');
            return '<div id="cb-map-' . $cb_map_id . '" style="width: 100%; height: ' . $map_height . 'px;"></div>';
          }
          else {
            return '<div>' . cb_map\__( 'NO_VALID_MAP_TYPE', 'commons-booking-map', 'no valid map type') . '</div>';
          }
        }
        else {
          return '<div>' . cb_map\__( 'NO_VALID_POST_STATUS', 'commons-booking-map', 'map is not published') . '</div>';
        }
      }
      else {
        return '<div>' . cb_map\__( 'NO_VALID_MAP_ID', 'commons-booking-map', 'no valid map id provided') . '</div>';
      }

    }
    else {
      return '<div>' . cb_map\__( 'NO_VALID_MAP_ID', 'commons-booking-map', 'no valid map id provided') . '</div>';
    }

  }

  public static function get_settings($cb_map_id) {
    $settings = [
      'data_url' => get_site_url(null, '', null) . '/wp-admin/admin-ajax.php',
      'marker_icon' => null,
      'filter_cb_item_categories' => [],
      'cb_map_id' => $cb_map_id,
      'locale' => str_replace('_', '-', get_locale())
    ];
    $options = CB_Map_Admin::get_options($cb_map_id, true);

    $pass_through = ['zoom_min', 'zoom_max', 'zoom_start', 'lat_start', 'lon_start', 'marker_map_bounds_initial', 'marker_map_bounds_filter', 'max_cluster_radius', 'show_location_contact', 'show_location_opening_hours'];

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
      else if($key == 'custom_marker_cluster_media_id') {
        if($value != null) {
          $settings['marker_cluster_icon'] = [
            'url'       => wp_get_attachment_url($options['custom_marker_cluster_media_id']),
            'size'      => [
              'width' => $options['marker_cluster_icon_width'],
              'height' => $options['marker_cluster_icon_height']
            ]
          ];
        }
      }
      //categories are only meant to be shown on local maps
      else if($key == 'cb_items_available_categories' && $options['map_type'] == 1 ) {
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
      //categories are only meant to be shown on local maps
      else if($key == 'cb_items_available_categories_custom_markup' && $options['map_type'] == 1) {
        $settings['filter_cb_item_categories_custom_markup'] = $options['cb_items_available_categories_custom_markup'];
      }

    }

    return $settings;
  }

  public static function get_translation($cb_map_id) {
    $label_location_opening_hours = CB_Map_Admin::get_option($cb_map_id, 'label_location_opening_hours');
    $label_location_contact = CB_Map_Admin::get_option($cb_map_id, 'label_location_contact');

    $translation = [
      'OPENING_HOURS' => strlen($label_location_opening_hours) > 0 ? $label_location_opening_hours : cb_map\__('OPENING_HOURS', 'commons-booking-map', 'opening hours'),
      'CONTACT' => strlen($label_location_contact) > 0 ? $label_location_contact : cb_map\__('CONTACT', 'commons-booking-map', 'contact'),
      'FROM' => cb_map\__( 'FROM', 'commons-booking-map', 'from'),
      'UNTIL' => cb_map\__( 'UNTIL', 'commons-booking-map', 'until')
    ];

    return $translation;
  }

  /**
  * the ajax request handler for locations
  **/
  public static function get_locations() {

    if(isset($_POST['code'])) {

      //find map with corresponding code
      $args = [
        'post_type' => 'cb_map'
      ];
      $cb_maps = get_posts($args);

      foreach ($cb_maps as $cb_map) {
        $options = get_post_meta( $cb_map->ID, 'cb_map_options', true );

        //var_dump($options);

        if($options['map_type'] == 3 && $options['export_code'] == $_POST['code']) {
          $cb_map_id = $cb_map->ID;
          $map_type = 3;
          $post = $cb_map;
          break;
        }
      }

      if(!isset($cb_map_id)) {
        wp_send_json_error([ 'error' => 1 ], 404);
        return wp_die();
      }
    }
    else if(isset($_POST['cb_map_id'])) {
      $post = get_post((int) $_POST['cb_map_id']);

      if($post && $post->post_type == 'cb_map') {
        $cb_map_id = $post->ID;

        //prepare response payload
        $map_type = CB_Map_Admin::get_option($cb_map_id, 'map_type');
      }
      else {
        wp_send_json_error( [ 'error' => 2 ], 400);
        return wp_die();
      }
    }
    else {
      wp_send_json_error( [ 'error' => 3 ], 400);
      return wp_die();
    }

    $apply_filters = CB_Map_Admin::get_option($cb_map_id, 'cb_items_preset_categories');

    if($post->post_status == 'publish') {
      //local
      if($map_type == 1) {
        $available_filters = CB_Map_Admin::get_option($cb_map_id, 'cb_items_available_categories');

        if(isset($_POST['filters']) && is_array($_POST['filters'])) {
          foreach($_POST['filters'] as $filter) {
            if(in_array($filter, $available_filters)) {
              $apply_filters[] = $filter;
            }
          }
        }

        require_once( CB_MAP_PATH . 'classes/class-cb-map.php' );
        $locations = array_values(CB_Map::get_locations_by_timeframes($cb_map_id, $apply_filters));
        $locations = CB_Map::cleanup_location_data($locations, '<br>', $map_type);
      }

      //import
      if($map_type == 2) {
        $map_imports = get_post_meta( $cb_map_id, 'cb_map_imports', true );

        $locations = [];

        if(is_array($map_imports)) {
          foreach ($map_imports as $import_locations_string) {
            $import_locations = json_decode(base64_decode($import_locations_string), true);
            if(is_array($import_locations)) {
              $locations = array_merge($locations, $import_locations);
            }
          }
        }

      }

      //export
      if($map_type == 3) {
        $available_filters = CB_Map_Admin::get_option($cb_map_id, 'cb_items_available_categories');
        require_once( CB_MAP_PATH . 'classes/class-cb-map.php' );
        $locations = array_values(CB_Map::get_locations_by_timeframes($cb_map_id, $apply_filters));
        $locations = CB_Map::cleanup_location_data($locations, '<br>', $map_type);
      }

      echo json_encode($locations, JSON_UNESCAPED_UNICODE);

      return wp_die();

    }
    else {
      wp_send_json_error( [ 'error' => 4 ], 403);
      return wp_die();
    }
  }
}
?>
