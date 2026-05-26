<?php
$value = $value ?? old($name);
$required = $required ?? false;
$rows = $rows ?? 3;
$placeholder = $placeholder ?? '';
$err = error($name);
$isInvalid = $err ? ' is-invalid' : '';
?>
<div class="form-group">
    <label class="form-label" for="<?= e($name) ?>"><?= e($label) ?></label>
    <textarea id="<?= e($name) ?>" name="<?= e($name) ?>" class="form-input<?= $isInvalid ?>" rows="<?= (int)$rows ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>><?= e($value) ?></textarea>
    <?php if ($err): ?>
    <div class="form-error"><?= e($err) ?></div>
    <?php endif; ?>
</div>
