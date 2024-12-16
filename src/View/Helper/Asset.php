<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\View\Helper;

use Assetic\Asset\AssetCollection;
use Assetic\Contracts\Asset\AssetInterface;
use Fabiang\AsseticBundle\Exception;
use Fabiang\AsseticBundle\Exception\InvalidArgumentException;
use Fabiang\AsseticBundle\Factory\ServiceFactory;
use Fabiang\AsseticBundle\Service;
use Laminas\View\Helper\Placeholder\Container;
use Psr\Container\ContainerInterface;

use function pathinfo;
use function strtolower;

use const PATHINFO_EXTENSION;
use const PHP_EOL;

/**
 * @psalm-suppress PropertyNotSetInConstructor Upstream issue with contructor
 * @psalm-suppress MissingTemplateParam
 */
class Asset extends Container\AbstractStandalone
{
    protected Service $service;
    protected ?string $baseUrl  = null;
    protected ?string $basePath = null;

    public function __construct(ContainerInterface $container)
    {
        $serviceFactory = new ServiceFactory();
        $this->service  = $serviceFactory($container, Service::class, []);
        $this->service->build();

        $this->baseUrl  = $this->service->getConfiguration()->getBaseUrl();
        $this->basePath = $this->service->getConfiguration()->getBasePath();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(string $collectionName, array $options = []): string
    {
        /**
         * @psalm-suppress UndefinedDocblockClass Upstram bug in `@return`
         */
        if (! $this->service->getAssetManager()->has($collectionName)) {
            throw new Exception\InvalidArgumentException(
                'Collection "' . $collectionName . '" does not exist.'
            );
        }

        $asset = $this->service->getAssetManager()->get($collectionName);

        return $this->setupAsset($asset, $options);
    }

    protected function setupAsset(AssetInterface $asset, array $options = []): string
    {
        $ret = '';

        if (
            $this->service->getConfiguration()->isDebug()
            && ! $this->service->getConfiguration()->isCombine()
            && $asset instanceof AssetCollection
        ) {
            // Move assets as single instance not as a collection
            /** @var AssetCollection $value */
            foreach ($asset as $value) {
                $ret .= $this->helper($value, $options) . PHP_EOL;
            }
        } else {
            $ret .= $this->helper($asset, $options) . PHP_EOL;
        }

        return $ret;
    }

    protected function helper(AssetInterface $asset, array $options = []): string
    {
        $path = $this->str($this->baseUrl) . $this->str($this->basePath) . $this->str($asset->getTargetPath());

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (isset($options['addFileMTime']) && $options['addFileMTime']) {
            $path .= '?' . (string) $asset->getLastModified();
        }

        switch ($extension) {
            case 'js':
                return $this->getScriptTag($path, $options);

            case 'css':
                return $this->getStylesheetTag($path, $options);
        }

        return '';
    }

    private function str(?string $s): string
    {
        return $s ?? '';
    }

    protected function getScriptTag(string $path, array $options = []): string
    {
        $type = isset($options['type']) && ! empty($options['type']) ? $options['type'] : 'text/javascript';

        return '<script type="' . $this->escape($type) . '" src="' . $this->escape($path) . '"></script>';
    }

    protected function getStylesheetTag(string $path, array $options = []): string
    {
        $media = isset($options['media']) && ! empty($options['media']) ? $options['media'] : 'screen';
        $type  = isset($options['type']) && ! empty($options['type']) ? $options['type'] : 'text/css';
        $rel   = isset($options['rel']) && ! empty($options['rel']) ? $options['rel'] : 'stylesheet';

        return '<link href="' . $this->escape($path)
            . '" media="' . $this->escape($media)
            . '" rel="' . $this->escape($rel)
            . '" type="' . $this->escape($type) . '">';
    }
}
