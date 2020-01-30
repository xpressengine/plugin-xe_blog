<div class="row">
    <div class="col-sm-12">
        <div class="panel-group">
            <div class="panel">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h3 class="panel-title">{{ xe_trans('xe_blog::manageBlog') }}</h3>
                    </div>

                    <div class="pull-right">
                        <a href="{{ route('blog.create') }}" class="xe-btn xe-btn-positive" target="_blank">생성</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>제목</th>
                            <th>작성자</th>
                            @foreach ($taxonomies as $taxonomy)
                                <th>{{ xe_trans($taxonomy->name) }}</th>
                            @endforeach
                            <th>생성일</th>
                            <th>수정일</th>
                            <th>발행일</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($blogs as $blog)
                            <tr>
                                <td>
                                    <a href="{{ route('blog.show', ['id' => $blog->id]) }}" target="_blank">{{ $blog->title }}</a>
                                </td>
                                <td>
                                    @if ($blog->user !== null)
                                        {{ $blog->user->getDisplayName() }}
                                    @else
                                        guest
                                    @endif
                                </td>
                                @foreach ($taxonomies as $taxonomy)
                                    @if ($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id) !== null)
                                        <td>{{ xe_trans($taxonomyHandler->getBlogTaxonomyItem($blog, $taxonomy->id)->word) }}</td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $blog->updated_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $blog->published_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">
                    <div class="pull-left">
                        <nav>
                            {!! $blogs->render() !!}
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
