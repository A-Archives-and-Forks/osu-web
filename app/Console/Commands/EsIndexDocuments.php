<?php

/**
 *    Copyright 2015-2017 ppy Pty. Ltd.
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

namespace App\Console\Commands;

use App\Libraries\Elasticsearch\Indexing;
use App\Models\Beatmapset;
use App\Models\Forum\Post;
use Illuminate\Console\Command;

class EsIndexDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:index-documents {--inplace} {--cleanup} {--yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes documents into Elasticsearch.';

    private $cleanup;
    private $inplace;
    private $types;
    private $suffix;
    private $yes;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->readOptions();
        $this->types = [Beatmapset::class, Post::class];
        $this->suffix = !$this->inplace ? '_'.time() : '';

        $oldIndices = Indexing::getOldIndices('osu');

        $continue = $this->starterMessage($oldIndices);
        if (!$continue) {
            return $this->error('User aborted!');
        }

        $indices = $this->index();

        $this->finish($indices, $oldIndices);
        $this->warn("\nIndexing completed.");
    }

    private function finish(array $indices, array $oldIndices)
    {
        // always update osu alias
        $indicesString = implode(', ', $indices);
        $this->warn("Aliasing '{$indicesString}' to 'osu'...");
        Indexing::updateAlias('osu', $indices);

        if (!$this->inplace && $this->cleanup) {
            foreach ($oldIndices as $index) {
                $this->warn("Removing '{$index}'...");
                Indexing::deleteIndex($index);
            }
        }
    }

    /**
     * Indexes and returns the names of the indices.
     *
     * @return array names of the indices indexed to.
     */
    private function index()
    {
        $indices = [];
        foreach ($this->types as $type) {
            if (!$this->inplace) {
                $indexName = "{$type::esIndexName()}{$this->suffix}";

                $this->info("Indexing {$type} into {$indexName}");
                $type::esIndexIntoNew(1000, $indexName);

                $indices[] = $indexName;
            } else {
                $this->info("In-place indexing {$type} into {$type::esIndexName()}");
                $type::esReindexAll(1000);

                $indices[] = $type::esIndexName();
            }
        }

        return $indices;
    }

    private function readOptions()
    {
        $this->inplace = $this->option('inplace');
        $this->cleanup = $this->option('cleanup');
        $this->yes = $this->option('yes');
    }

    private function starterMessage(array $oldIndices)
    {
        if ($this->inplace) {
            $this->warn('Running in-place reindex.');
            $confirmMessage = "This will reindex in-place (schemas must match) and alias them to 'osu'";
        } else {
            $this->warn('Running index transfer.');

            if ($this->cleanup) {
                $this->warn(
                    "The following indices will be deleted on completion!\n"
                    .implode("\n", $oldIndices)
                );
            }

            $confirmMessage = "This will create new indices and alias them to 'osu'";
        }

        return $this->yes || $this->confirm("{$confirmMessage}, begin indexing?");
    }
}
