@extends('layouts.main')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Databases') }} - {{ $server->name }}</h1>
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
                                                       href="#">{{ __('Databases') }}</a>
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
                    <h3 class="card-title d-inline-block">{{ __('Databases') }}</h3>
                    @if($serverLimits['databases'] > count($databases))
                        <div class="card-tools">
                            <button type="button" data-toggle="modal" data-target=".createDatabaseModal" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                   <div class="row">
                        @forelse($databases as $base)
                            <div class="col-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('Name') }}: {{ $base['name'] }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ __('Host') }}: {{ $base['host'] }}</p>
                                        <p>{{ __('Username') }}: {{ $base['username'] }}</p>
                                        <p>{{ __('Password') }}: {{ $base['password'] }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('kservermanagement.databases.delete', ['server' => $server, 'databaseId' => $base['id']]) }}" class="btn btn-danger">{{ __('Delete') }}</a>
                                        <a href="{{ route('kservermanagement.databases.reset', ['server' => $server, 'databaseId' => $base['id']]) }}" class="btn btn-primary">{{ __('Rotate password') }}</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="icon fas fa-info"></i> {{ __('No databases found') }}
                            </div>
                        </div>
                        @endforelse
                   </div>
                </div>
            </div>
        </div>
        @if($serverLimits['databases'] > count($databases))
        <div class="modal fade createDatabaseModal" tabindex="-1" role="dialog" aria-labelledby="createDatabaseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createDatabaseModalLabel">{{ __('Create') }} <span></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('kservermanagement.databases.create', ['server' => $server]) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="databaseName">{{ __('Database name') }}</label>
                                <input type="text" class="form-control" id="databaseName" name="database" placeholder="{{ __('Database name') }}">
                            </div>
                            <div class="form-group">
                                <label for="databaseRemote">{{ __('Allow connections from') }}</label>
                                <input type="text" class="form-control" id="databaseRemote" name="remote" placeholder="{{ __('Allow connections from') }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </section>
@endsection
