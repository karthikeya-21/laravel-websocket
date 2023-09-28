<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary bg-primary' ]) }}>
    {{ $slot }}
</button>


<!-- 'inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-dark focus:bg-gray-700 dark:focus:bg-white btn btn-primary focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150' -->