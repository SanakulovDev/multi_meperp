<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ProductionOrder;
use app\models\ProductSpecification;
use app\models\ProductSpecificationItem;
use app\models\Part;

class FactRequirementController extends Controller
{
    public function actionIndex()
    {
        // Get start date from request or default to beginning of current year
        $startDate = Yii::$app->request->get('start_date', date('Y-01-01'));
        $startTimestamp = strtotime($startDate);
        
        // Get filter parameter
        $filter = Yii::$app->request->get('filter');

        // Get production orders with their specifications
        $productionOrders = ProductionOrder::find()
            ->with(['part', 'productSpecification.productSpecificationItems.part.unit'])
            ->where(['is_label' => ProductionOrder::LABEL_ACTUAL])
            ->andWhere(['>=', 'created_at', $startTimestamp])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        // Calculate material requirements
        $materialRequirements = $this->calculateMaterialRequirements($productionOrders, $startDate, $filter);

        return $this->render('index', [
            'materialRequirements' => $materialRequirements['materials'],
            'periods' => $materialRequirements['periods'],
            'productionOrders' => $productionOrders,
            'startDate' => $startDate,
            'filter' => $filter
        ]);
    }

    /**
     * Get week range for a given date
     * @param string $date
     * @param string $startDate
     * @return string
     */
    private function getWeekRange($date, $startDate)
    {
        $startYear = date('Y', strtotime($startDate));
        $startMonth = date('m', strtotime($startDate));
        $startDay = date('d', strtotime($startDate));
        
        // Calculate week number from start date
        $orderDate = strtotime($date);
        $baseDate = strtotime($startDate);
        
        $daysDiff = floor(($orderDate - $baseDate) / (60 * 60 * 24));
        $weekNumber = floor($daysDiff / 7) + 1;
        
        // Calculate week start and end dates
        $weekStart = date('Y-m-d', $baseDate + (($weekNumber - 1) * 7 * 24 * 60 * 60));
        $weekEnd = date('Y-m-d', $baseDate + (($weekNumber - 1) * 7 + 6) * 24 * 60 * 60);
        
        return $weekStart . ' to ' . $weekEnd;
    }

    /**
     * Get month range for a given date
     * @param string $date
     * @return string
     */
    private function getMonthRange($date)
    {
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        
        $monthStart = $year . '-' . $month . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        
        return $monthStart . ' to ' . $monthEnd;
    }

    /**
     * Get year range for a given date
     * @param string $date
     * @return string
     */
    private function getYearRange($date)
    {
        $year = date('Y', strtotime($date));
        return $year . '-01-01 to ' . $year . '-12-31';
    }

