<?php
class Report
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // Tổng hợp tài chính theo tour
    // filters: from_date (Y-m-d), to_date (Y-m-d), category_id
    public function getTourFinanceSummary($filters = [])
    {
        $whereTs = [];
        $paramsTs = [];

        if (!empty($filters['from_date'])) {
            $whereTs[] = 'ts.departure_date >= ?';
            $paramsTs[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $whereTs[] = 'ts.departure_date <= ?';
            $paramsTs[] = $filters['to_date'];
        }
        if (!empty($filters['category_id'])) {
            $whereTs[] = 't.category_id = ?';
            $paramsTs[] = (int) $filters['category_id'];
        }

        $whereTsSql = count($whereTs) ? ('WHERE ' . implode(' AND ', $whereTs)) : '';

        // Subquery schedules filtered by date/category to anchor costs and revenue
        // Revenue: sum bookings.total_amount where b.tour_id = t.tour_id and (if possible) b.tour_date matches a filtered schedule date
        // Cost services: sum(quantity*unit_price) from schedule_services joined to filtered schedules
        // Cost staff: sum(salary_paid+bonus) from staff_tour_history joined to filtered schedules
        // Build complete subquery WHERE clauses
        $whereRev = $whereTsSql ?: 'WHERE 1=1';
        $whereServ = $whereTsSql ?: 'WHERE 1=1';
        $whereStaff = $whereTsSql ?: 'WHERE 1=1';

        $sql = "
            SELECT 
                t.tour_id,
                t.tour_name,
                tc.category_name,
                COALESCE(rev.total_revenue, 0) AS revenue,
                COALESCE(cost_serv.total_service_cost, 0) AS service_cost,
                COALESCE(cost_staff.total_staff_cost, 0) AS staff_cost,
                COALESCE(rev.total_revenue, 0) - (COALESCE(cost_serv.total_service_cost, 0) + COALESCE(cost_staff.total_staff_cost, 0)) AS profit,
                CASE WHEN COALESCE(rev.total_revenue,0) > 0 
                    THEN ROUND((COALESCE(rev.total_revenue,0) - (COALESCE(cost_serv.total_service_cost,0) + COALESCE(cost_staff.total_staff_cost,0))) / COALESCE(rev.total_revenue,1) * 100, 2)
                    ELSE 0 END AS margin_percent
            FROM tours t
            LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
            LEFT JOIN (
                SELECT ts.tour_id, SUM(b.total_amount) AS total_revenue
                FROM tour_schedules ts
                LEFT JOIN bookings b 
                    ON b.tour_id = ts.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                $whereRev
                GROUP BY ts.tour_id
            ) rev ON rev.tour_id = t.tour_id
            LEFT JOIN (
                SELECT ts.tour_id, SUM(ss.quantity * ss.unit_price) AS total_service_cost
                FROM tour_schedules ts
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                $whereServ
                GROUP BY ts.tour_id
            ) cost_serv ON cost_serv.tour_id = t.tour_id
            LEFT JOIN (
                SELECT ts.tour_id, SUM(COALESCE(sth.salary_paid,0) + COALESCE(sth.bonus,0)) AS total_staff_cost
                FROM tour_schedules ts
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                $whereStaff
                GROUP BY ts.tour_id
            ) cost_staff ON cost_staff.tour_id = t.tour_id
            WHERE 1=1
        ";

        $params = array_merge($paramsTs, $paramsTs, $paramsTs);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
