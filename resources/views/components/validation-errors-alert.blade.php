@props([
    'errorBag' => null,
    'fields' => [],
])

@php
    $resolvedBag = null;

    if ($errorBag instanceof \Illuminate\Support\ViewErrorBag) {
        $resolvedBag = $errorBag->getBag('default');
    } elseif ($errorBag instanceof \Illuminate\Support\MessageBag) {
        $resolvedBag = $errorBag;
    } elseif (isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag) {
        $resolvedBag = $errors->getBag('default');
    }

    $messages = collect();

    if ($resolvedBag) {
        if (!empty($fields)) {
            foreach ($fields as $field) {
                foreach ($resolvedBag->get($field) as $message) {
                    $messages->push($message);
                }
            }
        } else {
            $messages = collect($resolvedBag->all());
        }
    }

    $messages = $messages->filter()->unique()->values();
@endphp

@if ($messages->isNotEmpty())
    <div class="px-4 py-3 rounded relative border border-status-error border-l-4 bg-status-error/10 text-status-error" role="alert">
        <strong class="font-bold">{{ t('validation.error_heading') ?: 'Please fix the following errors:' }}</strong>
        <ul class="mt-2 list-disc list-inside text-sm">
            @foreach ($messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
