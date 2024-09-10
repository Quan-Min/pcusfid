<?php

namespace PriPPP\CustomFields\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Discussion\Discussion;
use Flarum\Post\CommentPost;
use Flarum\Api\Serializer\DiscussionSerializer;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tobscure\JsonApi\Document;
use Carbon\Carbon;
use Flarum\Tags\Tag; // 确保引入 Tag 模型
use Illuminate\Database\Connection; // 引入当前数据库连接
use Illuminate\Support\Facades\Schema;


class CreateDiscussionController extends AbstractCreateController
{
    public $serializer = DiscussionSerializer::class;

    protected function data(Request $request, Document $document)
    {
        // 开始事务
        $connection = app('db'); // 获取当前数据库连接
        $connection->beginTransaction();

        try {
            // 获取当前用户
            $actor = $request->getAttribute('actor');

            // 从请求体中解析讨论属性
            $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);

            // 检查标题是否存在
            $title = Arr::get($attributes, 'title');
            if (empty($title)) {
                throw new \InvalidArgumentException('Title is required.');
            }
            
            // 确保帖子内容存在
            $content = Arr::get($attributes, 'content');
            if (empty($content)) {
                throw new \InvalidArgumentException('Content is required.');
            }
            
            $testTime = Carbon::now(); // Carbon::createFromFormat('Y-m-d H:i:s', '2023-01-01 12:00:00');
            // 获取或生成创建时间
            $createdAt = Arr::has($attributes, 'created_at') ?
                new Carbon(Arr::get($attributes, 'created_at')) : $testTime;

            // 启动讨论，传递标题和当前用户
            $discussion = Discussion::start($title, $actor);
            $discussion->created_at = $createdAt;
            
            // if (Schema::hasColumn('discussions', 'view_count')) {
                // 如果 discussions 表中有 view_count 字段，则继续处理
            $discussion->view_count = rand(500, 1000);  // 生成 500 到 1000 之间的随机数
            // }

            // 保存讨论
            $discussion->save();

            // 确保讨论保存成功且有一个有效的ID
            if (!$discussion->id) {
                throw new \Exception('Failed to create discussion.');
            }

            // 处理 tags 关系
            $tags = Arr::get($request->getParsedBody(), 'data.relationships.tags.data', []);
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    // 确保标签 ID 是有效的，并将其添加到讨论中
                    $tagModel = Tag::find($tag['id']);
                    if ($tagModel) {
                        $discussion->tags()->attach($tagModel);
                    }
                }
            }

            // 创建第一个帖子
            $post = new CommentPost();
            $post->discussion_id = $discussion->id;
            $post->user_id = $actor->id;
            $post->created_at = $createdAt;
            $post->content = Arr::get($attributes, 'content');
            $post->is_approved = 1;
            $post->ip_address = $request->getServerParams()['REMOTE_ADDR'];

            // 将自定义字段插入帖子对象中
            $post->c_uindex = Arr::get($attributes, 'c_uindex', null);
            $post->c_olink = Arr::get($attributes, 'c_olink', null);
            $post->c_slink = Arr::get($attributes, 'c_slink', null);
            
            // 保存帖子
            $post->save();

            // 更新讨论的帖子信息
            $discussion->first_post_id = $post->id;
            $discussion->last_post_id = $post->id;
            $discussion->last_posted_at = $post->created_at;
            $discussion->save();

            // 提交事务
            $connection->commit();

            return $discussion;
        } catch (\Exception $e) {
            // 回滚事务
            $connection->rollBack();
            throw $e;  // 抛出异常以供后续处理
        }
    }
}
