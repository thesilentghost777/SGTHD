<x-mail::message>
{{-- Salutation --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Oups!')
@else
# @lang('Bonjour!')
@endif
@endif

{{-- Lignes d'introduction --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach

{{-- Bouton d'action --}}
@isset($actionText)
<?php
$color = match ($level) {
    'success', 'error' => $level,
    default => 'primary',
};
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Lignes de conclusion --}}
@foreach ($outroLines as $line)
{{ $line }}
@endforeach

{{-- Formule de politesse --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Cordialement,')<br>
{{ config('app.name') }}
@endif

{{-- Sous-copie --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "Si vous avez des difficultés à cliquer sur le bouton \":actionText\", copiez et collez l'URL ci-dessous\n".
    'dans votre navigateur web:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>