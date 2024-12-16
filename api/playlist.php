<?php

// Function to fetch JSON data from a URL
function fetch_json($url) {
    $response = file_get_contents($url);
    if ($response === FALSE) {
        die("Error fetching JSON data from: $url\n");
    }
    $data = json_decode($response, true);
    if ($data === NULL) {
        die("Error decoding JSON data.\n");
    }
    return $data;
}

// Function to make HTTP requests with custom headers
function fetch_with_headers($url, $headers) {
    $http_headers = [];
    foreach ($headers as $key => $value) {
        $http_headers[] = "$key: $value";
    }

    $options = [
        'http' => [
            'header' => implode("\r\n", $http_headers),
            'method' => 'GET'
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return "Error fetching data from: $url\n";
    }

    return $response;
}

// Main script execution
$json_url = 'https://raw.githubusercontent.com/Jeshan-akand/Toffee-Channels-Link-Headers/main/toffee_channel_data.json';
$data = fetch_json($json_url);

$name = $data['name'] ?? 'Unknown';
$owner = $data['owner'] ?? 'Unknown';
$channels_amount = $data['channels_amount'] ?? 0;
$channels_data = $data['channels'] ?? [];

echo "Application Name: $name\n";
echo "Owner: $owner\n";
echo "Total Channels: $channels_amount\n\n";

foreach ($channels_data as $channel) {
    $link = $channel['link'] ?? null;
    $headers = $channel['headers'] ?? [];

    if ($link === null || empty($headers)) {
        echo "Invalid channel data. Skipping...\n";
        continue;
    }

    echo "✓ Channel Link: $link\n";
    echo "✓ Channel Headers: " . print_r($headers, true) . "\n";

    $response = fetch_with_headers($link, $headers);

    echo "✓ Response From Toffee Server:\n$response\n\n";
}

?>
