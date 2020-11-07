@if ($messages = session('success'))
    <div class="alert alert-success alert-error" id="alert-message">
        <button type="button" class="close" data-dismiss="alert">x</button>
        @if(is_array($messages))
            @foreach ($messages as $message)
                {{ $message }}
            @endforeach
        @else
            {{ $messages }}
        @endif
    </div>
@endif