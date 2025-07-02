@php use Illuminate\Support\Facades\Request; @endphp
@php $activeClasses = 'text-white bg-blue-600 rounded-lg active'; @endphp
@php $inactiveClasses = 'rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white'; @endphp

<ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400 mb-3">
    <li class="me-2">
        <a href="{{ route('kservermanagement.index', ['server' => $server]) }}" class="inline-block px-4 py-3  {{ Request::routeIs('kservermanagement.index') ? $activeClasses : $inactiveClasses }}">
            {{ __('Overview') }}
        </a>
    </li>
    <li class="me-2">
        <a href="{{ route('kservermanagement.filemanager.index', ['server' => $server]) }}"  class="inline-block px-4 py-3 {{ Request::routeIs('kservermanagement.filemanager.*') ? $activeClasses : $inactiveClasses }}">
            {{ __('File Manager') }}
        </a>
    </li>
    <li class="me-2">
        <a href="{{ route('kservermanagement.settings.index', ['server' => $server]) }}" class="inline-block px-4 py-3 {{ Request::routeIs('kservermanagement.settings.*') ? $activeClasses : $inactiveClasses }}">
            {{ __('Settings') }}
        </a>
    </li>
    <li class="me-2">
        <a href="{{ route('kservermanagement.databases.index', ['server' => $server]) }}" class="inline-block px-4 py-3 {{ Request::routeIs('kservermanagement.databases.*') ? $activeClasses : $inactiveClasses }}">
            {{ __('Databases') }}
        </a>
    </li>
    <li>
        <a class="inline-block px-4 py-3 {{ Request::routeIs('kservermanagement.backups.*') ? $activeClasses : $inactiveClasses }}" href="{{ route('kservermanagement.backups.index', ['server' => $server]) }}">
            {{ __('Backups') }}
        </a>
    </li>
</ul>
