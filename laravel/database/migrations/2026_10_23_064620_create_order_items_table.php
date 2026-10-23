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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete(); //If an order is deleted, its associated order items are automatically deleted too.

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete(); //The restrictOnDelete() rule prevents deleting a product if existing order items still refer to it. This helps preserve the connection between historical orders and the products that were sold.

            $table->unsignedInteger('quantity'); //unsignedInteger means the database column cannot store a negative integer.
            $table->decimal('price', 10, 2);

            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
