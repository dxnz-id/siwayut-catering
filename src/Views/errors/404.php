<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link rel="stylesheet" href="/assets/css/fonts.css">
    <link rel="stylesheet" href="/assets/css/app.css?v=2">
    <link rel="icon" type="image/svg+xml" href="/assets/icon/favicon.svg">
</head>

<body
    class="bg-[#09090b] text-[#f4f4f5] font-body flex flex-col justify-center items-center min-h-screen text-center m-0">
    <h1 class="text-8xl font-extrabold font-display text-[#e58e26] m-0 leading-none">404</h1>
    <p class="text-lg mt-4 mb-8 text-[#a1a1aa]">
        <?= e($message ?? __('not_found_message')) ?>
    </p>
</body>

</html>