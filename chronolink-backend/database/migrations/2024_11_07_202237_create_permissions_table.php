<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    const CAN_CREATE_EVENT = 'CAN_CREATE_EVENT';

    const CAN_UPDATE_EVENT = 'CAN_UPDATE_EVENT';

    const CAN_DELETE_EVENT = 'CAN_DELETE_EVENT';

    const CAN_CREATE_LABEL = 'CAN_CREATE_LABEL';

    const CAN_UPDATE_LABEL = 'CAN_UPDATE_LABEL';

    const CAN_DELETE_LABEL = 'CAN_DELETE_LABEL';

    const CAN_INVITE_USER = 'CAN_INVITE_USER';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->timestamps();
            $table->string('name');
        });

        DB::table('permissions')->insert([
            ['id' => Str::uuid(), 'name' => self::CAN_CREATE_EVENT, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => self::CAN_UPDATE_EVENT, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => self::CAN_DELETE_EVENT, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => self::CAN_CREATE_LABEL, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => self::CAN_UPDATE_LABEL, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => self::CAN_DELETE_LABEL, 'created_at' => now(), 'updated_at' => now()],
            ['id' => Str::uuid(), 'name' => self::CAN_INVITE_USER, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
