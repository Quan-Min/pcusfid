<?php

namespace PriPPP\CustomFields\Listener;

use Flarum\Discussion\Event\Saving;
use Illuminate\Contracts\Events\Dispatcher;

class AddCustomFields
{
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Saving::class, [$this, 'handle']);
    }

    public function handle(Saving $event)
    {
        $discussion = $event->discussion;
        $attributes = $event->data['attributes'];

    }
}