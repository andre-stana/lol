<?php
class WP_Hotel_Plugin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_post_submit_hotel_form', array($this, 'handle_form_submission'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'Création d\'Hôtel',
            'Hôtels',
            'um_hotels',
            'hotel_form',
            array($this, 'render_form'),
            'dashicons-hotel',
            6
        );
    }

    public function render_form() {
        if (!current_user_can('um_hotels') && !current_user_can('administrator')) {
            wp_die('Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
        }
        include plugin_dir_path(__FILE__) . '../templates/form-template.php';
    }

    public function handle_form_submission() {
        if (!current_user_can('um_hotels') && !current_user_can('administrator')) {
            wp_die('Vous n\'avez pas les permissions nécessaires pour soumettre ce formulaire.');
        }

        // Vérification des champs du formulaire
        $hotel_name = sanitize_text_field($_POST['hotel_name']);
        $hotel_description = sanitize_textarea_field($_POST['hotel_description']);
        $hotel_address = sanitize_text_field($_POST['hotel_address']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $hotel_photos = $_FILES['hotel_photos'];

        // Traitement des photos et enregistrement des données dans la base de données
        // (Ajoutez ici la logique pour gérer les fichiers et insérer les données)

        wp_redirect(admin_url('admin.php?page=hotel_form&success=1'));
        exit;
    }
}
?>