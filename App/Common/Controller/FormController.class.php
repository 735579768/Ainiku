<?php
//所有模块共用的一个类
namespace Common\Controller;
use Think\Controller;

class FormController extends Controller {
	private $fiel = null;
	private $data = array();
	public function __construct($field, $da = array()) {
		parent::__construct();

		//实例化视图类
		// $this->view     = new \Think\View();

		$this->fiel = $field;
		$this->data = $da;

	}
	private function isvar() {
		if (!isset($GLOBALS['formjs'])) {
			$GLOBALS['formjs'] = array('editornum' => 0, 'datetime' => 0, 'picture' => 0, 'editor' => 0, 'umeditor' => 0, 'color' => 0, 'dandu' => 0);
		}
	}
	public function getData() {
		$field = $this->fiel;
		$da    = $this->data;

		//判断是不是编辑状态
		if (strpos(ACTION_NAME, 'edit') !== false) {
			$this->assign('actionstatus', 'edit');
		} else if (strpos(ACTION_NAME, 'add') !== false) {
			$this->assign('actionstatus', 'add');
		} else {
			$this->assign('actionstatus', 'other');
		}
		//缓存
		$cacheform = sha1(json_encode($field) . json_encode($da));
		$formstr   = F('_dataform/' . $cacheform);
		if (empty($formstr) || APP_DEBUG) {
			$this->isvar();
			//判断是不是数组不是的话组合成数组
			if (isset($field['title'])) {
				$field = array($field);
			}
			//下面变量保证只自加一次
			$pic        = false;
			$dtm        = false;
			$edr        = false;
			$umedr      = false;
			$colo       = false;
			$data['jc'] = null;
			$data['kz'] = null;
			foreach ($field as $key => $val) {
				if ($val['type'] === 'file' || $val['type'] === 'picture' || $val['type'] === 'batchpicture') {
					$pic = true;
				}

				if ($val['type'] === 'datetime') {$dtm = true;}
				if ($val['type'] === 'editor') {$edr = true;}
				if ($val['type'] === 'umeditor') {$umedr = true;}
				if ($val['type'] === 'color') {$colo = true;}
				$ttt                 = trim($val['field']);
				$field[$key]['data'] = isset($da[$ttt]) ? $da[$ttt] : '';
				if ($field[$key]['data'] === '' || $field[$key]['data'] === null) {$field[$key]['data'] = isset($val['value']) ? $val['value'] : '';}
				if (!isset($val['is_require'])) {$field[$key]['is_require'] = false;}
				//判断is_show是不是空或不符合规则
				if (!isset($field[$key]['is_show'])) {
					$field[$key]['is_show'] = 3;
				}

				if (isset($val['attrtype']) && $val['attrtype'] == '1') {
					$data['kz'][] = $field[$key];
				} else {
					$data['jc'][] = $field[$key];
				}
			}
			if ($pic) {
				$GLOBALS['formjs']['picture']++;
			}

			if ($dtm) {
				$GLOBALS['formjs']['datetime']++;
			}

			if ($edr || $umedr) {
				$GLOBALS['formjs']['editornum']++;
			}

			if ($edr) {
				$GLOBALS['formjs']['editor']++;
			}

			if ($umedr) {
				$GLOBALS['formjs']['umeditor']++;
			}

			if ($colo) {
				$GLOBALS['formjs']['color']++;
			}

			$fiearr[] = $data['jc'];
			$fiearr[] = $data['kz'];
			$this->assign('fiearr', $fiearr);
			//判断是单独还是有扩展tab
			if (empty($data['kz']) || empty($data['jc'])) {
				$GLOBALS['formjs']['dandu']++;
			}

			$this->assign('formjs', $GLOBALS['formjs']);
			$formstr = $this->fetch('./App/Common/View/Widget/Form/tab.html');
			F('_dataform/' . $cacheform, $formstr);
		}
		return $formstr;

	}
}
