<?php

use Flarum\Extend;
use YourNamespace\CustomFields\Listener\AddCustomFields;

return [
    (new Extend\Listeners())
        ->subscribe(AddCustomFields::class),
];