{{--
    Copyright 2015-2017 ppy Pty. Ltd.

    This file is part of osu!web. osu!web is distributed with the hope of
    attracting more community contributions to the core ecosystem of osu!.

    osu!web is free software: you can redistribute it and/or modify
    it under the terms of the Affero GNU General Public License version 3
    as published by the Free Software Foundation.

    osu!web is distributed WITHOUT ANY WARRANTY; without even the implied
    warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with osu!web.  If not, see <http://www.gnu.org/licenses/>.
--}}
<?php
    if (!isset($editing)) { $editing = false; }
?>
<div class="post-box__toolbar hidden-xs">
    @include("forum._post_toolbar")
</div>

<div class="post-editor__actions">
    <button class="js-editor-zoom--visible js-editor-zoom--end hidden btn-osu btn-osu--small btn-osu-default post-editor__action post-editor__action--compact" type="button" title="{{ trans("forum.topic.post_edit.zoom.end") }}">
        <i class="fa fa-compress"></i>
    </button>

    <div class="visible-xs">
        <button class="js-editor-zoom--hidden js-editor-zoom--start btn-osu btn-osu--small btn-osu-default post-editor__action post-editor__action--compact" type="button" title="{{ trans("forum.topic.post_edit.zoom.start") }}">
            <i class="fa fa-expand"></i>
        </button>
    </div>

    @if ($editing)
        <button class="js-editor-zoom--hidden btn-osu btn-osu--small btn-osu-default js-edit-post-cancel post-editor__action" type="button">{{ trans("forum.topic.post_edit.cancel") }}</button>
    @endif

    <button class="js-editor-zoom--hidden btn-osu btn-osu--small btn-osu-default post-editor__action" type="submit">{{ $submitText }}</button>
</div>
