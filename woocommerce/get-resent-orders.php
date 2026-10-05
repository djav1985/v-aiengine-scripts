/**
 * Lists the current user's 3 most recent completed/processing orders.
 *
 * @return string JSON: {status, message, data?}
 */
function get_recent_orders()
{
    if (!is_user_logged_in()) {
        return wp_json_encode(['status' => 'error', 'message' => 'User is not logged in. Please <a href="' . esc_url(wc_get_page_permalink('myaccount')) . '">log in</a>.']);
    }

    $user_id = get_current_user_id();
    $args = [
        'customer_id' => $user_id,
        'limit' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
        'status' => ['wc-completed', 'wc-processing'] // Only get completed and processing orders
    ];

    $query = new WC_Order_Query($args);
    $orders = $query->get_orders();

    // Return only the fields the chatbot needs, not the full order object.
    $output = [];
    foreach ($orders as $order) {
        $output[] = [
            'order_id' => $order->get_id(),
            'order_total' => wc_price($order->get_total()),
            'order_date' => $order->get_date_created()->date('Y-m-d H:i:s'),
            'order_status' => $order->get_status()
        ];
    }

    if (empty($output)) {
        return wp_json_encode(['status' => 'success', 'message' => 'No recent orders found.', 'data' => []]);
    }

    return wp_json_encode(['status' => 'success', 'message' => 'Recent orders retrieved.', 'data' => $output]);
}