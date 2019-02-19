<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserModel extends Migration
{
    protected static $columns = ['provider_name', 'provider_type'];

    protected $table;

    public function __construct()
    {
        $this->table = config('socialauth.user_table', 'users');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            foreach(self::$columns as $column ) {
                $table->string($column)->nullable();
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
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropIfExists([self::$columns]);
        });
    }
}