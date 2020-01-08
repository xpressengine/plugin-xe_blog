<?php

namespace Xpressengine\Plugins\Post\Services;

use XeDB;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\PostConfigHandler;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;
use Xpressengine\Tag\TagHandler;

class PostService
{
    /** @var PostHandler $postHandler */
    protected $postHandler;

    /** @var PostMetaDataHandler $metaDataHandler */
    protected $metaDataHandler;

    /** @var PostConfigHandler $postConfigHandler */
    protected $postConfigHandler;

    /** @var TagHandler $tagHandler */
    protected $tagHandler;

    public function __construct(PostHandler $postHandler, PostMetaDataHandler $metaDataHandler, PostConfigHandler $postConfigHandler, TagHandler $tagHandler)
    {
        $this->postHandler = $postHandler;
        $this->metaDataHandler = $metaDataHandler;
        $this->postConfigHandler = $postConfigHandler;
        $this->tagHandler = $tagHandler;
    }

    public function store(Request $request, $instanceId)
    {
        $inputs = $request->originExcept('_token');

        if (isset($inputs['user_id']) === false) {
            $inputs['user_id'] = auth()->user()->getId();
        }

        if (isset($inputs['writer']) === false) {
            $inputs['writer'] = auth()->user()->getDisplayName();
        }

        XeDB::beginTransaction();
        try {
            $post = $this->postHandler->store($inputs, $instanceId);
            $this->metaDataHandler->saveMetaData($post, $inputs);

            if (isset($inputs['_tags']) && empty($inputs['_tags']) === false) {
                $this->tagHandler->set($post->id, $inputs['_tags']);
            }
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();

        return $post;
    }

    public function update(Request $request, $post)
    {
        $inputs = $request->originExcept(['id', '_token']);

        XeDB::beginTransaction();
        try {
            $this->postHandler->update($post, $inputs);
            $this->metaDataHandler->saveMetaData($post, $inputs);

            if (isset($inputs['_tags']) && empty($inputs['_tags']) === false) {
                $this->tagHandler->set($post->id, $inputs['_tags']);
            }
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }

    public function delete($post, $instanceId)
    {
        XeDB::beginTransaction();
        try {
            $postConfig = $this->postConfigHandler->get($instanceId);
            if ($postConfig->get('deleteToTrash') === true) {
                $this->postHandler->trashPost($post);
            } else {
                $this->metaDataHandler->deleteMetaData($post);
                $this->postHandler->dropPost($post);
            }
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }
}
