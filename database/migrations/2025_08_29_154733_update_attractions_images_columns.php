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
        Schema::table('attractions', function (Blueprint $table) {
            //renaming method
            $table->renameColumn('image', 'front_img');

            //adding new column method
            $table->string('back_img')->nullable()->after('front_img');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attractions', function (Blueprint $table) {
            $table->renameColumn('front_img', 'image');
            $table->dropColumn('back_img');

        });
    }
};
