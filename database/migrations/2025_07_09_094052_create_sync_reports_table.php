<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sync_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // The admin who ran the sync
            $table->string('sync_source')->default('warehouse');
            $table->integer('fetched_count')->default(0);
            $table->integer('added_count')->default(0);
            $table->integer('skipped_count')->default(0);
            $table->json('details')->nullable(); // To store the list of new products
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_reports');
    }
};
