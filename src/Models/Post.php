<?php

namespace Xpressengine\Plugins\Post\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Xpressengine\Document\Models\Document;
use Xpressengine\Tag\Tag;

class Post extends Document
{
    use SoftDeletes;

    public function content()
    {
        return compile($this->instance_id, $this->content, $this->format === static::FORMAT_HTML);
    }

    public function scopeVisible($query)
    {
        return $query->where('status', static::STATUS_PUBLIC)
            ->where('display', '<>', static::DISPLAY_HIDDEN)
            ->where(function ($query) {
                $query->where('approved', static::APPROVED_APPROVED)
                    ->orWhere($this->getTable() . '.user_id', auth()->id());
            })
            ->where(function ($query) {
                $query->where('published_at', null)
                    ->orWhere('published_at', '<=', date('Y-m-d H:i:s'));
            });
    }

    public function getMetaDataQuery($metaDataType)
    {
        return $this->hasMany(MetaData::class, 'post_id', 'id')->where('type', $metaDataType);
    }

    public function favorite()
    {
        return $this->hasMany(PostFavorite::class, 'post_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'taggables', 'taggable_id', 'tag_id');
    }
}
