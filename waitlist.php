<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$host = getenv("MYSQLHOST") ?: "127.0.0.1";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "root";
$db   = getenv("MYSQLDATABASE") ?: "waitlist";
$port = getenv("MYSQLPORT") ? (int)getenv("MYSQLPORT") : 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed: " . $conn->connect_error
    ]);
    exit;
}


$stmt = $conn->prepare("INSERT INTO waitlist 
(full_name, business_name, business_type, city, phone, email, challenge, volume, ready_to_adopt) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssssss",
    $input['fullName'],
    $input['businessName'],
    $input['businessType'],
    $input['city'],
    $input['phone'],
    $input['email'],
    $input['challenge'],
    $input['volume'],
    $input['readyToAdopt']
);

if ($stmt->execute()) {

    // ── TELEGRAM NOTIFICATION ──
   $botToken = getenv("TELEGRAM_BOT_TOKEN");
$chatId   = getenv("TELEGRAM_CHAT_ID");

    $message = "
🍽️ *New Waitlist Signup!*

👤 *Name:* {$input['fullName']}
🏢 *Business:* {$input['businessName']}
📂 *Type:* {$input['businessType']}
📍 *City:* {$input['city']}
📞 *Phone:* {$input['phone']}
📧 *Email:* {$input['email']}
📊 *Monthly Volume:* {$input['volume']}
⚡ *Ready in 30 days:* {$input['readyToAdopt']}
💬 *Challenge:* {$input['challenge']}
    ";

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $payload = http_build_query([
        "chat_id"    => $chatId,
        "text"       => $message,
        "parse_mode" => "Markdown"
    ]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
    // ── END TELEGRAM ──

    echo json_encode(["success" => true]);

} else {
    echo json_encode(["success" => false]);
}

$stmt->close();
$conn->close();
?>
