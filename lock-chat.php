// Blocks further AI Engine chatbot replies once a callback was requested (cookie set by send_callback_request).
add_filter('mwai_ai_allowed', function ($allowed, $query, $limits) {
    if ($query instanceof Meow_MWAI_Query_Text && $query->scope === 'chatbot') {
        if (!empty($_COOKIE['mwai_chat_locked'])) {
            // Returning a string makes AI Engine show it instead of calling the model.
            return "Thank you for your interest. I have sent the request for a callback from our staff. If you need further assistance or would like to speak with someone now, give us a call at (941) 313-2107.";
        }
    }
    return $allowed;
}, 10, 3);