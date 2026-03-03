<?php
	/**
	 * created by Ekilei <ekilei@gmail.com>
	 */
	namespace app\widgets;

	use Yii;
	use yii\bootstrap\Dropdown;
	use yii\grid\Column;
	use yii\helpers\Html;
	use yii\helpers\Url;

	class ActionButtons extends Column{
		public $buttons = [];
		public $default = '';
		public $header = '';
		public $style_def = '';
		public $style_drop = 'border-radius:0;';
		public $urlCreator;
		public $controller;
		public $exclude = [];
		public $nav = 'down';
		const DOWN = 'down';
		const UP = 'up';

		static public function Btns(){
			return [
				'view' => 'eye-open',
				'update' => 'pencil',
				'delete' => [
					'icon' => 'trash',
					'method' => 'post',
					'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
				]
			];
		}

		/**
		 * @param mixed $model
		 * @param mixed $key
		 * @param int   $index
		 * @return string
		 */
		protected function renderDataCellContent($model, $key, $index){
			if(!$this->buttons){
				$btns = static::Btns();
				if($this->exclude) foreach($this->exclude as $k) unset($btns[$k]);
				$this->buttons = $btns;
			}
			if(!$this->default){
				$this->default = key($this->buttons);
			}
			if(!$this->default) return '';
			$drop_down_arr = [];
			foreach($this->buttons as $item => $val){
				$iconName = is_array($val) ? $val['icon'] : $val;
				$title = Yii::t('yii', ucfirst($item));
				$label = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]).' '.$title;
				$url = $this->createUrl($item, $model, $key, $index);
				$addon_oprions = $this->getAddonOptions($this->buttons[$item]);
				$options = array_merge([
																 'title' => $title,
																 'aria-label' => $title,
																 'data-pjax' => '0',
															 ], $addon_oprions);
				$drop_down_arr[] = ['label' => $label, 'url' => $url, 'linkOptions' => $options, 'encode' => false];
			}
			$btn_drop = Html::tag('button', Html::tag('span', '', ['class' => 'glyphicon glyphicon-triangle-'.($this->nav == 'up' ? 'top' : 'bottom')]),
														['class' => 'btn btn-default btn-sm dropdown-toggle', 'data-toggle' => 'dropdown', 'style' => $this->style_drop])
				.Dropdown::widget(['items' => $drop_down_arr,]);
			$def_url = $this->createUrl($this->default, $model, $key, $index);
			$def_title = Yii::t('yii', ucfirst($this->default));
			$addon_oprions = $this->getAddonOptions($this->buttons[$this->default]);
			$options = array_merge([
															 'class' => 'btn btn-default btn-sm',
															 'title' => $def_title,
															 'aria-label' => $def_title,
															 'data-pjax' => '0',
															 'style' => $this->style_def,
														 ], $addon_oprions);
			$iconName = is_array($this->buttons[$this->default]) ? $this->buttons[$this->default]['icon'] : $this->buttons[$this->default];
			$btn_def = Html::a(Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]), $def_url, $options);
			$buttons = Html::tag('div', $btn_def, ['class' => 'btn-group ']).Html::tag('div', $btn_drop, ['class' => 'btn-group dropdown-toggle drop'.$this->nav]);
			return Html::tag('div', $buttons, ['class' => 'btn-group', 'style' => 'min-width:80px']);
		}

		/**
		 * @param $arr
		 * @return array
		 */
		private function getAddonOptions($arr){
			if(is_array($arr)){
				$addon_oprions = [];
				foreach($arr as $k => $v){
					if($k == 'icon') continue;
					$addon_oprions['data-'.$k] = $v;
				}
				return (array)$addon_oprions;
			}else{
				return [];
			}
		}

		private function createUrl($action, $model, $key, $index){
			if(is_callable($this->urlCreator)){
				return call_user_func($this->urlCreator, $action, $model, $key, $index, $this);
			}else{
				$params = is_array($key) ? $key : ['id' => (string)$key];
				$params[0] = $this->controller ? $this->controller.'/'.$action : $action;
				return Url::toRoute($params);
			}
		}
	}
