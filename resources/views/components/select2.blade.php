@props([
    'name',
    'wireModel' => null,
    'options' => [],
    'placeholder' => 'Pilih opsi',
    'value' => null,
    'disabled' => false,
])

@php
    $selectId = 'select2_' . str_replace(['[', ']'], ['_', ''], $name) . '_' . uniqid();
    $isMultiple = $attributes->has('multiple') || str_contains($name, '[]');
@endphp

<div 
    class="select2-wrapper"
    wire:ignore
    x-data="{
        @if($wireModel)
        value: $wire.entangle('{{ $wireModel }}').live,
        @else
        value: @js($value ?? ''),
        @endif
        options: @js($options),
        disabled: @js($disabled),
        placeholder: @js($placeholder),
        isMultiple: @js($isMultiple),
        wireModel: @js($wireModel),
        selectId: '{{ $selectId }}',
        select2Instance: null,
        isInitialized: false,
        
        init() {
            this.$nextTick(() => {
                this.initSelect2();
            });
        },
        
        initSelect2() {
            const self = this;
            const selectEl = this.$refs.select;
            
            // Wait for jQuery and Select2 to be available
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') {
                setTimeout(() => this.initSelect2(), 100);
                return;
            }
            
            const $select = jQuery(selectEl);
            
            // Destroy existing instance if any
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
            
            // Initialize Select2
            $select.select2({
                theme: 'default',
                width: '100%',
                allowClear: !this.isMultiple,
                placeholder: this.placeholder,
                disabled: this.disabled,
                language: {
                    noResults: function() { return 'Tidak ada hasil'; },
                    searching: function() { return 'Mencari...'; }
                }
            });
            
            // Set initial value after a small delay to ensure Select2 is ready
            setTimeout(() => {
                if (this.value) {
                    $select.val(this.value).trigger('change.select2');
                }
            }, 50);
            
            // Listen for Select2 changes
            $select.on('select2:select select2:unselect select2:clear', function(e) {
                const newValue = self.isMultiple ? ($select.val() || []) : ($select.val() || '');
                self.value = newValue;
            });
            
            this.select2Instance = $select;
            this.isInitialized = true;
            
            // Watch for value changes (from Livewire entangle)
            this.$watch('value', (newValue, oldValue) => {
                if (this.isInitialized && this.select2Instance) {
                    const currentVal = this.select2Instance.val();
                    if (currentVal !== newValue) {
                        this.select2Instance.val(newValue).trigger('change.select2');
                    }
                }
            });
        },
        
        updateOptions(newOptions) {
            this.options = newOptions;
            const $select = this.select2Instance;
            if (!$select) return;
            
            const currentValue = this.value;
            
            // Clear and repopulate options
            $select.find('option:not([value=\'\'])').remove();
            Object.entries(newOptions).forEach(([val, label]) => {
                $select.append(new Option(label, val, false, false));
            });
            
            // Reset to placeholder if current value is not in new options
            if (currentValue && !newOptions[currentValue]) {
                this.value = '';
                $select.val('').trigger('change.select2');
            } else if (currentValue && newOptions[currentValue]) {
                $select.val(currentValue).trigger('change.select2');
            } else {
                $select.trigger('change.select2');
            }
        },
        
        updateDisabled(newDisabled) {
            this.disabled = newDisabled;
            if (this.select2Instance) {
                this.select2Instance.prop('disabled', newDisabled);
            }
        },
        
        updateValue(newValue) {
            this.value = newValue;
            if (this.select2Instance && this.select2Instance.hasClass('select2-hidden-accessible')) {
                this.select2Instance.val(newValue).trigger('change.select2');
            }
        }
    }"
    x-on:update-select2-options.window="if ($event.detail.name === '{{ $name }}') updateOptions($event.detail.options)"
    x-on:update-select2-disabled.window="if ($event.detail.name === '{{ $name }}') updateDisabled($event.detail.disabled)"
    x-on:update-select2-value.window="if ($event.detail.name === '{{ $name }}') updateValue($event.detail.value)"
>
    <select
        x-ref="select"
        id="{{ $selectId }}"
        name="{{ $name }}"
        class="select2-select {{ $attributes->get('class', '') }}"
        @if($isMultiple) multiple @endif
        @if($disabled) disabled @endif
        style="width: 100%;"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @if($value == $optionValue) selected @endif>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
</div>
