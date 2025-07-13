<?php

namespace App\Http\Controllers\Api;

use App\Models\Server;
use App\Http\Resources\ServerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Exception;

class ServerController extends BaseApiController
{
    public const ALLOWED_INCLUDES = ['product', 'user'];

    public const ALLOWED_FILTERS = ['name', 'suspended', 'identifier', 'pterodactyl_id', 'user_id', 'product_id'];

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $servers = QueryBuilder::for(Server::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters(self::ALLOWED_FILTERS)
            ->paginate($request->input('per_page') ?? 50);

        return ServerResource::collection($servers);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $serverId
     * @return ServerResource
     */
    public function show(string $serverId)
    {
        $server = QueryBuilder::for(Server::class)
            ->where('id', $serverId)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->firstOrFail();

        return ServerResource::make($server);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $serverId
     * @return ServerResource
     */
    public function destroy(string $serverId)
    {
        $server = QueryBuilder::for(Server::class)
            ->where('id', $serverId)
            ->firstOrFail();

        $server->delete();

        return ServerResource::make($server);
    }

    /**
     * suspend server
     *
     * @param  string  $serverId
     * @return ServerResource|JsonResponse
     */
    public function suspend(string $serverId)
    {
        $server = QueryBuilder::for(Server::class)
            ->where('id', $serverId)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->firstOrFail();

        try {
            $server->suspend();
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        return ServerResource::make($server);
    }

    /**
     * unsuspend server
     *
     * @param  string  $serverId
     * @return ServerResource|JsonResponse
     */
    public function unSuspend(string $serverId)
    {
        $server = QueryBuilder::for(Server::class)
            ->where('id', $serverId)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->firstOrFail();

        try {
            $server->unSuspend();
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        return ServerResource::make($server);
    }
}
