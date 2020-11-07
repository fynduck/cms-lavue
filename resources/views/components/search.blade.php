<div class="search_form" itemscope itemtype="https://schema.org/WebSite">
    <meta itemprop="url" content="{{ config('app.url') }}"/>
    <form action="{{ route('pages', $urlsPages['search'] ?? '') }}" id="form_search" class="d-none d-sm-block m-md-2"
          itemprop="potentialAction" itemscope itemtype="https://schema.org/SearchAction">
        <meta itemprop="target" content="{{ route('pages', $urlsPages['search'] ?? '') }}?q={q}"/>
        <div class="input-group">
            <input type="search" name="q" class="form-control" required itemprop="query-input" autocomplete="off"
                   placeholder="@lang('global.what_to_search')" value="{{ request()->get('q') }}">
            <div class="input-group-append">
                <button class="btn btn_search" type="submit">
                    <i class="icofont-search-2"></i>
                </button>
            </div>
        </div>
    </form>
    <button class="btn btn_search d-sm-none" type="button" data-toggle="modal" data-target="#mobile_search">
        <i class="icofont-search-2"></i>
    </button>
</div>