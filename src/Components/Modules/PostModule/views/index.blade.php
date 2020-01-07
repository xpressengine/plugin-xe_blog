<table class="xe-table">
    @foreach ($items as $item)
        <tr>
            <td @if ($metaDataHandler->getBackgroundColor($item) !== null) style="background-color: {{ $metaDataHandler->getBackgroundColor($item) }}" @endif>
                <a href="{{ instance_route('show', ['id' => $item->id], $instanceId) }}">
                    @if ($metaDataHandler->getThumbnail($item) !== null)
                        <img src="{{ $metaDataHandler->getThumbnail($item, 'spill', 'S')->url() }}">
                    @else
                        {{ $item->title }}
                    @endif
                </a>
            </td>
            <td>{{ $item->user->getDisplayName() }}</td>
            <td>{{ $item->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
    @endforeach
</table>

<a href="{{ instance_route('create', [], $instanceId) }}">생성</a>
