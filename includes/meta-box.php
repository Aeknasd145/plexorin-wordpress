<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function plexorin_add_meta_box() {
    add_meta_box(
        'plexorin_meta_box',
         __('Plexorin Post Settings', 'plexorin'),
        'plexorin_meta_box_callback',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'plexorin_add_meta_box');

function plexorin_meta_box_callback($post) {
    wp_nonce_field('plexorin_meta_box', 'prefix_nonce');

    $cancel_share = get_post_meta($post->ID, '_plexorin_cancel_share', true);
    $custom_title = get_post_meta($post->ID, '_plexorin_custom_title', true);
    $custom_description = get_post_meta($post->ID, '_plexorin_custom_description', true);
    $custom_image = get_post_meta($post->ID, '_plexorin_custom_image', true);

    ?>
    <p>
        <label for="plexorin_cancel_share">
            <input type="checkbox" name="plexorin_cancel_share" id="plexorin_cancel_share" value="1" <?php checked($cancel_share, '1'); ?> />
            <?php esc_html_e('Bu gönderi paylaşılmasın', 'plexorin'); ?>
        </label>
    </p>
    <p>
        <label for="plexorin_custom_title"><?php esc_html_e('Özel Başlık', 'plexorin'); ?></label>
        <input type="text" name="plexorin_custom_title" id="plexorin_custom_title" value="<?php echo esc_attr($custom_title); ?>" class="widefat" />
    </p>
    <p>
        <label for="plexorin_custom_description"><?php esc_html_e('Özel Açıklama', 'plexorin'); ?></label>
        <textarea name="plexorin_custom_description" id="plexorin_custom_description" class="widefat"><?php echo esc_textarea($custom_description); ?></textarea>
    </p>
    <p>
        <label for="plexorin_custom_image"><?php esc_html_e('Özel Öne Çıkan Resim', 'plexorin'); ?></label>
        <input type="text" name="plexorin_custom_image" id="plexorin_custom_image" value="<?php echo esc_url($custom_image); ?>" class="widefat" />
    </p>
    <?php
}

function plexorin_save_postdata($post_id) {
    // Check if our nonce is set.
    if (!isset($_POST['prefix_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! isset( $_POST['prefix_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['prefix_nonce'] ) ) , 'prefix_nonce' ) ){
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $cancel_share = isset($_POST['plexorin_cancel_share']) ? '1' : '';
    update_post_meta($post_id, '_plexorin_cancel_share', $cancel_share);

    if (isset($_POST['plexorin_custom_title'])) {
        update_post_meta($post_id, '_plexorin_custom_title', sanitize_text_field($_POST['plexorin_custom_title']));
    }

    if (isset($_POST['plexorin_custom_description'])) {
        update_post_meta($post_id, '_plexorin_custom_description', sanitize_textarea_field($_POST['plexorin_custom_description']));
    }

    if (isset($_POST['plexorin_custom_image'])) {
        update_post_meta($post_id, '_plexorin_custom_image', esc_url_raw($_POST['plexorin_custom_image']));
    }
}
add_action('save_post', 'plexorin_save_postdata');
