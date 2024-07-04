<?php

require "vendor/autoload.php";

use App\Repositories\SqliteArticleRepository;
use Carbon\Carbon;
use Medoo\Medoo;
use App\Models\Article;
use Ramsey\Uuid\Uuid;

$database = new Medoo([
    'type' => 'sqlite',
    'database' => 'storage/database.sqlite'
]);

//$id = Uuid::uuid4()->toString();

$repo = new SqliteArticleRepository($database);
//$repo->insert(new Article($id,'author now', 'Now wit hauthor','Test', Carbon::now('UTC')));



var_dump($repo->getById('e409cca2-202e-44da-8dc1-840695854252'));