<?php

use App\Models\Product;
use App\Models\ShopOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ShopOrder::class, 'order_id');
            $table->foreignIdFor(Product::class, 'product_id');
            $table->integer('qty');
            $table->decimal('price', 5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_lines');
    }
};
