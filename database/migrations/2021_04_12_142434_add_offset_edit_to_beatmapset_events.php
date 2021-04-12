<?php

use Illuminate\Database\Migrations\Migration;

class AddOffsetEditToBeatmapsetEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE beatmapset_events CHANGE type type ENUM(
            'nominate',
            'qualify',
            'disqualify',
            'approve',
            'rank',
            'kudosu_allow',
            'kudosu_deny',
            'kudosu_gain',
            'kudosu_lost',
            'issue_resolve',
            'issue_reopen',
            'discussion_delete',
            'discussion_restore',
            'discussion_post_delete',
            'discussion_post_restore',
            'kudosu_recalculate',
            'nomination_reset',
            'love',
            'discussion_lock',
            'discussion_unlock',
            'genre_edit',
            'language_edit',
            'remove_from_loved',
            'nsfw_toggle',
            'offset_edit'
        )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE beatmapset_events CHANGE type type ENUM(
            'nominate',
            'qualify',
            'disqualify',
            'approve',
            'rank',
            'kudosu_allow',
            'kudosu_deny',
            'kudosu_gain',
            'kudosu_lost',
            'issue_resolve',
            'issue_reopen',
            'discussion_delete',
            'discussion_restore',
            'discussion_post_delete',
            'discussion_post_restore',
            'kudosu_recalculate',
            'nomination_reset',
            'love',
            'discussion_lock',
            'discussion_unlock',
            'genre_edit',
            'language_edit',
            'remove_from_loved',
            'nsfw_toggle'
        )");
    }
}
