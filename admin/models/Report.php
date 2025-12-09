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

        // Category filter will be applied to main query, not subqueries
        $whereTsSql = count($whereTs) ? ('WHERE ' . implode(' AND ', $whereTs)) : '';

        // Subquery schedules filtered by date to anchor costs and revenue
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

        // Apply category filter to main query
        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = ?";
        }

        // Build params: 3 copies for subqueries, then category_id for main query
        $params = array_merge($paramsTs, $paramsTs, $paramsTs);
        if (!empty($filters['category_id'])) {
            $params[] = (int) $filters['category_id'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết tài chính của một tour cụ thể
     */
    public function getTourFinanceDetail($tour_id, $filters = [])
    {
        $where = ['ts.tour_id = ?'];
        $params = [$tour_id];

        if (!empty($filters['from_date'])) {
            $where[] = 'ts.departure_date >= ?';
            $params[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[] = 'ts.departure_date <= ?';
            $params[] = $filters['to_date'];
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT 
                    ts.schedule_id,
                    ts.departure_date,
                    ts.return_date,
                    ts.status,
                    ts.max_participants,
                    ts.current_participants,
                    COALESCE(SUM(b.total_amount), 0) AS revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS service_cost,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS staff_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS profit
                FROM tour_schedules ts
                LEFT JOIN bookings b ON b.tour_id = ts.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                WHERE $whereSql
                GROUP BY ts.schedule_id
                ORDER BY ts.departure_date DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Thống kê doanh thu theo tháng
     */
    public function getMonthlyRevenue($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $sql = "SELECT 
                    MONTH(ts.departure_date) AS month,
                    COUNT(DISTINCT ts.schedule_id) AS total_schedules,
                    COUNT(DISTINCT b.booking_id) AS total_bookings,
                    COALESCE(SUM(b.total_amount), 0) AS total_revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS total_service_cost,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS total_staff_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS profit
                FROM tour_schedules ts
                LEFT JOIN bookings b ON b.tour_id = ts.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                WHERE YEAR(ts.departure_date) = ?
                GROUP BY MONTH(ts.departure_date)
                ORDER BY month";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$year]);
        return $stmt->fetchAll();
    }

    /**
     * So sánh doanh thu giữa hai kỳ
     */
    public function compareRevenue($period1_start, $period1_end, $period2_start, $period2_end)
    {
        $sql = "SELECT 
                    'Period 1' AS period,
                    COUNT(DISTINCT b.booking_id) AS total_bookings,
                    COALESCE(SUM(b.total_amount), 0) AS revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS service_cost,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS staff_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS profit
                FROM tour_schedules ts
                LEFT JOIN bookings b ON b.tour_id = ts.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                WHERE ts.departure_date BETWEEN ? AND ?
                UNION ALL
                SELECT 
                    'Period 2' AS period,
                    COUNT(DISTINCT b.booking_id) AS total_bookings,
                    COALESCE(SUM(b.total_amount), 0) AS revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS service_cost,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS staff_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS profit
                FROM tour_schedules ts
                LEFT JOIN bookings b ON b.tour_id = ts.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                WHERE ts.departure_date BETWEEN ? AND ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$period1_start, $period1_end, $period2_start, $period2_end]);
        return $stmt->fetchAll();
    }

    /**
     * Dashboard KPIs
     */
    public function getDashboardKPIs($filters = [])
    {
        $where = [];
        $params = [];

        if (!empty($filters['from_date'])) {
            $where[] = 'ts.departure_date >= ?';
            $params[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[] = 'ts.departure_date <= ?';
            $params[] = $filters['to_date'];
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "SELECT 
                    COUNT(DISTINCT ts.schedule_id) AS total_schedules,
                    COUNT(DISTINCT b.booking_id) AS total_bookings,
                    COUNT(DISTINCT b.customer_id) AS total_customers,
                    SUM(b.num_adults + b.num_children + b.num_infants) AS total_guests,
                    COALESCE(SUM(b.total_amount), 0) AS total_revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS total_service_cost,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS total_staff_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS total_profit,
                    CASE WHEN COALESCE(SUM(b.total_amount), 0) > 0
                        THEN ROUND((COALESCE(SUM(b.total_amount), 0) - 
                                   COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                                   COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) / 
                                   COALESCE(SUM(b.total_amount), 1) * 100, 2)
                        ELSE 0 END AS profit_margin,
                    CASE WHEN COUNT(DISTINCT b.booking_id) > 0
                        THEN ROUND(COALESCE(SUM(b.total_amount), 0) / COUNT(DISTINCT b.booking_id), 0)
                        ELSE 0 END AS avg_booking_value
                FROM tour_schedules ts
                LEFT JOIN bookings b ON b.tour_id = ts.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                $whereSql";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Top tours theo doanh thu
     */
    public function getTopToursByRevenue($limit = 10, $filters = [])
    {
        $where = [];
        $params = [];

        if (!empty($filters['from_date'])) {
            $where[] = 'ts.departure_date >= ?';
            $params[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[] = 'ts.departure_date <= ?';
            $params[] = $filters['to_date'];
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "SELECT 
                    t.tour_id,
                    t.tour_name,
                    t.code,
                    tc.category_name,
                    COUNT(DISTINCT ts.schedule_id) AS total_schedules,
                    COUNT(DISTINCT b.booking_id) AS total_bookings,
                    COALESCE(SUM(b.total_amount), 0) AS total_revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS total_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS profit
                FROM tours t
                LEFT JOIN tour_categories tc ON t.category_id = tc.category_id
                LEFT JOIN tour_schedules ts ON ts.tour_id = t.tour_id
                LEFT JOIN bookings b ON b.tour_id = t.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                $whereSql
                GROUP BY t.tour_id
                ORDER BY total_revenue DESC
                LIMIT ?";

        $params[] = $limit;
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Phân tích chi phí theo loại
     */
    public function getCostBreakdown($filters = [])
    {
        $where = [];
        $params = [];

        if (!empty($filters['from_date'])) {
            $where[] = 'ts.departure_date >= ?';
            $params[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[] = 'ts.departure_date <= ?';
            $params[] = $filters['to_date'];
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "SELECT 
                    'Dịch vụ' AS cost_type,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS total_cost
                FROM tour_schedules ts
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                $whereSql
                UNION ALL
                SELECT 
                    'Nhân sự' AS cost_type,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS total_cost
                FROM tour_schedules ts
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                $whereSql";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array_merge($params, $params));
        return $stmt->fetchAll();
    }

    /**
     * Doanh thu theo danh mục tour
     */
    public function getRevenueByCategory($filters = [])
    {
        $where = [];
        $params = [];

        if (!empty($filters['from_date'])) {
            $where[] = 'ts.departure_date >= ?';
            $params[] = $filters['from_date'];
        }
        if (!empty($filters['to_date'])) {
            $where[] = 'ts.departure_date <= ?';
            $params[] = $filters['to_date'];
        }

        $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "SELECT 
                    tc.category_name,
                    COUNT(DISTINCT t.tour_id) AS total_tours,
                    COUNT(DISTINCT ts.schedule_id) AS total_schedules,
                    COUNT(DISTINCT b.booking_id) AS total_bookings,
                    COALESCE(SUM(b.total_amount), 0) AS total_revenue,
                    COALESCE(SUM(ss.quantity * ss.unit_price), 0) AS total_service_cost,
                    COALESCE(SUM(sth.salary_paid + sth.bonus), 0) AS total_staff_cost,
                    (COALESCE(SUM(b.total_amount), 0) - 
                     COALESCE(SUM(ss.quantity * ss.unit_price), 0) - 
                     COALESCE(SUM(sth.salary_paid + sth.bonus), 0)) AS profit
                FROM tour_categories tc
                LEFT JOIN tours t ON t.category_id = tc.category_id
                LEFT JOIN tour_schedules ts ON ts.tour_id = t.tour_id
                LEFT JOIN bookings b ON b.tour_id = t.tour_id 
                    AND (b.tour_date IS NULL OR b.tour_date = ts.departure_date)
                LEFT JOIN schedule_services ss ON ss.schedule_id = ts.schedule_id
                LEFT JOIN staff_tour_history sth ON sth.schedule_id = ts.schedule_id
                $whereSql
                GROUP BY tc.category_id
                HAVING total_revenue > 0
                ORDER BY total_revenue DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
