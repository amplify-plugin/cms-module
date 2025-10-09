@foreach (($tags ?? []) as $data)
    <meta{!! $arrayToHtmlAttributes($data) !!}/>
@endforeach
    <link rel="canonical" href="{{ url()->current() }}"/>
@foreach(config('backpack.crud.locales', []) as $code => $lang)
    <link rel="alternate" hreflang="{{$code}}" href="{{ config('app.url') }}"/>
@endforeach

