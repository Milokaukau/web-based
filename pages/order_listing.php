<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/order_listing.php";

$_title = 'Order Listing';

include $project_root.'components/header.php';
?>

<form method="GET" style="margin-bottom: 20px;">
    <button type="submit" name="member_id" value="1">Filter Member ID = 1</button>
    
    <?php 
        if (isset($_GET['member_id'])) { 
    ?>
        <a href="?"><button type="button">Clear Filter</button></a>
    <?php 
        } 
    ?>
</form>

<table>
    <tr>
        <th>Order ID</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Total Amount</th>
        <th>Member</th>
        <th>Status</th>
    </tr>
    <?php 
        if ($arr){
            foreach ($arr as $data){
    ?>  
            <tr>
                <td><?= $data->order_id ?></td>
                <td><?= $data->product_name ?></td>
                <td><?= $data->quantity ?></td>
                <td><?= $data->amount ?></td>
                <td><?= $data->member_name ?></td>
                <td><?= $data->status ?></td>
            </tr>   
    <?php

            }
        }
    ?>
</table>

<?php
include $project_root.'components/footer.php';
?>
