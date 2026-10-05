/**
 * Scrapes content from a webpage using the Jina Reader API.
 *
 * @param string $domain The domain of the webpage to scrape.
 * @return string JSON: {status, message, data?}
 */
function scrape_webpage_content($domain) {
    if (empty($domain)) {
        return wp_json_encode(['status' => 'error', 'message' => 'Missing required field: Domain (e.g., "example.com")']);
    }

    $domain = sanitize_text_field($domain);

    // Jina Reader API URL, constructed using the provided domain
    $apiUrl = "https://r.jina.ai/https://$domain";
    
    // Send a GET request to the Jina Reader API using wp_remote_get()
    $response = wp_remote_get($apiUrl);

    // Check if the request returned an error
    if (is_wp_error($response)) {
        // Return a JSON-encoded error message if the request failed
        return wp_json_encode(['status' => 'error', 'message' => $response->get_error_message()]);
    }

    // Get the response body from the API request
    $responseBody = wp_remote_retrieve_body($response);

    // Return the markdown content in JSON format
    return wp_json_encode(['status' => 'success', 'message' => 'Page scraped.', 'data' => ['markdown' => $responseBody]]);
}