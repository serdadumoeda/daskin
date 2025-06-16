@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-theme-primary focus:ring-theme-primary rounded-md shadow-sm']) !!}>