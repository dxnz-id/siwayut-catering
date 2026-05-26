<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($title ?? 'Update Order Status') ?></h1>
    <a href="/orders" class="btn btn-secondary">&larr; Back to Orders</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-body">
            <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-border);">Order Details #<?= $order['id'] ?></h4>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 0.5rem; font-size: 0.875rem;">
                <div style="font-weight: 500;">Customer Name</div>
                <div><?= htmlspecialchars($customer['name'] ?? '-') ?></div>
                <div style="font-weight: 500;">Phone</div>
                <div><?= htmlspecialchars($customer['phone'] ?? '-') ?></div>
                <div style="font-weight: 500;">Menu</div>
                <div><?= htmlspecialchars($menu['name'] ?? '-') ?></div>
                <div style="font-weight: 500;">Quantity</div>
                <div><?= $order['quantity'] ?> portions</div>
                <div style="font-weight: 500;">Total Price</div>
                <div style="color: var(--color-success); font-weight: 600;">Rp <?= number_format((float)$order['total_price'], 0, ',', '.') ?></div>
                <div style="font-weight: 500;">Event Date</div>
                <div><?= date('d F Y, H:i', strtotime($order['event_date'])) ?></div>
                <div style="font-weight: 500;">Delivery Address</div>
                <div><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></div>
                <div style="font-weight: 500;">Notes</div>
                <div><?= htmlspecialchars($order['notes'] ?: '-') ?></div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <h4 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-border);">Update Status</h4>
            <form action="/orders/<?= $order['id'] ?>" method="POST">
                <?= \App\Core\Csrf::field() ?>
                
                <?php component('form/select', [
                    'name' => 'status',
                    'label' => 'Order Status',
                    'options' => [
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'delivering' => 'Delivering',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled'
                    ],
                    'selected' => old('status', $order['status']),
                    'required' => true
                ]); ?>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
