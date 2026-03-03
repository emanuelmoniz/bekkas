@props([
    // shared translation prefix for address labels
    'labelPrefix' => 'address_form',
    // optional existing address model for update forms
    'address' => null,
    // Alpine expression string to disable inputs (e.g. "addressMode !== 'new'")
    'disabledExpr' => null,
    // extra Alpine bindings for postal code input
    'postalCodeBindings' => '',
    // extra Alpine bindings for country select
    'countryBindings' => '',
    // base classes applied to every input/select
    'inputClasses' => 'border rounded px-2 py-1',
    // when true, prefer old() input values (used for failed submissions)
    'useOldInput' => null,
    // optional array of field names that should be required
    'requiredFields' => null,
])

@php
    $defaultRequired = ['title', 'address_line_1', 'postal_code', 'city', 'country_id'];
    if (is_array($requiredFields)) {
        $required = $requiredFields;
    } else {
        $required = $defaultRequired;
    }

    $isRequired = function ($field) use ($required) {
        return in_array($field, $required, true);
    };

    $shouldUseOldInput = is_bool($useOldInput) ? $useOldInput : !isset($address);
@endphp

<div class="grid grid-cols-2 gap-2">
    @php
        $fields = ['title','nif','phone','address_line_1','address_line_2','postal_code','city'];
    @endphp
    @foreach ($fields as $field)
        @php
            $labelKey = "{$labelPrefix}.{$field}";
            $label = t($labelKey) ?: ucfirst(str_replace('_', ' ', $field));
            $value = $shouldUseOldInput
                ? old($field, $address->{$field} ?? '')
                : ($address->{$field} ?? '');
        @endphp
        <label class="block">
            <span class="inline-block text-sm font-medium text-grey-dark">
                {{ $label }}{!! $isRequired($field) ? ' <span class="text-status-error">*</span>' : '' !!}
            </span>
            <input
                name="{{ $field }}"
                value="{{ $value }}"
                class="{{ $inputClasses }}"
                @if($isRequired($field)) required @endif
                {!! $disabledExpr ? ':disabled="'.$disabledExpr.'"' : '' !!}
                @if($field === 'postal_code') {!! $postalCodeBindings !!} @endif
            >
        </label>
    @endforeach

    <label class="block">
        <span class="inline-block text-sm font-medium text-grey-dark">
            {{ t("{$labelPrefix}.country") ?: 'Country' }}{!! $isRequired('country_id') ? ' <span class="text-status-error">*</span>' : '' !!}
        </span>
        <select name="country_id"
                class="{{ $inputClasses }}"
                @if($isRequired('country_id')) required @endif
                {!! $disabledExpr ? ':disabled="'.$disabledExpr.'"' : '' !!}
                {!! $countryBindings !!}>
            <option value="">{{ t("{$labelPrefix}.country") ?: 'Country' }}</option>
            @foreach(\App\Models\Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get() as $country)
                <option value="{{ $country->id }}"
                        @if(isset($address) && $address->country_id == $country->id) selected
                        @elseif($shouldUseOldInput && old('country_id') == $country->id) selected
                        @endif>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
    </label>
</div>