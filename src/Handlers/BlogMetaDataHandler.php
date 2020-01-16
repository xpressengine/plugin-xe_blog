<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use XeMedia;
use XeStorage;
use Xpressengine\Plugins\XeBlog\Interfaces\Searchable;
use Xpressengine\Plugins\XeBlog\Models\BlogMetaData;

class BlogMetaDataHandler implements Searchable
{
    const UPLOAD_PATH = 'public/blog';

    public function getItems($query, array $attributes)
    {
        return $query;
    }

    public function getSubTitle($blog)
    {
        $subTitleMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_SUB_TITLE)->get()->first();
        if ($subTitleMetaData === null) {
            return '';
        }

        return $subTitleMetaData['meta_data'];
    }

    public function getThumbnail($blog, $thumbnailType = 'spill', $dimension = 'L')
    {
        $thumbnailMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->get()->first();
        if ($thumbnailMetaData === null) {
            return null;
        }

        $file = XeStorage::find($thumbnailMetaData['meta_data']);

        $thumbnail = null;
        if (XeMedia::is($file) === true) {
            $thumbnail = XeMedia::images()->getThumbnail(XeMedia::make($file), $thumbnailType, $dimension);
        }

        return $thumbnail;
    }

    public function getCoverImage($blog)
    {
        $coverMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->get()->first();

        if ($coverMetaData === null) {
             return null;
        }

        $coverImageFile = XeStorage::find($coverMetaData['meta_data']);
        $coverImage = XeMedia::make($coverImageFile);

        return $coverImage;
    }

    public function getBackgroundColor($blog)
    {
        $backgroundColorMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_BACKGROUND_COLOR)->get()->first();
        if ($backgroundColorMetaData === null) {
            return '';
        }

        return $backgroundColorMetaData['meta_data'];
    }

    public function saveMetaData($blog, $inputs)
    {
        $this->saveSubTitle($blog, $inputs);
        $this->saveThumbnail($blog, $inputs);
        $this->saveCoverImage($blog, $inputs);
        $this->saveBackgroundColor($blog, $inputs);
    }

    protected function saveSubTitle($blog, $inputs)
    {
        if (isset($inputs['sub_title']) === true && $inputs['sub_title'] !== '') {
            $subTitle = $blog->getMetaDataQuery(BlogMetaData::TYPE_SUB_TITLE)->get()->first();

            if ($subTitle === null) {
                $subTitle = new BlogMetaData();
                $subTitle->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_SUB_TITLE,
                    'meta_data' => $inputs['sub_title']
                ]);
            } else {
                $subTitle['meta_data'] = $inputs['sub_title'];
            }

            $subTitle->save();
        }
    }

    protected function saveThumbnail($blog, $inputs, $thumbnailType = 'spill')
    {
        if (isset($inputs['thumbnail']) === true) {
            $thumbnailFile = $inputs['thumbnail'];

            $file = XeStorage::upload($thumbnailFile, self::UPLOAD_PATH);

            if (XeMedia::is($file) === true) {
                $media = XeMedia::make($file);
                $thumbnail = XeMedia::createThumbnails($media, $thumbnailType);
            }

            $thumbnailMetaData = new BlogMetaData();
            $thumbnailMetaData->fill([
                'blog_id' => $blog->id,
                'type' => BlogMetaData::TYPE_COVER_THUMBNAIL,
                'meta_data' => $file->id
            ]);
            $thumbnailMetaData->save();
        }
    }

    protected function saveCoverImage($blog, $inputs)
    {
        if (isset($inputs['cover_image']) === true) {
            $coverImageFile = $inputs['cover_image'];

            $file = XeStorage::upload($coverImageFile, self::UPLOAD_PATH);

            $coverImageMetaData = new BlogMetaData();
            $coverImageMetaData->fill([
                'blog_id' => $blog->id,
                'type' => BlogMetaData::TYPE_COVER_IMAGE,
                'meta_data' => $file->id
            ]);
            $coverImageMetaData->save();
        }
    }

    protected function saveBackgroundColor($blog, $inputs)
    {
        if (isset($inputs['background_color']) === true && $inputs['background_color'] !== '') {
            $backgroundColorMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_BACKGROUND_COLOR)->get()->first();

            if ($backgroundColorMetaData === null) {
                $backgroundColorMetaData = new BlogMetaData();
                $backgroundColorMetaData->fill([
                    'blog_id' => $blog->id,
                    'type' => BlogMetaData::TYPE_BACKGROUND_COLOR,
                    'meta_data' => $inputs['background_color']
                ]);
            } else {
                $backgroundColorMetaData['meta_data'] = $inputs['background_color'];
            }

            $backgroundColorMetaData->save();
        }
    }

    public function deleteMetaData($blog)
    {
        $this->deleteSubTitle($blog);
        $this->deleteThumbnail($blog);
        $this->deleteCoverImage($blog);
        $this->deleteBackgroundColor($blog);
    }

    protected function deleteSubTitle($blog)
    {
        $blog->getMetaDataQuery(BlogMetaData::TYPE_SUB_TITLE)->delete();
    }

    protected function deleteThumbnail($blog)
    {
        $thumbnailMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->get()->first();
        if ($thumbnailMetaData === null) {
            return;
        }

        $file = XeStorage::find($thumbnailMetaData['meta_data']);
        XeStorage::delete($file);

        $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_THUMBNAIL)->delete();
    }

    protected function deleteCoverImage($blog)
    {
        $coverImageMetaData = $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->get()->first();
        if ($coverImageMetaData === null) {
            return;
        }

        $file = XeStorage::find($coverImageMetaData['meta_data']);
        XeStorage::delete($file);

        $blog->getMetaDataQuery(BlogMetaData::TYPE_COVER_IMAGE)->delete();
    }

    protected function deleteBackgroundColor($blog)
    {
        $blog->getMetaDataQuery(BlogMetaData::TYPE_BACKGROUND_COLOR)->delete();
    }
}
