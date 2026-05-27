<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::e($title ?? 'Siwayut Catering') ?></title>
    <!-- Google Fonts: Inter & Outfit -->
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
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .logo-text {
            font-family: 'Outfit', sans-serif;
        }

        /* Container */
        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Glass Navbar */
        header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(9, 9, 11, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
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
            font-size: 1.8rem;
            filter: drop-shadow(0 0 8px var(--accent-gold-glow));
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, var(--accent-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-login {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            color: var(--text-light);
            padding: 0.6rem 1.5rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
        }

        .btn-login:hover {
            background: var(--accent-gold);
            border-color: var(--accent-gold);
            box-shadow: 0 0 15px var(--accent-gold-glow);
            transform: translateY(-2px);
        }

        .hero-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
            margin-top: 2.5rem;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem 2.2rem;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 210px;
        }

        .hero-btn-primary {
            background: var(--accent-gold);
            border: 1px solid var(--accent-gold);
            color: #fff;
            box-shadow: 0 0 15px var(--accent-gold-glow);
        }

        .hero-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px var(--accent-gold-glow);
        }

        .hero-btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            color: var(--text-light);
            backdrop-filter: blur(8px);
        }

        .hero-btn-outline:hover {
            background: transparent;
            border-color: var(--accent-gold);
            box-shadow: 0 0 15px var(--accent-gold-glow);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            padding: 5rem 0 3rem 0;
            text-align: center;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(229, 142, 38, 0.1);
            border: 1px solid rgba(229, 142, 38, 0.2);
            color: var(--accent-gold);
            padding: 0.4rem 1.2rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, #ffffff, #f4f4f5, var(--accent-gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto 2.5rem auto;
            line-height: 1.8;
        }

        /* Glassmorphism Section Cards */
        .section-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 0.8rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            position: relative;
        }

        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -0.9rem;
            left: 0;
            width: 50px;
            height: 2px;
            background: var(--accent-gold);
            box-shadow: 0 0 8px var(--accent-gold);
        }

        /* Active Events Carousel/Grid */
        .grid-events {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
            margin-bottom: 4rem;
        }

        .event-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(16px) saturate(120%);
            -webkit-backdrop-filter: blur(16px) saturate(120%);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--accent-gold);
            box-shadow: 0 0 10px var(--accent-gold);
        }

        .event-card:hover {
            transform: translateY(-5px);
            border-color: rgba(229, 142, 38, 0.3);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 0 15px rgba(229, 142, 38, 0.1);
        }

        .event-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .event-name {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: #fff;
        }

        .event-date {
            font-size: 0.88rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .event-date-icon {
            color: var(--accent-gold);
        }

        /* Menus Grid */
        .grid-menus {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 5rem;
        }

        .menu-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            border-color: rgba(229, 142, 38, 0.25);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .menu-img-container {
            height: 180px;
            background: linear-gradient(135deg, rgba(229, 142, 38, 0.2) 0%, rgba(234, 32, 39, 0.1) 100%);
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            color: rgba(255, 255, 255, 0.15);
        }

        .menu-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-tag {
            position: absolute;
            bottom: 0.8rem;
            left: 0.8rem;
            background: rgba(9, 9, 11, 0.8);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(6px);
            color: var(--accent-gold);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
        }

        .menu-body {
            padding: 1.2rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .menu-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .menu-desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 1.2rem;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--card-border);
            padding-top: 0.8rem;
            margin-top: auto;
        }

        .menu-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent-gold);
        }

        .menu-portions {
            font-size: 0.78rem;
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.05);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            border: 1px solid var(--card-border);
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            background: var(--card-bg);
            border: 1px dashed var(--card-border);
            border-radius: 20px;
            padding: 3rem 1.5rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--card-border);
            padding: 2.5rem 0;
            background: rgba(9, 9, 11, 0.4);
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        footer p {
            margin-bottom: 0.5rem;
        }

        /* Responsive Utilities */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.3rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .grid-events,
            .grid-menus {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- Header Navigation -->
    <header>
        <div class="wrapper nav-container">
            <a href="/" class="logo">
                <span class="logo-icon">🍲</span>
                <span class="logo-text">Siwayut Catering</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="wrapper">

        <!-- Hero Section -->
        <section class="hero">
            <span class="hero-badge">✨ Premium Holiday Catering</span>
            <h1>Exquisite Taste<br>For Your Most Sacred Moments</h1>
            <p>Siwayut Catering provides exclusive catering menus specially crafted to celebrate your holidays. Enjoy
                delicious dishes without the hassle together with your loved ones.</p>
            <div class="hero-buttons">
                <a href="/order-form" class="hero-btn hero-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="currentColor" style="flex-shrink: 0;">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    Order Now
                </a>
                <a href="/track-order" class="hero-btn hero-btn-outline">Track Order</a>
            </div>
        </section>

        <?php
        // Map categories for efficient O(1) rendering lookup
        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['id']] = $cat['name'];
        }

        // Map events for O(1) lookup
        $eventMap = [];
        foreach ($events as $ev) {
            $eventMap[$ev['id']] = $ev['name'];
        }
        ?>

        <!-- Active Events (Hari Raya) -->
        <section style="margin-top: 2rem;">
            <div class="section-header">
                <h2>Active Events</h2>
            </div>
            <div class="grid-events">
                <?php if (empty($events)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📅</div>
                        <p>No active events at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <span class="event-status">Open for Orders</span>
                            <h3 class="event-name"><?= \App\Core\View::e($event['name']) ?></h3>
                            <div class="event-date">
                                <span class="event-date-icon">🗓️</span>
                                <span>
                                    <?= date('d M Y', strtotime($event['start_date'])) ?>
                                    -
                                    <?= date('d M Y', strtotime($event['end_date'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Featured Menus -->
        <section>
            <div class="section-header">
                <h2>Featured Holiday Menu</h2>
            </div>
            <div class="grid-menus">
                <?php if (empty($menus)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🍽️</div>
                        <p>No menu items available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($menus as $menu): ?>
                        <?php if (($menu['status'] ?? 'active') === 'active'): ?>
                            <div class="menu-card">
                                <div class="menu-img-container">
                                    <?php if ($menu['image']): ?>
                                        <img src="<?= str_starts_with($menu['image'], 'http') ? \App\Core\View::e($menu['image']) : '/uploads/' . \App\Core\View::e($menu['image']) ?>"
                                            alt="<?= \App\Core\View::e($menu['name']) ?>" class="menu-img">
                                    <?php else: ?>
                                        <span style="font-size: 3.5rem;">🍱</span>
                                    <?php endif; ?>

                                    <!-- Event Tag -->
                                    <?php if (isset($eventMap[$menu['event_id']])): ?>
                                        <span class="menu-tag"><?= \App\Core\View::e($eventMap[$menu['event_id']]) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="menu-body">
                                    <h3 class="menu-title"><?= \App\Core\View::e($menu['name']) ?></h3>
                                    <p class="menu-desc"><?= \App\Core\View::e($menu['description']) ?></p>

                                    <div class="menu-meta">
                                        <span class="menu-price">Rp <?= number_format((float) $menu['price'], 0, ',', '.') ?></span>
                                        <span class="menu-portions">Min. <?= (int) $menu['minimum_portions'] ?> Portions</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer>
        <div class="wrapper">
            <p>&copy; <?= date('Y') ?> Siwayut Catering. All rights reserved.</p>
            <!-- <p style="font-size: 0.78rem; opacity: 0.6;">Powered by Vanilla PHP MVC Framework</p> -->
        </div>
    </footer>

</body>

</html>