<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->string("referral_code", 10)->unique()->nullable()->after("email");
            $table->string("referred_by_code", 10)->nullable()->after("referral_code");
            $table->unsignedBigInteger("subadmin_id")->nullable()->after("referred_by_code");
            $table->enum("role", ["user", "subadmin", "admin"])->default("user")->after("subadmin_id");
            
            $table->foreign("subadmin_id")->references("id")->on("users")->onDelete("set null");
            $table->index(["referred_by_code", "subadmin_id", "role"]);
        });
    }

    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropForeign(["subadmin_id"]);
            $table->dropIndex(["referred_by_code", "subadmin_id", "role"]);
            $table->dropColumn(["referral_code", "referred_by_code", "subadmin_id", "role"]);
        });
    }
};