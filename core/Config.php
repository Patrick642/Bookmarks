<?php
namespace core;

class Config
{
    private $configFile = null;
    private $configsPath = ROOT_DIR . '/config/';

    public function get(string $configName): void
    {
        switch (ENV) {
            case 'dev':
                $this->configFile = $configName . '_local.php';
                break;

            case 'prod':
            default:
                $this->configFile = $configName . '.php';
                break;
        }

        $path = $this->configsPath . $this->configFile;

        if (file_exists($path))
            include_once $path;
        else
            throw new \ErrorException($path . ' not found!');
    }
}