<style>

.cb-map-settings-cat-filter-list .children {
  margin-left: 1.5em;
}

th {
  width: 220px;
}

.category-wrapper {
  height: 150px;
  padding: 10px;
  background-color: #fff;
  overflow-y: scroll;
}

</style>

<div class="wrap">

  <h1><?= cb_map\__('SETTINGS_PAGE_HEADER', 'commons-booking-map', 'Settings for Commons Booking Map') ?></h1>

  <p><?= cb_map\__('SETTINGS_DESCRIPTION', 'commons-booking-map', 'These settings help you to configure the Commons Booking Map.') ?></p>

  <form method="post" action="options.php">
    <?php
      settings_fields( 'cb-map-settings' );
      do_settings_sections( 'cb-map-settings' );
    ?>

    <h2><?= cb_map\__('MAP_PRESENTATION', 'commons-booking-map', 'Map Presentation') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th>
            <?= cb_map\__('MAP_HEIGHT', 'commons-booking-map', 'map height') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAP_HEIGHT_DESC', 'commons-booking-map', 'the height the map is rendered with - the width is the same as of the parent element') ?>"></span>
          </th>
          <td><input type="number" min="<?= CB_Map_Settings::MAP_HEIGHT_VALUE_MIN ?>" max="<?= CB_Map_Settings::MAP_HEIGHT_VALUE_MAX ?>" name="cb_map_options[map_height]" value="<?= esc_attr( CB_Map_Settings::get_option('map_height') ); ?>" size="4"> px</td>
      </tr>
    </table>

    <h2><?= cb_map\__('ZOOM', 'commons-booking-map', 'Zoom') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th>
            <?= cb_map\__('MIN_ZOOM_LEVEL', 'commons-booking-map', 'min. zoom level') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MIN_ZOOM_LEVEL_DESC', 'commons-booking-map', 'the minimal zoom level a user can choose') ?>"></span>
          </th>
          <td><input type="number" min="<?= CB_Map_Settings::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Settings::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_min]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_min') ); ?>" size="3"></td>
      </tr>
      <tr>
          <th>
            <?= cb_map\__('MAX_ZOOM_LEVEL', 'commons-booking-map', 'max. zoom level') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAX_ZOOM_LEVEL_DESC', 'commons-booking-map', 'the maximal zoom level a user can choose') ?>"></span>
          </th>
          <td><input type="number" min="<?= CB_Map_Settings::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Settings::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_max]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_max') ); ?>" size="3"></td>
      </tr>
      <tr>
          <th>
            <?= cb_map\__('START_ZOOM_LEVEL', 'commons-booking-map', 'start zoom level') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'START_ZOOM_LEVEL_DESC', 'commons-booking-map', 'the zoom level that will be set when the map is loaded') ?>"></span>
          </th>
          <td><input type="number" min="<?= CB_Map_Settings::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Settings::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_start]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_start') ); ?>" size="3"></td>
      </tr>
    </table>

    <h2><?= cb_map\__('POSITIONING_START', 'commons-booking-map', 'Map Positioning (center) at Intialization') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th>
            <?= cb_map\__('LATITUDE_START', 'commons-booking-map', 'start latitude') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'LATITUDE_START_DESC', 'commons-booking-map', 'the latitude of the map center when the map is loaded') ?>"></span>
          </th>
          <td><input type="text" name="cb_map_options[lat_start]" value="<?= esc_attr( CB_Map_Settings::get_option('lat_start') ); ?>" size="10"></td>
      </tr>

      <tr>
          <th>
            <?= cb_map\__('LONGITUDE_START', 'commons-booking-map', 'start longitude') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'LONGITUDE_START_DESC', 'commons-booking-map', 'the longitude of the map center when the map is loaded') ?>"></span>
          </th>
          <td><input type="text" name="cb_map_options[lon_start]" value="<?= esc_attr( CB_Map_Settings::get_option('lon_start') ); ?>" size="10"></td>
      </tr>
    </table>

    <h2><?= cb_map\__('ADAPTIVE_MAP_SECTION', 'commons-booking-map', 'Adaptive Map Section') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th>
            <?= cb_map\__('ADJUST_MAP_SECTION_TO_MARKERS_INITIALLY', 'commons-booking-map', 'initial adjustment to marker bounds') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ADJUST_MAP_SECTION_TO_MARKERS_INITIALLY_DESC', 'commons-booking-map', 'adjust map section to bounds of shown markers automatically when map is loaded') ?>"></span>
          </th>
          <td>
            <input type="checkbox" name="cb_map_options[marker_map_bounds_initial]" <?= CB_Map_Settings::get_option('marker_map_bounds_initial') ? 'checked="checked"' : '' ?> value="on">
          </td>
      </tr>
      <tr>
          <th>
            <?= cb_map\__('ADJUST_MAP_SECTION_TO_MARKERS_FILTER', 'commons-booking-map', 'adjustment to marker bounds on filter') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ADJUST_MAP_SECTION_TO_MARKERS_FILTER_DESC', 'commons-booking-map', 'adjust map section to bounds of shown markers automatically when filtered by users') ?>"></span>
          </th>
          <td>
            <input type="checkbox" name="cb_map_options[marker_map_bounds_filter]" <?= CB_Map_Settings::get_option('marker_map_bounds_filter') ? 'checked="checked"' : '' ?> value="on">
          </td>
      </tr>
    </table>

    <h2><?= cb_map\__('CLUSTER', 'commons-booking-map', 'Cluster') ?></h2>
    <table style="text-align: left;">
      <tr>
          <th>
            <?= cb_map\__('MAX_CLUSTER_RADIUS', 'commons-booking-map', 'max. cluster radius') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAX_CLUSTER_RADIUS_DESC', 'commons-booking-map', 'combine markers to a cluster within given radius - 0 for deactivation') ?>"></span>
          </th>
          <td>
            <input type="number" size="3" step="10" min="<?= CB_Map_Settings::MAX_CLUSTER_RADIUS_VALUE_MIN ?>" max="<?= CB_Map_Settings::MAX_CLUSTER_RADIUS_VALUE_MAX ?>" name="cb_map_options[max_cluster_radius]" value="<?= esc_attr( CB_Map_Settings::get_option('max_cluster_radius') ); ?>"> px
          </td>
      </tr>
    </table>

    <h2><?= cb_map\__('CUSTOM_MARKER', 'commons-booking-map', 'Custom Marker') ?></h2>

    <table style="text-align: left;">
      <tr>
        <th>
          <?= cb_map\__('IMAGE_FILE', 'commons-booking-map', 'image file') ?>:
          <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'IMAGE_FILE_DESC', 'commons-booking-map', 'the default marker icon can be replaced by a custom image') ?>"></span>
        </th>
        <td>
          <input id="select_image_button" type="button" class="button" value="<?= cb_map\__('SELECT', 'commons-booking-map', 'select') ?>" />
          <input id="remove_image_button" type="button" class="button" value="<?= cb_map\__('REMOVE', 'commons-booking-map', 'remove') ?>" />
        </td>
      </tr>
      <tr id="image-preview-settings" style="display: none;">
        <td>
          <div>
              <img id="image-preview" src="<?= wp_get_attachment_url(CB_Map_Settings::get_option('custom_marker_media_id')); ?>">
          </div>
          <input type="hidden" name="cb_map_options[custom_marker_media_id]" id="custom_marker_media_id" value="<?= CB_Map_Settings::get_option('custom_marker_media_id') ?>">
        </td>
        <td>
          <div id="image-preview-measurements"></div>
        </td>
      </tr>
      <tr id="marker-icon-size" style="display: none;">
          <th>
            <?= cb_map\__('ICON_SIZE', 'commons-booking-map', 'icon size') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ICON_SIZE_DESC', 'commons-booking-map', 'the size of the custom marker icon image as it is shown on the map') ?>"></span>
          </th>
          <td>
            <input type="text" name="cb_map_options[marker_icon_width]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_width') ); ?>" size="3"> x
            <input type="text" name="cb_map_options[marker_icon_height]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_height') ); ?>" size="3"> px
          </td>

      </tr>
      <tr id="marker-icon-anchor" style="display: none;">
        <th>
          <?= cb_map\__('ANCHOR_POINT', 'commons-booking-map', 'anchor point') ?>:
          <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ANCHOR_POINT_DESC', 'commons-booking-map', 'the position of the anchor point of the icon image, seen from the left top corner of the icon, often it is half of the width and full height of the icon size - this point is used to place the marker on the geo coordinates') ?>"></span>
        </th>
        <td>
          <input type="text" name="cb_map_options[marker_icon_anchor_x]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_anchor_x') ); ?>" size="3"> x
          <input type="text" name="cb_map_options[marker_icon_anchor_y]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_anchor_y') ); ?>" size="3"> px
        </td>
      </tr>
    </table>

    <h2><?= cb_map\__('POPUP', 'commons-booking-map', 'Marker Popup') ?></h2>
    <table style="text-align: left;">
      <tr>
          <th>
            <?= cb_map\__('SHOW_LOCATION_CONTACT', 'commons-booking-map', 'show location contact') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'SHOW_LOCATION_CONTACT_DESC', 'commons-booking-map', 'activate to show  the location contact details in the marker popup') ?>"></span>
          </th>
          <td><input type="checkbox" name="cb_map_options[show_location_contact]" <?= CB_Map_Settings::get_option('show_location_contact') ? 'checked="checked"' : '' ?> value="on"></td>
      </tr>
    </table>

    <h2><?= cb_map\__('FILTER_CONFIGURATION', 'commons-booking-map', 'Filter Configuration') ?></h2>
    <table style="text-align: left;">
      <tr>
        <th>
          <?= cb_map\__('AVAILABLE_CATEGORIES', 'commons-booking-map', 'available categories')?>:
          <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'AVAILABLE_CATEGORIES_DESC', 'commons-booking-map', 'select the categories that arre presented the users to filter items - none for no filters') ?>"></span>
        </th>
        <td>
          <ul class="cb-map-settings-cat-filter-list">
            <div class="category-wrapper">
              <?= $available_categories_checklist_markup ?>
            </div>
          </ul>
        </td>
      </tr>
    </table>

    <table style="text-align: left;">
      <tr>
        <th>
          <?= cb_map\__('PRESET_CATEGORIES', 'commons-booking-map', 'preset categories')?>:
          <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'PRESET_CATEGORIES_DESC', 'commons-booking-map', 'select the categories that are used to prefilter the items that are shown on the map - none for all items') ?>"></span>
        </th>
        <td>
          <ul class="cb-map-settings-cat-filter-list">
            <div class="category-wrapper">
              <?= $preset_categories_checklist_markup ?>
            </div>
          </ul>
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>

