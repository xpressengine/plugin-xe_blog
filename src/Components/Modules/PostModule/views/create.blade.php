<form method="post" action="{{ instance_route('store', [], $instanceId) }}" enctype="multipart/form-data">
    <input type="text" name="title" value="{{ Request::old('title') }}" placeholder="title">
    <input type="text" name="sub_title" value="{{ Request::old('sub_title') }}" placeholder="sub_title">

    {!! editor($instanceId, [
        'content' => Request::old('content'),
        'cover' => true,
    ]) !!}

    <hr>

    <input type="text" name="published_at" value="{{ Request::old('published_at') }}" placeholder="예약 발행(Y-m-d H:i:s)">

    <hr>

    <button type="submit" class="xe-btn">저장</button>
</form>
