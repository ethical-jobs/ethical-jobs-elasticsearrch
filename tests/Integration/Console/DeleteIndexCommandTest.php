<?php

namespace Tests\Integration\Console;

use Mockery;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use EthicalJobs\Elasticsearch\Index;

class DeleteIndexCommandTest extends \Tests\TestCase
{
    /**
     * @test
     * @group Integration
     */
    public function it_create_an_index()
    {
        $index = Mockery::mock(Index::class)
            ->shouldReceive('delete')
            ->once()
            ->withNoArgs()
            ->getMock();

        App::instance(Index::class, $index);

        Artisan::call('ej:es:index-delete');
    }
}
