@props([
    'name' => null,
])

@pushOnce('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css" rel="stylesheet">
    {{-- Addons (fold): --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldgutter.min.css" rel="stylesheet" />
@endPushOnce
@pushOnce('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    {{-- Addons: --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closetag.min.js"></script>
    {{-- Addons (fold): --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldgutter.min.js"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/brace-fold.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/xml-fold.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/display/autorefresh.min.js" integrity="sha512-vAsKB7xXQAWMn5kcwda0HkFVKUxSYwrmrGprVhmbGFNAG1Ij+2epT3zzdwjHTJyDsKXsiEdrUdhIxh7loHyX+A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/indent-fold.min.js"></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/markdown-fold.min.js"></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/comment-fold.min.js"></script>--}}

    <script>
        // init on dom loaded, to prevent lost focus after js frameworks (vue) rebuild dom
        document.addEventListener('DOMContentLoaded', e => {

            /**
             * Create CodeMirror from textarea
             * @see: https://codemirror.net/doc/manual.html
             */
            document.querySelectorAll('[data-codemirror-wrapper] textarea').forEach(textarea => {
                // get textarea height before hidden
                const textareaHeight = textarea.clientHeight

                const options = {
                    lineNumbers: true,
                    mode: 'null',
                    indentWithTabs: false,
                    indentUnit: 4,
                    smartIndent: false,
                    // tabSize: 4,
                    dragDrop: false,
                    autoCloseBrackets: true,
                    autoCloseTags: true,
                    foldGutter: true,
                    autoRefresh: true,
                    gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
                    /*foldOptions: {
                        // see source: https://codemirror.net/demo/folding.html
                        widget: function(from, to) {
                            console.log(from, to)
                        }
                    },*/
                }


                // create codemirror from textarea
                const editor = CodeMirror.fromTextArea(textarea, options)

                // make codemirror size same as textarea
                editor.setSize('100%', textareaHeight)

                // add method to textarea for get text from codemirror
                textarea.updateFromWysiwyg = () => {
                    editor.save()
                }

                textarea.updateValue = (value) => {
                    editor.getDoc().setValue(value)
                    editor.refresh()
                }

                textarea.refresh = () => {
                    editor.refresh()
                }

            })

        })
    </script>
@endPushOnce


<div data-codemirror-wrapper>
    {{ $slot }}
</div>
