/**
 * Sends a callback request via email.
 *
 * @param string $name    The name of the person requesting a callback.
 * @param string $phone   The phone number of the person requesting a callback.
 * @param string $email   The email address of the person requesting a callback.
 * @param string $message The message or reason for the callback.
 * @return string JSON: {status, message}
 */
function send_callback_request($name, $phone, $email, $message)
{
    // Initialize an array to keep track of any missing fields
    $missing_fields = [];

    // Check required fields
    if (empty($name)) {
        $missing_fields[] = 'Name';
    }

    if (empty($phone)) {
        $missing_fields[] = 'Phone';
    }

    if (empty($email)) {
        $missing_fields[] = 'Email';
    }

    if (empty($message)) {
        $missing_fields[] = 'Message';
    }

    // If there are missing fields, return an error message
    if (!empty($missing_fields)) {
        return wp_json_encode([
            'status' => 'error',
            'message' => 'The following fields are missing: ' . implode(', ', $missing_fields) . '.'
        ]);
    }

    // Sanitize and validate email
    $email = sanitize_email($email);

    if (!is_email($email)) {
        return wp_json_encode([
            'status' => 'error',
            'message' => 'Please enter a valid email address.'
        ]);
    }

    // Define the recipient email address
    $to = "services@vontainment.com";

    // Set the email subject
    $subject = "Callback Requested";

    // Construct the email body
    $body = "You have received a new callback request.\n\n"
          . "Name: " . $name . "\n"
          . "Phone: " . $phone . "\n"
          . "Email: " . $email . "\n"
          . "Message: " . $message;

    // Define the email headers
    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: Vontainment Services <no-reply@vontainment.com>',
        'Reply-To: ' . $name . ' <' . $email . '>'
    ];

    // Send the email using wp_mail
    $sent = wp_mail($to, $subject, $body, $headers);

    // Check if the email was sent successfully
    if ($sent) {

        // Lock the chatbot after a successful callback request
        setcookie(
            'mwai_chat_locked',
            '1',
            [
                'expires'  => time() + DAY_IN_SECONDS,
                'path'     => '/',
                'secure'   => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        // Make the cookie available during the current PHP request as well
        $_COOKIE['mwai_chat_locked'] = '1';

        return wp_json_encode([
            'status' => 'success',
            'message' => 'Your callback request was submitted successfully.'
        ]);

    } else {

        return wp_json_encode([
            'status' => 'error',
            'message' => 'Failed to send email. Please call us at (941) 313-2107.'
        ]);
    }
}