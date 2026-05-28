<?php
$type = $type ?? 'text';
$value = $value ?? old($name);
$required = $required ?? false;
$placeholder = $placeholder ?? '';
$help_text = $help_text ?? '';
$err = error($name);
$isInvalid = $err ? ' border-danger ring-1 ring-danger/30 focus:border-danger focus:ring-danger/30' : '';

$accept = $accept ?? 'image/jpeg,image/png,image/webp';
$max_size = $max_size ?? (5 * 1024 * 1024);

$formats = array_map(function ($t) {
    return strtoupper(str_replace('image/', '', trim($t)));
}, explode(',', $accept));
$formatLabel = implode(', ', $formats);
$maxSizeLabel = $max_size >= 1048576
    ? (round($max_size / 1048576, 1) . ' MB')
    : (round($max_size / 1024, 1) . ' KB');

if ($type === 'file' && !$help_text) {
    $help_text = 'Supported: ' . $formatLabel . '. Max ' . $maxSizeLabel . '.';
}
?>
<div class="mb-5">
    <label class="block text-sm font-medium text-text mb-1.5" for="<?= e($name) ?>"><?= e($label) ?><?= $required ? ' <span class="text-danger">*</span>' : '' ?></label>

    <?php if ($type === 'file'): ?>
    <div class="file-upload-zone<?= $err ? ' has-error' : '' ?>"
         data-accept="<?= e($accept) ?>"
         data-max-size="<?= e($max_size) ?>">
        <input type="file" id="<?= e($name) ?>" name="<?= e($name) ?>"
               class="file-upload-input"
               accept="<?= e($accept) ?>"
               <?= $required ? 'required' : '' ?>>

        <div class="file-upload-placeholder">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            <p>Drag &amp; drop here, or <span>browse</span></p>
            <small class="text-[0.75rem] text-muted mt-1"><?= e($formatLabel) ?> &mdash; Max <?= e($maxSizeLabel) ?></small>
        </div>

        <div class="file-upload-preview">
            <img class="file-upload-thumb" src="" alt="Preview">
            <div class="file-upload-details">
                <span class="file-upload-name"></span>
                <div class="file-upload-meta">
                    <span class="file-upload-type"></span>
                    <span class="file-upload-size"></span>
                </div>
            </div>
            <button type="button" class="file-upload-remove inline-flex items-center justify-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-danger text-white border-danger hover:bg-danger-hover hover:border-danger-hover hover:text-white">Remove</button>
        </div>

        <div class="file-upload-error"></div>
    </div>

    <?php elseif ($type === 'number'): ?>
    <input type="<?= e($type) ?>" id="<?= e($name) ?>" name="<?= e($name) ?>" class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light<?= $isInvalid ?>" value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?> <?= isset($min) ? 'min="'.e($min).'"' : '' ?> <?= isset($step) ? 'step="'.e($step).'"' : '' ?>>

    <?php else: ?>
    <input type="<?= e($type) ?>" id="<?= e($name) ?>" name="<?= e($name) ?>" class="w-full px-3 py-2 border border-border rounded-lg text-sm leading-relaxed text-text bg-black/40 font-body focus:outline-none focus:border-primary focus:ring-3 focus:ring-primary-light<?= $isInvalid ?>" value="<?= e($value) ?>" placeholder="<?= e($placeholder) ?>" <?= $required ? 'required' : '' ?>>
    <?php endif; ?>

    <?php if ($help_text && $type !== 'file'): ?>
    <small class="text-[0.8125rem] text-muted"><?= e($help_text) ?></small>
    <?php endif; ?>
    <?php if ($err): ?>
    <div class="text-danger text-[0.8125rem] mt-1"><?= e($err) ?></div>
    <?php endif; ?>
</div>
