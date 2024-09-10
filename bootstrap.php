<?php

use Flarum\Extend;
use PriPPP\CustomFields\Listener\AddCustomFields;

return [
    (new Extend\Listeners())
        ->subscribe(AddCustomFields::class),
];