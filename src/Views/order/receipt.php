<!DOCTYPE html>
<html lang="<?= str_replace('_', '-', \App\Core\Lang::locale()) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= __('receipt') ?> — <?= e($order['order_number']) ?></title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Courier New', monospace; font-size: 13px; color: #000; background: #fff; padding: 30px 20px; max-width: 360px; margin: 0 auto; }
h1 { font-size: 18px; text-align: center; margin-bottom: 4px; }
h2 { font-size: 14px; text-align: center; margin-bottom: 20px; color: #555; }
.divider { border-top: 1px dashed #333; margin: 12px 0; }
.row { display: flex; justify-content: space-between; padding: 2px 0; }
.row-label { font-weight: bold; }
.row-value { text-align: right; }
table { width: 100%; border-collapse: collapse; margin: 8px 0; }
th { text-align: left; font-size: 12px; border-bottom: 1px solid #333; padding: 4px 0; }
td { padding: 4px 0; }
td:last-child, th:last-child { text-align: right; }
.total-row { font-weight: bold; border-top: 1px dashed #333; padding-top: 4px; margin-top: 4px; }
.text-right { text-align: right; }
.mb-2 { margin-bottom: 8px; }
.text-center { text-align: center; }
.footer { text-align: center; margin-top: 16px; font-size: 11px; color: #888; }
@media print { body { padding: 20px; } .no-print { display: none; } }
</style>
</head>
<body>
    <h1><?= __('siwayut_catering') ?></h1>
    <h2><?= __('receipt') ?></h2>

    <div class="row"><span class="row-label"><?= __('order_no') ?></span><span class="row-value"><?= e($order['order_number']) ?></span></div>
    <?php if (!empty($order['invoice_number'])): ?>
    <div class="row"><span class="row-label"><?= __('invoice') ?></span><span class="row-value"><?= e($order['invoice_number']) ?></span></div>
    <?php endif; ?>
    <div class="row"><span class="row-label"><?= __('date') ?></span><span class="row-value"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span></div>
    <div class="row"><span class="row-label"><?= __('customer') ?></span><span class="row-value"><?= e($customer['name'] ?? '-') ?></span></div>
    <?php if ($customer['phone'] ?? null): ?>
    <div class="row"><span class="row-label"><?= __('phone') ?></span><span class="row-value"><?= e($customer['phone']) ?></span></div>
    <?php endif; ?>
    <div class="row"><span class="row-label"><?= __('event_date') ?></span><span class="row-value"><?= e(date('d/m/Y', strtotime($order['event_date'])) . (!empty($order['event_time']) ? ' ' . date('H:i', strtotime($order['event_time'])) : '')) ?></span></div>
    <?php if ($order['occasion']): ?>
    <div class="row"><span class="row-label"><?= __('occasion') ?></span><span class="row-value"><?= e($order['occasion']) ?></span></div>
    <?php endif; ?>

    <div class="divider"></div>

    <table>
        <thead>
            <tr><th><?= __('menu') ?></th><th><?= __('qty') ?></th><th><?= __('price') ?></th><th><?= __('subtotal') ?></th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['menu_name']) ?></td>
                <td class="text-right"><?= (int)$item['quantity'] ?>x</td>
                <td class="text-right"><?= number_format((float)$item['price_at_time'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format((float)$item['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="row"><span class="row-label"><?= __('subtotal') ?></span><span class="row-value">Rp <?= number_format((float)$order['total_price'], 0, ',', '.') ?></span></div>
    <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
    <div class="row"><span class="row-label"><?= __('discount') ?><?php if (($order['discount_type'] ?? '') === 'percentage'): ?> (<?= (float)$order['discount_value'] ?>%)<?php endif; ?></span><span class="row-value">- Rp <?= number_format((float)$order['discount_amount'], 0, ',', '.') ?></span></div>
    <?php endif; ?>
    <?php if ((float)($order['tax_amount'] ?? 0) > 0): ?>
    <div class="row"><span class="row-label"><?= __('tax') ?> (<?= (float)$order['tax_rate'] ?>%)</span><span class="row-value">Rp <?= number_format((float)$order['tax_amount'], 0, ',', '.') ?></span></div>
    <?php endif; ?>
    <div class="row total-row"><span class="row-label"><?= __('grand_total') ?></span><span class="row-value">Rp <?= number_format((float)($order['grand_total'] ?? $order['total_price']), 0, ',', '.') ?></span></div>
    <?php if ((float)($order['down_payment'] ?? 0) > 0): ?>
    <div class="row"><span class="row-label"><?= __('down_payment') ?></span><span class="row-value">Rp <?= number_format((float)$order['down_payment'], 0, ',', '.') ?></span></div>
    <div class="row"><span class="row-label"><?= __('remaining_balance') ?></span><span class="row-value">Rp <?= number_format((float)$order['remaining_balance'], 0, ',', '.') ?></span></div>
    <?php endif; ?>

    <div class="row" style="margin-top:8px"><span class="row-label"><?= __('payment_status') ?></span><span class="row-value"><?= __($order['payment_status']) ?></span></div>
    <div class="row"><span class="row-label"><?= __('payment_method') ?></span><span class="row-value"><?= __($order['payment_method'] ?? 'cash') ?></span></div>
    <?php if (!empty($order['paid_at'])): ?>
    <div class="row"><span class="row-label"><?= __('paid_at') ?></span><span class="row-value"><?= date('d/m/Y H:i', strtotime($order['paid_at'])) ?></span></div>
    <?php endif; ?>

    <?php if ($order['delivery_address']): ?>
    <div class="divider"></div>
    <div class="row-label mb-2"><?= __('delivery_address') ?></div>
    <div style="font-size:12px"><?= nl2br(e($order['delivery_address'])) ?></div>
    <?php endif; ?>

    <div class="footer">
        <p><?= __('receipt_footer') ?></p>
    </div>

    <div class="text-center no-print" style="margin-top:20px">
        <button onclick="window.print()" style="padding:8px 20px;font-size:14px;border:1px solid #333;background:#fff;cursor:pointer"><?= __('print') ?></button>
        <button onclick="window.close()" style="padding:8px 20px;font-size:14px;border:1px solid #333;background:#fff;cursor:pointer;margin-left:8px"><?= __('close') ?></button>
    </div>
</body>
</html>
