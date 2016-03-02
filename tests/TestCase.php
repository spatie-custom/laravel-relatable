<?php

namespace Spatie\Relatable\Test;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\Relatable\Test\TestModels\Apple;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase()
    {
        file_put_contents(__DIR__.'/temp/database.sqlite', null);

        $this->app['db']->connection()->getSchemaBuilder()->create('apples', function (Blueprint $table) {
            $table->increments('id');
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('oranges', function (Blueprint $table) {
            $table->increments('id');
        });

        foreach (range(1, 10) as $i) {
            Apple::create(['id' => $i]);
            Orange::create(['id' => $i]);
        }

        include_once '__DIR__'.'/../resources/migrations/create_relatables_table.php.stub';

        (new \CreateRelatablesTable())->up();
    }
}
