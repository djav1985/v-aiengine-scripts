/**
 * Generates an SEO report for a domain.
 *
 * @param string $domain      Domain, e.g. "example.com".
 * @param bool   $full_report Whether to return the full report.
 * @return string JSON: {status, message, data?}
 */
function generateSEOReport($domain, $full_report) {
  if (empty($domain)) {
    return wp_json_encode([
      'status' => 'error',
      'message' => 'Missing required field: Domain (e.g., "example.com")'
    ]);
  }

  $domain = sanitize_text_field($domain);

  // Delegates to the SEO audit plugin's generator, which returns JSON or a WP_Error.
  $report_json = V_WPSA_external_generation($domain, $full_report);

  if (is_wp_error($report_json)) {
    return wp_json_encode([
      'status' => 'error',
      'message' => $report_json->get_error_message()
    ]);
  }

  // Guard against malformed or empty JSON from the audit system.
  $data = json_decode($report_json, true);
  if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
    return wp_json_encode([
      'status' => 'error',
      'message' => 'Invalid or empty response from SEO Audit system.'
    ]);
  }

  return wp_json_encode(['status' => 'success', 'message' => 'SEO report generated.', 'data' => $data]);
}