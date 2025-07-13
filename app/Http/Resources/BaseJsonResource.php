<?php

namespace App\Http\Resources;

use App\Helpers\CurrencyHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    protected CurrencyHelper $currencyHelper;

    public function __construct($resource)
    {
        $this->currencyHelper = new CurrencyHelper();

        parent::__construct($resource);
    }
}
