<?php
$alt = $alt ?? '';
$class = $class ?? '';
$style = $style ?? '';
$src = $src ?? '';

if ($src === '' || $src === null) {
    return;
}

if (str_starts_with($src, 'http')) {
    echo '<img src="' . e($src) . '" alt="' . e($alt) . '" class="' . e($class) . '" style="' . e($style) . '">';
    return;
}

$full = '/uploads/' . e($src);
$dir = dirname($src);
$base = basename($src);
$thumbDir = $dir !== '.' ? $dir . '/thumbs' : 'thumbs';
$thumb = '/uploads/' . $thumbDir . '/' . $base;

$wrapStyle = preg_replace('/object-fit\s*:\s*[^;]+;?\s*/', '', $style);
?>
<span class="progressive-wrap <?= e($class) ?>"
    style="display:inline-block;overflow:hidden;line-height:0;vertical-align:top;background:rgba(255,255,255,0.04) url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' viewBox='0 0 24 24' stroke='rgba(255,255,255,0.2)' stroke-width='1.5'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z'/%3E%3C/svg%3E&quot;) center/20px no-repeat;<?= e($wrapStyle) ?>">
    <img data-thumb="<?= $thumb ?>" data-full="<?= $full ?>" alt="<?= e($alt) ?>" class="progressive-img"
        style="display:block;width:100%;height:100%;object-fit:cover;opacity:0">
</span>