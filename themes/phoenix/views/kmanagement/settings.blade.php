@extends('layouts.main')
@section('content')
    <div class="container px-6 mx-auto">

        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ __('Settings') }} - {{ $server->name }}
        </h2>
        @include('kmanagement.components.navigation')

        <form method="POST" action="{{ route('kservermanagement.settings.update', ['server' => $server]) }}">
            @csrf
            <div class="block w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ __('Docker Image') }}
                </h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    <select id="docker_image" name="docker_image" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        @foreach($dockerImages as $image)
                            <option value="{{ $image['image'] }}" {{ $image['image'] === $pterodactylServer['docker_image'] ? 'selected' : '' }}>{{ $image['name'] }}</option>
                        @endforeach
                    </select>
                </p>
            </div>
            <div class="grid md:grid-cols-4 gap-3 mt-3">
                @foreach($startup as $start_var)
                    <div class="block w-full p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            {{ $start_var['attributes']['name'] }}
                        </h5>
                        <p class="font-normal text-gray-700 dark:text-gray-400">
                            <label for="{{ $start_var['attributes']['name'] }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Value') }}</label>
                            <input name="startup_variables[{{ $start_var['attributes']['env_variable'] }}]" value="{{ $start_var['attributes']['server_value'] ?? $start_var['attributes']['default_value'] }}" type="text" id="{{ $start_var['attributes']['name'] }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </p>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="block w-full mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                {{ __('Save') }}
            </button>
        </form>
    </div>
@endsection
