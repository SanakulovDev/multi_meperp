<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */
namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Nenad Zivkovic <nenad@freetuts.org>
 *
 * @since  2.0
 */
class AdminLteAsset extends AssetBundle{
	public $basePath = '@webroot';
	public $baseUrl  = '@themes';

	public $css = [
		'bower_components/bootstrap/dist/css/bootstrap.min.css',
		'bower_components/font-awesome/css/font-awesome.min.css',
		'bower_components/Ionicons/css/ionicons.min.css',
		'bower_components/select2/dist/css/select2.min.css',
		'dist/css/AdminLTE.min.css',
		'dist/css/skins/_all-skins.min.css',
		'css/style.css',
		'css/float-label.css',
		'plugins/iCheck/square/blue.css',
		'css/introjs.css',
		'plugins/treeviewjs/jquery.treeView.css'
	];

	public $js = [
		'js/tableFixer.js',
		'js/custom.js',
		'js/modal.js',
		'js/intro.js',
		//'bower_components/bootstrap/dist/js/bootstrap.min.js',
		'bower_components/select2/dist/js/select2.full.min.js',
		'bower_components/jquery-slimscroll/jquery.slimscroll.min.js',
		'bower_components/fastclick/lib/fastclick.js',
		'bower_components/chart.js/Chart.js',
		'dist/js/adminlte.min.js',
		'dist/js/demo.js',
		'highcharts/js/highcharts.js',
		'highcharts/modules/exporting.js',
		'highcharts/modules/offline-exporting.js',
		'highcharts/modules/data.js',
		'highcharts/modules/drilldown.js',
		'plugins/iCheck/icheck.min.js',
		'plugins/treeviewjs/jquery.treeView.js',
		'js/session.js',
		'js/dynamicTable.js'
	];

	public $depends = [
		'yii\web\YiiAsset',
	];
}
