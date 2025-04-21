<?php
// save_data.php

header('Content-Type: application/json');

// DB connection
$host = 'localhost';
$dbname = 'app';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    if (!is_array($data)) throw new Exception("Invalid input format");

    foreach ($data as $row) {
        // Check or insert SR
        $srCheck = $pdo->prepare("SELECT id FROM sr_list WHERE srid = :srid");
        $srCheck->execute([':srid' => $row['srid']]);
        $sr = $srCheck->fetch(PDO::FETCH_ASSOC);

        if (!$sr) {
            $insertSr = $pdo->prepare("INSERT INTO sr_list (name, srid) VALUES (:name, :srid)");
            $insertSr->execute([':name' => $row['name'], ':srid' => $row['srid']]);
            $srId = $pdo->lastInsertId();
        } else {
            $srId = $sr['id'];
        }

        // Insert delivery data
        $stmt = $pdo->prepare("INSERT INTO delivery_data 
            (sr_id, date, ofd, delivery, rate, fuel, advance, payment)
            VALUES (:sr_id, :date, :ofd, :delivery, :rate, :fuel, :advance, :payment)");

        $stmt->execute([
            ':sr_id' => $srId,
            ':date' => $row['date'],
            ':ofd' => $row['ofd'],
            ':delivery' => $row['delivery'],
            ':rate' => $row['rate'],
            ':fuel' => $row['fuel'],
            ':advance' => $row['advance'],
            ':payment' => $row['payment']
        ]);
    }

    echo json_encode(["status" => "success", "message" => "Data saved SR-wise"]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