    /**
     * Calculate material requirements based on production orders and specifications
     * @param ProductionOrder[] $productionOrders
     * @param string $startDate
     * @param string $filter
     * @return array
     */
    private function calculateMaterialRequirements($productionOrders, $startDate = null, $filter = null)
    {
        if (!$startDate) {
            $startDate = date('Y-01-01');
        }

        $requirements = [];
        $periods = [
            'weekly' => [],
            'monthly' => [],
            'yearly' => []
        ];

        foreach ($productionOrders as $order) {
            // Skip if no specification or invalid order
            if (!$order->productSpecification || !$order->quantity || $order->quantity <= 0) {
                continue;
            }

            $specification = $order->productSpecification;
            // Use specification amount as the base quantity, default to 1 if not set or zero
            $specAmount = ($specification->amount && $specification->amount > 0) ? $specification->amount : 1;
            
            // Calculate multiplier - prevent division by zero
            $multiplier = $order->quantity / $specAmount;

            // Process each specification item (materials needed for the specification)
            foreach ($specification->productSpecificationItems as $item) {
                // Skip if invalid item data
                if (!$item->part || !$item->usage_qty || $item->usage_qty <= 0) {
                    continue;
                }


                $materialId = $item->part_id;
                $materialName = $item->part->part_no . ' - ' . $item->part->part_name;
                
                // Required quantity = usage quantity per spec * multiplier
                $requiredQty = $item->usage_qty * $multiplier;

                // Initialize material entry if not exists
                if (!isset($requirements[$materialId])) {
                    $requirements[$materialId] = [
                        'material_id' => $materialId,
                        'material_name' => $materialName,
                        'part_no' => $item->part->part_no ?: 'N/A',
                        'part_name' => $item->part->part_name ?: 'N/A',
                        'unit' => $item->part->unit ? $item->part->unit->unit_value : '',
                        'total_required' => 0,
                        'specifications' => [],
                        'weekly' => [],
                        'monthly' => [],
                        'yearly' => []
                    ];
                }

                // Track which specification used this material
                $specKey = $specification->id . ' (' . ($specification->code ?: 'No Code') . ')';
                if (!isset($requirements[$materialId]['specifications'][$specKey])) {
                    $requirements[$materialId]['specifications'][$specKey] = [
                        'code' => $specification->code ?: 'N/A',
                        'description' => $specification->description ?: 'N/A',
                        'spec_amount' => $specAmount,
                        'total_usage' => 0
                    ];
                }
                $requirements[$materialId]['specifications'][$specKey]['total_usage'] += $requiredQty;

                // Add to total requirement
                $requirements[$materialId]['total_required'] += $requiredQty;

                // Group by time periods - add error handling for invalid timestamps
                if (!$order->created_at || $order->created_at <= 0) {
                    continue;
                }

                $orderDate = date('Y-m-d', $order->created_at);
                
                // Get period ranges
                $weekRange = $this->getWeekRange($orderDate, $startDate);
                $monthRange = $this->getMonthRange($orderDate);
                $yearRange = $this->getYearRange($orderDate);

                // Weekly requirements
                if (!isset($requirements[$materialId]['weekly'][$weekRange])) {
                    $requirements[$materialId]['weekly'][$weekRange] = [
                        'period' => $weekRange,
                        'quantity' => 0,
                        'orders' => []
                    ];
                }
                $requirements[$materialId]['weekly'][$weekRange]['quantity'] += $requiredQty;
                $requirements[$materialId]['weekly'][$weekRange]['orders'][] = [
                    'order_id' => $order->id,
                    'order_serial' => $order->serial_number ?: 'N/A',
                    'order_date' => $orderDate,
                    'order_quantity' => $order->quantity,
                    'spec_code' => $specification->code ?: 'N/A',
                    'spec_amount' => $specAmount,
                    'multiplier' => round($multiplier, 4),
                    'usage_qty' => $item->usage_qty,
                    'required_qty' => $requiredQty
                ];

                // Monthly requirements
                if (!isset($requirements[$materialId]['monthly'][$monthRange])) {
                    $requirements[$materialId]['monthly'][$monthRange] = [
                        'period' => $monthRange,
                        'quantity' => 0,
                        'orders' => []
                    ];
                }
                $requirements[$materialId]['monthly'][$monthRange]['quantity'] += $requiredQty;
                $requirements[$materialId]['monthly'][$monthRange]['orders'][] = [
                    'order_id' => $order->id,
                    'order_serial' => $order->serial_number ?: 'N/A',
                    'order_date' => $orderDate,
                    'order_quantity' => $order->quantity,
                    'spec_code' => $specification->code ?: 'N/A',
                    'spec_amount' => $specAmount,
                    'multiplier' => round($multiplier, 4),
                    'usage_qty' => $item->usage_qty,
                    'required_qty' => $requiredQty
                ];

                // Yearly requirements
                if (!isset($requirements[$materialId]['yearly'][$yearRange])) {
                    $requirements[$materialId]['yearly'][$yearRange] = [
                        'period' => $yearRange,
                        'quantity' => 0,
                        'orders' => []
                    ];
                }
                $requirements[$materialId]['yearly'][$yearRange]['quantity'] += $requiredQty;
                $requirements[$materialId]['yearly'][$yearRange]['orders'][] = [
                    'order_id' => $order->id,
                    'order_serial' => $order->serial_number ?: 'N/A',
                    'order_date' => $orderDate,
                    'order_quantity' => $order->quantity,
                    'spec_code' => $specification->code ?: 'N/A',
                    'spec_amount' => $specAmount,
                    'multiplier' => round($multiplier, 4),
                    'usage_qty' => $item->usage_qty,
                    'required_qty' => $requiredQty
                ];

                // Track periods for display
                $periods['weekly'][$weekRange] = $weekRange;
                $periods['monthly'][$monthRange] = $monthRange;
                $periods['yearly'][$yearRange] = $yearRange;
            }
        }

        // Sort periods chronologically
        uksort($periods['weekly'], function($a, $b) {
            return strcmp(substr($a, 0, 10), substr($b, 0, 10));
        });
        uksort($periods['monthly'], function($a, $b) {
            return strcmp(substr($a, 0, 10), substr($b, 0, 10));
        });
        uksort($periods['yearly'], function($a, $b) {
            return strcmp(substr($a, 0, 10), substr($b, 0, 10));
        });

        // Sort by material part number
        uasort($requirements, function($a, $b) {
            return strcmp($a['part_no'], $b['part_no']);
        });

        return [
            'materials' => $requirements,
            'periods' => $periods
        ];
    }

