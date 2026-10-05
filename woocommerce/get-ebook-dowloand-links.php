/**
 * Lists the current user's available downloads.
 *
 * @return string JSON: {status, message, data?}
 */
function get_user_downloads()
{
    if (!is_user_logged_in()) {
        return wp_json_encode(['status' => 'error', 'message' => 'User is not logged in. Please <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '">log in</a>.']);
    }

    $user_id = get_current_user_id();
    $downloads = wc_get_customer_available_downloads($user_id);

    if (empty($downloads)) {
        return wp_json_encode(['status' => 'success', 'message' => 'No downloadable products in orders.', 'data' => []]);
    }

    $download_links = array();
    $current_time = time(); // Get current time once to avoid repeated calls

    // Skip downloads whose access has expired (null means never expires).
    foreach ($downloads as $download) {
        $expires = $download['access_expires'];
        if ($expires === null || strtotime($expires) > $current_time) {
            $download_links[] = array(
                'name' => esc_html($download['download_name']),
                'url' => esc_url_raw($download['download_url'])
            );
        }
    }

    if (empty($download_links)) {
        return wp_json_encode(['status' => 'success', 'message' => 'No downloadable products in orders.', 'data' => []]);
    }

    return wp_json_encode(['status' => 'success', 'message' => 'Download links retrieved.', 'data' => $download_links]);
}