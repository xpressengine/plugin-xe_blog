<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Category\CategoryHandler;
use Xpressengine\Category\Models\CategoryItem;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\BlogTaxonomy;

class BlogTaxonomyHandler implements Searchable
{
    const TAXONOMY_CONFIG_NAME = 'taxonomy';

    /** @var BlogConfigHandler $blogConfigHandler  */
    protected $blogConfigHandler;

    /** @var CategoryHandler $categoryHandler */
    protected $categoryHandler;

    protected $taxonomyDefaultConfig = [
        'require' => false,
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
            $taxonomyItem = $this->categoryHandler->createCate($inputs);

            $taxonomyInstanceConfigName = $this->getTaxonomyInstanceConfigName($taxonomyItem->id);
            $this->blogConfigHandler->addConfig($inputs, $taxonomyInstanceConfigName);
        } catch (\Exception $e) {
            \XeDB::rollback();

            throw $e;
        }
        \XeDB::commit();

        return $taxonomyItem;
    }

    public function getTaxonomies()
    {
        $taxonomyDefaultConfigName = $this->blogConfigHandler->getConfigName(self::TAXONOMY_CONFIG_NAME);
        $taxonomyDefaultConfig = $this->blogConfigHandler->get($taxonomyDefaultConfigName);

        $taxonomyInstanceConfigs = app('xe.config')->children($taxonomyDefaultConfig);
        $taxonomyIds = [];
        array_walk($taxonomyInstanceConfigs, function ($taxonomyInstanceConfig) use (&$taxonomyIds) {
            $taxonomyInstanceConfigName = array_get($taxonomyInstanceConfig->getAttributes(), 'name', '');
            $taxonomyConfigNamePrefix = $this->blogConfigHandler->getConfigName(self::TAXONOMY_CONFIG_NAME);
            $taxonomyIds[] = str_replace($taxonomyConfigNamePrefix . '.', '', $taxonomyInstanceConfigName);
        });

        $taxonomies = [];
        foreach ($taxonomyIds as $taxonomyId) {
            $taxonomies[] = $this->categoryHandler->cates()->find($taxonomyId);
        }

        return $taxonomies;
    }

    public function getTaxonomyGroups()
    {
        $taxonomyGroups = [];
        $taxonomies = $this->getTaxonomies();
        foreach ($taxonomies as $taxonomy) {
            $items = [];
            $taxonomyItems = CategoryItem::where('category_id', $taxonomy->id)->orderBy('ordering')->get();
            foreach ($taxonomyItems as $taxonomyItem) {
                $items[] = [
                    'value' => $taxonomyItem->id,
                    'text' => $taxonomyItem->word
                ];
            }
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
