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
        Schema::create('personal_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('persen_diskon', 5, 2)->default(0); // Percentage discount (0-100)
            $table->text('admin_note')->nullable(); // Admin note about the discount
            $table->boolean('is_active')->default(true); // Whether discount is active
            $table->timestamp('expires_at')->nullable(); // Optional expiry date
            $table->timestamps();
            
            // Ensure one discount per user-product combination
            $table->unique(['user_id', 'product_id']);
            
            // Add indexes for better performance
            $table->index(['user_id', 'is_active']);
            $table->index(['product_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_discounts');
    }
};