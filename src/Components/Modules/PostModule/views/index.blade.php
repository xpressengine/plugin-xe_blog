<table class="xe-table">
    @foreach ($items as $item)
        <tr>
            <td>
                <a href="#" data-url="{{ instance_route('favorite', ['postId' => $item->id], $instanceId) }}"
                   data-post_id="{{ $item->id }}"
                   class="xe-btn __post_favorite @if (count($item->favorite) > 0) xe-btn-danger @endif ">즐겨찾기
                </a>
            </td>
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

<script>
    $(function () {
        $('.__post_favorite').click(function () {
            var url = $(this).data('url')
            var postId = $(this).data('post_id')
            var _this = $(this)

            XE.ajax({
                type: 'post',
                dataType: 'json',
                data: {postId: postId},
                url: url,
                success: function(response) {
                    if (response.favorite === true) {
                        _this.addClass('xe-btn-danger')
                    } else {
                        _this.removeClass('xe-btn-danger')
                    }
                }
            });
        })
    })
</script>