    /**
     * Download material requirements as CSV (alternative to Excel)
     */
    public function actionDownload()
    {
        // Get start date from request
        $startDate = Yii::$app->request->get('start_date', date('Y-01-01'));
        $startTimestamp = strtotime($startDate);
        
        // Get filter parameter
        $filter = Yii::$app->request->get('filter');

        // Get production orders with their specifications
        $productionOrders = ProductionOrder::find()
            ->with(['part', 'productSpecification.productSpecificationItems.part.unit'])
            ->where(['is_label' => ProductionOrder::LABEL_ACTUAL])
            ->andWhere(['>=', 'created_at', $startTimestamp])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        // Calculate material requirements
        $result = $this->calculateMaterialRequirements($productionOrders, $startDate, $filter);
        $materialRequirements = $result['materials'];
        $periods = $result['periods'];

        // Prepare filename
        $fileName = Yii::t('app', 'Material Requirements Report') . '-' . $startDate;
        if ($filter) {
            $fileName .= '-filtered';
        }
        $fileName .= '.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Create headers
        $headers = [
            Yii::t('app', 'Part No'),
            Yii::t('app', 'Part Name'),
            Yii::t('app', 'Unit'),
            Yii::t('app', 'Total Required')
        ];

        // Add period headers
        if (!empty($periods['weekly'])) {
            foreach ($periods['weekly'] as $week) {
                $headers[] = Yii::t('app', 'Week Period') . ': ' . $week;
            }
        }

        if (!empty($periods['monthly'])) {
            foreach ($periods['monthly'] as $month) {
                $headers[] = Yii::t('app', 'Month Period') . ': ' . $month;
            }
        }

        if (!empty($periods['yearly'])) {
            foreach ($periods['yearly'] as $year) {
                $headers[] = Yii::t('app', 'Year Period') . ': ' . $year;
            }
        }

        // Write headers
        fputcsv($output, $headers);

        // Write data rows
        foreach ($materialRequirements as $requirement) {
            $row = [
                $requirement['part_no'],
                $requirement['part_name'],
                $requirement['unit'],
                round($requirement['total_required'], 6)
            ];

            // Add weekly data
            if (!empty($periods['weekly'])) {
                foreach ($periods['weekly'] as $week) {
                    $row[] = isset($requirement['weekly'][$week]) 
                        ? round($requirement['weekly'][$week]['quantity'], 6) 
                        : 0;
                }
            }

            // Add monthly data
            if (!empty($periods['monthly'])) {
                foreach ($periods['monthly'] as $month) {
                    $row[] = isset($requirement['monthly'][$month]) 
                        ? round($requirement['monthly'][$month]['quantity'], 6) 
                        : 0;
                }
            }

            // Add yearly data
            if (!empty($periods['yearly'])) {
                foreach ($periods['yearly'] as $year) {
                    $row[] = isset($requirement['yearly'][$year]) 
                        ? round($requirement['yearly'][$year]['quantity'], 6) 
                        : 0;
                }
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    

    /**
     * Download material requirements as Excel using codemix extension
     */
    public function actionDownloadExcelCodemix()
    {
        // Check if ZipArchive is available
        if (!class_exists('ZipArchive')) {
            Yii::$app->session->setFlash('error', 'ZipArchive class not found. PHP zip extension is not installed. Please use CSV download instead.');
            return $this->redirect(['index']);
        }

        // Check if codemix ExcelFile is available
        if (!class_exists('codemix\excelexport\ExcelFile')) {
            Yii::$app->session->setFlash('error', 'ExcelFile extension is not installed. Please use CSV download instead.');
            return $this->redirect(['index']);
        }

        // Set memory limit for large datasets
        ini_set("memory_limit", "-1");

        // Get start date from request
        $startDate = Yii::$app->request->get('start_date', date('Y-01-01'));
        $startTimestamp = strtotime($startDate);
        
        // Get filter parameter
        $filter = Yii::$app->request->get('filter');

        // Get production orders with their specifications
        $productionOrders = ProductionOrder::find()
            ->with(['part', 'productSpecification.productSpecificationItems.part.unit'])
            ->where(['is_label' => ProductionOrder::LABEL_ACTUAL])
            ->andWhere(['>=', 'created_at', $startTimestamp])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        // Calculate material requirements
        $result = $this->calculateMaterialRequirements($productionOrders, $startDate, $filter);
        $materialRequirements = $result['materials'];
        $periods = $result['periods'];

        // Prepare data for Excel
        $arrFile = [];
        
        foreach ($materialRequirements as $requirement) {
            unset($tmpArray);
            $tmpArray['part_no'] = $requirement['part_no'];
            $tmpArray['part_name'] = $requirement['part_name'];
            $tmpArray['unit'] = $requirement['unit'];
            $tmpArray['total_required'] = round($requirement['total_required'], 6);

            // Add weekly data
            if (!empty($periods['weekly'])) {
                foreach ($periods['weekly'] as $week) {
                    $weekKey = "'" . $week . "'";
                    $tmpArray[$weekKey] = isset($requirement['weekly'][$week]) 
                        ? round($requirement['weekly'][$week]['quantity'], 6) 
                        : 0;
                }
            }

            // Add monthly data
            if (!empty($periods['monthly'])) {
                foreach ($periods['monthly'] as $month) {
                    $monthKey = "'" . $month . "'";
                    $tmpArray[$monthKey] = isset($requirement['monthly'][$month]) 
                        ? round($requirement['monthly'][$month]['quantity'], 6) 
                        : 0;
                }
            }

            // Add yearly data
            if (!empty($periods['yearly'])) {
                foreach ($periods['yearly'] as $year) {
                    $yearKey = "'" . $year . "'";
                    $tmpArray[$yearKey] = isset($requirement['yearly'][$year]) 
                        ? round($requirement['yearly'][$year]['quantity'], 6) 
                        : 0;
                }
            }

            $arrFile[] = $tmpArray;
        }

        // Create header titles
        $header_titles = [
            0 => Yii::t('app', 'Part No'),
            1 => Yii::t('app', 'Part Name'),
            2 => Yii::t('app', 'Unit'),
            3 => Yii::t('app', 'Total Required'),
        ];

        // Create detail titles for periods
        $detail_titles = [];
        $i = 3;

        // Add weekly period titles
        if (!empty($periods['weekly'])) {
            foreach ($periods['weekly'] as $week) {
                $detail_titles[$i + 1] = Yii::t('app', 'Week') . ': ' . $week;
                $i++;
            }
        }

        // Add monthly period titles
        if (!empty($periods['monthly'])) {
            foreach ($periods['monthly'] as $month) {
                $detail_titles[$i + 1] = Yii::t('app', 'Month') . ': ' . $month;
                $i++;
            }
        }

        // Add yearly period titles
        if (!empty($periods['yearly'])) {
            foreach ($periods['yearly'] as $year) {
                $detail_titles[$i + 1] = Yii::t('app', 'Year') . ': ' . $year;
                $i++;
            }
        }

        // Merge all titles
        $titles = array_merge($header_titles, $detail_titles);

        // Create filename
        $fileName = 'material-requirements-' . $startDate;
        if ($filter) {
            $fileName .= '-filtered';
        }

        // Create Excel file
        $file = Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'material-requirements' => [
                    'data' => $arrFile,
                    'titles' => $titles,
                ],
            ],
        ]);

        $file->send(\app\components\Helpers::downloadFileName($fileName));
    }

