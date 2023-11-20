<?php

use App\Models\ShopOrder;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(ShopOrder::class, 'ordered_product_id')->unsigned();
            $table->decimal('rating_value', 5);
            $table->string('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_reviews');
    }
};
