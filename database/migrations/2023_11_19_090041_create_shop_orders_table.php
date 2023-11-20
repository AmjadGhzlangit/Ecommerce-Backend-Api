<?php


use App\Models\OrderLine;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(OrderLine::class);
            $table->unsignedTinyInteger('Payment_method_id')->unsigned();
            $table->string('Shipping_address');
            $table->integer('order_total');
            $table->string('shipping_method');
            $table->string('order_status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_orders');
    }
};
