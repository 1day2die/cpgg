<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('coupons')) {
            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'max_uses_per_user')) {
                $table->integer('max_uses_per_user')->nullable()->after('max_uses');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('coupons')) {
            return;
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (Schema::hasColumn('coupons', 'max_uses_per_user')) {
                $table->dropColumn('max_uses_per_user');
            }
        });
    }
};