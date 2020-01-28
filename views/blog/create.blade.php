{{ XeFrontend::css('plugins/xe_blog/assets/block-editor-dynamic-fields.css')->load() }}

<form method="post" action="{{ route('blog.store') }}" enctype="multipart/form-data">
    <input type="text" name="title" value="{{ Request::old('title') }}" placeholder="title">
    <input type="text" name="sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">

    {!! editor('xe_blog', [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <hr>
    <input type="text" name="published_at" value="{{ Request::old('published_at') }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>
    <span>썸네일</span>
    <input type="file" name="thumbnail">

    <hr>
    <span>커버 이미지</span>
    <input type="file" name="cover_image">

    <hr>
    <span>배경 컬러</span>
    <input type="text" name="background_color">

    <hr>
    <span>태그</span>
    {!! uio('uiobject/board@tag') !!}

    <hr>
    <span>Taxonomy</span>
    @foreach ($taxonomyGroups as $taxonomyName => $taxonomy)
        {!! uio('uiobject/board@select', [
            'name' => 'taxonomy_item_id[]',
            'label' => xe_trans($taxonomyName),
            'items' => $taxonomy,
        ]) !!}
    @endforeach

    <hr>
    <span>Slug</span>
    <input type="text" name="slug">

    <hr>
    <section class="section-blog-block-editor-field">
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

    <button type="submit" class="xe-btn">저장</button>
</form>

{{ XeFrontend::js('assets/core/xe-ui-component/js/xe-page.js')->load() }}
<div class="form-group">
    <span>템플릿 저장</span>
    <div>
        <input id="template_title" type="text" name="template_title" placeholder="템플릿 이름">
        <button id="btn_template_store" type="button">템플릿 저장</button>
    </div>

    <div>
        <button id="btn_template_index" type="button" data-url="{{ route('blog.template.get_items') }}">템플릿 보기</button>
        <div id="template_index"></div>
    </div>
</div>

<script>
    $(function () {
        $('#btn_template_store').click(function () {
            var content = window.wp.data.select('core/editor').getEditedPostContent()
            var title = $('#template_title').val()

            XE.post('/xe_blog/template/store', {
                'title': title,
                'content': content
            }).then(response => {
                if (response.data.result == true) {
                    alert('템플릿이 저장되었습니다.')
                }
            })
        })

        $('#btn_template_index').click(function () {
            var url = $(this).data('url')

            XE.get(url).then(response => {
                console.log(response)
            })
        })
    })
</script>
