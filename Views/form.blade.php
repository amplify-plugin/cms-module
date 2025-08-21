<div {!! $htmlAttributes !!}>
    {!! \Form::open([
        'url' => route('frontend.form-submit', $form->code),
        'method' => 'post', 'autocomplete' =>"on",
        'spellcheck' => 'false', 'files' => true
        ]) !!}

    <x-honeypot/>

    {!! \Form::hidden('return_url' , url()->full()) !!}

    @foreach($form->formFields as $field)
        @php
            $type = "rText";
            $type = $field->type;
            if ($field->is_inline == true) {
                $type= preg_replace('/^r(\w+)/im', 'h$1', $type);
            }
            $name = '__' .$field->name . '__';
            $label = $field->label;
        @endphp

        @if(in_array($type, ['rSelect', 'hSelect', 'rCheckbox', 'hCheckbox', 'rRadio', 'hRadio']))
            @php
                $defaultValue = old($field->name, ($field->value ?? null));
                if (in_array($type, ['rCheckbox', 'hCheckbox'])) {
                    $name = $name. '[]';
                    $defaultValue = \Arr::wrap($defaultValue);
                }
            @endphp
            {!! \Form::$type($name, $label, $field->displayable_options, $defaultValue, $field->is_required) !!}

        @elseif(in_array($type, ['rSelectYear', 'hSelectYear', 'rSelectRange', 'hSelectRange']))
            {!! \Form::$type($name, $label, $field->minimum ?? 1971, $field->maximum ?? date('Y'), old($field->name, ($field->value ?? null)), $field->is_required) !!}

        @elseif(in_array($type, ['rRange', 'hRange']))
            @php
                $attributes = [];
                if ($field->minimum) {
                    $attributes['min'] = $field->minimum;
                }
                if ($field->maximum) {
                    $attributes['max'] = $field->maximum;
                }
            @endphp
            {!! \Form::$type($name, $label, old($field->name, ($field->value ?? null)), $field->is_required, $attributes) !!}

        @elseif(in_array($type, ['rFile', 'hFile', 'rImage', 'hImage']))
            @php
                $preview = [];
                if(in_array($type, ['rImage', 'hImage'])) {
                    $preview = [
                        'preview' => true,
                        'height' => 100,
                        'default' => config('amplify.frontend.fallback_image_path', 'img/No-Image-Placeholder-min.png')
                    ];

                    echo "<style> img#{$name}_preview { height: 100px !important; width: auto;}</style>";
                }
            @endphp
            {!! \Form::rImage($name, $label, $field->is_required, [], $preview) !!}

        @else
            {!! \Form::$type($name, $label, old($field->name, ($field->value ?? null)), $field->is_required) !!}
        @endif

    @endforeach

    {!! $slot ?? '' !!}

    @if($form->allow_captcha)
        <div class="d-block mb-3">
            <x-captcha :display="$captchaVerification()" id="captcha-container-form"/>
        </div>
    @endif
    <div class="d-flex @if($form->allow_reset == true)justify-content-between @else justify-content-center @endif">
        @if($form->allow_reset == true)
            <button type="reset" class="btn btn-danger font-weight-bold">{{ __($clearButtonTitle()) }}</button>
        @endif
        {!! Form::submit('submit', __($submitButtonTitle()), true) !!}
    </div>
    {!! \Form::close() !!}
</div>
