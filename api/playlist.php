<?php
// Fetch the JSON data from the URL
$json_url = "https://raw.githubusercontent.com/byte-capsule/Toffee-Channels-Link-Headers/refs/heads/main/toffee_channel_data.json";
$json_data = file_get_contents($json_url);

// Check if data fetching is successful
if ($json_data === false) {
    die("Failed to fetch JSON data.");
}

// Decode the JSON data into an associative array
$channels = json_decode($json_data, true);

// Check if decoding is successful
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

// Start building the playlist
header('Content-Type: text/plain');
echo "#EXTM3U\n";

// Loop through the channels and build the playlist entries
foreach ($channels as $channel) {
    // Extract relevant data
    $category = $channel['category_name'] ?? "LIVE";
    $name = $channel['name'] ?? "Unknown Channel";
    $link = $channel['link'] ?? "";
    $headers = $channel['headers'] ?? [];
    $logo = $channel['logo'] ?? ""; // Optional, if available in the JSON

    // Build the #EXTINF line
    echo "#EXTINF:-1 group-title=\"$category\" tvg-chno=\"\" tvg-id=\"\"";
    if (!empty($logo)) {
        echo " tvg-logo=\"$logo\"";
    }
    echo ", $name\n";

    // Build the #EXTVLCOPT lines
    if (!empty($headers)) {
        if (isset($headers['user-agent'])) {
            echo "#EXTVLCOPT:http-user-agent={$headers['user-agent']}\n";
        }
        if (isset($headers['cookie'])) {
            echo "#EXTHTTP:{\"cookie\":\"{$headers['cookie']}\"}\n";
        }
    }

    // Add the link
    echo "$link\n";
}
