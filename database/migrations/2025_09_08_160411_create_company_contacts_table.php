<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->string('phone1')->nullable();
            $table->string('phone2')->nullable();
            $table->string('land_p')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        //Inserting Default Data to the field
        DB::table('company_contacts')->insert([
            'address'=>'65/5 Rajamaha Vihara Road, Mirihana,Pitakotte',
            'phone1' => '+94 77 337 5642',
            'phone2' => '+94 70 707 0653',
            'land_p' => '0112808473',
            'whatsapp' => '+94 77 337 5642',
            'email' => 'info@voyaztravel.com',

    ]);



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_contacts');
    }
};
