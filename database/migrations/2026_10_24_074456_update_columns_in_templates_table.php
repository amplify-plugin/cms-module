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
        Schema::table('templates', function (Blueprint $table) {
            $table->json('author')->nullable()->after('name')->change();
            $table->string('version')->nullable()->after('slug');
            $table->string('changelog')->nullable()->after('readme');
            $table->renameColumn('component_folder', 'layout');
            $table->renameColumn('asset_folder', 'asset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('version');
            $table->dropColumn('changelog');
            $table->renameColumn('layout', 'component_folder');
            $table->renameColumn('asset', 'asset_folder');
        });
    }
};
