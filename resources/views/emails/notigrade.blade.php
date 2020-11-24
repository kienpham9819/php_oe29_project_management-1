@component('mail::message')
# Ingredient score announcement

{{ 'Your grade : ' . $data['grade'] }}<br>
{{ 'Lecturer review : ' . $data['review'] }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
