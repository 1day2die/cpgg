@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit@0.8.0/lib/xterm-addon-fit.min.js"></script>
    <style>
        .progress {
            width: 100px;
            height: 100px;
            background: none;
            position: relative;
        }

        .progress::after {
            content: "";
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 6px solid #eee;
            position: absolute;
            top: 0;
            left: 0;
        }

        .progress>span {
            width: 50%;
            height: 100%;
            overflow: hidden;
            position: absolute;
            top: 0;
            z-index: 1;
        }

        .progress .progress-left {
            left: 0;
        }

        .progress .progress-bar {
            width: 100%;
            height: 100%;
            background: none;
            border-width: 6px;
            border-style: solid;
            position: absolute;
            top: 0;
        }

        .progress .progress-left .progress-bar {
            left: 100%;
            border-top-right-radius: 80px;
            border-bottom-right-radius: 80px;
            border-left: 0;
            -webkit-transform-origin: center left;
            transform-origin: center left;
        }

        .progress .progress-right {
            right: 0;
        }

        .progress .progress-right .progress-bar {
            left: -100%;
            border-top-left-radius: 80px;
            border-bottom-left-radius: 80px;
            border-right: 0;
            -webkit-transform-origin: center right;
            transform-origin: center right;
        }

        .progress .progress-value {
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('Server management') }} - {{ $server->name }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('servers.index') }}">{{ __('Servers') }}</a>
                        </li>
                        <li class="breadcrumb-item"><a class="text-muted"
                                                       href="{{ route('kservermanagement.index', ['server' => $server]) }}">{{ __('Server management') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT HEADER -->

    <!-- MAIN CONTENT -->
    <section class="content">
        <div class="container-fluid">
            @include('kmanagement.components.navigation')
            <div class="row">
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Console') }}</h3>
                            <div class="card-tools">
                                <span class="badge" id="server_status"></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="terminal"></div>
                        </div>
                        <div class="card-footer">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="command"
                                       placeholder="{{ __('Type command without /') }}">
                                <span class="input-group-append">
                                    <button type="button" class="sendCommand btn btn-info btn-flat">{{ __('Send') }}</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100 mb-1" role="group">
                        <button type="button" class="btn btn-success actionPower roundedr" data-action="start">{{ __('Start') }}</button>
                        <button type="button" class="btn btn-warning actionPower rounded-md" data-action="restart">{{ __('Restart') }}</button>
                        <button type="button" class="btn btn-danger actionPower rounded-md" data-action="kill">{{ __('Force shutdown') }}</button>
                        <button type="button" class="btn btn-danger actionPower rounded" data-action="stop">{{ __('Stop') }}</button>
                    </div>
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Resource usage') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <h2 class="h6 font-weight-bold text-center mb-4">CPU</h2>
                            <div class="progress mx-auto" data-value="0" data-stat="cpu" style="background: transparent !important;">
                                <span class="progress-left">
                                    <span class="progress-bar border-primary"></span>
                                </span>
                                <span class="progress-right">
                                    <span class="progress-bar border-primary"></span>
                                </span>
                                <div
                                    class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                    <div class="h2 font-weight-bold progress-text">0<sup class="small">%</sup></div>
                                </div>
                            </div>

                            <h2 class="h6 font-weight-bold text-center mb-4 mt-4">{{ __("Memory") }}</h2>
                            <div class="progress mx-auto" data-value="0" data-stat="memory" style="background: transparent !important;">
                                <span class="progress-left">
                                    <span class="progress-bar border-primary"></span>
                                </span>
                                <span class="progress-right">
                                    <span class="progress-bar border-primary"></span>
                                </span>
                                <div
                                    class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                    <div class="h2 font-weight-bold progress-text">0<sup class="small">%</sup></div>
                                </div>
                            </div>

                            <h2 class="h6 font-weight-bold text-center mb-4 mt-4">{{ __("Disk") }}</h2>
                            <div class="progress mx-auto" data-value="0" data-stat="disk" style="background: transparent !important;">
                                <span class="progress-left">
                                    <span class="progress-bar border-primary"></span>
                                </span>
                                <span class="progress-right">
                                    <span class="progress-bar border-primary"></span>
                                </span>
                                <div
                                    class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                    <div class="h2 font-weight-bold progress-text">0<sup class="small">%</sup></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Allocations') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row">
                                @foreach($serverAllocations as $allocation)
                                    <div class="col-md-6">
                                        <div class="card card-body rounded">
                                            <div class="justify-content-between align-items-center">
                                                @if($allocation['primary'])
                                                    <span class="badge badge-success">{{ __('Primary') }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ __('Extra') }}</span>
                                                @endif
                                                <span>{{ $allocation['ip'] }}:{{ $allocation['port'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('SFTP Details') }}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <p>{{ __('Username') }}: {{ auth()->user()->name }}.{{ $server->identifier }}</p>
                            <p>{{ __('Password') }}: {{ __('Your account password') }}</p>
                            <p>{{ __('Host') }}: {{ $sftpData['ip'] }}:{{ $sftpData['port'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>
        const serverLimits = @json($serverLimits);
        const connectWebSocket = (socket, token) => {
            let websocket = new WebSocket(socket);

            websocket.onopen = () => {
                websocket.send(JSON.stringify({event: 'auth', args: [token]}))
            }

            return websocket;
        }

        const fixProgresses = () => {
            $(".progress").each(function() {
                let value = $(this).attr('data-value');
                let left = $(this).find('.progress-left .progress-bar');
                let right = $(this).find('.progress-right .progress-bar');
                $(this).find('.progress-text').html(value + '<sup class="small">%</sup>')
                if (value <= 50) {
                    right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
                    left.css('transform', 'rotate(0deg)')
                } else {
                    right.css('transform', 'rotate(180deg)')
                    left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
                }
            })

            function percentageToDegrees(percentage) {
                return percentage > 0 ? percentage / 100 * 360 : 0;
            }
        }

        $(document).ready(function () {
            let term = new Terminal({fontFamily: 'monospace', rendererType: 'dom'});
            let fitAddon = new FitAddon.FitAddon();
            term.loadAddon(fitAddon);
            term.open(document.getElementById('terminal'));
            fitAddon.fit();

            let socket = null;

            $.get('{{ route('kservermanagement.websocket', ['server' => $server]) }}', (data) => {
                socket = connectWebSocket(data.socket, data.token);

                socket.onmessage = (event) => {
                    let data = JSON.parse(event.data);

                    if (data.event === "auth success") {
                        socket.send(JSON.stringify({event: 'send logs', args: []}));
                        $('.sendCommand').click(() => {
                            let command = $('input[name="command"]').val();
                            socket.send(JSON.stringify({event: 'send command', args: [command]}));
                            $('input[name="command"]').val('');
                        });

                        $('input[name="command"]').keypress(function (e) {
                            if (e.which === 13) {
                                $('.sendCommand').click();
                            }
                        });

                        $('.actionPower').click((e) => {
                            let action = $(e.target).data('action');
                            socket.send(JSON.stringify({event: 'set state', args: [action]}));
                        });
                    }

                    if (data.event === "status") {
                        switch (data.args[0]) {
                            case "running":
                                $('#server_status').text('Online').addClass('badge-success');
                                break;
                            case "offline":
                                $('#server_status').text('Offline').addClass('badge-danger');
                                break;
                            case "starting":
                                $('#server_status').text('Starting').addClass('badge-warning');
                                break;
                        }
                    }

                    if (data.event === "console output" || data.event === "install output") {
                        term.write(data.args + '\r\n');
                    }

                    if (data.event === "stats") {
                        let stats = JSON.parse(data.args);
                        let memory = (stats.memory_bytes / 1024 / 1024) / serverLimits.memory * 100;
                        let cpu = stats.cpu_absolute / serverLimits.cpu * 100;
                        let disk = (stats.disk_bytes / 1024 / 1024) / serverLimits.disk * 100;

                        $('.progress[data-stat="cpu"]').attr('data-value', cpu > 100 ? 100 : cpu.toFixed(0));
                        $('.progress[data-stat="memory"]').attr('data-value', memory > 100 ? 100 : memory.toFixed(0));
                        $('.progress[data-stat="disk"]').attr('data-value', disk.toFixed(0));
                        fixProgresses();
                    }

                    if (data.event === "token expiring") {
                        socket.close();
                    }
                }

                socket.onclose = () => {
                    $.get('{{ route('kservermanagement.websocket', ['server' => $server]) }}', (data) => {
                        socket = connectWebSocket(data.socket, data.token);
                    });
                }
            });
        })
    </script>
@endsection
