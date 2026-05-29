<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold font-display text-text"><?= htmlspecialchars($title ?? 'Orders') ?></h1>
    <a href="#" onclick="openCreateModal('createOrderModal');return false" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Create Order</a>
</div>

<!-- Search & Filter -->
<div class="mb-4">
    <form method="GET" class="flex items-center gap-3 relative">
        <input type="hidden" name="page" value="1">
        <div class="relative flex-1">
            <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="Search order ID, customer, phone, menu, status..." class="w-full px-3 py-2 pl-10 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted pointer-events-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
        </div>
        <button type="button" onclick="this.nextElementSibling.classList.toggle('hidden')" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-border bg-black/40 text-muted hover:text-text hover:border-primary transition-all duration-150 shrink-0" title="Filters">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
        </button>
        <div class="hidden p-4 bg-[#18181b] border border-border rounded-xl absolute mt-2 right-0 top-full z-10 min-w-[320px] shadow-lg">
            <div class="flex items-end gap-3 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Status</label>
                    <select name="status" class="px-3 py-2 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-w-[140px]">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($filters['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="delivering" <?= ($filters['status'] ?? '') === 'delivering' ? 'selected' : '' ?>>Delivering</option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted mb-1">Payment</label>
                    <select name="payment_status" class="px-3 py-2 border border-border rounded-lg text-sm text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light min-w-[140px]">
                        <option value="">All Payments</option>
                        <option value="unpaid" <?= ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                        <option value="paid" <?= ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="refunded" <?= ($filters['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-primary text-white border-primary hover:bg-primary-hover hover:border-primary-hover hover:shadow-[0_0_15px_var(--color-gold-glow)] hover:text-white">Apply</button>
            </div>
        </div>
    </form>
</div>

<div id="table-container" class="bg-[#18181b] border border-border rounded-xl overflow-hidden">
    <?php if (empty($orders)): ?>
    <div class="col-span-full bg-card-bg border border-dashed border-border rounded-[20px] px-6 py-12 text-center text-muted">
        <p>No orders found.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr>
<?php
$s = $sort_by ?? 'created_at';
$d = $dir ?? 'DESC';
$sortUrl = function($col) use ($s, $d) {
    $next = ($s === $col && $d === 'asc') ? 'desc' : 'asc';
    return '?' . http_build_query(array_merge($_GET, ['sort_by' => $col, 'dir' => $next]));
};
$sortIcon = function($col) use ($s, $d) {
    if ($s !== $col) return '<span class="opacity-0 group-hover:opacity-40 transition-opacity"><svg class="inline align-middle" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m8 10 4-4 4 4"/><path d="m8 14 4 4 4-4"/></svg></span>';
    $arrow = $d === 'asc' ? 'm8 10 4-4 4 4' : 'm8 14 4 4 4-4';
    return '<span class="text-gold"><svg class="inline align-middle" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="' . $arrow . '"/></svg></span>';
};
?>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('id') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">ID<?= $sortIcon('id') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('customer_name') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Customer<?= $sortIcon('customer_name') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('items_count') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Items<?= $sortIcon('items_count') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('event_date') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Event Date<?= $sortIcon('event_date') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('total_price') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Total Price<?= $sortIcon('total_price') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('status') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Status<?= $sortIcon('status') ?></a></th>
                    <th class="bg-black/30 text-left text-xs font-semibold uppercase tracking-wider text-muted border-b border-border"><a href="<?= $sortUrl('payment_status') ?>" class="flex items-center gap-1 px-4 py-3 group text-muted hover:text-gold transition-colors no-underline">Payment<?= $sortIcon('payment_status') ?></a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr class="cursor-pointer hover:bg-white/[0.03]" onclick="location.href='/orders/<?= (int)$order['id'] ?>'">
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= $order['id'] ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <div class="font-medium"><?= e($order['customer_name'] ?? 'Unknown') ?></div>
                        <div class="text-[0.8125rem] text-muted"><?= e($order['customer_phone'] ?? '-') ?></div>
                    </td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= (int)($order['item_cnt'] ?? 0) ?> item<?= ((int)($order['item_cnt'] ?? 0) !== 1) ? 's' : '' ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text"><?= date('d M Y H:i', strtotime($order['event_date'])) ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text font-medium text-success">Rp <?= number_format((float)$order['total_price'], 0, ',', '.') ?></td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase" style="background: <?= $colorClass ?>; color: white;">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-sm border-b border-white/[0.06] align-middle text-text">
                        <?php
                        $paymentColors = [
                            'unpaid' => 'var(--color-warning)',
                            'paid' => 'var(--color-success)',
                            'refunded' => 'var(--color-danger)',
                        ];
                        $paymentColor = $paymentColors[$order['payment_status']] ?? 'var(--color-text-muted)';
                        $paymentLabels = [
                            'unpaid' => 'Unpaid',
                            'paid' => 'Paid',
                            'refunded' => 'Refunded',
                        ];
                        ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase" style="background: <?= $paymentColor ?>; color: white;">
                            <?= htmlspecialchars($paymentLabels[$order['payment_status']] ?? $order['payment_status']) ?>
                        </span>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (($pagination['last_page'] ?? 1) > 1): ?>
    <div class="flex items-center justify-between px-6 py-4 border-t border-border">
        <div class="text-[0.8125rem] text-muted">
            Showing page <?= (int)$pagination['current_page'] ?> of <?= (int)$pagination['last_page'] ?> (Total: <?= (int)$pagination['total'] ?> orders)
        </div>
        <div class="flex gap-1">
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $pagination['current_page'] - 1)])) ?>" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $pagination['current_page'] <= 1 ? ' opacity-50 pointer-events-none' : '' ?>">&laquo; Prev</a>
            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $i === $pagination['current_page'] ? ' bg-primary text-white border-primary' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($pagination['last_page'], $pagination['current_page'] + 1)])) ?>" class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 rounded-lg text-[0.8125rem] font-medium text-muted border border-border hover:bg-white/5 hover:text-text<?= $pagination['current_page'] >= $pagination['last_page'] ? ' opacity-50 pointer-events-none' : '' ?>">Next &raquo;</a>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php
