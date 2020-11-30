Migrating to 3.0

Zend Framework support was dropped. You must migrate your application to Laminas first.

1. The namespace of the module has changed. Replace this in your `application.config.php`

    ```diff
    -        'AsseticBundle',
    +        'Fabiang\AsseticBundle',
    ```

2. If you were using the old module with Laminas, make sure you remove the rendering strategy mapping from your assetic configuration:

    ```diff
    -        'rendererToStrategy' => [
    -            'Laminas\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
    -            'Laminas\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
    -            'Laminas\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
    -        ],
   ```

Migrating to 2.5.0

This is just a drop-in-replacement for the old module,
so it should work out of the box with ZF2/3 and Laminas.
If you're using Laminas you must make sure that
`laminas/laminas-zendframework-bridge` is installed, so the old ZF classes are
correctly aliased.
