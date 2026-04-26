<?php
// Africa's Talking SMS Configuration
define('AT_USERNAME', 'your_username');   // Replace with your Africa's Talking username
define('AT_API_KEY', 'your_api_key');     // Replace with your Africa's Talking API key
define('AT_SENDER_ID', 'Crosslife');      // Replace with your registered sender ID (or leave blank for sandbox)
define('AT_ENV', 'sandbox');              // Change to 'production' when live

function sendSMS($recipients, $message) {
    $url = AT_ENV === 'sandbox'
        ? 'https://api.sandbox.africastalking.com/version1/messaging'
        : 'https://api.africastalking.com/version1/messaging';

    if (is_array($recipients)) {
        $recipients = implode(',', $recipients);
    }

    $data = [
        'username' => AT_USERNAME,
        'to'       => $recipients,
        'message'  => $message,
    ];
    if (AT_SENDER_ID) {
        $data['from'] = AT_SENDER_ID;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'apiKey: ' . AT_API_KEY,
        ],
    ]);
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) return ['success' => false, 'error' => $error];

    $result = json_decode($response, true);
    $status = $result['SMSMessageData']['Recipients'][0]['status'] ?? 'Unknown';
    return ['success' => ($status === 'Success'), 'response' => $result];
}
?>
