<?php

namespace App\Http\Controllers\Api;

use App\Models\Server;
use App\Http\Resources\ServerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\Controller;
use Exception;

class ServerController extends Controller
{
    public const ALLOWED_INCLUDES = ['product', 'user'];
    public const ALLOWED_FILTERS = ['name', 'suspended', 'identifier', 'pterodactyl_id', 'user_id', 'product_id'];

    /**
     * Show a list of servers.
     *
     * @param  Request  $request
     * @return ServerResource
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
     * Show the specified server.
     *
     * @param  Request  $request
     * @param  string  $serverId
     * @return ServerResource
     */
    public function show(Request $request, string $serverId)
    {
        $server = QueryBuilder::for(Server::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->where('id', $serverId)
            ->firstOrFail();

        return ServerResource::make($server);
    }

    /**
     * Remove the specified server from the system.
     *
     * @param  Request  $request
     * @param  Server  $server
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Server $server)
    {
        $server->delete();

        return response()->noContent();
    }

    /**
     * Suspend server.
     *
     * @param  Request  $request
     * @param  Server  $server
     * @return ServerResource|JsonResponse
     */
    public function suspend(Request $request, Server $server)
    {
        try {
            $server->suspend();
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        return ServerResource::make($server);
    }

    /**
     * Unsuspend server.
     *
     * @param  Request  $request
     * @param  Server  $server
     * @return ServerResource|JsonResponse
     */
    public function unSuspend(Request $request, Server $server)
    {
        try {
            $server->unSuspend();
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        return ServerResource::make($server);
    }
}
