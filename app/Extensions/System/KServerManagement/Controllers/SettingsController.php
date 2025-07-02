<?php
/**
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
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    /**
     * Show the server settings page.
     * @param Server $server
     * @return Application|Factory|View
     * @throws Exception
     */
    public function __invoke(Server $server): View|Factory|Application
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $startup = collect($pterodactyl->getStartup($server)['data'])->filter(function ($item) {
            return $item['attributes']['is_editable'] === true;
        });

        $dockerImages = $pterodactyl->getDockerImages($server);
        $pterodactylServer = $pterodactyl->getServerDetails($server);
        return view('kmanagement.settings')
            ->with('server', $server)
            ->with('startup', $startup)
            ->with('dockerImages', $dockerImages)
            ->with('pterodactylServer', $pterodactylServer);
    }

    /**
     * Update the server settings.
     * @param Server $server
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(Server $server, Request $request): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        if($request->has('startup_variables')) {
            foreach ($request->input('startup_variables') as $key => $value) {
                $pterodactyl->updateServerVariable($server, ['key' => $key, 'value' => $value]);
            }
        }

        if($request->has('docker_image')) {
            $pterodactyl->updateDockerImage($server, $request->input('docker_image'));
        }

        return back()->with('success', __('Server settings updated successfully.'));
    }
}
