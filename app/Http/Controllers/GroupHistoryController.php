<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserGroupEvent;

class GroupHistoryController extends Controller
{
    public function index()
    {
        $rawParams = request()->all();
        $params = get_params($rawParams, null, [
            'group:string_presence',
            'max_date:time',
            'min_date:time',
            'user:string_presence',
        ], ['null_missing' => true]);
        $query = UserGroupEvent::visibleForUser(auth()->user());

        if ($params['group'] !== null) {
            // Not `app('groups')->byIdentifier(...)` because that would create the group if not found
            $groupId = app('groups')->allByIdentifier()->get($params['group'])?->getKey();

            if ($groupId !== null) {
                $query->where('group_id', $groupId);
            } else {
                $query->none();
            }
        }

        if ($params['max_date'] !== null) {
            $params['max_date']->endOfDay();

            $query->where('created_at', '<=', $params['max_date']);

            $params['max_date'] = json_date($params['max_date']);
        }

        if ($params['min_date'] !== null) {
            $params['min_date']->startOfDay();

            $query->where('created_at', '>=', $params['min_date']);

            $params['min_date'] = json_date($params['min_date']);
        }

        if ($params['user'] !== null) {
            $userId = User::lookupWithHistory($params['user'], null, true)?->getKey();

            if ($userId !== null) {
                $query->where('user_id', $userId);
            } else {
                $query->none();
            }
        }

        $cursorHelper = UserGroupEvent::makeDbCursorHelper($rawParams['sort'] ?? null);
        $params['sort'] = $cursorHelper->getSortName();
        [$events, $hasMore] = $query
            ->cursorSort($cursorHelper, cursor_from_params($rawParams))
            ->limit(50)
            ->getWithHasMore();

        return [
            ...cursor_for_response($cursorHelper->next($events, $hasMore)),
            'events' => json_collection($events, 'UserGroupEvent'),
            'params' => $params,
        ];
    }
}