    /**
     * Download Excel for active tab period only
     */
    public function actionDownloadExcel()
    {
        // Check if ZipArchive is available
        if (!class_exists('ZipArchive')) {
            Yii::$app->session->setFlash('error', 'ZipArchive класси топилмади. PHP zip extension ўрнатилмаган. Илтимос, админ билан боғланинг.');
            // return $this->redirect(['index']);
        }

        // Check if codemix ExcelFile is available
        if (!class_exists('codemix\excelexport\ExcelFile')) {
            Yii::$app->session->setFlash('error', 'ExcelFile кенгайтмаси ўрнатилмаган. Илтимос, админ билан боғланинг.');
            return $this->redirect(['index']);
        }

        ini_set("memory_limit", "-1");

        // Get start date from request
        $startDate = Yii::$app->request->get('start_date', date('Y-01-01'));
        $startTimestamp = strtotime($startDate);
        
        // Get filter parameter
        $filter = Yii::$app->request->get('filter');

        // Get period parameter: weekly | monthly | yearly
        $period = Yii::$app->request->get('period', 'weekly');
        if (!in_array($period, ['weekly', 'monthly', 'yearly'], true)) {
            $period = 'weekly';
        }

        // Get production orders with their specifications
        $productionOrders = ProductionOrder::find()
            ->with(['part', 'productSpecification.productSpecificationItems.part.unit'])
            ->where(['is_label' => ProductionOrder::LABEL_ACTUAL])
            ->andWhere(['>=', 'created_at', $startTimestamp])
            ->orderBy(['created_at' => SORT_ASC])
            ->all();
®
        // Calculate material requirements
        $result = $this->calculateMaterialRequirements($productionOrders, $startDate, $filter);
        $materialRequirements = $result['materials'];
        $periods = $result['periods'];

        // Prepare data for Excel only for selected period
        $arrFile = [];
        foreach ($materialRequirements as $requirement) {
            unset($tmpArray);
            $tmpArray['part_no'] = $requirement['part_no'];
            $tmpArray['part_name'] = $requirement['part_name'];
            $tmpArray['unit'] = $requirement['unit'];

            $hasNonZero = false;
            if ($period === 'weekly' && !empty($periods['weekly'])) {
                foreach ($periods['weekly'] as $week) {
                    $weekKey = "'" . $week . "'";
                    $qty = isset($requirement['weekly'][$week])
                        ? round($requirement['weekly'][$week]['quantity'], 6)
                        : 0;
                    $tmpArray[$weekKey] = $qty;
                    if ($qty > 0) {
                        $hasNonZero = true;
                    }
                }
            } elseif ($period === 'monthly' && !empty($periods['monthly'])) {
                foreach ($periods['monthly'] as $month) {
                    $monthKey = "'" . $month . "'";
                    $qty = isset($requirement['monthly'][$month])
                        ? round($requirement['monthly'][$month]['quantity'], 6)
                        : 0;
                    $tmpArray[$monthKey] = $qty;
                    if ($qty > 0) {
                        $hasNonZero = true;
                    }
                }
            } elseif ($period === 'yearly' && !empty($periods['yearly'])) {
                foreach ($periods['yearly'] as $year) {
                    $yearKey = "'" . $year . "'";
                    $qty = isset($requirement['yearly'][$year])
                        ? round($requirement['yearly'][$year]['quantity'], 6)
                        : 0;
                    $tmpArray[$yearKey] = $qty;
                    if ($qty > 0) {
                        $hasNonZero = true;
                    }
                }
            }

            // Filter: skip if all period values are 0 when filter is active
            if ($filter && !$hasNonZero) {
                continue;
            }

            $tmpArray['total_required'] = round($requirement['total_required'], 6);
            $arrFile[] = $tmpArray;
        }

        // Create header titles - order must match data array order
        $titles = [
            'part_no' => Yii::t('app', 'Детал рақами'),
            'part_name' => Yii::t('app', 'Детал номи'),
            'unit' => Yii::t('app', 'Ҳисоб бирлиги'),
        ];

        // Add period column titles (must come before total_required to match data order)
        if ($period === 'weekly' && !empty($periods['weekly'])) {
            foreach ($periods['weekly'] as $week) {
                $weekKey = "'" . $week . "'";
                $titles[$weekKey] = Yii::t('app', 'Ҳафта') . ': ' . $week;
            }
        } elseif ($period === 'monthly' && !empty($periods['monthly'])) {
            foreach ($periods['monthly'] as $month) {
                $monthKey = "'" . $month . "'";
                $titles[$monthKey] = Yii::t('app', 'Ой') . ': ' . $month;
            }
        } elseif ($period === 'yearly' && !empty($periods['yearly'])) {
            foreach ($periods['yearly'] as $year) {
                $yearKey = "'" . $year . "'";
                $titles[$yearKey] = Yii::t('app', 'Йил') . ': ' . $year;
            }
        }

        // Add total_required at the end (matches data array order)
        $titles['total_required'] = Yii::t('app', 'Жами талаб');

        // Create filename
        $fileName = 'material-requirements-' . $period . '-' . $startDate;
        if ($filter) {
            $fileName .= '-filtered';
        }

        // Create Excel file
        $file = Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'material-requirements' => [
                    'data' => $arrFile,
                    'titles' => $titles,
                ],
            ],
        ]);

        $file->send(\app\components\Helpers::downloadFileName($fileName));
    }
}