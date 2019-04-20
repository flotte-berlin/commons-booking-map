<div class="wrap">

  <h1><?= cb_map\__('SETTINGS_PAGE_HEADER', 'commons-booking-map', 'Settings for Commons Booking Map') ?></h1>

  <p><?= cb_map\__('SETTINGS_DESCRIPTION', 'commons-booking-map', 'These settings help you to configure the Commons Booking Map.') ?></p>

  <form method="post" action="options.php">
    <?php
      settings_fields( 'cb-map-settings' );
      do_settings_sections( 'cb-map-settings' );
    ?>

    <h2><?= cb_map\__('ZOOM', 'commons-booking-map', 'Zoom') ?></h2>

    <table style="text-align: left;">
      <tr>
          <th><?= cb_map\__('MIN_ZOOM_LEVEL', 'commons-booking-map', 'min. zoom level') ?>:</th>
          <td><input type="number" min="1" max="19" name="cb_map_options[zoom_min]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_min') ); ?>" size="3"></td>
      </tr>
      <tr>
          <th><?= cb_map\__('MAX_ZOOM_LEVEL', 'commons-booking-map', 'max. zoom level') ?>:</th>
          <td><input type="number" min="1" max="19" name="cb_map_options[zoom_max]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_max') ); ?>" size="3"></td>
      </tr>
      <tr>
          <th><?= cb_map\__('START_ZOOM_LEVEL', 'commons-booking-map', 'start zoom level') ?>:</th>
          <td><input type="number" min="1" max="19" name="cb_map_options[zoom_start]" value="<?= esc_attr( CB_Map_Settings::get_option('zoom_start') ); ?>" size="3"></td>
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

    <h2><?= cb_map\__('CUSTOM_MARKER', 'commons-booking-map', 'Custom Marker') ?></h2>

    <table style="text-align: left;">
      <tr>
        <th><?= cb_map\__('IMAGE_FILE', 'commons-booking-map', 'image file') ?>:</th>
        <td>
          <input id="select_image_button" type="button" class="button" value="<?= cb_map\__('SELECT', 'commons-booking-map', 'select') ?>" />
          <input id="remove_image_button" type="button" class="button" value="<?= cb_map\__('REMOVE', 'commons-booking-map', 'remove') ?>" />
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div class="image-preview-wrapper">
              <img id="image-preview" src="<?= wp_get_attachment_url(CB_Map_Settings::get_option('custom_marker_media_id')); ?>" height="100">
          </div>
          <input type="hidden" name="cb_map_options[custom_marker_media_id]" id="custom_marker_media_id" value="<?= CB_Map_Settings::get_option('custom_marker_media_id') ?>">
        </td>
      </tr>
      <tr>
          <th><?= cb_map\__('ICON_SIZE', 'commons-booking-map', 'icon size') ?>:</th>
          <td>
            <input type="text" name="cb_map_options[marker_icon_width]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_width') ); ?>" size="3"> x
            <input type="text" name="cb_map_options[marker_icon_height]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_height') ); ?>" size="3">
            (<?= cb_map\__('ICON_SIZE_DESC', 'commons-booking-map', 'as shown on map') ?>)
          </td>

      </tr>
      <tr>
        <th><?= cb_map\__('ANCHOR_POINT', 'commons-booking-map', 'anchor point') ?>:</th>
        <td>
          <input type="text" name="cb_map_options[marker_icon_anchor_x]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_anchor_x') ); ?>" size="3"> x
          <input type="text" name="cb_map_options[marker_icon_anchor_y]" value="<?= esc_attr( CB_Map_Settings::get_option('marker_icon_anchor_y') ); ?>" size="3">
          (<?= cb_map\__('ANCHOR_POINT_DESC', 'commons-booking-map', 'seen from the left top corner of the image') ?>)
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
		});

</script>
