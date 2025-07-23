<?php

namespace App\Http\Controllers\Api;

use App\Models\Server;
use App\Http\Resources\ServerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\Controller;
use Exception;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Server Management
 */
class ServerController extends Controller
{
    public const ALLOWED_INCLUDES = ['product', 'user'];
    public const ALLOWED_FILTERS = ['name', 'suspended', 'identifier', 'pterodactyl_id', 'user_id', 'product_id'];

    /**
     * Show a list of servers.
     * 
     * @queryParam include string Comma-separated list of related resources to include. Example: product,user
     * @queryParam filter[name] string Filter by server name. Example: My Server
     * @queryParam filter[suspended] string Filter by suspended status. Example: 2023-01-01
     * @queryParam filter[identifier] string Filter by server identifier. Example: server-123
     * @queryParam filter[pterodactyl_id] string Filter by Pterodactyl ID. Example: 456
     * @queryParam filter[user_id] string Filter by user ID. Example: 789
     * @queryParam filter[product_id] string Filter by product ID. Example: 101112
     * @queryParam per_page integer Number of items per page (default: 50). Example: 25
     * @queryParam page integer Page number. Example: 1
     *
     * @param  Request  $request
     * @return ServerResource
     */
    public function index(Request $request)
    {
        $servers = QueryBuilder::for(Server::class)
            ->allowedIncludes(self::ALLOWED_INCLUDES)
            ->allowedFilters([
                AllowedFilter::exact('suspended')->nullable(),
                ...self::ALLOWED_FILTERS
            ])
            ->paginate($request->input('per_page') ?? 50);

        return ServerResource::collection($servers);
    }

    /**
     * Show the specified server.
     * 
     * @queryParam include string Comma-separated list of related resources to include. Example: product,user
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
