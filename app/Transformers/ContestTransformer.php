<?php

/**
 *    Copyright 2016 ppy Pty. Ltd.
 *
 *    This file is part of osu!web. osu!web is distributed with the hope of
 *    attracting more community contributions to the core ecosystem of osu!.
 *
 *    osu!web is free software: you can redistribute it and/or modify
 *    it under the terms of the Affero GNU General Public License version 3
 *    as published by the Free Software Foundation.
 *
 *    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
 *    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *    See the GNU Affero General Public License for more details.
 *
 *    You should have received a copy of the GNU Affero General Public License
 *    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Transformers;

use App\Models\Beatmap;
use App\Models\Contest;
use App\Models\ContestEntry;
use Auth;
use League\Fractal;

class ContestTransformer extends Fractal\TransformerAbstract
{
    protected $availableIncludes = [
        'entries',
    ];

    public function transform(Contest $contest)
    {
        $response = [
            'id' => $contest->id,
            'name' => $contest->name,
            'description' => $contest->description_voting,
            'type' => $contest->type,
            'header_url' => $contest->header_url,
            'max_entries' => $contest->max_entries,
            'max_votes' => $contest->max_votes,
            'entry_starts_at' => json_time($contest->entry_starts_at),
            'entry_ends_at' => json_time($contest->entry_ends_at),
            'voting_ends_at' => json_time($contest->voting_ends_at),
            'show_votes' => $contest->show_votes,
            'link_icon' => $contest->link_icon,
        ];

        if ($contest->type === 'art') {
            $response['shape'] = $contest->entry_shape;
        }

        if (isset($contest->extra_options['best_of'])) {
            $response['best_of'] = true;
        }

        return $response;
    }

    public function includeEntries(Contest $contest)
    {
        if (isset($contest->extra_options['best_of'])) {
            $user = Auth::user();
            if ($user === null) {
                $entries = [];
            } else {
                $playmode = Beatmap::MODES[$contest->extra_options['best_of']['mode'] ?? 'osu'];

                // This just does a join to playcounts (via beatmapset) to filter out maps a user hasn't played.
                $entries =
                    ContestEntry::with('contest')
                            ->whereIn('entry_url', function ($query) use ($playmode, $user) {
                                $query->select('beatmapset_id')
                                    ->from('osu_beatmaps')
                                    ->where('osu_beatmaps.playmode', '=', $playmode)
                                    ->whereIn('beatmap_id', function ($query) use ($user) {
                                        $query->select('beatmap_id')
                                            ->from('osu_user_beatmap_playcount')
                                            ->where('user_id', '=', $user->user_id);
                                    });
                            })
                            ->where('contest_id', $contest->id)
                            ->get();
            }
        } else {
            $entries = $contest->entries;
        }

        return $this->collection($entries, new ContestEntryTransformer);
    }
}
