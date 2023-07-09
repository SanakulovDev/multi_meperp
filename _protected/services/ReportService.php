<?php

namespace app\services;

use app\components\Helpers;
use app\console\controllers\CoverageController;
use app\console\controllers\CoverageVehicleController;
use app\enums\ContainerType;
use app\enums\FreightInvoicePaymentType;
use app\enums\FreightInvoiceType;
use app\enums\ShipMode;
use app\models\CoverageVehicle;
use app\models\Currency;
use app\models\CurrencyRate;
use app\models\Customer;
use app\models\FreightInvoiceDetail;
use app\models\HealthCheck;
use app\models\InvoiceDetail;
use app\models\LineStopReason;
use app\models\Part;
use app\models\PartOrder;
use app\models\PartOrderDetail;
use app\models\PaymentControl;
use app\models\ProductionOrder;
use app\models\ProductModel;
use app\models\Req;
use app\models\ReqDetailPlan;
use app\models\ReqDetailWide;
use app\models\ShipmentPerformanceDetail;
use app\models\Stock;
use app\models\Supplier;
use app\models\SalesContract;
use app\models\FgInvoice;
use app\models\FgInvoiceDetail;
use app\models\Waybill;
use app\models\User;
use app\models\Unit;
use app\models\ReceptControl;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use Codeception\Lib\Generator\Helper;
use Yii;
use yii\helpers\ArrayHelper;

class ReportService
{
    private $groupedPlan = 0;

    public function coverageByVehicleSet()
    {
        $covVeh = CoverageVehicle::find()->where(['type' => CoverageVehicleController::TYPE_DAILY])->asArray()->all();
        $query = "
      select type, min(ifnull(stock_out,'')) stock_out from coverage_vehicle where `type` in ('DS','D','DO') group by type
    ";
        $dates1 = Yii::$app->db->createCommand($query)->queryAll();
        $dates = [];
        foreach ($dates1 as $row) {
            $dates[$row['type']] = ($row['stock_out']) ? date('d.m.Y', strtotime($row['stock_out'])) : Yii::t('app', 'No dates');
        }
        $models = [];
        foreach ($covVeh as $cv) {
            $models[] = $cv['model_id'];
        }
        $models = array_unique($models);
        $data = [];
        foreach ($models as $model) {
            foreach ($covVeh as $cv) {
                if ($cv['model_id'] == $model) {
                    $data['stock'][$model] = $cv['stock'] ?? 0;
                    $data['uamstock'][$model] = $cv['uamstock'] ?? 0;
                    $data['intransit'][$model] = $cv['intransit'] ?? 0;
                    $data['orders'][$model] = $cv['orders'] ?? 0;
                }
            }
        }
        $dohData = $this->doh('cvs');
        $models = ProductModel::find()->where(['is_vehicle' => ProductModel::IS_VEHICLE])->all();

        return compact(
            'data',
            'dates',
            'dohData',
            'models'
        );
    }

    public function doh($r = null)
    {
        $data = [
            'less60' => $this->getDohData60(),
            'greater120' => $this->getDohData('g120')
        ];
        $keys = [];
        foreach ($data['less60'] as $row) {
            $keys[] = $row['country'] . '|' . $row['supplier'];
        }
        foreach ($data['greater120'] as $row) {
            $keys[] = $row['country'] . '|' . $row['supplier'];
        }
        $keys = array_unique($keys);
        asort($keys);
        $result = [];
        $totalLess60 = 0;
        $totalGreater120 = 0;
        $totalLess60Amount = 0;
        $totalGreater120Amount = 0;
        $countries = [];
        $suppliers = [];
        foreach ($keys as $rKey) {
            $keyArr = explode('|', $rKey);
            $tmpRes = [];
            $tmpRes['unknown'] = (empty($keyArr[0]) || empty($keyArr[1])) ? '1' : '';
            if (!empty($keyArr[0])) {
                $tmpRes['country'] = $keyArr[0];
                $countries[] = $tmpRes['country'];
            } else {
                $tmpRes['country'] = Yii::t('app', 'Unknown country');
            }
            if (!empty($keyArr[1])) {
                $tmpRes['supplier'] = $keyArr[1];
                $suppliers[] = $tmpRes['supplier'];
            } else {
                $tmpRes['supplier'] = Yii::t('app', 'Unknown supplier');
            }
            $less60 = 0;
            $greater120 = 0;
            $less60Amount = 0;
            $greater120Amount = 0;
            foreach ($data['less60'] as $row) {
                if ($row['country'] == $keyArr[0] and $row['supplier'] == $keyArr[1]) {
                    $less60 = (!empty($row['total_count'])) ? $row['total_count'] : 0;
                    $less60Amount = (!empty($row['total_amount'])) ? $row['total_amount'] : 0;
                    break;
                }
            }
            foreach ($data['greater120'] as $row) {
                if ($row['country'] == $keyArr[0] and $row['supplier'] == $keyArr[1]) {
                    $greater120 = (!empty($row['total_count'])) ? $row['total_count'] : 0;
                    $greater120Amount = (!empty($row['total_amount'])) ? $row['total_amount'] : 0;
                    break;
                }
            }
            $tmpRes['less60'] = $less60;
            $tmpRes['greater120'] = $greater120;
            $tmpRes['less60Amount'] = $less60Amount;
            $tmpRes['greater120Amount'] = $greater120Amount;
            $totalLess60 += $less60;
            $totalGreater120 += $greater120;
            $totalLess60Amount += $less60Amount;
            $totalGreater120Amount += $greater120Amount;
            $result[] = $tmpRes;
        }
        $total = [
            'countries' => count(array_unique($countries)),
            'suppliers' => count(array_unique($suppliers)),
            'less60' => $totalLess60,
            'greater120' => $totalGreater120,
            'less60Amount' => $totalLess60Amount,
            'greater120Amount' => $totalGreater120Amount
        ];
        if (!$r) {
            return compact('total', 'data');
        } else {
            return $total;
        }
    }

