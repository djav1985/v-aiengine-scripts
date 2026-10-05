/**
 * Updates a user's password after verifying their last order total.
 *
 * @param string $email        User email.
 * @param string $order_total  Total of the user's last order.
 * @param string $new_password New password.
 * @return string JSON: {status, message}
 */
function update_password($email, $order_total, $new_password)
{
    // Sanitize inputs
    $email = sanitize_email($email);
    $order_total = sanitize_text_field($order_total);
    $new_password = sanitize_text_field($new_password);

    // Get user by email
    $user = get_user_by('email', $email);
    if (!$user) {
        return wp_json_encode(['status' => 'error', 'message' => 'User not found.']);
    }

    // Get the last order for the user (used as a verification check)
    $customer_orders = wc_get_orders([
        'customer_id' => $user->ID,
        'limit'       => 1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ]);

    // Fall back to the standard reset email when verification isn't possible.
    if (empty($customer_orders)) {
        if (!function_exists('retrieve_password')) {
            require_once ABSPATH . 'wp-login.php';
        }
        retrieve_password($user->user_login);
        return wp_json_encode(['status' => 'error', 'message' => 'No orders found for this user. So instead, I sent a reset password email. Please check your email and SPAM folder for password reset instructions.']);
    }

    // Get the total of the last order
    $last_order = $customer_orders[0];
    $last_order_total = $last_order->get_total();

    // Remove dollar sign if present
    $order_total = str_replace('$', '', $order_total);

    // Check if the order total matches
    if (floatval($last_order_total) != floatval($order_total)) {
        if (!function_exists('retrieve_password')) {
            require_once ABSPATH . 'wp-login.php';
        }
        retrieve_password($user->user_login);
        return wp_json_encode(['status' => 'error', 'message' => 'Order total does not match. So instead, I sent a reset password email. Please check your email and SPAM folder for password reset instructions.']);
    }

    // Update the user's password
    wp_set_password($new_password, $user->ID);
    return wp_json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
}