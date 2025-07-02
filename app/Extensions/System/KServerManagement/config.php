<?php
namespace App\Extensions\System\KServerManagement;

function getConfig(): array
{
    return [
        "name" => "K Server Management",
        "description" => "Allowing direct server control within the Control Panel",
        "RoutesIgnoreCsrf" => [],
        "enabled" => true
    ];
}
