<?php

namespace app\console\controllers;

use app\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class DbController extends Controller
{
  public function actionClearVisitor()
  {
    $lastWeek = date("Y-m-d H:i:s", strtotime("-7 days"));
    Yii::$app->db->createCommand(
      "DELETE FROM `visitor` WHERE visited_at < '" . $lastWeek . " 00:00:00';
           OPTIMIZE TABLE `visitor`; 
          "
    )->execute();
  }

  public function actionCreateAdmin($username)
  {
    if (empty(trim($username))) {
      $this->stdout("Username is required.\n", Console::FG_RED);
      return false;
    }

    $auth = Yii::$app->authManager;
    $auth->removeAllAssignments();
    //Yii::$app->db->createCommand()->delete('user')->execute();
    //Yii::$app->db->createCommand()->resetSequence('user', 1)->execute();
    // admin user
    $admin = new User();
    $admin->id = 2; // migration da id=1 bn qlik user yaratilgan
    $admin->username = $username;
    $admin->tabno = substr($username, 2, 4);
    $admin->account_suffix = Yii::$app->params['account_suffix'];
    $admin->fullname = $username;
    $admin->password = 'admin1234';
    $admin->email = 'example@admin.uz';
    $admin->setPassword($admin->password);
    $admin->generateAuthKey();
    $admin->status = User::STATUS_ACTIVE;
    if ($admin->save()) {
      $this->stdout('User created by username ' . $username . "\n", Console::FG_GREEN);
    } else {
      print_r($admin->getErrors());

      return false;
    }
    $admin_role = $auth->getRole('admin');
    $auth->assign($admin_role, $admin->getId());
    $this->stdout('Admin role setted to user ' . $username . "\n", Console::FG_GREEN);
  }

  public function actionCreateSuperadmin($username)
  {
    $user = User::find()->where(['username' => $username])->one();
    if ($user) {
      // 1. Agar superadmin bo'lmasa yaratiladi va adminni unga child qilinadi
      $auth = Yii::$app->authManager;
      $superadmin = $auth->getRole('superadmin');
      if (!$superadmin) {
        $superadmin = $auth->createRole('superadmin');
        $superadmin->description = 'Super administrator of this application';
        $auth->add($superadmin);
        $admin = $auth->getRole('admin');
        $auth->addChild($superadmin, $admin);
        $this->stdout("Superadmin role created \n", Console::FG_GREEN);
      }
      // 2. Berilgan user ga superadmin role berildi, eski roli o'chiriladi
      $auth->revokeAll($user->id);
      $auth->assign($superadmin, $user->id);
      $this->stdout('Superadmin role assigned to user ' . $username . " \n", Console::FG_GREEN);
    } else {
      $this->stdout("User not found \n", Console::FG_RED);
    }
  }

  public function actionSeedAll()
  {
    self::seedCurrencies();
    self::seedUnits();
    self::seedReportGroupList();
    self::seedReportList();
    self::seedDocumentTypes();
    self::seedShipMode();
    self::seedPaymentTerms();
    self::seedDeliveryTerms();
    self::seedConsolidationTypes();
    self::seedWarehouseReportGroups();
    self::seedCountries();
    self::seedQuestionList();
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedCurrencies()
  {
    self::seedCurrencies();
  }

  private function seedCurrencies()
  {
    Yii::$app->db->createCommand()->batchInsert(
      'currency',
      ['code', 'name'],
      [
        ['UZS', 'Uzbek sums',],
        ['USD', 'US Dollars',],
        ['RUB', 'Russian rubl',],
        ['EUR', 'European Euro',],
      ]
    )->execute();
    $this->stdout("added currencies successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedUnits()
  {
    self::seedUnits();
  }

  private function seedUnits()
  {
    Yii::$app->db->createCommand()->batchInsert(
      'unit',
      ['unit_value', 'description'],
      [
        ['m', 'metr'],
        ['m²', 'kv.metr'],
        ['p/m', 'p/m'],
        ['EA', 'штук'],
        ['kg', 'килограм'],
        ['SET', 'SET'],
      ]
    )->execute();
    $this->stdout("added units successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */

  public function actionSeedReportGroups()
  {
    self::seedReportGroupList();
  }

  private function seedReportGroupList()
  {
    Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
    Yii::$app->db->createCommand()->truncateTable('report_group')->execute();
    Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();
    $list = [
      ['Material management', 1, 'fa fa-cube', '#e07768'],
      ['Inventory', 2, 'fa fa-diamond', '#6eadd4'],
      ['Production', 3, 'fa fa-cogs', '#4aada9'],
      ['Sales', 4, 'fa fa-truck', '#9c27b0'],
      ['Finance', 5, 'fa fa-line-chart', '#ff5722'],
      ['Visitors', 6, 'fa fa-users', '#4caf50'],
      ['Logistics', 7, 'fa fa-ship', '#795548'],
    ];
    Yii::$app->db->createCommand()
      ->batchInsert('report_group', ['name', 'order', 'icon', 'color'], $list)->execute();
    $this->stdout('added ' . count($list) . " report groups successfully \n", Console::FG_GREEN);
  }

  public function actionSeedReports()
  {
    self::seedReportList();
  }

  private function seedReportList()
  {
    Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
    Yii::$app->db->createCommand()->truncateTable('report')->execute();
    Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();
    $report_list = [
      ['stock', 'Stocks', 'Stocks', 1, 'ion ion-cube:yellow', 2],
      ['coverage', 'Coverage', 'Coverage', 2, 'ion ion-calendar:aqua', 1],
      ['vehicle-set', 'UzAuto visibility report', 'UzAuto visibility report', 3, 'ion ion-map:yellow', 1],
      ['production-plan', 'Production plan', 'Production plan', 4, 'ion ion-clipboard:green', 3],
      ['production-plan-actual', 'Production target/actual', 'Production target/actual', 5, 'ion ion-clipboard:green', 3],
      ['production-count-line', 'Production result by product line', 'Production result by product line', 6, 'ion ion-monitor:red', 3],
      ['requirement', 'Part requirement', 'Part requirement', 7, 'ion ion-network:aqua', 1],
      ['in-transit', 'In transit materials report', 'In transit materials report', 8, 'ion ion-map:aqua', 1],
      ['ftq-by-line', 'FTQ by product line', 'FTQ by product line', 9, 'ion ion-pie-graph:red', 3],
      ['ftq-summary', 'FTQ by product line(Summary)', 'FTQ by product line(Summary)', 10, 'ion ion-pie-graph:red', 3],
      ['pipeline', 'Pipeline material report(container)', 'Pipeline material report(container)', 11, 'ion ion-location:aqua', 1],
      ['fg-invoice', 'Finished good invoice report', 'Finished good invoice report', 12, 'ion ion-log-out:lime', 4],
      ['open-order', 'Order status', 'Order status', 13, 'ion ion-document:lime', 1],
      ['import', 'Monthly material import report', 'Monthly material import report', 14, 'ion ion-earth:lime', 1],
      ['visitors', 'Visitors statistics', 'Visitors statistics', 15, 'ion ion-person-stalker:maroon', 6],
      ['all-visitors', 'All visitors', 'All visitors', 16, 'ion ion-android-contacts:maroon', 6],
      ['doh', 'Days On Hand', 'Days On Hand', 17, 'ion ion-calendar:aqua', 2],
      ['coverage-balance-old', 'Cash requirement for import shipments (OLD)', 'Cash requirement for import shipments (OLD)', 18, 'ion ion-calendar:aqua', 5],
      ['spl', 'SPL', 'SPL', 19, 'fa fa-dollar:aqua', 5],
      ['bom-cost', 'Material BOM Cost', 'Material BOM Cost', 20, 'fa fa-dollar:aqua', 5],
      ['fact-by-hour', 'Fact by hour', 'Fact by hour', 21, 'fa fa-dollar:aqua', 3],
      ['sp', 'Shipment performance', 'Shipment performance', 22, 'fa fa-dollar:aqua', 1],
      ['pfep-parts', 'PFEP Active Parts', 'PFEP Active Parts', 23, 'fa fa-dollar:aqua', 2],
      ['ccu', 'Container cube utilization', 'Container cube utilization', 24, 'fa fa-dollar:aqua', 7],
      ['ccum', 'Container cube utilization (month)', 'Container cube utilization (month)', 25, 'fa fa-dollar:aqua', 7],
      ['doh-calc', 'Days on Hand calculation', 'Days on Hand calculation', 26, 'ion ion-calendar:aqua', 2],
      ['cash-requirement', 'Cash requirement for import shipments', 'Cash requirement for import shipments', 27, 'ion ion-calendar:aqua', 5],
      ['imos', 'Import materials order status', 'Import materials order status', 28, 'ion ion-calendar:aqua', 1],
      ['sum-imos', 'Import materials order status (SUM)', 'Import materials order status (SUM)', 29, 'ion ion-calendar:aqua', 1],
      
      ['sales-summary', 'Sales report', 'Sales report', 30, 'fa fa-dollar:aqua', 4],
      ['implementation-plan', 'Implementation plan', 'Implementation plan', 31, 'fa fa-dollar:aqua', 4],
      ['implementation-plan-fact', 'Plan/fact of implementation', 'Plan/fact of implementation', 32, 'fa fa-dollar:aqua', 4],
      ['sales-payment-status', 'Payment status for customers (Debit/credit)', 'Payment status for customers (Debit/credit)', 33, 'fa fa-dollar:aqua', 4],
      
      ['material-stock', 'Material stock', 'Material stock', 34, 'fion ion-cube:yellow', 2],
    ];
    Yii::$app->db->createCommand()
      ->batchInsert('report', ['action', 'title', 'description', 'list_order', 'style', 'report_group_id'], $report_list)->execute();
    $this->stdout('added ' . count($report_list) . " reports successfully \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedDocumentTypes()
  {
    self::seedDocumentTypes();
  }

  private function seedDocumentTypes()
  {
    Yii::$app->db->createCommand()->batchInsert(
      'document_type',
      ['code', 'name', 'description'],
      [
        ['INV', 'Инвойс', 'Инвойс'],
        ['NAK', 'Накладной', 'Накладной'],
        ['AKT', 'Акт', 'Акт'],
      ]
    )->execute();
    $this->stdout("added document_types successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedShipModes()
  {
    self::seedShipMode();
  }

  private function seedShipMode()
  {
    Yii::$app->db->createCommand()->batchInsert(
      'ship_mode',
      ['name', 'description'],
      [
        ['Avia', 'Авиагруз'],
        ['Container', 'Контейнер'],
        ['Track', 'Автовоз'],
      ]
    )->execute();
    $this->stdout("added ship_mode successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedPaymentTerms()
  {
    self::seedPaymentTerms();
  }

  private function seedPaymentTerms()
  {
    Yii::$app->db->createCommand()->batchInsert(
      '{{%payment_term}}',
      ['name'],
      [
        ['LC',],
        ['ПО',],
        ['ПФ',],
      ]
    )->execute();
    $this->stdout("added payment_terms successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedDeliveryTerms()
  {
    self::seedDeliveryTerms();
  }

  private function seedDeliveryTerms()
  {
    Yii::$app->db->createCommand()->batchInsert(
      '{{%delivery_term}}',
      ['name', 'priority'],
      [
        ['CIP', 1],
        ['FOB', 2],
        ['DAP', 3],
        ['CPT', 4],
        ['FCA', 5],
        ['CFR', 6],
        ['CIF', 7],
        ['FAS', 8],
        ['EXW', 9],
        ['DAT', 10],
        ['DDP', 11]
      ]
    )->execute();
    $this->stdout("added delivery_terms successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedConsolidationTypes()
  {
    self::seedConsolidationTypes();
  }

  private function seedConsolidationTypes()
  {
    Yii::$app->db->createCommand()->batchInsert(
      '{{%consolidation_type}}',
      ['name'],
      [
        ['AIR',],
        ['CON',],
        ['FCL',],
        ['FTL',],
      ]
    )->execute();
    $this->stdout("added consolidation_type successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedWarehouseReportGroups()
  {
    self::seedWarehouseReportGroups();
  }

  private function seedWarehouseReportGroups()
  {
    Yii::$app->db->createCommand()->batchInsert(
      '{{%warehouse_report_group}}',
      ['title', 'description'],
      [
        ['Warehouse stock', 'Warehouse stock'],
        ['Consigment', 'Consigment'],
        ['Subsidiary materials', 'Склад вспомогательных не прямых материалы'],
        ['Finished goods', 'Finished goods'],
        ['Quality on hold', 'Quality on hold'],
        ['Line stock', 'Line stock'],
      ]
    )->execute();
    $this->stdout("added warehouse_report_group successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedLocationTypes()
  {
    self::seedLocationType();
  }

  private function seedLocationType()
  {
    Yii::$app->db->createCommand()->batchInsert(
      'location_type',
      ['name'],
      [
        ['plant'],
        ['shop'],
        ['line'],
        ['sector'],
      ]
    )->execute();
    $this->stdout("added location_type successful \n", Console::FG_GREEN);
  }

  public function actionSeedCountries()
  {
    self::seedCountries();
  }

  private function seedCountries()
  {
    $query = Yii::$app->db->queryBuilder->batchInsert(
      'country_code',
      ['alpha_2', 'alpha_3', 'numeric_code', 'name_en', 'name_ru'],
      [
        ['AF', 'AFG', 4, 'Afghanistan', ' Афганистан'],
        ['AL', 'ALB', 8, 'Albania', ' Албания'],
        ['DZ', 'DZA', 12, 'Algeria', ' Алжир'],
        ['AS', 'ASM', 16, 'American Samoa', ' Американское Самоа'],
        ['AD', 'AND', 20, 'Andorra', ' Андорра'],
        ['AO', 'AGO', 24, 'Angola', ' Ангола'],
        ['AI', 'AIA', 660, 'Anguilla', ' Ангилья'],
        ['AQ', 'ATA', 10, 'Antarctica', ' Антарктика'],
        ['AG', 'ATG', 28, 'Antigua and Barbuda', ' Антигуа и Барбуда'],
        ['AR', 'ARG', 32, 'Argentina', ' Аргентина'],
        ['AM', 'ARM', 51, 'Armenia', ' Армения'],
        ['AW', 'ABW', 533, 'Aruba', ' Аруба'],
        ['AU', 'AUS', 36, 'Australia', ' Австралия'],
        ['AT', 'AUT', 40, 'Austria', ' Австрия'],
        ['AZ', 'AZE', 31, 'Azerbaijan', ' Азербайджан'],
        ['BS', 'BHS', 44, 'Bahamas (the)', ' Багамские Острова'],
        ['BH', 'BHR', 48, 'Bahrain', ' Бахрейн'],
        ['BD', 'BGD', 50, 'Bangladesh', ' Бангладеш'],
        ['BB', 'BRB', 52, 'Barbados', ' Барбадос'],
        ['BY', 'BLR', 112, 'Belarus', ' Белоруссия'],
        ['BE', 'BEL', 56, 'Belgium', ' Бельгия'],
        ['BZ', 'BLZ', 84, 'Belize', ' Белиз'],
        ['BJ', 'BEN', 204, 'Benin', ' Бенин'],
        ['BM', 'BMU', 60, 'Bermuda', ' Бермуды'],
        ['BT', 'BTN', 64, 'Bhutan', ' Бутан'],
        ['BO', 'BOL', 68, 'Bolivia (Plurinational State of)', ' Боливия'],
        ['BQ', 'BES', 535, 'Bonaire, Sint Eustatius and Saba', ' Бонэйр, Синт-Эстатиус и Саба'],
        ['BA', 'BIH', 70, 'Bosnia and Herzegovina', ' Босния и Герцеговина'],
        ['BW', 'BWA', 72, 'Botswana', ' Ботсвана'],
        ['BV', 'BVT', 74, 'Bouvet Island', ' Остров Буве'],
        ['BR', 'BRA', 76, 'Brazil', ' Бразилия'],
        ['IO', 'IOT', 86, 'British Indian Ocean Territory (the)', ' Британская территория в Индийском океане'],
        ['BN', 'BRN', 96, 'Brunei Darussalam', ' Бруней'],
        ['BG', 'BGR', 100, 'Bulgaria', ' Болгария'],
        ['BF', 'BFA', 854, 'Burkina Faso', ' Буркина-Фасо'],
        ['BI', 'BDI', 108, 'Burundi', ' Бурунди'],
        ['CV', 'CPV', 132, 'Cabo Verde', ' Кабо-Верде'],
        ['KH', 'KHM', 116, 'Cambodia', ' Камбоджа'],
        ['CM', 'CMR', 120, 'Cameroon', ' Камерун'],
        ['CA', 'CAN', 124, 'Canada', ' Канада'],
        ['KY', 'CYM', 136, 'Cayman Islands (the)', ' Острова Кайман'],
        ['CF', 'CAF', 140, 'Central African Republic (the)', ' ЦАР'],
        ['TD', 'TCD', 148, 'Chad', ' Чад'],
        ['CL', 'CHL', 152, 'Chile', ' Чили'],
        ['CN', 'CHN', 156, 'China', ' Китай'],
        ['CX', 'CXR', 162, 'Christmas Island', ' Остров Рождества'],
        ['CC', 'CCK', 166, 'Cocos (Keeling) Islands (the)', ' Кокосовые острова'],
        ['CO', 'COL', 170, 'Colombia', ' Колумбия'],
        ['KM', 'COM', 174, 'Comoros (the)', ' Коморы'],
        ['CD', 'COD', 180, 'Congo (the Democratic Republic of the)', ' ДР Конго'],
        ['CG', 'COG', 178, 'Congo (the)', ' Республика Конго'],
        ['CK', 'COK', 184, 'Cook Islands (the)', ' Острова Кука'],
        ['CR', 'CRI', 188, 'Costa Rica', ' Коста-Рика'],
        ['HR', 'HRV', 191, 'Croatia', ' Хорватия'],
        ['CU', 'CUB', 192, 'Cuba', ' Куба'],
        ['CW', 'CUW', 531, 'Curaçao', ' Кюрасао'],
        ['CY', 'CYP', 196, 'Cyprus', ' Кипр'],
        ['CZ', 'CZE', 203, 'Czechia', ' Чехия'],
        ['CI', 'CIV', 384, "Côte d'Ivoire", ' Кот-д’Ивуар'],
        ['DK', 'DNK', 208, 'Denmark', ' Дания'],
        ['DJ', 'DJI', 262, 'Djibouti', ' Джибути'],
        ['DM', 'DMA', 212, 'Dominica', ' Доминика'],
        ['DO', 'DOM', 214, 'Dominican Republic (the)', ' Доминиканская Республика'],
        ['EC', 'ECU', 218, 'Ecuador', ' Эквадор'],
        ['EG', 'EGY', 818, 'Egypt', ' Египет'],
        ['SV', 'SLV', 222, 'El Salvador', ' Сальвадор'],
        ['GQ', 'GNQ', 226, 'Equatorial Guinea', ' Экваториальная Гвинея'],
        ['ER', 'ERI', 232, 'Eritrea', ' Эритрея'],
        ['EE', 'EST', 233, 'Estonia', ' Эстония'],
        ['SZ', 'SWZ', 748, 'Eswatini', ' Эсватини'],
        ['ET', 'ETH', 231, 'Ethiopia', ' Эфиопия'],
        ['FK', 'FLK', 238, 'Falkland Islands (the) [Malvinas]', ' Фолклендские острова'],
        ['FO', 'FRO', 234, 'Faroe Islands (the)', ' Фарерские острова'],
        ['FJ', 'FJI', 242, 'Fiji', ' Фиджи'],
        ['FI', 'FIN', 246, 'Finland', ' Финляндия'],
        ['FR', 'FRA', 250, 'France', ' Франция'],
        ['GF', 'GUF', 254, 'French Guiana', ' Гвиана'],
        ['PF', 'PYF', 258, 'French Polynesia', ' Французская Полинезия'],
        ['TF', 'ATF', 260, 'French Southern Territories (the)', ' Французские Южные и Антарктические территории'],
        ['GA', 'GAB', 266, 'Gabon', ' Габон'],
        ['GM', 'GMB', 270, 'Gambia (the)', ' Гамбия'],
        ['GE', 'GEO', 268, 'Georgia', ' Грузия'],
        ['DE', 'DEU', 276, 'Germany', ' Германия'],
        ['GH', 'GHA', 288, 'Ghana', ' Гана'],
        ['GI', 'GIB', 292, 'Gibraltar', ' Гибралтар'],
        ['GR', 'GRC', 300, 'Greece', ' Греция'],
        ['GL', 'GRL', 304, 'Greenland', ' Гренландия'],
        ['GD', 'GRD', 308, 'Grenada', ' Гренада'],
        ['GP', 'GLP', 312, 'Guadeloupe', ' Гваделупа'],
        ['GU', 'GUM', 316, 'Guam', ' Гуам'],
        ['GT', 'GTM', 320, 'Guatemala', ' Гватемала'],
        ['GG', 'GGY', 831, 'Guernsey', ' Гернси'],
        ['GN', 'GIN', 324, 'Guinea', ' Гвинея'],
        ['GW', 'GNB', 624, 'Guinea-Bissau', ' Гвинея-Бисау'],
        ['GY', 'GUY', 328, 'Guyana', ' Гайана'],
        ['HT', 'HTI', 332, 'Haiti', ' Гаити'],
        ['HM', 'HMD', 334, 'Heard Island and McDonald Islands', ' Херд и Макдональд'],
        ['VA', 'VAT', 336, 'Holy See (the)', ' Ватикан'],
        ['HN', 'HND', 340, 'Honduras', ' Гондурас'],
        ['HK', 'HKG', 344, 'Hong Kong', ' Гонконг'],
        ['HU', 'HUN', 348, 'Hungary', ' Венгрия'],
        ['IS', 'ISL', 352, 'Iceland', ' Исландия'],
        ['IN', 'IND', 356, 'India', 'Индия Индия'],
        ['ID', 'IDN', 360, 'Indonesia', ' Индонезия'],
        ['IR', 'IRN', 364, 'Iran (Islamic Republic of)', ' Иран'],
        ['IQ', 'IRQ', 368, 'Iraq', ' Ирак'],
        ['IE', 'IRL', 372, 'Ireland', ' Ирландия'],
        ['IM', 'IMN', 833, 'Isle of Man', ' Остров Мэн'],
        ['IL', 'ISR', 376, 'Israel', ' Израиль'],
        ['IT', 'ITA', 380, 'Italy', ' Италия'],
        ['JM', 'JAM', 388, 'Jamaica', ' Ямайка'],
        ['JP', 'JPN', 392, 'Japan', ' Япония'],
        ['JE', 'JEY', 832, 'Jersey', ' Джерси'],
        ['JO', 'JOR', 400, 'Jordan', ' Иордания'],
        ['KZ', 'KAZ', 398, 'Kazakhstan', ' Казахстан'],
        ['KE', 'KEN', 404, 'Kenya', ' Кения'],
        ['KI', 'KIR', 296, 'Kiribati', ' Кирибати'],
        ['KP', 'PRK', 408, "Korea (the Democratic People's Republic of)", ' КНДР'],
        ['KR', 'KOR', 410, 'Korea (the Republic of)', ' Республика Корея'],
        ['KW', 'KWT', 414, 'Kuwait', ' Кувейт'],
        ['KG', 'KGZ', 417, 'Kyrgyzstan', ' Киргизия'],
        ['LA', 'LAO', 418, "Lao People's Democratic Republic (the)", ' Лаос'],
        ['LV', 'LVA', 428, 'Latvia', ' Латвия'],
        ['LB', 'LBN', 422, 'Lebanon', ' Ливан'],
        ['LS', 'LSO', 426, 'Lesotho', ' Лесото'],
        ['LR', 'LBR', 430, 'Liberia', ' Либерия'],
        ['LY', 'LBY', 434, 'Libya', ' Ливия'],
        ['LI', 'LIE', 438, 'Liechtenstein', ' Лихтенштейн'],
        ['LT', 'LTU', 440, 'Lithuania', ' Литва'],
        ['LU', 'LUX', 442, 'Luxembourg', ' Люксембург'],
        ['MO', 'MAC', 446, 'Macao', ' Макао'],
        ['MG', 'MDG', 450, 'Madagascar', ' Мадагаскар'],
        ['MW', 'MWI', 454, 'Malawi', ' Малави'],
        ['MY', 'MYS', 458, 'Malaysia', ' Малайзия'],
        ['MV', 'MDV', 462, 'Maldives', ' Мальдивы'],
        ['ML', 'MLI', 466, 'Mali', ' Мали'],
        ['MT', 'MLT', 470, 'Malta', ' Мальта'],
        ['MH', 'MHL', 584, 'Marshall Islands (the)', ' Маршалловы Острова'],
        ['MQ', 'MTQ', 474, 'Martinique', ' Мартиника'],
        ['MR', 'MRT', 478, 'Mauritania', ' Мавритания'],
        ['MU', 'MUS', 480, 'Mauritius', ' Маврикий'],
        ['YT', 'MYT', 175, 'Mayotte', ' Майотта'],
        ['MX', 'MEX', 484, 'Mexico', ' Мексика'],
        ['FM', 'FSM', 583, 'Micronesia (Federated States of)', ' Микронезия'],
        ['MD', 'MDA', 498, 'Moldova (the Republic of)', ' Молдавия'],
        ['MC', 'MCO', 492, 'Monaco', ' Монако'],
        ['MN', 'MNG', 496, 'Mongolia', ' Монголия'],
        ['ME', 'MNE', 499, 'Montenegro', ' Черногория'],
        ['MS', 'MSR', 500, 'Montserrat', ' Монтсеррат'],
        ['MA', 'MAR', 504, 'Morocco', ' Марокко'],
        ['MZ', 'MOZ', 508, 'Mozambique', ' Мозамбик'],
        ['MM', 'MMR', 104, 'Myanmar', ' Мьянма'],
        ['NA', 'NAM', 516, 'Namibia', ' Намибия'],
        ['NR', 'NRU', 520, 'Nauru', ' Науру'],
        ['NP', 'NPL', 524, 'Nepal', ' Непал'],
        ['NL', 'NLD', 528, 'Netherlands (the)', ' Нидерланды'],
        ['NC', 'NCL', 540, 'New Caledonia', ' Новая Каледония'],
        ['NZ', 'NZL', 554, 'New Zealand', ' Новая Зеландия'],
        ['NI', 'NIC', 558, 'Nicaragua', ' Никарагуа'],
        ['NE', 'NER', 562, 'Niger (the)', ' Нигер'],
        ['NG', 'NGA', 566, 'Nigeria', ' Нигерия'],
        ['NU', 'NIU', 570, 'Niue', ' Ниуэ'],
        ['NF', 'NFK', 574, 'Norfolk Island', ' Остров Норфолк'],
        ['MK', 'MKD', 807, 'North Macedonia', ' Северная Македония'],
        ['MP', 'MNP', 580, 'Northern Mariana Islands (the)', ' Северные Марианские Острова'],
        ['NO', 'NOR', 578, 'Norway', ' Норвегия'],
        ['OM', 'OMN', 512, 'Oman', ' Оман'],
        ['PK', 'PAK', 586, 'Pakistan', ' Пакистан'],
        ['PW', 'PLW', 585, 'Palau', ' Палау'],
        ['PS', 'PSE', 275, 'Palestine, State of', ' Государство Палестина'],
        ['PA', 'PAN', 591, 'Panama', ' Панама'],
        ['PG', 'PNG', 598, 'Papua New Guinea', ' Папуа — Новая Гвинея'],
        ['PY', 'PRY', 600, 'Paraguay', ' Парагвай'],
        ['PE', 'PER', 604, 'Peru', ' Перу'],
        ['PH', 'PHL', 608, 'Philippines (the)', ' Филиппины'],
        ['PN', 'PCN', 612, 'Pitcairn', ' Острова Питкэрн'],
        ['PL', 'POL', 616, 'Poland', ' Польша'],
        ['PT', 'PRT', 620, 'Portugal', ' Португалия'],
        ['PR', 'PRI', 630, 'Puerto Rico', ' Пуэрто-Рико'],
        ['QA', 'QAT', 634, 'Qatar', ' Катар'],
        ['RO', 'ROU', 642, 'Romania', ' Румыния'],
        ['RU', 'RUS', 643, 'Russian Federation (the)', ' Россия'],
        ['RW', 'RWA', 646, 'Rwanda', ' Руанда'],
        ['RE', 'REU', 638, 'Réunion', ' Реюньон'],
        ['BL', 'BLM', 652, 'Saint Barthélemy', ' Сен-Бартелеми (Карибы)'],
        ['SH', 'SHN', 654, 'Saint Helena, Ascension and Tristan da Cunha', ' Острова Святой Елены, Вознесения и Тристан-да-Кунья'],
        ['KN', 'KNA', 659, 'Saint Kitts and Nevis', ' Сент-Китс и Невис'],
        ['LC', 'LCA', 662, 'Saint Lucia', ' Сент-Люсия'],
        ['MF', 'MAF', 663, 'Saint Martin (French part)', ' Сен-Мартен'],
        ['PM', 'SPM', 666, 'Saint Pierre and Miquelon', ' Сен-Пьер и Микелон'],
        ['VC', 'VCT', 670, 'Saint Vincent and the Grenadines', ' Сент-Винсент и Гренадины'],
        ['WS', 'WSM', 882, 'Samoa', ' Самоа'],
        ['SM', 'SMR', 674, 'San Marino', ' Сан-Марино'],
        ['ST', 'STP', 678, 'Sao Tome and Principe', ' Сан-Томе и Принсипи'],
        ['SA', 'SAU', 682, 'Saudi Arabia', ' Саудовская Аравия'],
        ['SN', 'SEN', 686, 'Senegal', ' Сенегал'],
        ['RS', 'SRB', 688, 'Serbia', ' Сербия'],
        ['SC', 'SYC', 690, 'Seychelles', ' Сейшельские Острова'],
        ['SL', 'SLE', 694, 'Sierra Leone', ' Сьерра-Леоне'],
        ['SG', 'SGP', 702, 'Singapore', ' Сингапур'],
        ['SX', 'SXM', 534, 'Sint Maarten (Dutch part)', ' Синт-Мартен'],
        ['SK', 'SVK', 703, 'Slovakia', ' Словакия'],
        ['SI', 'SVN', 705, 'Slovenia', ' Словения'],
        ['SB', 'SLB', 90, 'Solomon Islands', ' Соломоновы Острова'],
        ['SO', 'SOM', 706, 'Somalia', ' Сомали'],
        ['ZA', 'ZAF', 710, 'South Africa', ' ЮАР'],
        ['GS', 'SGS', 239, 'South Georgia and the South Sandwich Islands', ' Южная Георгия и Южные Сандвичевы Острова'],
        ['SS', 'SSD', 728, 'South Sudan', ' Южный Судан'],
        ['ES', 'ESP', 724, 'Spain', ' Испания'],
        ['LK', 'LKA', 144, 'Sri Lanka', ' Шри-Ланка'],
        ['SD', 'SDN', 729, 'Sudan (the)', ' Судан'],
        ['SR', 'SUR', 740, 'Suriname', ' Суринам'],
        ['SJ', 'SJM', 744, 'Svalbard and Jan Mayen', 'Флаг Шпицбергена и Ян-Майена Шпицберген и Ян-Майен'],
        ['SE', 'SWE', 752, 'Sweden', ' Швеция'],
        ['CH', 'CHE', 756, 'Switzerland', ' Швейцария'],
        ['SY', 'SYR', 760, 'Syrian Arab Republic (the)', ' Сирия'],
        ['TW', 'TWN', 158, 'Taiwan (Province of China)', ' Китайская Республика'],
        ['TJ', 'TJK', 762, 'Tajikistan', ' Таджикистан'],
        ['TZ', 'TZA', 834, 'Tanzania, the United Republic of', ' Танзания'],
        ['TH', 'THA', 764, 'Thailand', ' Таиланд'],
        ['TL', 'TLS', 626, 'Timor-Leste', ' Восточный Тимор'],
        ['TG', 'TGO', 768, 'Togo', ' Того'],
        ['TK', 'TKL', 772, 'Tokelau', ' Токелау'],
        ['TO', 'TON', 776, 'Tonga', ' Тонга'],
        ['TT', 'TTO', 780, 'Trinidad and Tobago', ' Тринидад и Тобаго'],
        ['TN', 'TUN', 788, 'Tunisia', ' Тунис'],
        ['TR', 'TUR', 792, 'Turkey', ' Турция'],
        ['TM', 'TKM', 795, 'Turkmenistan', ' Туркмения'],
        ['TC', 'TCA', 796, 'Turks and Caicos Islands (the)', ' Теркс и Кайкос'],
        ['TV', 'TUV', 798, 'Tuvalu', ' Тувалу'],
        ['UG', 'UGA', 800, 'Uganda', ' Уганда'],
        ['UA', 'UKR', 804, 'Ukraine', ' Украина'],
        ['AE', 'ARE', 784, 'United Arab Emirates (the)', ' ОАЭ'],
        ['GB', 'GBR', 826, 'United Kingdom of Great Britain and Northern Ireland (the)', ' Великобритания'],
        ['UM', 'UMI', 581, 'United States Minor Outlying Islands (the)', ' Внешние малые острова США'],
        ['US', 'USA', 840, 'United States of America (the)', ' США'],
        ['UY', 'URY', 858, 'Uruguay', ' Уругвай'],
        ['UZ', 'UZB', 860, 'Uzbekistan', ' Узбекистан'],
        ['VU', 'VUT', 548, 'Vanuatu', ' Вануату'],
        ['VE', 'VEN', 862, 'Venezuela (Bolivarian Republic of)', ' Венесуэла'],
        ['VN', 'VNM', 704, 'Viet Nam', ' Вьетнам'],
        ['VG', 'VGB', 92, 'Virgin Islands (British)', ' Виргинские Острова (Великобритания)'],
        ['VI', 'VIR', 850, 'Virgin Islands (U.S.)', ' Виргинские Острова (США)'],
        ['WF', 'WLF', 876, 'Wallis and Futuna', ' Уоллис и Футуна'],
        ['EH', 'ESH', 732, 'Western Sahara*', ' САДР'],
        ['YE', 'YEM', 887, 'Yemen', ' Йемен'],
        ['ZM', 'ZMB', 894, 'Zambia', ' Замбия'],
        ['ZW', 'ZWE', 716, 'Zimbabwe', ' Зимбабве'],
        ['AX', 'ALA', 248, 'Åland Islands', ' Аландские острова']
      ]
    );
    $query = str_replace('INSERT INTO', 'INSERT IGNORE', $query);
    Yii::$app->db->createCommand($query)->execute();
    $this->stdout("added countries successful \n", Console::FG_GREEN);
  }

  /* ------------------------------------------------------------------------------------------- */
  public function actionSeedQuestions()
  {
    self::seedQuestionList();
  }

  private function seedQuestionList()
  {
    $questionList = [
      [1, 1, 'difference_part_contract', 'Хамма деталлар шартномалари борми? Шартномаси йук деталлар нечта?'],
      [2, 2, 'difference_part_sales_contract', 'Хамма деталлар сотув шартномалари борми? Шартномаси йук деталлар нечта?'],
      [3, 3, 'order_status', 'Заказларнинг хаммаси тизимга киритилганми? Order Status хисоботи тугри курсатяптими?'],
      [4, 4, 'intransit_eta', 'Олинган Инвойслар тизимга киритилганми? Intransit хисоботи тугри курсатяптими?'],
      [5, 5, 'order_loc_not_input', 'Тизимга киритилган заказларнинг Местонахождение груза каби маълумотлари тулдирилганми?'],
      [6, 6, 'difference_part_tnved', 'Деталнинг ТН-ВЭД код лари тизимга киритилганми?'],
      [7, 7, 'mrd_status', 'Тизимга киритилган инвойс контейнерларнинг MRD санаси маълумотлари тулдирилганми?'],
      [8, 8, 'primary_price_status', 'Импорт шартномадаги деталларнинг асосий нархи танланганми?'],
      [9, 9, 'plan_fact_status', 'Тизимда ишлаб чикариш фактлари  режалаштиришга нисбатан тўғрими?'],
      [10, 10, 'delivery_term_filled', 'Контракты компонентов ойнасида "Условия поставки" тулдирилганми (Импорт деталлар учун)?'],
      [11, 11, 'outsource_stock', 'Махаллий ва Давал асосидаги (агар булса) деталлар кирим-чикимлари тизим оркали тулик юритиляптими? Манфий детал колдиклари борми?'],
      [12, 12, 'wh_line_stock', 'Асосий омбордан И/ч ва колган омборларга деталлар чикими тизим оркали тулик юритиляптими? Манфий детал колдиклари борми?'],
      [13, 13, 'fg_doc_status', 'Тайёр махсулот омбори кирим-чикимлари тулик юритиляптими? '],
      [14, 14, 'fg_invoice_status', 'Счёт фактура уз вактида киритиябдими?'],
      [15, 15, 'vehicle_eta_status', 'Йўлдаги машинакомплектларнинг ETA муддати ўтиб кетганлар муддати янгиланябдими? '],
    ];
    Yii::$app->db->createCommand('SET foreign_key_checks = 0')->execute();
    $query = Yii::$app->db->queryBuilder->batchInsert(
      'health_check',
      ['id', 'sort_order', 'title', 'description'],
      $questionList
    );
    $query = str_replace('INSERT INTO', 'REPLACE INTO', $query);
    Yii::$app->db->createCommand($query)->execute();
    Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();
    //    $res = Console::ansiFormat(count($questionList), [Console::FG_YELLOW,Console::BOLD]);
    //    $txt = Console::ansiFormat(" questions added successfully", [Console::FG_BLUE]);
    $res = count($questionList);
    $txt = " questions added successfully";
    Console::output("{$res}{$txt}");
  }
}
