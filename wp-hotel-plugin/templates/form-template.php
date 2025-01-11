Voici le contenu du fichier /wp-hotel-plugin/wp-hotel-plugin/templates/form-template.php :

<?php
if ( ! current_user_can( 'um_hotels' ) && ! current_user_can( 'administrator' ) ) {
    wp_die( __( 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.', 'wp-hotel-plugin' ) );
}
?>

<form method="post" action="">
    <label for="hotel_name"><?php _e( 'Nom de l\'hôtel', 'wp-hotel-plugin' ); ?></label>
    <input type="text" id="hotel_name" name="hotel_name" required>

    <label for="hotel_photos"><?php _e( 'Photos de l\'hôtel', 'wp-hotel-plugin' ); ?></label>
    <input type="file" id="hotel_photos" name="hotel_photos[]" multiple required>

    <label for="hotel_description"><?php _e( 'Description de l\'hôtel', 'wp-hotel-plugin' ); ?></label>
    <textarea id="hotel_description" name="hotel_description" required></textarea>

    <label for="availability_start"><?php _e( 'Date de début de disponibilité', 'wp-hotel-plugin' ); ?></label>
    <input type="date" id="availability_start" name="availability_start" required>

    <label for="availability_end"><?php _e( 'Date de fin de disponibilité', 'wp-hotel-plugin' ); ?></label>
    <input type="date" id="availability_end" name="availability_end" required>

    <label for="hotel_address"><?php _e( 'Adresse de l\'hôtel', 'wp-hotel-plugin' ); ?></label>
    <input type="text" id="hotel_address" name="hotel_address" required>

    <input type="submit" value="<?php _e( 'Créer l\'hôtel', 'wp-hotel-plugin' ); ?>">
</form>