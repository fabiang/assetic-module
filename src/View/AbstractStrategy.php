<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\View;

use Laminas\View\Renderer\RendererInterface as Renderer;

abstract class AbstractStrategy implements StrategyInterface
{
    protected ?Renderer $renderer = null;
    protected ?string $baseUrl    = null;
    protected ?string $basePath   = null;
    protected bool $debug         = false;
    protected bool $combine       = true;

    public function setRenderer(?Renderer $renderer): void
    {
        $this->renderer = $renderer;
    }

    public function getRenderer(): ?Renderer
    {
        return $this->renderer;
    }

    public function setBaseUrl(?string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function setBasePath(?string $basePath): void
    {
        $this->basePath = $basePath;
    }

    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    public function setDebug(bool $flag): void
    {
        $this->debug = $flag;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setCombine(bool $flag): void
    {
        $this->combine = $flag;
    }

    public function isCombine(): bool
    {
        return $this->combine;
    }
}
