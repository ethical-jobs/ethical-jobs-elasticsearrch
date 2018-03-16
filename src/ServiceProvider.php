<?php

namespace EthicalJobs\Elasticsearch;

use Maknz\Slack\Client as SlackClient;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Console\Output\ConsoleOutput;
use EthicalJobs\Elasticsearch\Indexing\Indexer;
use EthicalJobs\Elasticsearch\Indexing\Logger;
use EthicalJobs\Elasticsearch\IndexSettings;
use EthicalJobs\Elasticsearch\Console;
use EthicalJobs\Elasticsearch\Index;

/**
 * Elasticsearch service provider
 *
 * @author Andrew McLagan <andrew@ethicaljobs.com.au>
 */

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Config file path
     *
     * @var string
     */
    protected $configPath = __DIR__.'/../config/elasticsearch.php';  

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->configPath => config_path('elasticsearch.php')
        ], 'config');

        $this->bootObservers();

        $this->bootCommands();
    }

     /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath, 'elasticsearch');        

        $this->registerConnectionSingleton();

        $this->registerIndexSingleton();

        $this->registerDocumentIndexer();

        $this->registerLogger();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Index::class,
            Client::class,
            Indexer::class,
        ];
    }    

    /**
     * Register connection instance
     *
     * @return void
     */
    protected function registerConnectionSingleton(): void
    {
        $this->app->singleton(Client::class, function () {

            $config = array_merge([
                'logPath'   => storage_path().'/logs/elasticsearch-'.php_sapi_name().'.log',
            ], config('elasticsearch'));

            $connection = array_get($config, 'connections.'.$config['defaultConnection']);

            $client = ClientBuilder::create()->setHosts($connection['hosts']);

            if ($connection['logging']) {
                $logger = ClientBuilder::defaultLogger($connection['logPath']);
                $client->setLogger($logger);
            }

            return $client->build();
        });
    }

    /**
     * Register index instance
     *
     * @return void
     */
    protected function registerIndexSingleton(): void
    {
        $this->app->singleton(Index::class, function ($app) {

            $settings = new IndexSettings(
                config('elasticsearch.index'),
                config('elasticsearch.settings'),
                config('elasticsearch.mappings')
            );

            $settings->setIndexables(config('elasticsearch.indexables'));

            return new Index($app[Client::class], $settings);
        });
    }   

    /**
     * Register document indexer
     *
     * @return void
     */
    protected function registerDocumentIndexer(): void
    {
        $this->app->bind(Indexer::class, function ($app) {
            return new Indexer(
                $app[Client::class],
                $app[Index::class],
                $app[Logger::class]
            );
        });
    }     

    /**
     * Register slack logger
     *
     * @return void
     */
    protected function registerLogger(): void
    {
        $this->app->bind(Logger::class, function ($app) {
            $slack = new SlackClient(
                config('elasticsearch.logging.slack.webhook'), 
                config('elasticsearch.logging.slack')
            );
            $console = new ConsoleOutput;
            return new Logger($slack, $console);
        });
    }         

    /**
     * Configure indexable observers
     *
     * @return Void
     */
    protected function bootObservers(): void
    {
        $indexables = resolve(Index::class)
            ->getSettings()
            ->getIndexables();

        foreach ($indexables as $indexable) {
            $indexable::observe(IndexableObserver::class);
        }
    }        

    /**
     * Register console commands
     *
     * @return Void
     */
    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\CreateIndex::class,
                Console\DeleteIndex::class,
                Console\FlushIndex::class,
                Console\IndexDocuments::class,
            ]);
        }
    }        
}