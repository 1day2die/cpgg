<?php
/**
 * Class BackupsController.php | Date: 25.02.2024
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupsController extends Controller
{

    /**
     * Display the server backups management page.
     * @param Server $server
     * @return Application|Factory|View|RedirectResponse
     * @throws Exception
     */
    public function __invoke(Server $server): Application|Factory|View|RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $serverLimits = $pterodactyl->getServerDetails($server)['feature_limits'];

        if($serverLimits['backups'] === 0)
            return back()->with('error', 'Backups are not enabled for this server.');

        return view('kmanagement.backups')
            ->with('server', $server)
            ->with('serverLimits', $serverLimits)
            ->with('backups', $pterodactyl->getBackups($server)->filter(fn($backup) => $backup['created']))
            ->with('limits', $serverLimits);
    }

    /**
     * Remove the specified backup from the server.
     * @param Server $server
     * @param string $backupId
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(Server $server, string $backupId): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $pterodactyl->deleteBackup($server, $backupId);

        return back()->with('success', __('Backup has been deleted'));
    }

    /**
     * Create a new backup for the server.
     * @param Server $server
     * @return RedirectResponse
     * @throws Exception
     */
    public function create(Server $server): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $pterodactyl = new Pterodactyl();
        $pterodactyl->createBackup($server);

        return back()->with('success', __('Backup has been created'));
    }

    /**
     * Download the specified backup from the server.
     * @param Server $server
     * @param string $backupId
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function download(Server $server, string $backupId): BinaryFileResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $backup = Pterodactyl::downloadBackup($server, $backupId);
        $tempFile = tempnam(sys_get_temp_dir(), "backup-{$backupId}.tar.gz");
        copy($backup, $tempFile);

        return response()->download($tempFile, "backup-{$backupId}.tar.gz");
    }

}
