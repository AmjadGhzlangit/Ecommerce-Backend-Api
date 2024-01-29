<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('slug');
            $table->foreignIdFor(Category::class); //->constrained('categories');
            $table->string('description');
            $table->string('image')->nullable();
            $table->uuid('sku'); //Stock Keeping Unit
            $table->integer('qty');
            $table->decimal('price');
            $table->string('currency');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
