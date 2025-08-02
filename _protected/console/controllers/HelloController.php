<?php

/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace app\console\controllers;

use yii\console\Controller;
use yii\helpers\StringHelper;
use yii\helpers\Inflector;




/**
 * This command echoes the first argument that you have entered.
 * This command is provided as an example for you to learn how to create console commands.
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since  2.0
 */
class HelloController extends Controller
{
	/**
	 * This command echoes what you have entered as the message.
	 * @param string $message the message to be echoed.
	 */
	public function actionIndex($message = 'hello world')
	{
		echo $message . ": " . date("Y-m-d H:i:s");
	}

	public function actionTest()
	{

		$controllers = [];
		$controllerActions = [];
		$fp = fopen('config/actions.txt', 'w+');
		foreach (glob(__DIR__ . '\..\..\controllers\*Controller.php') as $controller) {
			$controllerName = basename($controller, '.php');
			if ($controllerName == 'MaintenanceController') continue;
			$className = 'app\controllers\\' . $controllerName;

			echo "\n";
			print_r($controllerName);
			echo "\n";
			//die;

			$methods = (new \ReflectionClass($className))->getMethods(\ReflectionMethod::IS_PUBLIC);

			foreach ($methods as $method) {
				//$controller_id = rtrim(trim($controllerName),'Controller');
				$controller_id = Inflector::camel2id(preg_replace('/Controller$/', '', $controllerName));
				if (StringHelper::startsWith($method->name, 'action')) {
					if ($method->name == 'actions') continue;
					$action_id = Inflector::camel2id(ltrim($method->name, 'action'));
					$controllerActions[] = $controller_id . ' | ' . $action_id;
					fwrite($fp, $controller_id . ' | ' . $action_id . "\n");
				}
			}
		}
		echo "\n";
		print_r($controllerActions);
		echo "\n";

		fclose($fp);
	}

