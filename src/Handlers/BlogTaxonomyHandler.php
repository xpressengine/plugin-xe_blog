<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Xpressengine\Category\CategoryHandler;
use Xpressengine\Category\Models\CategoryItem;
use Xpressengine\Plugins\XeBlog\Interfaces\Jsonable;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Models\BlogTaxonomy;

class BlogTaxonomyHandler implements Searchable, Jsonable
{
    const TAXONOMY_CONFIG_NAME = 'taxonomy';

    const TAXONOMY_ITEM_ID_ATTRIBUTE_NAME_PREFIX = 'taxonomy_item_id_';

    /** @var BlogConfigHandler $blogConfigHandler  */
    protected $blogConfigHandler;

    /** @var CategoryHandler $categoryHandler */
    protected $categoryHandler;

    protected $taxonomyDefaultConfig = [
        'taxonomy_id' => '',
        'require' => false,
        'use_slug' => false,
        'slug_url' => null
    ];

    public function __construct()
    {
        $this->blogConfigHandler = app('xe.blog.configHandler');
        $this->categoryHandler  = app('xe.category');

        if ($this->blogConfigHandler->get($this->blogConfigHandler->getConfigName(self::TAXONOMY_CONFIG_NAME)) === null) {
            $this->createTaxonomyDefaultConfig();
        }
    }

    public function getItems($query, array $attributes)
    {
        if (isset($attributes['taxonomy_id']) === true) {
            $query = $query->whereHas('taxonomy', function ($query) use ($attributes) {
                $query->where('taxonomy_id', $attributes['taxonomy_id']);
            });
        }

        if (isset($attributes['taxonomy_item_id']) === true) {
            $query = $query->whereHas('taxonomy', function ($query) use ($attributes) {
                $query->where('taxonomy_item_id', $attributes['taxonomy_item_id']);
            });
        }

        return $query;
    }

    public function getTypeName()
    {
        return 'taxonomy';
    }

    public function getJsonData(Blog $blog)
    {
        $taxonomyData = [];
        $taxonomies = $this->getTaxonomies();
        foreach ($taxonomies as $taxonomy) {
            $blogTaxonomyItem = $this->getBlogTaxonomyItem($blog, $taxonomy->id);
            if ($blogTaxonomyItem === null) {
                continue;
            }

            $taxonomyData[$taxonomy->id] = xe_trans($blogTaxonomyItem->word);
        }

        return $taxonomyData;
    }

    public function getBlogTaxonomyItem($blog, $taxonomyId)
    {
        $taxonomy = $blog->taxonomy()->where('taxonomy_id', $taxonomyId)->get()->first();

        $taxonomyItem = null;
        if ($taxonomy !== null) {
            $taxonomyItem = $taxonomy->taxonomyItem;
        }

        return $taxonomyItem;
    }

    private function createTaxonomyDefaultConfig()
    {
        $taxonomyConfigName = $this->blogConfigHandler->getConfigName(self::TAXONOMY_CONFIG_NAME);
        $this->blogConfigHandler->addConfig($this->taxonomyDefaultConfig, $taxonomyConfigName);
    }

    public function getTaxonomyInstanceConfigName($taxonomyId)
    {
        return $this->blogConfigHandler->getConfigName(sprintf('%s.%s', self::TAXONOMY_CONFIG_NAME, $taxonomyId));
    }

    public function createTaxonomy($inputs)
    {
        \XeDB::beginTransaction();
        try {
            $slugUrl = isset($inputs['slug_url']) === true? $inputs['slug_url'] : null;
            if ($slugUrl !== null && \XeMenu::items()->query()->where('url', $slugUrl)->exists()) {
                throw new HttpException(422, xe_trans('xe::menuItemUrlAlreadyExists'));
            }

            $taxonomyUseUrls = $this->getTaxonomyUseUrls();
            if (in_array($slugUrl, $taxonomyUseUrls) === true) {
                throw new HttpException(422, xe_trans('xe::menuItemUrlAlreadyExists'));
            }

            $taxonomyItem = $this->categoryHandler->createCate($inputs);

            $taxonomyInstanceConfigName = $this->getTaxonomyInstanceConfigName($taxonomyItem->id);

            $inputs['taxonomy_id'] = $taxonomyItem->id;
            $inputs['use_slug'] = isset($inputs['use_slug']);
            $this->blogConfigHandler->addConfig($inputs, $taxonomyInstanceConfigName);
        } catch (\Exception $e) {
            \XeDB::rollback();

            throw $e;
        }
        \XeDB::commit();

        return $taxonomyItem;
    }

    public function deleteTaxonomy($taxonomyId)
    {
        \XeDB::beginTransaction();
        try {
            $taxonomy = \XeCategory::cates()->find($taxonomyId);
            $this->categoryHandler->deleteCate($taxonomy);

            $taxonomyConfig = $this->getTaxonomyInstanceConfig($taxonomyId);
            $this->blogConfigHandler->removeConfig($taxonomyConfig);
        } catch (\Exception $e) {
            \XeDB::rollback();

            throw $e;
        }
        \XeDB::commit();
    }

