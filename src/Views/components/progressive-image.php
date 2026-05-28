<?php
$alt = $alt ?? '';
$class = $class ?? '';
$style = $style ?? '';
$src = $src ?? '';

if ($src === '' || $src === null) {
    return;
}

if (str_starts_with($src, 'http')) {
    echo '<img src="' . e($src) . '" alt="' . e($alt) . '" class="' . e($class) . '" style="' . $style . '">';
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
    style="display:inline-block;overflow:hidden;line-height:0;vertical-align:top;<?= $wrapStyle ?>">
    <img src="<?= $thumb ?>" data-full="<?= $full ?>" alt="<?= e($alt) ?>" class="progressive-img blur-up"
        style="display:block;width:100%;height:100%;object-fit:cover"
        onerror="this.onerror=null;this.src='<?= $full ?>';this.classList.remove('blur-up');this.classList.add('loaded')">
</span>