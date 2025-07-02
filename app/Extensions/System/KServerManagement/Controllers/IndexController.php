<?php
/**
 * Class IndexController.php | Date: 24.02.2024
 * Distributed by 1Day2Die
 *
 * All rights reserved
 *
 * Redistribution and use in source and binary forms, with or without modification, are not permitted without the express permission of 1Day2Die
 *
 * Contact: dleipe@hafuga.de
 *
 * Website: https://ctrlpanel.gg
 *
 */

namespace App\Extensions\System\KServerManagement\Controllers;

use App\Extensions\System\KServerManagement\Classes\Pterodactyl;
use App\Http\Controllers\Controller;
use App\Models\Server;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class IndexController extends Controller
{

    /**
     * Display the server management index page.
     * @param Server $server
     * @return Application|Factory|View
     * @throws Exception
     */
    public function __invoke(Server $server): View|Factory|Application
    {
        if($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $serverDetails = $pterodactyl->getServerDetails($server);
        $allocations = $pterodactyl->getServerAllocations($server);

        return view('kmanagement.index')
            ->with('server', $server)
            ->with('sftpData', $serverDetails['sftp_details'])
            ->with('serverLimits', $serverDetails['limits'])
            ->with('serverAllocations', $allocations);
    }

    /**
     * Return the websocket token for the server.
     * @param Server $server
     * @return JsonResponse
     * @throws Exception
     */
    public function websocketToken(Server $server): JsonResponse
    {
        if($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        return response()->json($pterodactyl->getServerWebsocketDetails($server));
    }
}
