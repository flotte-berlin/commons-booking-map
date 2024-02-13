<div class="wrap">

    <h1><?= cb_map\__('SETTINGS_PAGE_HEADER', 'commons-booking-map', 'Settings for Commons Booking Map') ?></h1>

    <p><?= cb_map\__('SETTINGS_DESCRIPTION', 'commons-booking-map', 'general settings regarding the behaviour of the Commons Booking Map plugin') ?></p>

    <form method="post" action="options.php">
        <?php
        settings_fields('cb-map-settings');
        do_settings_sections('cb-map-settings');
        ?>

        <table class="text-left">
            <tr>
                <th>
                    <?= cb_map\__('BOOKING_PAGE_LINK_REPLACEMENT', 'commons-booking-map', 'replace map link on booking page') ?>
                    :
                    <span class="dashicons dashicons-editor-help"
                          title="<?= cb_map\__('BOOKING_PAGE_LINK_REPLACEMENT_DESC', 'commons-booking-map', 'set the target of the map link on booking page to openstreetmap') ?>"></span>
                </th>
                <td>
                    <input type="checkbox"
                           name="cb_map_options[booking_page_link_replacement]" <?= CB_Map_Settings::get_option('booking_page_link_replacement') ? 'checked="checked"' : '' ?>
                           value="on">
                </td>

            </tr>
            <tr>
                <th>
                    <?= cb_map\__('BOOKING_PAGE_CACHE_INTERVAL', 'commons-booking-map', 'refresh interval of map cache') ?>:
                    <span class="dashicons dashicons-editor-help" title="<?= cb_map\__( 'BOOKING_PAGE_CACHE_INTERVAL_DESC', 'commons-booking-map', 'refresh every n minutes, for maps that show item availability') ?>"></span>
                </th>

                <td>
                    <input type="number"
                           step="5"
                           min="0"
                           name="cb_map_options[cb_map_cache_interval_in_minutes]"
                           value="<?= CB_Map_Settings::get_option('cb_map_cache_interval_in_minutes')?>">
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
