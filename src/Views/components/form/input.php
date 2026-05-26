<?php
$type = $type ?? 'text';
$value = $value ?? old($name);
$required = $required ?? false;
$placeholder = $placeholder ?? '';
$help_text = $help_text ?? '';
$err = error($name);
$isInvalid = $err ? ' is-invalid' : '';
?>
<div class="form-group">
    <label class="form-label" for="<?= e($name) ?>"><?= e($label) ?></label>
    <?php if ($type === 'number'): ?>
    <input type="<?= e($type) ?>" id="<?= e($name) ?>" name="<?= e($name) ?>" class="form-input<?= $isInvalid ?>" value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?> <?= isset($min) ? 'min="'.e($min).'"' : '' ?> <?= isset($step) ? 'step="'.e($step).'"' : '' ?>>
    <?php else: ?>
    <input type="<?= e($type) ?>" id="<?= e($name) ?>" name="<?= e($name) ?>" class="form-input<?= $isInvalid ?>" value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>>
    <?php endif; ?>
    <?php if ($help_text): ?>
    <small style="color: var(--color-text-muted); font-size: 0.8125rem;"><?= e($help_text) ?></small>
    <?php endif; ?>
    <?php if ($err): ?>
    <div class="form-error"><?= e($err) ?></div>
    <?php endif; ?>
</div>
