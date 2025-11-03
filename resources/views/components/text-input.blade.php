@props(['disabled' => false])

<input {{ $attributes->merge(['class' => '
    border-gray-300 
    bg-white
    text-gray-900
    
    appearance-none 
    
    py-2 
    px-3         
    h-10 
    
    focus:border-indigo-500 
    focus:ring-indigo-500 
    rounded-lg 
    shadow-sm
    w-full       
']) }} style="padding-left: 0.75rem !important; padding-right: 0.75rem !important;">