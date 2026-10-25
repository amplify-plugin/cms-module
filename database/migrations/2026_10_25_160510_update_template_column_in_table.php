<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = ['footers', 'menu_groups', 'pages', 'sites'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Footer
        Schema::table('footers', function (Blueprint $table) {
            $table->dropColumn('template_id');
            $table->string('template')->nullable()->after('content');
        });

        // Menu Groups
        Schema::table('menu_groups', function (Blueprint $table) {
            $table->dropColumn('template_id');
            $table->dropColumn('blade_location');
        });

        // Pages
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('template_id');
            $table->dropColumn('paramModel');
            $table->dropColumn('modelFindColumn');
            $table->dropColumn('default_page');
        });

        // Sites
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('template_id');
            $table->string('template')->nullable()->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Footer
        Schema::table('footers', function (Blueprint $table) {
            $table->dropColumn('template');
            $table->foreignId('template_id')->nullable()->after('content');
        });

        //Menu Groups
        Schema::table('menu_groups', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('short_code');
            $table->string('blade_location')->nullable()->after('short_code');
        });

        // Pages
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('id');
            $table->string('paramModel')->nullable()->after('page_type');
            $table->string('modelFindColumn')->nullable()->after('page_type');
            $table->boolean('default_page')->default(false)->after('is_published');
        });

        // Sites
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('template');
            $table->foreignId('template_id')->nullable()->after('url');
        });
    }
};
