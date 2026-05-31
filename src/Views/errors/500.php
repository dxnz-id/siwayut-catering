<!DOCTYPE html>
<html lang="en">

<head>
    <?php $title = '500 Internal Server Error'; require __DIR__ . '/../partials/head.php' ?>
</head>

<body class="bg-[#09090b] text-[#f4f4f5] font-body flex justify-center items-center min-h-screen text-center m-0">
    <div>
        <h1 class="text-8xl font-extrabold font-display text-[#ef4444] m-0 leading-none">500</h1>
        <p class="text-lg mt-4 mb-8 text-[#a1a1aa]">
            <?= e($message ?? __('server_error_message')) ?>
        </p>
        <a href="/"
            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg text-sm font-medium leading-tight cursor-pointer border transition-all duration-150 no-underline whitespace-nowrap font-body hover:translate-y-[-1px] hover:shadow-md active:translate-y-0 bg-[#e58e26] text-white border-[#e58e26] hover:bg-[#c97d20] hover:border-[#c97d20] hover:shadow-[0_0_15px_rgba(229,142,38,0.3)] hover:text-white"><?= __('back_to_home') ?></a>
    </div>
</body>

</html>