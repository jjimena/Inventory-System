<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneNumberHubNameAndLocationToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->after('name')->unique()->nullable();
            $table->string('hub_name')->after('phone_number')->nullable();
            $table->string('address')->after('hub_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping the columns in reverse order to maintain referential integrity
            $table->dropColumn('address');
            $table->dropColumn('hub_name');
            $table->dropColumn('phone_number');
        });
    }
}
