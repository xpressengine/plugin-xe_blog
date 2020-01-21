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
                        <a href="#" class="widget-bold-xe-blog-category-list__link" data-taxonomy-item-id="{{ $taxonomyItem['value'] }}">{{ xe_trans($taxonomyItem['text']) }}</a>
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

    <div class="widget-bold-xe-blog-button-box widget-bold-xe-blog-button-box--more __blog-more-wrap">
        <button type="button" class="widget-bold-xe-blog-button__more __blog-more-btn">
            <span class="widget-bold-xe-blog-button__more-text">더보기</span>
            <span class="widget-bold-xe-blog-list__button-icon widget-bold-xe-blog-list__button-icon--chevron-down"></span>
        </button>
    </div>

    <div class="text-right">
        <!-- <a href="{{ route('blog.create') }}" class="xe-btn xe-btn-positive" target="_blank">글쓰기</a> -->
        <a href="{{ route('blog.create') }}" class="btn btn-bj btn-bj--black">
            글쓰기
        </a>
    </div>

</section>

@expose_route('blog.show')
@expose_route('blog.favorite')

@verbatim
    <script type="text/x-template" id="blog-list-story-item">
        {{for items ~favorite_url=favorite_url}}
            <li>
                <div class="widget-bold-xe-blog-card-item widget-bold-xe-blog-card-item--story {{if meta_data.thumbnail_url}} widget-bold-xe-blog-card-item--image {{/if}}">
                    <div class="widget-bold-xe-blog-card-item-meta">
                            <span class="widget-bold-xe-blog-card-item-meta__category">{{:taxonomy_text}}</span>

                            <button type="button" data-blog_id="{{:blog.id}}" data-url="{{:~favorite_url}}" class="__favorite-button widget-bold-xe-blog-card-item-meta__button-wish widget-bold-xe-blog-card-item-meta__button-wish--black {{if favorite.length}} on {{/if}}">
                                <span class="blind">찜하기</span>
                            </button>
                    </div>
                    <a href="{{:post_url}}" class="widget-bold-xe-blog-card-item-content-box ">
                        <div class="widget-bold-xe-blog-card-item-content">
                            {{if meta_data.thumbnail_url}}
                                <div class="widget-bold-xe-blog-card-item-content__image-wrap" {{if meta_data.background_color}} style="background-color: {{:meta_data.background_color}};" {{/if}}>
                                    <div class="widget-bold-xe-blog-card-item-content__image-box">
                                        <div class="widget-bold-xe-blog-card-item-content__image" {{if meta_data.thumbnail_url}} style="background-image: url({{:meta_data.thumbnail_url}});"{{/if}}></div>
                                    </div>
                                </div>
                            {{/if}}

                            <div class="widget-bold-xe-blog-card-item-content__hover">
                                <div class="widget-bold-xe-blog-card-item-content__hover-dimmed"></div>
                                <div class="widget-bold-xe-blog-card-item-content__hover-content">
                                    <p class="widget-bold-xe-blog-card-item-content__hover-title">{{:blog.title}}</p>
                                    <p class="widget-bold-xe-blog-card-item-content__hover-text">{{:blog.sub_title}}</p>
                                </div>
                            </div>
                        </div>

                        {{if blog.is_new}}
                            <div class="widget-bold-xe-blog-card-item-tag widget-bold-xe-blog-card-item-tag--new">
                                <span class="blind">NEW</span>
                            </div>
                        {{/if}}
                    </a>
                </div>
            </li>
        {{/for}}
    </script>
@endverbatim

{{ app('xe.frontend')->js('https://cdnjs.cloudflare.com/ajax/libs/jsrender/1.0.5/jsrender.min.js')->load() }}
<script>
    $(function () {
        var $blogListStory = $('.section-widget-bold-xe-blog-list-story')
        $blogListStory.on('click', '.__favorite-button', function () {
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

        // gallery 리스트
        var perPage = '{{ $_config['perPage'] }}'
        var filterOptions = {
            perPage: perPage,
            taxonomy_item_id: null,
            page: 1
        }
        var tmpl = $.templates('#blog-list-story-item')

        $blogListStory.on('click', '.widget-bold-xe-blog-category-list__link', function (e) {
            e.preventDefault()
            var $this = $(this)
            $this.closest('li').addClass('on').siblings().removeClass('on')

            XE.get('/xe_blog/items_json', {
                taxonomy_item_id: $this.data('taxonomy-item-id'),
                perPage: perPage
            })
            XE.get('/xe_blog/items_json', XE._.assign({}, filterOptions, { page: 1, taxonomy_item_id: $this.data('taxonomy-item-id') }))
            .then(function (res) {
                res.data.favorite_url = '{{ route('blog.favorite') }}'
                XE._.forEach(res.data.items, function (item) {
                    var taxonomies = []
                    item.post_url = XE.Router.get('blog.show').url({ blogId: item.blog.id })
                    XE._.forEach(item.taxonomy, function (taxonomy) {
                        taxonomies.push(taxonomy)
                    })
                    item.taxonomy_text = XE._.join(taxonomies, ' / ')
                })

                $blogListStory.find('.widget-bold-xe-blog-card-list').html(tmpl(res.data))
                {{-- 더보기 영역 토글 --}}
                if (!res.data.page.hasMorePages) {
                    $blogListStory.find('.__blog-more-wrap').hide()
                    filterOptions.page = 1
                } else {
                    $blogListStory.find('.__blog-more-wrap').show()
                    filterOptions.page = res.data.page.currentPage
                }
            })
        })

        $blogListStory.on('click', '.__blog-more-btn', function (e) {
            XE.get('/xe_blog/items_json', XE._.assign({}, filterOptions, { page: ++filterOptions.page}))
            .then(function (res) {
                res.data.favorite_url = '{{ route('blog.favorite') }}'
                XE._.forEach(res.data.items, function (item) {
                    var taxonomies = []
                    item.post_url = XE.Router.get('blog.show').url({ blogId: item.blog.id })
                    XE._.forEach(item.taxonomy, function (taxonomy) {
                        taxonomies.push(taxonomy)
                    })
                    item.taxonomy_text = XE._.join(taxonomies, ' / ')
                })

                $blogListStory.find('.widget-bold-xe-blog-card-list').append(tmpl(res.data))
                {{-- 더보기 영역 토글 --}}
                if (!res.data.page.hasMorePages) {
                    $blogListStory.find('.__blog-more-wrap').hide()
                    filterOptions.page = 1
                } else {
                    $blogListStory.find('.__blog-more-wrap').show()
                    filterOptions.page = res.data.page.currentPage
                }
            })
        })
    })
</script>
