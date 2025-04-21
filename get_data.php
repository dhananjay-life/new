<?php
header('Content-Type: application/json');

// DB config
$host = 'localhost';
$dbname = 'app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $srid = $_GET['srid'] ?? null;
    $from = $_GET['from'] ?? null;
    $to = $_GET['to'] ?? null;

    $query = "
        SELECT d.*, s.name, s.srid
        FROM delivery_data d
        JOIN sr_list s ON d.sr_id = s.id
        WHERE 1
    ";

    $params = [];

    if ($srid) {
        $query .= " AND s.srid = :srid";
        $params[':srid'] = $srid;
    }

    if ($from) {
        $query .= " AND d.date >= :from";
        $params[':from'] = $from;
    }

    if ($to) {
        $query .= " AND d.date <= :to";
        $params[':to'] = $to;
    }

    $query .= " ORDER BY d.date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "data" => $data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
