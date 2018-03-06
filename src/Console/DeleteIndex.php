<?php

namespace EthicalJobs\Console;

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
     * @param \App\Services\Elasticsearch\Index
     */
    private $index;

    /**
     * Constructor
     *
     * @param \App\Services\Elasticsearch\Index $index
     * @return void
     */
    public function __construct(Index $index)
    {
        $this->index = $index;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->index->delete();

        dump($response);
    }
}
