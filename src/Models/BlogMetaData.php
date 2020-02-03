<?php

namespace Xpressengine\Plugins\XeBlog\Models;

use Xpressengine\Database\Eloquent\DynamicModel;

class BlogMetaData extends DynamicModel
{
    const TYPE_SUB_TITLE = 'sub_title';
    const TYPE_BACKGROUND_COLOR = 'background_color';
    const TYPE_COVER_THUMBNAIL = 'cover_thumbnail';
    const TYPE_COVER_IMAGE = 'cover_image';
    const TYPE_GALLERY_GROUP_ID = 'gallery_group_id';

    protected $table = 'blog_meta_data';

    protected $fillable = ['blog_id', 'type', 'meta_data'];

    public $timestamps = false;
}
