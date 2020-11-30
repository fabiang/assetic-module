<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

use Assetic\Asset\AssetCollection;
use Assetic\AssetManager;
use Assetic\FilterManager as AsseticFilterManager;
use Assetic\Contracts\Factory\Worker\WorkerInterface;
use Assetic\Factory\AssetFactory;
use Assetic\AssetWriter;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Laminas\View\Renderer\RendererInterface as Renderer;
use Fabiang\AsseticBundle\View\StrategyInterface;
use Fabiang\AsseticBundle\Exception\InvalidArgumentException;

class Service
{

    public const DEFAULT_ROUTE_NAME = 'default';

    protected string $routeName      = self::DEFAULT_ROUTE_NAME;
    protected ?string $controllerName = null;
    protected ?string $actionName     = null;
    protected Configuration $configuration;

    /**
     * @var array<string, StrategyInterface>
     */
    protected array $strategy            = [];
    protected ?AssetManager $assetManager        = null;
    protected ?AssetWriter $assetWriter         = null;
    protected ?WorkerInterface $cacheBusterStrategy = null;
    protected ?AsseticFilterManager $filterManager       = null;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setRouteName(string $routeName): void
    {
        $this->routeName = $routeName;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function setAssetManager(AssetManager $assetManager): void
    {
        $this->assetManager = $assetManager;
    }

    public function getAssetManager(): AssetManager
    {
        if (null === $this->assetManager) {
            $this->assetManager = new AssetManager();
        }

        return $this->assetManager;
    }

    public function getAssetWriter(): AssetWriter
    {
        if (null === $this->assetWriter) {
            $webPath = $this->configuration->getWebPath();
            $this->assetWriter = new AssetWriter($webPath ?? '');
        }

        return $this->assetWriter;
    }

    public function setAssetWriter(AssetWriter $assetWriter): void
    {
        $this->assetWriter = $assetWriter;
    }

    public function getCacheBusterStrategy(): ?WorkerInterface
    {
        return $this->cacheBusterStrategy;
    }

    public function setCacheBusterStrategy(?WorkerInterface $cacheBusterStrategy): void
    {
        $this->cacheBusterStrategy = $cacheBusterStrategy;
    }

    public function setFilterManager(AsseticFilterManager $filterManager): void
    {
        $this->filterManager = $filterManager;
    }

    public function getFilterManager(): AsseticFilterManager
    {
        if (null === $this->filterManager) {
            $this->filterManager = new AsseticFilterManager();
        }

        return $this->filterManager;
    }

    public function setControllerName(?string $controllerName): void
    {
        $this->controllerName = $controllerName;
    }

    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    public function setActionName(?string $actionName): void
    {
        $this->actionName = $actionName;
    }

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    /**
     * Build collection of assets.
     */
    public function build(): void
    {
        $moduleConfiguration = $this->configuration->getModules();
        foreach ($moduleConfiguration as $configuration) {
            $factory     = $this->createAssetFactory($configuration);
            $collections = (array) $configuration['collections'];
            foreach ($collections as $name => $options) {
                $this->prepareCollection($options, $name, $factory);
            }
        }
    }

    private function cacheAsset(AssetInterface $asset): AssetInterface
    {
        if ($this->configuration->getCacheEnabled()) {
            return new AssetCache(
                $asset,
                new FilesystemCache($this->configuration->getCachePath())
            );
        }
        return $asset;
    }

    private function initFilters(array $filters): array
    {
        $result = [];

        $fm = $this->getFilterManager();

        foreach ($filters as $alias => $options) {
            $option = null;
            $name   = null;
            if (is_array($options)) {
                if (!isset($options['name'])) {
                    throw new Exception\InvalidArgumentException(
                        'Filter "' . $alias . '" required option "name"'
                    );
                }

                $name   = $options['name'];
                $option = isset($options['option']) ? $options['option'] : null;
            } elseif (is_string($options)) {
                $name = $options;
                unset($options);
            }

            if (!is_string($name)) {
                throw new InvalidArgumentException(
                    'Name of filter could not be found. '
                    . 'Did you provide the `name` option to the filter config?'
                );
            }

            if (is_numeric($alias)) {
                $alias = $name;
            }

            // Filter Id should have optional filter indicator "?"
            $filterId = ltrim($alias, '?');

            if (!$fm->has($filterId)) {
                if (is_array($option) && !empty($option)) {
                    /** @var class-string $name */
                    $r      = new \ReflectionClass($name);
                    /** @var \Assetic\Contracts\Filter\FilterInterface $filter */
                    $filter = $r->newInstanceArgs($option);
                } elseif ($option) {
                    /** @var \Assetic\Contracts\Filter\FilterInterface $filter */
                    $filter = new $name($option);
                } else {
                    /** @var \Assetic\Contracts\Filter\FilterInterface $filter */
                    $filter = new $name();
                }

                $fm->set($filterId, $filter);
            }

            $result[] = $alias;
        }

        return $result;
    }

    public function setupRenderer(Renderer $renderer): bool
    {
        $controllerConfig = $this->getControllerConfig();
        $actionConfig     = $this->getActionConfig();
        $config           = array_merge($controllerConfig, $actionConfig);

        if (count($config) == 0) {
            $config = $this->getRouterConfig();
        }

        // If we don't have any assets listed by now, or if we are mixing in
        // the default assets, then merge in the default assets to the config array
        $defaultConfig = $this->getDefaultConfig();
        if (count($config) == 0 || (isset($defaultConfig['options']['mixin']) && $defaultConfig['options']['mixin'])) {
            $config = array_merge($defaultConfig['assets'], $config);
        }

        if (count($config) > 0) {
            $this->setupRendererFromOptions($renderer, $config);

            return true;
        }

        return false;
    }

    public function getDefaultConfig(): array
    {
        $defaultDefinition = $this->configuration->getDefault();

        return $defaultDefinition ? $defaultDefinition : [];
    }

    /**
     * @return array|mixed
     */
    public function getRouterConfig()
    {
        $assetOptions = $this->configuration->getRoute($this->getRouteName());

        return $assetOptions ? $assetOptions : [];
    }

    public function getControllerConfig(): array
    {
        $assetOptions = [];

        $controllerName = $this->getControllerName();
        if (null !== $controllerName) {
            $assetOptions = $this->configuration->getController($controllerName);
            if ($assetOptions) {
                if (array_key_exists('actions', $assetOptions)) {
                    unset($assetOptions['actions']);
                }
            } else {
                $assetOptions = [];
            }
        }

        return $assetOptions;
    }

    public function getActionConfig(): array
    {
        $actionAssets   = [];
        $controllerName = $this->getControllerName();
        if (null !== $controllerName) {
            $assetOptions = $this->configuration->getController($controllerName);
            $actionName   = $this->getActionName();
            if (null !== $actionName && $assetOptions && array_key_exists('actions', $assetOptions) && array_key_exists($actionName, $assetOptions['actions'])
            ) {
                $actionAssets = $assetOptions['actions'][$actionName];
            }
        }

        return $actionAssets;
    }

    public function setupRendererFromOptions(Renderer $renderer, array $options): void
    {
        if (!$this->hasStrategyForRenderer($renderer)) {
            throw new Exception\InvalidArgumentException(sprintf(
                    'no strategy defined for renderer "%s"',
                    $this->getRendererName($renderer)
            ));
        }

        $strategy = $this->getStrategyForRenderer($renderer);
        if (null !== $strategy) {
            while ($assetAlias = array_shift($options)) {
                $assetAlias = ltrim($assetAlias, '@');

                /** @var AssetInterface $asset */
                $asset = $this->getAssetManager()->get($assetAlias);
                // Prepare view strategy
                $strategy->setupAsset($asset);
            }
        }
    }

    public function hasStrategyForRenderer(Renderer $renderer): bool
    {
        $rendererName = $this->getRendererName($renderer);

        return (bool) $this->configuration->getStrategyNameForRenderer($rendererName);
    }

    /**
     * Get strategy to setup assets for given $renderer.
     *
     * @throws Exception\DomainException
     * @throws Exception\InvalidArgumentException
     */
    public function getStrategyForRenderer(Renderer $renderer): ?StrategyInterface
    {
        if (!$this->hasStrategyForRenderer($renderer)) {
            return null;
        }

        $rendererName = $this->getRendererName($renderer);
        if (!isset($this->strategy[$rendererName])) {
            $strategyClass = $this->configuration->getStrategyNameForRenderer($rendererName);

            if (null === $strategyClass) {
                throw new Exception\InvalidArgumentException(
                    sprintf(
                        'No strategy defined for renderer "%s"',
                        get_class($renderer)
                    )
                );
            }

            if (!class_exists($strategyClass, true)) {
                throw new Exception\InvalidArgumentException(
                    sprintf(
                        'strategy class "%s" dosen\'t exists',
                        $strategyClass
                    )
                );
            }

            $instance = new $strategyClass();

            if (!($instance instanceof StrategyInterface)) {
                throw new Exception\DomainException(
                    sprintf(
                        'strategy class "%s" is not instanceof "Fabiang\AsseticBundle\View\StrategyInterface"',
                        $strategyClass
                    )
                );
            }

            $this->strategy[$rendererName] = $instance;
        }

        /** @var \Fabiang\AsseticBundle\View\StrategyInterface $strategy */
        $strategy = $this->strategy[$rendererName];
        $strategy->setBaseUrl($this->configuration->getBaseUrl());
        $strategy->setBasePath($this->configuration->getBasePath());
        $strategy->setDebug($this->configuration->isDebug());
        $strategy->setCombine($this->configuration->isCombine());
        $strategy->setRenderer($renderer);

        return $strategy;
    }

    /**
     * Get renderer name from $renderer object.
     */
    public function getRendererName(Renderer $renderer): string
    {
        return get_class($renderer);
    }

    /**
     * Gets the service configuration.
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function createAssetFactory(array $configuration): AssetFactory
    {
        $factory = new AssetFactory($configuration['root_path']);
        $factory->setAssetManager($this->getAssetManager());
        $factory->setFilterManager($this->getFilterManager());
        $worker  = $this->getCacheBusterStrategy();
        if ($worker instanceof WorkerInterface) {
            $factory->addWorker($worker);
        }
        /**
         * @psalm-suppress InvalidArgument Upstream type-hint error
         */
        $factory->setDebug($this->configuration->isDebug());

        return $factory;
    }

