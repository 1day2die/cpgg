<?php

namespace App\Extensions\System\KServerManagement\Classes;

use App\Classes\PterodactylClient as PterodactylBase;
use App\Models\Server;
use App\Settings\PterodactylSettings;
use App\Settings\ServerSettings;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

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
class Pterodactyl extends PterodactylBase
{
    protected PterodactylSettings $ptero_settings;
    protected ServerSettings $server_settings;

    public function __construct(PterodactylSettings $ptero_settings = null)
    {
        $this->ptero_settings = $ptero_settings ?? new PterodactylSettings();
        $this->server_settings = new ServerSettings();
        parent::__construct($this->ptero_settings);
    }

    public function getException(string $message = '', int|null $status = 0): Exception
    {
        // Die Methode ist jetzt public in der Parent-Klasse, daher public aufrufen
        return parent::getException($message, $status);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $kilobyte = 1024;
        $megabyte = $kilobyte * 1024;
        $gigabyte = $megabyte * 1024;
        if ($bytes < $kilobyte) {
            return $bytes . ' B';
        } elseif ($bytes < $megabyte) {
            return round($bytes / $kilobyte, $precision) . ' KB';
        } elseif ($bytes < $gigabyte) {
            return round($bytes / $megabyte, $precision) . ' MB';
        } else {
            return round($bytes / $gigabyte, $precision) . ' GB';
        }
    }

