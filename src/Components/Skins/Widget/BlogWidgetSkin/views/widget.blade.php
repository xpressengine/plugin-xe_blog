@php
    use Xpressengine\Plugins\XeBlog\Plugin;
@endphp

<div class="widget-bold-list-card">
    <div class="row">
        @foreach ($blogs as $blog)
            @php
                $thumbnail = $metaDataHandler->getThumbnail($blog);
                $taxonomyText = '';
                foreach ($taxonomies as $taxonomy) {
                    $taxonomyItem = $taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id);
                    if ($taxonomyItem !== null) {
                        $taxonomyText .= xe_trans($taxonomyItem->word) . ' / ';
                    }
                }
                $taxonomyText = rtrim($taxonomyText, ' / ');
            @endphp
            <div class="col-lg-4">
                <div class="card card--ground">
                    <a href="{{ route('blog.show', ['blogId' => $blog->id]) }}" class="card-img__box card-img__box--no-before">
                        <div class="card-img-top" style="padding-top:100%; @if ($thumbnail !== null) background-image: url({{ $thumbnail->url() }}); @else background-image: url({{ Plugin::asset('src/Components/Skins/Widget/BlogWidgetSkin/assets/img/img-no-image.jpg') }}); @endif"></div>
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('blog.show', ['blogId' => $blog->id]) }}">{{ $blog->title }}</a>
                        </h5>
                        <p class="card-text">{{ $taxonomyText }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="bold-paging mt-5">
    {!! $blogs->render('xe_blog::src.Components.Skins.Widget.BlogWidgetSkin.views.paginate') !!}
</div>
