<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visits', function (Blueprint $table) {
            // On change le type 'string' (255) en 'text' (illimité)
            $table->text('url')->change();
        });
    }
    
    public function down()
    {
        Schema::table('visits', function (Blueprint $table) {
            // Pour revenir en arrière si besoin
            $table->string('url', 255)->change();
        });
    }
};
