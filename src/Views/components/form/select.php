<?php
$selected = $selected ?? old($name);
$required = $required ?? false;
$err = error($name);
$isInvalid = $err ? ' is-invalid' : '';
?>
<div class="form-group">
    <label class="form-label" for="<?= e($name) ?>"><?= e($label) ?></label>
    <select id="<?= e($name) ?>" name="<?= e($name) ?>" class="form-select<?= $isInvalid ?>" <?= $required ? 'required' : '' ?>>
        <?php if (!empty($placeholder)): ?>
        <option value=""><?= e($placeholder) ?></option>
        <?php endif; ?>
        <?php foreach ($options as $val => $text): ?>
            <?php 
                // Handle both associative arrays [ 'admin' => 'Admin' ] and arrays of arrays [ ['value' => '1', 'label' => 'A'] ]
                $optVal = is_array($text) ? $text['value'] : $val;
                $optText = is_array($text) ? $text['label'] : $text;
                $isSelected = (string)$selected === (string)$optVal ? 'selected' : '';
            ?>
            <option value="<?= e($optVal) ?>" <?= $isSelected ?>><?= e($optText) ?></option>
        <?php endforeach; ?>
    </select>
    <?php if ($err): ?>
    <div class="form-error"><?= e($err) ?></div>
    <?php endif; ?>
</div>
