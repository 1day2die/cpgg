<?php
/**
 * Class FileManagerController.php | Date: 24.02.2024
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
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileManagerController extends Controller
{

    /**
     * Display the server file manager page.
     * @param Server $server
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function __invoke(Server $server, Request $request): Application|Factory|View
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $path = $request->input('path', '/') ?? '/';
        $pterodactyl = new Pterodactyl();
        $files = $pterodactyl->getFiles($server, $path);
        return view('kmanagement.fileManager')
            ->with('server', $server)
            ->with('files', $files)
            ->with('path', $path);
    }

    /**
     * Fetch upload url for the server.
     * @param Server $server
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function uploadUrl(Server $server, Request $request): JsonResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $path = request()->input('path', '/');
        $pterodactyl = new Pterodactyl();
        return response()->json([
            'url' => $pterodactyl->getUploadUrl($server, $path)
        ]);
    }

    /**
     * Download file from server.
     * @param Server $server
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadFile(Server $server, Request $request): BinaryFileResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->input('path', '/');
        $pterodactyl = new Pterodactyl();
        $file = $pterodactyl->downloadFile($server, $path);
        $tempFile = tempnam(sys_get_temp_dir(), explode('/', $path)[count(explode('/', $path)) - 1]);
        copy($file, $tempFile);

        return response()->download($tempFile, explode('/', $path)[count(explode('/', $path)) - 1]);
    }

    /**
     * Get contents of a file.
     * @param Server $server
     * @param Request $request
     * @return Application|ResponseFactory|Response
     * @throws Exception
     */
    public function getFileContents(Server $server, Request $request): Application|ResponseFactory|Response
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->input('path', '/');
        $pterodactyl = new Pterodactyl();
        return response($pterodactyl->getFileContent($server, $path));
    }

    /**
     * Update file in server.
     * @param Server $server
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateFile(Server $server, Request $request): JsonResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $request->validate([
            'path' => 'required|string',
            'content' => 'required|string'
        ]);

        $path = $request->input('path', '/');
        $content = $request->input('content', '');
        $pterodactyl = new Pterodactyl();
        return response()->json([
            'success' => $pterodactyl->updateFileContent($server, $path, $content)
        ]);
    }

    /**
     * Delete file from server.
     * @param Server $server
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function deleteFile(Server $server, Request $request): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->input('path', '/');
        $file = explode('/', $path);
        $root = dirname($path);
        $pterodactyl = new Pterodactyl();
        $pterodactyl->deleteFile($server, $root, $file[count($file) - 1]);

        return back()->with('success', 'File deleted successfully.');
    }

    /**
     * Compress files or directories.
     * @param Server $server
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function compress(Server $server, Request $request): RedirectResponse
    {
        if ($server->user_id !== auth()->id())
            abort(403, 'You are not allowed to access this server.');

        $request->validate([
            'root' => 'required|string',
            'files' => 'required|string'
        ]);
        $files = explode(',', $request->input('files', ''));
        $pterodactyl = new Pterodactyl();
        $pterodactyl->compressFiles($server, $request->input('root', '/'), $files);
        return back()->with('success', __('Files will be compressed and available for download shortly.'));
    }
}
