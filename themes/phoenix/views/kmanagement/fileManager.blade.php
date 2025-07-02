@extends('layouts.main')
@section('content')
    <div class="container px-6 mx-auto">

        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ __('File Manager') }} - {{ $server->name }}
        </h2>
        <p class="mt-2 mb-2 font-semibold text-gray-700 dark:text-gray-200">{{ $path }}</p>

        @include('kmanagement.components.navigation')

        <div class="flex items-center mt-2 mb-2">
            <button type="button" class="uploadFile inline-flex items-center px-4 py-2 font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 rounded-s-lg focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 56 56">
                    <path fill="currentColor" fill-rule="evenodd" d="M29.956 39.852v6.722a2 2 0 1 1-4 0v-6.722H10.872c-5.71 0-10.34-4.629-10.34-10.34c0-4.907 3.42-9.017 8.007-10.075a8.744 8.744 0 0 1 11.221-8.65c2.747-4.224 7.508-7.016 12.921-7.016c8.507 0 15.403 6.896 15.403 15.402c0 .484-.023.963-.066 1.435c4.303 1.014 7.506 4.88 7.506 9.492c0 5.386-4.366 9.752-9.752 9.752zm-2.012-19.794c-.468 0-.89.164-1.359.633l-7.922 7.64c-.351.352-.539.727-.539 1.243c0 .96.727 1.64 1.711 1.64a1.71 1.71 0 0 0 1.266-.562l3.539-3.773l1.594-1.664l-.141 3.492v9.98c0 .984.844 1.805 1.851 1.805c1.008 0 1.875-.82 1.875-1.805v-9.98l-.164-3.492l1.594 1.664l3.563 3.773c.328.375.82.563 1.289.563c.984 0 1.687-.68 1.687-1.641c0-.516-.21-.89-.562-1.242l-7.922-7.64c-.469-.47-.867-.634-1.36-.634" />
                </svg>
            </button>
            <button type="button" class="compressFiles inline-flex items-center px-4 py-2 font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M22 4H2v6h2v10h16V10h2zM6 10h12v8H6zm14-4v2H4V6zm-5 6H9v2h6z" />
                </svg>
            </button>
        </div>
        <input type="file" hidden name="uploadFile">

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Size') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Date') }}
                        </th>
                        <th scope="col" class="px-6 py-3">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if($path !== "/")
                        @php $prevPath = explode('/', $path); array_pop($prevPath); @endphp
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 36 36">
                                    <path fill="currentColor" d="M30 9H16.42l-2.31-3.18A2 2 0 0 0 12.49 5H6a2 2 0 0 0-2 2v22a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V11a2 2 0 0 0-2-2M6 11V7h6.49l2.72 4Z" class="clr-i-solid clr-i-solid-path-1" />
                                    <path fill="none" d="M0 0h36v36H0z" />
                                </svg> ..
                            </th>
                            <td class="px-6 py-4">
                            </td>
                            <td class="px-6 py-4">
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex rounded-md shadow-sm" role="group">
                                    <button type="button" data-name="{{ implode("/", $prevPath) }}" data-action="backDirectory" class="fileAction inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M30 9H16.42l-2.31-3.18A2 2 0 0 0 12.49 5H6a2 2 0 0 0-2 2v22a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V11a2 2 0 0 0-2-2M6 11V7h6.49l2.72 4Z" class="clr-i-solid clr-i-solid-path-1" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @foreach($files as $file)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <input id="default-checkbox{{ $file['name'] }}" type="checkbox" value="" data-name="{{ $file['name'] }}" data-root="{{ $path }}" class="toCompress w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="default-checkbox{{ $file['name'] }}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"></label>
                                </div>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white inline-flex">
                                @if($file['is_file'])
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <rect width="24" height="24" fill="none" />
                                        <path fill="currentColor" fill-rule="evenodd" d="M14 22h-4c-3.771 0-5.657 0-6.828-1.172C2 19.657 2 17.771 2 14v-4c0-3.771 0-5.657 1.172-6.828C4.343 2 6.239 2 10.03 2c.606 0 1.091 0 1.5.017c-.013.08-.02.161-.02.244l-.01 2.834c0 1.097 0 2.067.105 2.848c.114.847.375 1.694 1.067 2.386c.69.69 1.538.952 2.385 1.066c.781.105 1.751.105 2.848.105h4.052c.043.534.043 1.19.043 2.063V14c0 3.771 0 5.657-1.172 6.828C19.657 22 17.771 22 14 22" clip-rule="evenodd" opacity="0.5" />
                                        <path fill="currentColor" d="m11.51 2.26l-.01 2.835c0 1.097 0 2.066.105 2.848c.114.847.375 1.694 1.067 2.385c.69.691 1.538.953 2.385 1.067c.781.105 1.751.105 2.848.105h4.052c.013.155.022.321.028.5H22c0-.268 0-.402-.01-.56a5.322 5.322 0 0 0-.958-2.641c-.094-.128-.158-.204-.285-.357C19.954 7.494 18.91 6.312 18 5.5c-.81-.724-1.921-1.515-2.89-2.161c-.832-.556-1.248-.834-1.819-1.04a5.488 5.488 0 0 0-.506-.154c-.384-.095-.758-.128-1.285-.14z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 36 36">
                                        <path fill="currentColor" d="M30 9H16.42l-2.31-3.18A2 2 0 0 0 12.49 5H6a2 2 0 0 0-2 2v22a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V11a2 2 0 0 0-2-2M6 11V7h6.49l2.72 4Z" class="clr-i-solid clr-i-solid-path-1" />
                                        <path fill="none" d="M0 0h36v36H0z" />
                                    </svg>
                                @endif {{ $file['name'] }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $file['size'] }}
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($file['date'])->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex rounded-md shadow-sm" role="group">
                                    @if(!str_contains($file['name'], '.jar') && !str_contains($file['name'], '.tar.gz'))
                                        <button type="button" data-name="{{ $file['name'] }}" data-action="{{ $file['is_file'] ? 'editFile' : 'changeDirectory' }}" class="fileAction inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                @if($file['is_file'])
                                                    <path fill="currentColor" fill-rule="evenodd" d="M14 22h-4c-3.771 0-5.657 0-6.828-1.172C2 19.657 2 17.771 2 14v-4c0-3.771 0-5.657 1.172-6.828C4.343 2 6.239 2 10.03 2c.606 0 1.091 0 1.5.017c-.013.08-.02.161-.02.244l-.01 2.834c0 1.097 0 2.067.105 2.848c.114.847.375 1.694 1.067 2.386c.69.69 1.538.952 2.385 1.066c.781.105 1.751.105 2.848.105h4.052c.043.534.043 1.19.043 2.063V14c0 3.771 0 5.657-1.172 6.828C19.657 22 17.771 22 14 22" clip-rule="evenodd" opacity="0.5" />
                                                    <path fill="currentColor" d="m11.51 2.26l-.01 2.835c0 1.097 0 2.066.105 2.848c.114.847.375 1.694 1.067 2.385c.69.691 1.538.953 2.385 1.067c.781.105 1.751.105 2.848.105h4.052c.013.155.022.321.028.5H22c0-.268 0-.402-.01-.56a5.322 5.322 0 0 0-.958-2.641c-.094-.128-.158-.204-.285-.357C19.954 7.494 18.91 6.312 18 5.5c-.81-.724-1.921-1.515-2.89-2.161c-.832-.556-1.248-.834-1.819-1.04a5.488 5.488 0 0 0-.506-.154c-.384-.095-.758-.128-1.285-.14z" />
                                                @else
                                                    <path d="M30 9H16.42l-2.31-3.18A2 2 0 0 0 12.49 5H6a2 2 0 0 0-2 2v22a2 2 0 0 0 2 2h24a2 2 0 0 0 2-2V11a2 2 0 0 0-2-2M6 11V7h6.49l2.72 4Z" class="clr-i-solid clr-i-solid-path-1" />
                                                @endif
                                            </svg>
                                        </button>
                                    @endif
                                    <button type="button" data-name="{{ $file['name'] }}" data-action="download" class="fileAction inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 @if(str_contains($file['name'], '.jar') || str_contains($file['name'], '.tar.gz')) rounded-s-lg @endif focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M14.707 7.793a1 1 0 0 0-1.414 0L11 10.086V1.5a1 1 0 0 0-2 0v8.586L6.707 7.793a1 1 0 1 0-1.414 1.414l4 4a1 1 0 0 0 1.416 0l4-4a1 1 0 0 0-.002-1.414Z"/>
                                            <path d="M18 12h-2.55l-2.975 2.975a3.5 3.5 0 0 1-4.95 0L4.55 12H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2Zm-3 5a1 1 0 1 1 0-2 1 1 0 0 1 0 2Z"/>
                                        </svg>
                                    </button>
                                    <button type="button" data-name="{{ $file['name'] }}" data-action="remove" class="fileAction inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill="currentColor" d="M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="editFileModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="editFileModalLabel">
                            {{ __('Edit file') }} - <span></span>
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="editFileModal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <div class="p-4 md:p-5 space-y-4">
                        @component('kmanagement.components.fileEditor', ['name' => 'fileContent'])
                            <textarea name="fileContent" data-file="" rows="20" style="width: 100%"></textarea>
                        @endcomponent
                    </div>

                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="editFileModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 fileAction" data-action="saveFile">{{ __('Save') }}</button>
                        <button data-modal-hide="editFileModal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

            const fileEditModal = new Modal(document.getElementById('editFileModal'), {
                placement: 'bottom-right',
                backdrop: 'dynamic',
                backdropClasses:
                    'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
                closable: true,
            }, {id: "editFileModal", override: true})

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
                            document.querySelectorAll('textarea[name="fileContent"]').forEach(textarea => {
                                if (textarea.updateValue) {
                                    textarea.updateValue(response)
                                }
                            })
                            fileEditModal.show();
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


            $('.uploadFile').click(() => {
                $('input[name="uploadFile"]').click()
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
                        color: '#000',
                        html: `
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                              <div class="bg-blue-600 h-2.5 rounded-full" id="uploadProgress" style="width: 0%"></div>
                            </div>
                        `,
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
                                color: '#000',
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
                                color: '#fff',
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
                        color: '#000',
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
        })
    </script>
@endsection
