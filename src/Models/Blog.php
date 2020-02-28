<?php

namespace Xpressengine\Plugins\XeBlog\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Xpressengine\Document\Models\Document;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\Seo\SeoUsable;
use Xpressengine\Tag\Tag;
use Xpressengine\User\Models\Guest;
use Xpressengine\User\Models\UnknownUser;

class Blog extends Document implements SeoUsable
{
    protected $casts = [
        'published_at' => 'datetime'
    ];

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

    public function scopeBlog($query)
    {
        return $query->where('type', Plugin::getId());
    }

    public function scopePublic($query)
    {
        return $query->where('status', self::STATUS_PUBLIC)
            ->where('approved', self::APPROVED_APPROVED)
            ->where('display', self::DISPLAY_VISIBLE);
    }

    public function isPublic()
    {
        return $this->status === self::STATUS_PUBLIC &&
            $this->approved === self::APPROVED_APPROVED &&
            $this->display === self::DISPLAY_VISIBLE;
    }

    public function setPublic()
    {
        $this->status = self::STATUS_PUBLIC;
        $this->approved = self::APPROVED_APPROVED;
        $this->display = self::DISPLAY_VISIBLE;

        $this->save();
    }

    public function scopePrivate($query)
    {
        return $query->where('status', self::STATUS_PRIVATE)
            ->where('approved', self::APPROVED_APPROVED)
            ->where('display', self::DISPLAY_SECRET);
    }

    public function isPrivate()
    {
        return $this->status === self::STATUS_PRIVATE &&
            $this->approved === self::APPROVED_APPROVED &&
            $this->display === self::DISPLAY_SECRET;
    }

    public function setPrivate()
    {
        $this->status = self::STATUS_PRIVATE;
        $this->approved = self::APPROVED_APPROVED;
        $this->display = self::DISPLAY_SECRET;

        $this->save();
    }

    public function scopeTemp($query)
    {
        return $query->where('status', self::STATUS_TEMP)
            ->where('approved', self::APPROVED_WAITING)
            ->where('display', self::DISPLAY_HIDDEN);
    }

    public function isTemp()
    {
        return $this->status === self::STATUS_TEMP &&
            $this->approved === self::APPROVED_WAITING &&
            $this->display === self::DISPLAY_HIDDEN;
    }

    public function setTemp()
    {
        $this->status = self::STATUS_TEMP;
        $this->approved = self::APPROVED_WAITING;
        $this->display = self::DISPLAY_HIDDEN;

        $this->save();
    }

    public function scopePublishReserved($query)
    {
        return $query->where('published_at', '>', date('Y-m-d H:i:s'));
    }

    public function isPublishReserved()
    {
        return $this->published_at > date('Y-m-d H:i:s');
    }

    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', date('Y-m-d H:i:s'));
    }

    public function isPublished()
    {
        return $this->published_at <= date('Y-m-d H:i:s');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', static::STATUS_PUBLIC)
            ->where('display', '<>', static::DISPLAY_HIDDEN)
            ->where('approved', static::APPROVED_APPROVED)
            ->where('published_at', '<=', date('Y-m-d H:i:s'));
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

    public function taxonomy()
    {
        return $this->hasMany(BlogTaxonomy::class, 'blog_id', 'id');
    }

    public function slug()
    {
        return $this->hasOne(BlogSlug::class, 'target_id', 'id');
    }

    public function isNew($hour)
    {
        return strtotime($this->getAttribute('published_at')) + ($hour * 3600) > time();
    }
}