$createModalId = 'createOrderModal';
$createTitle = 'Create New Order';
$createAction = '/orders';
$createSubmitText = 'Process Order';
ob_start();
echo '<h4 class="mb-4 pb-2 border-b border-border font-display font-semibold text-text" style="color:var(--color-text)">Customer Details</h4>';
echo '<div class="grid grid-cols-2 gap-4">';
component('form/input', ['name' => 'phone', 'label' => 'Phone Number (Member ID)', 'placeholder' => '08123456789', 'required' => true, 'help_text' => 'If registered, name & address will be updated.']);
component('form/input', ['name' => 'customer_name', 'label' => 'Customer Name', 'required' => true]);
echo '</div>';
component('form/input', ['name' => 'delivery_address', 'label' => 'Delivery Address', 'required' => true]);
component('form/select', ['name' => 'event_id', 'label' => 'Event', 'options' => array_column($events ?? [], 'name', 'id'), 'required' => true]);
echo '<h4 class="mt-6 mb-4 pb-2 border-b border-border font-display font-semibold text-text" style="color:var(--color-text)">Menu Items</h4>';
echo '<div id="menu-items-container" class="flex flex-col gap-3">';
echo '    <div class="menu-item-row flex items-start gap-2" data-index="0">';
echo '        <div class="flex-1">';
$menuOpts = ['' => '-- Select Menu --'];
foreach ($menus as $m) { $menuOpts[$m['id']] = $m['name'] . ' (Rp ' . number_format((float)$m['price'], 0, ',', '.') . ')'; }
component('form/select', ['name' => 'items[0][menu_id]', 'label' => 'Menu', 'options' => $menuOpts, 'required' => true]);
echo '        </div>';
echo '        <div class="w-28 shrink-0">';
component('form/input', ['name' => 'items[0][quantity]', 'label' => 'Qty', 'type' => 'number', 'value' => '1', 'min' => '1', 'required' => true]);
echo '        </div>';
echo '        <button type="button" class="remove-menu-item mt-6 w-9 h-9 flex items-center justify-center rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all duration-150 cursor-pointer border-0 bg-transparent shrink-0 hidden" data-index="0" title="Remove">';
echo '            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>';
echo '        </button>';
echo '    </div>';
echo '</div>';
echo '<button type="button" id="add-menu-item" class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-[0.8125rem] rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-white/6 text-text border-border hover:bg-white/10 hover:text-text mt-2">+ Add Another Menu</button>';
echo '<div class="grid grid-cols-2 gap-4 mt-4">';
component('form/input', ['name' => 'event_date', 'label' => 'Event Date & Time', 'type' => 'datetime-local', 'required' => true]);
component('form/input', ['name' => 'notes', 'label' => 'Additional Notes', 'placeholder' => 'e.g. Less spicy']);
echo '</div>';
$createFormContent = ob_get_clean();
require __DIR__ . '/../partials/create-modal.php';
?>
<script>
<?php $menuJson = json_encode(array_map(function($m) {
    return ['id' => $m['id'], 'name' => $m['name'], 'price' => $m['price']];
}, $menus)); ?>
(function() {
    'use strict';
    var container = document.getElementById('menu-items-container');
    var addBtn = document.getElementById('add-menu-item');
    if (!container || !addBtn) return;

    var menuList = <?= $menuJson ?>;

    function buildSelect(index) {
        var html = '<select name="items[' + index + '][menu_id]" required class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">';
        html += '<option value="">-- Select Menu --</option>';
        for (var i = 0; i < menuList.length; i++) {
            var m = menuList[i];
            var price = 'Rp ' + Number(m.price).toLocaleString('id-ID');
            html += '<option value="' + m.id + '">' + m.name + ' (' + price + ')</option>';
        }
        html += '</select>';
        return html;
    }

    function updateIndices() {
        var rows = container.querySelectorAll('.menu-item-row');
        rows.forEach(function(row, i) {
            row.dataset.index = i;
            var selects = row.querySelectorAll('select, input');
            selects.forEach(function(el) {
                el.name = el.name.replace(/\[\d+\]/, '[' + i + ']');
            });
            var removeBtn = row.querySelector('.remove-menu-item');
            if (removeBtn) {
                removeBtn.dataset.index = i;
                removeBtn.classList.toggle('hidden', rows.length <= 1);
            }
        });
    }

    addBtn.addEventListener('click', function() {
        var rows = container.querySelectorAll('.menu-item-row');
        var newIndex = rows.length;
        var div = document.createElement('div');
        div.className = 'menu-item-row flex items-start gap-2';
        div.dataset.index = newIndex;
        div.innerHTML =
            '<div class="flex-1"><label class="block text-sm font-medium text-text mb-1.5">Menu <span class="text-danger">*</span></label>' +
            buildSelect(newIndex) +
            '</div>' +
            '<div class="w-28 shrink-0"><label class="block text-sm font-medium text-text mb-1.5">Qty <span class="text-danger">*</span></label>' +
            '<input type="number" name="items[' + newIndex + '][quantity]" value="1" min="1" required class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light">' +
            '</div>' +
            '<button type="button" class="remove-menu-item mt-6 w-9 h-9 flex items-center justify-center rounded-lg text-muted hover:text-danger hover:bg-danger/10 transition-all duration-150 cursor-pointer border-0 bg-transparent shrink-0" data-index="' + newIndex + '" title="Remove">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg></button>';
        container.appendChild(div);
        updateIndices();
    });

    container.addEventListener('click', function(e) {
        var btn = e.target.closest('.remove-menu-item');
        if (!btn) return;
        var row = btn.closest('.menu-item-row');
        if (row) {
            row.remove();
            updateIndices();
        }
    });

    // Patch resetModal to also reset menu items to single row
    var origReset = window.resetModal;
    if (typeof origReset === 'function') {
        window.resetModal = function(modalId) {
            origReset(modalId);
            // Reset menu items back to a single row
            var rows = container.querySelectorAll('.menu-item-row');
            while (rows.length > 1) {
                rows[rows.length - 1].remove();
                rows = container.querySelectorAll('.menu-item-row');
            }
            updateIndices();
        };
    }
})();
</script>
