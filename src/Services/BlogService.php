<?php

namespace Xpressengine\Plugins\XeBlog\Services;

use XeDB;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogSlugHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Interfaces\Jsonable;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Tag\TagHandler;

class BlogService
{
    const DEFAULT_PER_PAGE = 12;

    /** @var BlogHandler $blogHandler */
    protected $blogHandler;

    /** @var BlogMetaDataHandler $metaDataHandler */
    protected $metaDataHandler;

    /** @var BlogConfigHandler $blogConfigHandler */
    protected $blogConfigHandler;

    /** @var TagHandler $tagHandler */
    protected $tagHandler;

    /** @var BlogTaxonomyHandler $taxonomyHandler */
    protected $taxonomyHandler;

    /** @var BlogSlugHandler $blogSlugHandler */
    protected $blogSlugHandler;

    protected $handlers = [];

    public function __construct(
        BlogHandler $blogHandler,
        BlogMetaDataHandler $metaDataHandler,
        BlogConfigHandler $blogConfigHandler,
        TagHandler $tagHandler,
        BlogTaxonomyHandler $taxonomyHandler,
        BlogSlugHandler $blogSlugHandler
    ) {
        $this->blogHandler = $blogHandler;
        $this->metaDataHandler = $metaDataHandler;
        $this->blogConfigHandler = $blogConfigHandler;
        $this->tagHandler = $tagHandler;
        $this->taxonomyHandler = $taxonomyHandler;
        $this->blogSlugHandler = $blogSlugHandler;
    }

    public function addHandlers($handler)
    {
        $this->handlers[] = $handler;
    }

    public function getItemsQuery(array $attributes)
    {
        $query = Blog::division(Plugin::getId())->where('instance_id', Plugin::getId());

        foreach ($this->handlers as $handler) {
            if ($handler instanceof Searchable) {
                $query = $handler->getItems($query, $attributes);
            }
        }

        return $query;
    }

    public function getItemsJson(array $attributes)
    {
        $perPage = self::DEFAULT_PER_PAGE;
        if (isset($attributes['perPage']) === true) {
            $perPage = $attributes['perPage'];
        }

        $currentPage = 1;
        if (isset($attributes['page']) === true) {
            $currentPage = $attributes['page'];
        }

        $query = $this->getItemsQuery($attributes);
        $query->orderByDesc('created_at');

        $items = $query->paginate($perPage, ['*'], 'page', $currentPage)->appends(array_except($attributes, 'page'));

        $json['page'] = [
            'count' => $items->count(),
            'currentPage' => $items->currentPage(),
            'hasMorePages' => $items->hasMorePages(),
            'lastPage' => $items->lastPage(),
            'perPage' => $items->perPage(),
            'totalCount' => $items->total(),
        ];

        $items->each(function ($blog) use (&$json) {
            $blogData = [];
            foreach ($this->handlers as $handler) {
                if ($handler instanceof Jsonable) {
                    $blogData[$handler->getTypeName()] = $handler->getJsonData($blog);
                }
            }
            $json['items'][] = $blogData;
        });

        return $json;
    }

    public function getItems(array $attributes)
    {
        $query = $this->getItemsQuery($attributes);

        $query->orderByDesc('created_at');

        $perPage = self::DEFAULT_PER_PAGE;
        if (isset($attributes['perPage']) === true) {
            $perPage = $attributes['perPage'];
        }

        return $query->paginate($perPage, ['*'], 'page')->appends(array_except($attributes,'page'));
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
            $this->taxonomyHandler->storeTaxonomy($blog, $inputs);
            $this->blogSlugHandler->storeSlug($blog, $inputs);

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
