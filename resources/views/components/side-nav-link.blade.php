@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg shadow-sm'
            // Perubahan di baris ini: hapus bg-gray-100 agar transparan
            : 'flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>