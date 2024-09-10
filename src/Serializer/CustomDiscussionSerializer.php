<?php

namespace YourNamespace\CustomFields\Serializer;

use Flarum\Api\Serializer\DiscussionSerializer;

class CustomDiscussionSerializer extends DiscussionSerializer
{
    protected function getDefaultAttributes($discussion)
    {
        $attributes = parent::getDefaultAttributes($discussion);

        // 添加自定义字段
        $attributes['customField'] = $discussion->custom_field;
        $attributes['anotherCustomField'] = $discussion->another_custom_field;

        return $attributes;
    }
}