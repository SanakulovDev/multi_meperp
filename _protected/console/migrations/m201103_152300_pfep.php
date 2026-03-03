<?php

use yii\db\Migration;

class m201103_152300_pfep extends Migration
{

  public function init()
  {
    $this->db = 'db';
    parent::init();
  }

  public function safeUp()
  {
    $tableOptions = 'ENGINE=InnoDB';

    $this->createTable(
      '{{%pfep}}',
      [
        'id' => $this->primaryKey(11),
        'created_at' => $this->dateTime()->null(),
        'prt' => $this->string(100)->null(),
        'duns' => $this->string(100)->null(),
        'carr_transit_time1' => $this->string(100)->null(),
        'beg_in_proc' => $this->string(100)->null(),
        'shrt_desc' => $this->string(100)->null(),
        'description' => $this->string(100)->null(),
        'dock' => $this->string(100)->null(),
        'prt_stat' => $this->string(100)->null(),
        'over_under' => $this->string(100)->null(),
        'pc_bank' => $this->string(100)->null(),
        'part_hr_bank' => $this->string(100)->null(),
        'beg_in_proc_job' => $this->string(100)->null(),
        'lst_schd_dt' => $this->string(100)->null(),
        'part_schd_typ' => $this->string(100)->null(),
        'bcst_cd' => $this->string(100)->null(),
        'dloc' => $this->string(100)->null(),
        'prm_uloc' => $this->string(100)->null(),
        'prt_wgt' => $this->string(100)->null(),
        'bbal' => $this->string(100)->null(),
        'rwrk_qty' => $this->string(100)->null(),
        'qual_hold_qty' => $this->string(100)->null(),
        'doh' => $this->string(100)->null(),
        'tot_bank' => $this->string(100)->null(),
        'ptg' => $this->string(100)->null(),
        'dept' => $this->string(100)->null(),
        'operation' => $this->string(100)->null(),
        'cnt_actv_ulocs' => $this->string(100)->null(),
        'horizon' => $this->string(100)->null(),
        'wreq1' => $this->string(100)->null(),
        'wreq2' => $this->string(100)->null(),
        'wreq3' => $this->string(100)->null(),
        'wreq4' => $this->string(100)->null(),
        'wreq5' => $this->string(100)->null(),
        'wreq6' => $this->string(100)->null(),
        'wreq7' => $this->string(100)->null(),
        'wreq8' => $this->string(100)->null(),
        'wreq9' => $this->string(100)->null(),
        'wreq10' => $this->string(100)->null(),
        'wreq11' => $this->string(100)->null(),
        'wreq12' => $this->string(100)->null(),
        'wreq13' => $this->string(100)->null(),
        'wreq14' => $this->string(100)->null(),
        'wreq15' => $this->string(100)->null(),
        'wreq16' => $this->string(100)->null(),
        'wreq17' => $this->string(100)->null(),
        'wreq18' => $this->string(100)->null(),
        'wreq19' => $this->string(100)->null(),
        'wreq20' => $this->string(100)->null(),
        'wreq21' => $this->string(100)->null(),
        'wreq22' => $this->string(100)->null(),
        'wreq23' => $this->string(100)->null(),
        'wreq24' => $this->string(100)->null(),
        'wreq25' => $this->string(100)->null(),
        'wreq26' => $this->string(100)->null(),
        'wreq27' => $this->string(100)->null(),
        'wreq28' => $this->string(100)->null(),
        'wreq29' => $this->string(100)->null(),
        'wreq30' => $this->string(100)->null(),
        'wreq31' => $this->string(100)->null(),
        'wreq32' => $this->string(100)->null(),
        'wreq33' => $this->string(100)->null(),
        'wreq34' => $this->string(100)->null(),
        'wreq35' => $this->string(100)->null(),
        'wreq36' => $this->string(100)->null(),
        'wreq37' => $this->string(100)->null(),
        'wreq38' => $this->string(100)->null(),
        'wreq39' => $this->string(100)->null(),
        'wreq40' => $this->string(100)->null(),
        '20_day_reqt' => $this->string(100)->null(),
        'ytd_prod' => $this->string(100)->null(),
        'dreq1' => $this->string(100)->null(),
        'dreq2' => $this->string(100)->null(),
        'dreq3' => $this->string(100)->null(),
        'dreq4' => $this->string(100)->null(),
        'dreq5' => $this->string(100)->null(),
        'dreq6' => $this->string(100)->null(),
        'dreq7' => $this->string(100)->null(),
        'dreq8' => $this->string(100)->null(),
        'dreq9' => $this->string(100)->null(),
        'dreq10' => $this->string(100)->null(),
        'dreq11' => $this->string(100)->null(),
        'dreq12' => $this->string(100)->null(),
        'dreq13' => $this->string(100)->null(),
        'dreq14' => $this->string(100)->null(),
        'dreq15' => $this->string(100)->null(),
        'dreq16' => $this->string(100)->null(),
        'dreq17' => $this->string(100)->null(),
        'dreq18' => $this->string(100)->null(),
        'dreq19' => $this->string(100)->null(),
        'dreq20' => $this->string(100)->null(),

      ],
      $tableOptions
    );
  }

  public function safeDown()
  {

    $this->dropTable('{{%pfep}}');
  }
}
