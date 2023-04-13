<?php
use yii\widgets\Menu; ?>
<!-- Left side column. contains the sidebar -->

<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" data-step="1" data-intro="<?=Yii::t('intro', 'Sidebar')?>">
    <!-- Sidebar user panel -->
    <div class="user-panel">

      <div class="pull-left image ">
        <img src="<?=Yii::$app->homeUrl?>img/user.png" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?=Yii::$app->user->identity->fullname?></p>
        <a style="font-size: 14px;">
          <?=Yii::t('app', 'Role')?>:&nbsp;&nbsp;<span style="color: #ff870b;"><?=Yii::$app->user->identity->rolename?></span>
        </a>
      </div>
    </div>
    <!-- search form -->
    <div class="sidebar-form">
      <div class="input-group">
        <input type="text" id="menu-search" class="form-control input-sm" placeholder="<?=Yii::t('app', 'Search')?>...">
        <span class="input-group-btn">
          <button type="submit" name="search" id="search-btn" class="btn btn-flat">
            <i class="fa fa-search"></i>
          </button>
        </span>
      </div>
    </div>
    <!-- /.search form -->

    <?php if (in_array(Yii::$app->user->identity->rolename, ['mrp', 'mrp-logx', 'plan', 'mrpc', 'counter', 'crusher'])) { ?>
      <p style="clear: both;color: #ffffff;padding-left: 10px;" <? if (count(Yii::$app->user->identity->warehouseNames) > 3) { ?> title="<?=implode('&#010;', Yii::$app->user->identity->warehouseNames)?> <? } ?>">
        <?=Yii::t('app', 'Warehouse(s)')?>:<br><span style="color: #ff870b;"><?=implode('<br>', array_slice(Yii::$app->user->identity->warehouseNames, 0, 3))?><? if (count(Yii::$app->user->identity->warehouseNames) > 3) {
            echo '<br>...';
          } ?></span>
      </p>
    <?php } ?>

    <?php
    $menuItems = [];
    // new menu
    $m100_purchase = [];
    $m110_sourcing = [];
    $m113_directory = [];
    $m120_mfu = [];
    $m126_payment = [];
    $m127_directory = [];
    $m200_inventory = [];
    $m210_whm = [];
    $m220_mf = [];
    $m221_packing = [];
    $m230_directory = [];
    $m300_production = [];
    $m310_production_directory = [];
    $m400_plm = [];
    $m430_directory = [];
    $m500_sales = [];
    $m_new = [];
    $m560_directory = [];
    $m700_admin = [];
    $m710_statistics = [];
    $m130_logistics = [];
    $m131_directory = [];
    $test_admin = [];
    $reporting = [];
    $reportData = [];
    $planning = [];

    if (Yii::$app->user->can('freight-invoice-index')) {
      $m130_logistics[] = [
        'label' => Yii::t('app', 'Freight invoice'),
        'url' => ['/freight-invoice/index'],
        'template' => '<a href="{url}"><i class="fa fa-file-text"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('carrier-index')) {
      $m131_directory[] = [
        'label' => Yii::t('app', 'Carrier info'),
        'url' => ['/carrier/index'],
        'template' => '<a href="{url}"><i class="fa fa-truck"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('point-index')) {
      $m131_directory[] = [
        'label' => Yii::t('app', 'Points'),
        'url' => ['/point/index'],
        'template' => '<a href="{url}"><i class="fa fa-map-marker"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('route-index')) {
      $m131_directory[] = [
        'label' => Yii::t('app', 'Routes'),
        'url' => ['/route/index'],
        'template' => '<a href="{url}"><i class="fa fa-map"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('container-type-index')) {
      $m131_directory[] = [
        'label' => Yii::t('app', 'Container types'),
        'url' => ['/container-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-square-o"></i> <span>{label}</span></a>',
      ];
    }


    if (Yii::$app->user->can('stock-index')) {
      $m210_whm[] = [
        'label' => Yii::t('app', 'Stock'),
        'url' => ['/stock/index'],
        'template' => '<a href="{url}"><i class="fa fa-bar-chart"></i> <span>{label}</span></a>',
      ];
    }
    // For admin or superadmin (development)

    if (Yii::$app->user->can('mold-index')) {
      $m310_production_directory[] = [
        'label' => Yii::t('app', 'Mold'),
        'url' => ['mold/index'],
        'template' => '<a href="{url}"><i class="fa fa-tv"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('machine-index')) {
      $m310_production_directory[] = [
        'label' => Yii::t('app', 'Machine'),
        'url' => ['machine/index'],
        'template' => '<a href="{url}"><i class="fa fa-tv"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('mold-machine/index')) {
      $m310_production_directory[] = [
        'label' => Yii::t('app', 'Mold Machines'),
        'url' => ['mold-machine/index'],
        'template' => '<a href="{url}"><i class="fa fa-tv"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('line-stop-reason-index')) {
      $m310_production_directory[] = [
        'label' => Yii::t('app', 'Line stop reason'),
        'url' => ['/line-stop-reason/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('superadmin')) {
      $test_admin[] = [
        'label' => Yii::t('app', 'Location'),
        'url' => ['location/index'],
        'template' => '<a href="{url}"><i class="fa fa-location-arrow"></i> <span>{label}</span></a>',
      ];

      $test_admin[] = [
        'label' => Yii::t('app', 'Location type'),
        'url' => ['location-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-location-arrow"></i> <span>{label}</span></a>',
      ];
    }

    if (count($test_admin) > 0) {
      // $menuItems[] = [
      //   'label' => Yii::t('app', 'Testing Admin'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="glyphicon glyphicon-fire"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $test_admin,
      // ];
    }
    if (Yii::$app->user->can('document-index')) {
      $m210_whm[] = [
        'label' => Yii::t('app', 'Receive & Issue'),
        'url' => ['/document/index'],
        'template' => '<a href="{url}"><i class="fa fa-file-text"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('warehouse-report-group-index')) {
      $m230_directory[] = [
        'label' => Yii::t('app', 'Warehouse report groups'),
        'url' => ['/warehouse-report-group/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('warehouse-index')) {
      $m230_directory[] = [
        'label' => Yii::t('app', 'Warehouses'),
        'url' => ['/warehouse/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-order-index')) {
      $m120_mfu[] = [
        'label' => Yii::t('app', 'Orders Supplier'),
        'url' => ['/part-order/index'],
        'template' => '<a href="{url}"><i class="fa fa-edit"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('contract-index')) {
      $m110_sourcing[] = [
        'label' => Yii::t('app', 'Contract Supplier'),
        'url' => ['/contract/index'],
        'template' => '<a href="{url}"><i class="fa fa-handshake-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('contract-detail-index')) {
      $m110_sourcing[] = [
        'label' => Yii::t('app', 'Part contract'),
        'url' => ['/contract-detail/index'],
        'template' => '<a href="{url}"><i class="fa fa-handshake-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('supplier-index')) {
      $m110_sourcing[] = [
        'label' => Yii::t('app', 'Supplier info'),
        'url' => ['/supplier/index'],
        'template' => '<a href="{url}"><i class="fa fa-link"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('payment-term-index')) {
      $m113_directory[] = [
        'label' => Yii::t('app', 'Payment terms'),
        'url' => ['/payment-term/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('contract-subject-index')) {
      $m113_directory[] = [
        'label' => Yii::t('app', 'Contract subject'),
        'url' => ['/contract-subject/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('delivery-term-index')) {
      $m127_directory[] = [
        'label' => Yii::t('app', 'Delivery terms'),
        'url' => ['/delivery-term/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('contract-source-index')) {
      $m127_directory[] = [
        'label' => Yii::t('app', 'Supply source'),
        'url' => ['/contract-source/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('container-invoice-index')) {
      $m120_mfu[] = [
        'label' => Yii::t('app', 'ASN'),
        'url' => ['/container-invoice/index'],
        'template' => '<a href="{url}"><i class="fa fa-sticky-note-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('shipment-index')) {
      // $m120_mfu[] = [
      //   'label' => Yii::t('app', 'Shipment control'),
      //   'url' => ['/shipment/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-sticky-note-o"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('pack-index')) {
      $m221_packing[] = [
        'label' => Yii::t('app', 'Container'),
        'url' => ['/pack/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-packing-index')) {
      $m221_packing[] = [
        'label' => Yii::t('app', 'Component'),
        'url' => ['/part-packing/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('pack-level-index')) {
      $m230_directory[] = [
        'label' => Yii::t('app', 'Pack level'),
        'url' => ['/pack-level/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('lms-index')) {
      $m220_mf[] = [
        'label' => Yii::t('app', 'Storage'),
        'url' => ['/lms/index'],
        'template' => '<a href="{url}"><i class="fa fa-building"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('coverage-balance-index')) {
      $m126_payment[] = [
        'label' => Yii::t('app', 'Coverage balance'),
        'url' => ['/coverage-balance/index'],
        'template' => '<a href="{url}"><i class="fa fa-handshake-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('payment-control-index')) {
      $m126_payment[] = [
        'label' => Yii::t('app', 'Payment'),
        'url' => ['/payment-control/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('invoice-index')) {
      $m126_payment[] = [
        'label' => Yii::t('app', 'Invoice'),
        'url' => ['/invoice/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('invoice-payment-index')) {
      $m126_payment[] = [
        'label' => Yii::t('app', 'Payment — invoice'),
        'url' => ['/invoice-payment/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('mfu-index')) {
      $m120_mfu[] = [
        'label' => Yii::t('app', 'MFU'),
        'url' => ['/mfu/index'],
        'template' => '<a href="{url}"><i class="fa fa-file-text"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('consolidation-type-index')) {
      $m127_directory[] = [
        'label' => Yii::t('app', 'Consolidation type'),
        'url' => ['/consolidation-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('gtd-index')) {
      $m120_mfu[] = [
        'label' => Yii::t('app', 'Customs declaration'),
        'url' => ['/gtd/index'],
        'template' => '<a href="{url}"><i class="fa fa-file-text-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-pop')) {
      // $m120_mfu[] = [
      //   'label' => Yii::t('app', 'POP'),
      //   'url' => ['/part/pop'],
      //   'template' => '<a href="{url}"><i class="fa fa-file-text"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('production-plan-index')) {
      $planning[] = [
        //        'label' => Yii::t('app', 'JV'),
        'label' => Yii::$app->params['comp_short_name'],
        'url' => ['/production-plan/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (count($planning) > 0) {
      $m300_production[] = [
        'label' => Yii::t('app', 'Production plan'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-calendar"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $planning,
      ];
    }

    if (Yii::$app->user->can('production-order-create')) {
      $m300_production[] = [
        'label' => Yii::t('app', 'Production count'),
        'url' => ['/production-order/create'],
        'template' => '<a href="{url}"><i class="fa fa-table"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('production-order-upload')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Count upload'),
      //   'url' => ['/production-order/upload'],
      //   'template' => '<a href="{url}"><i class="fa fa-table"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('production-order-create-isbulk')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Labeling'),
      //   'url' => ['/production-order/create-isbulk'],
      //   'template' => '<a href="{url}"><i class="fa fa-list-alt"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('machine-counter')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Machine Counter'),
      //   'url' => ['machine/counter'],
      //   'template' => '<a href="{url}"><i class="fa fa-tv"></i> <span>{label}</span></a>',
      // ];
    }

    if (Yii::$app->user->can('line-stop-index')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Line stop'),
      //   'url' => ['/line-stop/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-warning"></i> <span>{label}</span></a>',
      // ];
    }

    if (Yii::$app->user->can('part-production-monitor-index')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Production results'),
      //   'url' => ['/part-production-monitor/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-warning"></i> <span>{label}</span></a>',
      // ];
    }

    if (Yii::$app->user->can('setting-index')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Setting'),
        'url' => ['/setting/index'],
        'template' => '<a href="{url}"><i class="fa fa-cog"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('user-index')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Users'),
        'url' => ['/user/index'],
        'template' => '<a href="{url}"><i class="fa fa-users"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('auth-item-index')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Roles and permissions'),
        'url' => ['/auth-item/index'],
        'template' => '<a href="{url}"><i class="fa fa-users"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('history-document-index')) {
      $m210_whm[] = [
        'label' => Yii::t('app', 'History documents'),
        'url' => ['/history-document/index'],
        'template' => '<a href="{url}"><i class="fa fa-list-ul"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('document-type-index')) {
      $m230_directory[] = [
        'label' => Yii::t('app', 'Document types'),
        'url' => ['/document-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('ship-mode-index')) {
      $m127_directory[] = [
        'label' => Yii::t('app', 'Ship mode'),
        'url' => ['/ship-mode/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('cargo-type-index')) {
      $m127_directory[] = [
        'label' => Yii::t('app', 'Cargo types'),
        'url' => ['/cargo-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('defect-index')) {
      $m230_directory[] = [
        'label' => Yii::t('app', 'Defects'),
        'url' => ['/defect/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('truck-index')) {
      // $m560_directory[] = [
      //   'label' => Yii::t('app', 'Trucks'),
      //   'url' => ['/truck/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-car"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('driver-index')) {
      // $m560_directory[] = [
      //   'label' => Yii::t('app', 'Drivers'),
      //   'url' => ['/driver/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('receiving-person-index')) {
      // $m560_directory[] = [
      //   'label' => Yii::t('app', 'Attorney letter'),
      //   'url' => ['/receiving-person/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-book"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('customer-type-index')) {
      $m560_directory[] = [
        'label' => Yii::t('app', 'Customer types'),
        'url' => ['/customer-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('fg-invoice-index')) {
      $m_new[] = [
        'label' => 'Статус обеспеченности',
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="/report/coverage"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('fg-invoice-index')) {
      $m_new[] = [
        'label' => 'Part requirement',
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="/report/requirement"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if(Yii::$app->user->can('fg-invoice-index')) {
      $m_new[] = [
        'label' => 'Part requirement Short',
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="/report/requirement-short"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('fg-invoice-index')) {
      $m_new[] = [
        'label' => 'Текущий остаток',
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="/report/material-stock"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('fg-invoice-index')) {
      $m_new[] = [
        'label' => 'Отчёт по клиентам',
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="/report/sales-payment-info"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    

    if (Yii::$app->user->can('posts-index')) {
      $m_new[] = [
        'label' => 'Posts',
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="/posts/index"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('fg-invoice-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Waybill'),
        'url' => ['/fg-invoice/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('waybill-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Invoice'),
        'url' => ['/waybill/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('fg-invoice-contract-factory')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Группа Счёт фактура'),
        'url' => ['/fg-invoice/contract-factory'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    
    if (Yii::$app->user->can('sales-plan-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Sales plan'),
        'url' => ['/sales-plan/index'],
        'template' => '<a href="{url}"><i class="fa fa-calendar"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('sales-contract-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Sales contract'),
        'url' => ['/sales-contract/index'],
        'template' => '<a href="{url}"><i class="fa fa-handshake-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('sales-contract-detail-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'FG contract'),
        'url' => ['/sales-contract-detail/index'],
        'template' => '<a href="{url}"><i class="fa fa-handshake-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('customer-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Customer info'),
        'url' => ['/customer/index'],
        'template' => '<a href="{url}"><i class="fa fa-user"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('recept-control-index')) {
      $m500_sales[] = [
          'label' => Yii::t('app', 'Receipt control'),
          'url' => ['/recept-control/index'],
          'template' => '<a href="{url}"><i class="fa fa-money"></i> <span>{label}</span></a>',
      ];
    }
//    if (Yii::$app->user->can('fg-invoice-receipt-index')) {
//      $m500_sales[] = [
//        'label' => Yii::t('app', 'Fg invoice receipt'),
//        'url' => ['/fg-invoice-receipt/index'],
//        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
//      ];
//    }
    if (Yii::$app->user->can('receipt-waybill-index')) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Fg invoice receipt'),
        'url' => ['/receipt-waybill/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-index')) {
      $m400_plm[] = [
        'label' => Yii::t('app', 'Parts'),
        'url' => ['/part/index'],
        'template' => '<a href="{url}"><i class="fa fa-book"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('product-specification-index')) {
      $m400_plm[] = [
        'label' => Yii::t('app', 'Product specification'),
        'url' => ['/product-specification/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-part-index')) {
      // $m400_plm[] = [
      //   'label' => Yii::t('app', 'BOM'),
      //   'url' => ['/part-part/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      // ];
    }

    if (Yii::$app->user->can('part-part-version-index')) {
      // $m400_plm[] = [
      //   'label' => Yii::t('app', 'BOM version'),
      //   'url' => ['/part-part-version/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('part-type-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'The types of part'),
        'url' => ['/part-type/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-color-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'Part color'),
        'url' => ['/part-color/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-mark-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'Part mark'),
        'url' => ['/part-mark/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('unit-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'Units'),
        'url' => ['/unit/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('product-model-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'OEM models'),
        'url' => ['/product-model/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('product-line-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'The production lines'),
        'url' => ['/product-line/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('product-group-index')) {
      $m430_directory[] = [
        'label' => Yii::t('app', 'Product groups'),
        'url' => ['/product-group/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('stock-upload')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Upload stock'),
        'url' => ['/stock/upload'],
        'template' => '<a href="{url}"><i class="fa fa-upload"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-part-upload')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Upload BOM'),
        'url' => ['/part-part/upload'],
        'template' => '<a href="{url}"><i class="fa fa-upload"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('part-upload')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Upload part'),
        'url' => ['/part/upload'],
        'template' => '<a href="{url}"><i class="fa fa-upload"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('air-shipment-reason-index')) {
      // $m700_admin[] = [
      //   'label' => Yii::t('app', 'Reason for Air shipment'),
      //   'url' => ['/air-shipment-reason/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-plane"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('factory-index')) {
      $m700_admin[] = [
        'label' => Yii::t('app', 'Plants'),
        'url' => ['/factory/index'],
        'template' => '<a href="{url}"><i class="fa fa-industry"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('line-index')) {
      // $m700_admin[] = [
      //   'label' => Yii::t('app', 'Lines'),
      //   'url' => ['/line/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('uloc-index')) {
      // $m700_admin[] = [
      //   'label' => Yii::t('app', 'Ulocs'),
      //   'url' => ['/uloc/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('contact-index')) {
      // $m700_admin[] = [
      //   'label' => Yii::t('app', 'Contact'),
      //   'url' => ['/contact/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-address-book"></i> <span>{label}</span></a>',
      // ];
    }

    if ( in_array(Yii::$app->user->identity->rolename,['admin','superadmin'])) {
      $m710_statistics[] = [
        'label' => Yii::t('app', 'Statistics'),
        'url' => ['/report/visitors'],
        'template' => '<a href="{url}"><i class="fa fa-bar-chart"></i> <span>{label}</span></a>',
      ];
      $m710_statistics[] = [
        'label' => Yii::t('app', 'List'),
        'url' => ['/report/all-visitors'],
        'template' => '<a href="{url}"><i class="fa fa-bars"></i> <span>{label}</span></a>',
      ];
    }

    if (Yii::$app->user->can('currency-rate-index')) {
      $m113_directory[] = [
        'label' => Yii::t('app', 'Currency rate'),
        'url' => ['/currency-rate/index'],
        'template' => '<a href="{url}"><i class="fa fa-dot-circle-o"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('crushing-index')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Shredding'),
      //   'url' => ['/crushing/index'],
      //   'template' => '<a href="{url}"><i class="fa fa-cut"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('production-order-defect-create')) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Quality'),
      //   'url' => ['/production-order-defect/create'],
      //   'template' => '<a href="{url}"><i class="fa fa-calendar-check-o"></i> <span>{label}</span></a>',
      // ];
    }
    if (Yii::$app->user->can('vehicle-coverage-input-index')) {
      $reportData[] = [
        'label' => Yii::t('app', 'Vehicle set'),
        'url' => ['/vehicle-coverage-input/index'],
        'template' => '<a href="{url}"><i class="fa fa-tv"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('air-shipment-index')) {
      $reportData[] = [
        'label' => Yii::t('app', 'Air shipment'),
        'url' => ['/air-shipment/index'],
        'template' => '<a href="{url}"><i class="fa fa-plane"></i> <span>{label}</span></a>',
      ];
    }
    if (Yii::$app->user->can('report-index')) {
      $reporting[] = [
        'label' => Yii::t('app', 'Reports'),
        'url' => ['/report/index'],
        'template' => '<a href="{url}"><i class="fa fa-list"></i> <span>{label}</span></a>',
      ];
    }

    // Logistics
    if (count($m131_directory) > 0) {
      $m130_logistics[] = [
        'label' => Yii::t('app', 'Directory'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m131_directory,
      ];
    }

    // Purchase
    if (count($m126_payment) > 0) {
      // $m120_mfu[] = [
      //   'label' => Yii::t('app', 'Payment Tracking'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="fa fa-money"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $m126_payment,
      // ];
    }
    if (count($m127_directory) > 0) {
      $m120_mfu[] = [
        'label' => Yii::t('app', 'Directory'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m127_directory,
      ];
    }
    if (count($m113_directory) > 0) {
      $m110_sourcing[] = [
        'label' => Yii::t('app', 'Directory'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m113_directory,
      ];
    }
    if (count($m110_sourcing) > 0) {
      $m100_purchase[] = [
        'label' => Yii::t('app', 'Sourcing'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-random"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m110_sourcing,
      ];
    }
    if (count($m120_mfu) > 0) {
      $m100_purchase[] = [
        'label' => Yii::t('app', 'Material Follow Up'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-random"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m120_mfu,
      ];
    }
    if (count($m130_logistics) > 0) {
      // $m100_purchase[] = [
      //   'label' => Yii::t('app', 'Logistics'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="fa fa-ship"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $m130_logistics,
      // ];
    }
    if (count($m100_purchase) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'Purchasing'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-cart-arrow-down"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m100_purchase,
      ];
    }
    // Inventory
    if (count($m221_packing) > 0) {
      $m220_mf[] = [
        'label' => Yii::t('app', 'Packing'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-file-zip-o"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m221_packing,
      ];
    }
    if (count($m210_whm) > 0) {
      $m200_inventory[] = [
        'label' => Yii::t('app', 'Warehouse management'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-building-o"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m210_whm,
      ];
    }
    if (count($m220_mf) > 0) {
      // $m200_inventory[] = [
      //   'label' => Yii::t('app', 'Material Flow'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="fa fa-sitemap"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $m220_mf,
      // ];
    }
    if (count($m230_directory) > 0) {
      $m200_inventory[] = [
        'label' => Yii::t('app', 'Directory'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m230_directory,
      ];
    }
    if (count($m200_inventory) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'Inventory'),
        'icon' => 'fa fa-circle-o',
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-gears"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m200_inventory,
      ];
    }
    // Production Control
    if (count($m310_production_directory) > 0) {
      // $m300_production[] = [
      //   'label' => Yii::t('app', 'Directory'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $m310_production_directory,
      // ];
    }

    if (count($m300_production) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'Production Control'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-diamond"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m300_production,
      ];
    }
    // PLM
    if (count($m430_directory) > 0) {
      $m400_plm[] = [
        'label' => Yii::t('app', 'Directory'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m430_directory,
      ];
    }
    if (count($m400_plm) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'BOM'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-gears"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m400_plm,
      ];
    }
    // Sales
    if (count($m560_directory) > 0) {
      $m500_sales[] = [
        'label' => Yii::t('app', 'Directory'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m560_directory,
      ];
    }
    if (count($m500_sales) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'Sales'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-line-chart"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m500_sales,
      ];
    }
    // Report
    if (count($reportData) > 0) {
      // $reporting[] = [
      //   'label' => Yii::t('app', 'Reports data'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="fa fa-minus-square"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $reportData,
      // ];
    }
    if (count($reporting) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'Reporting'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-pie-chart"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $reporting,
      ];
    }
    // Admin

    if (count($m710_statistics) > 0) {
      // $m700_admin[] = [
      //   'label' => Yii::t('app', 'Visitors'),
      //   'url' => '#',
      //   'template' => '<a href="{url}"><i class="fa fa-users"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      //   'options' => ['class' => 'treeview'],
      //   'items' => $m710_statistics,
      // ];
    }

    if (count($m700_admin) > 0) {
      $menuItems[] = [
        'label' => Yii::t('app', 'Admin'),
        'url' => '#',
        'template' => '<a href="{url}"><i class="fa fa-user-secret"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
        'options' => ['class' => 'treeview'],
        'items' => $m700_admin,
      ];
    }
    $menuItems[] = [
      'label' => 'Управление материалами',
      'url' => '#',
      'template' => '<a href="{url}"><i class="fa fa-line-chart"></i> <span>{label}</span> <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span> </a>',
      'options' => ['class' => 'treeview'],
      'items' => $m_new,
    ];




    ?>

    <?php echo Menu::widget(
      [
        'items' => $menuItems,
        'options' => [
          'class' => 'sidebar-menu',
          'data-widget' => 'tree'
        ],
        'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
        'activeCssClass' => 'active',
        'firstItemCssClass' => 'fist',
        'lastItemCssClass' => 'last',
        'activateParents' => true,
      ]
    );
    ?>

  </section>
  <!-- /.sidebar -->
</aside>
