<style>

.cb-map-settings-cat-filter-list .children {
  margin-left: 1.5em;
}

th {
  width: 200px;
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
          <th><?= cb_map\__('MAP_HEIGHT', 'commons-booking-map', 'map height') ?>:</th>
          <td><input type="number" min="<?= CB_Map_Settings::MAP_HEIGHT_VALUE_MIN ?>" max="<?= CB_Map_Settings::MAP_HEIGHT_VALUE_MAX ?>" name="cb_map_options[map_height]" value="<?= esc_attr( CB_Map_Settings::get_option('map_height') ); ?>" size="4"> px</td>
      </tr>
    </table>

    <h2><?= cb_map\__('ZOOM', 'commons-booking-map', 'Zoom') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th><?= cb_map\__('MIN_ZOOM_LEVEL', 'commons-booking-map', 'min. zoom level') ?>:</th>
          <td><input type="number" min="<?= CB_Map_Settings::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Settings::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_min]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_min') ); ?>" size="3"></td>
      </tr>
      <tr>
          <th><?= cb_map\__('MAX_ZOOM_LEVEL', 'commons-booking-map', 'max. zoom level') ?>:</th>
          <td><input type="number" min="<?= CB_Map_Settings::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Settings::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_max]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_max') ); ?>" size="3"></td>
      </tr>
      <tr>
          <th><?= cb_map\__('START_ZOOM_LEVEL', 'commons-booking-map', 'start zoom level') ?>:</th>
          <td><input type="number" min="<?= CB_Map_Settings::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Settings::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_start]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_start') ); ?>" size="3"></td>
      </tr>
    </table>

    <h2><?= cb_map\__('POSITIONING_START', 'commons-booking-map', 'Map Positioning (center) at Intialization') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th><?= cb_map\__('LATITUDE_START', 'commons-booking-map', 'start latitude') ?>:</th>
          <td><input type="text" name="cb_map_options[lat_start]" value="<?= esc_attr( CB_Map_Settings::get_option('lat_start') ); ?>" size="10"></td>
      </tr>

      <tr>
          <th><?= cb_map\__('LONGITUDE_START', 'commons-booking-map', 'start longitude') ?>:</th>
          <td><input type="text" name="cb_map_options[lon_start]" value="<?= esc_attr( CB_Map_Settings::get_option('lon_start') ); ?>" size="10"></td>
      </tr>
    </table>

    <h2><?= cb_map\__('CLUSTER', 'commons-booking-map', 'Cluster') ?></h2>
    <table style="text-align: left;">
      <tr>
          <th><?= cb_map\__('MAX_CLUSTER_RADIUS', 'commons-booking-map', 'max. cluster radius') ?>:</th>
          <td>
            <input type="number" size="3" step="10" min="<?= CB_Map_Settings::MAX_CLUSTER_RADIUS_VALUE_MIN ?>" max="<?= CB_Map_Settings::MAX_CLUSTER_RADIUS_VALUE_MAX ?>" name="cb_map_options[max_cluster_radius]" value="<?= esc_attr( CB_Map_Settings::get_option('max_cluster_radius') ); ?>"> px (<?= cb_map\__('MAX_CLUSTER_RADIUS_DESC', 'commons-booking-map', '0 for deactivation') ?>)
          </td>
      </tr>
    </table>

    <h2><?= cb_map\__('CUSTOM_MARKER', 'commons-booking-map', 'Custom Marker') ?></h2>

    <table style="text-align: left;">
      <tr>
        <th><?= cb_map\__('IMAGE_FILE', 'commons-booking-map', 'image file') ?>:</th>
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
          <th><?= cb_map\__('ICON_SIZE', 'commons-booking-map', 'icon size') ?>:</th>
          <td>
            <input type="text" name="cb_map_options[marker_icon_width]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_width') ); ?>" size="3"> x
            <input type="text" name="cb_map_options[marker_icon_height]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_height') ); ?>" size="3">
            (<?= cb_map\__('ICON_SIZE_DESC', 'commons-booking-map', 'as shown on map') ?>)
          </td>

      </tr>
      <tr id="marker-icon-anchor" style="display: none;">
        <th><?= cb_map\__('ANCHOR_POINT', 'commons-booking-map', 'anchor point') ?>:</th>
        <td>
          <input type="text" name="cb_map_options[marker_icon_anchor_x]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_anchor_x') ); ?>" size="3"> x
          <input type="text" name="cb_map_options[marker_icon_anchor_y]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_anchor_y') ); ?>" size="3">
          (<?= cb_map\__('ANCHOR_POINT_DESC', 'commons-booking-map', 'seen from the left top corner of the icon') ?>)
        </td>
      </tr>
    </table>

    <h2><?= cb_map\__('POPUP', 'commons-booking-map', 'Marker Popup') ?></h2>
    <table style="text-align: left;">
      <tr>
          <th><?= cb_map\__('SHOW_LOCATION_CONTACT', 'commons-booking-map', 'show location contact') ?>:</th>
          <td><input type="checkbox" name="cb_map_options[show_location_contact]" <?= CB_Map_Settings::get_option('show_location_contact') ? 'checked="checked"' : '' ?>" value="on"></td>
      </tr>
    </table>

    <h2><?= cb_map\__('FILTER_CONFIGURATION', 'commons-booking-map', 'Filter Configuration') ?></h2>
    <table style="text-align: left;">
      <tr>
        <th><?= cb_map\__('AVAILABLE_CATEGORIES', 'commons-booking-map', 'available categories')?>:</th>
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
        <th><?= cb_map\__('PRESET_CATEGORIES', 'commons-booking-map', 'preset categories')?>:</th>
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
