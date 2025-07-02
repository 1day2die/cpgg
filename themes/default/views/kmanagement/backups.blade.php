@extends('layouts.main')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Backups') }} - {{ $server->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('servers.index') }}">{{ __('Servers') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('kservermanagement.index', ['server' => $server]) }}">{{ __('Server management') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a class="text-muted"
                                                       href="#">{{ __('Backups') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT HEADER -->
    <section class="content">
        <div class="container-fluid">
            @include('kmanagement.components.navigation')
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title d-inline-block">{{ __('Backups') }}</h3>
                    @if($serverLimits['backups'] > count($backups))
                        <div class="card-tools">
                            <a href="{{ route('kservermanagement.backups.create', ['server' => $server]) }}" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-plus"></i></a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($backups as $backup)
                            <div class="col-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('Name') }}: backup-{{ $backup['id'] }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ __('Size') }}: {{ $backup['size'] }}</p>
                                        <p>{{ __('Date') }}: {{ \Carbon\Carbon::parse($backup['date'])->format('d/m/y H:i') }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('kservermanagement.backups.delete', ['server' => $server, 'backupId' => $backup['id']]) }}" class="btn btn-danger">{{ __('Delete') }}</a>
                                        <a target="_blank" href="{{ route('kservermanagement.backups.download', ['server' => $server, 'backupId' => $backup['id']]) }}" class="btn btn-primary">{{ __('Download') }}</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="icon fas fa-info"></i> {{ __('No backups found') }}
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
