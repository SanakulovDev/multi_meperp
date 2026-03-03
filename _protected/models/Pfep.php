<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pfep".
 *
 * @property int $id
 * @property string|null $created_at
 * @property string|null $prt
 * @property string|null $duns
 * @property string|null $carr_transit_time1
 * @property string|null $beg_in_proc
 * @property string|null $shrt_desc
 * @property string|null $description
 * @property string|null $dock
 * @property string|null $prt_stat
 * @property string|null $over_under
 * @property string|null $pc_bank
 * @property string|null $part_hr_bank
 * @property string|null $beg_in_proc_job
 * @property string|null $lst_schd_dt
 * @property string|null $part_schd_typ
 * @property string|null $bcst_cd
 * @property string|null $dloc
 * @property string|null $prm_uloc
 * @property string|null $prt_wgt
 * @property string|null $bbal
 * @property string|null $rwrk_qty
 * @property string|null $qual_hold_qty
 * @property string|null $doh
 * @property string|null $tot_bank
 * @property string|null $ptg
 * @property string|null $dept
 * @property string|null $operation
 * @property string|null $cnt_actv_ulocs
 * @property string|null $horizon
 * @property string|null $wreq1
 * @property string|null $wreq2
 * @property string|null $wreq3
 * @property string|null $wreq4
 * @property string|null $wreq5
 * @property string|null $wreq6
 * @property string|null $wreq7
 * @property string|null $wreq8
 * @property string|null $wreq9
 * @property string|null $wreq10
 * @property string|null $wreq11
 * @property string|null $wreq12
 * @property string|null $wreq13
 * @property string|null $wreq14
 * @property string|null $wreq15
 * @property string|null $wreq16
 * @property string|null $wreq17
 * @property string|null $wreq18
 * @property string|null $wreq19
 * @property string|null $wreq20
 * @property string|null $wreq21
 * @property string|null $wreq22
 * @property string|null $wreq23
 * @property string|null $wreq24
 * @property string|null $wreq25
 * @property string|null $wreq26
 * @property string|null $wreq27
 * @property string|null $wreq28
 * @property string|null $wreq29
 * @property string|null $wreq30
 * @property string|null $wreq31
 * @property string|null $wreq32
 * @property string|null $wreq33
 * @property string|null $wreq34
 * @property string|null $wreq35
 * @property string|null $wreq36
 * @property string|null $wreq37
 * @property string|null $wreq38
 * @property string|null $wreq39
 * @property string|null $wreq40
 * @property string|null $20_day_reqt
 * @property string|null $ytd_prod
 * @property string|null $dreq1
 * @property string|null $dreq2
 * @property string|null $dreq3
 * @property string|null $dreq4
 * @property string|null $dreq5
 * @property string|null $dreq6
 * @property string|null $dreq7
 * @property string|null $dreq8
 * @property string|null $dreq9
 * @property string|null $dreq10
 * @property string|null $dreq11
 * @property string|null $dreq12
 * @property string|null $dreq13
 * @property string|null $dreq14
 * @property string|null $dreq15
 * @property string|null $dreq16
 * @property string|null $dreq17
 * @property string|null $dreq18
 * @property string|null $dreq19
 * @property string|null $dreq20
 */
