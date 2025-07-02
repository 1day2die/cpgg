@extends('layouts.main')
@section('content')
    <div class="container px-6 mx-auto">

        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ __('Backups') }} - {{ $server->name }}
        </h2>

        @include('kmanagement.components.navigation')
        @if($serverLimits['backups'] > count($backups))
            <a href="{{ route('kservermanagement.backups.create', ['server' => $server]) }}" class="inline-flex mb-3 px-5 py-2.5 text-sm font-medium text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                {{ __('Create') }}
            </a>
        @endif

        <div class="grid md:grid-cols-4 gap-3">
            @forelse($backups as $backup)
                <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ $backup['name'] }}
                    </h5>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                        {{ __('Size') }}: {{ $backup['size'] }}
                    </p>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                        {{ __('Date') }}: {{ \Carbon\Carbon::parse($backup['date'])->format('d/m/y H:i') }}
                    </p>
                    <a href="{{ route('kservermanagement.backups.delete', ['server' => $server, 'backupId' => $backup['id']]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300">
                        {{ __('Delete') }}
                    </a>
                    <a href="{{ route('kservermanagement.backups.download', ['server' => $server, 'backupId' => $backup['id']]) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                        {{ __('Download') }}
                    </a>
                </div>
            @empty
                <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <div>
                        {{ __('No backups found') }}
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
