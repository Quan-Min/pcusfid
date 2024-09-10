<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::addColumns('posts', [
    'c_uindex' => ['string', 'nullable' => true],
    'c_olink' => ['string', 'nullable' => true],
    'c_slink' => ['string', 'nullable' => true],
]);
