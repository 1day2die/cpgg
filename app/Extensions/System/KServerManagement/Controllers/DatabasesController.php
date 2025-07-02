<?php
/**
 * Class DatabasesController.php | Date: 24.02.2024
 * Created by Krzysztof Haller
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
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DatabasesController extends Controller
{

    /**
     * Display the server databases management page.
     * @param Server $server
     * @return Application|Factory|View|RedirectResponse
     * @throws \Exception
     */
    public function __invoke(Server $server): View|Factory|RedirectResponse|Application
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $serverLimits = $pterodactyl->getServerDetails($server)['feature_limits'];

        if($serverLimits['databases'] === 0)
            return back()->with('error', 'Databases are not enabled for this server.');

        return view('kmanagement.databases')
            ->with('server', $server)
            ->with('serverLimits', $serverLimits)
            ->with('databases', $pterodactyl->getDatabases($server))
            ->with('limits', $serverLimits);
    }

    /**
     * Remove the specified database from the server.
     * @param Server $server
     * @param string $databaseId
     * @return RedirectResponse
     * @throws \Exception
     */
    public function delete(Server $server, string $databaseId): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $pterodactyl->deleteDatabase($server, $databaseId);
        return back()->with('success', __('Database has been removed.'));
    }


    /**
     * Create a new database on the server.
     * @param Server $server
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function create(Server $server, Request $request): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $request->validate([
            'database' => 'required|string',
            'remote' => 'required|string',
        ]);

        $pterodactyl = new Pterodactyl();
        $pterodactyl->createDatabase($server, $request->input('database'), $request->input('remote'));
        return back()->with('success', __('Database has been created.'));
    }

    /**
     * Rotate the password for the specified database.
     * @param Server $server
     * @param string $databaseId
     * @return RedirectResponse
     * @throws \Exception
     */
    public function rotate(Server $server, string $databaseId): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        Pterodactyl::rotateDatabasePassword($server, $databaseId);
        return back()->with('success', __('Database password has been rotated.'));
    }
}
