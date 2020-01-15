<div class="panel">
    <table class="xe-table">
        <tr>
            <th>제목</th>
            <th>작성일</th>
        </tr>

        @foreach ($blogs as $blog)
            <tr>
                <td>{{ $blog->title }}</td>
                <td>{{ $blog->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
    </table>
</div>
