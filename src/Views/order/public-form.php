<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Order Catering — Siwayut Catering') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #09090b;
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.08);
            --accent-gold: #e58e26;
            --accent-gold-glow: rgba(229, 142, 38, 0.3);
            --text-light: #f4f4f5;
            --text-muted: #a1a1aa;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: radial-gradient(circle at 15% 25%, rgba(229, 142, 38, 0.12) 0%, transparent 45%),
                        radial-gradient(circle at 85% 75%, rgba(234, 32, 39, 0.08) 0%, transparent 45%),
                        var(--bg-dark);
            color: var(--text-light);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            line-height: 1.6;
        }
        h1, h2, h3, .logo-text { font-family: 'Outfit', sans-serif; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 0 1.5rem; }
        header {
            background: rgba(9, 9, 11, 0.6);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 0;
        }
        .nav-container { display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--text-light); }
        .logo-icon { font-size: 1.5rem; filter: drop-shadow(0 0 8px var(--accent-gold-glow)); }
        .logo-text {
            font-size: 1.25rem; font-weight: 700; letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, var(--accent-gold) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
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
        .btn-outline:hover { background: var(--accent-gold); border-color: var(--accent-gold); box-shadow: 0 0 15px var(--accent-gold-glow); }
        .form-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(16px) saturate(120%);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            margin-top: 2.5rem;
        }
        .form-card h1 {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .form-card .subtitle {
            text-align: center;
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
            color: var(--text-muted);
        }
        .form-input, .form-select, .form-textarea {
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
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px var(--accent-gold-glow);
        }
        .form-input::placeholder, .form-textarea::placeholder { color: rgba(255, 255, 255, 0.2); }
        .form-select option { background: #1a1a1e; color: var(--text-light); }
        .form-textarea { min-height: 100px; resize: vertical; }
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: #25D366;
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 0 20px rgba(37, 211, 102, 0.3); }
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
        .footer-text a { color: var(--accent-gold); text-decoration: none; }
        @media (max-width: 768px) {
            .form-card { padding: 1.5rem 1.25rem; }
            .form-card h1 { font-size: 1.4rem; }
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
        <div class="form-card">
            <h1>📝 Order Catering</h1>
            <p class="subtitle">Fill out the form below, then your order will be sent to us via WhatsApp.</p>

            <?php $flashError = \App\Core\Session::getFlash('error'); ?>
            <?php if ($flashError): ?>
            <div class="alert alert-error"><?= \App\Core\View::e($flashError) ?></div>
            <?php endif; ?>

            <form action="/order-form" method="POST">
                <?= \App\Core\Csrf::field() ?>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Enter your name" value="<?= \App\Core\View::e(old('name')) ?>" required>
                </div>

                <div class="form-group">
                    <label for="menu">Menu</label>
                    <select id="menu" name="menu" class="form-select" required>
                        <option value="">— Select Menu —</option>
                        <?php foreach ($menus as $m): ?>
                        <option value="<?= \App\Core\View::e($m['name']) ?>" <?= old('menu') === $m['name'] ? 'selected' : '' ?>>
                            <?= \App\Core\View::e($m['name']) ?> — Rp <?= number_format((float)$m['price'], 0, ',', '.') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="event_date">Event Date</label>
                    <input type="datetime-local" id="event_date" name="event_date" class="form-input" value="<?= \App\Core\View::e(old('event_date')) ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Portions</label>
                    <input type="number" id="quantity" name="quantity" class="form-input" placeholder="E.g. 50" min="1" value="<?= \App\Core\View::e(old('quantity')) ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" name="address" class="form-textarea" placeholder="Enter your complete delivery address" required><?= \App\Core\View::e(old('address')) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">Notes <span style="color: var(--text-muted);">(optional)</span></label>
                    <textarea id="notes" name="notes" class="form-textarea" placeholder="E.g. additional requests, delivery time, etc."><?= \App\Core\View::e(old('notes')) ?></textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Send Order via WhatsApp
                </button>
            </form>
        </div>
        <div class="footer-text">
            <a href="/">Siwayut Catering</a> — Exquisite Taste For Your Most Sacred Moments
        </div>
    </main>
</body>
</html>
