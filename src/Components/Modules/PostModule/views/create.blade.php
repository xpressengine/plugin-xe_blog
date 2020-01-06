<form method="post" action="{{ instance_route('store', [], $instanceId) }}">
    <input type="text" name="title" value="{{ Request::old('title') }}">

    {!! editor($instanceId, [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <input type="text" name="published_at" value="{{ Request::old('published_at') }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <button type="submit" class="xe-btn">저장</button>
</form>
