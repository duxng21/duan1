ALTER TABLE tour_schedules
    ADD COLUMN customer_name VARCHAR(255) NULL AFTER meeting_time,
    ADD COLUMN customer_phone VARCHAR(50) NULL AFTER customer_name,
    ADD COLUMN customer_email VARCHAR(255) NULL AFTER customer_phone;
