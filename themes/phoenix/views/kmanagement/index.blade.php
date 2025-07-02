@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit@0.8.0/lib/xterm-addon-fit.min.js"></script>

    <div class="container px-6 mx-auto">

        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ __('Server management') }} - {{ $server->name }}
        </h2>

        @include('kmanagement.components.navigation')

        <div class="md:flex flex-row gap-2">
            <div class="basis-3/4">
                <div class="md:mb-0 mb-3 block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        {{ __('Console') }} <span class="badge" id="server_status"></span>
                    </h5>
                    <div id="terminal"></div>
                    <div class="mt-2">
                        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">{{ __('Type command without /') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14L21 3m0 0l-6.5 18a.55.55 0 0 1-1 0L10 14l-7-3.5a.55.55 0 0 1 0-1z" />
                                </svg>
                            </div>
                            <input type="text" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="{{ __('Type command without /') }}" name="command" />
                            <button type="button" class="sendCommand text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">{{ __('Send') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="basis-1/4">
                <div class="flex justify-center">
                    <div class="inline-flex rounded-md shadow-sm mb-3" role="group">
                        <button type="button" data-action="start" class="actionPower px-4 py-2 text-sm font-medium text-white bg-green-600 border-l border-t border-b border-green-600 rounded-s-lg focus:z-10 focus:ring-2 focus:ring-green-700">
                            {{ __('Start') }}
                        </button>
                        <button type="button" data-action="stop" class="actionPower px-4 py-2 text-sm font-medium text-white bg-red-600 border-t border-b border-r border-l border-red-600 focus:z-10 focus:ring-2 focus:ring-red-700">
                            {{ __('Shutdown') }}
                        </button>
                        <button type="button" data-action="kill" class="actionPower px-4 py-2 text-sm font-medium text-white bg-red-600 border-t border-b border-r border-red-600 focus:z-10 focus:ring-2 focus:ring-red-700">
                            {{ __('Force shutdown') }}
                        </button>
                        <button type="button" data-action="restart" class="actionPower px-4 py-2 text-sm font-medium text-white bg-yellow-600 border-t border-b border-r border-yellow-600 rounded-e-lg focus:z-10 focus:ring-2 focus:ring-yellow-700">
                            {{ __('Restart') }}
                        </button>
                    </div>
                </div>

                <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('Resource usage') }}</h5>

                    <div class="progress" data-stat="cpu">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-blue-700 dark:text-white">{{ __('CPU') }}</span>
                            <span class="text-sm font-medium text-blue-700 dark:text-white">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-purple-600 h-2.5 rounded-full progressBar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress mt-4 mb-4" data-stat="memory">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-blue-700 dark:text-white">{{ __('Memory') }}</span>
                            <span class="text-sm font-medium text-blue-700 dark:text-white">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-indigo-600 h-2.5 rounded-full progressBar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="progress" data-stat="disk">
                        <div class="flex justify-between mb-1">
                            <span class="text-base font-medium text-blue-700 dark:text-white">{{ __('Disk') }}</span>
                            <span class="text-sm font-medium text-blue-700 dark:text-white">0%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-blue-600 h-2.5 rounded-full progressBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-3 mt-2">
            <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-3 text-base font-semibold text-gray-900 md:text-xl dark:text-white">
                    {{ __('Allocations') }}
                </h5>
                <ul class="my-4 space-y-3">
                    @foreach($serverAllocations as $allocation)
                        <li>
                            <p class="flex items-center p-3 text-base font-bold text-gray-900 rounded-lg bg-gray-50 hover:bg-gray-100 group hover:shadow dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white">
                                <span class="flex-1 ms-3 whitespace-nowrap">{{ $allocation['ip'] }}:{{ $allocation['port'] }}</span>
                                @if($allocation['primary'])
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-green-500 bg-green-200 rounded dark:bg-green-700 dark:text-green-400">
                                        {{ __('Primary') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 ms-3 text-xs font-medium text-gray-500 bg-gray-200 rounded dark:bg-gray-700 dark:text-gray-400">
                                        {{ __('Extra') }}
                                    </span>
                                @endif
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>


            <div class="block w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ __('SFTP Details') }}
                </h5>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    {{ __('Username') }}: {{ auth()->user()->name }}.{{ $server->identifier }}
                </p>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    {{ __('Password') }}: {{ __('Your account password') }}
                </p>
                <p class="font-normal text-gray-700 dark:text-gray-400">
                    {{ __('Host') }}: {{ $sftpData['ip'] }}:{{ $sftpData['port'] }}
                </p>
            </div>


        </div>
    </div>
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

            })
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
                                $('#server_status').text('Online').addClass('bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300');
                                break;
                            case "offline":
                                $('#server_status').text('Offline').addClass('bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300');
                                break;
                            case "starting":
                                $('#server_status').text('Starting').addClass('bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300');
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

                        $('.progress[data-stat="cpu"]').find('.progressBar').css('width', cpu.toFixed(0) > 100 ? 100 : cpu.toFixed(0) + '%');
                        $('.progress[data-stat="cpu"]').find('.text-sm').text(cpu.toFixed(0) > 100 ? 100 : cpu.toFixed(0) + '%');
                        $('.progress[data-stat="memory"]').find('.progressBar').css('width', memory.toFixed(0) > 100 ? 100 : memory.toFixed(0) + '%');
                        $('.progress[data-stat="memory"]').find('.text-sm').text(memory.toFixed(0) > 100 ? 100 : memory.toFixed(0) + '%');
                        $('.progress[data-stat="disk"]').find('.progressBar').css('width', disk.toFixed(0) > 100 ? 100 : disk.toFixed(0) + '%');
                        $('.progress[data-stat="disk"]').find('.text-sm').text(disk.toFixed(0) > 100 ? 100 : disk.toFixed(0) + '%');
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
