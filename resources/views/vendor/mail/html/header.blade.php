@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('assets/logos/SGc.png') }}" class="logo" alt="Easy Gest Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
