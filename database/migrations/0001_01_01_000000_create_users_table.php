<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', length: 100);
            $table->string('last_name', length: 100);
            $table->string('username', length: 50)->unique();
            $table->string('email')->unique();
            // type = 'basic': basic, 'custom': custom, 'premium': premium, 'enterprise': enterprise
            $table->string('type', length: 20)->default('basic');
            // admin_level = 0: super admin, 1: admin, 2: moderator, 3: user, 4: guest
            $table->tinyInteger('admin_level')->default(1);
            // admin_id = 0: this user is super admin or admin, all other values are the id of the admin that created this user
            $table->unsignedBigInteger('admin_id')->default(0);
            // view_level = 0: all entires, 1: only his own entries
            $table->tinyInteger('view_level')->default(0);
            $table->char('language', 2)->default('en');
            $table->string('timezone', length: 60)->default('UTC');
            $table->string('date_format', length: 5)->default('d.m.Y');
            $table->tinyInteger('first_day_of_week')->default(1); // 0 = Sunday, 1 = Monday, etc.
            $table->string('currency', length: 3)->default('EUR');
            $table->foreignIdFor(\App\Models\Collection::class)->default(1);
            $table->boolean('demo')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger('wrong_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('twofa')->default(false);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->string('purpose', length: 20)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
