<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOtpCodeColumnFromUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('otp_code');
            // Drop other OTP columns if needed, e.g., $table->dropColumn('otp_expires_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp_code')->nullable();
            // Re-add other OTP columns if needed
        });
    }
}