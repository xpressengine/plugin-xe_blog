{{ \XeFrontend::css([
    'plugins/xe_blog/src/Components/Skins/Widget/BlogWidgetSkin/assets/css/widget-xe-blog-list.css'
])->load() }}

<section class="section-widget-bold-xe-blog-list-story">
    <div class="widget-bold-xe-blog-category clearfix">
        <ul class="widget-bold-xe-blog-category-list">
            @if (isset($_config['targetTaxonomyId']) === true && $_config['targetTaxonomyId'] !== '')
                <li class="on">
                    <a href="#" class="widget-bold-xe-blog-category-list__link">All</a>
                </li>

                @foreach ($taxonomyHandler->getTaxonomyItems($_config['targetTaxonomyId']) as $taxonomyItem)
                    <li>
                        <a href="#" class="widget-bold-xe-blog-category-list__link">{{ xe_trans($taxonomyItem['text']) }}</a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    <div class="widget-bold-xe-blog-card">
        <ul class="widget-bold-xe-blog-card-list">
            @foreach ($blogs as $blog)
                @php
                    $thumbnail = $metaDataHandler->getThumbnail($blog);

                    $taxonomyText = '';
                    foreach ($taxonomies as $taxonomy) {
                        if ($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id) !== null) {
                            $taxonomyText .= xe_trans($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id)->word) . ' / ';
                        }
                    }
                    $taxonomyText = rtrim($taxonomyText , ' / ');
                @endphp
                <li class="">
                    <div class="widget-bold-xe-blog-card-item widget-bold-xe-blog-card-item--story @if ($thumbnail !== null) widget-bold-xe-blog-card-item--image @endif ">
                        <div class="widget-bold-xe-blog-card-item-meta">
                                <span class="widget-bold-xe-blog-card-item-meta__category">{{ $taxonomyText }}</span>

                                <button type="button" data-blog_id="{{ $blog->id }}" data-url="{{ route('blog.favorite') }}" class="__favorite-button widget-bold-xe-blog-card-item-meta__button-wish widget-bold-xe-blog-card-item-meta__button-wish--black @if ($blog->favorite->count() > 0) on @endif">
                                    <span class="blind">찜하기</span>
                                </button>
                        </div>
                        <a href="{{ route('blog.show', ['blogId' => $blog->id]) }}" class="widget-bold-xe-blog-card-item-content-box ">
                            <div class="widget-bold-xe-blog-card-item-content">
                                @if ($thumbnail !== null)
                                    <div class="widget-bold-xe-blog-card-item-content__image-wrap" style="background-color: {{ $metaDataHandler->getBackgroundColor($blog) }} ;">
                                        <div class="widget-bold-xe-blog-card-item-content__image-box">
                                            <div class="widget-bold-xe-blog-card-item-content__image" style="background-image: url({{ $thumbnail->url() }});"></div>
                                        </div>
                                    </div>
                                @endif

                                <div class="widget-bold-xe-blog-card-item-content__hover">
                                    <div class="widget-bold-xe-blog-card-item-content__hover-dimmed"></div>
                                    <div class="widget-bold-xe-blog-card-item-content__hover-content">
                                        <p class="widget-bold-xe-blog-card-item-content__hover-title">{{ $blog->title }}</p>
                                        <p class="widget-bold-xe-blog-card-item-content__hover-text">{{ $metaDataHandler->getSubTitle($blog) }}</p>
                                    </div>
                                </div>
                            </div>

                            @if ($blog->isNew($blogConfig->get('newBlogTime')) === true)
                                <div class="widget-bold-xe-blog-card-item-tag widget-bold-xe-blog-card-item-tag--new">
                                    <span class="blind">NEW</span>
                                </div>
                            @endif
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="text-right">
        <!-- <a href="{{ route('blog.create') }}" class="xe-btn xe-btn-positive" target="_blank">글쓰기</a> -->
        <a href="{{ route('blog.create') }}" class="btn btn-bj btn-bj--black">
            글쓰기
        </a>
    </div>

</section>

<script>
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
                        _this.addClass('on')
                    } else {
                        _this.removeClass('on')
                    }
                }
            });
        })
    })
</script>