    public function getServerWebsocketDetails(Server $server): Collection
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/websocket");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response->json()['data']);
    }

    public function getServerDetails(Server $server): Collection
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        if ($response->failed()) {
            throw $this->getException('Failed to get ptero Server details KManagement - ', $response->status());
        }
        return collect($response->json()['attributes']);
    }

    public function getServerAllocations(Server $server): Collection
    {
        try {
            $response = $this->getServerDetails($server);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response['relationships']['allocations']['data'])->map(function ($allocation) {
            return [
                'id' => $allocation['attributes']['id'],
                'ip' => $allocation['attributes']['ip'],
                'port' => $allocation['attributes']['port'],
                'primary' => $allocation['attributes']['is_default'],
            ];
        });
    }

    public function getServerSftpDetails(Server $server): Collection
    {
        try {
            $response = $this->getServerDetails($server);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response['attributes']['sftp_details'])->map(function ($sftp) use ($server) {
            return [
                'address' => $sftp['ip'],
                'port' => $sftp['port'],
                'username' => "{$server->user->name}.{$server->identifier}"
            ];
        });
    }

    public function getFiles(Server $server, string $path = '/'): Collection
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/files/list?directory={$path}");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response->json()['data'])->map(function ($file) {
            return [
                'name' => $file['attributes']['name'],
                'is_file' => $file['attributes']['is_file'],
                'size' => $this->formatBytes($file['attributes']['size']),
                'date' => $file['attributes']['created_at'],
            ];
        });
    }

    public function getFileContent(Server $server, string $path): string
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/files/contents?file={$path}");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->body();
    }

    public function deleteFile(Server $server, string $path, string $file): bool
    {
        try {
            $response = $this->client->post("client/servers/{$server->identifier}/files/delete", [
                'root' => $path,
                'files' => [$file]
            ]);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    public function updateFileContent(Server $server, string $path, string $content): bool
    {
        try {
            $response = $this->client
                ->withBody($content, 'text/plain')
                ->post("client/servers/{$server->identifier}/files/write?file={$path}");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    public function downloadFile(Server $server, string $file): string
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/files/download?file=$file");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->body();
    }


    /**
     * Fetch upload url for the server.
     * @param Server $server
     * @param string|null $path
     * @return string
     * @throws Exception
     */
    public function getUploadUrl(Server $server, ?string $path = '/'): string
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/files/upload");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->json()['attributes']['url'] . "&directory=$path";
    }

    /**
     * Get databases for a server
     * @param Server $server
     * @return Collection
     * @throws Exception
     */
    public function getDatabases(Server $server): Collection
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/databases?include=password");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response->json()['data'])->map(function ($database) {
            return [
                'id' => $database['attributes']['id'],
                'name' => $database['attributes']['name'],
                'username' => $database['attributes']['username'],
                'connections_from' => $database['attributes']['connections_from'],
                'host' => $database['attributes']['host']['address'] . ':' . $database['attributes']['host']['port'],
                'password' => $database['attributes']['relationships']['password']['attributes']['password']
            ];
        });
    }


    /**
     * Create a database for a server
     * @param Server $server
     * @param string $name
     * @param string $remote
     * @return bool
     * @throws Exception
     */
    public function createDatabase(Server $server, string $name, string $remote): bool
    {
        try {
            $response = $this->client->post("client/servers/{$server->identifier}/databases", [
                'database' => $name,
                'remote' => $remote
            ]);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Delete a database for a server
     * @param Server $server
     * @param string $database
     * @return bool
     * @throws Exception
     */
    public function deleteDatabase(Server $server, string $database): bool
    {
        try {
            $response = $this->client->delete("client/servers/{$server->identifier}/databases/$database");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Rotate a database password
     * @param Server $server
     * @param string $database
     * @return bool
     * @throws Exception
     */
    public function rotateDatabasePassword(Server $server, string $database): bool
    {
        try {
            $response = $this->client->post("client/servers/{$server->identifier}/databases/$database/rotate-password");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Get backups for a server
     * @param Server $server
     * @return Collection
     * @throws Exception
     */
    public function getBackups(Server $server): Collection
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/backups?per_page=" . $this->ptero_settings->per_page_limit);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response->json()['data'])->map(function ($backup) {
            return [
                'id' => $backup['attributes']['uuid'],
                'name' => $backup['attributes']['name'],
                'size' => $this->formatBytes($backup['attributes']['bytes']),
                'created' => $backup['attributes']['completed_at'] !== null,
                'date' => $backup['attributes']['completed_at'] ?? $backup['attributes']['created_at'],
            ];
        });
    }

    /**
     * Download a backup
     * @param Server $server
     * @param string $backup
     * @return string
     * @throws Exception
     */
    public function downloadBackup(Server $server, string $backup): string
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/backups/$backup/download");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->json()['attributes']['url'];
    }

    /**
     * Create a backup
     * @param Server $server
     * @return bool
     * @throws Exception
     */
    public function createBackup(Server $server): bool
    {
        try {
            $response = $this->client->post("client/servers/{$server->identifier}/backups");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Delete a backup
     * @param Server $server
     * @param string $backup
     * @return bool
     * @throws Exception
     */
    public function deleteBackup(Server $server, string $backup): bool
    {
        try {
            $response = $this->client->delete("client/servers/{$server->identifier}/backups/$backup");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Run re-installation for a server
     * @param Server $server
     * @return bool
     * @throws Exception
     */
    public function reinstallServer(Server $server): bool
    {
        try {
            $response = $this->client->post("client/servers/{$server->identifier}/reinstall");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Get startup data for a server
     * @param Server $server
     * @return Collection
     * @throws Exception
     */
    public function getStartup(Server $server): Collection
    {
        try {
            $response = $this->client->get("client/servers/{$server->identifier}/startup");
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response->json());
    }

    /**
     * Get docker images for a server
     * @param Server $server
     * @return Collection
     * @throws Exception
     */
    public function getDockerImages(Server $server): Collection
    {
        try {
            $response = $this->getStartup($server);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return collect($response['meta']['docker_images'])->map(function ($image, $name) {
            return [
                'name' => $name,
                'image' => $image
            ];
        });
    }

    /**
     * Update server variables
     * @param Server $server
     * @param array $variable
     * @return bool
     * @throws Exception
     */
    public function updateServerVariable(Server $server, array $variable): bool
    {
        try {
            $response = $this->client->put("client/servers/{$server->identifier}/startup/variable", $variable);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * Update docker image for a server
     * @param Server $server
     * @param string $image
     * @return bool
     * @throws Exception
     */
    public function updateDockerImage(Server $server, string $image): bool
    {
        try {
            $response = $this->client->put("client/servers/{$server->identifier}/settings/docker-image", [
                'docker_image' => $image
            ]);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }
        return $response->successful();
    }

    /**
     * @param Server $server
     * @param $root
     * @param array $files
     * @return bool
     * @throws Exception
     */
    public function compressFiles(Server $server, $root, array $files): bool
    {
        try {
            $response = $this->client->post("client/servers/{$server->identifier}/files/compress", [
                'root' => $root,
                'files' => $files
            ]);
        } catch (Exception $e) {
            throw $this->getException($e->getMessage(), $e->getCode());
        }

        return $response->successful();
    }
}
