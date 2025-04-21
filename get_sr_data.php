<?php
// get_sr_data.php

header('Content-Type: application/json');
$host = 'localhost';
$dbname = 'app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    $srid = $_GET['srid'] ?? null;
    if (!$srid) throw new Exception("Missing SR ID");

    $stmt = $pdo->prepare("
        SELECT d.*, s.name 
        FROM delivery_data d
        JOIN sr_list s ON d.sr_id = s.id
        WHERE s.srid = :srid
        ORDER BY d.date DESC
    ");
    $stmt->execute([':srid' => $srid]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "data" => $rows]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