    protected function getDohData60()
    {
        $less_dates_count = isset(Yii::$app->params['less_dates_count']) ? Yii::$app->params['less_dates_count'] : 0;
        $columnNumber = array_search(date('Y-m-d', strtotime('+' . $less_dates_count . ' days')), Helpers::getPeriodFull()) + 1;
        $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode('USD')->id);
        $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode('EUR')->id);
        $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode('RUB')->id);
        $query = ReqDetailWide::find()
            ->joinWith('req')
            ->where([
                'and',
                ['req_detail_wide.type' => CoverageController::TYPE_DAILY],
                ['<', 'req_detail_wide.col' . $columnNumber, 0],
            ]);
        $coverage = $query->all();
        $result = [];
        foreach ($coverage as $crow) {
            $actualContract = $crow->req->part->getActualContract();
            $supplierModel = $actualContract->contract->supplier ?? '';
            $countryCode = $supplierModel->countryCode ?? '';
            // Agar postavshik UZ bo'lsa hisobga olmaymiz
            if (($countryCode->alpha_2 ?? '') == 'UZ') {
                continue;
            }
            $price = $actualContract->price ?? 0;
            $currency = $actualContract->contract->currency->code ?? '';
            $priceUSD = 0;
            switch ($currency) {
                case 'EUR':
                    $priceUSD = $price * $rateEUR / $rateUSD;
                    break;
                case 'RUB':
                    $priceUSD = $price * $rateRUB / $rateUSD;
                    break;
                case 'UZS':
                    $priceUSD = $price / $rateUSD;
                    break;
                case 'USD':
                    $priceUSD = $price;
                    break;
            }
            $supplier = $supplierModel->name ?? '';
            $country = $countryCode->name ?? '';
            $totalRequiredAmount = abs($crow['col' . $columnNumber]) * $priceUSD;
            $result[] = [
                'coun_supp' => $country . '|' . $supplier,
                'supplier' => $supplier,
                'country' => $country,
                'amount' => $totalRequiredAmount
            ];
        }
        $keys = [];
        foreach ($result as $row) {
            $keys[] = $row['coun_supp'];
        }
        $keys = array_unique($keys);
        $data = [];
        foreach ($keys as $key) {
            $totalCount = 0;
            $totalAmount = 0;
            foreach ($result as $row) {
                if ($key == $row['coun_supp']) {
                    $totalCount++;
                    $totalAmount += $row['amount'];
                }
            }
            [$country, $supplier] = explode('|', $key);
            $data[] = [
                'country' => $country,
                'supplier' => $supplier,
                'total_count' => $totalCount,
                'total_amount' => $totalAmount
            ];
        }

        return $data;
    }

    protected function getDohData($type = 'g120')
    {
        if ($type == 'l60') {
            $days = isset(Yii::$app->params['less_dates_count']) ? Yii::$app->params['less_dates_count'] : 60;
            $sign = '<';
        } elseif ($type == 'g120') {
            $days = isset(Yii::$app->params['greater_dates_count']) ? Yii::$app->params['greater_dates_count'] : 120;
            $sign = '>';
        }
        $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode('USD')->id);
        $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode('EUR')->id);
        $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode('RUB')->id);
        $query = Req::find()
            ->where([
                'and',
                ['type' => CoverageController::TYPE_DAILY],
                [$sign, 'doh', $days]
            ]);
        $coverage = $query->all();
        $result = [];
        foreach ($coverage as $crow) {
            $actualContract = $crow->part->getActualContract();
            $supplierModel = $actualContract->contract->supplier ?? '';
            $countryCode = $supplierModel->countryCode ?? '';
            // Agar postavshik UZ bo'lsa hisobga olmaymiz
            if (($countryCode->alpha_2 ?? '') == 'UZ') {
                continue;
            }
            $price = $actualContract->price ?? 0;
            $currency = $actualContract->contract->currency->code ?? '';
            $priceUSD = 0;
            switch ($currency) {
                case 'EUR':
                    $priceUSD = $price * $rateEUR / $rateUSD;
                    break;
                case 'RUB':
                    $priceUSD = $price * $rateRUB / $rateUSD;
                    break;
                case 'UZS':
                    $priceUSD = $price / $rateUSD;
                    break;
                case 'USD':
                    $priceUSD = $price;
                    break;
            }
            $supplier = $supplierModel->name ?? '';
            $country = $countryCode->name ?? '';
            $daysQty = $days * round($crow->part->averageUsage);
            // 60 kunga yetish uchun yoki 120 kundan ortiq qty
            if ($type == 'l60') {
                $needQty = ($crow->totalstock > 0) ? $daysQty - $crow->totalstock : $daysQty;
            } else {
                $needQty = ($crow->totalstock > 0) ? $crow->totalstock - $daysQty : $daysQty;
            }
            // 120 kunga yetish uchun yoki 120 kundan ortiq summa
            $totalRequiredAmount = $needQty * $priceUSD;
            $result[] = [
                'coun_supp' => $country . '|' . $supplier,
                'supplier' => $supplier,
                'country' => $country,
                'amount' => $totalRequiredAmount
            ];
        }
        $keys = [];
        foreach ($result as $row) {
            $keys[] = $row['coun_supp'];
        }
        $keys = array_unique($keys);
        $data = [];
        foreach ($keys as $key) {
            $totalCount = 0;
            $totalAmount = 0;
            foreach ($result as $row) {
                if ($key == $row['coun_supp']) {
                    $totalCount++;
                    $totalAmount += $row['amount'];
                }
            }
            [$country, $supplier] = explode('|', $key);
            $data[] = [
                'country' => $country,
                'supplier' => $supplier,
                'total_count' => $totalCount,
                'total_amount' => $totalAmount
            ];
        }

        return $data;
    }

    public function healthCheck()
    {
        ini_set('memory_limit', '1024M');
        $query = "
			SELECT savollar.description savol_nomi, details.status status, details.description sabab, details.updated_at updated_at FROM 
      (SELECT * FROM health_check WHERE status='" . HealthCheck::STATUS_ACTIVE . "' ORDER BY sort_order) savollar
      LEFT JOIN
      (SELECT * FROM health_check_detail WHERE check_date='" . date('Y-m-d', time()) . "') details
      ON savollar.id=details.health_check_id
      ORDER BY savollar.sort_order
      ";
        $data = (Yii::$app->db->createCommand($query)->queryAll()) ? (Yii::$app->db->createCommand($query)->queryAll()) : null;

        return compact('data');
    }

    public function stock()
    {
        $rateUSD = CurrencyRate::currentRate(Currency::findOneCurrencyCode('USD')->id);
        $rateEUR = CurrencyRate::currentRate(Currency::findOneCurrencyCode('EUR')->id);
        $rateRUB = CurrencyRate::currentRate(Currency::findOneCurrencyCode('RUB')->id);
        $data = [];
        $stock = Stock::find()
            ->joinWith('part')
            ->joinWith('warehouse.warehouseReportGroup')
            ->orderBy('warehouse_report_group.sort_order')
            ->all();
        $keys = [];
        foreach ($stock as $st) {
            $keys[] = $st->warehouse->warehouse_report_group_id . '|' . $st->warehouse->warehouseReportGroup->title . '|' . $st->warehouse_id . '|' . $st->warehouse->name;
        }
        $keys = array_unique($keys);
        foreach ($keys as $key) {
            [$group_id, $group_name, $wh_id, $wh_name] = explode('|', $key);
            $data[$group_id]['group_name'] = Yii::t('app', $group_name);
            $totalUZS = $totalUSD = $totalEUR = $totalRUB = 0;
            $grandTotalUZS = 0;
            foreach ($stock as $st) {
                if ($group_id == $st->warehouse->warehouse_report_group_id and $wh_id == $st->warehouse_id) {
                    $actualContract = $st->part->getActualContract();
                    $currency = $actualContract->contract->currency->code ?? '';
                    $price = $actualContract->price ?? 0;
                    switch ($currency) {
                        case 'UZS':
                            $totalUZS += $st->qty * $price;
                            break;
                        case 'USD':
                            $totalUSD += $st->qty * $price;
                            break;
                        case 'EUR':
                            $totalEUR += $st->qty * $price;
                            break;
                        case 'RUB':
                            $totalRUB += $st->qty * $price;
                            break;
                    }
                }
                if ($totalUZS != 0 or $totalUSD != 0 or $totalEUR != 0 or $totalRUB != 0) {
                    $data[$group_id]['items'][$wh_id] = [
                        'wh_name' => $wh_name,
                        'UZS' => number_format($totalUZS / 1000, 0, '', ' '),
                        'USD' => number_format($totalUSD, 0, '', ' '),
                        'EUR' => number_format($totalEUR, 0, '', ' '),
                        'RUB' => number_format($totalRUB, 0, '', ' '),
                        'TUZS' => number_format(($totalUZS + ($totalUSD * $rateUSD) + ($totalEUR * $rateEUR) + ($totalRUB * $rateRUB)) / 1000, 0, '', ' ')
                    ];
                }
            }
            $grandTotalUZS = $grandTotalUSD = $grandTotalEUR = $grandTotalRUB = $grandTotalTUZS = 0;
            if (!isset($data[$group_id]['items'])) {
                $data[$group_id]['items'] = [];
            }
            foreach ($data[$group_id]['items'] as $item) {
                $grandTotalUZS += str_replace(' ', '', $item['UZS']);
                $grandTotalUSD += str_replace(' ', '', $item['USD']);
                $grandTotalEUR += str_replace(' ', '', $item['EUR']);
                $grandTotalRUB += str_replace(' ', '', $item['RUB']);
                $grandTotalTUZS += str_replace(' ', '', $item['TUZS']);
            }
            $data[$group_id]['UZS'] = number_format($grandTotalUZS, 0, '', ' ');
            $data[$group_id]['USD'] = number_format($grandTotalUSD, 0, '', ' ');
            $data[$group_id]['EUR'] = number_format($grandTotalEUR, 0, '', ' ');
            $data[$group_id]['RUB'] = number_format($grandTotalRUB, 0, '', ' ');
            $data[$group_id]['TUZS'] = number_format($grandTotalTUZS, 0, '', ' ');
            if (round($grandTotalUZS) == 0 and round($grandTotalUSD) == 0 and round($grandTotalEUR) == 0 and round($grandTotalRUB) == 0 and round($grandTotalTUZS) == 0) {
                unset($data[$group_id]);
            }
        }
        // In transit
        $inTransit = InvoiceDetail::find()
            ->joinWith(['contInv', 'part'])
            ->where([
                'and',
                ['>=', 'app_arr_at', 'CURDATE()'],
                ['not', ['shipped_at' => null]],
                ['arrived_at' => null],
                ['part.status' => Part::STATUS_ACTIVE]
            ])
            ->all();
        $inTransitUZS = $inTransitUSD = $inTransitEUR = $inTransitRUB = $inTransitTUZS = 0;
        foreach ($inTransit as $int) {
            $actualContract = $int->part->getActualContract();
            $currency = $actualContract->contract->currency->code ?? '';
            $price = $actualContract->price ?? 0;
            switch ($currency) {
                case 'UZS':
                    $inTransitUZS += $int->qty * $price;
                    break;
                case 'USD':
                    $inTransitUSD += $int->qty * $price;
                    break;
                case 'EUR':
                    $inTransitEUR += $int->qty * $price;
                    break;
                case 'RUB':
                    $inTransitRUB += $int->qty * $price;
                    break;
            }
        }
        $data['intransit']['group_name'] = Yii::t('app', 'In transit');
        $data['intransit']['items'] = [];
        $data['intransit']['UZS'] = number_format($inTransitUZS / 1000, 0, '', ' ');
        $data['intransit']['USD'] = number_format($inTransitUSD, 0, '', ' ');
        $data['intransit']['EUR'] = number_format($inTransitEUR, 0, '', ' ');
        $data['intransit']['RUB'] = number_format($inTransitRUB, 0, '', ' ');
        $data['intransit']['TUZS'] = number_format(($inTransitUZS + ($inTransitUSD * $rateUSD) + ($inTransitEUR * $rateEUR) + ($inTransitRUB * $rateRUB)) / 1000, 0, '', ' ');
        // ****
        $gGrandTotalUZS = $gGrandTotalUSD = $gGrandTotalEUR = $gGrandTotalRUB = $gGrandTotalTUZS = 0;
        foreach ($data as $item) {
            $gGrandTotalUZS += str_replace(' ', '', $item['UZS']);
            $gGrandTotalUSD += str_replace(' ', '', $item['USD']);
            $gGrandTotalEUR += str_replace(' ', '', $item['EUR']);
            $gGrandTotalRUB += str_replace(' ', '', $item['RUB']);
            $gGrandTotalTUZS += str_replace(' ', '', $item['TUZS']);
        }
        $total['UZS'] = $gGrandTotalUZS;
        $total['USD'] = $gGrandTotalUSD;
        $total['EUR'] = $gGrandTotalEUR;
        $total['RUB'] = $gGrandTotalRUB;
        $total['TUZS'] = $gGrandTotalTUZS;

        return compact('data', 'total');
    }

    public function factByHour($from, $to, $flocId = null, $lineId = null)
    {
        $cond = [
            'and',
            ['is_label' => ProductionOrder::LABEL_ACTUAL],
            ['between', 'production_order.created_at', $from, $to],
        ];
        if ($flocId) {
            $cond[] = ['part.warehouse_id' => $flocId];
        }
        $facts = ProductionOrder::find()
            ->joinWith(['part.warehouse'])
            ->where($cond)
            ->all();
        $data1 = [];
        foreach ($facts as $fact) {
            $tmp = [];
            $tmp['key'] = $fact->part->partinfo . '|' . $fact->part->part_name . '|' . ($fact->part->warehouse->name ?? '') . '|' . ($fact->part->productLine->linename ?? '');
            $tmp['partinfo'] = $fact->part->partinfo;
            $tmp['partname'] = $fact->part->part_name;
            $tmp['floc'] = $fact->part->warehouse->name ?? '';
            $tmp['created_at'] = date('Y-m-d H:i', $fact->created_at);
            $tmp['quantity'] = $fact->quantity;
            $factHour = $this->getFactHour($fact->created_at);
            if ($factHour) {
                $tmp['F' . $factHour] = $fact->quantity;
            }
            $data1[] = $tmp;
        }
        $keys = [];
        foreach ($data1 as $row) {
            $keys[] = $row['key'];
        }
        $keys = array_unique($keys);
        $data = [];
        foreach ($keys as $key) {
            [$partinfo, $partname, $floc] = explode('|', $key);
            $f1 = $f2 = $f3 = $f4 = $f5 = $f6 = $f7 = $f8 = $f9 = $f10 = $f11 = $f12 = 0;
            foreach ($data1 as $row) {
                if ($partinfo == $row['partinfo'] and $partname == $row['partname'] and $floc == $row['floc']) {
                    if (isset($row['F1'])) {
                        $f1 += $row['F1'];
                    }
                    if (isset($row['F2'])) {
                        $f2 += $row['F2'];
                    }
                    if (isset($row['F3'])) {
                        $f3 += $row['F3'];
                    }
                    if (isset($row['F4'])) {
                        $f4 += $row['F4'];
                    }
                    if (isset($row['F5'])) {
                        $f5 += $row['F5'];
                    }
                    if (isset($row['F6'])) {
                        $f6 += $row['F6'];
                    }
                    if (isset($row['F7'])) {
                        $f7 += $row['F7'];
                    }
                    if (isset($row['F8'])) {
                        $f8 += $row['F8'];
                    }
                    if (isset($row['F9'])) {
                        $f9 += $row['F9'];
                    }
                    if (isset($row['F10'])) {
                        $f10 += $row['F10'];
                    }
                    if (isset($row['F11'])) {
                        $f11 += $row['F11'];
                    }
                    if (isset($row['F12'])) {
                        $f12 += $row['F12'];
                    }
                }
            }
            $data[] = [
                'partinfo' => $partinfo,
                'partname' => $partname,
                'floc' => $floc,
                'F1' => $f1,
                'F2' => $f2,
                'F3' => $f3,
                'F4' => $f4,
                'F5' => $f5,
                'F6' => $f6,
                'F7' => $f7,
                'F8' => $f8,
                'F9' => $f9,
                'F10' => $f10,
                'F11' => $f11,
                'F12' => $f12,
                'total' => $f1 + $f2 + $f3 + $f4 + $f5 + $f6 + $f7 + $f8 + $f9 + $f10 + $f11 + $f12
            ];
        }

        return compact('data');
    }

    private function getFactHour($createdAt)
    {
        $hour = date('H', $createdAt);
        $firstHour = (int)substr(Yii::$app->params['shifts'][1][0], 0, 2);
        $c = 1;
        $shiftHours = [];
        for ($i = $firstHour; $i < $firstHour + 24; $i++) {
            if ($i >= 24) {
                $h = $i - 24;
            } else {
                $h = $i;
            }
            $shiftHours[$c] = str_pad($h, 2, "0", STR_PAD_LEFT);
            $c++;
        }
        foreach ($shiftHours as $key => $shiftHour) {
            if ($hour == $shiftHour) {
                return ($key <= 12) ? $key : $key - 12;
            }
        }

        return false;
    }

    /* Bu funksiya dev process uchun yaratildi */
    public function bomCostDetail($part_id)
    {
        $part = Part::findOne($part_id);

        return [
            'part' => [
                'partinfo' => $part->partinfo,
                'part_name' => $part->part_name,
                'part_name' => $part->part_name,
            ],
            'items' => $this->getComponenentsWithCost($part)
        ];
    }

    public function bomCost()
    {
        // Get All rates and create variables $rateUSD, $rateEUR, $rateRUB
        extract(Helpers::currencyRates());
        $data = [];
        $products = Part::find()
            ->with('allComponents')
            ->where([
                'status' => Part::STATUS_ACTIVE,
                'state' => Part::STATE_FINISHED
            ])->all();
        foreach ($products as $prod) {
            $componentsCost = $this->calcComponetsCost($prod);
            [$uzs, $usd, $eur, $rub] = $componentsCost;
            if ($uzs == 0 and $usd == 0 and $eur == 0 and $rub == 0) {
                $tuzs = 'N/A';
            } else {
                $tuzs = ($uzs + ($usd * $rateUSD) + ($eur * $rateEUR) + ($rub * $rateRUB)) / 1000;
            }
            $data[] = [
                'prod' => $prod,
                'uzs' => $uzs,
                'usd' => $usd,
                'eur' => $eur,
                'rub' => $rub,
                'tuzs' => $tuzs
            ];
        }

        return compact('data');
    }

    private function calcComponetsCost($part)
    {
        $allComponents = $part->allComponents;
        $uzs = $usd = $eur = $rub = 0;
        if ($allComponents) {
            foreach ($allComponents as $comp) {
                if ($comp->type != 'R') {
                    continue;
                }
                $contract = $comp->subPart->actualContract ?? '';
                if ($contract) {
                    $currency = $contract->contract->currency->code ?? '';
                    $price = $contract->price ?? 0;
                    switch ($currency) {
                        case 'UZS':
                            $uzs += $comp->usage_qty * $price;
                            break;
                        case 'USD':
                            $usd += $comp->usage_qty * $price;
                            break;
                        case 'EUR':
                            $eur += $comp->usage_qty * $price;
                            break;
                        case 'RUB':
                            $rub += $comp->usage_qty * $price;
                            break;
                    }
                }
            }
        } else {
            $contract = $part->actualContract ?? '';
            if ($contract) {
                $currency = $contract->contract->currency->code ?? '';
                $price = $contract->price ?? 0;
                switch ($currency) {
                    case 'UZS':
                        $uzs = $price;
                        break;
                    case 'USD':
                        $usd = $price;
                        break;
                    case 'EUR':
                        $eur = $price;
                        break;
                    case 'RUB':
                        $rub = $price;
                        break;
                }
            }
        }

        return [$uzs, $usd, $eur, $rub];
    }

    private function getComponenentsWithCost($part)
    {
        extract(Helpers::currencyRates());
        $components = $part->components;
        $data = [];
        if ($components) {
            foreach ($components as $comp) {
                $componentsCost = $this->calcComponetsCost($comp->subPart);
                if ($comp->subPart->state == Part::STATE_RAW) {
                    $componentsCost = array_map(function ($item) use ($comp) {
                        return $item * $comp->usage_qty;
                    }, $componentsCost);
                }
                [$uzs, $usd, $eur, $rub] = $componentsCost;
                if ($uzs == 0 and $usd == 0 and $eur == 0 and $rub == 0) {
                    $tuzs = 'N/A';
                } else {
                    $tuzs = ($uzs + ($usd * $rateUSD) + ($eur * $rateEUR) + ($rub * $rateRUB)) / 1000;
                }
                $data[] = [
                    'comp' => [
                        'id' => $comp->subPart->id,
                        'partinfo' => $comp->subPart->partinfo,
                        'part_name' => $comp->subPart->part_name,
                        'state_code' => $comp->subPart->state,
                        'state' => $comp->subPart->stateText,
                        'usage_qty' => $comp->usage_qty,
                        'unit' => $comp->subPart->unit->unit_value
                    ],
                    'uzs' => $uzs,
                    'usd' => $usd,
                    'eur' => $eur,
                    'rub' => $rub,
                    'tuzs' => $tuzs
                ];
            }
        }
        // $totaluzs = 0;
        //   $totalusd = 0;
        //   $totaleur = 0;
        //   $totalrub = 0;
        //   $totaltuzs = 0;
        // foreach ($data as $row) {
        //   $totaluzs += $row['uzs'] ;
        //   $totalusd += $row['usd'] ;
        //   $totaleur += $row['eur'] ;
        //   $totalrub += $row['rub'] ;
        //   $totaltuzs += $row['tuzs'] ;
        // }
        //   echo $totaluzs.'<br>';
        //   echo $totalusd.'<br>';
        //   echo $totaleur.'<br>';
        //   echo $totalrub.'<br>';
        //   echo $totaltuzs.'<br>';
        //   die;
        return $data;
    }

    public function productionCountPivot($from, $to)
    {
        $_from = strtotime($from);
        $_to = strtotime($to);
        $fact = ProductionOrder::LABEL_ACTUAL;
        if ($from > $to) {
            $_from = strtotime($to);
            $_to = strtotime($from);
        }
        $query = "SELECT p.part_no, p.part_name, p.state, w.name as whname, dsh, sum(quantity) as total FROM (
      SELECT 
        part_id, 
          quantity, 
        CASE 
              WHEN from_unixtime(created_at,'%H') BETWEEN 8 AND 19 THEN concat(from_unixtime(created_at,'%Y-%m-%d'),' ',1)
              WHEN from_unixtime(created_at,'%H') < 8 THEN concat(DATE_SUB(from_unixtime(created_at,'%Y-%m-%d'), INTERVAL 1 day),' ',2)
              WHEN from_unixtime(created_at,'%H') > 19 THEN concat(from_unixtime(created_at,'%Y-%m-%d'),' ',2)
          ELSE 2 END as dsh
      FROM `production_order`
      WHERE is_label=$fact AND created_at>=$_from AND created_at<=$_to
      ) T INNER JOIN part p ON p.id=T.part_id LEFT JOIN warehouse w ON p.warehouse_id=w.id
      GROUP BY dsh, p.part_no, p.part_name, p.state, w.name";
        $data = Yii::$app->db->createCommand($query)->queryAll();
        $dates = [];
        $parts = [];
        foreach ($data as $row) {
            $parts[$row['part_no']] = [
                $row['part_no'],
                $row['part_name'],
                $row['part_state'],
                $row['whname']
            ];
            $dates[$row['dsh']] = 0;
        }
        array_unique($parts);
        array_unique($dates, SORT_STRING);
        $i = 0;
        $dateList = [];
        foreach ($dates as $k => $v) {
            $dates[$k] = 6 + $i;
            $i++;
            $dateList[] = 0;
        }
        $matrix = [];
        $partList = [];
        $i = 0;
        foreach ($parts as $k => $v) {
            $partList[$k] = $i;
            $i++;
            $matrix[] = array_merge($v, $dateList);
        }
        foreach ($data as $row) {
            $row_ind = $partList[$row['part_no']];
            $col_ind = $dateList[$row['dsh']];
            $dateList[$row_ind][$col_ind] = $row['total'];
        }

        return $matrix;
    }

    public function spDetailed($reqSpId, $reqSupplierId)
    {
        $spds = ShipmentPerformanceDetail::find()
            ->joinWith(['shipmentPerformance', 'part'])
            ->where(['shipment_performance.id' => $reqSpId])
            ->all();
        //->createCommand()->rawSql;
        $parts = [];
        foreach ($spds as $spd) {
            $contractDetail = $spd->part->actualContract;
            $contract = $contractDetail->contract ?? '';
            $supplier = $contractDetail->contract->supplier ?? '';
            $partSupplierId = $contract->supplier_id ?? '';
            $partSupplierName = $supplier->name ?? '';
            $partContractSubject = $contract->contractSubject->name ?? '';
            if ($reqSupplierId != $partSupplierId) {
                continue;
            }
            // Agar postavshik UZ bo'lsa hisobga olmaymiz
            if (($supplier->countryCode ?? '') == 'UZ') {
                continue;
            }
            $price = $contractDetail->price ?? 0;
            $currency = $contractDetail->contract->currency->code ?? '';
            extract(Helpers::currencyRates());
            $priceUSD = 0;
            switch ($currency) {
                case 'EUR':
                    $priceUSD = $price * $rateEUR / $rateUSD;
                    break;
                case 'RUB':
                    $priceUSD = $price * $rateRUB / $rateUSD;
                    break;
                case 'UZS':
                    $priceUSD = $price / $rateUSD;
                    break;
                case 'USD':
                    $priceUSD = $price;
                    break;
            }
            $packSize = null;
            $toShip = $spd->less_doh_qty;
            if (count(ArrayHelper::map($spd->part->partPackings, 'id', 'pack_qty')) != 0) {
                $packSize = min(ArrayHelper::map($spd->part->partPackings, 'id', 'pack_qty'));
                if ($packSize) {
                    $toShip = ceil($spd->less_doh_qty / $packSize) * $packSize;
                }
            }
            $parts[] = [
                'part_no' => $spd->part->partinfo,
                'part_name' => $spd->part->part_name,
                'doh' => $spd->doh,
                'shortage' => $spd->less_doh_qty,
                'to_ship' => $toShip,
                'shipped' => $spd->shipped_qty,
                'balance' => $toShip - $spd->shipped_qty,
                'percent' => $spd->shipped_qty * 100 / $toShip,
                'to_ship_amount' => $toShip * $priceUSD,
                'shipped_amount' => $spd->shipped_qty * $priceUSD,
                'balance_amount' => ($toShip - $spd->shipped_qty) * $priceUSD,
                'over_doh_amount' => $spd->over_doh_qty * $priceUSD,
            ];
        }
        $data['parts'] = $parts;
        $supplier = Supplier::findOne($reqSupplierId);
        if ($supplier) {
            $data['header'] = $supplier->name . ' ' . $supplier->countryCode->name;
        }

        return compact('data');
    }

    public function sp($reqSpId = null)
    {
        $spds = ShipmentPerformanceDetail::find()
            ->joinWith(['shipmentPerformance', 'part'])
            ->where(['>=', 'shipment_performance.report_date', date("Y-01-01")])
            //->andWhere(['<=','shipment_performance.report_date' , date('Y-m-d', strtotime('sunday last week'))])
            ->andFilterWhere(['shipment_performance.id' => $reqSpId])
            ->orderBy(['shipment_performance.report_date' => SORT_DESC])
            ->all();
        //->createCommand()->rawSql;
        $data1 = [];
        foreach ($spds as $spd) {
            $contractDetail = $spd->part->actualContract;
            $contract = $contractDetail->contract ?? '';
            $supplier = $contractDetail->contract->supplier ?? '';
            $partSupplierId = $contract->supplier_id ?? '';
            $partSupplierName = $supplier->name ?? '';
            $partContractSubject = $contract->contractSubject->name ?? '';
            // Agar postavshik UZ bo'lsa hisobga olmaymiz
            if (($supplier->countryCode ?? '') == 'UZ') {
                continue;
            }
            $price = $contractDetail->price ?? 0;
            $currency = $contractDetail->contract->currency->code ?? '';
            extract(Helpers::currencyRates());
            $priceUSD = 0;
            switch ($currency) {
                case 'EUR':
                    $priceUSD = $price * $rateEUR / $rateUSD;
                    break;
                case 'RUB':
                    $priceUSD = $price * $rateRUB / $rateUSD;
                    break;
                case 'UZS':
                    $priceUSD = $price / $rateUSD;
                    break;
                case 'USD':
                    $priceUSD = $price;
                    break;
            }
            $key = $spd->shipmentPerformance->id . '|' . $spd->shipmentPerformance->calendarWeek . '|';
            if ($reqSpId) {
                $key .= $partSupplierId . '|' . $partSupplierName . '|' . $partContractSubject;
            }
            $tmpArr1 = [
                'key' => $key,
                'sp_id' => $spd->shipmentPerformance->id,
                'cw' => $spd->shipmentPerformance->calendarWeek,
                'less_doh_amount' => $spd->less_doh_qty * $priceUSD,
                'shipped_amount' => $spd->shipped_qty * $priceUSD,
                'over_doh_amount' => $spd->over_doh_qty * $priceUSD,
                'status' => $spd->status,
            ];
            if ($reqSpId) {
                $tmpArr1['supplier_id'] = $partSupplierId;
                $tmpArr1['supplier_name'] = $partSupplierName;
                $tmpArr1['contract_subject'] = $partContractSubject;
            };
            $data1[] = $tmpArr1;
        }
        $keys = [];
        foreach ($data1 as $row) {
            $keys[] = $row['key'];
        }
        $keys = array_unique($keys);
        $data = [];
        foreach ($keys as $key) {
            if ($reqSpId) {
                [$spId, $cw, $partSupplierId, $partSupplierName, $partContractSubject] = explode('|', $key);
            } else {
                [$spId, $cw] = explode('|', $key);
            }
            $ok = 0;
            $over = 0;
            $under = 0;
            $notShipped = 0;
            $okAmount = 0;
            $overAmount = 0;
            $underAmount = 0;
            $notShippedAmount = 0;
            $overDohAmount = 0;
            foreach ($data1 as $row) {
                $itsOk = false;
                if ($reqSpId) {
                    if (
                        $spId == $row['sp_id'] and
                        $cw == $row['cw'] and
                        $partSupplierId == $row['supplier_id'] and
                        $partSupplierName == $row['supplier_name'] and
                        $partContractSubject == $row['contract_subject']
                    ) {
                        $itsOk = true;
                    }
                } else {
                    if (
                        $spId == $row['sp_id'] and
                        $cw == $row['cw']
                    ) {
                        $itsOk = true;
                    }
                }
                if ($itsOk) {
                    switch ($row['status']) {
                        case ShipmentPerformanceDetail::STATUS_OK:
                            $ok++;
                            $okAmount += $row['shipped_amount'];
                            break;
                        case ShipmentPerformanceDetail::STATUS_OVER:
                            $over++;
                            $overAmount += ($row['shipped_amount'] - $row['less_doh_amount']);
                            break;
                        case ShipmentPerformanceDetail::STATUS_UNDER:
                            $under++;
                            $underAmount += ($row['less_doh_amount'] - $row['shipped_amount']);
                            break;
                        case ShipmentPerformanceDetail::STATUS_NOT_SHIPPED:
                            $notShipped++;
                            $notShippedAmount += $row['less_doh_amount'];
                            break;
                    }
                    $overDohAmount += $row['over_doh_amount'];
                }
            }
            $all = $ok + $over + $under + $notShipped;
            $shipped = $ok + $over + $under;
            $allAmount = $okAmount + $overAmount + $underAmount + $notShippedAmount;
            $shippedAmount = $okAmount + $overAmount + $underAmount;
            $tmpArr = [
                'sp_id' => $spId,
                'cw' => $cw,
                'all' => $all,
                'shipped' => $shipped,
                'ok' => $ok,
                'over' => $over,
                'under' => $under,
                'not_shipped' => $notShipped,
                'all_amount' => $allAmount,
                'shipped_amount' => $shippedAmount,
                'ok_amount' => $okAmount,
                'over_amount' => $overAmount,
                'under_amount' => $underAmount,
                'not_shipped_amount' => $notShippedAmount,
                'over_doh_amount' => $overDohAmount,
            ];
            if ($reqSpId) {
                $tmpArr['supplier_id'] = $partSupplierId;
                $tmpArr['supplier_name'] = $partSupplierName;
                $tmpArr['contract_subject'] = $partContractSubject;
            };
            $data[] = $tmpArr;
        }

        return compact('data');
    }

    public function pfepParts($filter)
    {
        $query = 'SELECT 
                pt.part_no,
                pt.part_name,
                cd.cnfea,
                pt.part_color,
                s.name as supplier,
                pp.pack_qty,
                pp.piece_weight,
                p1.code, 
                p1.construction,	
                p1.thickness,	
                p1.weight,	
                p1.length,	
                p1.width,	
                p1.height,
                p2.code as code1, 
                p2.construction as construction1,	
                p2.thickness as thickness1,	
                p2.weight as weight1,	
                p2.length as length1,	
                p2.width as width1,	
                p2.height as height1,
                lev.quantity as pack_level_quantity,
                w.name as warehouse,
                lms.mpr,
                lms.dloc,
                lms.minimum,
                lms.maximum,
                lms.stack
              FROM part_packing pp
              INNER JOIN pack p1 ON pp.pack_id=p1.id
              INNER JOIN part pt ON pp.part_id=pt.id
              LEFT JOIN pack_level lev ON pp.part_id=lev.part_id AND pp.pack_id=lev.pack_id
              LEFT JOIN pack p2 ON lev.in_pack_id=p2.id
              LEFT JOIN lms ON pp.part_id=lms.part_id AND pp.supplier_id=lms.supplier_id
              LEFT JOIN warehouse w ON lms.warehouse_id=w.id
              LEFT JOIN contract_detail cd ON pt.actual_contract_detail_id=cd.id
              LEFT JOIN supplier s ON pp.supplier_id=s.id';
        $ids = isset(Yii::$app->params[$filter]) ? Yii::$app->params[$filter] : [];
        if (count($ids) > 0) {
            $query .= ' WHERE pt.contract_source_id IN (' . implode(',', $ids) . ')';
        }
        $data = Yii::$app->db->createCommand($query)->queryAll();

        return compact('data');
    }

    public function ccu()
    {
        $monthes = Helpers::getLast12Monthes();

        rsort($monthes);

        $data = [];
        foreach ($monthes as $month) {
            $ccumData = $this->getCcum($month);
            if (!$ccumData) {
                continue;
            }
            $cnt = 0;
            $contCost = 0;
            $truckCost = 0;
            $airCost = 0;
            $localCost = 0;
            $totalCbm = 0;
            $totalWeight = 0;
            $totalLost = 0;
            foreach ($ccumData as $cont) {
                $cnt++;
                if ($cont['ship_mode'] == ShipMode::name(ShipMode::CONTAINER)) {
                    $contCost += $cont['trans_cost'];
                }

                if ($cont['ship_mode'] == ShipMode::name(ShipMode::TRUCK)) {
                    $truckCost += $cont['trans_cost'];
                }

                if ($cont['ship_mode'] == ShipMode::name(ShipMode::AIR)) {
                    $airCost += $cont['trans_cost'];
                }

                $totalCbm += $cont['cu_cbm'];
                $totalWeight += $cont['cu_weight'];
                $totalLost += $cont['trans_lost'];
            }

            $groupedData = [
                'total_cost' => $contCost + $truckCost + $airCost + $localCost,
                'container_cost' => $contCost,
                'truck_cost' => $truckCost,
                'air_cost' => $airCost,
                'local_cost' => 0, // keyin hisoblanadi
                'cu_cbm_avg' => ($cnt > 0) ? $totalCbm / $cnt : 0,
                'cu_weight_avg' => ($cnt > 0) ? $totalWeight / $cnt : 0,
                'trans_lost_monthly_avg' => ($cnt > 0) ? $totalLost / $cnt : 0,
            ];

            $data[] = [
                'month' => $month,
                'data' =>  $groupedData,
                //'orig_data' =>  $this->getCcum($month)
            ];
        }

        return $data;
    }

    public function ccum($month)
    {
        return $this->getCcum($month);
    }

    private function getCcum($month)
    {
        $from = date("Y-m-01", strtotime($month));
        $to = date("Y-m-t", strtotime($month));

        $detail = FreightInvoiceDetail::find()
            ->joinWith(['freightInvoice'])
            ->where([
                'and',
                ['freight_invoice.invoice_type' => FreightInvoiceType::FREIGHT_TYPE_OUTBOUND],
                ['between', 'freight_invoice.invoice_date', $from, $to]
            ])
            //->createCommand()->rawSql;
            ->all();

        $data = [];

        foreach ($detail as $r) {

            // CointainerInvoice dan ma'lumotlarni olish

            [$shipMode, $delTerm, $cbm, $grossWeight] = $r->getContInvData();

            // ***

            $containerType = $r->container->container_type ?? '';

            [$capacity, $load, $cuCBM, $cuWeight] = $r->calcCu($containerType, $cbm, $grossWeight);

            $transCost = $r->getCostInCurrency('USD') ?? 0;
            $transCostInbound = ($r->inboundInvoiceDetail) ? $r->inboundInvoiceDetail->getCostInCurrency('USD') ?? 0 : 0;
            $totalCost  = $transCost + $transCostInbound;
            $data[] = [
                'fr_inv_det_id' => $r->id,
                'carrier' => $r->freightInvoice->carrier->company_name ?? '',
                'container' => $r->container->container_no ?? '',
                'container_type' => ContainerType::name($containerType) ?? '',
                'ship_mode' => $shipMode,
                'del_term' => $delTerm,
                'capacity' => ContainerType::$capacity[$containerType] ?? 0,
                'cbm' => $cbm,
                'cu_cbm' => $cuCBM,
                'cu_weight' => $cuWeight,
                'trans_cost' => $totalCost,
                'trans_lost' => ($capacity > 0) ? $totalCost * ($capacity - $cbm) / $capacity : 0,
            ];
        }

        return $data;
    }

    public function ccuc($detail_id)
    {
        $detail = FreightInvoiceDetail::findOne($detail_id);

        [$shipMode, $delTerm, $cbm, $grossWeight, $suppliers, $cargoType, $shippingDate, $stationDate, $arriveDate] = $detail->getContInvData();
        $containerType = $detail->container->container_type ?? '';
        [$capacity, $load, $cuCBM, $cuWeight] = $detail->calcCu($containerType, $cbm, $grossWeight);



        $detCosts = $detail->freightInvoiceDetailCosts;
        $costsOutbound = [];
        foreach ($detCosts as $cost) {
            $costsOutbound[$cost->cost_type] = [
                'value' => $cost->value,
                'comment' => $cost->comment,
            ];
        }

        $detCostsIn = ($detail->inboundInvoiceDetail) ? $detail->inboundInvoiceDetail->freightInvoiceDetailCosts : [];
        $costsInbound = [];
        foreach ($detCostsIn as $cost) {
            $costsInbound[$cost->cost_type] = [
                'value' => $cost->value,
                'comment' => $cost->comment,
            ];
        }

        $paymentTypes = FreightInvoicePaymentType::list();

        $outCosts = [];
        $outCostsTotal = 0;
        foreach ($paymentTypes as $type => $name) {
            $cost = $costsOutbound[$type] ?? null;
            $outCosts[] = [
                'type' => $type,
                'name' => $name,
                'value' => $cost['value'] ?? 0,
                'comment' => $cost['comment'] ?? '',
            ];
            $outCostsTotal += $cost['value'] ?? 0;
        }

        $inCosts = [];
        $inCostsTotal = 0;
        foreach ($paymentTypes as $type => $name) {
            $cost = $costsInbound[$type] ?? null;
            $inCosts[] = [
                'type' => $type,
                'name' => $name,
                'value' => $cost['value'] ?? 0,
                'comment' => $cost['comment'] ?? '',
            ];
            $inCostsTotal += $cost['value'] ?? 0;
        }

        $outCurrency = $detail->freightInvoice->currency->code ?? '';
        $inCurrency = $detail->inboundInvoiceDetail->freightInvoice->currency->code ?? '';

        $transCost = $detail->summCost() ?? 0;
        $transCostInbound = ($detail->inboundInvoiceDetail) ? $detail->inboundInvoiceDetail->summCost() ?? 0 : 0;
        //$totalTransCost = $transCost + $transCostInbound;

        $data = [
            'month' =>  date("Y-m", strtotime($detail->freightInvoice->invoice_date)) ?? '',
            'invoice_no' => $detail->freightInvoice->invoice_no ?? '',
            'carrier' => $detail->freightInvoice->carrier->company_name ?? '',
            'container' => $detail->container->container_no ?? '',
            'suppliers' => implode(',', array_unique($suppliers)),
            'type' =>  ContainerType::name($containerType) ?? '',
            'ship_mode' => $shipMode,
            'del_term' => $delTerm,

            'cu_cbm' => $cuCBM,
            'cu_weight' => $cuWeight,
            'cbm' => $cbm,
            'weight' => $grossWeight,
            'trans_lost' => ($capacity > 0) ? $transCost * ($capacity - $cbm) / $capacity : 0,

            'out_route' => $detail->freightInvoice->route->name ?? '',
            'out_currency' => $outCurrency,
            'out_trans_cost' => $transCost,

            'in_invoice_no' => $detail->inboundInvoiceDetail->freightInvoice->invoice_no ?? '',
            'in_route' => $detail->inboundInvoiceDetail->freightInvoice->route->name ?? '',
            'in_currency' => $inCurrency,
            'in_trans_cost' => $transCostInbound,

            'cargo_type' => $cargoType,
            'point_of_dep' => $detail->freightInvoice->route->fromPoint->name ?? '',
            'shipping_date' => $shippingDate,
            'dest_station' => $detail->freightInvoice->route->toPoint->name ?? '',
            'station_date' => $stationDate,
            'dif_station_shipping' => ($stationDate and $shippingDate) ? Helpers::dateDifference($stationDate, $shippingDate) : '',
            'arrive_date' => $arriveDate,
            'dif_arrive_shipping' => ($arriveDate and $shippingDate) ? Helpers::dateDifference($arriveDate, $shippingDate) : '',

            'out_costs' => $outCosts,
            'out_costs_total' => $outCostsTotal,

            'in_costs' => $inCosts,
            'in_costs_total' => $inCostsTotal



        ];

        return $data;
    }

    public function dohCalc($isFormatted = true)
    {
        $parts = Part::find()
            ->where([
                'contract_source_id' => Yii::$app->params['import_contract_source_ids'],
                'status' => Part::STATUS_ACTIVE
            ])
            ->all();

        foreach ($parts as $part) {
            $actualContractDetail = $part->actualContractDetail;
            $contract = $actualContractDetail->contract ?? '';
            $supplier = $contract->supplier ?? '';
            $supplierName = $supplier->name ?? '';
            $countryName = $supplier->countryCode->alpha_2 ?? '';
            $price =  $actualContractDetail->price ?? 0;
            $currency = $contract->currency->code ?? '';
            $contractSubject = $contract->contractSubject->name ?? '';
            $transitTime = $supplier->transit_time ?? 0;

            $averageUsage = $part->averageUsage;

            $mfu = $part->mfu;
            $bank = $mfu->bank ?? 0;
            // $transitTime = $mfu->transit_time ?? 0;

            $moq = $mfu->moq ?? 0;
            $moqDays = ($averageUsage != 0) ? ceil($moq / $averageUsage) : 0;

            // total with MOQ
            if( (($bank + $transitTime) * $averageUsage) < $moq){
             $totalDohQtyWithMOQ = $moq;
            }else{
             $totalDohQtyWithMOQ = ($moq != 0) ? ceil((($bank + $transitTime) * $averageUsage / $moq)) * $moq : 0;
            }
            $totalDohDaysWithMOQ = ($averageUsage != 0) ? floor($totalDohQtyWithMOQ / $averageUsage) : 0;
            $totalDohAmountWithMOQ = $totalDohQtyWithMOQ * $price;

            // total without MOQ

            $totalDohQtyWithoutMOQ = ($bank + $transitTime) * $averageUsage;
            $totalDohDaysWithoutMOQ = $totalDohDaysWithMOQ;
            $totalDohAmountWithoutMOQ = $totalDohQtyWithoutMOQ * $price;


            $data[] = [
                'part_no' => $part->part_no,
                'part_name' => $part->part_name,
                'supplier' => $supplierName,
                'contract_subject' => $contractSubject,
                'country' => $countryName,
                'uom' => $part->unit->unit_value,
                'price' => $this->formatNumber($price, 2, $isFormatted),
                'currency' => $currency,
                'average_usage' => $this->formatNumber($averageUsage, 0, $isFormatted),
                'bank' => $this->formatNumber($bank, 0, $isFormatted),
                'transit_time' => $this->formatNumber($transitTime, 0, $isFormatted),
                'moq_pieces' => $this->formatNumber($moq, 2, $isFormatted),
                'moq_days' => $this->formatNumber($moqDays, 0, $isFormatted),

                'total_doh_days_with_moq' => $this->formatNumber($totalDohDaysWithMOQ, 0, $isFormatted),
                'total_doh_qty_with_moq' => $this->formatNumber($totalDohQtyWithMOQ, 2, $isFormatted),
                'total_doh_amount_with_moq' => $this->formatNumber($totalDohAmountWithMOQ, 2, $isFormatted),

                'total_doh_days_without_moq' => $this->formatNumber($totalDohDaysWithoutMOQ, 0, $isFormatted),
                'total_doh_qty_without_moq' => $this->formatNumber($totalDohQtyWithoutMOQ, 2, $isFormatted),
                'total_doh_amount_without_moq' => $this->formatNumber($totalDohAmountWithoutMOQ, 2, $isFormatted),

                'diff_amounts' => $this->formatNumber(($totalDohAmountWithoutMOQ - $totalDohAmountWithMOQ),2 , $isFormatted)
            ];
        }

        return $data;
    }

    private function formatNumber($value, $dec = 0, $isFormatted){

        return ($isFormatted) ? Helpers::numberFormatRemoveZero($value, $dec) : $value;
    }

    public function cashRequirement()
    {
        
        $dummyOrders = PaymentControl::find()
        ->with(['supplier.countryCode','contract.paymentTerm','contract.currency'])
        ->where([
            'and',
            ['dummy_order' => 1],
            ['>=', 'date', date('Y-m-d')]
        ])->all();

        $arrPlan = [];    
        $keys = [];    
        foreach($dummyOrders as $dummyOrder){
            $objSupplier = $dummyOrder->supplier ?? '';
            $objContract = $dummyOrder->contract ?? '';
            $country = $objSupplier->countryCode->alpha_2 ?? '';
            $supplier = $objSupplier->name ?? '';
            if(empty($paymentTerm)){
                $paymentTerm = $objContract->paymentTerm->name ?? '';
            }
            $paymentDate = $dummyOrder->date ?? null;
            $currency = $objContract->currency->code ?? '';
            $paymentAmount = $dummyOrder->amount ?? 0;
            $convertedPaymentAmount = Helpers::convertCurrency($paymentAmount, $currency, 'UZS', $paymentDate) / 1000000;
            
            $arrPlan[] = [
                'country' => $country,
                'supplier' => $supplier,
                'paymentTerm' => $paymentTerm,
                'paymentDate' => $paymentDate,
                'convertedPaymentAmount' => $convertedPaymentAmount,
                'paymentAmount' => $paymentAmount,
                'currency' => $currency
            ];

            $keys[] = $country . '|' . $supplier . '|' . $paymentTerm . '|' . $currency;
        }
        $keys = array_unique($keys);
        
        $this->groupedPlan = [];
        foreach($keys as $key){
            [$country, $supplier, $paymentTerm, $currency] = explode('|', $key);
            foreach($arrPlan as $plan){
                if(
                    $plan['country'] == $country and 
                    $plan['supplier'] == $supplier and 
                    $plan['paymentTerm'] == $paymentTerm and 
                    $plan['currency'] == $currency
                ){
                    $oldValue = $this->groupedPlan[$key][$plan['paymentDate']] ?? 0;
                    $this->groupedPlan[$key][$plan['paymentDate']] = $oldValue + $plan['convertedPaymentAmount'];
                }
            }
        }

        
        $periodWeekly = Helpers::getPeriodWeek6Month();
        $periodDaily = [];
        foreach (Helpers::getPeriodFull() as $pdate) {
            if($pdate > date('Y-m-t', strtotime('+6 month'))) break;
            $periodDaily[] = $pdate;
        }
        $data = [];
        foreach($keys as $key){
            $row = [];
            [$country, $supplier, $paymentTerm, $currency] = explode('|', $key);
            $row['country'] = $country;
            $row['supplier'] = $supplier;
            $row['paymentTerm'] = $paymentTerm;
            $row['currency'] = $currency;

            foreach($periodDaily as $pdate){
                $row['daily'][$pdate] = $this->groupedPlan[$key][$pdate] ?? 0;
            }
            
            foreach($periodWeekly as $pweek){
                $row['weekly'][$pweek['plandate']] = $this->getPeriodPlan($key,$pweek['from'], $pweek['to']);
            }
            $data[] = $row;

        }
       return $data;
    }

    private function getPeriodPlan($key, $from, $to){
        $amount = 0;
        foreach($this->groupedPlan[$key] as $paymentDate => $paymentAmount){
            if($paymentDate >= $from and $paymentDate <= $to){
                $amount += $paymentAmount;
            }    
        }
        return $amount;
    }

    public function imos()
    {
        $data = [];
        
        // getting coverage data
        $coverage = ArrayHelper::map(Req::find()->where(['type' => CoverageController::TYPE_STOCK])->all(), 'part_id','totalStock') ;
       
        // getting order data
        $openOrders = [];
        $ordersQtyInMonth = [];
        $ordersInvQtyInMonth = [];
        $orderItems = PartOrderDetail::find()->all();
        foreach($orderItems as $oItem){
            $balance = $oItem->qty - $oItem->invoiceQty;
            $oldValueOpenOrders = $openOrders[$oItem->part_id] ?? 0;
            if($balance > 0) $openOrders[$oItem->part_id] = $oldValueOpenOrders + $balance;
            if($oItem->partOrder->for_month){
                if(!empty($oItem->qty))
                    $oldValueOrder = $ordersQtyInMonth[$oItem->partOrder->for_month][$oItem->part_id] ?? 0;
                    $ordersQtyInMonth[$oItem->partOrder->for_month][$oItem->part_id] = $oldValueOrder + $oItem->qty;   
                if(!empty($oItem->invoiceQty))
                    $oldValueInv = $ordersInvQtyInMonth[$oItem->partOrder->for_month][$oItem->part_id] ?? 0;
                    $ordersInvQtyInMonth[$oItem->partOrder->for_month][$oItem->part_id] = $oldValueInv + $oItem->invoiceQty;   
            }
        }

        // getting requirement data
        $reqDetail = ReqDetailPlan::find()
        ->joinWith('req')
        ->where([
            'req.type' => CoverageController::TYPE_DAILY
        ])->all();

        
        
        $periodDaily = [];
        foreach (Helpers::getPeriodFull() as $pdate) {
            if($pdate > date('Y-m-t', strtotime('+6 month'))) break;
            $periodDaily[] = $pdate;
        }

        $req = [];
        foreach($reqDetail as $reqd){
            foreach($periodDaily as $key => $pdate){
                $col = $key + 1;
                $month = substr($pdate,0,7);
                $partId = $reqd->req->part_id;
                $oldVal  = $req[$month][$partId] ?? 0;
                $req[$month][$partId] = $oldVal + $reqd->{'col'.$col};
                
                
            }
        }
        
        $parts = Part::find()
        ->with([
            'contractSource',
            'productModel',
            'unit',
            'actualContractDetail.contract.supplier.countryCode',
            'actualContractDetail.contract.currency',
            'mfu'
        ])
        ->where([
			'status' => Part::STATUS_ACTIVE,
			'state' => Part::STATE_RAW,
			'contract_source_id' => Yii::$app->params['import_contract_source_ids']
        ])->all();
        
        $months = PartOrder::getMonths(7, false);
              
        foreach($parts as $part){

            $cbal = $coverage[$part->id] ?? 0;
            $openOrder = $openOrders[$part->id] ?? 0;
            $tmpArr = [
                'part_no' => $part->part_no,
                'part_color' => $part->part_color,
                'part_name' => $part->part_name,
                'contract_source' => $part->contractSource->name,
                'model' => $part->productModel->modelname ?? '',
                'part_type' => $part->partType->typename ?? '',
                'uom' => $part->unit->unit_value,
                'supplier' => $part->actualContractDetail->contract->supplier->name ?? '',
                'country' => $part->actualContractDetail->contract->supplier->countryCode->alpha_2 ?? '',
                'lead_time' => $part->actualContractDetail->lead_time ?? '',
                'currency' => $part->actualContractDetail->contract->currency->code ?? '',
                'price' => $part->actualContractDetail->price ?? '',
                'moq' => $part->mfu->moq ?? 0,
                'cbal' => $cbal,
                'open_orders' => $openOrder,

            ];

            $monthsData = [];
            foreach($months as $key => $month){
                
                $reqQty = $req[$month][$part->id] ?? 0;
                $orderQty = $ordersQtyInMonth[$month][$part->id] ?? 0;
                $invQty =  $ordersInvQtyInMonth[$month][$part->id] ?? 0;

                if($key === 0){
                    // Joriy oy stock hisoblanishi
                    $stock = $cbal + $openOrder + $orderQty - $reqQty;
                    $stockWithoutOrder =  $cbal + $openOrder - $reqQty;
                }else{
                    // Keyingi oylar stock hisoblanishi
                    $stock = $monthsData[$key-1]['stock'] + $orderQty - $reqQty;
                    $stockWithoutOrder = $monthsData[$key-1]['stock'] - $reqQty;
                }

                $monthsData[$key] = [
                    'stock' => $stock,
                    'stock_without_order' => $stockWithoutOrder,
                    'req_qty' => $reqQty,
                    'order_qty' => $orderQty,
                    'inv_qty' => $invQty,
                    'open_order' => $orderQty - $invQty,
                ];
            }

            $tmpArr['months'] = $monthsData;
            $data[] = $tmpArr; 
        }
        
        return compact('data', 'months');
    }

    public function sumImos(){  

        $dohCalc = $this->dohCalc(false);

        // echo "<pre>";
        // print_r($dohCalc);
        // echo "</pre>";
        // die;

        $uniqueKeys = [];
        foreach($dohCalc as $row){
            $uniqueKeys[] = $row['supplier'] . '|' . $row['contract_subject'] . '|' . $row['country'] . '|' . $row['transit_time'];
        }
        $uniqueKeys = array_unique($uniqueKeys);

        foreach($uniqueKeys as $key){
            [$supplier, $contractSubject, $country, $transitTime] = explode('|',$key);
            $totalDohAmountWithMoq = 0;
            $totalDohAmountWithoutMoq = 0;
            $totalAmountAverageUsage = 0;
            foreach($dohCalc as $row){
                if(
                    $row['supplier'] == $supplier and 
                    $row['contract_subject'] == $contractSubject and 
                    $row['country'] == $country and 
                    $row['transit_time'] == $transitTime
                ){
                    $totalDohAmountWithMoq += $row['total_doh_amount_with_moq'];
                    $totalDohAmountWithoutMoq += $row['total_doh_amount_without_moq'];
                    $totalAmountAverageUsage += $row['average_usage'] * $row['price'];

                }
            }

            $totalDohDiff = $totalDohAmountWithMoq - $totalDohAmountWithoutMoq;  
            $totalDohDays = ($totalAmountAverageUsage != 0) ? ($totalDohAmountWithMoq / $totalAmountAverageUsage) : 0;
            $bankStock = $totalDohDays - $transitTime ?? 0;

            $sumDohCalc[] = compact(
                'supplier',
                'contractSubject',
                'country',
                'bankStock',
                'transitTime',
                'totalDohDays',
                'totalDohAmountWithMoq',
                'totalDohAmountWithoutMoq',
                'totalDohDiff'
            );
        }


        // echo "<pre>";
        // print_r($sumDohCalc);
        // echo "</pre>";
        // die;
        $data = $sumDohCalc;
        return compact('data');

    }

    public function productionMonitor($fromDate, $toDate, $needWarehouseId = null) {
      $planned = LineStopReason::TYPE_PLANNED;
      $notPlanned = LineStopReason::TYPE_NOTPLANNED;
      $queryPlanned =
        "SELECT part_production_monitor_id, SUM(elapsed_minutes) as total FROM line_stop ls 
         INNER JOIN line_stop_reason lsr ON ls.line_stop_reason_id=lsr.id AND lsr.type=$planned
         GROUP BY part_production_monitor_id";

      $queryNotPlanned =
        "SELECT part_production_monitor_id, SUM(elapsed_minutes) as total FROM line_stop ls 
         INNER JOIN line_stop_reason lsr ON ls.line_stop_reason_id=lsr.id AND lsr.type=$notPlanned
         GROUP BY part_production_monitor_id";

      $query =
        "SELECT 
            w.name as warehouse_name, 
            p.part_color, 
            p.part_no, 
            p.part_name,
            pm.production_date,
            pm.shift,
            ppm.actual_production_time,
            IFNULL(Tp.total, 0) as planned, 
            IFNULL(Tnp.total, 0) as not_planned,
            ppm.produced_qty,
            ppm.repaired_qty,
            ppm.broken_qty 
          FROM part_production_monitor ppm 
         INNER JOIN production_monitor pm ON ppm.production_monitor_id = pm.id
         INNER JOIN warehouse w ON pm.warehouse_id = w.id
         INNER JOIN part p ON ppm.part_id = p.id
         LEFT JOIN ($queryPlanned) Tp ON ppm.id=Tp.part_production_monitor_id
         LEFT JOIN ($queryNotPlanned) Tnp ON ppm.id=Tnp.part_production_monitor_id
         ";
      $condition = " WHERE pm.production_date>='$fromDate' AND pm.production_date<='$toDate'";

      if ($needWarehouseId!==null) {
        $condition .= " AND pm.warehouse_id=$needWarehouseId";
      }

      $query .= $condition;
      return Yii::$app->db->createCommand($query)->queryAll();
    }

    public function salesSummary()
    {
        $year = date('Y');
        $query = "
            select
                c.customer_type_id rtype, SUBSTRING(w.waybill_date,1,7) rmonth, cu.code currency, sum(fid.qty) rqty, sum(fid.qty * fid.price) ramt  
            from 
                waybill w , fg_invoice_waybill fiw , fg_invoice fi , fg_invoice_detail fid , customer c, sales_contract sc, currency cu 
            where
                SUBSTRING(w.waybill_date,1,4) = :year and
                w.id = fiw.waybill_id and
                fiw.fg_invoice_id = fi.id and
                fid.fg_invoice_id = fi.id and
                fi.customer_id = c.id and
                sc.contract_no = fi.contract and
                cu.id = sc.currency_id 
            group by 
                c.customer_type_id, SUBSTRING(w.waybill_date,1,7), cu.code
        ";
        $result = Yii::$app->db->createCommand($query,[':year' => $year])->queryAll();

        $domexp = [1,2];
        $currencies = [];
        $months = [];
        foreach ($result as $item) {
            $months[] = $item['rmonth'];
            $currencies[] = $item['currency'];
        }

        $months = array_unique($months);
        $currencies = array_unique($currencies);
        
        $data = [];
        foreach ($domexp as $type) {
            foreach ($currencies as $curr) {
                foreach ($months as $mon) {
                    foreach ($result as $res) {
                        if($res['rtype'] == $type and $res['currency'] == $curr and $res['rmonth'] == $mon){
                            $oldAmt = $data[$type][$curr][$mon] ?? 0;
                            $data[$type][$curr][$mon] = $oldAmt + $res['ramt'];
                            $oldQty = $data[$type]['qty'][$mon] ?? 0;
                            $data[$type]['qty'][$mon] = $oldQty + $res['rqty'];
                        }
                    }
                }
            }    
        }

        foreach ($currencies as $curr) {
            foreach ($months as $mon) {
                foreach ($result as $res) {
                    if($res['currency'] == $curr and $res['rmonth'] == $mon){
                        $oldAmt = $data['domexp'][$curr][$mon] ?? 0;
                        $data['domexp'][$curr][$mon] = $oldAmt + $res['ramt'];
                        $oldQty = $data['domexp']['qty'][$mon] ?? 0;
                        $data['domexp']['qty'][$mon] = $oldQty + $res['rqty'];
                    }
                }
            }
        }

        $monthList = $this->getMonthList($year, 'short');

        $dataByPartType = $this->salesSummaryByPartType();
        $types = $dataByPartType['types'];
        $specTypes = $dataByPartType['specTypes'];
        $dataSecond = $dataByPartType['data'];

        return compact('data', 'monthList', 'year', 'specTypes',  'types', 'dataSecond');
    }

    public function salesSummaryDomestic()
    {
        $year = date('Y');
        $query = "
        select
            concat(c.name, '|', c.id) customer_name, SUBSTRING(w.waybill_date,1,7) rmonth, sum(fid.qty) rqty, sum(fid.qty * fid.price) ramt  
        from 
            waybill w , fg_invoice_waybill fiw , fg_invoice fi , fg_invoice_detail fid , customer c
        where
            SUBSTRING(w.waybill_date,1,4) = :year and
            c.customer_type_id = 1 and
            w.id = fiw.waybill_id and
            fiw.fg_invoice_id = fi.id and
            fid.fg_invoice_id = fi.id and
            fi.customer_id = c.id
        group by 
            concat(c.name, '|', c.id), SUBSTRING(w.waybill_date,1,7)
        ";
        $result = Yii::$app->db->createCommand($query,[':year' => $year])->queryAll();

        $customers = [];
        $months = [];
        foreach ($result as $item) {
            $months[] = $item['rmonth'];
            $customers[] = $item['customer_name'];
        }

        $months = array_unique($months);
        $customers = array_unique($customers);
        
        $data = [];
        foreach ($customers as $cust) {
            foreach ($months as $mon) {
                foreach ($result as $res) {
                    if($res['customer_name'] == $cust and $res['rmonth'] == $mon){
                        $oldAmt = $data[$cust][$mon]['amount'] ?? 0;
                        $data[$cust][$mon]['amount'] = $oldAmt + $res['ramt'];
                        $oldQty = $data[$cust][$mon]['qty'] ?? 0;
                        $data[$cust][$mon]['qty'] = $oldQty + $res['rqty'];
                    }
                }
            }
        }

        $monthList = $this->getMonthList($year);

        return compact('data', 'monthList', 'customers', 'year');
    }

    public function salesSummaryByPartType($customer_id = null)
    {
        $year = date('Y');
        $params[':year'] = $year;
        $filterCustomer = '';
        $customerName = '';
        if($customer_id){
            $params[':customer_id'] = $customer_id; 
            $filterCustomer = " fi.customer_id = :customer_id and ";  
            $customerName = Customer::findOne($customer_id)->name ?? 'N/A';  
        }
        $query = "
            select
                pt.typename, SUBSTRING(w.waybill_date,1,7) rmonth, cu.code currency, 
                sum(fid.qty) rqty, sum(fid.qty * fid.price) ramt  
            from 
                waybill w , fg_invoice_waybill fiw , fg_invoice fi , fg_invoice_detail fid , sales_contract sc, currency cu , part p , part_type pt 
            where
                ".$filterCustomer."
                SUBSTRING(w.waybill_date,1,4) = :year and
                w.id = fiw.waybill_id and
                fiw.fg_invoice_id = fi.id and
                fid.fg_invoice_id = fi.id and
                sc.contract_no = fi.contract and
                cu.id = sc.currency_id and
                fid.part_no = p.part_no and
                p.part_type_id = pt.id 
            group by 
                pt.typename, SUBSTRING(w.waybill_date,1,7), cu.code
        ";
        $result = Yii::$app->db->createCommand($query,$params)->queryAll();

        $specTypes = ['ABS','PP','PA','PC','SAN'];
        $types = [];
        $currencies = [];
        $months = [];
        foreach ($result as $key => $item) {
            if(!in_array($item['typename'],$specTypes)){
                $result[$key]['typename'] = 'others';
            }
            $types[] = $result[$key]['typename'];
            $currencies[] = $item['currency'];
            $months[] = $item['rmonth'];
            
        }

        $types = array_unique($types);
        $currencies = array_unique($currencies);
        $months = array_unique($months);

        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        // // die;
        
        $data = [];
        foreach ($types as $type) {
            foreach ($currencies as $curr) {
                foreach ($months as $mon) {
                    foreach ($result as $res) {
                        if ($res['typename'] == $type and $res['currency'] == $curr and $res['rmonth'] == $mon) {
                            $oldAmt = $data[$type][$curr][$mon] ?? 0;
                            $data[$type][$curr][$mon] = $oldAmt + $res['ramt'];
                            $oldQty = $data[$type]['qty'][$mon] ?? 0;
                            $data[$type]['qty'][$mon] = $oldQty + $res['rqty'];
                        }
                    }
                }
            }    
        }    

        // Total
        foreach ($currencies as $curr) {
            foreach ($months as $mon) {
                foreach ($result as $res) {
                    if ($res['currency'] == $curr and $res['rmonth'] == $mon) {
                        $oldAmt = $data['total'][$curr][$mon] ?? 0;
                        $data['total'][$curr][$mon] = $oldAmt + $res['ramt'];
                        $oldQty = $data['total']['qty'][$mon] ?? 0;
                        $data['total']['qty'][$mon] = $oldQty + $res['rqty'];
                    }
                }
            }
        }    
            
        
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // die;

        $monthList = $this->getMonthList($year);

        return compact('data', 'monthList', 'year', 'specTypes', 'types', 'customerName');
    }

    public function salesSummaryExport()
    {
        $year = date('Y');
        $params[':year'] = $year;
        $query = "
        select
            c.name customer_name, pt.typename , SUBSTRING(w.waybill_date,1,7) rmonth, cu.code currency, 
            sum(fid.qty) rqty, sum(fid.qty * fid.price) ramt  
        from 
            waybill w , fg_invoice_waybill fiw , fg_invoice fi , fg_invoice_detail fid , sales_contract sc, currency cu , part p , part_type pt , customer c
        where
            c.customer_type_id = 2 and
            SUBSTRING(w.waybill_date,1,4) = :year and
            w.id = fiw.waybill_id and
            fiw.fg_invoice_id = fi.id and
            fid.fg_invoice_id = fi.id and
            sc.contract_no = fi.contract and
            cu.id = sc.currency_id and
            fid.part_no = p.part_no and
            p.part_type_id = pt.id and
            fi.customer_id = c.id 
        group by 
            c.name, pt.typename, SUBSTRING(w.waybill_date,1,7), cu.code
        ";
        
        $result = Yii::$app->db->createCommand($query,$params)->queryAll();
        
        $specTypes = ['ABS','PP','PA','PC','SAN'];
        $cusromers = [];
        $types = [];
        $currencies = [];
        $months = [];
        foreach ($result as $key => $item) {
            if(!in_array($item['typename'],$specTypes)){
                $result[$key]['typename'] = 'others';
            }
            $types[] = $result[$key]['typename'];
            $customers[] = $item['customer_name'];
            $currencies[] = $item['currency'];
            $months[] = $item['rmonth'];
            
        }

        $customers = array_unique($customers);
        $types = array_unique($types);
        $currencies = array_unique($currencies);
        $months = array_unique($months);
        
        $data = [];
        foreach ($customers as $customer) {
            foreach ($types as $type) {
                foreach ($currencies as $curr) {
                    foreach ($months as $mon) {
                        foreach ($result as $res) {
                            if ($res['customer_name'] == $customer and $res['typename'] == $type and $res['currency'] == $curr and $res['rmonth'] == $mon) {
                                $oldAmt = $data[$customer][$type][$curr][$mon] ?? 0;
                                $data[$customer][$type][$curr][$mon] = $oldAmt + $res['ramt'];
                                $oldQty = $data[$customer][$type]['qty'][$mon] ?? 0;
                                $data[$customer][$type]['qty'][$mon] = $oldQty + $res['rqty'];
                            }
                        }
                    }
                }    
            }    
        }    

        // Total
        foreach ($currencies as $curr) {
            foreach ($months as $mon) {
                foreach ($result as $res) {
                    if ($res['currency'] == $curr and $res['rmonth'] == $mon) {
                        $oldAmt = $data['total'][$curr][$mon] ?? 0;
                        $data['total'][$curr][$mon] = $oldAmt + $res['ramt'];
                        $oldQty = $data['total']['qty'][$mon] ?? 0;
                        $data['total']['qty'][$mon] = $oldQty + $res['rqty'];
                    }
                }
            }
        }  

        $monthList = $this->getMonthList($year);

        return compact('data', 'monthList', 'year', 'specTypes', 'types', 'customers');
    }

    private function getMonthList($year,$type = 'short', $quarter = false){
        
        return [
            $year . '-01|1' => ($type == 'short') ? Yii::t('app', 'jan') : Yii::t('app', 'January'),
            $year . '-02|1' => ($type == 'short') ? Yii::t('app', 'feb') : Yii::t('app', 'February'),
            $year . '-03|1' => ($type == 'short') ? Yii::t('app', 'mar') : Yii::t('app', 'March'),
            $year . '-04|2' => ($type == 'short') ? Yii::t('app', 'apr') : Yii::t('app', 'April'),
            $year . '-05|2' => ($type == 'short') ? Yii::t('app', 'may') : Yii::t('app', 'May'),
            $year . '-06|2' => ($type == 'short') ? Yii::t('app', 'jun') : Yii::t('app', 'June'),
            $year . '-07|3' => ($type == 'short') ? Yii::t('app', 'jul') : Yii::t('app', 'July'),
            $year . '-08|3' => ($type == 'short') ? Yii::t('app', 'avg') : Yii::t('app', 'August'),
            $year . '-09|3' => ($type == 'short') ? Yii::t('app', 'sep') : Yii::t('app', 'September'),
            $year . '-10|4' => ($type == 'short') ? Yii::t('app', 'oct') : Yii::t('app', 'October'),
            $year . '-11|4' => ($type == 'short') ? Yii::t('app', 'nov') : Yii::t('app', 'November'),
            $year . '-12|4' => ($type == 'short') ? Yii::t('app', 'dec') : Yii::t('app', 'December'),
        ];
    }

    public function salesImplementationPlan()
    {
        $year = date('Y');
        $query = "
        select 
            c.name customer_name, concat(p.part_no,'?', p.part_name ,'?', p.part_color) partinfo, SUBSTRING(sp.target_date ,1,7) rmonth, sum(sp .target_qty) qty
        from 
            sales_plan sp, customer c , part p
        where
            sp.customer_id  = c.id and
            sp.part_id = p.id 	and
            SUBSTRING(sp.target_date,1,4) = :year
        group by 
            c.name, concat(p.part_no,'|', p.part_name ,'|', p.part_color), SUBSTRING(sp.target_date ,1,7)
        ";

        $result = Yii::$app->db->createCommand($query,[':year' => $year])->queryAll();

        $customers = [];
        $parts = [];
        $months = [];
        foreach ($result as $item) {
            $customers[] = $item['customer_name'];
            $parts[] = $item['partinfo'];
            $months[] = $item['rmonth'];
        }

        $customers = array_unique($customers);
        $parts = array_unique($parts);
        $months = array_unique($months);

        $data = [];
        foreach ($customers as $cust) {
            foreach ($parts as $part) {
                foreach ($months as $mon) {
                    foreach ($result as $res) {
                        if($res['customer_name'] == $cust and $res['partinfo'] == $part and $res['rmonth'] == $mon){
                            $oldQty = $data[$cust][$part][$mon]['qty'] ?? 0;
                            $data[$cust][$part][$mon]['qty'] = $oldQty + $res['qty'];
                        }
                    }
                }
            }
        }

        foreach ($months as $mon) {
            foreach ($result as $res) {
                if($res['rmonth'] == $mon){
                    $oldQty = $data['total'][$mon]['qty'] ?? 0;
                    $data['total'][$mon]['qty'] = $oldQty + $res['qty'];
                }
            }
        }

        $monthList = $this->getMonthList($year,'short');

        return compact('data', 'monthList','year');
    }

    public function salesImplementationPlanFact()
    {
        $year = '2022';
        $query = "
        select customer_name, partinfo, rmonth, sum(plan_qty) plan_qty, sum(fact_qty) fact_qty from 
        (
            select 
                c.name customer_name, concat(p.part_no,'?', p.part_name ,'?', p.part_color) partinfo, SUBSTRING(sp.target_date ,1,7) rmonth, sum(sp .target_qty) plan_qty, 0 fact_qty
            from 
                sales_plan sp, customer c , part p
            where
                sp.customer_id  = c.id and
                sp.part_id = p.id 	and
                SUBSTRING(sp.target_date,1,4) = :year
            group by 
                c.name, concat(p.part_no,'|', p.part_name ,'|', p.part_color), SUBSTRING(sp.target_date ,1,7)
            
            union    
                
            select
                c.name customer_name, concat(p.part_no,'?', p.part_name ,'?', p.part_color) partinfo,  SUBSTRING(w.waybill_date,1,7) rmonth,  0 plan_qty, sum(fid.qty) fact_qty  
            from 
                waybill w , fg_invoice_waybill fiw , fg_invoice fi , fg_invoice_detail fid , customer c, part p 
            where
                SUBSTRING(w.waybill_date,1,4) = :year and
                w.id = fiw.waybill_id and
                fiw.fg_invoice_id = fi.id and
                fid.fg_invoice_id = fi.id and
                fi.customer_id = c.id and 
                p.part_no = fid.part_no 
            group by 
                c.name, concat(p.part_no,'?', p.part_name ,'?', p.part_color), SUBSTRING(w.waybill_date,1,7)
        ) a
        group by 
            customer_name, partinfo, rmonth
        ";
        $result = Yii::$app->db->createCommand($query,[':year' => $year])->queryAll();

        $customers = [];
        $parts = [];
        $months = [];
        foreach ($result as $item) {
            $customers[] = $item['customer_name'];
            $parts[] = $item['partinfo'];
            $months[] = $item['rmonth'];
        }

        $customers = array_unique($customers);
        $parts = array_unique($parts);
        $months = array_unique($months);

        $data = [];
        foreach ($customers as $cust) {
            foreach ($parts as $part) {
                foreach ($months as $mon) {
                    foreach ($result as $res) {
                        if($res['customer_name'] == $cust and $res['partinfo'] == $part and $res['rmonth'] == $mon){
                            $oldPlanQty = $data[$cust][$part][$mon]['plan_qty'] ?? 0;
                            $data[$cust][$part][$mon]['plan_qty'] = $oldPlanQty + $res['plan_qty'];
                            $oldFactQty = $data[$cust][$part][$mon]['fact_qty'] ?? 0;
                            $data[$cust][$part][$mon]['fact_qty'] = $oldFactQty + $res['fact_qty'];
                        }
                    }
                }
            }
        }

        foreach ($months as $mon) {
            foreach ($result as $res) {
                if($res['rmonth'] == $mon){
                    $oldPlanQty = $data['total'][$mon]['plan_qty'] ?? 0;
                    $data['total'][$mon]['plan_qty'] = $oldPlanQty + $res['plan_qty'];
                    $oldFactQty = $data['total'][$mon]['fact_qty'] ?? 0;
                    $data['total'][$mon]['fact_qty'] = $oldFactQty + $res['fact_qty'];
                }
            }
        }

        $monthList = $this->getMonthList($year, 'long');
        $month = date("n");
        $q = ceil(date("n") / 3);

        return compact('data', 'monthList','year','q');
    }

    

    public function salesPaymentStatus()
    {
        $query = "
        select 
            customer, max(inv_date) inv_date, sum(inv_amt) inv_amt, max(pay_date) pay_date, sum(pay_amt) pay_amt
        from 
        (
            select 
                concat(c.name , '|', rc.customer_id) customer, '' inv_date, 0 inv_amt, min(rc.date) pay_date, sum(fir.amount) pay_amt
            from 
                recept_control rc, fg_invoice_receipt fir, customer c
            where 
                rc.id = fir.recept_control_id  and	
                c.id = rc.customer_id 
            group by 
                concat(c.name , '|', rc.customer_id)
            union	
            select 
                concat(c.name , '|', fi.customer_id) customer, min(fi.invoice_date) inv_date, sum(fid.price*fid.qty) inv_amt, '' payd_date, 0 pay_amt 
            from 
                fg_invoice fi, fg_invoice_detail fid, customer c 
            where 
                fi.id = fid.fg_invoice_id and	
                c.id = fi.customer_id 
            group by 
                concat(c.name ,'|',fi.customer_id)
        ) a
        group by customer
        ";
        $data = Yii::$app->db->createCommand($query,[])->queryAll();

        return compact('data');
    }
    
    public function salesPaymentStatusCustomer($customer_id)
    {
        $query = "
        select a.*, b.waybill_no from
        (
            select 
                fi.id invoice_id, invoice_no, invoice_date, contract, sum(qty * price) amt
            from
                fg_invoice fi, fg_invoice_detail fid
            where 
                fi.id = fid.fg_invoice_id and
                fi.customer_id = :customer_id
            group by 
                fi.id, invoice_no, invoice_date, contract	 
        ) a
        left join
        (
            select fiw.fg_invoice_id, w.waybill_no from waybill w, fg_invoice_waybill fiw where w.id = fiw.waybill_id 
        ) b
        on a.invoice_id = b.fg_invoice_id
        ";
        $debit = Yii::$app->db->createCommand($query,[':customer_id' => $customer_id])->queryAll();

        $query = "
        select 
            no,fir.amount  pay_amt, rc.date  pay_date, sc.contract_no , rc.amount, (rc.amount - fir.amount) amt_diff
        from 
            recept_control rc, fg_invoice_receipt fir, customer c, sales_contract sc 
        where 
            rc.id = fir.recept_control_id  and	
            c.id = rc.customer_id and
            sc.id = rc.sales_contract_id and
            rc.customer_id  = :customer_id
        ";
        $credit = Yii::$app->db->createCommand($query,[':customer_id' => $customer_id])->queryAll();
        $customerName = Customer::findOne($customer_id)->name;
        return compact('debit','credit','customerName');
    }
    public static function salesPaymentInfo($customer_id, $year)
    {
        $sum = ReceptControl::find()->where(['customer_id'=>$customer_id])->andWhere(['DATE_FORMAT(date, \'%Y\')'=>$year])->sum('amount');
        return $sum;
    }
    public function materialStock($state)
    {
        $stocks = $this->getStock($state);

        $types = [];
        foreach ($stocks as $stock) {
            $types[] = $stock['type_name'].'|'.$stock['type_id'];
        }
        
        $types = array_unique($types);

        foreach ($types as $type) {
            $qty = 0; $amt = 0;
            list($typeName, $typeId) = explode('|', $type);
            foreach ($stocks as $stock) {
                if($typeName == $stock['type_name'] and $typeId == $stock['type_id']){
                    $qty +=  $stock['qty'];
                    $amt +=  $stock['amt'];
                }
            }
            $data[] = [
                'type_id' => $typeId,
                'type_name' => $typeName,
                'qty' => $qty,
                'amt' => $amt
            ];
        }

        return compact('data');
    }

    public function materialStockByType($state, $type)
    {
        $data = $this->getStock($state, $type);

        return compact('data');
    }

    private function getStock($state, $type = null)
    {
        $typeCondition = ($type) ? $typeCondition = ['part.part_type_id' => $type] : [];

        $stocks = Stock::find()
        ->with(['part.actualContractDetail.contract.currency', 'part.partType'])
        ->joinWith(['part'])
        ->where(['part.state' => $state] + $typeCondition)
        ->all();

        $data = [];
        foreach($stocks as $stock){
           
            $tmp = [];
            $tmp['type_name'] = ($stock->part->partType) ? $stock->part->partType->typename : '';
            $tmp['type_id'] = ($stock->part->partType) ? $stock->part->part_type_id : '';
            $tmp['no'] = $stock->part->part_no;
            $tmp['name'] = $stock->part->part_name;
            $tmp['color'] = $stock->part->part_color;
            $tmp['qty'] = $stock->qty + 0;


            // get prices
            $priceUZS = 0;
            extract(Helpers::currencyRates());
            if($state == Part::STATE_RAW){
                $contractDetail = $stock->part->actualContractDetail;
                $contract = $contractDetail->contract ?? '';
                $price = $contractDetail->price ?? 0;
                $currency = $contract->currency->code ?? '';
                switch ($currency) {
                    case 'USD':
                        $priceUZS = $price * $rateUSD;
                        break;
                    case 'EUR':
                        $priceUZS = $price * $rateEUR;
                        break;
                    case 'RUB':
                        $priceUZS = $price * $rateRUB;
                        break;
                    case 'UZS':
                        $priceUZS = $price;
                        break;
                }
            }elseif($state == Part::STATE_FINISHED){

                $componentsCost = $this->calcComponetsCost($stock->part);
                [$uzs, $usd, $eur, $rub] = $componentsCost;

                if ($uzs == 0 and $usd == 0 and $eur == 0 and $rub == 0) {
                    $priceUZS = 0;
                } else {
                    $priceUZS = $uzs + ($usd * $rateUSD) + ($eur * $rateEUR) + ($rub * $rateRUB);
                }

            }

            $tmp['amt'] = ($stock->qty * $priceUZS) / 1000000; 
            $data[] = $tmp;
        }

        return $data;
    }
    public static function queryContracts($year, $contract_factory=null)
    {
        
        $query = "SELECT distinct(contract) FROM `fg_invoice`";
        $inv = Yii::$app->db->createCommand($query)->queryAll();
        $contracts = [];
        foreach ($inv as $row) {
        $contracts[$row['contract']]=[];
        $query2 = "SELECT id, customer_id, invoice_no FROM `fg_invoice` where contract = '".$row['contract']."'";
        $inv2 = Yii::$app->db->createCommand($query2)->queryAll();
        foreach($inv2 as $row2){
            $contracts[$row['contract']]['invoice_no'][] = $row2['invoice_no'];
            $query3 = "SELECT waybill_id FROM `fg_invoice_waybill` where fg_invoice_id = '".$row2['id']."'";
            $inv3 = Yii::$app->db->createCommand($query3)->queryAll();
            foreach($inv3 as $row3){
                $query4 = "SELECT waybill_no, factory_id FROM `waybill` where  DATE_FORMAT(waybill_date,  '%Y')='".$year."' and  id = '".$row3['waybill_id']."'";
                if(!empty($contract_factory)){
                    $query4 .= " and waybill_no = '".$contract_factory."'";
                }
                $inv4 = Yii::$app->db->createCommand($query4)->queryAll();
                if(!empty($inv4)){
                    $contracts[$row['contract']]['waybill_ids'][] = $row3['waybill_id'];
                    $contracts[$row['contract']]['waybill_no'][] = $inv4[0]['waybill_no'];
                    $contracts[$row['contract']]['customer'] = Customer::findOne($row2['customer_id'])->name;
                }
            }
        }
        }
        return $contracts;
    }
  
    // contractdagi fakturalar tablitsasi
    public static function queryFactorys($contract, $year)
    {
        
        $result = [];

        $detailsTable = FgInvoiceDetail::tableName();
        $partsTable = Part::tableName();
        $unitsTable = Unit::tableName();
        $waybillsTable = Waybill::tableName();
        $modelsTable = ProductModel::tableName();
        $waybill_ids =  [];
        $query2 = "SELECT id, customer_id, invoice_no FROM `fg_invoice` where contract = '".$contract."'";
        $inv2 = Yii::$app->db->createCommand($query2)->queryAll();
        foreach($inv2 as $row2){
            $contracts[$row['contract']]['invoice_no'][] = $row2['invoice_no'];
            $query3 = "SELECT waybill_id FROM `fg_invoice_waybill` where fg_invoice_id = '".$row2['id']."'";
            $inv3 = Yii::$app->db->createCommand($query3)->queryAll();
            foreach($inv3 as $row3){
                $waybill_ids[] = $row3['waybill_id'];
            }
        }
        if(!empty($waybill_ids)){
            foreach($waybill_ids as $key => $item){
              $model = Waybill::find()->with([
                'fgInvoiceWaybills.fgInvoice.customer', 'factory', 'createdBy', 'updatedBy' => function ($query) {
                  $query->from(['u2' => User::tableName()]);
                }
              ])->where(['id' => $item])->andWhere(['DATE_FORMAT(waybill_date,  \'%Y\')' => $year])->one();
              if ($model === null) {
                return null;
              }
              $pivotData = [];
              foreach ($model->fgInvoiceWaybills as $pivot) {
                $model->invoices[] = $pivot->fg_invoice_id;
                $pivotData[] = $pivot->id;
              }
              // show fg invoice details
            
              $details[$key] = (new Query())->select(["$detailsTable.part_name", "$partsTable.part_color", "$unitsTable.unit_value", "$detailsTable.price", "SUM(qty) as total_qty"])
                ->from($detailsTable)
                ->leftJoin($partsTable, "$partsTable.part_no = $detailsTable.part_no")
                ->leftJoin($unitsTable, "$unitsTable.id = $detailsTable.unit_id")
                ->groupBy(["$detailsTable.part_name", "$partsTable.part_color", "$unitsTable.unit_value", "$detailsTable.price"])
                ->where(["$detailsTable.fg_invoice_id" => $model->invoices])
                ->all();
              
            }
          }
          
          return $details;
    }

  // customerning barcha fakturalari bo'yicha
  public static function queryCustomerFactory($customer_id, $year)
  {
      $customer = Customer::findOne($customer_id);
      if($customer){
        $details = [];
        $all_amount_with_vat = 0;
        $all_amount = 0;
        $all_qty = 0;
        $all_vat_amount = 0;
        $vat = $firstFgInvoice->vat;
        $contracts = FgInVoice::find()->where(['customer_id'=>$customer_id])->orderBy(['id'=>SORT_ASC])->all();
        if($contracts){
          foreach($contracts as $key => $contract){
            $result = self::queryFactorys($contract->contract, $year);
            if($result){
                foreach($result as $items){
                    if($items){
                        foreach($items as $detail){
                            $unit = $detail['unit_value'];
                            $qty = $detail['total_qty'];
                            $price = $detail['price'];
                            $amount = ($qty*$price);
                            $vat_amount = $amount*$vat/100;
                            $amount_with_vat = ($vat_amount) ? ($amount + $vat_amount) : $amount;
                        
                            $all_amount_with_vat = $all_amount_with_vat + $amount_with_vat;
                        }
                    }
                }
            }
          }
        }
      }
      return $all_amount_with_vat;
  }

  // fakt bo'yicha qilingan otchot update qilindi
	/*
	@@@ Sanakulov Anvar  @@@
	@@@ 2023-07-08   @@@
	Create by 

	*/

	public  function getProductionFakt($date, $line=null, $partId=null, $todayDay, $type=1)
	{
		$condition = '';
        $condition2 = '';
		if(!empty($line)){
			$condition .= " and po.line = $line";
            $condition2 .= " and pp.line = $line";
		}
		if(!empty($partId)){
			$condition .= " and po.part_id = $partId";
            $condition2 .= " and pp.part_id = $partId";
		}
		$query = "SELECT po.line,  p.part_no, p.id as part_id,  p.part_name, p.part_color  FROM production_order po
			inner JOIN part p on p.id = po.part_id
			where po.line is not null  and  
            FROM_UNIXTIME(po.created_at, \"%Y-%m\") = :date
            $condition
            group by po.line, po.part_id

            UNION

            SELECT pp.line,  p.part_no, p.id as part_id,  p.part_name, p.part_color  FROM production_plan pp
            inner JOIN part p on p.id = pp.part_id
            where pp.line is not null  and  
            DATE_FORMAT(pp.production_date, '%Y-%m') = :date
            $condition2
            group by pp.line, pp.part_id
		";
        $countWhere = '';
       
        // query asArray
        $parts = Yii::$app->db->createCommand($query,[':date' => $date])->queryAll();
        if($type == 1){

            $quantityFact = 'SELECT sum(po.quantity) as quantity from production_order po
                where po.line = :line and po.part_id = :part_id and FROM_UNIXTIME(po.created_at, "%Y-%m-%d") = :date
                ';
            $quantityPlan = 'SELECT sum(pp.target_qty) as quantity from production_plan pp
                where pp.line = :line and pp.part_id = :part_id and DATE_FORMAT(pp.production_date, \'%Y-%m-%d\') = :date
                ';
        }
        else{
            $quantityFact = 'SELECT sum(po.quantity) as quantity from production_order po
                where po.line = :line and po.part_id = :part_id and FROM_UNIXTIME(po.created_at, "%Y-%m-%d") = :date 
                and  FROM_UNIXTIME(po.created_at, "%h:%i") between  :interval1 and :interval2
                ';
            $quantityPlan = 'SELECT sum(pp.target_qty) as quantity from production_plan pp
             where pp.line = :line and pp.part_id = :part_id and DATE_FORMAT(pp.production_date, \'%Y-%m-%d\') = :date
             and pp.shift = :shift
            ';
        }

        $monthDaysCount = date('t', strtotime($date));
        if(!empty($parts)){
            foreach($parts as $key => $part){
                for($day = 1; $day <= $monthDaysCount; $day++){
                    $quantityShift1 = 0;
                    $quantityShift2 = 0;
                    if($day < 10){
                        $day = '0'.$day;
                    }
                    $date1  = $date . '-' . $day;
                    if($todayDay < $day){
                        if($type == 2){
                            $quantityShift1 = Yii::$app->db->createCommand($quantityFact,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id'], ':interval1'=>'08:00', ':interval2'=>'19:59'])->queryOne();
                            $quantityShift2 = Yii::$app->db->createCommand($quantityFact,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id'], ':interval1'=>'20:00', ':interval2'=>'07:59'])->queryOne();
                        }
                        else{
                            $quantity = Yii::$app->db->createCommand($quantityFact,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id']])->queryOne();
                        }
                    }
                    else{
                        if($type == 2){
                            $quantityShift1 = Yii::$app->db->createCommand($quantityPlan,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id'], ':shift'=>1])->queryOne();
                            $quantityShift2 = Yii::$app->db->createCommand($quantityPlan,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id'], ':shift'=>2])->queryOne();
                        }
                        else{
                            $quantity = Yii::$app->db->createCommand($quantityPlan,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id']])->queryOne();
                        }
                    }
                    
                    if($type == 2){
                        $parts[$key]['counts'][$day.'-1'] = 1*($quantityShift1['quantity']?:0);
                        $parts[$key]['counts'][$day.'-2'] = 1*($quantityShift2['quantity']?:0);
                        
                    }
                    else{
                        $parts[$key]['counts'][$day] = 1*($quantity['quantity']?:0);
                    }
                }
                $parts[$key]['counts']['total'] = array_sum($parts[$key]['counts']);
            }
        }
        
		return $parts;

	}

    public function getProductionPlan($date=null, $warehouse_id=null, $partId=null)
    {
        $condition = '';
		if(!empty($warehouse_id)){
			$condition .= " and p.warehouse_id = $warehouse_id";
		}
		if(!empty($partId)){
			$condition .= " and po.part_id = $partId";
		}
		$query = "SELECT po.line,  p.part_no, p.id as part_id,  p.part_name, p.part_color, p.warehouse_id  FROM production_order po
			inner JOIN part p on p.id = po.part_id
			where po.line is not null  and  
            FROM_UNIXTIME(po.created_at, \"%Y-%m\") = :date
            $condition
            group by po.line, po.part_id
            
		";
        // query asArray
        $parts = Yii::$app->db->createCommand($query,[':date' => $date])->queryAll();
        $quantityQuery = 'SELECT sum(po.quantity) as quantity from production_order po
            where po.line = :line and po.part_id = :part_id and FROM_UNIXTIME(po.created_at, "%Y-%m-%d") = :date
            ';

        $monthDaysCount = date('t', strtotime($date));
        if(!empty($parts)){
            foreach($parts as $key => $part){
                for($day = 1; $day <= $monthDaysCount; $day++){
                    if($day < 10){
                        $day = '0'.$day;
                    }
                    $date1  = $date . '-' . $day;
                    $quantity = Yii::$app->db->createCommand($quantityQuery,[':date' => $date1, ':line' => $part['line'], ':part_id' => $part['part_id']])->queryOne();
                    $parts[$key]['counts'][$day] = 1*($quantity['quantity']?:0);
                }
                $parts[$key]['counts']['total'] = array_sum($parts[$key]['counts']);
            }
        }
        
		return $parts;
    }
}
