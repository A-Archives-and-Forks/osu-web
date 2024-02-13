<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * @property int $banchostats_id
 * @property \Carbon\Carbon $date
 * @property int $multiplayer_games
 * @property int $users_irc
 * @property int $users_osu
 * @property int $users_lazer
 */
class BanchoStats extends Model
{
    protected $table = 'osu_banchostats';
    protected $primaryKey = 'banchostats_id';

    public $timestamps = false;

    public static function stats()
    {
        return array_reverse(
            static::whereRaw('banchostats_id mod 10 = 0')
                ->select(['users_irc', DB::raw('(users_osu + users_lazer) AS users'), 'multiplayer_games', 'date'])
                ->orderBy('banchostats_id', 'DESC')
                ->limit(24 * 60 / 10)
                ->get()
                ->all()
        );
    }
}
