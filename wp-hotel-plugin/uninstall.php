<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Supprimer les options du plugin
delete_option( 'wp_hotel_plugin_options' );

// Supprimer les tables personnalisées si elles existent
global $wpdb;
$table_name = $wpdb->prefix . 'hotels';

if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name ) {
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}
?>