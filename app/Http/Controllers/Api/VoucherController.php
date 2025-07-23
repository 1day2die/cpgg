<?php

namespace App\Http\Controllers\Api;

use App\Models\Voucher;
use App\Http\Resources\VoucherResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vouchers\CreateVoucherRequest;
use App\Http\Requests\Api\Vouchers\UpdateVoucherRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @group Voucher Management
 */
class VoucherController extends Controller
{
    const ALLOWED_INCLUDES = ['users'];
    const ALLOWED_FILTERS = ['code', 'memo', 'credits', 'uses'];

    /**
     * Show a list of vouchers.
     * 
     * @queryParam include string Comma-separated list of related resources to include. Example: users
     * @queryParam filter[code] string Filter by voucher code. Example: VOUCHER2024
     * @queryParam filter[memo] string Filter by voucher memo. Example: Promocional
     * @queryParam filter[credits] numeric Filter by credits. Example: 100
     * @queryParam filter[uses] integer Filter by number of uses. Example: 10
     * @queryParam per_page integer Number of items per page (default: 50). Example: 25
     * @queryParam page integer Page number. Example: 1
     *
     * @param Request $request
     * @return VoucherResource
     */
    public function index(Request $request)
    {
        $vouchers = QueryBuilder::for(Voucher::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->paginate($request->input('per_page') ?? 50);

        return VoucherResource::collection($vouchers);
    }

    /**
     * Store a new voucher in the system.
     *
     * @param  Request  $request
     * @return VoucherResource
     */
    public function store(CreateVoucherRequest $request)
    {
        $data = $request->validated();
        
        $voucher = Voucher::create($data);

        return VoucherResource::make($voucher);
    }

    /**
     * Show the specified voucher.
     *
     * @queryParam include string Comma-separated list of related resources to include. Example: users
     * 
     * @param Request $request
     * @param  int  $voucher
     * @return VoucherResource
     * 
     * @throws ModelNotFoundException
     */
    public function show(Request $request, int $voucher)
    {
        $voucherQuery = QueryBuilder::for(Voucher::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->where('id', $voucher)
            ->firstOrFail();

        return VoucherResource::make($voucherQuery);
    }

    /**
     * Update the specified voucher in the system.
     *
     * @param  Request  $request
     * @param  Voucher  $voucher
     * @return VoucherResource
     * 
     * @throws ModelNotFoundException
     */
    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $data = $request->validated();

        $voucher->update($data);

        return VoucherResource::make($voucher->fresh());
    }

    /**
     * Remove the specified voucher from the system.
     *
     * @param  Request  $request
     * @param  Voucher  $voucher
     * @return \Illuminate\Http\Response
     * 
     * @throws ModelNotFoundException
     */
    public function destroy(Request $request, Voucher $voucher)
    {
        $voucher->delete();

        return response()->noContent();
    }
}
