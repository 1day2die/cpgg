@extends('layouts.main')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Settings') }} - {{ $server->name }}</h1>
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
                                                       href="#">{{ __('Settings') }}</a>
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
            <form method="POST" action="{{ route('kservermanagement.settings.update', ['server' => $server]) }}">
                @csrf
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Docker Image') }}</h3>
                    </div>
                    <div class="card-body">
                        <select class="custom-select" name="docker_image">
                            @foreach($dockerImages as $image)
                                <option value="{{ $image['image'] }}" {{ $image['image'] === $pterodactylServer['docker_image'] ? 'selected' : '' }}>{{ $image['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    @foreach($startup as $start_var)
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">{{ $start_var['attributes']['name'] }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="">{{ __('Value') }}</label>
                                        <input type="text" class="form-control" name="startup_variables[{{ $start_var['attributes']['env_variable'] }}]" value="{{ $start_var['attributes']['server_value'] ?? $start_var['attributes']['default_value'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </form>
        </div>
    </section>
@endsection
