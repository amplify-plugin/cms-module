<span {!! $htmlAttributes !!}>
    {{ $entry->{$column}->format(config('amplify.basic.date_time_format', 'r')) }}
</span>
