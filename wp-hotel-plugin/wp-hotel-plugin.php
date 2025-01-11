<?php
/**
 * Plugin Name: WP Hotel Plugin
 * Description: Un plugin pour créer des produits WooCommerce dans la catégorie "hôtel".
 * Version: 1.0
 * Author: Votre Nom
 */

// Sécuriser l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Inclure les fichiers nécessaires
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-hotel-plugin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-hotel-plugin-activator.php';

// Activation du plugin
register_activation_hook( __FILE__, array( 'WP_Hotel_Plugin_Activator', 'activate' ) );

// Initialiser le plugin
add_action( 'plugins_loaded', 'wp_hotel_plugin_init' );

function wp_hotel_plugin_init() {
    // Vérifier les permissions de l'utilisateur
    if ( current_user_can( 'um_hotels' ) || current_user_can( 'administrator' ) ) {
        $plugin = new WP_Hotel_Plugin();
        $plugin->run();
    } else {
        add_action( 'admin_notices', 'wp_hotel_plugin_permission_notice' );
    }
}

function wp_hotel_plugin_permission_notice() {
    echo '<div class="notice notice-error"><p>Vous n\'avez pas la permission d\'accéder à cette page.</p></div>';
}
?>