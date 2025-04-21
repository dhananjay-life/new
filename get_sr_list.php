<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $stmt = $pdo->query("SELECT DISTINCT srid, name FROM sr_list ORDER BY name ASC");
    $srList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($srList);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>