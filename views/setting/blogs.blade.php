<a href="{{ route('blog.create') }}" class="xe-btn xe-btn-positive" target="_blank">생성</a>

<table class="xe-table">
    <thead>
    <tr>
        <th>제목</th>
        <th>작성자</th>
        <th>생성일</th>
    </tr>
    </thead>
    <tbody>
        @foreach($blogs as $blog)
            <tr>
                <td @if ($metaDataHandler->getBackgroundColor($blog) !== null) style="background-color: {{ $metaDataHandler->getBackgroundColor($blog) }}" @endif>
                    <a href="{{ route('blog.show', ['id' => $blog->id]) }}" target="_blank">
                        @if ($metaDataHandler->getThumbnail($blog) !== null)
                            <img src="{{ $metaDataHandler->getThumbnail($blog, 'spill', 'S')->url() }}">
                        @else
                            {{ $blog->title }}
                        @endif
                    </a>
                </td>
                <td>
                    @if ($blog->user !== null)
                        {{ $blog->user->getDisplayName() }}
                    @else
                        guest
                    @endif
                </td>
                <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
