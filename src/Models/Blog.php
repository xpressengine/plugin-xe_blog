<?php

namespace Xpressengine\Plugins\XeBlog\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Xpressengine\Document\Models\Document;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Seo\SeoUsable;
use Xpressengine\Tag\Tag;
use Xpressengine\User\Models\Guest;
use Xpressengine\User\Models\UnknownUser;

class Blog extends Document implements SeoUsable
{
    use SoftDeletes;

    protected $canonical;

    public function getTitle()
    {
        $title = str_replace('"', '\"', $this->getAttribute('title'));

        return $title;
    }

    public function getDescription()
    {
        return str_replace(
            ['"', "\n"],
            ['\"', ''],
            $this->getAttribute('pure_content')
        );
    }

    public function getKeyword()
    {
        return [];
    }

    public function getUrl()
    {
        return $this->canonical;
    }

    public function getAuthor()
    {
        if ($this->user !== null) {
            return $this->user;
        } elseif ($this->isGuest() === true) {
            return new Guest;
        } else {
            return new UnknownUser;
        }
    }

    public function getImages()
    {
        $images = [];
        $blogMetaData = new BlogMetaDataHandler();

        if ($thumbnail = $blogMetaData->getThumbnail($this)) {
            $images[] = $thumbnail;
        }

        if ($coverImage = $blogMetaData->getCoverImage($this)) {
            $images[] = $coverImage;
        }

        return $images;
    }

    public function setCanonical($url)
    {
        $this->canonical = $url;

        return $this;
    }

    public function isGuest()
    {
        return $this->getAttribute('user_type') === self::USER_TYPE_GUEST;
    }

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
        return $this->hasMany(BlogMetaData::class, 'blog_id', 'id')->where('type', $metaDataType);
    }

    public function favorite()
    {
        return $this->hasMany(BlogFavorite::class, 'blog_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'taggables', 'taggable_id', 'tag_id');
    }
}
