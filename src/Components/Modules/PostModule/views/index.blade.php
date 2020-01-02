<table class="xe-table">
    @foreach ($items as $item)
        <tr>
            <td><a href="{{ instance_route('show', ['id' => $item->id], $instanceId) }}">{{ $item->pure_content }}</a></td>
            <td>{{ $item->user->getDisplayName() }}</td>
            <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
    @endforeach
</table>

<a href="{{ instance_route('create', [], $instanceId) }}">생성</a>
