@foreach (($tags ?? []) as $data)
<meta{!! $arrayToHtmlAttributes($data) !!}/>
@endforeach

