
var cb_map = {
  settings: null,
  translation: null,
  map: null,
  markers: null,

  init_filters: function($) {
    var that = this;

    if(this.settings.filter_cb_item_categories.length > 0) {
      var $filter_container = $('<div style="width:100%; height: 50px;"></div>');

      var $form = $('<form></form');
      this.settings.filter_cb_item_categories.forEach(function(category) {
        $input = $('<label style="margin-right: 20px;"><input type="checkbox" name="cb_item_categories[]" value="' + category.term_id + '">' + category.name + '</label>');
        $form.append($input);
      })

      var $button = $('<button>filter</button>');

      $button.click(function(event) {
        event.preventDefault();

        var filters = [];
        var data = $form.serializeArray();
        data.forEach(function(obj) {
          filters.push(obj.value);
        })

        that.get_location_data(filters);
      });

      $button_wrapper = $('<div></div>');
      $button_wrapper.append($button);
      $form.append($button_wrapper);

      $filter_container.append($form);

      $filter_container.insertAfter($('#cb-map'));
    }
  },

  init_map: function() {
    var osm_url='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  	var osm_attrib='Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';
    var map_options = {
      minZoom: this.settings.zoom_min,
      maxZoom: this.settings.zoom_max,
      attribution: osm_attrib
    }

  	// set up the map
  	var map = new L.Map('cb-map');

  	// create the tile layer with correct attribution
  	var osm = new L.TileLayer(osm_url, map_options);

  	map.setView(new L.LatLng(this.settings.lat_start, this.settings.lon_start), this.settings.zoom_start);
  	map.addLayer(osm);

    this.map = map;

    //get location data
    this.get_location_data();

  },

  get_location_data: function(filters) {
    filters = filters || [];

    var that = this;
    var data = {
			'action': 'cb_map_locations',
      'filters': filters,
      'cb_map_id': this.settings.cb_map_id
		};

    console.log('fetch location data from: ', this.settings.data_url);

    this.map.spin(true);

    jQuery.post(this.settings.data_url, data, function(response) {
      var location_data = JSON.parse(response);
      console.log('location data: ', location_data);

      that.render_locations(location_data, filters);

      that.map.spin(false);
		});

  },

  render_locations: function(data, filters) {
    var that = this;

    if(this.markers) {
      this.markers.clearLayers();
    }

    var markers;
    if(this.settings.max_cluster_radius > 0) {
      var marker_cluster_options = {
        showCoverageOnHover: false,
        maxClusterRadius: this.settings.max_cluster_radius
      };

      if(this.settings.marker_cluster_icon) {
        marker_cluster_options.iconCreateFunction = function (cluster) {
        		var child_count = cluster.getChildCount();

        		var c = ' marker-cluster-';
        		if (child_count < 10) {
        			c += 'small';
        		} else if (childCount < 100) {
        			c += 'medium';
        		} else {
        			c += 'large';
        		}

        		return new L.DivIcon({ html: '<div style="width: 100%; height: 100%; font-weight: bold; line-height: ' + that.settings.marker_cluster_icon.size.height + 'px; background-size: cover; background-image: url(' + that.settings.marker_cluster_icon.url + ')"><span>' + child_count + '</span></div>', className: 'marker-cluster', iconSize: new L.Point(that.settings.marker_cluster_icon.size.width, that.settings.marker_cluster_icon.size.height) });
        }
      }

      markers = L.markerClusterGroup(marker_cluster_options);

    }
    else {
      markers = L.layerGroup();
    }

    var custom_marker_icon;
    if(this.settings.marker_icon) {
      custom_marker_icon = L.icon(this.settings.marker_icon);
    }

    //iterate data and add markers
    jQuery.each(data, function(index, location) {
      console.log(location);

      var marker_options = {};

      //item names
      var item_names = [];
      popup_items = '';
      location.items.forEach(function(item) {
        item_names.push(item.name);

        var item_thumb_image = item.thumbnail ? '<img src="' + item.thumbnail + '">' : '';

        popup_items += '<div>'
          + '<div style="display: inline-block; width: 25%; margin-right: 5%;">'
          + item_thumb_image
          + '</div>'
          + '<div style="display: inline-block; width: 70%;"><b><a href="' + item.link + '">' + item.name + '</a></b> - '
          + item.short_desc
          + '</div>'
          + '</div>'
      })
      var marker_options = {
        title: item_names.toString()
      };

      //icon
      if(custom_marker_icon) {
        marker_options.icon = custom_marker_icon;
      }

      var marker = L.marker([location.lat, location.lon], marker_options);

      var popup_content = '<b>' + location.location_name + '</b><br>'
              + location.address.street + '<br>'
              + location.address.zip + ' ' + location.address.city
              + '<p><b>' + that.translation['OPENING_HOURS'] + ':</b><br>' + location.opening_hours + '</p>';

      if(that.settings.show_location_contact) {
        popup_content += '<p><b>' + that.translation['CONTACT'] + ':</b><br>' + location.contact + '</p>'
      }

      popup_content += popup_items;
      marker.bindPopup(popup_content);

      markers.addLayer(marker);

      that.markers = markers;
    });

    this.map.addLayer(markers);

    //adjust map section to marker bounds based on settings
    if((filters.length > 0 && this.settings.marker_map_bounds_filter) || (filters.length == 0 && this.settings.marker_map_bounds_initial)) {
      if(Object.keys(data).length > 0) {
        that.map.fitBounds(markers.getBounds());
      }
      else {
        this.map.setView(new L.LatLng(this.settings.lat_start, this.settings.lon_start), this.settings.zoom_start);
      }
    }

  }
}

jQuery(document).ready(function ($) {
  console.log('cb_map.settings: ', cb_map.settings);

  cb_map.init_filters($);
  cb_map.init_map();

});
