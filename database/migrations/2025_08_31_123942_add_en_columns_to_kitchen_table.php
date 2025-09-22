<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kitchen', function (Blueprint $table) {
            if (!Schema::hasColumn('kitchen', 'title_en')) {
                $table->string('title_en')->nullable()->after('title');
            }

            if (!Schema::hasColumn('kitchen', 'description_en')) {
                $table->text('description_en')->nullable()->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('kitchen', function (Blueprint $table) {
            if (Schema::hasColumn('kitchen', 'title_en')) {
                $table->dropColumn('title_en');
            }
            if (Schema::hasColumn('kitchen', 'description_en')) {
                $table->dropColumn('description_en');
            }
        });
    }
};
