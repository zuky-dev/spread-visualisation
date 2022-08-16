<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_book_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source_currency');
            $table->string('destination_currency');
            $table->enum('transaction', ['BUY, SELL']);
            $table->double('price');
            $table->timestamp('time_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_book_logs');
    }
};
