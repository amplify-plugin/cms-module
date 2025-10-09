@foreach (($tags ?? []) as $data)
<meta{!! $arrayToHtmlAttributes($data) !!}/>
@endforeach
<link rel="canonical" href="{{ url()->current() }}" />

