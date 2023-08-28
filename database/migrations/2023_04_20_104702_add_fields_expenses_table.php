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
        Schema::table('expenses', function (Blueprint $table) {
            $table->date('date')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_recurrence')->nullable();
            $table->boolean('is_installment')->nullable();

            $table->date('start_installment_at')->nullable();
            $table->date('end_installment_at')->nullable();

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('expenses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('description');
            $table->dropColumn('category');
            $table->dropColumn('is_recurrence');
            $table->dropColumn('is_installment');
            $table->dropColumn('start_installment_at');
            $table->dropColumn('end_installment_at');
            $table->dropColumn('parent_id');
        });
    }
};
