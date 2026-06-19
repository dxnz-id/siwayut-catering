<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Core\Request;
use App\Services\OrderService;

class ReportController extends BaseController
{
    public function __construct(
        private OrderService $orderService
    ) {
        parent::__construct();
    }

    public function revenue(Request $request): void
    {
        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-t'));
        $sortBy = $request->input('sort_by', 'date');
        $dir = $request->input('dir', 'ASC');
        $page = (int) $request->input('page', 1);

        $result = $this->orderService->getRevenueByPeriod($dateFrom, $dateTo, $sortBy, $dir, $page);

        $this->render('report/revenue', [
            'title' => __('revenue_report'),
            'rows' => $result['rows'],
            'totals' => $result['totals'],
            'pagination' => $result['pagination'],
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sort_by' => $sortBy,
            'dir' => $dir,
        ]);
    }

    public function menuRevenue(Request $request): void
    {
        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-t'));
        $menus = $this->orderService->getTopMenus(100, $dateFrom, $dateTo);

        $this->render('report/menu-revenue', [
            'title' => __('menu_revenue'),
            'menus' => $menus,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function exportCsv(Request $request): void
    {
        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-t'));
        $result = $this->orderService->getRevenueByPeriod($dateFrom, $dateTo);

        ob_clean();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="revenue-export.csv"');

        $out = fopen('php://output', 'w');

        fputcsv($out, [__('revenue_report')], escape: "\\");
        fputcsv($out, ['Period: ' . $dateFrom . ' — ' . $dateTo], escape: "\\");
        fputcsv($out, [], escape: "\\");

        fputcsv($out, [__('date'), __('orders'), __('revenue'), __('profit')], escape: "\\");

        foreach ($result['rows'] as $row) {
            fputcsv($out, [
                $row['date'],
                (int) $row['orders'],
                number_format((float) $row['revenue'], 0, ',', '.'),
                number_format((float) $row['profit'], 0, ',', '.'),
            ], escape: "\\");
        }

        fputcsv($out, [
            __('total'),
            (int) ($result['totals']['orders'] ?? 0),
            number_format((float) ($result['totals']['revenue'] ?? 0), 0, ',', '.'),
            number_format((float) ($result['totals']['profit'] ?? 0), 0, ',', '.'),
        ], escape: "\\");

        fclose($out);
        exit;
    }

    public function exportMenuRevenueCsv(Request $request): void
    {
        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-t'));
        $menus = $this->orderService->getTopMenus(100, $dateFrom, $dateTo);

        ob_clean();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="menu-revenue-export.csv"');

        $out = fopen('php://output', 'w');

        fputcsv($out, [__('menu_revenue')], escape: "\\");
        fputcsv($out, [], escape: "\\");

        fputcsv($out, [
            __('menu'), __('price'), __('cost_price'), __('total_qty'),
            __('revenue'), __('profit'), __('profit_margin'),
        ], escape: "\\");

        foreach ($menus as $m) {
            $margin = (float) $m['total_revenue'] > 0
                ? ((float) $m['total_revenue'] - (float) $m['total_cost']) / (float) $m['total_revenue'] * 100
                : 0;

            fputcsv($out, [
                $m['name'] ?? '',
                number_format((float) ($m['price'] ?? 0), 0, ',', '.'),
                number_format((float) ($m['cost_price'] ?? 0), 0, ',', '.'),
                (int) ($m['total_qty'] ?? 0),
                number_format((float) ($m['total_revenue'] ?? 0), 0, ',', '.'),
                number_format((float) (($m['total_revenue'] ?? 0) - ($m['total_cost'] ?? 0)), 0, ',', '.'),
                number_format($margin, 1) . '%',
            ], escape: "\\");
        }

        fclose($out);
        exit;
    }
}
