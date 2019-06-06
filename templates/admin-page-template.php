<style>

.cb-map-settings-cat-filter-list .children {
  margin-left: 1.5em;
}

th {
  width: 250px;
}

.category-wrapper {
  height: 150px;
  padding: 10px;
  background-color: #fff;
  overflow-y: scroll;
}

.option-group {
  display: none;
}

button>span.dashicons {
  display: inline-block;
  margin-top: 4px;
}

@keyframes spin { 100% { -webkit-transform: rotateZ(360deg); transform:rotateZ(360deg); } }

.rotate {
  animation:  spin 2s linear infinite;
}

</style>

<div class="inside">

    <p><?= cb_map\__('SETTINGS_DESCRIPTION', 'commons-booking-map', 'These settings help you to configure the Commons Booking Map.') ?></p>

    <div class="option-group" id="option-group-usage">
      <h1><?= cb_map\__('USAGE', 'commons-booking-map', 'Usage') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('MAP_TYPE', 'commons-booking-map', 'Map Type') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAP_TYPE_DESC', 'commons-booking-map', 'the type of the map defines the usage - if the map is shown on the own website (local), collect data from external sources (import) or provide data for other websites (export)') ?>"></span>
            </th>
            <td>
              <? $selected_option = CB_Map_Admin::get_option($cb_map_id, 'map_type') ?>
              <select id="map_type" name="cb_map_options[map_type]">
                <option value="1" <?= $selected_option == 1 ? 'selected' : '' ?>><?= cb_map\__('MAP_TYPE_LOCAL', 'commons-booking-map', 'local') ?></option>
                <option value="2" <?= $selected_option == 2 ? 'selected' : '' ?>><?= cb_map\__('MAP_TYPE_IMPORT', 'commons-booking-map', 'import') ?></option>
                <option value="3" <?= $selected_option == 3 ? 'selected' : '' ?>><?= cb_map\__('MAP_TYPE_EXPORT', 'commons-booking-map', 'export') ?></option>
              </select>
            </td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-data-export">

      <h1><?= cb_map\__('DATA_EXPORT', 'commons-booking-map', 'Data Export') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('EXPORT_CODE', 'commons-booking-map', 'export code') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'EXPORT_CODE_DESC', 'commons-booking-map', 'generate an export code, that you can give to someone running a website with Commons Booking Map plugin to import and show your locations and items') ?>"></span>
            </th>
            <td>
              <input type="text" autocomplete="off" size="10" minlength="<?= CB_Map_Admin::EXPORT_CODE_VALUE_MIN_LENGTH ?>" name="cb_map_options[export_code]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'export_code') ); ?>">
              <input id="create-export-code-button" type="button" class="button" value="<?= cb_map\__('CREATE', 'commons-booking-map', 'create') ?>" />
            </td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('EXPORT_BASE_URL', 'commons-booking-map', 'export url') ?>:
              </th>
            <td><?= $data_export_base_url ?></td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-data-import">
      <h1><?= cb_map\__('DATA_IMPORT', 'commons-booking-map', 'Data Import') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('ADD_IMPORT_SOURCE', 'commons-booking-map', 'new import source') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ADD_IMPORT_SOURCE_DESC', 'commons-booking-map', 'add an import source (another website with installed Commons Booking Map plugin and prepared map export) by typing the url and the code you got from the website admin'); ?>"></span>
            </th>
            <td>
              <button id="add-import-source-button" class="button" title="<?= cb_map\__('ADD_IMPORT_SOURCE_BUTTON_TITLE', 'commons-booking-map', 'add import source') ?>"><span class="dashicons dashicons-plus"></span></button>
            </td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('IMPORT_SOURCES', 'commons-booking-map', 'import sources') ?>:
              </th>
            <td id="import-sources"></td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-map-presentation">

      <h1><?= cb_map\__('MAP_PRESENTATION', 'commons-booking-map', 'Map Presentation') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('MAP_SHORTCODE', 'commons-booking-map', 'shortcode') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAP_SHORTCODE_DESC', 'commons-booking-map', 'with this shortcode the map can be included in posts or pages') ?>"></span>
            </th>
            <td>[cb_map id=<?= $cb_map_id ?>]</td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('MAP_HEIGHT', 'commons-booking-map', 'map height') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAP_HEIGHT_DESC', 'commons-booking-map', 'the height the map is rendered with - the width is the same as of the parent element') ?>"></span>
            </th>
            <td><input type="number" min="<?= CB_Map_Admin::MAP_HEIGHT_VALUE_MIN ?>" max="<?= CB_Map_Admin::MAP_HEIGHT_VALUE_MAX ?>" name="cb_map_options[map_height]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'map_height') ); ?>" size="4"> px</td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-zoom">
      <h1><?= cb_map\__('ZOOM', 'commons-booking-map', 'Zoom') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('MIN_ZOOM_LEVEL', 'commons-booking-map', 'min. zoom level') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MIN_ZOOM_LEVEL_DESC', 'commons-booking-map', 'the minimal zoom level a user can choose') ?>"></span>
            </th>
            <td><input type="number" min="<?= CB_Map_Admin::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Admin::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_min]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'zoom_min') ); ?>" size="3"></td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('MAX_ZOOM_LEVEL', 'commons-booking-map', 'max. zoom level') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAX_ZOOM_LEVEL_DESC', 'commons-booking-map', 'the maximal zoom level a user can choose') ?>"></span>
            </th>
            <td><input type="number" min="<?= CB_Map_Admin::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Admin::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_max]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'zoom_max') ); ?>" size="3"></td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('START_ZOOM_LEVEL', 'commons-booking-map', 'start zoom level') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'START_ZOOM_LEVEL_DESC', 'commons-booking-map', 'the zoom level that will be set when the map is loaded') ?>"></span>
            </th>
            <td><input type="number" min="<?= CB_Map_Admin::ZOOM_VALUE_MIN ?>" max="<?= CB_Map_Admin::ZOOM_VALUE_MAX ?>" name="cb_map_options[zoom_start]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'zoom_start') ); ?>" size="3"></td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-positioning-start">
      <h1><?= cb_map\__('POSITIONING_START', 'commons-booking-map', 'Map Positioning (center) at Intialization') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('LATITUDE_START', 'commons-booking-map', 'start latitude') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'LATITUDE_START_DESC', 'commons-booking-map', 'the latitude of the map center when the map is loaded') ?>"></span>
            </th>
            <td><input type="text" name="cb_map_options[lat_start]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'lat_start') ); ?>" size="10"></td>
        </tr>

        <tr>
            <th>
              <?= cb_map\__('LONGITUDE_START', 'commons-booking-map', 'start longitude') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'LONGITUDE_START_DESC', 'commons-booking-map', 'the longitude of the map center when the map is loaded') ?>"></span>
            </th>
            <td><input type="text" name="cb_map_options[lon_start]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'lon_start') ); ?>" size="10"></td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-adaptive-map-section">
      <h1><?= cb_map\__('ADAPTIVE_MAP_SECTION', 'commons-booking-map', 'Adaptive Map Section') ?></h1>

      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('ADJUST_MAP_SECTION_TO_MARKERS_INITIALLY', 'commons-booking-map', 'initial adjustment to marker bounds') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ADJUST_MAP_SECTION_TO_MARKERS_INITIALLY_DESC', 'commons-booking-map', 'adjust map section to bounds of shown markers automatically when map is loaded') ?>"></span>
            </th>
            <td>
              <input type="checkbox" name="cb_map_options[marker_map_bounds_initial]" <?= CB_Map_Admin::get_option($cb_map_id, 'marker_map_bounds_initial') ? 'checked="checked"' : '' ?> value="on">
            </td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('ADJUST_MAP_SECTION_TO_MARKERS_FILTER', 'commons-booking-map', 'adjustment to marker bounds on filter') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ADJUST_MAP_SECTION_TO_MARKERS_FILTER_DESC', 'commons-booking-map', 'adjust map section to bounds of shown markers automatically when filtered by users') ?>"></span>
            </th>
            <td>
              <input type="checkbox" name="cb_map_options[marker_map_bounds_filter]" <?= CB_Map_Admin::get_option($cb_map_id, 'marker_map_bounds_filter') ? 'checked="checked"' : '' ?> value="on">
            </td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-popup">
      <h1><?= cb_map\__('POPUP', 'commons-booking-map', 'Marker Popup') ?></h1>
      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('SHOW_LOCATION_OPENING_HOURS', 'commons-booking-map', 'show location opening hours') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'SHOW_LOCATION_OPENING_HOURS_DESC', 'commons-booking-map', 'activate to show the opening hours of locations in the marker popup') ?>"></span>
            </th>
            <td><input type="checkbox" name="cb_map_options[show_location_opening_hours]" <?= CB_Map_Admin::get_option($cb_map_id, 'show_location_opening_hours') ? 'checked="checked"' : '' ?> value="on"></td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('LABEL_LOCATION_OPENING_HOURS', 'commons-booking-map', 'label for opening hours') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'LABEL_LOCATION_OPENING_HOURS_DESC', 'commons-booking-map', 'alternative label for the opening hours of locations in the marker popup') ?>"></span>
            </th>
            <td><input type="text" name="cb_map_options[label_location_opening_hours]" placeholder="<?= cb_map\__('OPENING_HOURS', 'commons-booking-map', 'opening hours') ?>" value="<?= CB_Map_Admin::get_option($cb_map_id, 'label_location_opening_hours') ?>"></td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('SHOW_LOCATION_CONTACT', 'commons-booking-map', 'show location contact') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'SHOW_LOCATION_CONTACT_DESC', 'commons-booking-map', 'activate to show the location contact details in the marker popup') ?>"></span>
            </th>
            <td><input type="checkbox" name="cb_map_options[show_location_contact]" <?= CB_Map_Admin::get_option($cb_map_id, 'show_location_contact') ? 'checked="checked"' : '' ?> value="on"></td>
        </tr>
        <tr>
            <th>
              <?= cb_map\__('LABEL_LOCATION_CONTACT', 'commons-booking-map', 'label for opening hours') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'LABEL_LOCATION_CONTACT_DESC', 'commons-booking-map', 'alternative label for the contact information of locations in the marker popup') ?>"></span>
            </th>
            <td><input type="text" name="cb_map_options[label_location_contact]" placeholder="<?= cb_map\__('CONTACT', 'commons-booking-map', 'opening hours') ?>" value="<?= CB_Map_Admin::get_option($cb_map_id, 'label_location_contact') ?>"></td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-custom-marker">
      <h1><?= cb_map\__('CUSTOM_MARKER', 'commons-booking-map', 'Custom Marker') ?></h1>

      <table style="text-align: left;">
        <tr>
          <th>
            <?= cb_map\__('IMAGE_FILE', 'commons-booking-map', 'image file') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'IMAGE_FILE_DESC', 'commons-booking-map', 'the default marker icon can be replaced by a custom image') ?>"></span>
          </th>
          <td>
            <input id="select-marker-image-button" type="button" class="button" value="<?= cb_map\__('SELECT', 'commons-booking-map', 'select') ?>" />
            <input id="remove-marker-image-button" type="button" class="button" value="<?= cb_map\__('REMOVE', 'commons-booking-map', 'remove') ?>" />
          </td>
        </tr>
        <tr id="marker-image-preview-settings" style="display: none;">
          <td>
            <div>
                <img id="marker-image-preview" src="<?= wp_get_attachment_url(CB_Map_Admin::get_option($cb_map_id, 'custom_marker_media_id')); ?>">
            </div>
            <input type="hidden" name="cb_map_options[custom_marker_media_id]" value="<?= CB_Map_Admin::get_option($cb_map_id, 'custom_marker_media_id') ?>">
          </td>
          <td>
            <div id="marker-image-preview-measurements"></div>
          </td>
        </tr>
        <tr id="marker-icon-size" style="display: none;">
            <th>
              <?= cb_map\__('ICON_SIZE', 'commons-booking-map', 'icon size') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ICON_SIZE_DESC', 'commons-booking-map', 'the size of the custom marker icon image as it is shown on the map') ?>"></span>
            </th>
            <td>
              <input type="text" name="cb_map_options[marker_icon_width]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'marker_icon_width') ); ?>" size="3"> x
              <input type="text" name="cb_map_options[marker_icon_height]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'marker_icon_height') ); ?>" size="3"> px
            </td>

        </tr>
        <tr id="marker-icon-anchor" style="display: none;">
          <th>
            <?= cb_map\__('ANCHOR_POINT', 'commons-booking-map', 'anchor point') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ANCHOR_POINT_DESC', 'commons-booking-map', 'the position of the anchor point of the icon image, seen from the left top corner of the icon, often it is half of the width and full height of the icon size - this point is used to place the marker on the geo coordinates') ?>"></span>
          </th>
          <td>
            <input type="text" name="cb_map_options[marker_icon_anchor_x]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'marker_icon_anchor_x') ); ?>" size="3"> x
            <input type="text" name="cb_map_options[marker_icon_anchor_y]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'marker_icon_anchor_y') ); ?>" size="3"> px
          </td>
        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-cluster">
      <h1><?= cb_map\__('CLUSTER', 'commons-booking-map', 'Cluster') ?></h1>
      <table style="text-align: left;">
        <tr>
            <th>
              <?= cb_map\__('MAX_CLUSTER_RADIUS', 'commons-booking-map', 'max. cluster radius') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'MAX_CLUSTER_RADIUS_DESC', 'commons-booking-map', 'combine markers to a cluster within given radius - 0 for deactivation') ?>"></span>
            </th>
            <td>
              <input type="number" size="3" step="10" min="<?= CB_Map_Admin::MAX_CLUSTER_RADIUS_VALUE_MIN ?>" max="<?= CB_Map_Admin::MAX_CLUSTER_RADIUS_VALUE_MAX ?>" name="cb_map_options[max_cluster_radius]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'max_cluster_radius') ); ?>"> px
            </td>
        </tr>
      </table>

      <h1><?= cb_map\__('CUSTOM_CLUSTER_MARKER', 'commons-booking-map', 'Custom Cluster Marker') ?></h1>

      <table style="text-align: left;">
        <tr>
          <th>
            <?= cb_map\__('IMAGE_FILE', 'commons-booking-map', 'image file') ?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'IMAGE_FILE_DESC', 'commons-booking-map', 'the default marker icon can be replaced by a custom image') ?>"></span>
          </th>
          <td>
            <input id="select-marker-cluster-image-button" type="button" class="button" value="<?= cb_map\__('SELECT', 'commons-booking-map', 'select') ?>" />
            <input id="remove-marker-cluster-image-button" type="button" class="button" value="<?= cb_map\__('REMOVE', 'commons-booking-map', 'remove') ?>" />
          </td>
        </tr>
        <tr id="marker-cluster-image-preview-settings" style="display: none;">
          <td>
            <div>
                <img id="marker-cluster-image-preview" src="<?= wp_get_attachment_url(CB_Map_Admin::get_option($cb_map_id, 'custom_marker_cluster_media_id')); ?>">
            </div>
            <input type="hidden" name="cb_map_options[custom_marker_cluster_media_id]" value="<?= CB_Map_Admin::get_option($cb_map_id, 'custom_marker_cluster_media_id') ?>">
          </td>
          <td>
            <div id="marker-cluster-image-preview-measurements"></div>
          </td>
        </tr>
        <tr id="marker-cluster-icon-size" style="display: none;">
            <th>
              <?= cb_map\__('ICON_SIZE', 'commons-booking-map', 'icon size') ?>:
              <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'ICON_SIZE_DESC', 'commons-booking-map', 'the size of the custom marker icon image as it is shown on the map') ?>"></span>
            </th>
            <td>
              <input type="text" name="cb_map_options[marker_cluster_icon_width]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'marker_cluster_icon_width') ); ?>" size="3"> x
              <input type="text" name="cb_map_options[marker_cluster_icon_height]" value="<?= esc_attr( CB_Map_Admin::get_option($cb_map_id, 'marker_cluster_icon_height') ); ?>" size="3"> px
            </td>

        </tr>
      </table>
    </div>

    <div class="option-group" id="option-group-filter-users">
      <h1><?= cb_map\__('FILTER_USERS', 'commons-booking-map', 'Filter for Users') ?></h1>
      <table style="text-align: left;">
        <tr>
          <th>
            <?= cb_map\__('AVAILABLE_CATEGORIES', 'commons-booking-map', 'available categories')?>:
            <span style="cursor: help;" class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'AVAILABLE_CATEGORIES_DESC', 'commons-booking-map', 'select the categories that are presented the users to filter items - none for no filters') ?>"></span>
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

      <table style="text-align: left;" id="available-categories-custom-markup-wrapper">
        <tr><th col-span="2"><?= cb_map\__('CUSTOM_CATEGORY_FILTER_LABEL_MARKUP', 'commons-booking-map', 'custom markup for filters')?></th><tr>
      </table>
    </div>

    <div class="option-group" id="option-group-filter-presets">
      <h1><?= cb_map\__('FILTER_PRESETS', 'commons-booking-map', 'Filter Presets') ?></h1>
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
    </div>

