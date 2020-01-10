<div class="panel">
    <table class="xe-table">
        <tr>
            <th>제목</th>
            <th>작성일</th>
        </tr>

        @foreach ($posts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td>{{ $post->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
    </table>
</div>
