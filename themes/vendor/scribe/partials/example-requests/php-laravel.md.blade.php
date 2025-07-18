@php
    use Knuckles\Scribe\Tools\WritingUtils as u;
    /** @var  Knuckles\Camel\Output\OutputEndpointData $endpoint */
@endphp
```php
use Illuminate\Support\Facades\Http;

$url = '{!! rtrim($baseUrl, '/') . '/' . ltrim($endpoint->boundUri, '/') !!}';
@if($endpoint->hasHeadersOrQueryOrBodyParams())
$response = Http::
@if(!empty($endpoint->headers))
@foreach($endpoint->headers as $header => $value)
@if($header !== 'Content-Type')
    withHeader('{!! $header !!}', '{!! $value !!}')->
@endif
@endforeach
@endif
@if(!empty($endpoint->cleanQueryParameters))
    withQueryParameters({!! u::printPhpValue($endpoint->cleanQueryParameters, 4) !!})->
@endif
@if($endpoint->hasFiles() || (isset($endpoint->headers['Content-Type']) && $endpoint->headers['Content-Type'] == 'multipart/form-data' && !empty($endpoint->cleanBodyParameters)))
    asMultipart()
@foreach($endpoint->cleanBodyParameters as $parameter => $value)
@foreach(u::getParameterNamesAndValuesForFormData($parameter, $value) as $key => $actualValue)
    ->attach('{!! $key !!}', '{!! $actualValue !!}')
@endforeach
@endforeach
@foreach($endpoint->fileParameters as $parameter => $value)
@foreach(u::getParameterNamesAndValuesForFormData($parameter, $value) as $key => $file)
    ->attach('{!! $key !!}', fopen('{!! $file->path() !!}', 'r'), '{!! basename($file->path()) !!}')
@endforeach
@endforeach
    ->{{ strtolower($endpoint->httpMethods[0]) }}($url);
@elseif(count($endpoint->cleanBodyParameters))
@if ($endpoint->headers['Content-Type'] == 'application/x-www-form-urlencoded')
    asForm()->
    {{ strtolower($endpoint->httpMethods[0]) }}($url, {!! u::printPhpValue($endpoint->cleanBodyParameters, 4) !!});
@else
    {{ strtolower($endpoint->httpMethods[0]) }}($url, {!! u::printPhpValue($endpoint->cleanBodyParameters, 4) !!});
@endif
@else
    {{ strtolower($endpoint->httpMethods[0]) }}($url);
@endif
@else
$response = Http::{{ strtolower($endpoint->httpMethods[0]) }}($url);
@endif

$body = $response->json();
dd($body);
```