</div>

<script>

jQuery(document).ready(function($) {
  var map_type_option_groups = {
    //local
    1: ['usage', 'map-presentation', 'zoom', 'positioning-start', 'adaptive-map-section', 'popup', 'custom-marker', 'cluster', 'filter-users', 'filter-presets'],
    //import
    2: ['usage', 'data-import', 'map-presentation', 'zoom', 'positioning-start', 'adaptive-map-section', 'popup', 'custom-marker', 'cluster'],
    //export
    3: ['usage', 'data-export', 'popup', 'filter-presets']
  };

  function show_option_groups(map_type) {
    //show options inside option groups
    $('.option').show();

    //show/hide groups
    $('.option-group').each(function() {
      var $this = $(this);
      if(map_type_option_groups[map_type].includes($this.attr('id').replace('option-group-', ''))) {
        $(this).show();
      }
      else {
        $(this).hide();
      }
    });
  };

  $('#map_type').change(function() {
    show_option_groups($(this).val());
  });

  show_option_groups($('#map_type').val());

  //----------------------------------------------------------------------------
  // users filters custom markup

  $('.cb_items_available_category').change(function() {
    var $this = $(this);
    var el_id_arr = $this.attr('id').split('-');
    var cat_id = el_id_arr[el_id_arr.length - 1];
    //console.log(cat_id);

    if ($this.prop("checked")) {
      //console.log('checked');
      add_custom_markup_option(cat_id, $this.parent().text(), $this.parent().text().trim());
    }
    else {
      //console.log('unchecked');
      $('#available_category_cutom_markup_' + cat_id).remove();
    }

  });

  function add_custom_markup_option(cat_id, label_text, markup) {
    var $accm_table = $('#available-categories-custom-markup-wrapper');
    var $row = $('<tr id="available_category_cutom_markup_' + cat_id + '"><th>' + label_text + ':</th><td><textarea name="cb_map_options[cb_items_available_categories_custom_markup][' + cat_id + ']">' + markup + '</textarea></td></tr>');
    $accm_table.append($row);
  }

  function add_custom_markup_options() {
    var custom_markup_options_data = <?= json_encode( CB_Map_Admin::get_option($cb_map_id, 'cb_items_available_categories_custom_markup') ); ?>;
    $('.cb_items_available_category').each(function() {
      var $this = $(this);

      if ($this.prop("checked")) {
        var el_id_arr = $this.attr('id').split('-');
        var cat_id = el_id_arr[el_id_arr.length - 1];

        var markup = custom_markup_options_data[cat_id] || $this.parent().text().trim();
        add_custom_markup_option(cat_id, $this.parent().text(), markup);
      }

    });
  }

  add_custom_markup_options();

  //----------------------------------------------------------------------------
  // data export

  function random_string(length, chars) {
    var result = '';
    for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
    return result;
  }

  function create_export_code($input_field) {
    var export_code = random_string(12, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    $input_field.val(export_code);
  }

  var $export_code_input = $('input[name="cb_map_options[export_code]"]');

  if($export_code_input.val().length == 0) {
    create_export_code($export_code_input);
  }

  $('#create-export-code-button').click(function() {
    create_export_code($export_code_input);
  });

  //----------------------------------------------------------------------------
  // data import
  function add_import_sources($target_element, urls, codes) {
    $.each(urls, function(index) {
      add_import_source($target_element, urls[index], codes[index]);
    });
  }

  function add_import_source($target_element, url, code) {
    $url_input = $('<input type="url" pattern="https?://.*" autocomplete="off" size="20" name="cb_map_options[import_sources][urls][]" placeholder="<?= cb_map\__( 'URL', 'commons-booking-map', 'URL') ?>" required>');
    $code_input = $('<input type="text" autocomplete="off" size="10" name="cb_map_options[import_sources][codes][]" minlength="<?= CB_Map_Admin::EXPORT_CODE_VALUE_MIN_LENGTH ?>" placeholder="<?= cb_map\__( 'CODE', 'commons-booking-map', 'Code') ?>" required>');

    var $import_source = $('<div style="margin-top: 5px;"></div>');

    $import_source.append($url_input);
    $import_source.append($code_input);

    if(url) {
      $url_input.val(url);
    }
    if(code) {
      $code_input.val(code);
    }

    var $remove_source_button = $('<button style="margin-left: 10px;" class="button remove-import-source-button" title="<?= cb_map\__('REMOVE_IMPORT_SOURCE_BUTTON_TITLE', 'commons-booking-map', 'remove import source') ?>"><span class="dashicons dashicons-minus"></span></button>');
    $import_source.append($remove_source_button);

    $remove_source_button.click(function(event) {
      event.preventDefault();
      $remove_source_button.parent('div').remove();
    });

    var $import_button = $('<button style="margin-left: 10px;" class="button test-import-source-button" title="<?= cb_map\__('TEST_IMPORT_SOURCE_BUTTON_TITLE', 'commons-booking-map', 'test import source') ?>"><span class="dashicons dashicons-download"></span></button>');
    $import_source.append($import_button);

    $import_button.click(function(event) {
      event.preventDefault();

      var url = $($import_source.find('input')[0]).val();

      var data = {
        action: 'cb_map_import_spurce_test',
        cb_map_id: <?= $cb_map_id ?>,
        url: url,
        code: $($import_source.find('input')[1]).val()
      };

      $import_button.prop("disabled", true);
      $import_button.find('span').removeClass('dashicons-download');
      $import_button.find('span').addClass('dashicons-update');
      $import_button.find('span').addClass('rotate');

      jQuery.post('<?= get_site_url(null, '', null) . '/wp-admin/admin-ajax.php' ?>', data, function(response) {
        $import_button.find('span').addClass('dashicons-yes');

        setTimeout(function() {
          $import_button.find('span').removeClass('dashicons-yes');
          $import_button.find('span').addClass('dashicons-download');
          $import_button.prop("disabled", false);
        }, 2000);
      }).fail(function() {
        $import_button.find('span').addClass('dashicons-no');

        setTimeout(function() {
          $import_button.find('span').removeClass('dashicons-no');
          $import_button.find('span').addClass('dashicons-download');
          $import_button.prop("disabled", false);
        }, 2000);
      }).always(function() {

        $import_button.find('span').removeClass('rotate');
        $import_button.find('span').removeClass('dashicons-update');

      });
    });

    $target_element.append($import_source);
  }

  $('#add-import-source-button').click(function(event) {
    event.preventDefault();
    add_import_source($('#import-sources'));
  });

  var import_sources = <?= CB_Map_Admin::get_option($cb_map_id, 'import_sources') ? json_encode(CB_Map_Admin::get_option($cb_map_id, 'import_sources')) : 'null'; ?>;

  if(import_sources) {
    add_import_sources($('#import-sources'), import_sources.urls, import_sources.codes);
  }

});

</script>
