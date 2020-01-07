<?php

namespace Xpressengine\Plugins\Post\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Xpressengine\Document\Models\Document;

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

    public function metaData()
    {
        return $this->hasMany(MetaData::class, 'post_id', 'id');
    }

    public function getSubTitle()
    {
        $subTitle = $this->metaData()->where('type', MetaData::TYPE_SUB_TITLE)->get()->first();

        if ($subTitle !== null) {
            return $subTitle['meta_data'];
        }

        return '';
    }
}
