<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id') // this will create a column that suitable for referencing another table's ID
                                         // it know which table it's referencing because user_id refer to user.id in table user
                ->constrained() // make sure that user_id is match to a id on other table, This prevents an order from referring to a nonexistent user.
                ->restrictOnDelete(); //Prevents deleting a user while their orders still reference that user.

            $table->foreignId('pickup_slot_id')
                ->constrained('pickup_slots') //tells Laravel which table to reference.
                ->restrictOnDelete();

            $table->string('status')->default('PENDING');
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['pickup_slot_id', 'status']);
            $table->index(['user_id', 'created_at']);
            // An index helps the database find matching records efficiently.
            // The first index supports queries such as:
            // Find orders for pickup slot 3 that are still pending.

            // The second supports queries such as:
            // Find this customer's orders, ordered by creation time.

            // These are composite indexes: each index covers more than one column.
            // The column order matters. An index on (user_id, created_at) is particularly useful when filtering by user_id and then ordering or filtering by created_at.
            // An index consumes storage and adds some cost to inserts and updates, so we choose indexes based on actual queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
