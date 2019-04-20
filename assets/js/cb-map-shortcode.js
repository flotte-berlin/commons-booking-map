
var cb_map = {
  settings: null,
  map: null,
  data_url: '/wp-admin/admin-ajax.php',

  init_map: function() {
    var map_options = {
      minZoom: this.settings.zoom_min,
      maxZoom: this.settings.zoom_max,
      attribution: osmAttrib
    }

  	// set up the map
  	map = new L.Map('cb-map');

  	// create the tile layer with correct attribution
  	var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  	var osmAttrib='Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors';
  	var osm = new L.TileLayer(osmUrl, map_options);

  	map.setView(new L.LatLng(this.settings.lat_start, this.settings.lon_start), this.settings.zoom_start);
  	map.addLayer(osm);

    this.map = map;

    //get location data
    this.get_location_data();

  },

  get_location_data: function() {
    var that = this;

    var data = {
			'action': 'cb_map_locations'
		};

    console.log('fetch location data from: ', this.data_url);

    jQuery.post(this.data_url, data, function(response) {
      var location_data = JSON.parse(response);
      console.log('location data: ', location_data);

      that.render_locations(location_data);
		});

  },

  render_locations: function(data) {
    var that = this;

    var markers = L.markerClusterGroup();

    console.log('settings: ', this.settings);

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
      popup_timeframes = '';
      location.timeframes.forEach(function(timeframe) {
        item_names.push(timeframe.name);

        popup_timeframes += '<div>'
          + '<div style="display: inline-block; width: 25%; margin-right: 5%;">'
          + '<img src="' + timeframe.thumbnail + '">'
          + '</div>'
          + '<div style="display: inline-block; width: 70%;"><b><a href="' + timeframe.link + '">' + timeframe.name + '</a></b> - '
          + timeframe.short_desc
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
              + '<p>' + location.opening_hours + '</p>'
              + '<p>' + location.contact + '</p>';

      popup_content += popup_timeframes;
      marker.bindPopup(popup_content);

      markers.addLayer(marker);
    });

    map.addLayer(markers);
  }
}

jQuery(document).ready(function ($) {
  console.log('cb_map.settings: ', cb_map.settings);

  cb_map.init_map();

});