	public function actionRole()
	{
		$rm = [

			['role' => 'bom-log', 'action' => 'index', 'permission' => 'bom-log-index'],
			['role' => 'consolidation-type', 'action' => 'index', 'permission' => 'consolidation-type-index'],
			['role' => 'consolidation-type', 'action' => 'create', 'permission' => 'consolidation-type-create'],
			['role' => 'consolidation-type', 'action' => 'update', 'permission' => 'consolidation-type-update'],
			['role' => 'consolidation-type', 'action' => 'delete', 'permission' => 'consolidation-type-delete'],
			['role' => 'contact', 'action' => 'index', 'permission' => 'contact-index'],
			['role' => 'contact', 'action' => 'create', 'permission' => 'contact-create'],
			['role' => 'contact', 'action' => 'update', 'permission' => 'contact-update'],
			['role' => 'contact', 'action' => 'delete', 'permission' => 'contact-delete'],
			['role' => 'contact', 'action' => 'xls', 'permission' => 'contact-xls'],
			['role' => 'container-invoice', 'action' => 'index', 'permission' => 'container-invoice-index'],
			['role' => 'container-invoice', 'action' => 'view', 'permission' => 'container-invoice-view'],
			['role' => 'container-invoice', 'action' => 'create', 'permission' => 'container-invoice-create'],
			['role' => 'container-invoice', 'action' => 'update', 'permission' => 'container-invoice-update'],
			['role' => 'container-invoice', 'action' => 'update-regime', 'permission' => 'container-invoice-update-regime'],
			['role' => 'container-invoice', 'action' => 'update-awb', 'permission' => 'container-invoice-update-awb'],
			['role' => 'container-invoice', 'action' => 'add-detail', 'permission' => 'container-invoice-add-detail'],
			['role' => 'container-invoice', 'action' => 'delete', 'permission' => 'container-invoice-delete'],
			['role' => 'container-invoice', 'action' => 'create-document', 'permission' => 'container-invoice-create-document'],
			['role' => 'container-invoice', 'action' => 'remove-document', 'permission' => 'container-invoice-remove-document'],
			['role' => 'container-invoice', 'action' => 'import-detail', 'permission' => 'container-invoice-import-detail'],
			['role' => 'container-invoice', 'action' => 'to-xlsx', 'permission' => 'container-invoice-to-xlsx'],
			['role' => 'container-invoice', 'action' => 'cont-inv-xlsx', 'permission' => 'container-invoice-cont_inv-xlsx'],
			['role' => 'contract', 'action' => 'index', 'permission' => 'contract-index'],
			['role' => 'contract', 'action' => 'create', 'permission' => 'contract-create'],
			['role' => 'contract', 'action' => 'update', 'permission' => 'contract-update'],
			['role' => 'contract', 'action' => 'delete', 'permission' => 'contract-delete'],
			['role' => 'contract', 'action' => 'view', 'permission' => 'contract-view'],
			['role' => 'contract', 'action' => 'xls', 'permission' => 'contract-xls'],
			['role' => 'contract-detail', 'action' => 'index', 'permission' => 'contract-detail-index'],
			['role' => 'contract-detail', 'action' => 'create', 'permission' => 'contract-detail-create'],
			['role' => 'contract-detail', 'action' => 'upload', 'permission' => 'contract-detail-upload'],
			['role' => 'contract-detail', 'action' => 'update', 'permission' => 'contract-detail-update'],
			['role' => 'contract-detail', 'action' => 'delete', 'permission' => 'contract-detail-delete'],
			['role' => 'contract-detail', 'action' => 'download-template', 'permission' => 'contract-detail-download-template'],
			['role' => 'contract-source', 'action' => 'index', 'permission' => 'contract-source-index'],
			['role' => 'contract-source', 'action' => 'create', 'permission' => 'contract-source-create'],
			['role' => 'contract-source', 'action' => 'update', 'permission' => 'contract-source-update'],
			['role' => 'contract-source', 'action' => 'delete', 'permission' => 'contract-source-delete'],
			['role' => 'contract-subject', 'action' => 'index', 'permission' => 'contract-subject-index'],
			['role' => 'contract-subject', 'action' => 'create', 'permission' => 'contract-subject-create'],
			['role' => 'contract-subject', 'action' => 'update', 'permission' => 'contract-subject-update'],
			['role' => 'contract-subject', 'action' => 'delete', 'permission' => 'contract-subject-delete'],
			['role' => 'crushing', 'action' => 'index', 'permission' => 'crushing-index'],
			['role' => 'crushing', 'action' => 'create', 'permission' => 'crushing-create'],
			['role' => 'crushing', 'action' => 'update', 'permission' => 'crushing-update'],
			['role' => 'crushing', 'action' => 'delete', 'permission' => 'crushing-delete'],
			['role' => 'crushing', 'action' => 'xls', 'permission' => 'crushing-xls'],
			['role' => 'currency-rate', 'action' => 'index', 'permission' => 'currency-rate-index'],
			['role' => 'currency-rate', 'action' => 'create', 'permission' => 'currency-rate-create'],
			['role' => 'currency-rate', 'action' => 'update', 'permission' => 'currency-rate-update'],
			['role' => 'currency-rate', 'action' => 'delete', 'permission' => 'currency-rate-delete'],
			['role' => 'customer', 'action' => 'index', 'permission' => 'customer-index'],
			['role' => 'customer', 'action' => 'view', 'permission' => 'customer-view'],
			['role' => 'customer', 'action' => 'create', 'permission' => 'customer-create'],
			['role' => 'customer', 'action' => 'update', 'permission' => 'customer-update'],
			['role' => 'customer', 'action' => 'delete', 'permission' => 'customer-delete'],
			['role' => 'customer', 'action' => 'xls', 'permission' => 'customer-xls'],
			['role' => 'customer-type', 'action' => 'index', 'permission' => 'customer-type-index'],
			['role' => 'customer-type', 'action' => 'create', 'permission' => 'customer-type-create'],
			['role' => 'customer-type', 'action' => 'update', 'permission' => 'customer-type-update'],
			['role' => 'customer-type', 'action' => 'delete', 'permission' => 'customer-type-delete'],
			['role' => 'customer-type', 'action' => 'xls', 'permission' => 'customer-type-xls'],
			['role' => 'defect', 'action' => 'index', 'permission' => 'defect-index'],
			['role' => 'defect', 'action' => 'create', 'permission' => 'defect-create'],
			['role' => 'defect', 'action' => 'update', 'permission' => 'defect-update'],
			['role' => 'defect', 'action' => 'delete', 'permission' => 'defect-delete'],
			['role' => 'defect', 'action' => 'xls', 'permission' => 'defect-xls'],
			['role' => 'delivery-plan', 'action' => 'index', 'permission' => 'delivery-plan-index'],
			['role' => 'delivery-term', 'action' => 'index', 'permission' => 'delivery-term-index'],
			['role' => 'delivery-term', 'action' => 'create', 'permission' => 'delivery-term-create'],
			['role' => 'delivery-term', 'action' => 'update', 'permission' => 'delivery-term-update'],
			['role' => 'delivery-term', 'action' => 'delete', 'permission' => 'delivery-term-delete'],
			['role' => 'document', 'action' => 'index', 'permission' => 'document-index'],
			['role' => 'document', 'action' => 'index-scan', 'permission' => 'document-index-scan'],
			['role' => 'document', 'action' => 'view', 'permission' => 'document-view'],
			['role' => 'document', 'action' => 'create-act', 'permission' => 'document-create-act'],
			['role' => 'document', 'action' => 'create', 'permission' => 'document-create'],
			['role' => 'document', 'action' => 'create-local', 'permission' => 'document-create-local'],
			['role' => 'document', 'action' => 'create-local-issue', 'permission' => 'document-create-local-issue'],
			['role' => 'document', 'action' => 'update', 'permission' => 'document-update'],
			['role' => 'document', 'action' => 'update-local', 'permission' => 'document-update-local'],
			['role' => 'document', 'action' => 'update-local-issue', 'permission' => 'document-update-local-issue'],
			['role' => 'document', 'action' => 'update-act', 'permission' => 'document-update-act'],
			['role' => 'document', 'action' => 'delete', 'permission' => 'document-delete'],
			['role' => 'document', 'action' => 'delete-local', 'permission' => 'document-delete-local'],
			['role' => 'document', 'action' => 'delete-local-issue', 'permission' => 'document-delete-local-issue'],
			['role' => 'document', 'action' => 'delete-act', 'permission' => 'document-delete-act'],
			['role' => 'document', 'action' => 'confirm', 'permission' => 'document-confirm'],
			['role' => 'document', 'action' => 'print', 'permission' => 'document-print'],
			['role' => 'document', 'action' => 'xls', 'permission' => 'document-xls'],
			['role' => 'document', 'action' => 'create-local-kd', 'permission' => 'document-create-local-kd'],
			['role' => 'document', 'action' => 'update-local-kd', 'permission' => 'document-update-local-kd'],
			['role' => 'document', 'action' => 'delete-local-kd', 'permission' => 'document-delete-local-kd'],
			['role' => 'document', 'action' => 'create-shop-consumption', 'permission' => 'document-create-shop-consumption'],
			['role' => 'document', 'action' => 'create-shop-consumption-ver2', 'permission' => 'document-create-shop-consumption-ver2'],
			['role' => 'document', 'action' => 'delete-shop-consumption', 'permission' => 'document-delete-shop-consumption'],
			['role' => 'document', 'action' => 'shop-confirm', 'permission' => 'document-shop-confirm'],
			['role' => 'document', 'action' => 'shop-confirm-ver2', 'permission' => 'document-shop-confirm-ver2'],
			['role' => 'document', 'action' => 'shop-disconfirm', 'permission' => 'document-shop-disconfirm'],
			['role' => 'document', 'action' => 'issue', 'permission' => 'document-issue'],
			['role' => 'document-type', 'action' => 'index', 'permission' => 'document-type-index'],
			['role' => 'driver', 'action' => 'index', 'permission' => 'driver-index'],
			['role' => 'driver', 'action' => 'create', 'permission' => 'driver-create'],
			['role' => 'driver', 'action' => 'update', 'permission' => 'driver-update'],
			['role' => 'driver', 'action' => 'delete', 'permission' => 'driver-delete'],
			['role' => 'factory', 'action' => 'index', 'permission' => 'factory-index'],
			['role' => 'factory', 'action' => 'view', 'permission' => 'factory-view'],
			['role' => 'factory', 'action' => 'create', 'permission' => 'factory-create'],
			['role' => 'factory', 'action' => 'update', 'permission' => 'factory-update'],
			['role' => 'factory', 'action' => 'delete', 'permission' => 'factory-delete'],
			['role' => 'factory', 'action' => 'xls', 'permission' => 'factory-xls'],
			['role' => 'fg-invoice', 'action' => 'index', 'permission' => 'fg-invoice-index'],
			['role' => 'fg-invoice', 'action' => 'xls', 'permission' => 'fg-invoice-xls'],
			['role' => 'fg-invoice', 'action' => 'confirm', 'permission' => 'fg-invoice-confirm'],
			['role' => 'fg-invoice', 'action' => 'reject', 'permission' => 'fg-invoice-reject'],
			['role' => 'fg-invoice', 'action' => 'view', 'permission' => 'fg-invoice-view'],
			['role' => 'fg-invoice', 'action' => 'print', 'permission' => 'fg-invoice-print'],
			['role' => 'fg-invoice', 'action' => 'create', 'permission' => 'fg-invoice-create'],
			['role' => 'fg-invoice', 'action' => 'upload-fginvoice', 'permission' => 'fg-invoice-upload-fginvoice'],
			['role' => 'fg-invoice', 'action' => 'update', 'permission' => 'fg-invoice-update'],
			['role' => 'fg-invoice', 'action' => 'delete', 'permission' => 'fg-invoice-delete'],
			['role' => 'fg-invoice-detail', 'action' => 'index', 'permission' => 'fg-invoice-detail-index'],
			['role' => 'fg-invoice-detail', 'action' => 'view', 'permission' => 'fg-invoice-detail-view'],
			['role' => 'fg-invoice-detail', 'action' => 'create', 'permission' => 'fg-invoice-detail-create'],
			['role' => 'fg-invoice-detail', 'action' => 'update', 'permission' => 'fg-invoice-detail-update'],
			['role' => 'fg-invoice-detail', 'action' => 'delete', 'permission' => 'fg-invoice-detail-delete'],
			['role' => 'invoice-detail', 'action' => 'index', 'permission' => 'invoice-detail-index'],
			['role' => 'invoice-detail', 'action' => 'view', 'permission' => 'invoice-detail-view'],
			['role' => 'invoice-detail', 'action' => 'create', 'permission' => 'invoice-detail-create'],
			['role' => 'invoice-detail', 'action' => 'update', 'permission' => 'invoice-detail-update'],
			['role' => 'invoice-detail', 'action' => 'delete', 'permission' => 'invoice-detail-delete'],
			['role' => 'formulation-base', 'action' => 'index', 'permission' => 'formulation-base-index'],
			['role' => 'formulation-base', 'action' => 'view', 'permission' => 'formulation-base-view'],
			['role' => 'formulation-base', 'action' => 'create', 'permission' => 'formulation-base-create'],
			['role' => 'formulation-base', 'action' => 'update', 'permission' => 'formulation-base-update'],
			['role' => 'formulation-base', 'action' => 'delete', 'permission' => 'formulation-base-delete'],
			['role' => 'payment-control', 'action' => 'index', 'permission' => 'payment-control-index'],
			['role' => 'payment-control', 'action' => 'view', 'permission' => 'payment-control-view'],
			['role' => 'payment-control', 'action' => 'create', 'permission' => 'payment-control-create'],
			['role' => 'payment-control', 'action' => 'update', 'permission' => 'payment-control-update'],
			['role' => 'payment-control', 'action' => 'delete', 'permission' => 'payment-control-delete'],
			['role' => 'payment-term', 'action' => 'index', 'permission' => 'payment-term-index'],
			['role' => 'payment-term', 'action' => 'create', 'permission' => 'payment-term-create'],
			['role' => 'payment-term', 'action' => 'update', 'permission' => 'payment-term-update'],
			['role' => 'payment-term', 'action' => 'delete', 'permission' => 'payment-term-delete'],
			['role' => 'payment-type', 'action' => 'index', 'permission' => 'payment-type-index'],
			['role' => 'payment-type', 'action' => 'view', 'permission' => 'payment-type-view'],
			['role' => 'payment-type', 'action' => 'create', 'permission' => 'payment-type-create'],
			['role' => 'payment-type', 'action' => 'update', 'permission' => 'payment-type-update'],
			['role' => 'payment-type', 'action' => 'delete', 'permission' => 'payment-type-delete'],
			['role' => 'product-group', 'action' => 'index', 'permission' => 'product-group-index'],
			['role' => 'product-group', 'action' => 'view', 'permission' => 'product-group-view'],
			['role' => 'product-group', 'action' => 'create', 'permission' => 'product-group-create'],
			['role' => 'product-group', 'action' => 'update', 'permission' => 'product-group-update'],
			['role' => 'product-group', 'action' => 'delete', 'permission' => 'product-group-delete'],
			['role' => 'product-line', 'action' => 'index', 'permission' => 'product-line-index'],
			['role' => 'product-line', 'action' => 'view', 'permission' => 'product-line-view'],
			['role' => 'product-line', 'action' => 'create', 'permission' => 'product-line-create'],
			['role' => 'product-line', 'action' => 'update', 'permission' => 'product-line-update'],
			['role' => 'product-line', 'action' => 'delete', 'permission' => 'product-line-delete'],
			['role' => 'product-model', 'action' => 'index', 'permission' => 'product-model-index'],
			['role' => 'product-model', 'action' => 'view', 'permission' => 'product-model-view'],
			['role' => 'product-model', 'action' => 'create', 'permission' => 'product-model-create'],
			['role' => 'product-model', 'action' => 'update', 'permission' => 'product-model-update'],
			['role' => 'product-model', 'action' => 'delete', 'permission' => 'product-model-delete'],
			['role' => 'production-order', 'action' => 'index', 'permission' => 'production-order-index'],
			['role' => 'production-order', 'action' => 'view', 'permission' => 'production-order-view'],
			['role' => 'production-order', 'action' => 'create-production-orders', 'permission' => 'production-order-create-production-orders'],
			['role' => 'production-order', 'action' => 'create', 'permission' => 'production-order-create'],
			['role' => 'production-order', 'action' => 'upload', 'permission' => 'production-order-upload'],
			['role' => 'production-order', 'action' => 'create-isbulk', 'permission' => 'production-order-create-isbulk'],
			['role' => 'production-order', 'action' => 'selected-print', 'permission' => 'production-order-selected-print'],
			['role' => 'production-order', 'action' => 'xls', 'permission' => 'production-order-xls'],
			['role' => 'production-order', 'action' => 'produce', 'permission' => 'production-order-produce'],
			['role' => 'production-order', 'action' => 'delete', 'permission' => 'production-order-delete'],
			['role' => 'production-order-defect', 'action' => 'index', 'permission' => 'production-order-defect-index'],
			['role' => 'production-order-defect', 'action' => 'view', 'permission' => 'production-order-defect-view'],
			['role' => 'production-order-defect', 'action' => 'create', 'permission' => 'production-order-defect-create'],
			['role' => 'production-order-defect', 'action' => 'update', 'permission' => 'production-order-defect-update'],
			['role' => 'production-order-defect', 'action' => 'delete', 'permission' => 'production-order-defect-delete'],
			['role' => 'production-order-defect', 'action' => 'xls', 'permission' => 'production-order-defect-xls'],
			['role' => 'production-plan', 'action' => 'index', 'permission' => 'production-plan-index'],
			['role' => 'production-plan', 'action' => 'index-report', 'permission' => 'production-plan-index-report'],
			['role' => 'production-plan', 'action' => 'create', 'permission' => 'production-plan-create'],
			['role' => 'production-plan', 'action' => 'update', 'permission' => 'production-plan-update'],
			['role' => 'production-plan', 'action' => 'comment', 'permission' => 'production-plan-comment'],
			['role' => 'production-plan', 'action' => 'delete', 'permission' => 'production-plan-delete'],
			['role' => 'production-plan', 'action' => 'upload', 'permission' => 'production-plan-upload'],
			['role' => 'production-plan', 'action' => 'upload-today', 'permission' => 'production-plan-upload-today'],
			['role' => 'production-plan', 'action' => 'download-template', 'permission' => 'production-plan-download-template'],
			['role' => 'receiving-person', 'action' => 'index', 'permission' => 'receiving-person-index'],
			['role' => 'receiving-person', 'action' => 'view', 'permission' => 'receiving-person-view'],
			['role' => 'receiving-person', 'action' => 'create', 'permission' => 'receiving-person-create'],
			['role' => 'receiving-person', 'action' => 'update', 'permission' => 'receiving-person-update'],
			['role' => 'receiving-person', 'action' => 'delete', 'permission' => 'receiving-person-delete'],
			['role' => 'receiving-person', 'action' => 'xls', 'permission' => 'receiving-person-xls'],
			['role' => 'sales-contract', 'action' => 'index', 'permission' => 'sales-contract-index'],
			['role' => 'sales-contract', 'action' => 'create', 'permission' => 'sales-contract-create'],
			['role' => 'sales-contract', 'action' => 'update', 'permission' => 'sales-contract-update'],
			['role' => 'sales-contract', 'action' => 'delete', 'permission' => 'sales-contract-delete'],
			['role' => 'sales-contract', 'action' => 'view', 'permission' => 'sales-contract-view'],
			['role' => 'sales-contract', 'action' => 'xls', 'permission' => 'sales-contract-xls'],
			['role' => 'sales-contract', 'action' => 'list-by-sales-supplier', 'permission' => 'sales-contract-list-by-sales-supplier'],
			['role' => 'sales-contract-detail', 'action' => 'index', 'permission' => 'sales-contract-detail-index'],
			['role' => 'sales-contract-detail', 'action' => 'create', 'permission' => 'sales-contract-detail-create'],
			['role' => 'sales-contract-detail', 'action' => 'upload', 'permission' => 'sales-contract-detail-upload'],
			['role' => 'sales-contract-detail', 'action' => 'update', 'permission' => 'sales-contract-detail-update'],
			['role' => 'sales-contract-detail', 'action' => 'delete', 'permission' => 'sales-contract-detail-delete'],
			['role' => 'ship-mode', 'action' => 'index', 'permission' => 'ship-mode-index'],
			['role' => 'ship-mode', 'action' => 'create', 'permission' => 'ship-mode-create'],
			['role' => 'ship-mode', 'action' => 'update', 'permission' => 'ship-mode-update'],
			['role' => 'ship-mode', 'action' => 'delete', 'permission' => 'ship-mode-delete'],
			['role' => 'stock', 'action' => 'index', 'permission' => 'stock-index'],
			['role' => 'stock', 'action' => 'xls', 'permission' => 'stock-xls'],
			['role' => 'stock', 'action' => 'upload', 'permission' => 'stock-upload'],
			['role' => 'supplier', 'action' => 'index', 'permission' => 'supplier-index'],
			['role' => 'supplier', 'action' => 'view', 'permission' => 'supplier-view'],
			['role' => 'supplier', 'action' => 'create', 'permission' => 'supplier-create'],
			['role' => 'supplier', 'action' => 'update', 'permission' => 'supplier-update'],
			['role' => 'supplier', 'action' => 'delete', 'permission' => 'supplier-delete'],
			['role' => 'supplier', 'action' => 'xls', 'permission' => 'supplier-xls'],
			['role' => 'supplier', 'action' => 'list-by-part-contract', 'permission' => 'supplier-list-by-part-contract'],
			['role' => 'truck', 'action' => 'index', 'permission' => 'truck-index'],
			['role' => 'truck', 'action' => 'create', 'permission' => 'truck-create'],
			['role' => 'truck', 'action' => 'update', 'permission' => 'truck-update'],
			['role' => 'truck', 'action' => 'delete', 'permission' => 'truck-delete'],
			['role' => 'uloc', 'action' => 'index', 'permission' => 'uloc-index'],
			['role' => 'uloc', 'action' => 'view', 'permission' => 'uloc-view'],
			['role' => 'uloc', 'action' => 'create', 'permission' => 'uloc-create'],
			['role' => 'uloc', 'action' => 'update', 'permission' => 'uloc-update'],
			['role' => 'uloc', 'action' => 'delete', 'permission' => 'uloc-delete'],
			['role' => 'uloc', 'action' => 'xls', 'permission' => 'uloc-xls'],
			['role' => 'unit', 'action' => 'index', 'permission' => 'unit-index'],
			['role' => 'unit', 'action' => 'create', 'permission' => 'unit-create'],
			['role' => 'unit', 'action' => 'update', 'permission' => 'unit-update'],
			['role' => 'unit', 'action' => 'delete', 'permission' => 'unit-delete'],
			['role' => 'user', 'action' => 'index', 'permission' => 'user-index'],
			['role' => 'user', 'action' => 'view', 'permission' => 'user-view'],
			['role' => 'user', 'action' => 'create', 'permission' => 'user-create'],
			['role' => 'user', 'action' => 'update', 'permission' => 'user-update'],
			['role' => 'user', 'action' => 'get-info', 'permission' => 'user-get-info'],
			['role' => 'user', 'action' => 'delete', 'permission' => 'user-delete'],
			['role' => 'user', 'action' => 'xls', 'permission' => 'user-xls'],
			['role' => 'warehouse', 'action' => 'index', 'permission' => 'warehouse-index'],
			['role' => 'warehouse', 'action' => 'create', 'permission' => 'warehouse-create'],
			['role' => 'warehouse', 'action' => 'update', 'permission' => 'warehouse-update'],
			['role' => 'warehouse', 'action' => 'delete', 'permission' => 'warehouse-delete'],
			['role' => 'warehouse', 'action' => 'xls', 'permission' => 'warehouse-xls'],
			['role' => 'warehouse-report-group', 'action' => 'index', 'permission' => 'warehouse-report-group-index'],
			['role' => 'warehouse-report-group', 'action' => 'create', 'permission' => 'warehouse-report-group-create'],
			['role' => 'warehouse-report-group', 'action' => 'update', 'permission' => 'warehouse-report-group-update'],
			['role' => 'warehouse-report-group', 'action' => 'delete', 'permission' => 'warehouse-report-group-delete'],
			['role' => 'waybill', 'action' => 'index', 'permission' => 'waybill-index'],
			['role' => 'waybill', 'action' => 'view', 'permission' => 'waybill-view'],
			['role' => 'waybill', 'action' => 'create', 'permission' => 'waybill-create'],
			['role' => 'waybill', 'action' => 'update', 'permission' => 'waybill-update'],
			['role' => 'waybill', 'action' => 'delete', 'permission' => 'waybill-delete'],
			['role' => 'waybill', 'action' => 'factory-info', 'permission' => 'waybill-factory-info'],
			['role' => 'gtd', 'action' => 'index', 'permission' => 'gtd-index'],
			['role' => 'gtd', 'action' => 'xls', 'permission' => 'gtd-xls'],
			['role' => 'gtd', 'action' => 'view', 'permission' => 'gtd-view'],
			['role' => 'gtd', 'action' => 'create', 'permission' => 'gtd-create'],
			['role' => 'gtd', 'action' => 'delete', 'permission' => 'gtd-delete'],
			['role' => 'gtd', 'action' => 'invoice-list', 'permission' => 'gtd-invoice-list'],
			['role' => 'gtd', 'action' => 'invoice-data', 'permission' => 'gtd-invoice-data'],
			['role' => 'gtd-invoice', 'action' => 'update', 'permission' => 'gtd-invoice-update'],
			['role' => 'gtd-invoice', 'action' => 'delete', 'permission' => 'gtd-invoice-delete'],
			['role' => 'gtd-invoice', 'action' => 'xls', 'permission' => 'gtd-invoice-xls'],
			['role' => 'history-document', 'action' => 'index', 'permission' => 'history-document-index'],
			['role' => 'history-document', 'action' => 'view', 'permission' => 'history-document-view'],
			['role' => 'inventory', 'action' => 'index', 'permission' => 'inventory-index'],
			['role' => 'inventory', 'action' => 'create', 'permission' => 'inventory-create'],
			['role' => 'inventory', 'action' => 'update', 'permission' => 'inventory-update'],
			['role' => 'inventory', 'action' => 'delete', 'permission' => 'inventory-delete'],
			['role' => 'inventory', 'action' => 'xls', 'permission' => 'inventory-xls'],
			['role' => 'inventory-detail', 'action' => 'create', 'permission' => 'inventory-detail-create'],
			['role' => 'inventory-detail', 'action' => 'update', 'permission' => 'inventory-detail-update'],
			['role' => 'inventory-detail', 'action' => 'delete', 'permission' => 'inventory-detail-delete'],
			['role' => 'inventory-detail', 'action' => 'upload', 'permission' => 'inventory-detail-upload'],
			['role' => 'lc', 'action' => 'index', 'permission' => 'lc-index'],
			['role' => 'lc', 'action' => 'view', 'permission' => 'lc-view'],
			['role' => 'lc', 'action' => 'create', 'permission' => 'lc-create'],
			['role' => 'lc', 'action' => 'update', 'permission' => 'lc-update'],
			['role' => 'lc', 'action' => 'delete', 'permission' => 'lc-delete'],
			['role' => 'lc', 'action' => 'xls', 'permission' => 'lc-xls'],
			['role' => 'line', 'action' => 'index', 'permission' => 'line-index'],
			['role' => 'line', 'action' => 'view', 'permission' => 'line-view'],
			['role' => 'line', 'action' => 'create', 'permission' => 'line-create'],
			['role' => 'line', 'action' => 'update', 'permission' => 'line-update'],
			['role' => 'line', 'action' => 'delete', 'permission' => 'line-delete'],
			['role' => 'line', 'action' => 'xls', 'permission' => 'line-xls'],
			['role' => 'lms', 'action' => 'index', 'permission' => 'lms-index'],
			['role' => 'lms', 'action' => 'view', 'permission' => 'lms-view'],
			['role' => 'lms', 'action' => 'create', 'permission' => 'lms-create'],
			['role' => 'lms', 'action' => 'update', 'permission' => 'lms-update'],
			['role' => 'lms', 'action' => 'delete', 'permission' => 'lms-delete'],
			['role' => 'lms', 'action' => 'xls', 'permission' => 'lms-xls'],
			['role' => 'machine', 'action' => 'index', 'permission' => 'machine-index'],
			['role' => 'machine', 'action' => 'view', 'permission' => 'machine-view'],
			['role' => 'machine', 'action' => 'create', 'permission' => 'machine-create'],
			['role' => 'machine', 'action' => 'update', 'permission' => 'machine-update'],
			['role' => 'machine', 'action' => 'delete', 'permission' => 'machine-delete'],
			['role' => 'machine', 'action' => 'counter', 'permission' => 'machine-counter'],
			['role' => 'machine', 'action' => 'machine-list', 'permission' => 'machine-machine-list'],
			['role' => 'machine', 'action' => 'machine-mold-list', 'permission' => 'machine-machine-mold-list'],
			['role' => 'machine', 'action' => 'machine-setting', 'permission' => 'machine-machine-setting'],
			['role' => 'mfu', 'action' => 'index', 'permission' => 'mfu-index'],
			['role' => 'mfu', 'action' => 'view', 'permission' => 'mfu-view'],
			['role' => 'mfu', 'action' => 'create', 'permission' => 'mfu-create'],
			['role' => 'mfu', 'action' => 'update', 'permission' => 'mfu-update'],
			['role' => 'mfu', 'action' => 'delete', 'permission' => 'mfu-delete'],
			['role' => 'mold', 'action' => 'index', 'permission' => 'mold-index'],
			['role' => 'mold', 'action' => 'view', 'permission' => 'mold-view'],
			['role' => 'mold', 'action' => 'create', 'permission' => 'mold-create'],
			['role' => 'mold', 'action' => 'update', 'permission' => 'mold-update'],
			['role' => 'mold', 'action' => 'delete', 'permission' => 'mold-delete'],
			['role' => 'pack', 'action' => 'index', 'permission' => 'pack-index'],
			['role' => 'pack', 'action' => 'create', 'permission' => 'pack-create'],
			['role' => 'pack', 'action' => 'update', 'permission' => 'pack-update'],
			['role' => 'pack', 'action' => 'delete', 'permission' => 'pack-delete'],
			['role' => 'pack', 'action' => 'xls', 'permission' => 'pack-xls'],
			['role' => 'pack-level', 'action' => 'index', 'permission' => 'pack-level-index'],
			['role' => 'pack-level', 'action' => 'create', 'permission' => 'pack-level-create'],
			['role' => 'pack-level', 'action' => 'update', 'permission' => 'pack-level-update'],
			['role' => 'pack-level', 'action' => 'delete', 'permission' => 'pack-level-delete'],
			['role' => 'part', 'action' => 'index', 'permission' => 'part-index'],
			['role' => 'part', 'action' => 'create', 'permission' => 'part-create'],
			['role' => 'part', 'action' => 'update', 'permission' => 'part-update'],
			['role' => 'part', 'action' => 'delete', 'permission' => 'part-delete'],
			['role' => 'part', 'action' => 'xls', 'permission' => 'part-xls'],
			['role' => 'part', 'action' => 'get-partname', 'permission' => 'part-get-partname'],
			['role' => 'part', 'action' => 'get-parts-by-floc', 'permission' => 'part-get-parts-by-floc'],
			['role' => 'part', 'action' => 'get-parts-by-supplier', 'permission' => 'part-get-parts-by-supplier'],
			['role' => 'part', 'action' => 'get-parts-by-model-and-side', 'permission' => 'part-get-parts-by-model-and-side'],
			['role' => 'part', 'action' => 'search', 'permission' => 'part-search'],
			['role' => 'part', 'action' => 'pop', 'permission' => 'part-pop'],
			['role' => 'part', 'action' => 'upload', 'permission' => 'part-upload'],
			['role' => 'part-order', 'action' => 'index', 'permission' => 'part-order-index'],
			['role' => 'part-order', 'action' => 'view', 'permission' => 'part-order-view'],
			['role' => 'part-order', 'action' => 'create', 'permission' => 'part-order-create'],
			['role' => 'part-order', 'action' => 'update', 'permission' => 'part-order-update'],
			['role' => 'part-order', 'action' => 'delete', 'permission' => 'part-order-delete'],
			['role' => 'part-order', 'action' => 'import-detail', 'permission' => 'part-order-import-detail'],
			['role' => 'part-order', 'action' => 'xls', 'permission' => 'part-order-xls'],
			['role' => 'part-order', 'action' => 'to-xlsx', 'permission' => 'part-order-to-xlsx'],
			['role' => 'part-order', 'action' => 'part-list', 'permission' => 'part-order-part-list'],
			['role' => 'part-order', 'action' => 'part-data', 'permission' => 'part-order-part-data'],
			['role' => 'part-packing', 'action' => 'index', 'permission' => 'part-packing-index'],
			['role' => 'part-packing', 'action' => 'view', 'permission' => 'part-packing-view'],
			['role' => 'part-packing', 'action' => 'create', 'permission' => 'part-packing-create'],
			['role' => 'part-packing', 'action' => 'update', 'permission' => 'part-packing-update'],
			['role' => 'part-packing', 'action' => 'delete', 'permission' => 'part-packing-delete'],
			['role' => 'part-packing', 'action' => 'xls', 'permission' => 'part-packing-xls'],
			['role' => 'part-part', 'action' => 'index', 'permission' => 'part-part-index'],
			['role' => 'part-part', 'action' => 'create', 'permission' => 'part-part-create'],
			['role' => 'part-part', 'action' => 'update', 'permission' => 'part-part-update'],
			['role' => 'part-part', 'action' => 'delete', 'permission' => 'part-part-delete'],
			['role' => 'part-part', 'action' => 'xls', 'permission' => 'part-part-xls'],
			['role' => 'part-part', 'action' => 'part-raw-excel', 'permission' => 'part-part-part-raw-excel'],
			['role' => 'part-part', 'action' => 'detailed-bom', 'permission' => 'part-part-detailed-bom'],
			['role' => 'part-part', 'action' => 'download-det-bom', 'permission' => 'part-part-download-det-bom'],
			['role' => 'part-part', 'action' => 'upload', 'permission' => 'part-part-upload'],
			['role' => 'part-type', 'action' => 'index', 'permission' => 'part-type-index'],
			['role' => 'part-type', 'action' => 'create', 'permission' => 'part-type-create'],
			['role' => 'part-type', 'action' => 'update', 'permission' => 'part-type-update'],
			['role' => 'part-type', 'action' => 'delete', 'permission' => 'part-type-delete'],
		
    ];

		$rules = [
			[
				'controllers' => ['stock', 'part-part', 'part'],
				'actions' => ['upload'],
				'allow' => true,
				'roles' => ['superadmin'],
			],
			[
				'controllers' => ['stock', 'part-part', 'part'],
				'actions' => ['upload'],
				'allow' => false,
				'roles' => ['admin'],
			],
			[
				'allow' => true,
				'roles' => ['admin'],
			],
			[
				'controllers' => ['part', 'part-type', 'product-model', 'product-group', 'product-line', 'product', 'product-parts', 'part-part', 'unit', 'bom-log'],
				'actions' => ['index', 'view', 'create', 'update', 'delete', 'xls', 'search'],
				'allow' => true,
				'roles' => ['pe'],
			],
			[
				'controllers' => ['delivery-term', 'payment-term', 'contract-subject', 'contract-source', 'currency-rate'],
				'allow' => true,
				'roles' => ['buyer'],
			],
			[
				'controllers' => ['invoice', 'container-invoice', 'invoice-detail'],
				'actions' => ['index', 'view', 'to-xlsx', 'cont_inv-xlsx'],
				'allow' => true,
				'roles' => ['buyer'],
			],
			[
				'controllers' => ['inventory', 'inventory-detail', 'line', 'uloc'],
				'allow' => true,
				'roles' => ['admin'],
			],
			[
				'controllers' => ['production-plan'],
				'allow' => true,
				'roles' => ['plan'],
			],
			[
				'controllers' => ['document'],
				'actions' => ['index', 'view', 'create-shop-consumption', 'create-shop-consumption-ver2', 'delete-shop-consumption', 'shop-confirm', 'shop-confirm-ver2', 'shop-disconfirm', 'xls'],
				'allow' => true,
				'roles' => ['counter'],
			],
			[
				'controllers' => ['production-order', 'machine', 'mold', 'mold-machine', 'mold-part'],
				'allow' => true,
				'roles' => ['counter', 'mrpc'],
			],
			[
				'controllers' => ['part'],
				'actions' => ['get-parts-by-model-and-side'],
				'allow' => true,
				'roles' => ['counter', 'mrpc'],
			],
			[
				'controllers' => ['production-order'],
				'actions' => ['index', 'view', 'xls'],
				'allow' => true,
				'roles' => ['plan'],
			],
			[
				'controllers' => ['production-order-defect'],
				'allow' => true,
				'roles' => ['quality'],
			],
			[
				'controllers' => ['crushing'],
				'allow' => true,
				'roles' => ['crusher'],
			],
			[
				'controllers' => ['customer-type', 'customer', 'fg-invoice', 'fg-invoice-detail', 'receiving-person', 'waybill', 'driver', 'truck', 'sales-contract', 'sales-contract-detail'],
				'allow' => true,
				'roles' => ['sales'],
			],
			[
				'controllers' => ['fg-invoice', 'fg-invoice-detail', 'receiving-person', 'waybill', 'driver', 'truck'],
				'allow' => true,
				'roles' => ['shipper'],
			],
			[
				'controllers' => ['production-plan'],
				'allow' => true,
				'roles' => ['plan'],
			],
			[
				'controllers' => ['part'],
				'actions' => ['get-partname', 'get-parts-by-floc', 'get-parts-by-supplier'],
				'allow' => true,
				'roles' => ['mrp', 'mfu', 'mrpc'],
			],
			[
				'controllers' => ['supplier', 'contract', 'contract-detail', 'contact', 'lc'],
				'actions' => ['index', 'view', 'create', 'update', 'delete', 'xls', 'upload'],
				'allow' => true,
				'roles' => ['buyer'],
			],
			[
				'controllers' => ['consolidation-type', 'mfu', 'pack', 'part-packing', 'part-level', 'lms', 'part-order', 'part-order-detail', 'invoice', 'container-invoice', 'invoice-part-problem', 'invoice-detail'],
				'allow' => true,
				'roles' => ['mfu'],
			],
			[
				'controllers' => ['pack', 'part-packing', 'part-level', 'lms'],
				'allow' => true,
				'roles' => ['mrp'],
			],
			[
				'controllers' => ['document'],
				'actions' => ['index', 'view', 'create-act', 'update-act', 'delete-act', 'xls'],
				'allow' => true,
				'roles' => ['mfu'],
			],
			[
				'controllers' => ['document'],
				'actions' => ['index', 'view', 'xls', 'stock', 'coverage', 'requirement', 'download-coverage', 'download-requirement', 'ftq-by-line'],
				'allow' => true,
				'roles' => ['report'],
			],
			[
				'controllers' => ['document'],
				'actions' => [
					'index', 'index-scan', 'view', 'create', 'update', 'delete', 'confirm', 'print', 'create-local', 'update-local', 'delete-local', 'create-local-issue', 'update-local-issue', 'delete-local-issue', 'create-local-kd', 'update-local-kd', 'delete-local-kd', 'create-act', 'update-act', 'delete-act', 'xls',
					'issue'
				],
				'allow' => true,
				'roles' => ['mrp', 'mrpc'],
			],
			[
				'controllers' => ['container-invoice'],
				'actions' => ['create-document', 'remove-document', 'cont_inv-xlsx'],
				'allow' => true,
				'roles' => ['mrp-logx'],
			],
			[
				'controllers' => ['invoice', 'container-invoice', 'invoice-detail'],
				'actions' => ['index', 'view', 'create', 'update', 'update-regime', 'import-detail', 'add-detail', 'to-xlsx'],
				'allow' => true,
				'roles' => ['mrp-logx'],
			],
			[
				'controllers' => ['invoice', 'container-invoice', 'invoice-detail'],
				'actions' => ['index', 'view', 'create', 'update', 'delete', 'import-detail', 'add-detail', 'to-xlsx'],
				'allow' => true,
				'roles' => ['logistics'],
			],
			[
				'controllers' => ['gtd', 'gtd-invoice'],
				'allow' => true,
				'roles' => ['declarant'],
			],
			[
				'controllers' => ['stock'],
				'actions' => ['index', 'xls'],
				'allow' => true,
				'roles' => ['@'],
			],
			[
				'controllers' => ['contact'],
				'actions' => ['index', 'xls'],
				'allow' => true,
				'roles' => ['@'],
			],
			[
				'actions' => ['validate'],
				'allow' => true,
				'roles' => ['@'],
			],
			[
				'controllers' => ['part', 'warehouse', 'warehouse-report-group', 'product', 'currency-rate'],
				'actions' => ['index', 'view', 'xls', 'pop'],
				'allow' => true,
				'roles' => ['@'],
			],
			[
				'controllers' => ['report'],
				'allow' => true,
				'roles' => ['@'],
			],
			[
				'controllers' => ['monitor'],
				'actions' => ['shop', 'line', 'daily'],
				'roles' => ['?', '@'],
				'allow' => true
			],
			[
				'controllers' => ['part-part'],
				'actions' => ['index', 'view', 'xls', 'part-raw-excel', 'detailed-bom', 'download-det-bom'],
				'allow' => true,
				'roles' => ['@'],
			],
			[
				'actions' => ['index', 'view', 'xls', 'search'],
				'allow' => true,
				'roles' => ['observer'],
			],
		];


		// $rollar ro'yxatini normal xolatga keltirish

		$roles = [];
		$actions = [];
		$permissions = [];
		foreach ($rm as $rmRow) {
			$roles[] = $rmRow['role'];
			$permissions[] = $rmRow['permission'];
			$actions[] = $rmRow['action'];
		}

		$roles = array_unique($roles);
		$permissions = array_unique($permissions);
		$actions = array_unique($actions);

		$rm2 = [];
		foreach ($roles as $role) {
			foreach ($rm as $rmRow) {
				if ($role == $rmRow['role'])
					$rm2[$role][] = $rmRow['permission'];
			}
		}

		// generate code for seeding permissions
		$fp = fopen('config/auth_item_seed.sql', 'w+');
		foreach ($permissions as $perm) {
			$item = "['" . $perm . "',2],";
			fwrite($fp, $item . "\n");
		}
		fclose($fp);

		// ***********************************
		$fp = fopen('config/auth_item_child_seed.sql', 'w+');
		$result = [];
		$errors = [];
		foreach ($rules as $rule) {
			if (!$rule['allow']) continue;
			foreach ($rule['roles'] as $role) {

				if (in_array($role, ['@', '?', 'observer', 'admin'])) continue;

				if (isset($rule['controllers']) and isset($rule['actions'])) {

					foreach ($rule['controllers'] as $controller) {
						foreach ($rule['actions'] as $action) {
							$permission = $controller . '-' . $action;
							if (in_array($permission, $permissions)) {
								$result[] = [
									'role' => $role,
									'permission' => $permission
								];
								//$sql = "INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('" . $role . "', '" . $permission . "');";
								$sql = "['" . $role . "','" . $permission . "'],";
								fwrite($fp, $sql . "\n");
							} else {
								// $errors[] = [
								// 	'role' => $role,
								// 	'permission' => $permission
								// ];
							}
						}
					}
				} elseif (isset($rule['controllers']) and !isset($rule['actions'])) {
					// ma'lum controllerlarning barcha actionlari
					foreach ($rule['controllers'] as $controller) {
						foreach ($actions as $action) {
							$permission = $controller . '-' . $action;
							if (in_array($permission, $permissions)) {
								$result[] = [
									'role' => $role,
									'permission' => $permission
								];
								//$sql = "INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('" . $role . "', '" . $permission . "');";
								$sql = "['" . $role . "','" . $permission . "'],";
								fwrite($fp, $sql . "\n");
							} else {
								$errors[] = [
									'role' => $role,
									'permission' => $permission
								];
							}
						}
					}
				} elseif (!isset($rule['controllers']) and isset($rule['actions'])) {
					// barcha controllerlardagi ma'lum actionlar 


				}
			}
		}
		fclose($fp);

		// INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('admin', 'buyer')

		// echo "\n";
		// print_r($result);
		// echo "\n";

		//echo 'Errors:';
		echo "\n";
		print_r($result);
		echo "\n";
		die;
	}
}
