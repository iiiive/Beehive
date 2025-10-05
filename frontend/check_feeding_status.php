<?php
require_once "../config.php";
require_once "discord_alertfeed.php";

date_default_timezone_set('Asia/Manila');

// Fetch latest feeding schedule
$sql = "SELECT user_id, next_feed FROM bee_feeding_schedule ORDER BY id DESC LIMIT 1";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($res);

if ($row) {
    $next_feed = strtotime($row['next_feed']);
    $now = time();

    // If current time is past next_feed, trigger alert
    if ($now >= $next_feed) {
        sendDiscordAlert("🐝 **Alert:** The bees are hungry! Time to feed them 🍯");
    }
}

mysqli_close($link);
?>
