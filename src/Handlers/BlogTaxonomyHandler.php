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
        $query->with('taxonomy');

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

    public function getTaxonomyInstanceConfigs()
    {
        $taxonomyDefaultConfigName = $this->blogConfigHandler->getConfigName(BlogTaxonomyHandler::TAXONOMY_CONFIG_NAME);
        $taxonomyDefaultConfig = $this->blogConfigHandler->get($taxonomyDefaultConfigName);

        return app('xe.config')->children($taxonomyDefaultConfig);
    }

    public function getTaxonomyUseUrls()
    {
        $taxonomyInstanceConfigs = $this->getTaxonomyInstanceConfigs();

        $taxonomyUseUrls = [];
        foreach ($taxonomyInstanceConfigs as $taxonomyInstanceConfig) {
            if ($taxonomyInstanceConfig->get('use_slug') === true && $taxonomyInstanceConfig->get('slug_url') !== null) {
                $taxonomyUseUrls[] = $taxonomyInstanceConfig->get('slug_url');
            }
        }

        return $taxonomyUseUrls;
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

    public function getTaxonomyGroups()
    {
        $taxonomyGroups = [];
        $taxonomies = $this->getTaxonomies();
        foreach ($taxonomies as $taxonomy) {
            $items = $this->getTaxonomyItems($taxonomy->id);

            $taxonomyGroups[$taxonomy->name] = $items;
        }

        return $taxonomyGroups;
    }

    public function storeTaxonomy($blog, $inputs)
    {
        if (isset($inputs['taxonomy_item_id']) === false || empty($inputs['taxonomy_item_id']) === true) {
            return;
        }

        $taxonomyIds = $inputs['taxonomy_item_id'];
        if (is_array($taxonomyIds) === false) {
            $taxonomyIds = [$taxonomyIds];
        }

        $taxonomyIds = array_filter($taxonomyIds, function ($taxonomyId) {
            if ($taxonomyId !== '' || $taxonomyId !== null) {
                return $taxonomyId;
            }
        });

        foreach ($taxonomyIds as $taxonomyId) {
            $taxonomyItem = CategoryItem::find($taxonomyId);
            if ($taxonomyItem === null) {
                continue;
            }

            $newBlogTaxonomy = new BlogTaxonomy();
            $newBlogTaxonomy->fill([
                'blog_id' => $blog->id,
                'taxonomy_id' => $taxonomyItem->category_id,
                'taxonomy_item_id' => $taxonomyId
            ]);
            $newBlogTaxonomy->save();
        }
    }
}
