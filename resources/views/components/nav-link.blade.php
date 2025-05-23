@props(['active' => false])
<li><a class="{{ $active ? 'active' : ''}}" aria-current="{{$active ? 'page' : 'false'}}" {{ $attributes }}>{{ $slot }}</a></li>