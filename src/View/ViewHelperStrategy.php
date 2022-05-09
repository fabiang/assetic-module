<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\View;

use Assetic\Asset\AssetCollection;
use Assetic\Contracts\Asset\AssetInterface;
use Laminas\View\Helper\HeadLink;
use Laminas\View\Helper\HeadScript;
use Laminas\View\Helper\InlineScript;
use Laminas\View\Renderer\PhpRenderer;

use function pathinfo;
use function strpos;
use function strtolower;

use const PATHINFO_EXTENSION;

class ViewHelperStrategy extends AbstractStrategy
{
    public function setupAsset(AssetInterface $asset): void
    {
        $baseUrl  = $this->str($this->getBaseUrl());
        $basePath = $this->str($this->getBasePath());

        if ($this->isDebug() && ! $this->isCombine() && $asset instanceof AssetCollection) {
            // Move assets as single instance not as a collection
            /** @var AssetCollection $value */
            foreach ($asset as $value) {
                $path = $baseUrl . $basePath . $this->str($value->getTargetPath());
                $this->helper($path);
            }
        } else {
            $path = $baseUrl . $basePath . $this->str($asset->getTargetPath());
            $this->helper($path);
        }
    }

    private function str(?string $s): string
    {
        return $s ?? '';
    }

    protected function helper(string $path): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'js':
                $this->appendScript($path);
                break;

            case 'css':
                $this->appendStylesheet($path);
                break;
        }
    }

    protected function appendScript(string $path): void
    {
        $renderer = $this->getRenderer();
        switch (true) {
            case $renderer instanceof PhpRenderer:
                if (strpos($path, "head_") !== false) {
                    /** @var HeadScript $headScript */
                    $headScript = $renderer->plugin('HeadScript');
                    $headScript->appendFile($path);
                } else {
                    /** @var InlineScript $inlineScript */
                    $inlineScript = $renderer->plugin('InlineScript');
                    $inlineScript->appendFile($path);
                }
                break;
        }
    }

    protected function appendStylesheet(string $path): void
    {
        $renderer = $this->getRenderer();
        switch (true) {
            case $renderer instanceof PhpRenderer:
                /** @var HeadLink $headLink */
                $headLink = $renderer->plugin('HeadLink');
                $headLink->appendStylesheet($path);
                break;
        }
    }
}
