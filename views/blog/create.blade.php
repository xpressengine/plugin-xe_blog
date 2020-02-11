{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}
{{ XeFrontend::js('plugins/xe_blog/assets/js/boldjournal-block-style.js')->load() }}

<form method="post" class="metabox-base-form" action="{{ route('blog.store') }}" enctype="multipart/form-data" style="padding-bottom: 40px;">
    {!! editor('xe_blog', [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <br>
    <br>

    <div id="metaboxes" style="padding: 40px; display: none;">
        <input type="text" name="title" value="{{ Request::old('title') }}">
        <input type="text" name="sub_title" value="{{ Request::old('sub_title') }}">
        <input type="text" name="blog_status" value="public">
        <input type="text" name="background_color" value="{{ Request::old('background_color') }}">
        <input type="text" name="slug" value="{{ Request::old('slug') }}">
        <input type="text" name="published_at" value="{{ Request::old('published_at') }}">
    </div>

    <fieldset style="margin: 40px;">
        <div class="xe-form-group">
            <label>썸네일</label>
            <input class="xe-form-control" type="file" name="thumbnail">
        </div>

        <div class="xe-form-group">
            <label>커버 이미지</label>
            <input class="xe-form-control" type="file" name="cover_image">
        </div>

        {{--TODO 스킨으로 이동--}}
        <div class="xe-form-group">
            <input type="hidden" class="xe-form-control" name="gallery_group_id">
            <button id="createBannerGroup" type="button" class="xe-btn" data-url="{{ route('boldjournal.gallery.store_banner_group') }}">배너 생성</button>
{{--            <a href="{{ route('boldjournal.gallery.edit_banner_group', ['groupId' => $groupId]) }}">수정</a>--}}
        </div>
    </fieldset>

    <section class="section-blog-block-editor-field" style="margin: 40px 0;">
        <div class="blog-block-editor-field__title-box">
            <h2 class="blog-block-editor-field__title">{{ xe_trans('xe::dynamicField') }}</h2>
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

    <div style="padding: 40px;">
        <button type="submit" class="pull-right xe-btn xe-btn-lg xe-btn-primary"> 저장 </button>
    </div>
</form>

<div id="meta-sidebar-container" style="display: none;">
    <form class="metabox-location-side" onsubmit="return false">
        <div class="components-panel__body is-opened">
            <h2 class="components-panel__body-title">
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">글 설정</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">제목</span>
                    <input type="text" id="__f-title" class="components-text-control__input" name="f_title" value="{{ Request::old('title') }}" placeholder="title">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">부제목</span>
                    <input type="text" id="__f-sub-title" class="components-text-control__input" name="f_sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">
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
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">카테고리 &amp; 태그</button>
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
                            <div class="components-base-control__field">
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
                <button type="button" aria-expanded="true" class="components-button components-panel__body-toggle">기타</button>
            </h2>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">배경 컬러</span>
                    <input type="text" id="__f-background-color" class="components-text-control__input" name="f_background_color" value="{{ Request::old('background_color') }}">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <span class="components-base-control__label">Slug</span>
                    <input type="text" id="__f-slug" class="components-text-control__input" name="f_slug" value="{{ Request::old('slug') }}">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function () {
        //배너 만들기 ajax
        $('#createBannerGroup').click(function () {
            var url = $(this).data('url')

            XE.ajax({
                type: 'post',
                dataType: 'json',
                data: {},
                url: url,
                success: function(response) {
                    $('[name=gallery_group_id]').val(response.groupId)
                }
            });
        })

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

        wp.data.subscribe(function (select) {
            var publishedAt = wp.data.select('core/editor').getEditedPostAttribute('date')
            var dateString = XE.moment(publishedAt).format('YYYY-MM-DD HH:mm:ss')
            var title = wp.data.select('core/editor').getEditedPostAttribute('title')
            var $field = $('[name=published_at]').val(dateString)
            {{-- $('[name=title]').val(title) --}}
        })

        // 폼채우기
        $(document).on('change', '#__f-title', function () {
            $('[name=title]').val($(this).val())
        })
        $(document).on('change', '#__f-sub-title', function () {
            $('[name=sub_title]').val($(this).val())
        })
        $(document).on('change', '#__f-blog-status', function () {
            $('[name=blog_status]').val($(this).val())
        })
        $(document).on('change', '#__f-background-color', function () {
            $('[name=background_color]').val($(this).val())
        })
        $(document).on('change', '#__f-slug', function () {
            $('[name=slug]').val($(this).val())
        })
    })
</script>
