<?php
namespace cb_map;

function is_plugin_active($plugin_name){
    $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
    foreach($active_plugins as $plugin){
        if(strpos($plugin, $plugin_name) !== false){
            return true;
        }
    }
    return false;
}
?>
