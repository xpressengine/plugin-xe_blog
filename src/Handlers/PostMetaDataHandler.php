<?php

namespace Xpressengine\Plugins\Post\Handlers;

use XeMedia;
use XeStorage;
use Xpressengine\Plugins\Post\Models\MetaData;

class PostMetaDataHandler
{
    const UPLOAD_PATH = 'public/post';

    public function getSubTitle($post)
    {
        $subTitleMetaData = $post->getMetaDataQuery(MetaData::TYPE_SUB_TITLE)->get()->first();
        if ($subTitleMetaData === null) {
            return '';
        }

        return $subTitleMetaData['meta_data'];
    }

    public function getThumbnail($post, $thumbnailType = 'spill', $dimension = 'L')
    {
        $thumbnailMetaData = $post->getMetaDataQuery(MetaData::TYPE_COVER_THUMBNAIL)->get()->first();
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

    public function getCoverImage($post)
    {
        $coverMetaData = $post->getMetaDataQuery(MetaData::TYPE_COVER_IMAGE)->get()->first();

        if ($coverMetaData === null) {
             return null;
        }

        $coverImage = XeStorage::find($coverMetaData['meta_data']);

        return $coverImage;
    }

    public function getBackgroundColor($post)
    {
        $backgroundColorMetaData = $post->getMetaDataQuery(MetaData::TYPE_BACKGROUND_COLOR)->get()->first();
        if ($backgroundColorMetaData === null) {
            return '';
        }

        return $backgroundColorMetaData['meta_data'];
    }

    public function saveMetaData($post, $inputs)
    {
        $this->saveSubTitle($post, $inputs);
        $this->saveThumbnail($post, $inputs);
        $this->saveCoverImage($post, $inputs);
        $this->saveBackgroundColor($post, $inputs);
    }

    protected function saveSubTitle($post, $inputs)
    {
        if (isset($inputs['sub_title']) === true && $inputs['sub_title'] !== '') {
            $subTitle = $post->getMetaDataQuery(MetaData::TYPE_SUB_TITLE)->get()->first();

            if ($subTitle === null) {
                $subTitle = new MetaData();
                $subTitle->fill([
                    'post_id' => $post->id,
                    'type' => MetaData::TYPE_SUB_TITLE,
                    'meta_data' => $inputs['sub_title']
                ]);
            } else {
                $subTitle['meta_data'] = $inputs['sub_title'];
            }

            $subTitle->save();
        }
    }

    protected function saveThumbnail($post, $inputs, $thumbnailType = 'spill')
    {
        if (isset($inputs['thumbnail']) === true) {
            $thumbnailFile = $inputs['thumbnail'];

            $file = XeStorage::upload($thumbnailFile, self::UPLOAD_PATH);

            if (XeMedia::is($file) === true) {
                $media = XeMedia::make($file);
                $thumbnail = XeMedia::createThumbnails($media, $thumbnailType);
            }

            $thumbnailMetaData = new MetaData();
            $thumbnailMetaData->fill([
                'post_id' => $post->id,
                'type' => MetaData::TYPE_COVER_THUMBNAIL,
                'meta_data' => $file->id
            ]);
            $thumbnailMetaData->save();
        }
    }

    protected function saveCoverImage($post, $inputs)
    {
        if (isset($inputs['cover_image']) === true) {
            $coverImageFile = $inputs['cover_image'];

            $file = XeStorage::upload($coverImageFile, self::UPLOAD_PATH);

            $coverImageMetaData = new MetaData();
            $coverImageMetaData->fill([
                'post_id' => $post->id,
                'type' => MetaData::TYPE_COVER_IMAGE,
                'meta_data' => $file->id
            ]);
            $coverImageMetaData->save();
        }
    }

    protected function saveBackgroundColor($post, $inputs)
    {
        if (isset($inputs['background_color']) === true && $inputs['background_color'] !== '') {
            $backgroundColorMetaData = $post->getMetaDataQuery(MetaData::TYPE_BACKGROUND_COLOR)->get()->first();

            if ($backgroundColorMetaData === null) {
                $backgroundColorMetaData = new MetaData();
                $backgroundColorMetaData->fill([
                    'post_id' => $post->id,
                    'type' => MetaData::TYPE_BACKGROUND_COLOR,
                    'meta_data' => $inputs['background_color']
                ]);
            } else {
                $backgroundColorMetaData['meta_data'] = $inputs['background_color'];
            }

            $backgroundColorMetaData->save();
        }
    }

    public function deleteMetaData($post)
    {
        $this->deleteSubTitle($post);
        $this->deleteThumbnail($post);
        $this->deleteCoverImage($post);
        $this->deleteBackgroundColor($post);
    }

    protected function deleteSubTitle($post)
    {
        $post->getMetaDataQuery(MetaData::TYPE_SUB_TITLE)->delete();
    }

    protected function deleteThumbnail($post)
    {
        $thumbnailMetaData = $post->getMetaDataQuery(MetaData::TYPE_COVER_THUMBNAIL)->get()->first();
        if ($thumbnailMetaData === null) {
            return;
        }

        $file = XeStorage::find($thumbnailMetaData['meta_data']);
        XeStorage::delete($file);

        $post->getMetaDataQuery(MetaData::TYPE_COVER_THUMBNAIL)->delete();
    }

    protected function deleteCoverImage($post)
    {
        $coverImageMetaData = $post->getMetaDataQuery(MetaData::TYPE_COVER_IMAGE)->get()->first();
        if ($coverImageMetaData === null) {
            return;
        }

        $file = XeStorage::find($coverImageMetaData['meta_data']);
        XeStorage::delete($file);

        $post->getMetaDataQuery(MetaData::TYPE_COVER_IMAGE)->delete();
    }

    protected function deleteBackgroundColor($post)
    {
        $post->getMetaDataQuery(MetaData::TYPE_BACKGROUND_COLOR)->delete();
    }
}
