
function CB_Map() {
  var cb_map = {};

  cb_map.settings = null;
  cb_map.translation = null;
  cb_map.map = null;
  cb_map.markers = null;

  cb_map.init_filters = function($) {
    var that = this;

    if(Object.keys(this.settings.filter_cb_item_categories).length > 0) {
      var $filter_container = $('<div class="cb-map-filters" style="width:100%; height: 50px;"></div>');

      var $form = $('<form></form');
      var $filter_options = $('<div class="cb-filter-options"></div>');
      $.each(this.settings.filter_cb_item_categories, function(index, category) {
        $input = $('<input type="checkbox" name="cb_item_categories[]" value="' + category.cat_id + '">')
        $label = $('<label style="margin-right: 20px;"></label>');
        $label.html(category.markup);
        $filter_options.append($input);
        $filter_options.append($label);
      });

      $form.append($filter_options);

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

      $button_wrapper = $('<div class="cb-map-button-wrapper"></div>');
      $button_wrapper.append($button);
      $form.append($button_wrapper);

      $filter_container.append($form);
      $filter_container.insertAfter($('#cb-map-' + this.settings.cb_map_id));
    }
  },

  cb_map.init_map = function() {
    var osm_url='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  	var osm_attrib='Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';
    var map_options = {
      minZoom: this.settings.zoom_min,
      maxZoom: this.settings.zoom_max,
      attribution: osm_attrib
    }

  	// set up the map
  	var map = new L.Map('cb-map-' + this.settings.cb_map_id);

  	// create the tile layer with correct attribution
  	var osm = new L.TileLayer(osm_url, map_options);

  	map.setView(new L.LatLng(this.settings.lat_start, this.settings.lon_start), this.settings.zoom_start);
  	map.addLayer(osm);

    this.map = map;

    //get location data
    this.get_location_data();

  },

  cb_map.get_location_data = function(filters) {
    filters = filters || [];

    var that = this;
    var data = {
      'nonce': this.settings.nonce,
			'action': 'cb_map_locations',
      'filters': filters,
      'cb_map_id': this.settings.cb_map_id
		};
    //console.log('fetch location data from: ', this.settings.data_url);

    this.map.spin(true);

    if(this.markers) {
      this.markers.clearLayers();
    }

    jQuery.post(this.settings.data_url, data, function(response) {
      var location_data = JSON.parse(response);
      console.log('location data: ', location_data);

      that.render_locations(location_data, filters);

		}).always(function() {
      that.map.spin(false);
    });

  },

  cb_map.render_locations = function(data, filters) {
    var that = this;

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
        		} else if (child_count < 100) {
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

    var date_format_options = { year: 'numeric', month: '2-digit', day: '2-digit' };

    //iterate data and add markers
    jQuery.each(data, function(index, location) {
      //console.log(location);

      var marker_options = {};

      //item names
      var item_names = [];
      popup_items = '';
      location.items.forEach(function(item) {
        item_names.push(item.name);

        var item_thumb_image = item.thumbnail ? '<img src="' + item.thumbnail + '">' : '';

        popup_items += '<div style="margin-top: 10px;">'
          + '<div style="display: inline-block; width: 25%; margin-right: 5%;">'
          + item_thumb_image
          + '</div>'
          + '<div style="display: inline-block; width: 70%;"><b><a href="' + item.link + '">' + item.name + '</a></b>';

        if(item.timeframe_hints && item.timeframe_hints.length > 0) {
          popup_items += ' (';

          for(var t = 0; t < item.timeframe_hints.length; t++) {
            if(t > 0) {
              popup_items += ', '
            }

            var timeframe_hint = item.timeframe_hints[t];

            var date = new Date(timeframe_hint.timestamp * 1000);
            var formatted_date_string = date.toLocaleDateString(cb_map.settings.locale, date_format_options);
            popup_items += cb_map.translation[timeframe_hint.type.toUpperCase()] + ' ' + formatted_date_string;
          }

          popup_items += ') ';
        }

        popup_items += ' - ' + item.short_desc
          + '</div>'
          + '</div>'
      });

      var marker_options = {
        title: item_names.toString()
      };

      //icon
      if(custom_marker_icon) {
        marker_options.icon = custom_marker_icon;
      }

      var marker = L.marker([location.lat, location.lon], marker_options);

      var popup_content = '<div class="cb-map-location-info-name">';
      popup_content += '<b style="line-height: 25px;">' + location.location_name + '</b>';
      popup_content += '<span id="location-zoom-in-' + that.settings.cb_map_id + '-' + index + '" style="cursor: pointer; padding-left: 5px; padding-top: 2.5px;" class="dashicons dashicons-search"></span>';
      popup_content += '</div>';
      popup_content += '<div  class="cb-map-location-info-address">' + location.address.street + ', ' + location.address.zip + ' ' + location.address.city + '</div>';

      if(that.settings.show_location_opening_hours && location.opening_hours) {
        popup_content += '<div class="cb-map-location-info-opening-hours" style="margin-top: 10px;"><b><i>' + cb_map.translation['OPENING_HOURS'] + ':</i></b><br>' + location.opening_hours + '</div>'
      }

      if(that.settings.show_location_contact && location.contact) {
        popup_content += '<div class="cb-map-location-info-contact" style="margin-top: 10px;"><b><i>' + cb_map.translation['CONTACT'] + ':</i></b><br>' + location.contact + '</div>'
      }

      popup_content += popup_items;

      var popup = L.DomUtil.create('div', 'cb-map-location-info');
      popup.innerHTML = popup_content;
      marker.bindPopup(popup);

      markers.addLayer(marker);

      //set map view to location and zoom in
      jQuery('#location-zoom-in-' + that.settings.cb_map_id + '-' + index, popup).on('click', function() {
        that.map.closePopup();
        that.map.setView(new L.LatLng(location.lat, location.lon), that.settings.zoom_max);
      });

    });

    this.map.addLayer(markers);

    that.markers = markers;

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

  return cb_map;
}
