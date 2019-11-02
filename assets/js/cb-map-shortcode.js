
function CB_Map() {
  var cb_map = {};

  cb_map.settings = null;
  cb_map.translation = null;
  cb_map.map = null;
  cb_map.markers = null;
  cb_map.messagebox = null;

  cb_map.tile_servers = {
    1: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    2: 'https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png',
    3: 'https://tiles.wmflabs.org/hikebike/{z}/{x}/{y}.png',
    4: 'https://tiles.lokaler.de/osmbright-20171212/{z}/{x}/{y}/tile@1x.jpeg'
  }

  cb_map.init_availability_filter = function($, $filter_options) {
    if(this.settings.show_item_availability_filter) {

      var $container = $('<div><div class="cb-map-filter-group-label">' + cb_map.translation['AVAILABILITY'] + '</div></div>');
      var $wrapper = $('<div class="cb-map-filter-group"></div>');
      $container.append($wrapper);

      var $date_start_input = $('<input type="date" name="date_start" min="' + cb_map.settings.filter_availability.date_min + '" max="' + cb_map.settings.filter_availability.date_max + '">');
      var $date_end_input = $('<input type="date" name="date_end" min="' + cb_map.settings.filter_availability.date_min + '" max="' + cb_map.settings.filter_availability.date_max + '">');
      var $day_count_select = $('<select name="day_count"></select>')
      for(var d = 0; d <= cb_map.settings.filter_availability.day_count_max; d++) {
        var show_value = d == 0 ? '-' : d;
        $day_count_select.append('<option value="' + d + '">' + show_value + '</option>')
      }

      $wrapper.append('<label>' + cb_map.translation['FROM'] + '</label>'); //TODO: translate label texts
      $wrapper.append($date_start_input);
      $wrapper.append('<label>' + cb_map.translation['UNTIL'] + '</label>');
      $wrapper.append($date_end_input);
      $wrapper.append('<label>' + cb_map.translation['AT_LEAST'] + '</label>');
      $wrapper.append($day_count_select);
      $wrapper.append('<label>' + cb_map.translation['DAYS'] + '</label>');
    }

    $filter_options.append($container);
  },

  cb_map.init_category_filter = function($, $filter_options) {
    var $container = $('<div><div class="cb-map-filter-group-label">' + cb_map.translation['CATEGORIES'] + '</div></div>');
    var $wrapper = $('<div class="cb-map-filter-group"></div>');
    $container.append($wrapper);

    $.each(this.settings.filter_cb_item_categories, function(index, group) {
      var $fieldset = $('<fieldset></fieldset>');
      if(group.name.length > 0) {
        $fieldset.append('<legend>' + group.name + '</legend>');
      }

      $.each(group.elements, function(index, category) {
        var $input = $('<input type="checkbox" name="cb_item_categories[]" value="' + category.cat_id + '">')
        var $label = $('<label></label>');
        $label.html(category.markup);
        $fieldset.append($input);
        $fieldset.append($label);
      });

      $wrapper.append($fieldset);
    });

    $filter_options.append($container);
  },

  cb_map.init_filters = function($) {
    var that = this;

    var $filter_container = $('<div class="cb-map-filters"></div>');

    var show_item_availability_filter =  this.settings.show_item_availability_filter;
    var show_cb_item_categories_filter = Object.keys(this.settings.filter_cb_item_categories).length > 0

    if(show_item_availability_filter || show_cb_item_categories_filter) {
      var $form = $('<form></form');
      var $filter_options = $('<div class="cb-filter-options"></div>');

      if(show_item_availability_filter) {
        cb_map.init_availability_filter($, $filter_options);
      }

      if(show_cb_item_categories_filter) {
        cb_map.init_category_filter($, $filter_options);
      }

      $form.append($filter_options);

      var $button = $('<button>' + cb_map.translation['FILTER'] + '</button>');

      $button.click(function(event) {
        event.preventDefault();

        var filters = {
          cb_item_categories: [],
          availability: {}
        };
        var data = $form.serializeArray();
        data.forEach(function(obj) {
          if(obj.name.indexOf('cb_item_categories') > -1) {
            filters.cb_item_categories.push(obj.value);
          }
          else {
            console.log('obj.name: ', obj.name)
            filters[obj.name] = obj.value;
          }
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
    var tile_server_url = cb_map.tile_servers[this.settings.base_map];
  	var attribution = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors - <a href="https://www.openstreetmap.org/copyright">License</a>';
    var map_options = {
      minZoom: this.settings.zoom_min,
      maxZoom: this.settings.zoom_max,
      attribution: attribution
    }

  	// set up the map
  	var map = new L.Map('cb-map-' + this.settings.cb_map_id);

    //create messagebox
    this.messagebox = L.control.messagebox({ timeout: 5000 }).addTo(map);

    //create scale
    if(this.settings.show_scale) {
      L.control.scale({imperial: false, updateWhenIdle: true}).addTo(map);
    }

  	// create the tile layer with correct attribution
  	var osm = new L.TileLayer(tile_server_url, map_options);

  	map.setView(new L.LatLng(this.settings.lat_start, this.settings.lon_start), this.settings.zoom_start);
  	map.addLayer(osm);

    this.map = map;

    map.on('popupopen', function (e) {
      console.log(e.popup);

      jQuery(e.popup._container).find('.cb-map-popup-item-availability').overscroll({
        direction: 'horizontal'
      });
  });

    //get location data
    this.get_location_data(null, true);

  },

  cb_map.get_location_data = function(filters, init) {
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

      that.render_locations(location_data, filters, init);

      if(location_data.length == 0) {
        that.messagebox.show(cb_map.translation['NO_LOCATIONS_MESSAGE']);
      }

		}).always(function() {
      that.map.spin(false);
    });

  },

  cb_map.render_item_availability = function(availability) {
    var markup = '';

    var status_classes = {
        0: 'available',
        1: 'location-closed',
        2: 'booked',
        3: 'no-timeframe'
    }

    availability.forEach(function(day) {
      var timestamp = Date.parse(day.date);
      var date = new Date(timestamp);
      var show_date = date.getDate();
      show_date = show_date <= 9 ? '0' + show_date : show_date;
      var show_month = date.getMonth() + 1;
      var date_string = show_date + '.' + show_month + '.';
      markup += '<div class="cb-map-popup-item-availability-day ' + status_classes[day.status] + '">' + date_string + '</div>'
    });

    return markup;
  }

  cb_map.render_locations = function(data, filters, init) {
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

        		return new L.DivIcon({ html: '<div class="cb-map-marker-cluster-icon" style="line-height: ' + that.settings.marker_cluster_icon.size.height + 'px; background-image: url(' + that.settings.marker_cluster_icon.url + ')"><span>' + child_count + '</span></div>', className: 'marker-cluster', iconSize: new L.Point(that.settings.marker_cluster_icon.size.width, that.settings.marker_cluster_icon.size.height) });
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

        popup_items += '<div class="cb-map-popup-item">'
          + '<div class="cb-map-popup-item-thumbnail">'
          + item_thumb_image
          + '</div>';

        popup_items += '<div class="cb-map-popup-item-info">';
        popup_items += '<div class="cb-map-popup-item-link"><b><a href="' + item.link + '">' + item.name + '</a></b>';

        //popup_items += '<span class="dashicons dashicons-calendar-alt"></span>';

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

        popup_items += '</div>';

        if(cb_map.settings.show_item_availability && item.availability) {
          popup_items += '<div class="cb-map-popup-item-availability">' + cb_map.render_item_availability(item.availability) + '</div>';
        }

        popup_items += '<div class="cb-map-popup-item-desc">' + item.short_desc + '</div>';
        popup_items += '</div></div>';
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
      popup_content += '<b>' + location.location_name + '</b>';
      popup_content += '<span id="location-zoom-in-' + that.settings.cb_map_id + '-' + index + '" class="dashicons dashicons-search"></span>';
      popup_content += '</div>';
      popup_content += '<div  class="cb-map-location-info-address">' + location.address.street + ', ' + location.address.zip + ' ' + location.address.city + '</div>';

      if(that.settings.show_location_opening_hours && location.opening_hours) {
        popup_content += '<div class="cb-map-location-info-opening-hours"><b><i>' + cb_map.translation['OPENING_HOURS'] + ':</i></b><br>' + location.opening_hours + '</div>'
      }

      if(that.settings.show_location_contact && location.contact) {
        popup_content += '<div class="cb-map-location-info-contact"><b><i>' + cb_map.translation['CONTACT'] + ':</i></b><br>' + location.contact + '</div>'
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
    if((!init && this.settings.marker_map_bounds_filter) || (init && this.settings.marker_map_bounds_initial)) {
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
