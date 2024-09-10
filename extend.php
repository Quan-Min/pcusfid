<?php

use Flarum\Extend;
use PriPPP\CustomFields\Controller\CreateDiscussionController;
use PriPPP\CustomFields\Listener\AddCustomFields;
use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Discussion\Event\Saving as DiscussionSaving;
use Flarum\Post\Event\Saving as PostSaving;
use Illuminate\Support\Arr;

return [
    // 修改 API 路由名称以避免冲突
    (new Extend\Routes('api'))
        ->post('/custom-discussions', 'custom-discussions.create', CreateDiscussionController::class),

    // 自定义序列化器属性（针对 posts 表）
    (new Extend\ApiSerializer(PostSerializer::class))
        ->attribute('c_uindex', function ($serializer, $post) {
            return $post->c_uindex;
        })
        ->attribute('c_olink', function ($serializer, $post) {
            return $post->c_olink;
        })
        ->attribute('c_slink', function ($serializer, $post) {
            return $post->c_slink;
        }),

    // 事件监听器（针对 discussions 表，更新 createdAt）
    (new Extend\Event())
        ->listen(DiscussionSaving::class, function (DiscussionSaving $event) {
            $attributes = Arr::get($event->data, 'attributes', []);

            // 修改创建日期
            if (isset($attributes['createdAt'])) {
                $event->discussion->created_at = new \DateTime($attributes['createdAt']);
            }
        }),

    // 事件监听器（针对 posts 表，插入自定义字段）
    (new Extend\Event())
        ->listen(PostSaving::class, function (PostSaving $event) {  // 注意：这里应该是 PostSaving 而不是 DiscussionSaving
            $attributes = Arr::get($event->data, 'attributes', []);

            // 添加自定义字段 c_uindex
            if (isset($attributes['c_uindex'])) {
                $event->post->c_uindex = $attributes['c_uindex'];
            }

            // 添加自定义字段 c_olink
            if (isset($attributes['c_olink'])) {
                $event->post->c_olink = $attributes['c_olink'];
            }

            // 添加自定义字段 c_slink
            if (isset($attributes['c_slink'])) {
                $event->post->c_slink = $attributes['c_slink'];
            }
        }),
];

