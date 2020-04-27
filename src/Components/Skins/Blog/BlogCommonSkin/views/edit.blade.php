{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}
{{ XeFrontend::js('plugins/xe_blog/assets/js/boldjournal-block-style.js')->load() }}

<form method="post" action="{{ route('blog.update') }}" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <input type="hidden" name="blogId" value="{{ $blog->id }}">

    {!! editor('xe_blog', [
        'content' => $blog->content,
        'cover' => true,
        'title' => $blog->title,
        'publishedAt' => $blog->published_at,
    ]) !!}

    <div id="metaboxes" style="display: none;">
        <input type="text" name="title" value="{{ $blog->title }}">
        <input type="text" name="sub_title" value="{{ $metaDataHandler->getSubTitle($blog) }}">
        @if ($blog->isPublic() === true)
            <input type="text" name="blog_status" value="public">
        @endif
        @if ($blog->isPrivate() === true)
            <input type="text" name="blog_status" value="private">
        @endif
        @if ($blog->isTemp() === true)
            <input type="text" name="blog_status" value="temp">
        @endif
        <input type="text" name="slug" @if ($blog->slug !== null) value="{{ $blog->slug['slug'] }}" @endif>
        <input type="text" name="published_at" value="{{ $blog->published_at }}">
        <input type="text" name="thumbnail">
        <input type="text" name="cover_image">
        <textarea name="summary">{{ $metaDataHandler->getSummary($blog) }}</textarea>
        <button type="submit" class="__btn-submit">저장</button>
    </div>

    <section class="section-blog-block-editor-field">
        <div class="blog-block-editor-field__title-box">
            <h2 class="blog-block-editor-field__title">{{ xe_trans('xe::dynamicField') }} <i class="xi-angle-down"></i></h2>
        </div>

        <div class="blog-block-editor-filed-content">
            <div class="inner">
                @foreach ($dynamicFields as $dynamicField)
                    @if ($dynamicField->getConfig()->get('use') === true)
                        {!! $dynamicField->getSkin()->edit($blog->getAttributes()) !!}
                    @endif
                @endforeach
            </div>
        </div>
    </section>
</form>