    public function getTaxonomyInstanceConfigs()
    {
        $taxonomyDefaultConfigName = $this->blogConfigHandler->getConfigName(BlogTaxonomyHandler::TAXONOMY_CONFIG_NAME);
        $taxonomyDefaultConfig = $this->blogConfigHandler->get($taxonomyDefaultConfigName);

        return app('xe.config')->children($taxonomyDefaultConfig);
    }

    public function getTaxonomyItemAttributeName($taxonomyId)
    {
        return self::TAXONOMY_ITEM_ID_ATTRIBUTE_NAME_PREFIX . $taxonomyId;
    }

    public function getTaxonomyInstanceConfig($taxonomyId)
    {
        return $this->blogConfigHandler->get($this->getTaxonomyInstanceConfigName($taxonomyId));
    }

    public function updateTaxonomyInstanceConfig($taxonomyConfig, $attributes)
    {
        if ($attributes['use_slug'] === 'true') {
            $taxonomyUseUrls = $this->getTaxonomyUseUrls();

            $slugUrl = isset($attributes['slug_url']) === true? $attributes['slug_url'] : null;
            if ($slugUrl !== null && \XeMenu::items()->query()->where('url', $slugUrl)->exists()) {
                throw new HttpException(422, xe_trans('xe::menuItemUrlAlreadyExists'));
            }

            if (isset($attributes[$taxonomyConfig->get('taxonomy_id')]) === true) {
                unset($attributes[$taxonomyConfig->get('taxonomy_id')]);
            }

            if (in_array($slugUrl, $taxonomyUseUrls) === true) {
                throw new HttpException(422, xe_trans('xe::menuItemUrlAlreadyExists'));
            }
        }

        foreach ($attributes as $key => $value) {
            $taxonomyConfig->set($key, $value);
        }

        $this->blogConfigHandler->modifyConfig($taxonomyConfig);
    }

    public function getTaxonomyUseUrls()
    {
        $taxonomyInstanceConfigs = $this->getTaxonomyInstanceConfigs();

        $taxonomyUseUrls = [];
        foreach ($taxonomyInstanceConfigs as $taxonomyInstanceConfig) {
            if ($taxonomyInstanceConfig->get('use_slug') === true && $taxonomyInstanceConfig->get('slug_url') !== null) {
                $taxonomyUseUrls[$taxonomyInstanceConfig->get('taxonomy_id')] = $taxonomyInstanceConfig->get('slug_url');
            }
        }

        return $taxonomyUseUrls;
    }

    public function getTaxonomyItem($taxonomyId)
    {
        return $this->categoryHandler->cates()->find($taxonomyId);
    }

    public function getTaxonomies()
    {
        $taxonomyDefaultConfigName = $this->blogConfigHandler->getConfigName(self::TAXONOMY_CONFIG_NAME);
        $taxonomyDefaultConfig = $this->blogConfigHandler->get($taxonomyDefaultConfigName);

        $taxonomyInstanceConfigs = app('xe.config')->children($taxonomyDefaultConfig);
        $taxonomyIds = [];
        array_walk($taxonomyInstanceConfigs, function ($taxonomyInstanceConfig) use (&$taxonomyIds) {
            $taxonomyIds[] = $taxonomyInstanceConfig->get('taxonomy_id');
        });

        $taxonomies = [];
        foreach ($taxonomyIds as $taxonomyId) {
            $taxonomies[] = $this->categoryHandler->cates()->find($taxonomyId);
        }

        return $taxonomies;
    }

    public function getTaxonomyItems($taxonomyId)
    {
        $items = [];
        $taxonomyItems = CategoryItem::where('category_id', $taxonomyId)->orderBy('ordering')->get();
        foreach ($taxonomyItems as $taxonomyItem) {
            $items[] = [
                'value' => $taxonomyItem->id,
                'text' => $taxonomyItem->word
            ];
        }

        return $items;
    }

    public function storeTaxonomy($blog, $inputs)
    {
        $taxonomies = $this->getTaxonomies();
        foreach ($taxonomies as $taxonomy) {
            $taxonomyAttributeName = $this->getTaxonomyItemAttributeName($taxonomy->id);
            if (isset($inputs[$taxonomyAttributeName]) === false) {
                continue;
            }
            $taxonomyItemId = $inputs[$taxonomyAttributeName];
            if ($taxonomyItemId === null || $taxonomyItemId === '') {
                continue;
            }

            $taxonomyItem = CategoryItem::find($taxonomyItemId);
            if ($taxonomyItem === null) {
                continue;
            }

            $newBlogTaxonomy = new BlogTaxonomy();
            $newBlogTaxonomy->fill([
                'blog_id' => $blog->id,
                'taxonomy_id' => $taxonomyItem->category_id,
                'taxonomy_item_id' => $taxonomyItemId
            ]);
            $newBlogTaxonomy->save();
        }
    }
}
