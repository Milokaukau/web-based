<?php
// database/search.php

function searchProducts($term) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $like = '%' . $term . '%';

    $sql = "SELECT 
                MIN(p.id)       AS id,
                p.name,
                p.description,
                MIN(p.price)    AS price,
                SUM(p.stock)    AS stock,
                MIN(p.photo)    AS photo,
                c.name          AS category_name
            FROM  tb_product  p
            JOIN  tb_category c  ON c.id = p.category_id
            WHERE p.name LIKE ?
               OR c.name LIKE ?
            GROUP BY p.name, c.name
            ORDER BY p.name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_object()) {
        $rows[] = $row;
    }

    $stmt->close();
    $conn->close();
    return $rows;
}