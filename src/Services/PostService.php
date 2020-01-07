<?php

namespace Xpressengine\Plugins\Post\Services;

use XeDB;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;

class PostService
{
    /** @var PostHandler $postHandler */
    protected $postHandler;

    /** @var PostMetaDataHandler $metaDataHandler */
    protected $metaDataHandler;

    public function __construct(PostHandler $postHandler, PostMetaDataHandler $metaDataHandler)
    {
        $this->postHandler = $postHandler;
        $this->metaDataHandler = $metaDataHandler;
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
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }
}
