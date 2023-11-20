<?php


use App\Models\ProductItem;
use App\Models\ShoppingCart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopping_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ShoppingCart::class);
            $table->foreignIdFor(ProductItem::class);
            $table->integer('qty');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopping_cart_items');
    }
};
