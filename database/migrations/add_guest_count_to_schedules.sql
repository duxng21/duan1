-- Add guest count columns to tour_schedules
ALTER TABLE tour_schedules 
ADD COLUMN num_adults INT DEFAULT 0 AFTER max_participants,
ADD COLUMN num_children INT DEFAULT 0 AFTER num_adults,
ADD COLUMN num_infants INT DEFAULT 0 AFTER num_children;

-- Optional: Add index for guest queries
CREATE INDEX idx_schedule_guests ON tour_schedules(num_adults, num_children);