</div>

<script>

//based on: https://jeroensormani.com/how-to-include-the-wordpress-media-selector-in-your-plugin/
jQuery( document ).ready( function( $ ) {

			// uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = $('#custom_marker_media_id').val();

      $('#remove_image_button').on('click', function(event) {
        event.preventDefault();

        $( '#custom_marker_media_id' ).val( '' );
        $( '#image-preview' ).attr( 'src', '' );

        $('input[name="cb_map_options[marker_icon_width]"').val(0);
        $('input[name="cb_map_options[marker_icon_height]"').val(0);
        $('input[name="cb_map_options[marker_icon_anchor_x]"').val(0);
        $('input[name="cb_map_options[marker_icon_anchor_y]"').val(0);

        $('#image-preview-settings').hide();
        $('#image-preview-measurements').text('');
        $('#marker-icon-size').hide();
        $('#marker-icon-anchor').hide();

      });

			$('#select_image_button').on('click', function(event) {

				event.preventDefault();
				// if the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;

				} else {
					// set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}

				// create the media frame
				file_frame = wp.media.frames.file_frame = wp.media({
					title: '<?= cb_map\__('SELECT_IMAGE', 'commons-booking-map', 'Select an image') ?>',
					button: {
						text: '<?= cb_map\__('SAVE', 'commons-booking-map', 'save') ?>',
					},
					multiple: false
				});

				// image select callback
				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#custom_marker_media_id' ).val( attachment.id );
					// restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});

				// finally, open the modal
				file_frame.open();
			});

			// restore the main ID when the add media button is pressed
			$( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});

      $('#image-preview').load(function() {
        $('#image-preview-settings').show();
        $('#image-preview-measurements').text('<?= cb_map\__('MARKER_IMAGE_MEASUREMENTS', 'commons-booking-map', 'measurements') ?>: ' + $('#image-preview').width() + ' x ' + $('#image-preview').height() + ' px');
        $('#marker-icon-size').show();
        $('#marker-icon-anchor').show();

        if($('input[name="cb_map_options[marker_icon_width]"').val() == 0) {
          $('input[name="cb_map_options[marker_icon_width]"').val($('#image-preview').width());
        }

        if($('input[name="cb_map_options[marker_icon_height]"').val() == 0) {
          $('input[name="cb_map_options[marker_icon_height]"').val($('#image-preview').height());
        }

        if($('input[name="cb_map_options[marker_icon_anchor_x]"').val() == 0) {
          $('input[name="cb_map_options[marker_icon_anchor_x]"').val($('#image-preview').width() / 2);
        }

        if($('input[name="cb_map_options[marker_icon_anchor_y]"').val() == 0) {
          $('input[name="cb_map_options[marker_icon_anchor_y]"').val($('#image-preview').height());
        }

      });
		});

</script>
