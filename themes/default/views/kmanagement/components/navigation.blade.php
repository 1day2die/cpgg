@php use Illuminate\Support\Facades\Request; @endphp
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('kservermanagement.index') ? 'active' : '' }}"
           href="{{ route('kservermanagement.index', ['server' => $server]) }}">{{ __('Overview') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('kservermanagement.filemanager.*') ? 'active' : '' }}"
           href="{{ route('kservermanagement.filemanager.index', ['server' => $server]) }}">{{ __('File Manager') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('kservermanagement.settings.*') ? 'active' : '' }}"
           href="{{ route('kservermanagement.settings.index', ['server' => $server]) }}">{{ __('Settings') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('kservermanagement.databases.*') ? 'active' : '' }}" href="{{ route('kservermanagement.databases.index', ['server' => $server]) }}">{{ __('Databases') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('kservermanagement.backups.*') ? 'active' : '' }}" href="{{ route('kservermanagement.backups.index', ['server' => $server]) }}">{{ __('Backups') }}</a>
    </li>
</ul>
