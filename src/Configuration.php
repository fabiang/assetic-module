<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle;

use Laminas\Stdlib;
use Traversable;

use function array_key_exists;
use function array_map;
use function explode;
use function implode;
use function ltrim;
use function method_exists;
use function preg_match;
use function rtrim;
use function strtolower;
use function trim;

class Configuration
{
    /**
     * Debug option that is passed to Assetic.
     */
    protected bool $debug = false;

    /**
     * Combine option giving the opportunity not to combine the assets in debug mode.
     */
    protected bool $combine = true;

    /**
     * Should build assets on request.
     */
    protected bool $buildOnRequest = true;

    /**
     * Full path to public directory where assets will be generated.
     */
    protected ?string $webPath = null;

    /**
     * Full path to cache directory.
     */
    protected ?string $cachePath = null;

    /**
     * Is cache enabled.
     */
    protected bool $cacheEnabled = false;

    /**
     * The base url.
     *
     * By default this value is set from "\Laminas\Http\PhpEnvironment\Request::getBaseUrl()"
     *
     * Example:
     * <code>
     * http://example.com/
     * </code>
     */
    protected ?string $baseUrl = null;

    /**
     * The base path.
     *
     * By default this value is set from "\Laminas\Http\PhpEnvironment\Request::getBasePath()"
     *
     * Example:
     * <code>
     * <baseUrl>/~jdo/
     * </code>
     */
    protected ?string $basePath = null;

    /**
     * Asset will be save on disk, only when it's modification time was changed
     */
    protected bool $writeIfChanged = true;

    /**
     * Default options.
     */
    protected array $default = [
        'assets'  => [],
        'options' => [],
    ];

    /**
     * Map of routes names and assets configuration.
     */
    protected array $routes = [];

    /**
     * Map of modules names and assets configuration.
     */
    protected array $modules = [];

    /**
     * Map of controllers names and assets configuration.
     */
    protected array $controllers = [];

    /**
     * Map of strategies that will be choose to setup Assetic\AssetInterface
     * for particular Laminas\View\Renderer\RendererInterface
     */
    protected array $rendererToStrategy = [];

    /**
     * List of error types occurring in EVENT_DISPATCH_ERROR that will use
     * this module to render assets.
     */
    protected array $acceptableErrors = [];

    /**
     * Umask
     */
    protected ?int $umask = null;

    public function __construct(?iterable $config = null)
    {
        if (null !== $config) {
            if ($config instanceof Traversable) {
                $this->processArray(Stdlib\ArrayUtils::iteratorToArray($config));
            } else {
                $this->processArray($config);
            }
        }
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $flag): void
    {
        $this->debug = $flag;
    }

    public function isCombine(): bool
    {
        return $this->combine;
    }

    public function setCombine(bool $flag): void
    {
        $this->combine = $flag;
    }

    public function setWebPath(?string $path): void
    {
        $this->webPath = $path;
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function getWebPath(?string $file = null): ?string
    {
        if (null === $this->webPath) {
            throw new Exception\RuntimeException('Web path is not set');
        }

        if (null !== $file) {
            return rtrim($this->webPath, '/\\') . '/' . ltrim($file, '/\\');
        }

        return $this->webPath;
    }

    public function setCachePath(?string $path): void
    {
        $this->cachePath = $path;
    }

    public function getCachePath(): ?string
    {
        return $this->cachePath;
    }

    public function setCacheEnabled(bool $cacheEnabled): void
    {
        $this->cacheEnabled = $cacheEnabled;
    }

    public function getCacheEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    public function setDefault(array $default): void
    {
        if (! isset($default['assets'])) {
            $default['assets'] = [];
        }

        if (! isset($default['options'])) {
            $default['options'] = [];
        }

        $this->default = $default;
    }

    public function getDefault(): array
    {
        return $this->default;
    }

    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getRoute(string $name, ?array $default = null): ?array
    {
        $assets       = [];
        $routeMatched = false;

        // Merge all assets configuration for which regular expression matches route
        foreach ($this->routes as $spec => $config) {
            if (preg_match('(^' . $spec . '$)i', $name)) {
                $routeMatched = true;
                $assets       = Stdlib\ArrayUtils::merge($assets, (array) $config);
            }
        }

        // Only return default if none regular expression matched
        return $routeMatched ? $assets : $default;
    }

    public function setControllers(array $controllers): void
    {
        $this->controllers = $controllers;
    }

    public function getControllers(): array
    {
        return $this->controllers;
    }

    public function getController(string $name, ?array $default = null): ?array
    {
        return array_key_exists($name, $this->controllers) ? $this->controllers[$name] : $default;
    }

    public function setModules(array $modules): void
    {
        $this->modules = [];
        foreach ($modules as $name => $options) {
            $this->addModule($name, $options);
        }
    }

    public function addModule(string $name, array $options): void
    {
        $lowername                 = strtolower($name);
        $this->modules[$lowername] = $options;
    }

    public function getModules(): array
    {
        return $this->modules;
    }

    public function getModule(string $name, ?array $default = null): ?array
    {
        $lowername = strtolower($name);
        return array_key_exists($lowername, $this->modules) ? $this->modules[$lowername] : $default;
    }

    public function detectBaseUrl(): bool
    {
        return null === $this->baseUrl || 'auto' === $this->baseUrl;
    }

    public function setBaseUrl(?string $baseUrl): void
    {
        if (null !== $baseUrl && 'auto' !== $baseUrl) {
            $baseUrl = rtrim($baseUrl, '/') . '/';
        }
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBasePath(?string $basePath): void
    {
        if (null !== $basePath) {
            $basePath  = trim($basePath, '/');
            $basePath .= '/';
        }
        $this->basePath = $basePath;
    }

    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    protected function processArray(iterable $config): void
    {
        foreach ($config as $key => $value) {
            $setter = $this->assembleSetterNameFromConfigKey($key);
            $this->{$setter}($value);
        }
    }

    protected function assembleSetterNameFromConfigKey(string $key): string
    {
        $parts  = explode('_', $key);
        $parts  = array_map('ucfirst', $parts);
        $setter = 'set' . implode('', $parts);
        if (! method_exists($this, $setter)) {
            throw new Exception\BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                    . 'have a matching ' . $setter . ' setter method '
                    . 'which must be defined'
            );
        }

        return $setter;
    }

    public function setRendererToStrategy(array $strategyForRenderer): void
    {
        $this->rendererToStrategy = $strategyForRenderer;
    }

    public function addRendererToStrategy(string $rendererClass, string $strategyClass): void
    {
        $this->rendererToStrategy[$rendererClass] = $strategyClass;
    }

    public function getStrategyNameForRenderer(string $rendererName, ?string $default = null): ?string
    {
        return array_key_exists($rendererName, $this->rendererToStrategy)
            ? $this->rendererToStrategy[$rendererName] : $default;
    }

    public function setAcceptableErrors(array $acceptableErrors): void
    {
        $this->acceptableErrors = $acceptableErrors;
    }

    public function getAcceptableErrors(): array
    {
        return $this->acceptableErrors;
    }

    public function getUmask(): ?int
    {
        return $this->umask;
    }

    public function setUmask(?int $umask): void
    {
        $this->umask = $umask;
    }

    public function setBuildOnRequest(bool $flag): void
    {
        $this->buildOnRequest = $flag;
    }

    public function getBuildOnRequest(): bool
    {
        return $this->buildOnRequest;
    }

    public function setWriteIfChanged(bool $flag): void
    {
        $this->writeIfChanged = $flag;
    }

    public function getWriteIfChanged(): bool
    {
        return $this->writeIfChanged;
    }
}
