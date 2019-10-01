<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace achertovsky\debug;

/**
 * Description of AssetManager
 *
 * @author alexander
 */
class OverrideAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@ach-debug/assets';
    public $js = [
        'index.js',
        'override.js'
    ];
    public $css = [
        'override.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
