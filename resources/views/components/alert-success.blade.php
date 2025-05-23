@php
    $success = session('success');
@endphp
@if ($success)
    <div class="alert alert-success">
        @if(is_array($success))
            <ul style="margin:0;padding-left:20px;">
                @foreach ($success as $msg)
                    <li>{{ $msg }}</li>
                @endforeach
            </ul>
        @else
            {{ $success }}
        @endif
    </div>
@endif
