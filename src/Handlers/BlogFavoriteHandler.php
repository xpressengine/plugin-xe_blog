<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Plugins\XeBlog\Interfaces\Jsonable;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\Blog;
use Xpressengine\Plugins\XeBlog\Models\BlogFavorite;

class BlogFavoriteHandler implements Searchable, Jsonable
{
    public function isFavoriteBlog($blog, $user)
    {
        return $blog->favorite()->where('user_id', $user->getId())->exists();
    }

    public function setFavoriteBlog($blog, $user)
    {
        $newFavorite = new BlogFavorite();
        $newFavorite->fill([
            'blog_id' => $blog->id,
            'user_id' => $user->getId()
        ]);
        $newFavorite->save();

        return $newFavorite;
    }

    public function unsetFavoriteBlog($blog, $user)
    {
        $blog->favorite()->where('user_id', $user->getId())->delete();
    }

    public function getItems($query, array $attributes)
    {
        $query->with(['favorite' => function ($query) {
            $query->where('user_id', auth()->user()->getId());
        }]);

        if (isset($attributes['only_favorite']) === true) {
            $query->whereHas('favorite', function ($query) {
                $query->where('user_id', auth()->user()->getId());
            });
        }

        return $query;
    }

    public function getTypeName()
    {
        return 'favorite';
    }

    public function getJsonData(Blog $blog)
    {
        return $blog->favorite()->where('user_id', auth()->user()->getId())->get();
    }
}
