<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS sync_email_nm_empleados');
        DB::unprepared("
            CREATE TRIGGER sync_email_nm_empleados
            AFTER UPDATE ON nm_empleados
            FOR EACH ROW
            BEGIN
                IF NEW.emp_email <> OLD.emp_email OR (NEW.emp_email IS NOT NULL AND OLD.emp_email IS NULL) THEN
                    UPDATE usuario
                    SET email = NEW.emp_email,
                        updated_at = NOW()
                    WHERE usuario = NEW.emp_ced
                      AND NEW.emp_email IS NOT NULL
                      AND NEW.emp_email <> '';
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS sync_email_nm_empleados');
    }
};
