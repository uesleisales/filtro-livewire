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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('sku')->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('active')->default(true);
            $table->string('image')->nullable();
            
            // Foreign keys
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            
            $table->timestamps();
            
            // Índices para otimização de consultas
            $table->index('name');
            $table->index('price');
            $table->index('active');
            $table->index(['category_id', 'brand_id']);
            $table->index(['name', 'category_id', 'brand_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
