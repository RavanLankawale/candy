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

// Function to generate M3U8 playlist format
function generate_m3u8_entry($channel) {
    $name = $channel['name'] ?? 'Unknown Channel';
    $logo = $channel['tvg-logo'] ?? '';
    $link = $channel['link'] ?? '';
    $headers = $channel['headers'] ?? [];

    if (!$link || empty($headers)) {
        return ""; // Skip invalid channels
    }

    $user_agent = $headers['user-agent'] ?? '';
    $http_headers = json_encode($headers, JSON_UNESCAPED_SLASHES);

    return "#EXTINF:-1 group-title=\"LIVE\" tvg-chno=\"\" tvg-id=\"\" tvg-logo=\"$logo\", $name\n" .
           "#EXTVLCOPT:http-user-agent=$user_agent\n" .
           "#EXTHTTP:$http_headers\n" .
           "$link\n";
}

// Main script execution
$json_url = 'https://raw.githubusercontent.com/Jeshan-akand/Toffee-Channels-Link-Headers/main/toffee_channel_data.json';
$data = fetch_json($json_url);

$channels_data = $data['channels'] ?? [];
$output = "";

foreach ($channels_data as $channel) {
    $entry = generate_m3u8_entry($channel);
    if (!empty($entry)) {
        $output .= $entry . "\n";
    }
}

// Output the M3U8 playlist
header('Content-Type: text/plain');
echo $output;

?>