class Pfep extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pfep';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['prt', 'duns', 'carr_transit_time1', 'beg_in_proc', 'shrt_desc', 'description', 'dock', 'prt_stat', 'over_under', 'pc_bank', 'part_hr_bank', 'beg_in_proc_job', 'lst_schd_dt', 'part_schd_typ', 'bcst_cd', 'dloc', 'prm_uloc', 'prt_wgt', 'bbal', 'rwrk_qty', 'qual_hold_qty', 'doh', 'tot_bank', 'ptg', 'dept', 'operation', 'cnt_actv_ulocs', 'horizon', 'wreq1', 'wreq2', 'wreq3', 'wreq4', 'wreq5', 'wreq6', 'wreq7', 'wreq8', 'wreq9', 'wreq10', 'wreq11', 'wreq12', 'wreq13', 'wreq14', 'wreq15', 'wreq16', 'wreq17', 'wreq18', 'wreq19', 'wreq20', 'wreq21', 'wreq22', 'wreq23', 'wreq24', 'wreq25', 'wreq26', 'wreq27', 'wreq28', 'wreq29', 'wreq30', 'wreq31', 'wreq32', 'wreq33', 'wreq34', 'wreq35', 'wreq36', 'wreq37', 'wreq38', 'wreq39', 'wreq40', '20_day_reqt', 'ytd_prod', 'dreq1', 'dreq2', 'dreq3', 'dreq4', 'dreq5', 'dreq6', 'dreq7', 'dreq8', 'dreq9', 'dreq10', 'dreq11', 'dreq12', 'dreq13', 'dreq14', 'dreq15', 'dreq16', 'dreq17', 'dreq18', 'dreq19', 'dreq20'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'prt' => Yii::t('app', 'Prt'),
            'duns' => Yii::t('app', 'Duns'),
            'carr_transit_time1' => Yii::t('app', 'Carr Transit Time1'),
            'beg_in_proc' => Yii::t('app', 'Beg In Proc'),
            'shrt_desc' => Yii::t('app', 'Shrt Desc'),
            'description' => Yii::t('app', 'Description'),
            'dock' => Yii::t('app', 'Dock'),
            'prt_stat' => Yii::t('app', 'Prt Stat'),
            'over_under' => Yii::t('app', 'Over Under'),
            'pc_bank' => Yii::t('app', 'Pc Bank'),
            'part_hr_bank' => Yii::t('app', 'Part Hr Bank'),
            'beg_in_proc_job' => Yii::t('app', 'Beg In Proc Job'),
            'lst_schd_dt' => Yii::t('app', 'Lst Schd Dt'),
            'part_schd_typ' => Yii::t('app', 'Part Schd Typ'),
            'bcst_cd' => Yii::t('app', 'Bcst Cd'),
            'dloc' => Yii::t('app', 'Dloc'),
            'prm_uloc' => Yii::t('app', 'Prm Uloc'),
            'prt_wgt' => Yii::t('app', 'Prt Wgt'),
            'bbal' => Yii::t('app', 'Bbal'),
            'rwrk_qty' => Yii::t('app', 'Rwrk Qty'),
            'qual_hold_qty' => Yii::t('app', 'Qual Hold Qty'),
            'doh' => Yii::t('app', 'Doh'),
            'tot_bank' => Yii::t('app', 'Tot Bank'),
            'ptg' => Yii::t('app', 'Ptg'),
            'dept' => Yii::t('app', 'Dept'),
            'operation' => Yii::t('app', 'Operation'),
            'cnt_actv_ulocs' => Yii::t('app', 'Cnt Actv Ulocs'),
            'horizon' => Yii::t('app', 'Horizon'),
            'wreq1' => Yii::t('app', 'Wreq1'),
            'wreq2' => Yii::t('app', 'Wreq2'),
            'wreq3' => Yii::t('app', 'Wreq3'),
            'wreq4' => Yii::t('app', 'Wreq4'),
            'wreq5' => Yii::t('app', 'Wreq5'),
            'wreq6' => Yii::t('app', 'Wreq6'),
            'wreq7' => Yii::t('app', 'Wreq7'),
            'wreq8' => Yii::t('app', 'Wreq8'),
            'wreq9' => Yii::t('app', 'Wreq9'),
            'wreq10' => Yii::t('app', 'Wreq10'),
            'wreq11' => Yii::t('app', 'Wreq11'),
            'wreq12' => Yii::t('app', 'Wreq12'),
            'wreq13' => Yii::t('app', 'Wreq13'),
            'wreq14' => Yii::t('app', 'Wreq14'),
            'wreq15' => Yii::t('app', 'Wreq15'),
            'wreq16' => Yii::t('app', 'Wreq16'),
            'wreq17' => Yii::t('app', 'Wreq17'),
            'wreq18' => Yii::t('app', 'Wreq18'),
            'wreq19' => Yii::t('app', 'Wreq19'),
            'wreq20' => Yii::t('app', 'Wreq20'),
            'wreq21' => Yii::t('app', 'Wreq21'),
            'wreq22' => Yii::t('app', 'Wreq22'),
            'wreq23' => Yii::t('app', 'Wreq23'),
            'wreq24' => Yii::t('app', 'Wreq24'),
            'wreq25' => Yii::t('app', 'Wreq25'),
            'wreq26' => Yii::t('app', 'Wreq26'),
            'wreq27' => Yii::t('app', 'Wreq27'),
            'wreq28' => Yii::t('app', 'Wreq28'),
            'wreq29' => Yii::t('app', 'Wreq29'),
            'wreq30' => Yii::t('app', 'Wreq30'),
            'wreq31' => Yii::t('app', 'Wreq31'),
            'wreq32' => Yii::t('app', 'Wreq32'),
            'wreq33' => Yii::t('app', 'Wreq33'),
            'wreq34' => Yii::t('app', 'Wreq34'),
            'wreq35' => Yii::t('app', 'Wreq35'),
            'wreq36' => Yii::t('app', 'Wreq36'),
            'wreq37' => Yii::t('app', 'Wreq37'),
            'wreq38' => Yii::t('app', 'Wreq38'),
            'wreq39' => Yii::t('app', 'Wreq39'),
            'wreq40' => Yii::t('app', 'Wreq40'),
            '20_day_reqt' => Yii::t('app', '20 Day Reqt'),
            'ytd_prod' => Yii::t('app', 'Ytd Prod'),
            'dreq1' => Yii::t('app', 'Dreq1'),
            'dreq2' => Yii::t('app', 'Dreq2'),
            'dreq3' => Yii::t('app', 'Dreq3'),
            'dreq4' => Yii::t('app', 'Dreq4'),
            'dreq5' => Yii::t('app', 'Dreq5'),
            'dreq6' => Yii::t('app', 'Dreq6'),
            'dreq7' => Yii::t('app', 'Dreq7'),
            'dreq8' => Yii::t('app', 'Dreq8'),
            'dreq9' => Yii::t('app', 'Dreq9'),
            'dreq10' => Yii::t('app', 'Dreq10'),
            'dreq11' => Yii::t('app', 'Dreq11'),
            'dreq12' => Yii::t('app', 'Dreq12'),
            'dreq13' => Yii::t('app', 'Dreq13'),
            'dreq14' => Yii::t('app', 'Dreq14'),
            'dreq15' => Yii::t('app', 'Dreq15'),
            'dreq16' => Yii::t('app', 'Dreq16'),
            'dreq17' => Yii::t('app', 'Dreq17'),
            'dreq18' => Yii::t('app', 'Dreq18'),
            'dreq19' => Yii::t('app', 'Dreq19'),
            'dreq20' => Yii::t('app', 'Dreq20'),
        ];
    }
}
