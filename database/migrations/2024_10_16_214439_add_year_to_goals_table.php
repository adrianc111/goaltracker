<?php

use App\Models\Goal;
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
        Schema::table('goals', function (Blueprint $table) {
            $table->year('year')->nullable()->after('due_date');
        });

        $goals = Goal::withoutGlobalScopes()->whereNull('due_date')->get();

        foreach ($goals as $goal) {
            $goal->update(['year' => now()->year]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
