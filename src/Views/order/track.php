<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Track Order — Siwayut Catering') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg-dark: #09090b;
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.08);
            --accent-gold: #e58e26;
            --accent-gold-glow: rgba(229, 142, 38, 0.3);
            --accent-red: #ea2027;
            --text-light: #f4f4f5;
            --text-muted: #a1a1aa;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: radial-gradient(circle at 15% 25%, rgba(229, 142, 38, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(234, 32, 39, 0.08) 0%, transparent 45%),
                var(--bg-dark);
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        .logo-text {
            font-family: 'Outfit', sans-serif;
        }

        .wrapper {
            max-width: 540px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        header {
            background: rgba(9, 9, 11, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 0;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-light);
        }

        .logo-icon {
            font-size: 1.5rem;
            filter: drop-shadow(0 0 8px var(--accent-gold-glow));
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, var(--accent-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            color: var(--text-light);
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: var(--accent-gold);
            border-color: var(--accent-gold);
            box-shadow: 0 0 15px var(--accent-gold-glow);
        }

        .track-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(16px) saturate(120%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
        }

        .track-card h1 {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .track-card p {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: var(--text-muted);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px var(--accent-gold-glow);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.2);
        }

        .btn-search {
            width: 100%;
            padding: 0.85rem;
            background: var(--accent-gold);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px var(--accent-gold-glow);
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px var(--accent-gold-glow);
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .alert-error {
            background: rgba(234, 32, 39, 0.1);
            border: 1px solid rgba(234, 32, 39, 0.2);
            color: #f87171;
        }

        .footer-text {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 2rem;
            padding-bottom: 2rem;
        }

        .footer-text a {
            color: var(--accent-gold);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .track-card {
                padding: 1.5rem 1.25rem;
            }

            .track-card h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="wrapper nav-container">
            <a href="/" class="logo">
                <span class="logo-icon">🍲</span>
                <span class="logo-text">Siwayut Catering</span>
            </a>
            <a href="/" class="btn-outline">&larr; Home</a>
        </div>
    </header>
    <main class="wrapper">
        <div class="track-card">
            <h1>Track Order</h1>
            <p>Enter your order number and phone number to check the latest status.</p>

            <?php $flashError = \App\Core\Session::getFlash('error'); ?>
            <?php if ($flashError): ?>
                <div class="alert alert-error"><?= \App\Core\View::e($flashError) ?></div>
            <?php endif; ?>

            <form action="/track-order" method="POST">
                <?= \App\Core\Csrf::field() ?>
                <div class="form-group">
                    <label for="order_id">Order Number</label>
                    <input type="text" id="order_id" name="order_id" class="form-input" placeholder="E.g. 1"
                        value="<?= \App\Core\View::e(old('order_id')) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number (when ordering)</label>
                    <input type="tel" id="phone" name="phone" class="form-input" placeholder="E.g. 08123456789"
                        value="<?= \App\Core\View::e(old('phone')) ?>" required>
                </div>
                <button type="submit" class="btn-search">Search Order</button>
            </form>
        </div>
        <div class="footer-text">
            <a href="/">Siwayut Catering</a> — Exquisite Taste For Your Most Sacred Moments
        </div>
    </main>
</body>

</html>