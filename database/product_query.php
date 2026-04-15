<?php
// database/product_query.php

function get_all_products($db) {
    $stm = $db->query('
        SELECT *
        FROM tb_product
        ORDER BY
            CASE WHEN is_active = 0 OR stock = 0 THEN 1 ELSE 0 END ASC,
            id ASC
    ');
    return $stm->fetchAll(PDO::FETCH_OBJ);
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