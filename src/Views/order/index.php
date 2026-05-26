<div class="content-header">
    <h1 class="content-title"><?= htmlspecialchars($title ?? 'Orders') ?></h1>
    <a href="/orders/create" class="btn btn-primary">Create Order</a>
</div>

<div class="card">
    <?php if (empty($orders)): ?>
    <div class="empty-state">
        <p>No orders found.</p>
    </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Menu</th>
                    <th>Event Date</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td>
                        <div style="font-weight: 500;"><?= htmlspecialchars($customerMap[$order['customer_id']]['name'] ?? 'Unknown') ?></div>
                        <div style="font-size: 0.8125rem; color: var(--color-text-muted);"><?= htmlspecialchars($customerMap[$order['customer_id']]['phone'] ?? '-') ?></div>
                    </td>
                    <td><?= htmlspecialchars($menuMap[$order['menu_id']] ?? 'Unknown') ?></td>
                    <td><?= date('d M Y H:i', strtotime($order['event_date'])) ?></td>
                    <td><?= number_format((float)$order['quantity'], 0, ',', '.') ?> portions</td>
                    <td style="font-weight: 500; color: var(--color-success);">Rp <?= number_format((float)$order['total_price'], 0, ',', '.') ?></td>
                    <td>
                        <?php
                        $statusColors = [
                            'pending' => 'var(--color-warning)',
                            'processing' => 'var(--color-primary)',
                            'delivering' => 'var(--color-primary)',
                            'completed' => 'var(--color-success)',
                            'cancelled' => 'var(--color-danger)',
                        ];
                        $colorClass = $statusColors[$order['status']] ?? 'var(--color-text-muted)';
                        ?>
                        <span class="badge" style="background: <?= $colorClass ?>; color: white; text-transform: uppercase;">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="/orders/<?= $order['id'] ?>/edit" class="btn btn-secondary btn-sm">Update Status</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (($pagination['last_page'] ?? 1) > 1): ?>
    <div class="pagination">
        <div class="pagination-info">
            Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (Total: <?= (int)$pagination['total'] ?> orders)
        </div>
        <div class="pagination-links">
            <a href="?page=<?= max(1, $pagination['current_page'] - 1) ?>" class="pagination-link<?= $pagination['current_page'] <= 1 ? ' disabled' : '' ?>">&laquo; Prev</a>
            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>" class="pagination-link<?= $i === $pagination['current_page'] ? ' active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?page=<?= min($pagination['last_page'], $pagination['current_page'] + 1) ?>" class="pagination-link<?= $pagination['current_page'] >= $pagination['last_page'] ? ' disabled' : '' ?>">Next &raquo;</a>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
