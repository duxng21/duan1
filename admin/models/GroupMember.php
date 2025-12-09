<?php
// admin/models/GroupMember.php
class GroupMember
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function getByScheduleId($schedule_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM schedule_group_members WHERE schedule_id = ? ORDER BY member_id ASC");
        $stmt->execute([$schedule_id]);
        return $stmt->fetchAll();
    }
}
