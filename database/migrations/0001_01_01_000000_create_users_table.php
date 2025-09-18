<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL
            )
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS sessions (
                id VARCHAR(255) PRIMARY KEY,
                user_id INTEGER NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                payload TEXT NOT NULL,
                last_activity INTEGER NOT NULL
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_sessions_user_id ON sessions(user_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_sessions_last_activity ON sessions(last_activity)");
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
