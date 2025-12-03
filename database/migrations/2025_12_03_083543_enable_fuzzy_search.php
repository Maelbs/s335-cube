<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Active l'extension pour pouvoir utiliser levenshtein()
        DB::statement('CREATE EXTENSION IF NOT EXISTS fuzzystrmatch');
    }

    public function down()
    {
        DB::statement('DROP EXTENSION IF EXISTS fuzzystrmatch');
    }
};
