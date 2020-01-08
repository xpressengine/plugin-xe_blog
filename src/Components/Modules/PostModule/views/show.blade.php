{{ $item->title }}
{{ $metaDataHandler->getSubTitle($item) }}

@if ($metaDataHandler->getCoverImage($item) !== null)
    <img src="{{ $metaDataHandler->getCoverImage($item)->url() }}">
@endif

{!! $item->content() !!}

@foreach ($item->tags->toArray() as $tag)
    <span class="xe-btn xe-btn-danger-outline">#{{ $tag['word'] }}</span>
@endforeach

<a href="{{ instance_route('edit', ['id' => $item->id], $instanceId) }}" class="xe-btn xe-btn-positive">수정</a>
<form method="post" action="{{ instance_route('delete', ['id' => $item->id], $instanceId) }}">
    {!! csrf_field() !!}
    <button type="submit" class="xe-btn xe-btn-danger">삭제</button>
</form>
<a href="{{ instance_route('index', [], $instanceId) }}" class="xe-btn">목록</a>
