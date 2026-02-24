<?php
session_start();
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header("Access-Control-Allow-Origin: *"); // If needed for cross-origin

require '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "data: " . json_encode(['error' => 'Not logged in']) . "\n\n";
    flush();
    exit;
}

$userId = $_SESSION['user_id'];
$lastId = isset($_GET['lastId']) ? intval($_GET['lastId']) : 0;

ignore_user_abort(true); // Keep streaming even if client disconnects
set_time_limit(0); // No timeout

while (true) {
    $newNotifs = [];
    try {
        // Fetch ALL new notifications since lastId (no LIMIT, but expect few)
        $stmt = $pdo->prepare("
            SELECT id, message, 
                   DATE_FORMAT(date, '%Y-%m-%d %H:%i:%s') AS date,
                   read_status AS read
            FROM notifications 
            WHERE user_id = :user_id AND id > :lastId 
            ORDER BY id ASC
        ");
        $stmt->execute([':user_id' => $userId, ':lastId' => $lastId]);
        
        while ($notif = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $newNotifs[] = $notif;
        }

        if (!empty($newNotifs)) {
            // Update lastId to the highest in this batch
            $lastId = max(array_column($newNotifs, 'id'));
            
            // Stream as array
            echo "data: " . json_encode($newNotifs) . "\n\n";
            ob_flush();
            flush();
        } else {
            // No new; heartbeat
            echo ": heartbeat\n\n";
            ob_flush();
            flush();
        }
    } catch (PDOException $e) {
        echo "data: " . json_encode(['error' => 'Database error']) . "\n\n";
        ob_flush();
        flush();
        break;
    }

    // Wait 3 seconds
    sleep(3);

    // Check disconnection
    if (connection_aborted()) {
        break;
    }
}

// Close
echo "event: close\ndata: Connection closed\n\n";
flush();
?>