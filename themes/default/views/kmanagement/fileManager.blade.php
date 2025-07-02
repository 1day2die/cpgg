@extends('layouts.main')
@section('content')
    <style>
        .CodeMirror {
            height: 400px !important;
        }
    </style>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('File Manager') }} - {{ $server->name }}</h1>
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
                                                       href="#">{{ __('File Manager') }}</a>
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
                    <h3 class="card-title d-inline-block">{{ __('File Manager') }} - {{ $path }}</h3>
                    <div class="card-tools">
                        <button type="button" class="uploadFile btn btn-secondary btn-sm"><i
                                class="fas fa-upload"></i></button>
                        <button type="button" class="compressFiles btn btn-secondary btn-sm"><i
                                class="fas fa-archive"></i></button>
                        <input type="file" class="d-none" name="uploadFile">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive-sm">
                        <table class="table table-hover">
                            <thead class="bg-gradient-primary">
                            <tr>
                                <th></th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Size') }}</th>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col" style="width: 40px">{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if($path !== "/")
                                    <tr>
                                        <td></td>
                                        <td><i class="fas fa-folder"></i> ..</td>
                                        <td>..</td>
                                        <td>-</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @php $prevPath = explode('/', $path); array_pop($prevPath); @endphp
                                                <button type="button" data-name="{{ implode("/", $prevPath) }}" data-action="backDirectory" class="fileAction btn btn-secondary btn-sm"><i class="fas fa-folder"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @foreach($files as $file)
                                    <tr>
                                        <td style="max-width: 32px"><input class="toCompress" type="checkbox" data-name="{{ $file['name'] }}" data-root="{{ $path }}"></td>
                                        <td><i class="{{ $file['is_file'] ? 'fas fa-file' : 'fas fa-folder' }}"></i> {{ $file['name'] }}</td>
                                        <td>{{ $file['size'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($file['date'])->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(!str_contains($file['name'], '.jar') && !str_contains($file['name'], '.tar.gz'))
                                                    <button type="button" data-name="{{ $file['name'] }}" data-action="{{ $file['is_file'] ? 'editFile' : 'changeDirectory' }}" class="fileAction btn btn-secondary btn-sm"><i class="{{ $file['is_file'] ? 'fas fa-edit' : 'fas fa-folder' }}"></i></button>
                                                @endif
                                                @if($file['is_file'])
                                                    <button type="button" data-name="{{ $file['name'] }}" data-action="download" class="fileAction btn btn-info btn-sm"><i class="fas fa-download"></i></button>
                                                @endif
                                                <button type="button" data-name="{{ $file['name'] }}" data-action="remove" class="fileAction btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade editFileModal" tabindex="-1" role="dialog" aria-labelledby="editFileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFileModalLabel">{{ __('Edit file') }} - <span></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @component('kmanagement.components.fileEditor', ['name' => 'fileContent'])
                            <textarea name="fileContent" data-file="" rows="11" style="width: 100%"></textarea>
                        @endcomponent
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-primary fileAction" data-action="saveFile">{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(() => {
            document.querySelectorAll('textarea').forEach(textarea => {
                if(textarea.updateFromWysiwyg) {
                    textarea.updateFromWysiwyg()
                }
            })

            setInterval(() => {
                document.querySelectorAll('textarea[name="fileContent"]').forEach(textarea => {
                    if (textarea.refresh) {
                        textarea.refresh()
                    }
                })
            }, 1000)
            $('.uploadFile').click(() => {
                $('input[name="uploadFile"]').click()
            })

            $('.compressFiles').click(() => {
                let root = '{{ $path }}';
                let files = [];
                $('.toCompress:checked').each((index, element) => {
                    files.push($(element).data('name'))
                })

                if (files.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: '{{ __('No files selected') }}',
                        position: 'top-end',
                        showConfirmButton: false,
                        background: '#343a40',
                        toast: true,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })
                    return;
                }

                window.location.href = '{{ route('kservermanagement.filemanager.compress', ['server' => $server]) }}?root=' + root + '&files=' + files.join(',')
            })

            $('input[name="uploadFile"]').change(e => {
                const file = e.target.files[0]
                const formData = new FormData()
                formData.append('files', file)
                let fileUploadUrl = '';
                $.get('{{ route('kservermanagement.filemanager.upload', ['server' => $server]) }}', (response) => {
                    fileUploadUrl = response.url
                    let notify = Swal.fire({
                        title: '{{ __('Uploading File...') }}',
                        html: '<div class="progress"><div class="progress-bar" id="uploadProgress" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div>',
                        showConfirmButton: false,
                        background: '#343a40',
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    })
                    $.ajax({
                        url: fileUploadUrl,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: () => {
                          let xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", (evt) => {
                                if (evt.lengthComputable) {
                                    let percentComplete = evt.loaded / evt.total;
                                    percentComplete = parseInt(percentComplete * 100);
                                    console.log(percentComplete)
                                    $('#uploadProgress').css('width', percentComplete + '%')
                                    $('#uploadProgress').attr('aria-valuenow', percentComplete)
                                }
                            }, false);
                            return xhr;
                        },
                        success: (response) => {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __('File was uploaded') }}',
                                position: 'top-end',
                                showConfirmButton: false,
                                background: '#343a40',
                                toast: true,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                },
                                willClose: () => {
                                    window.location.reload()
                                }
                            })
                        },
                        fail: (res) => {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('File was not uploaded') }}',
                                position: 'top-end',
                                showConfirmButton: false,
                                background: '#343a40',
                                toast: true,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                },
                                willClose: () => {
                                    window.location.reload()
                                }
                            })
                        }
                    })
                })
            })

            $('.fileAction').click(function () {
                const action = $(this).data('action');
                const name = $(this).data('name');
                const path = '{{ $path !== '/' ? $path : '' }}' + (action === 'changeDirectory' ? '/' + name : '');

                switch (action) {
                    case 'remove':
                        window.location.href = '{{ route('kservermanagement.filemanager.remove', ['server' => $server]) }}?path=' + `{{ $path !== '/' ? $path : '' }}` + '/' + name;
                        break;
                    case 'changeDirectory':
                        window.location.href = '{{ route('kservermanagement.filemanager.index', ['server' => $server]) }}?path=' + path;
                        break;
                    case 'backDirectory':
                        window.location.href = '{{ route('kservermanagement.filemanager.index', ['server' => $server]) }}?path=' + $(this).data('name');
                        break;
                    case 'download':
                        window.open('{{ route('kservermanagement.filemanager.download', ['server' => $server]) }}?path=' + `{{ $path !== '/' ? $path : '' }}` + '/' + name, '_blank');
                        break;
                    case 'editFile':
                        $.post(`{{ route('kservermanagement.filemanager.content', ['server' => $server]) }}`, {
                            path: path + '/' + name
                        }, (response) => {
                            $('textarea[name="fileContent"]').attr('data-file', path + '/' + name);
                            $('textarea[name="fileContent"]').val(response);
                            $('#editFileModalLabel').find('span').text(path + '/' + name);
                            $('.editFileModal').modal('show');
                            document.querySelectorAll('textarea[name="fileContent"]').forEach(textarea => {
                                if (textarea.updateValue) {
                                    textarea.updateValue(response)
                                }
                            })
                        })
                        break;
                    case 'saveFile':
                        document.querySelectorAll('textarea').forEach(textarea => {
                            if(textarea.updateFromWysiwyg) {
                                textarea.updateFromWysiwyg()
                            }
                        })
                        $.post(`{{ route('kservermanagement.filemanager.save', ['server' => $server]) }}`, {
                            path: $('textarea[name="fileContent"]').attr('data-file'),
                            content: $('textarea[name="fileContent"]').val()
                        }, (response) => {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __('File was saved') }}',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    background: '#343a40',
                                    toast: true,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    },
                                    willClose: () => {
                                        window.location.reload()
                                    }
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('File was not saved') }}',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    background: '#343a40',
                                    toast: true,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    },
                                    willClose: () => {
                                        window.location.reload()
                                    }
                                })
                            }
                        })
                        break;
                    default:
                        Swal.fire({
                            icon: 'info',
                            title: '{{ __('Selected action was not found') }}',
                            position: 'top-end',
                            showConfirmButton: false,
                            background: '#343a40',
                            toast: true,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })
                        break;
                }
            })
        })
    </script>
@endsection
