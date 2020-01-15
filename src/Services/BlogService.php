<?php

namespace Xpressengine\Plugins\XeBlog\Services;

use XeDB;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Tag\TagHandler;

class BlogService
{
    /** @var BlogHandler $blogHandler */
    protected $blogHandler;

    /** @var BlogMetaDataHandler $metaDataHandler */
    protected $metaDataHandler;

    /** @var BlogConfigHandler $blogConfigHandler */
    protected $blogConfigHandler;

    /** @var TagHandler $tagHandler */
    protected $tagHandler;

    public function __construct(BlogHandler $blogHandler, BlogMetaDataHandler $metaDataHandler, BlogConfigHandler $blogConfigHandler, TagHandler $tagHandler)
    {
        $this->blogHandler = $blogHandler;
        $this->metaDataHandler = $metaDataHandler;
        $this->blogConfigHandler = $blogConfigHandler;
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
            $blog = $this->blogHandler->store($inputs, $instanceId);
            $this->metaDataHandler->saveMetaData($blog, $inputs);

            if (isset($inputs['_tags']) && empty($inputs['_tags']) === false) {
                $this->tagHandler->set($blog->id, $inputs['_tags']);
            }
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();

        return $blog;
    }

    public function update(Request $request, $blog)
    {
        $inputs = $request->originExcept(['id', '_token']);

        XeDB::beginTransaction();
        try {
            $this->blogHandler->update($blog, $inputs);
            $this->metaDataHandler->saveMetaData($blog, $inputs);

            if (isset($inputs['_tags']) && empty($inputs['_tags']) === false) {
                $this->tagHandler->set($blog->id, $inputs['_tags']);
            }
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }

    public function delete($blog, $instanceId)
    {
        XeDB::beginTransaction();
        try {
            $blogConfig = $this->blogConfigHandler->get($instanceId);
            if ($blogConfig->get('deleteToTrash') === true) {
                $this->blogHandler->trashBlog($blog);
            } else {
                $this->metaDataHandler->deleteMetaData($blog);
                $this->blogHandler->dropBlog($blog);
            }
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }
}