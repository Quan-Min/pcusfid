<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::addColumns('posts', [
    'c_uindex' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'c_olink' => ['type' => 'string', 'length' => 255, 'nullable' => true],
    'c_slink' => ['type' => 'string', 'length' => 255, 'nullable' => true],
]);
