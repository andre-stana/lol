<?php
class WP_Hotel_Plugin_Activator {
    public static function activate() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'hotels';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            hotel_name tinytext NOT NULL,
            hotel_photos text NOT NULL,
            hotel_description text NOT NULL,
            availability_start date NOT NULL,
            availability_end date NOT NULL,
            hotel_address text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}