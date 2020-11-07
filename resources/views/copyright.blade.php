<section class="copyright">
    <div class="container text-center">
        @php
            $urlP = 'sozdanie-saytov';
        if(config('app.locale_prefix') == 'ro')
            $urlP = 'crearea-website';
        if(config('app.locale_prefix') == 'en')
            $urlP = 'website-development'
        @endphp

        <a href="http://punct.md/{{ App::getLocale() }}/{{ $urlP }}"
           target="_blank">@lang('global.created_site')</a>
        <a href="http://punct.md/{{ App::getLocale() }}" target="_blank">&laquo;Punct Technology&raquo;</a>
    </div>
</section>
