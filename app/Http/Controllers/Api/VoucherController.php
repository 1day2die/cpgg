<?php

namespace App\Http\Controllers\Api;

use App\Models\Voucher;
use App\Http\Resources\VoucherResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VoucherController extends Controller
{
    const ALLOWED_INCLUDES = ['users'];
    const ALLOWED_FILTERS = ['code', 'memo', 'credits', 'uses'];

    /**
     * Show a list of vouchers.
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
    public function store(Request $request)
    {
        $data = $request->validate([
            'memo' => 'nullable|string|max:191',
            'code' => 'required|string|alpha_dash|max:36|min:4|unique:vouchers',
            'uses' => 'required|numeric|max:2147483647|min:1',
            'credits' => 'required|numeric|min:0.01|max:50000',
            'expires_at' => 'nullable|multiple_date_format:d-m-Y H:i:s,d-m-Y|after:now|before:10 years',
        ]);

        $voucher = Voucher::create($data);

        return VoucherResource::make($voucher);
    }

    /**
     * Show the specified voucher.
     *
     * @param Request $request
     * @param  int  $id
     * @return VoucherResource
     * 
     * @throws ModelNotFoundException
     */
    public function show(Request $request, int $id)
    {
        $voucher = QueryBuilder::for(Voucher::class)
            ->where('id', '=', $id)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->firstOrFail();

        return VoucherResource::make($voucher);
    }

    /**
     * Update the specified voucher in the system.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return VoucherResource
     * 
     * @throws ModelNotFoundException
     */
    public function update(Request $request, int $id)
    {
        $voucher = Voucher::findOrFail($id);

        $data = $request->validate([
            'memo' => 'nullable|string|max:191',
            'code' => "required|string|alpha_dash|max:36|min:4|unique:vouchers,code,{$voucher->id}",
            'uses' => 'required|numeric|max:2147483647|min:1',
            'credits' => 'required|numeric|min:0.01|max:50000',
            'expires_at' => 'nullable|multiple_date_format:d-m-Y H:i:s,d-m-Y|after:now|before:10 years',
        ]);

        $voucher->update($data);

        return VoucherResource::make($voucher->fresh());
    }

    /**
     * Remove the specified voucher from the system.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return VoucherResource
     * 
     * @throws ModelNotFoundException
     */
    public function destroy(Request $request, int $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return VoucherResource::make($voucher);
    }
}
