<?php

namespace inspinia\sweetalert2\assets;

use yii\web\AssetBundle;

/**
 * Class ExampleAsset
 * @package inspinia\sweetalert2\assets
 */
class ExampleAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath;

    /**
     * @var array
     */
    public $css = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/src';
        $this->css = [
            'css/example.css',
            'css/buttons.css'
        ];
    }
}
