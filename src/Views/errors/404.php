<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=2">
</head>

<body
    class="bg-[#09090b] text-[#f4f4f5] font-body flex flex-col justify-center items-center min-h-screen text-center m-0">
    <h1 class="text-8xl font-extrabold font-display text-[#e58e26] m-0 leading-none">404</h1>
    <p class="text-lg mt-4 mb-8 text-[#a1a1aa]">
        <?= htmlspecialchars($message ?? 'The page you are looking for was not found.') ?>
    </p>
</body>

</html>