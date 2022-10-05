<?php

namespace app\components;

use yii\base\Component;
use Yii;


class ReqPeriod extends Component {

    static function get() {
        $from = date('Y-m-d');
        $to = date('Y-m-t', strtotime('+1 month'));

        $begin = new \DateTime($from);
        $end = new \DateTime($to);
        $end = $end->modify('+1 day');

        $daterange = new \DatePeriod($begin, new \DateInterval('P1D'), $end);

        $period = [];
        foreach ($daterange as $date) {
            unset($tmpArr);
            $tmpArr['plandate'] = $date->format("Y-m-d");
            $tmpArr['from'] = $date->format("Y-m-d");
            $tmpArr['to'] = $date->format("Y-m-d");
            $period[] = $tmpArr;
        }

        $next2month = date('Y-m-01', strtotime('+2 month'));



        for ($i = 0; $i < 9; $i++) {
            unset($tmpArr);
            $tmpArr['plandate'] = date('Y-m', strtotime('+' . $i . ' month', strtotime($next2month)));
            $tmpArr['from'] = date('Y-m-d', strtotime('+' . $i . ' month', strtotime($next2month)));
            $tmpArr['to'] = date('Y-m-t', strtotime('+' . $i . ' month', strtotime($next2month)));
            $period[] = $tmpArr;
        }
        
        return $period;
    }

}
