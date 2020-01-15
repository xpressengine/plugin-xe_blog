<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Plugins\XeBlog\Models\BlogFavorite;

class BlogFavoriteHandler
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
}
