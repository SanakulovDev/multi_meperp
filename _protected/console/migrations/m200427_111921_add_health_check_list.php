<?php
use yii\db\Migration;

class m200427_111921_add_health_check_list extends Migration {

  public function Up() {
    $query = Yii::$app->db->queryBuilder->batchInsert(
      'health_check',
      ['id','sort_order', 'title', 'description'],
      [
//        ['0', 'intransit_eta', 'XXX'],
        ['1', '1', 'difference_part_contract', 'Хамма деталлар шартномалари борми? Шартномаси йук деталлар нечта?'],
        ['2', '2', 'difference_part_sales_contract', 'Хамма деталлар сотув шартномалари борми? Шартномаси йук деталлар нечта?'],
        ['3', '3', 'order_status', 'Заказларнинг хаммаси тизимга киритилганми? Order Status хисоботи тугри курсатяптими?'],
        ['4', '4', 'intransit_eta', 'Олинган Инвойслар тизимга киритилганми? Intransit хисоботи тугри курсатяптими?'],
        ['5', '5', 'order_loc_not_input', 'Тизимга киритилган заказларнинг Местонахождение груза каби маълумотлари тулдирилганми?'],
        ['11','11', 'outsource_stock', 'Махаллий ва Давал асосидаги (агар булса) деталлар кирим-чикимлари тизим оркали тулик юритиляптими? Манфий детал колдиклари борми?'],
        ['12','12', 'wh_line_stock', 'Асосий омбордан И/ч ва колган омборларга деталлар чикими тизим оркали тулик юритиляптими? Манфий детал колдиклари борми?'],
      ]
    );
    $query = str_replace('INSERT INTO', 'REPLACE INTO', $query);
    Yii::$app->db->createCommand($query)->execute();
  }

}