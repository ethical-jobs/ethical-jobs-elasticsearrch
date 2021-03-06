<?php

namespace EthicalJobs\Elasticsearch\Console;

use Illuminate\Console\Command;
use EthicalJobs\Elasticsearch\Index;

/**
 * Deletes the primary elasticsearch index
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class DeleteIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ej:es:index-delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the primary elasticsearch index';

    /**
     * Elastic search index service
     *
     * @param \EthicalJobs\Elasticsearch\Index
     */
    private $index;

    /**
     * Constructor
     *
     * @param \EthicalJobs\Elasticsearch\Index $index
     * @return void
     */
    public function __construct(Index $index)
    {
        parent::__construct();

        $this->index = $index;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->index->delete();

        $this->info(implode("\n", $response ?? []));
    }
}
