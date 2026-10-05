/**
 * Repairs download permissions for the current user's recent orders.
 *
 * @return string JSON: {status, message, data?}
 */
function fix_download_permissions() {
    if (!is_user_logged_in()) {
        return wp_json_encode(['status' => 'error', 'message' => 'User is not logged in. Please <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '">log in</a>.']);
    }

    $user_id = get_current_user_id();
    // Only the 3 most recent orders are checked.
    $args = array(
        'customer_id' => $user_id,
        'limit' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $query = new WC_Order_Query($args);
    $orders = $query->get_orders();

    $download_links = [];

    foreach ($orders as $order) {
        $items = $order->get_items();
        foreach ($items as $item) {
            $product = $item->get_product();
            if ($product && $product->is_downloadable()) {
                $downloads = $product->get_files();
                foreach ($downloads as $download_id => $download) {
                    // Grant download permission if the customer doesn't already have it.
                    $has_permission = wc_customer_bought_product($user_id, $user_id, $product->get_id());
                    if (!$has_permission) {
                        wc_downloadable_file_permission($download_id, $product->get_id(), $order->get_id(), $user_id);
                    }

                    // Update product meta for unlimited downloads and no expiration if necessary
                    $allowed_downloads = get_post_meta($product->get_id(), '_wc_download_limit', true);
                    if ($allowed_downloads === '-1') { // Set unlimited downloads if -1
                        update_post_meta($product->get_id(), '_wc_download_limit', '');
                    }

                    $download_expiry = get_post_meta($product->get_id(), '_wc_download_expiry', true);
                    if ($download_expiry === '0') { // Set no expiration if 0
                        update_post_meta($product->get_id(), '_wc_download_expiry', '');
                    }

                    // Build the link the chatbot will show to the user.
                    $download_url = $order->get_download_url($download_id, $item->get_id());
                    $download_links[] = [
                        'name' => esc_html($download['name']),
                        'url' => esc_url($download_url)
                    ];
                }
            }
        }
    }

    if (empty($download_links)) {
        return wp_json_encode(['status' => 'success', 'message' => 'All downloadable products are correct, <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '">click here</a>.', 'data' => []]);
    }

    return wp_json_encode(['status' => 'success', 'message' => 'Download links retrieved.', 'data' => $download_links]);
}