    public function moveRaw(
        AssetCollection $asset,
        ?string $targetPath,
        AssetFactory $factory,
        bool $disableSourcePath = false
    ): void
    {
        foreach ($asset as $value) {
            $sourcePath = $value->getSourcePath() ?? '';

            /** @var AssetInterface $value */
            if ($disableSourcePath) {
                $value->setTargetPath(( $targetPath ?? '' ) . basename($sourcePath));
            } else {
                $value->setTargetPath(( $targetPath ?? '' ) . $sourcePath);
            }

            $value = $this->cacheAsset($value);
            $this->writeAsset($value, $factory);
        }
    }

    public function prepareCollection(array $options, string $name, AssetFactory $factory): void
    {
        $assets            = isset($options['assets']) ? $options['assets'] : [];
        $filters           = isset($options['filters']) ? $options['filters'] : [];
        $options           = isset($options['options']) ? $options['options'] : [];
        $options['output'] = isset($options['output']) ? $options['output'] : $name;
        $moveRaw           = isset($options['move_raw']) && $options['move_raw'];
        $targetPath        = !empty($options['targetPath']) ? $options['targetPath'] : '';
        if (substr($targetPath, -1) != DIRECTORY_SEPARATOR) {
            $targetPath .= DIRECTORY_SEPARATOR;
        }

        $filters = $this->initFilters($filters);
        $asset   = $factory->createAsset($assets, $filters, $options);

        // Allow to move all files 1:1 to new directory
        // its particularly useful when this assets are i.e. images.
        if ($moveRaw) {
            if (isset($options['disable_source_path'])) {
                $this->moveRaw($asset, $targetPath, $factory, $options['disable_source_path']);
            } else {
                $this->moveRaw($asset, $targetPath, $factory);
            }
        } else {
            $asset = $this->cacheAsset($asset);
            $this->getAssetManager()->set($name, $asset);
            // Save asset on disk
            $this->writeAsset($asset, $factory);
        }
    }

