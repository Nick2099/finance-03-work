@props(['name' => 'Test name', 'label' => 'Test label', 'description' => null, 'help' => null])
<div>
    <div class="form-field">
        <label for="{{ $name }}" class="form-field-label">
            {{ $label }}
        </label>
        {{ $slot }}
        <p class="form-field-error">
            @if ($errors->has($name))
                {{ $errors->first($name) }}
            @endif
        </p>
        @if ($description)
            <p class="form-field-description">{{ $description }}</p>
        @endif
        @if ($help)
            <p class="form-field-help">{{ $help }}</p>
        @endif
    </div>
</div>
