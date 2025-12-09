<?php
/**
 * Script chạy database migrations
 * Truy cập: http://localhost/duan1/admin/run_migrations.php
 */

// Require database connection
require_once '../commons/env.php';
require_once '../commons/function.php';

$conn = connectDB();

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Run Database Migrations</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .success { color: green; padding: 10px; background: #e8f5e9; border-left: 4px solid green; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #ffebee; border-left: 4px solid red; margin: 10px 0; }
        .info { color: #0066cc; padding: 10px; background: #e3f2fd; border-left: 4px solid #0066cc; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px 10px 0; }
        .btn:hover { background: #45a049; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔧 Database Migrations</h1>
";

// Migration 1: Tour Pricing & Suppliers
echo "<h2>Migration 1: Tour Pricing & Suppliers</h2>";
$migration1 = file_get_contents(__DIR__ . '/../database/migrations/tour_pricing_suppliers.sql');

if ($migration1) {
    try {
        // Split by semicolon and execute each statement
        $statements = explode(';', $migration1);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                // Check if error is "table already exists"
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "<div class='info'>⚠️ Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 1 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: tour_pricing_suppliers.sql</div>";
}

// Migration 2: Quote Enhancement
echo "<h2>Migration 2: Quote Enhancement</h2>";
$migration2 = file_get_contents(__DIR__ . '/../database/migrations/quote_enhancement.sql');

if ($migration2) {
    try {
        $statements = explode(';', $migration2);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                // Check if error is "duplicate column" or "already exists"
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 2 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: quote_enhancement.sql</div>";
}

// Migration 3: Booking Enhancement
echo "<h2>Migration 3: Booking Enhancement</h2>";
$migration3 = file_get_contents(__DIR__ . '/../database/migrations/booking_enhancement.sql');

if ($migration3) {
    try {
        $statements = explode(';', $migration3);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 3 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: booking_enhancement.sql</div>";
}

// Migration 4: Schedule Service Links
echo "<h2>Migration 4: Schedule Service Links</h2>";
$migration4 = file_get_contents(__DIR__ . '/../database/migrations/schedule_service_links.sql');

if ($migration4) {
    try {
        $statements = explode(';', $migration4);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 4 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: schedule_service_links.sql</div>";
}

// Migration 5: Schedule Staff Check-in
echo "<h2>Migration 5: Schedule Staff Check-in</h2>";
$migration5 = file_get_contents(__DIR__ . '/../database/migrations/schedule_staff_checkin.sql');

if ($migration5) {
    try {
        $statements = explode(';', $migration5);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 5 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: schedule_staff_checkin.sql</div>";
}

// Migration 6: Tour Journal Tables
echo "<h2>Migration 6: Tour Journal Tables</h2>";
$migration6 = file_get_contents(__DIR__ . '/../database/migrations/tour_journal_tables.sql');

if ($migration6) {
    try {
        $statements = explode(';', $migration6);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 6 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: tour_journal_tables.sql</div>";
}

// Migration 7: Tour Feedback Tables
echo "<h2>Migration 7: Tour Feedback Tables</h2>";
$migration7 = file_get_contents(__DIR__ . '/../database/migrations/tour_feedback_tables.sql');

if ($migration7) {
    try {
        $statements = explode(';', $migration7);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 7 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: tour_feedback_tables.sql</div>";
}

// Migration 8: Booking Documents
echo "<h2>Migration 8: Booking Documents</h2>";
$migration8 = file_get_contents(__DIR__ . '/../database/migrations/booking_documents.sql');

if ($migration8) {
    try {
        $statements = explode(';', $migration8);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Column/Table already exists (skipped): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 8 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: booking_documents.sql</div>";
}

// Migration 9: Schedule Task Checks
echo "<h2>Migration 9: Schedule Task Checks</h2>";
$migration9 = file_get_contents(__DIR__ . '/../database/migrations/schedule_task_checks.sql');

if ($migration9) {
    try {
        $statements = explode(';', $migration9);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Skipped (exists): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 9 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: schedule_task_checks.sql</div>";
}

// Migration 10: Tour Versions
echo "<h2>Migration 10: Tour Versions</h2>";
$migration10 = file_get_contents(__DIR__ . '/../database/migrations/tour_versions.sql');

if ($migration10) {
    try {
        $statements = explode(';', $migration10);
        $success_count = 0;
        $error_count = 0;

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement))
                continue;

            try {
                $conn->exec($statement);
                $success_count++;
            } catch (PDOException $e) {
                if (
                    strpos($e->getMessage(), 'Duplicate column') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false
                ) {
                    echo "<div class='info'>⚠️ Skipped (exists): " . substr($statement, 0, 50) . "...</div>";
                } else {
                    $error_count++;
                    echo "<div class='error'>❌ Error: " . $e->getMessage() . "<br>Statement: " . substr($statement, 0, 100) . "...</div>";
                }
            }
        }

        echo "<div class='success'>✅ Migration 10 completed! Success: $success_count, Errors: $error_count</div>";
    } catch (Exception $e) {
        echo "<div class='error'>❌ Fatal error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ Could not read migration file: tour_versions.sql</div>";
}

// Verify tables
echo "<h2>Verification</h2>";
echo "<div class='info'><strong>Checking created tables...</strong></div>";

$tables_to_check = [
    'tour_pricing',
    'tour_suppliers',
    'tour_supplier_links',
    'tour_pricing_history',
    'quote_breakdown',
    'quote_status_history',
    'quote_templates',
    'booking_status_history',
    'booking_notifications',
    'notification_templates',
    'schedule_service_links',
    'tour_journals',
    'tour_journal_images',
    'tour_feedbacks',
    'tour_feedback_images',
    'booking_documents'
    ,
    'schedule_task_checks',
    'tour_versions',
    'tour_version_itineraries',
    'tour_version_prices',
    'tour_version_media',
    'tour_version_rules'
];

foreach ($tables_to_check as $table) {
    try {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            // Count rows
            $count_stmt = $conn->query("SELECT COUNT(*) as cnt FROM $table");
            $count = $count_stmt->fetch()['cnt'];
            echo "<div class='success'>✅ Table '$table' exists ($count rows)</div>";
        } else {
            echo "<div class='error'>❌ Table '$table' NOT found</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error checking table '$table': " . $e->getMessage() . "</div>";
    }
}

echo "
    <hr>
    <p><a href='index.php' class='btn'>← Back to Admin</a></p>
    <p><strong>Note:</strong> Nếu có lỗi, kiểm tra file migration trong <code>database/migrations/</code></p>
</div>
</body>
</html>";
