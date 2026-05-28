<?php
$value = $value ?? old($name);
$required = $required ?? false;
$rows = $rows ?? 3;
$placeholder = $placeholder ?? '';
$err = error($name);
$isInvalid = $err ? ' border-danger ring-1 ring-danger/30 focus:border-danger focus:ring-danger/30' : '';
?>
<div class="mb-5">
    <label class="block text-sm font-medium text-text mb-1.5" for="<?= e($name) ?>"><?= e($label) ?><?= $required ? ' <span class="text-danger">*</span>' : '' ?></label>
    <textarea id="<?= e($name) ?>" name="<?= e($name) ?>" class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light<?= $isInvalid ?>" rows="<?= (int)$rows ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>><?= e($value) ?></textarea>
    <?php if ($err): ?>
    <div class="text-danger text-[0.8125rem] mt-1"><?= e($err) ?></div>
    <?php endif; ?>
</div>
