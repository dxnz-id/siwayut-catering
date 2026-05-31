<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\{Request, Session};

class LangController
{
    private const SUPPORTED = ['id', 'en'];

    public function switch(Request $request): void
    {
        $locale = $request->param('locale');

        if (in_array($locale, self::SUPPORTED, true)) {
            Session::set('locale', $locale);
        }

        // Redirect back to referring page, or home if no referrer
        $referrer = $_SERVER['HTTP_REFERER'] ?? '/';
        $host = parse_url($referrer, PHP_URL_HOST);
        if ($host && $host !== ($_SERVER['HTTP_HOST'] ?? '')) {
            $referrer = '/';
        }
        header('Location: ' . $referrer);
        exit;
    }
}
