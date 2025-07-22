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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(column: 'user_id', unsigned: true);
            $table->string('title', 255);
            $table->text('description');
            $table->enum('status', $this->getTasksStatus());
            $table->date('deadline');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }

    /**
     * @return array
     */
    protected function getTasksStatus(): array
    {
        return [
            'pending',
            'finished',
        ];
    }
};
