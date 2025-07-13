<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CurrencyHelper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class BaseApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected CurrencyHelper $currencyHelper;

    public function __construct()
    {
        $this->currencyHelper = new CurrencyHelper();
    }
}
