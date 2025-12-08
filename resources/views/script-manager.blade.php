@foreach (($scripts ?? []) as $script)
    {!!  $script->scripts !!}
@endforeach
