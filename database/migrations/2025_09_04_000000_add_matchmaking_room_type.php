<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `multiplayer_rooms` MODIFY `type` ENUM(
            'playlists',
            'head_to_head',
            'team_versus',
            'matchmaking'
        ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `multiplayer_rooms` MODIFY `type` ENUM(
            'playlists',
            'head_to_head',
            'team_versus'
        ) NOT NULL");
    }
};
