<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// ── DB config ── (update to match your credentials)
$host = "127.0.0.1";
$db   = "waitlist";
$user = "root";
$pass = "root";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT setting_value FROM enflow_settings WHERE setting_key = 'launch_date'");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(["success" => false, "error" => "Launch date not found"]);
        exit;
    }

    $launchDate = new DateTime($row['setting_value'], new DateTimeZone("Africa/Lagos"));
    $now        = new DateTime("now",                 new DateTimeZone("Africa/Lagos"));
    $diff       = $launchDate->getTimestamp() - $now->getTimestamp();

    if ($diff < 0) $diff = 0;

    echo json_encode([
        "success"     => true,
        "launch_date" => $row['setting_value'],
        "remaining"   => [
            "days"    => floor($diff / 86400),
            "hours"   => floor(($diff % 86400) / 3600),
            "minutes" => floor(($diff % 3600) / 60),
            "seconds" => $diff % 60
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
