<?php

namespace Xpressengine\Plugins\Post\Handlers;

use Xpressengine\Plugins\Post\Models\PostFavorite;

class PostFavoriteHandler
{
    public function isFavoritePost($post, $user)
    {
        return $post->favorite()->where('user_id', $user->getId())->exists();
    }

    public function setFavoritePost($post, $user)
    {
        $newFavorite = new PostFavorite();
        $newFavorite->fill([
            'post_id' => $post->id,
            'user_id' => $user->getId()
        ]);
        $newFavorite->save();

        return $newFavorite;
    }

    public function unsetFavoritePost($post, $user)
    {
        $post->favorite()->where('user_id', $user->getId())->delete();
    }
}
