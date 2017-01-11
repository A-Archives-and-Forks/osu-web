<?php

/**
 *    Copyright 2015 ppy Pty. Ltd.
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

namespace App\Http\Controllers\Forum;

use App\Models\Forum\Post;
use App\Models\Forum\Topic;
use Auth;
use Request;

class PostsController extends Controller
{
    protected $section = 'community';

    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'changeVisibility',
            'raw',
        ]]);

        return parent::__construct();
    }

    public function delete($id)
    {
        $post = Post::query()
            ->showDeleted(priv_check('ForumTopicModerate')->can())
            ->findOrFail($id);

        priv_check('ForumPostDelete', $post)->ensureCan();

        $post->topic->removePost($post, Auth::user());

        if ($post->topic->trashed()) {
            $redirect = route('forum.forums.show', $post->forum);

            return ujs_redirect($redirect);
        }

        return js_view('forum.topics.delete', compact('post'));
    }

    public function restore($id)
    {
        priv_check('ForumTopicModerate')->ensureCan();

        $post = Post::withTrashed()->findOrFail($id);
        $topic = $post->topic()->withTrashed()->first();

        $topic->restorePost($post, Auth::user());

        return js_view('forum.topics.restore', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);

        priv_check('ForumPostEdit', $post)->ensureCan();

        return view('forum.topics._post_edit', compact('post'));
    }

    public function update($id)
    {
        $post = Post::findOrFail($id);

        priv_check('ForumPostEdit', $post)->ensureCan();

        $body = Request::input('body');
        if ($body !== '') {
            $post->edit($body, Auth::user());
        }

        $posts = collect([$post->fresh()]);
        $topic = $post->topic;
        $postPosition = $topic->postPosition($post->post_id);

        return view('forum.topics._posts', compact('posts', 'postPosition', 'topic'));
    }

    public function raw($id)
    {
        $post = Post::findOrFail($id);

        if ($post->forum === null) {
            abort(404);
        }

        priv_check('ForumView', $post->forum)->ensureCan();

        $text = $post->bodyRaw;

        if (Request::input('quote') === '1') {
            $text = sprintf('[quote="%s"]%s[/quote]', $post->userNormalized()->username, $text);
        }

        return $text;
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);

        if ($post->forum === null) {
            abort(404);
        }

        priv_check('ForumView', $post->forum)->ensureCan();

        return ujs_redirect(post_url($post->topic_id, $post->post_id));
    }
}
