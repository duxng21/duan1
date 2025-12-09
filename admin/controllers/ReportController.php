<?php
class ReportController
{
    protected $reportModel;
    protected $tourModel;

    public function __construct()
    {
        requireRole('ADMIN');
        $this->reportModel = new Report();
        $this->tourModel = new Tour();
    }

    public function TourFinanceReport()
    {
        $filters = [
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'category_id' => $_GET['category_id'] ?? ''
        ];
        $categories = $this->tourModel->getCategories();
        $rows = $this->reportModel->getTourFinanceSummary($filters);
        require_once __DIR__ . '/../views/schedule/report_finance.php';
    }

    public function ExportTourFinance()
    {
        $filters = [
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'category_id' => $_GET['category_id'] ?? ''
        ];
        $format = strtolower($_GET['format'] ?? 'csv');
        $rows = $this->reportModel->getTourFinanceSummary($filters);

        if ($format === 'csv' || $format === 'excel') {
            $filename = 'tour_finance_' . date('Ymd_His') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Tour ID', 'Tour', 'Danh mục', 'Doanh thu', 'Chi phí dịch vụ', 'Chi phí nhân sự', 'Lợi nhuận', 'Tỷ lệ (%)']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['tour_id'],
                    $r['tour_name'],
                    $r['category_name'],
                    (float) $r['revenue'],
                    (float) $r['service_cost'],
                    (float) $r['staff_cost'],
                    (float) $r['profit'],
                    (float) $r['margin_percent']
                ]);
            }
            fclose($out);
            logUserActivity('export_report', 'report', null, 'Export tour finance CSV');
            exit();
        }

        // print-friendly HTML (save as PDF via browser)
        $rowsData = $rows;
        logUserActivity('export_report', 'report', null, 'Open print view tour finance');
        require_once __DIR__ . '/../views/schedule/report_finance_print.php';
    }

    /**
     * Dashboard tổng quan tài chính
     */
    public function FinancialDashboard()
    {
        requireRole('ADMIN');

        $filters = [
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'), // First day of month
            'to_date' => $_GET['to_date'] ?? date('Y-m-t') // Last day of month
        ];

        // KPIs
        $kpis = $this->reportModel->getDashboardKPIs($filters);

        // Monthly revenue (current year)
        $monthlyRevenue = $this->reportModel->getMonthlyRevenue(date('Y'));

        // Top tours
        $topTours = $this->reportModel->getTopToursByRevenue(10, $filters);

        // Revenue by category
        $revenueByCategory = $this->reportModel->getRevenueByCategory($filters);

        // Cost breakdown
        $costBreakdown = $this->reportModel->getCostBreakdown($filters);

        require_once __DIR__ . '/../views/report/financial_dashboard.php';
    }

    /**
     * Báo cáo chi tiết tour cụ thể
     */
    public function TourDetailReport()
    {
        requireRole('ADMIN');

        $tour_id = $_GET['tour_id'] ?? 0;
        if (!$tour_id) {
            $_SESSION['error'] = 'Vui lòng chọn tour!';
            header('Location: ?act=bao-cao-tour');
            exit();
        }

        $filters = [
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? ''
        ];

        $tour = $this->tourModel->getById($tour_id);
        $schedules = $this->reportModel->getTourFinanceDetail($tour_id, $filters);

        require_once __DIR__ . '/../views/report/tour_detail_report.php';
    }

    /**
     * Báo cáo so sánh giữa các kỳ
     */
    public function ComparisonReport()
    {
        requireRole('ADMIN');

        $period1_start = $_GET['period1_start'] ?? date('Y-m-01', strtotime('last month'));
        $period1_end = $_GET['period1_end'] ?? date('Y-m-t', strtotime('last month'));
        $period2_start = $_GET['period2_start'] ?? date('Y-m-01');
        $period2_end = $_GET['period2_end'] ?? date('Y-m-t');

        $comparison = $this->reportModel->compareRevenue(
            $period1_start,
            $period1_end,
            $period2_start,
            $period2_end
        );

        require_once __DIR__ . '/../views/report/comparison_report.php';
    }

    /**
     * Báo cáo doanh thu theo tháng
     */
    public function MonthlyReport()
    {
        requireRole('ADMIN');

        $year = $_GET['year'] ?? date('Y');
        $monthlyData = $this->reportModel->getMonthlyRevenue($year);

        require_once __DIR__ . '/../views/report/monthly_report.php';
    }

    /**
     * Export báo cáo dashboard
     */
    public function ExportDashboard()
    {
        requireRole('ADMIN');

        $filters = [
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'),
            'to_date' => $_GET['to_date'] ?? date('Y-m-t')
        ];

        $format = $_GET['format'] ?? 'csv';
        $kpis = $this->reportModel->getDashboardKPIs($filters);
        $topTours = $this->reportModel->getTopToursByRevenue(10, $filters);

        if ($format === 'csv') {
            $filename = 'financial_dashboard_' . date('Ymd_His') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename=' . $filename);
            $out = fopen('php://output', 'w');

            // KPIs section
            fputcsv($out, ['TỔNG QUAN TÀI CHÍNH']);
            fputcsv($out, ['Từ ngày', $filters['from_date'], 'Đến ngày', $filters['to_date']]);
            fputcsv($out, []);
            fputcsv($out, ['Tổng lịch khởi hành', $kpis['total_schedules']]);
            fputcsv($out, ['Tổng booking', $kpis['total_bookings']]);
            fputcsv($out, ['Tổng khách hàng', $kpis['total_customers']]);
            fputcsv($out, ['Tổng số khách', $kpis['total_guests']]);
            fputcsv($out, ['Tổng doanh thu', number_format($kpis['total_revenue'], 0)]);
            fputcsv($out, ['Tổng chi phí', number_format($kpis['total_service_cost'] + $kpis['total_staff_cost'], 0)]);
            fputcsv($out, ['Lợi nhuận', number_format($kpis['total_profit'], 0)]);
            fputcsv($out, ['Tỷ suất lợi nhuận', $kpis['profit_margin'] . '%']);
            fputcsv($out, []);

            // Top tours section
            fputcsv($out, ['TOP 10 TOUR THEO DOANH THU']);
            fputcsv($out, ['Tour', 'Danh mục', 'Số đoàn', 'Số booking', 'Doanh thu', 'Chi phí', 'Lợi nhuận']);
            foreach ($topTours as $tour) {
                fputcsv($out, [
                    $tour['tour_name'],
                    $tour['category_name'],
                    $tour['total_schedules'],
                    $tour['total_bookings'],
                    number_format($tour['total_revenue'], 0),
                    number_format($tour['total_cost'], 0),
                    number_format($tour['profit'], 0)
                ]);
            }

            fclose($out);
            exit();
        }
    }

    /**
     * Báo cáo doanh thu từ bookings (theo tháng, theo tour)
     */
    public function revenueReport()
    {
        requireRole('ADMIN');

        // Lấy filter từ GET
        $filters = [
            'from_date' => $_GET['from_date'] ?? '',
            'to_date' => $_GET['to_date'] ?? '',
            'tour_id' => $_GET['tour_id'] ?? ''
        ];

        require_once __DIR__ . '/../models/Booking.php';
        $bookingModel = new Booking();
        $monthlyReport = $bookingModel->getRevenueReport($filters);
        $tourReport = $bookingModel->getRevenueByTour($filters['from_date'], $filters['to_date']);

        require_once __DIR__ . '/../views/report/revenue_report.php';
    }
}
