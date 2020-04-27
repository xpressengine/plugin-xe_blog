<?php

namespace Xpressengine\Plugins\XeBlog\Components\Skins\Blog\BlogCommonSkin;

use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Skin\GenericSkin;

class BlogCommonSkin extends GenericSkin
{
    protected static $path = 'xe_blog/src/Components/Skins/Blog/BlogCommonSkin';
    
    public function render()
    {
        \XeFrontend::css([
            self::asset('css/bootstrap.css'),
            self::asset('css/boldjournal-widget.css'),
            self::asset('css/widget-xe-blog-board.css')
        ])->load();
        
        return parent::render();
    }

    public function show($view)
    {
        $data = $this->data;

        $blog = $this->data['blog'];

        $blogTags = \XeTag::fetchByTaggable($blog->id);
        $blogTagIds = $blogTags->map(function ($tag) {
            return $tag['id'];
        });

        $targetBlogIds = [];
        foreach ($blogTagIds as $blogTagId) {
            $targetTaggables = \XeTag::fetchByTag($blogTagId);
            $targetBlogIds = $targetTaggables->map(function ($taggable) {
                return $taggable->taggable_id;
            });
        }

        $relationBlogs = Blog::where('id', '<>', $blog->id)->whereIn('id', $targetBlogIds)->get();
        if ($relationBlogs->count() > 0) {
            $data['relationBlog'] = $relationBlogs->random();
        } else {
            $data['relationBlog'] = null;
        }

        $this->setData($data);

        return $this->renderBlade($view);
    }
}
