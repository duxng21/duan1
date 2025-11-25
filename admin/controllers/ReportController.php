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
}
