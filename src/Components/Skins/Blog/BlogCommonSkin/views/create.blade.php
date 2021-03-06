{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}
{{ XeFrontend::js('plugins/xe_blog/assets/js/boldjournal-block-style.js')->load() }}

@expose_route('banner::group.edit')

<form method="post" class="metabox-base-form" action="{{ route('blog.store') }}" enctype="multipart/form-data">
    {!! csrf_field() !!}

    {!! editor('xe_blog', [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <div id="metaboxes" style="display: none;">
        <input type="text" name="title" value="{{ Request::old('title') }}">
        <input type="text" name="sub_title" value="{{ Request::old('sub_title') }}">
        <input type="text" name="blog_status" value="public">
        <input type="text" name="slug" value="{{ Request::old('slug') }}">
        <input type="text" name="published_at" value="{{ Request::old('published_at') }}">
        <input type="text" name="thumbnail">
        <input type="text" name="cover_image">
        <textarea name="summary">{{ Request::old('summary') }}</textarea>
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
                        {!! df_create($dynamicField->getConfig()->get('group'), $dynamicField->getConfig()->get('id'), Request::all()) !!}
                    @endif
                @endforeach
            </div>
        </div>
    </section>
</form>

<div id="meta-sidebar-container" style="display: none;">
    <form class="metabox-location-side" onsubmit="return false">
        <div class="components-panel__body is-opened">
            <h2 class="components-panel__body-title">
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">글 설정</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">부제목</span>
                    <input type="text" id="__f-sub-title" class="components-text-control__input" name="f_sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">Slug</span>
                    <input type="text" id="__f-slug" class="components-text-control__input" name="f_slug" value="{{ Request::old('slug') }}">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">요약 (optional)</span>
                    <textarea id="__f-summary" class="components-text-control__input" name="f_summary">{{ Request::old('summary') }}</textarea>
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">공개 속성</span>
                    <select id="__f-blog-status" class="components-select-control__input">
                        <option value="public">공개</option>
                        <option value="private">비공개</option>
                        <option value="temp">임시</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="components-panel__body is-opened">
            <h2 class="components-panel__body-title">
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">Taxonomy & 태그</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    @foreach ($taxonomies as $taxonomy)
                        <label class="components-base-control__label">
                            {{ xe_trans($taxonomy->name) }}
                            @if (app('xe.blog.taxonomyHandler')->getTaxonomyInstanceConfig($taxonomy->id)->get('require', false) === true)
                                <em style="color: red;">(필수)</em>
                            @endif
                        </label>
                        <div class="__taxonomy-field">
                            <div class="components-base-control__field" @if (app('xe.blog.taxonomyHandler')->getTaxonomyInstanceConfig($taxonomy->id)->get('require', false) === true) data-required="required" @endif data-required-title="{{ xe_trans($taxonomy->name) }}">
                                {!! uio('uiobject/board@select', [
                                    'name' => app('xe.blog.taxonomyHandler')->getTaxonomyItemAttributeName($taxonomy->id),
                                    'label' => xe_trans($taxonomy->name),
                                    'items' => app('xe.blog.taxonomyHandler')->getTaxonomyItems($taxonomy->id),
                                ]) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">태그</span>
                    {!! uio('uiobject/board@tag') !!}
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
                    {!! uio('formMedialibraryImage', [ 'name' => 'thumbnail', 'field' => '#metaboxes [name=thumbnail]', 'limit' => 1 ]) !!}
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">커버 이미지</span>
                    {!! uio('formMedialibraryImage', [ 'name' => 'cover_image', 'field' => '#metaboxes [name=cover_image]', 'limit' => 1 ]) !!}
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
                "id": "meta-sidebar-container",
                "title": "documents"
            }],
            "normal": [{
                id: 'meta-normal-container',
                title: 'dynamic fileds'
            }],
            "advanced": []
        });

        wp.data.dispatch('core/edit-post').toggleFeature('welcomeGuide')

        var $fieldPublishedAt = $metaboxes.find('[name=published_at]')
        var $fieldTitle = $metaboxes.find('[name=title]')

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
        $(document).on('change', '#__f-title', function () {
            $metaboxes.find('[name=title]').val($(this).val())
        })
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
        var $btnSubmit = $('<div style="padding: 0 4px;"><button type="button" class="components-button is-button is-primary is-large"> 저장 </button></div>')
        $('.edit-post-header__settings').prepend($btnSubmit)
        $btnSubmit.on('click', function () {
            $('.__btn-submit').click()
        })
    })
</script>
