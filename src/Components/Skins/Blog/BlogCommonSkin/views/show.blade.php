@php
    use Xpressengine\Permission\Instance;
    use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;

    $coverImage = $metaDataHandler->getCoverImage($blog);
    $blogPermissionHandler = app('xe.blog.permissionHandler');
    $manageAble = \Gate::allows(BlogPermissionHandler::ACTION_CREATE, new Instance($blogPermissionHandler->getPermissionName()));
@endphp

@if ($coverImage !== null)
    <div class="row">
        <div class="col-lg-12">
            <section class="section-widget-bold-xe-blog-board-top-image">
                <div class="widget-bold-xe-blog-board-top-image-item" style="background-image: url({{ $coverImage->url() }})">
                    <span class="blind">페이지 대표 이미지</span>
                </div>
            </section>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-lg-10 offset-lg-1 @if ($coverImage !== null) section-widget-bold-xe-blog-board-title-wrap @endif">
        <section class="section-widget-bold-xe-blog-board-title">
            <div class="widget-bold-xe-blog-board-title">
                <h1 class="widget-bold-xe-blog-board-title__title">{{ $blog->title }}</h1>
                <p class="widget-bold-xe-blog-board-title__text">{{ $metaDataHandler->getSubTitle($blog) }}</p>

                <div class="widget-bold-xe-blog-board-title-meta widget-bold-xe-blog-board-title-meta--sns">
                    <ul class="widget-bold-xe-blog-board-title-meta-list">
                        @if (Auth::check() === true)
                            <li>
                                <button type="button" data-blog_id="{{ $blog->id }}" data-url="{{ route('blog.favorite') }}" class="__favorite-button widget-bold-xe-blog-board-title-meta__item widget-bold-xe-blog-board-title-meta__item--bookmark @if ($favoriteHandler->isFavoriteBlog($blog, auth()->user()) === true) on @endif">
                                    <span class="blind">북마크</span>
                                </button>
                                <span class="widget-bold-xe-blog-board-title-meta__bar"></span>
                            </li>
                        @endif

                        <li>
                            <a href="{{ 'https://twitter.com/intent/tweet?url=' . urlencode(url()->current()) }}" class="widget-bold-xe-blog-board-title-meta__item widget-bold-xe-blog-board-title-meta__item--twitter" target="_blank">
                                <span class="blind">트위터</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ 'http://www.facebook.com/sharer/sharer.php?u=' . urlencode(url()->current()) }}" class="widget-bold-xe-blog-board-title-meta__item widget-bold-xe-blog-board-title-meta__item--facebook" target="_blank">
                                <span class="blind">페이스북</span>
                            </a>
                        </li>
                        <li>
                            <button type="button" data-clipboard-text="{{ url()->current() }}" class="__copy_url widget-bold-xe-blog-board-title-meta__item widget-bold-xe-blog-board-title-meta__item--share">
                                <span class="blind">공유하기</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="widget-bold-xe-blog-board-title-meta widget-bold-xe-blog-board-title-meta--more-info">
                    <ul class="widget-bold-xe-blog-board-title-meta-list">
                        <li class="date">{{ $blog->published_at->format('Y.m.d') }}</li>
                    </ul>
                </div>

                @if (Auth::check() === true)
                    <button type="button" data-blog_id="{{ $blog->id }}" data-url="{{ route('blog.favorite') }}" class="__favorite-button widget-bold-xe-blog-board-title-meta__item widget-bold-xe-blog-board-title-meta__item--mobile widget-bold-xe-blog-board-title-meta__item--bookmark @if ($favoriteHandler->isFavoriteBlog($blog, auth()->user()) === true) on @endif">
                        <span class="blind">모바일 북마크</span>
                    </button>
                @endif
            </div>
        </section>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <section>
            {!! $blog->content() !!}
        </section>

        @if ($manageAble === true)
            <div class="text-right">
                <a href="{{ route('blog.edit', ['blogId' => $blog->id]) }}" class="btn btn-bj btn-bj--black">수정</a>
                <form method="post" action="{{ route('blog.delete', ['blogId' => $blog->id]) }}" style="display: inline-block;">
                    {!! csrf_field() !!}
                    <button id="delete-btn" type="button" class="btn btn-bj btn-bj--black">삭제</button>
                </form>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <section class="section-widget-bold-xe-blog-board-meta-mobile">
            <div class="widget-bold-xe-blog-board-meta-mobile widget-bold-xe-blog-board-meta-mobile--more-info">
                <ul class="widget-bold-xe-blog-board-meta-mobile-list">
                    <li class="date">{{ $blog->published_at->format('Y.m.d') }}</li>
                </ul>
            </div>
        </section>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <section class="section-widget-bold-xe-blog-board-meta-mobile">
            <div class="widget-bold-xe-blog-board-meta-mobile widget-bold-xe-blog-board-meta-mobile--sns">
                <ul class="widget-bold-xe-blog-board-meta-mobile-list">
                    <li>
                        <a href="{{ 'https://twitter.com/intent/tweet?url=' . urlencode(url()->current()) }}" class="widget-bold-xe-blog-board-meta-mobile__item widget-bold-xe-blog-board-meta-mobile__item--twitter" target="_blank">
                            <span class="blind">트위터</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ 'http://www.facebook.com/sharer/sharer.php?u=' . urlencode(url()->current()) }}" class="widget-bold-xe-blog-board-meta-mobile__item widget-bold-xe-blog-board-meta-mobile__item--facebook" target="_blank">
                            <span class="blind">페이스북</span>
                        </a>
                    </li>
                    <li>
                        <button type="button" data-clipboard-text="{{ url()->current() }}" class="__copy_url widget-bold-xe-blog-board-meta-mobile__item widget-bold-xe-blog-board-meta-mobile__item--share">
                            <span class="blind">공유하기</span>
                        </button>
                    </li>
                </ul>
            </div>
        </section>
    </div>
</div>

@if ($relationBlog !== null)
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <section class="section-widget-bold-xe-blog-related-article">
                <div class="widget-bold-xe-blog-related-article clearfix">
                    <a href="{{ route('blog.show', ['blogId' => $relationBlog->id]) }}" class="widget-item-link">
                        <span class="widget-item__meta-text">Related Article</span>
                        <strong class="widget-item__title">{{ $relationBlog->title }}</strong>
                        <p class="widget-item__text">{{ $metaDataHandler->getSubTitle($relationBlog) }}</p>
                    </a>
                </div>
            </section>
        </div>
    </div>
@endif

@foreach ($dynamicFields as $dynamicField)
    @if ($dynamicField->getConfig()->get('use') === true)
        {!! $dynamicField->getSkin()->show($blog->getAttributes()) !!}
    @endif
@endforeach

<script>
    XE.DynamicLoadManager.jsLoad('https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js', function () {
        var clipboard = new ClipboardJS('.__copy_url');
        clipboard.on('success', function () {
            alert('주소가 복사되었습니다.')
        })
    })

    $(function () {
        $('.__favorite-button').click(function () {
            var url = $(this).data('url')
            var blogId = $(this).data('blog_id')
            var _this = $(this)

            XE.ajax({
                type: 'post',
                dataType: 'json',
                data: {blogId: blogId},
                url: url,
                success: function(response) {
                    if (response.favorite === true) {
                        $('.__favorite-button').addClass('on')
                    } else {
                        $('.__favorite-button').removeClass('on')
                    }
                }
            });
        })

        $('#delete-btn').click(function (e) {
            if (confirm('삭제하시겠습니까?') == true) {
                $(this).closest('form').submit()
            }
        })
    })
</script>
