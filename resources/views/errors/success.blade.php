@if ($message = session('success'))
    <div class="alert alert-success alert-error" id="alert-message">
        <button type="button" class="close" data-dismiss="alert">x</button>
        @if(is_array($message))
            @foreach ($message as $message)
                {{ $message }}
            @endforeach
        @else
            {{ $message }}
        @endif
    </div>
@endif