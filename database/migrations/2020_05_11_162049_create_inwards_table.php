<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inwards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->onDelete('cascade');
            $table->string('sample_id')->unique();
            $table->integer('patient_id');
            $table->integer('batch_id');
            $table->string('name');
            $table->integer('age');
            $table->enum('sex', ['M', 'F', 'T']);
            $table->text('address');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('testing_at')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inwards');
    }
}