    /**
     * Write $asset to public directory.
     *
     * @param AssetInterface       $asset     Asset to write
     * @param AssetFactory $factory   The factory this asset was generated with
     */
    public function writeAsset(AssetInterface $asset, AssetFactory $factory): void
    {
        // We're not interested in saving assets on request
        if (!$this->configuration->getBuildOnRequest()) {
            return;
        }

        // Write asset on disk on every request
        if (!$this->configuration->getWriteIfChanged()) {
            $this->write($asset, $factory);

            return;
        }

        $created   = false;
        $isChanged = false;

        $targetPath = $asset->getTargetPath();
        if (null !== $targetPath) {
            $target = $this->configuration->getWebPath($targetPath);

            if (null !== $target) {
                $created   = is_file($target);
                $isChanged = $created && filemtime($target) < $factory->getLastModified($asset);
            }
        }

        // And long requested optimization
        if (!$created || $isChanged) {
            $this->write($asset, $factory);
        }
    }

    /**
     * @param AssetInterface       $asset     Asset to write
     * @param AssetFactory $factory   The factory this asset was generated with
     */
    protected function write(AssetInterface $asset, AssetFactory $factory): void
    {
        $umask = $this->configuration->getUmask();
        if (null !== $umask) {
            $umask = umask($umask);
        }

        if ($this->configuration->isDebug() && !$this->configuration->isCombine() && ($asset instanceof AssetCollection)
        ) {
            foreach ($asset as $item) {
                $this->writeAsset($item, $factory);
            }
        } else {
            $this->getAssetWriter()->writeAsset($asset);
        }

        if (null !== $umask) {
            umask($umask);
        }
    }

}
