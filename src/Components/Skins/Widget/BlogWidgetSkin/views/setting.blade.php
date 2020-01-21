<div class="form-group">
    <label>글 수</label>
    <input type="number" name="take" class="form-control" value="5">
</div>

<div>
    <span>출력할 Taxonomy 선택</span>
    <select name="targetTaxonomyId">
        <option value="">선택</option>
        @foreach (app('xe.blog.taxonomyHandler')->getTaxonomies() as $taxonomy)
            <option value="{{ $taxonomy->id }}" @if (isset($config['targetTaxonomyId']) === true && $taxonomy->id == $config['targetTaxonomyId']) selected @endif>{{ xe_trans($taxonomy->name) }}</option>
        @endforeach
    </select>
</div>
