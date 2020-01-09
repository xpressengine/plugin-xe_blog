{{ $post->title }}
{{ $metaDataHandler->getSubTitle($post) }}

@if ($metaDataHandler->getCoverImage($post) !== null)
    <img src="{{ $metaDataHandler->getCoverImage($post)->url() }}">
@endif

{!! $post->content() !!}

@foreach ($post->tags->toArray() as $tag)
    <span class="xe-btn xe-btn-danger-outline">#{{ $tag['word'] }}</span>
@endforeach

<a href="{{ route('post.edit', ['postId' => $post->id]) }}" class="xe-btn xe-btn-positive">수정</a>
<form method="post" action="{{ route('post.delete', ['postId' => $post->id]) }}">
    {!! csrf_field() !!}
    <button type="submit" class="xe-btn xe-btn-danger">삭제</button>
</form>
