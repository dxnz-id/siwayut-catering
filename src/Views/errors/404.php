<!DOCTYPE html>
<html lang="en">

<head>
    <?php $title = '404 Not Found'; require __DIR__ . '/../partials/head.php' ?>
</head>

<body
    class="bg-[#09090b] text-[#f4f4f5] font-body flex flex-col justify-center items-center min-h-screen text-center m-0">
    <h1 class="text-8xl font-extrabold font-display text-[#e58e26] m-0 leading-none">404</h1>
    <p class="text-lg mt-4 mb-8 text-[#a1a1aa]">
        <?= e($message ?? __('not_found_message')) ?>
    </p>
</body>

</html>