<div id="sidebar-container" style="display: none;">
    <form class="metabox-location-side" onsubmit="return false">
        <div class="components-panel__body is-opened">
            <h2 class="components-panel__body-title">
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">글 설정</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">부제목</span>
                    <input type="text" id="__f-sub-title" class="components-text-control__input" name="f_sub_title" value="{{ $metaDataHandler->getSubTitle($blog) }}" placeholder="sub_title">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">Slug</span>
                    <input type="text" id="__f-slug" class="components-text-control__input" name="f_slug" @if ($blog->slug !== null) value="{{ $blog->slug['slug'] }}" @endif>
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">요약 (optional)</span>
                    <textarea id="__f-summary" class="components-text-control__input" name="f_summary">{{ $metaDataHandler->getSummary($blog) }}</textarea>
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">공개 속성</span>
                    <select id="__f-blog-status" class="components-select-control__input">
                        <option value="public" @if ($blog->isPublic() === true) selected @endif>공개</option>
                        <option value="private" @if ($blog->isPrivate() === true) selected @endif>비공개</option>
                        <option value="temp" @if ($blog->isTemp() === true) selected @endif>임시</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="components-panel__body is-opened">
            <h2 class="components-panel__body-title">
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">Taxonomy &amp; 태그</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    @foreach ($taxonomies as $taxonomy)
                        <div class="__taxonomy-field">
                            <label class="components-base-control__label">
                                {{ xe_trans($taxonomy->name) }}
                                @if (app('xe.blog.taxonomyHandler')->getTaxonomyInstanceConfig($taxonomy->id)->get('require', false) === true)
                                    <em style="color: red;">(필수)</em>
                                @endif
                            </label>
                            <div class="components-base-control__field"@if (app('xe.blog.taxonomyHandler')->getTaxonomyInstanceConfig($taxonomy->id)->get('require', false) === true) data-required="required" @endif data-required-title="{{ xe_trans($taxonomy->name) }}">
                                {!! uio('uiobject/board@select', [
                                    'name' => app('xe.blog.taxonomyHandler')->getTaxonomyItemAttributeName($taxonomy->id),
                                    'label' => xe_trans($taxonomy->name),
                                    'items' => app('xe.blog.taxonomyHandler')->getTaxonomyItems($taxonomy->id),
                                    'value' => app('xe.blog.taxonomyHandler')->getBlogTaxonomyItem($blog, $taxonomy->id)['id']
                                ]) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">태그</span>
                    {!! uio('uiobject/board@tag', [
                        'tags' => $blog->tags->toArray()
                    ]) !!}
                </div>
            </div>
        </div>

        <div class="components-panel__body is-opened">
            <h2 class="components-panel__body-title">
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">이미지</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">썸네일</span>
                    @php
                        $thumbnail = $metaDataHandler->getThumbnail($blog);
                        $files = [];
                        if ($thumbnail !== null) {
                            $files = [[
                                'file_id' => $thumbnail['id'],
                                'mime' => $thumbnail['mime'],
                                'preview' => $thumbnail->url()
                            ]];
                        }
                    @endphp
                    {!! uio('formMedialibraryImage', [ 'valueTarget' => 'file_id', 'field' => '#metaboxes [name=thumbnail]', 'name' => 'thumbnail', 'limit' => 1, 'files' => $files ]) !!}
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">커버 이미지</span>
                    @php
                        $cover_image = $metaDataHandler->getCoverImage($blog);
                        $files = [];
                        if ($cover_image !== null) {
                            $files = [[
                                'file_id' => $cover_image['id'],
                                'mime' => $cover_image['mime'],
                                'preview' => $cover_image->url()
                            ]];
                        }
                    @endphp
                    {!! uio('formMedialibraryImage', [ 'valueTarget' => 'file_id', 'field' => '#metaboxes [name=cover_image]', 'name' => 'cover_image', 'limit' => 1, 'files' => $files ]) !!}
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function () {
        var $metaboxes = $('#metaboxes')

        wp.data.dispatch('core/edit-post').setAvailableMetaBoxesPerLocation({
            "side": [{
                "id": "sidebar-container",
                "title": "documents"
            }],
            "normal": [],
            "advanced": []
        });

        wp.data.dispatch('core/edit-post').toggleFeature('welcomeGuide')

        var $fieldPublishedAt = $('[name=published_at]')
        var $fieldTitle = $('[name=title]')

        wp.data.subscribe(function (select) {
            var title = wp.data.select('core/editor').getEditedPostAttribute('title')
            var publishedAt = wp.data.select('core/editor').getEditedPostAttribute('date')
            var momentDate = window.XE.moment(publishedAt)

            if (typeof title !== 'undefined' && title) {
                $fieldTitle.val(title)
            }
            if (momentDate.isValid()) {
                $fieldPublishedAt.val(momentDate.format('YYYY-MM-DD HH:mm:ss'))
            }
        })

        // 폼채우기
        $(document).on('change', '#__f-sub-title', function () {
            $metaboxes.find('[name=sub_title]').val($(this).val())
        })
        $(document).on('change', '#__f-blog-status', function () {
            $metaboxes.find('[name=blog_status]').val($(this).val())
        })
        $(document).on('change', '#__f-slug', function () {
            $metaboxes.find('[name=slug]').val($(this).val())
        })
        $(document).on('change', '#__f-summary', function () {
            $metaboxes.find('[name=summary]').val($(this).val())
        })

        $('.blog-block-editor-field__title-box').on('click', function () {
            $('.section-blog-block-editor-field').toggleClass('section-blog-block-editor-field--open')
            $('body, html').animate({
                scrollTop: $('.blog-block-editor-field__title-box').offset().top
            });
        })
        var $btnSubmit = $('<div style="padding: 0 4px;"><button type="submit" class="components-button is-button is-primary is-large"> 저장 </button></div>')
        $('.edit-post-header__settings').prepend($btnSubmit)
        $btnSubmit.on('click', function () {
            $('.__btn-submit').click()
        })
    })
</script>
