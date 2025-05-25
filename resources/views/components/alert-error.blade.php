@php
    $error = session('error');
@endphp
@if ($error)
    <div class="alert alert-error">
        @if(is_array($error))
            <ul style="margin:0;padding-left:20px;">
                @foreach ($error as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        @else
            {{ $error }}
        @endif
    </div>
@endif
