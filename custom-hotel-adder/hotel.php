<?php
/**
 * Plugin Name: Custom Hotel Adder
 * Description: Ajout dynamique d'hôtels WooCommerce avec gestion et personnalisation complète des labels depuis l'administration.
 * Version: 1.1
 * Author: Glacier
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Empêche l'accès direct
}

// Enqueue styles and scripts
add_action('wp_enqueue_scripts', 'custom_hotel_adder_enqueue_scripts');

function custom_hotel_adder_enqueue_scripts() {
    wp_enqueue_style('custom-hotel-style', plugin_dir_url(__FILE__) . 'css/style.css');
    wp_enqueue_script('custom-hotel-script', plugin_dir_url(__FILE__) . 'js/script.js', ['jquery'], null, true);
}

// Ajouter une page d'options dans l'administration
add_action('admin_menu', 'custom_hotel_admin_menu');

function custom_hotel_admin_menu() {
    add_menu_page(
        "Configuration Formulaire", // Titre de la page
        "Formulaire Hôtels", // Texte du menu
        "manage_options", // Capacité requise
        "custom-hotel-settings", // Identifiant unique
        "custom_hotel_settings_page", // Fonction callback
        "dashicons-building", // Icône du menu
        80 // Position dans le menu
    );
}

function custom_hotel_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Sauvegarder les labels si le formulaire est soumis
    if (isset($_POST['submit_custom_hotel_labels'])) {
        // Vérifier le nonce
        if (!isset($_POST['custom_hotel_nonce']) || !wp_verify_nonce($_POST['custom_hotel_nonce'], 'save_custom_hotel_labels')) {
            wp_die('Nonce verification failed');
        }

        $labels = isset($_POST['custom_labels']) ? $_POST['custom_labels'] : [];
        // Filtrer les labels vides
        $labels = array_filter($labels, function($label) {
            return !empty($label['label']) && !empty($label['id']) && !empty($label['type']);
        });
        // Sanitiser les labels
        $labels = array_map(function($label) {
            return [
                'id' => sanitize_text_field($label['id']),
                'label' => sanitize_text_field($label['label']),
                'type' => sanitize_text_field($label['type']),
            ];
        }, $labels);
        update_option('custom_hotel_form_labels', $labels);
        echo '<div class="updated"><p>Les labels ont été mis à jour avec succès.</p></div>';
    }

    // Réinitialiser les labels si le formulaire de réinitialisation est soumis
    if (isset($_POST['reset_custom_hotel_labels'])) {
        // Vérifier le nonce
        if (!isset($_POST['reset_hotel_nonce']) || !wp_verify_nonce($_POST['reset_hotel_nonce'], 'reset_custom_hotel_labels')) {
            wp_die('Nonce verification failed');
        }

        // Réinitialiser les labels
        delete_option('custom_hotel_form_labels');
        echo '<div class="updated"><p>Les labels ont été réinitialisés avec succès.</p></div>';
    }

    // Charger les labels existants
    $labels = get_option('custom_hotel_form_labels', [
        ["id" => "hotel_name", "label" => "Nom de l'Hôtel", "type" => "text"],
        ["id" => "hotel_description", "label" => "Description", "type" => "textarea"],
        ["id" => "hotel_address", "label" => "Adresse de l'Hôtel", "type" => "text"],
        ["id" => "hotel_start_date", "label" => "Date de début de disponibilité", "type" => "date"],
        ["id" => "hotel_end_date", "label" => "Date de fin de disponibilité", "type" => "date"],
        ["id" => "hotel_main_image", "label" => "Image principale", "type" => "file"],
        ["id" => "hotel_gallery_images", "label" => "Images de la galerie", "type" => "multiple_files"],
    ]);
    ?>
    <div class="wrap">
        <h1>Configuration des Labels du Formulaire</h1>
        <form method="post">
            <?php wp_nonce_field('save_custom_hotel_labels', 'custom_hotel_nonce'); ?>
            <table id="labels-table" class="form-table">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($labels as $index => $label): ?>
                        <tr>
                            <td>
                                <input type="text" name="custom_labels[<?php echo $index; ?>][label]" value="<?php echo esc_attr($label['label']); ?>" class="regular-text">
                                <input type="hidden" name="custom_labels[<?php echo $index; ?>][id]" value="<?php echo esc_attr($label['id']); ?>">
                            </td>
                            <td>
                                <select name="custom_labels[<?php echo $index; ?>][type]">
                                    <option value="text" <?php selected($label['type'], 'text'); ?>>Texte</option>
                                    <option value="textarea" <?php selected($label['type'], 'textarea'); ?>>Zone de texte</option>
                                    <option value="number" <?php selected($label['type'], 'number'); ?>>Nombre</option>
                                    <option value="file" <?php selected($label['type'], 'file'); ?>>Fichier unique</option>
                                    <option value="multiple_files" <?php selected($label['type'], 'multiple_files'); ?>>Fichiers multiples</option>
                                    <option value="checkbox" <?php selected($label['type'], 'checkbox'); ?>>Checkbox</option>
                                    <option value="yes_no" <?php selected($label['type'], 'yes_no'); ?>>Oui/Non</option>
                                    <option value="date" <?php selected($label['type'], 'date'); ?>>Date</option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="remove-label button">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="button" id="add-label" class="button">Ajouter un label</button>
            <?php submit_button('Sauvegarder les labels', 'primary', 'submit_custom_hotel_labels'); ?>
        </form>
        <form method="post" style="margin-top: 20px;">
            <?php wp_nonce_field('reset_custom_hotel_labels', 'reset_hotel_nonce'); ?>
            <?php submit_button('Réinitialiser les labels', 'secondary', 'reset_custom_hotel_labels'); ?>
        </form>
    </div>

    <div class="image-carousel">
        <h2>Galerie d'Images</h2>
        <div class="carousel-container">
            <div class="carousel-images" id="carousel-images"></div>
            <button id="prev" class="carousel-button">Précédent</button>
            <button id="next" class="carousel-button">Suivant</button>
        </div>
        <div class="image-preview" id="image-preview"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('labels-table');
            const addButton = document.getElementById('add-label');
            const carouselImages = document.getElementById('carousel-images');
            const imagePreview = document.getElementById('image-preview');
            let images = [];

            addButton.addEventListener('click', function () {
                const index = table.querySelectorAll('tbody tr').length;
                const row = `
                    <tr>
                        <td>
                            <input type="text" name="custom_labels[${index}][label]" value="" class="regular-text" placeholder="Nom du label">
                            <input type="hidden" name="custom_labels[${index}][id]" value="custom_field_${index}">
                        </td>
                        <td>
                            <select name="custom_labels[${index}][type]">
                                <option value="text">Texte</option>
                                <option value="textarea">Zone de texte</option>
                                <option value="number">Nombre</option>
                                <option value="file">Fichier unique</option>
                                <option value="multiple_files">Fichiers multiples</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="yes_no">Oui/Non</option>
                                <option value="date">Date</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="remove-label button">Supprimer</button>
                        </td>
                    </tr>
                `;
                table.querySelector('tbody').insertAdjacentHTML('beforeend', row);
            });

            table.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-label')) {
                    e.target.closest('tr').remove();
                }
            });

            document.getElementById('hotel_gallery_images').addEventListener('change', function (event) {
                const files = event.target.files;
                images = Array.from(files);
                updateCarousel();
            });

            function updateCarousel() {
                carouselImages.innerHTML = '';
                images.forEach((image, index) => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.alt = 'Image ' + (index + 1);
                        imgElement.classList.add('carousel-image');
                        imgElement.addEventListener('click', function () {
                            removeImage(index);
                        });
                        carouselImages.appendChild(imgElement);
                    };
                    reader.readAsDataURL(image);
                });
            }

            function removeImage(index) {
                images.splice(index, 1);
                updateCarousel();
            }
        });
    </script>
    <?php
}

// Ajouter un shortcode pour le formulaire
add_shortcode('add_hotel_form', 'custom_hotel_form');

function custom_hotel_form() {
    if (!is_user_logged_in()) {
        return '<p>Veuillez vous connecter pour ajouter un hôtel.</p>';
    }

    $labels = get_option('custom_hotel_form_labels', []);
    ob_start();
    ?>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('submit_hotel', 'submit_hotel_nonce'); ?>
        <?php foreach ($labels as $label): ?>
            <label for="<?php echo esc_attr($label['id']); ?>"><?php echo esc_html($label['label']); ?></label>
            <?php if ($label['type'] === 'textarea'): ?>
                <textarea id="<?php echo esc_attr($label['id']); ?>" name="<?php echo esc_attr($label['id']); ?>"></textarea>
            <?php elseif ($label['type'] === 'multiple_files'): ?>
                <input type="file" id="<?php echo esc_attr($label['id']); ?>" name="<?php echo esc_attr($label['id']); ?>[]" multiple>
            <?php elseif ($label['type'] === 'checkbox'): ?>
                <input type="checkbox" id="<?php echo esc_attr($label['id']); ?>" name="<?php echo esc_attr($label['id']); ?>">
            <?php elseif ($label['type'] === 'yes_no'): ?>
                <label><input type="radio" name="<?php echo esc_attr($label['id']); ?>" value="yes"> Oui</label>
                <label><input type="radio" name="<?php echo esc_attr($label['id']); ?>" value="no"> Non</label>
            <?php elseif ($label['type'] === 'date'): ?>
                <input type="date" id="<?php echo esc_attr($label['id']); ?>" name="<?php echo esc_attr($label['id']); ?>">
            <?php else: ?>
                <input type="<?php echo esc_attr($label['type']); ?>" id="<?php echo esc_attr($label['id']); ?>" name="<?php echo esc_attr($label['id']); ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <button type="submit" name="submit_hotel">Ajouter l'Hôtel</button>
    </form>
    <?php
    return ob_get_clean();
}

// Gérer la soumission du formulaire
add_action('init', 'handle_hotel_submission');

function handle_hotel_submission() {
    if (isset($_POST['submit_hotel'])) {
        // Vérifier le nonce
        if (!isset($_POST['submit_hotel_nonce']) || !wp_verify_nonce($_POST['submit_hotel_nonce'], 'submit_hotel')) {
            wp_die('Nonce verification failed');
        }

        // Vérifier les permissions de l'utilisateur
        if (!is_user_logged_in() || !current_user_can('edit_posts')) {
            return;
        }

        // Récupérer les données du formulaire
        $labels = get_option('custom_hotel_form_labels', []);
        $hotel_data = [];
        foreach ($labels as $label) {
            $field_id = $label['id'];
            if (isset($_POST[$field_id])) {
                $hotel_data[$field_id] = sanitize_text_field($_POST[$field_id]);
            }
        }

        // Vérifier et obtenir l'ID de la catégorie "Hotel"
        $category = get_term_by('slug', 'hotel', 'product_cat');
        if (!$category) {
            // Créer la catégorie si elle n'existe pas
            $category_id = wp_insert_term('Hotel', 'product_cat', ['slug' => 'hotel']);
            if (is_wp_error($category_id)) {
                wp_die('Erreur lors de la création de la catégorie "Hotel".');
            }
            $category_id = $category_id['term_id'];
        } else {
            $category_id = $category->term_id;
        }

        // Créer un nouveau produit WooCommerce
        $product = new WC_Product();
        $product->set_name($hotel_data['hotel_name'] ?? 'Nouvel Hôtel');
        $product->set_description($hotel_data['hotel_description'] ?? '');
        $product->set_category_ids([$category_id]); // Ajouter à la catégorie "Hotel"

        // Ajouter les champs personnalisés
        if (isset($hotel_data['hotel_address'])) {
            $product->update_meta_data('hotel_address', $hotel_data['hotel_address']);
        }
        if (isset($hotel_data['hotel_start_date'])) {
            $product->update_meta_data('hotel_start_date', $hotel_data['hotel_start_date']);
        }
        if (isset($hotel_data['hotel_end_date'])) {
            $product->update_meta_data('hotel_end_date', $hotel_data['hotel_end_date']);
        }

        // Enregistrer le produit
        $product_id = $product->save();

        // Gérer l'image principale
        if (isset($_FILES['hotel_main_image']) && $_FILES['hotel_main_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['hotel_main_image'];
            $upload = wp_handle_upload($file, ['test_form' => false]);
            if (!isset($upload['error']) && isset($upload['file'])) {
                $attachment = [
                    'post_mime_type' => $upload['type'],
                    'post_title' => sanitize_file_name($upload['file']),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ];
                $attachment_id = wp_insert_attachment($attachment, $upload['file'], $product_id);
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attach_data);
                set_post_thumbnail($product_id, $attachment_id);
            }
        }

        // Gérer les images de la galerie
        $gallery_image_ids = [];
        if (isset($_FILES['hotel_gallery_images'])) {
            $files = $_FILES['hotel_gallery_images'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i],
                    ];
                    $upload = wp_handle_upload($file, ['test_form' => false]);
                    if (!isset($upload['error']) && isset($upload['file'])) {
                        $attachment = [
                            'post_mime_type' => $upload['type'],
                            'post_title' => sanitize_file_name($upload['file']),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        ];
                        $attachment_id = wp_insert_attachment($attachment, $upload['file'], $product_id);
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                        wp_update_attachment_metadata($attachment_id, $attach_data);
                        $gallery_image_ids[] = $attachment_id;
                    }
                }
            }
        }

        // Ajouter les images à la galerie du produit
        if (!empty($gallery_image_ids)) {
            update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_image_ids));
        }

        // Rediriger ou afficher un message de succès
        wp_redirect(add_query_arg('hotel_added', 'true', get_permalink()));
        exit;
    }
}
?>