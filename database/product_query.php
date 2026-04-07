<?php
// database/product_query.php

function get_all_products($db) {
    return $db->query('
        SELECT p.*, c.name AS color_name, cat.name AS category_name
        FROM tb_product p
        JOIN tb_color c      ON p.color_id    = c.id
        JOIN tb_category cat ON p.category_id = cat.id
    ')->fetchAll();
}

function get_product_by_id($db, $id) {
    $stm = $db->prepare('SELECT * FROM tb_product WHERE id = ?');
    $stm->execute([$id]);
    return $stm->fetch();
}

function insert_product($db, $color_id, $category_id, $name, $description,
                        $weight_g, $height_cm, $base_diameter_cm, $material,
                        $price, $stock, $photo) {
    $stm = $db->prepare('
        INSERT INTO tb_product
            (color_id, category_id, name, description, weight_g, height_cm,
             base_diameter_cm, material, price, stock, photo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stm->execute([$color_id, $category_id, $name, $description,
                   $weight_g, $height_cm, $base_diameter_cm, $material,
                   $price, $stock, $photo]);
}

function update_product($db, $color_id, $category_id, $name, $description,
                        $weight_g, $height_cm, $base_diameter_cm, $material,
                        $price, $stock, $photo, $id) {
    $stm = $db->prepare('
        UPDATE tb_product
        SET color_id = ?, category_id = ?, name = ?, description = ?,
            weight_g = ?, height_cm = ?, base_diameter_cm = ?, material = ?,
            price = ?, stock = ?, photo = ?
        WHERE id = ?
    ');
    $stm->execute([$color_id, $category_id, $name, $description,
                   $weight_g, $height_cm, $base_diameter_cm, $material,
                   $price, $stock, $photo, $id]);
}

function get_colors($db) {
    return $db->query('SELECT id, name FROM tb_color ORDER BY name')
              ->fetchAll(PDO::FETCH_KEY_PAIR);
}

function get_categories($db) {
    return $db->query('SELECT id, name FROM tb_category ORDER BY name')
              ->fetchAll(PDO::FETCH_KEY_PAIR);
}