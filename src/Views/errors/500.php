<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Internal Server Error</title>
    <style>
        body { margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f8fafc; color: #334155; text-align: center; }
        h1 { font-size: 4rem; margin: 0; color: #ef4444; }
        p { font-size: 1.25rem; margin-top: 1rem; margin-bottom: 2rem; }
        a { color: #fff; background-color: #3b82f6; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 6px; font-weight: 500; transition: background-color 0.2s; }
        a:hover { background-color: #2563eb; }
    </style>
</head>
<body>
    <div>
        <h1>500</h1>
        <p><?= htmlspecialchars($message ?? 'An error occurred on our server.') ?></p>
        <a href="/">Back to Home</a>
    </div>
</body>
</html>
