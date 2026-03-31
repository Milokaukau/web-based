<?php
$project_root = $_SERVER['DOCUMENT_ROOT']."/";
require $project_root."config.php";
require $project_root."logic/order_listing.php";

$_title = 'Order Management';

include $project_root.'components/header.php';
?>

<div class="container admin-container">
    
    <div class="admin-header">
        <h1 class="admin-title">Orders</h1>
        
        <form method="GET" class="admin-filter-form">
            <select name="member_id" class="status-select" style="min-width: 200px;">
                <option value="" disabled <?= !isset($_GET['member_id']) ? 'selected' : '' ?>>Select a Member...</option>
                
                <?php foreach ($members as $member): ?>
                    <option value="<?= $member->id ?>" <?= (isset($_GET['member_id']) && $_GET['member_id'] == $member->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($member->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn btn-filter">Filter</button>
            
            <?php if (isset($_GET['member_id'])): ?>
                <a href="order_listing.php" class="btn btn-clear">Clear Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total Amount</th>
                    <th>Member</th>
                    <th>Status</th>
                    <th class="action-col">Action</th> 
                </tr>
            </thead>
            <tbody>
                <?php if ($arr): ?>
                    <?php foreach ($arr as $data): ?>  
                        <tr>
                            <td style="font-weight: 700;">#<?= htmlspecialchars($data->order_id) ?></td>
                            <td><?= htmlspecialchars($data->product_name) ?></td>
                            <td><?= htmlspecialchars($data->quantity) ?></td>
                            <td style="font-weight: 600;">$<?= htmlspecialchars($data->amount) ?></td>
                            <td><?= htmlspecialchars($data->member_name) ?></td>
                            
                            <td>
                                <select name="status" class="status-select" form="update-form-<?= $data->order_id ?>">
                                    <option value="processing" <?= ($data->status === 'processing') ? 'selected' : '' ?>>Processing</option>
                                    <option value="success" <?= ($data->status === 'success') ? 'selected' : '' ?>>Success</option>
                                    <option value="failed" <?= ($data->status === 'failed') ? 'selected' : '' ?>>Failed</option>
                                </select>
                            </td>

                            <td class="action-col">
                                <form method="POST" action="order_listing.php" id="update-form-<?= $data->order_id ?>">
                                    <input type="hidden" name="payment_id" value="<?= $data->payment_id ?>">
                                    <button type="submit" class="btn btn-save">Save</button>
                                </form>
                            </td>

                        </tr>   
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            No orders found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php
include $project_root.'components/footer.php';
?>