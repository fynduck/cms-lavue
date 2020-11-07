@component('mail::message')
# {{ $data['title'] }}

@component('mail::table')
<tr>
<td>
<table>
@foreach($data as $key => $item)
@if($key !== 'title')
<tr>
<td>
{{ $item['title'] }}:
</td>
<td>
{{ $item['value'] }}
</td>
</tr>
@endif
@endforeach
</table>
</td>
</tr>
@endcomponent

@endcomponent
