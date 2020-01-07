{{ $item->title }}
{{ $metaDataHandler->getSubTitle($item) }}

@if ($metaDataHandler->getCoverImage($item) !== null)
    <img src="{{ $metaDataHandler->getCoverImage($item)->url() }}">
@endif

{!! $item->content() !!}

<a href="{{ instance_route('edit', ['id' => $item->id], $instanceId) }}" class="xe-btn xe-btn-danger">수정</a>
<a href="{{ instance_route('index', [], $instanceId) }}" class="xe-btn">목록</a>
