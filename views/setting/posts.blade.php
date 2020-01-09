<a href="{{ route('post.create') }}" class="xe-btn xe-btn-positive" target="_blank">생성</a>

<table class="xe-table">
    <thead>
    <tr>
        <th>제목</th>
        <th>작성자</th>
        <th>생성일</th>
    </tr>
    </thead>
    <tbody>
        @foreach($posts as $post)
            <tr>
                <td @if ($metaDataHandler->getBackgroundColor($post) !== null) style="background-color: {{ $metaDataHandler->getBackgroundColor($post) }}" @endif>
                    <a href="{{ route('post.show', ['id' => $post->id]) }}" target="_blank">
                        @if ($metaDataHandler->getThumbnail($post) !== null)
                            <img src="{{ $metaDataHandler->getThumbnail($post, 'spill', 'S')->url() }}">
                        @else
                            {{ $post->title }}
                        @endif
                    </a>
                </td>
                <td>
                    @if ($post->user !== null)
                        {{ $post->user->getDisplayName() }}
                    @else
                        guest
                    @endif
                </td>
                <td>{{ $post->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
