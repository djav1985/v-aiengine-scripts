# v-aiengine-scripts

Code snippets for the [AI Engine](https://wordpress.org/plugins/ai-engine/) (Meow Apps) chatbot on a WordPress/WooCommerce site. Most files are PHP functions registered in AI Engine's **Code Engine** so the chatbot can call them. The rest are hooks or front-end scripts.

## Response format

Every Code Engine function returns a JSON string:

```json
{ "status": "success" | "error", "message": "Human-readable text", "data": [] }
```

`data` is only present when the function returns a payload.

## Files

| File | Function / hook | Purpose |
| --- | --- | --- |
| [fix-download-premissions.php](fix-download-premissions.php) | `fix_download_permissions()` | Repairs download permissions for the logged-in user's 3 most recent orders and returns the links. |
| [get-ebook-dowloand-links.php](get-ebook-dowloand-links.php) | `get_user_downloads()` | Lists the logged-in user's non-expired downloads. |
| [get-resent-orders.php](get-resent-orders.php) | `get_recent_orders()` | Returns the last 3 completed or processing orders. |
| [generate-seo-report.php](generate-seo-report.php) | `generateSEOReport($domain, $full_report)` | Generates an SEO audit for a domain. |
| [webscraper.php](webscraper.php) | `scrape_webpage_content($domain)` | Fetches a page as markdown via the Jina Reader API. |
| [send-callback.php](send-callback.php) | `send_callback_request(...)` | Emails a callback request to staff and locks the chatbot. |
| [lock-chat.php](lock-chat.php) | `mwai_ai_allowed` filter | Stops chatbot replies after a callback is requested. |
| [update-passwords.php](update-passwords.php) | `update_password(...)` | Resets a password after checking the last order total, otherwise sends a reset email. |
| [youtube.js](youtube.js) | n/a | Replaces YouTube links in chatbot messages with embedded players. |

## Requirements

- WordPress with the AI Engine plugin (Pro for Code Engine functions)
- WooCommerce, for the order and download functions
- The SEO audit plugin that provides `V_WPSA_external_generation()`, for `generateSEOReport`

## Usage

1. **Functions:** in AI Engine, open **Code Engine** and create a function. Paste the file contents, keeping the function name, and define its arguments to match the signature.
2. **`lock-chat.php`:** add it as a code snippet, for example with a snippets plugin or the theme's `functions.php`.
3. **`youtube.js`:** it includes its own `<script>` tags. Add it to the site footer, for example with a header/footer plugin.

Function names are used by AI Engine to find the code, so don't rename them without updating the Code Engine entries.

## Security notes

- `update_password()` resets an account's password after checking only the email and last order total. Treat it as low assurance.
- `scrape_webpage_content()` fetches whatever domain it is given, with no allowlist.

## License

See [LICENSE](LICENSE).
