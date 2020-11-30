<?php

declare(strict_types=1);

namespace Fabiang\AsseticBundle\View;

use Assetic\Asset\AssetCollection;
use Laminas\View\Renderer\PhpRenderer;
use Assetic\Contracts\Asset\AssetInterface;

class ViewHelperStrategy extends AbstractStrategy
{

    public function setupAsset(AssetInterface $asset): void
    {
        if ($this->isDebug() && !$this->isCombine() && $asset instanceof AssetCollection) {
            // Move assets as single instance not as a collection
            foreach ($asset as $value) {
                /** @var AssetCollection $value */
                $path = $this->getBaseUrl() . $this->getBasePath() . $value->getTargetPath();
                $this->helper($path);
            }
        } else {
            $path = $this->getBaseUrl() . $this->getBasePath() . $asset->getTargetPath();
            $this->helper($path);
        }
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
            case ($renderer instanceof PhpRenderer):
                if (strpos($path, "head_") !== false) {
                    $renderer->plugin('HeadScript')->appendFile($path);
                } else {
                    $renderer->plugin('InlineScript')->appendFile($path);
                }
                break;
        }
    }

    protected function appendStylesheet(string $path): void
    {
        $renderer = $this->getRenderer();
        switch (true) {
            case ($renderer instanceof PhpRenderer):
                $renderer->plugin('HeadLink')->appendStylesheet($path);
                break;
        }
    }

}
