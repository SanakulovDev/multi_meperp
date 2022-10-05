<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use Yii;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Nenad Zivkovic <nenad@freetuts.org>
 * 
 * @since 2.0
 */
class ReqAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@themes';

    public $css = [
        
        'bower_components/bootstrap/dist/css/bootstrap.min.css',
        'bower_components/font-awesome/css/font-awesome.min.css',
        'bower_components/Ionicons/css/ionicons.min.css',
		'bower_components/select2/dist/css/select2.min.css',
        'dist/css/AdminLTE.min.css',
        'css/req.css',
        'css/style.css'

    ];

    public $js = [
        'js/tableFixer.js',
        'bower_components/bootstrap/dist/js/bootstrap.min.js',
		'bower_components/select2/dist/js/select2.full.min.js',
        'js/custom.js',
];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
