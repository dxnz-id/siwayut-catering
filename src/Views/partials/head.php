<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= \App\Core\View::e($title ?? 'Siwayut Catering') ?><?= isset($titleSuffix) ? ' — ' . \App\Core\View::e($titleSuffix) : '' ?></title>
<link rel="stylesheet" href="/assets/css/fonts.css">
<link rel="stylesheet" href="/assets/css/app.css?v=2">
<link rel="icon" type="image/svg+xml" href="/assets/icon/favicon.svg">
<?= $headExtra ?? '' ?>
