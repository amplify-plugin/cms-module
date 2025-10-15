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
        Schema::table('contents', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('name');
            $table->text('summary')->nullable()->after('slug');
            $table->unsignedBigInteger('author_id')->nullable()->after('id');
            $table->unsignedBigInteger('page_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('cover_image');
            $table->dropColumn('summary');
            $table->dropColumn('author_id');
            $table->dropColumn('page_id');
        });
    }
};
