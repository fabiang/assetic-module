<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

class ConfigProvider
{
    public function __invoke(): array
    {
        $config = include __DIR__ . '/../config/module.config.php';

        $config['dependencies'] = $config['service_manager'];
        unset($config['service_manager']);

        return $config;
    }
}
