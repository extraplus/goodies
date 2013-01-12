<?php

App::uses('AppHelper', 'View/Helper');
App::uses('HtmlHelper', 'View/Helper');

/**
 * Automatic JavaScript Helper
 *
 * Facilitates JavaScript Automatic loading and inclusion for page specific JS
 * and CSS
 *
 * @copyright   Copyright 2009-2011, Graham Weldon (http://grahamweldon.com)
 * @copyright   parts Copyright 2013, Andreas Pizsa (http://twitter.com/AndreasPizsa)
 * @package     goodies
 * @subpackage  goodies.View.Helper
 * @author      Graham Weldon (http://grahamweldon.com)
 * @author      Andreas Pizsa (http://twitter.com/AndreasPizsa) (CSS)
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class AutoJavascriptHelper extends AppHelper {

/**
 * Options
 *
 * path => Path from which the controller/action file path will be built
 *         from. This is relative to the 'WWW_ROOT/js' directory
 *
 * @var array
 */
	private $__options = array(
		'path' => 'autoload',
		'theme' => true);

/**
 * View helpers required by this helper
 *
 * @var array
 */
	public $helpers = array('Html');

/**
 * Object constructor
 *
 * Allows passing in options to change class behavior
 *
 * @param string $options Key value array of options
 */
	public function __construct(View $view, $options = array()) {
		if ($options == null) {
			$options = array();
		}
		parent::__construct($view, $options);
		$this->__options = array_merge($this->__options, $options);
	}

/**
 * Before Render callback
 *
 * @return void
 */
	public function beforeRender($viewFile) {
		extract($this->__options);
		if (!empty($path)) {
			$path .= DS;
		}

		$fileTypes = array(
			'js' => array(
				$this->request->controller . '.js',
				$this->request->controller . DS . $this->request->action . '.js'
			),
			'css' => array(
				$this->request->controller . '.css',
				$this->request->controller . DS . $this->request->action . '.css'
			)
		);

		if(!defined('VIEWS')) define('VIEWS', APP . 'View' . DS);
		foreach ($fileTypes as $fileType=>$files) {
			foreach ($files as $file) {
				$file = $path . $file;
				$includeFile = ( $fileType=='js' ? JS : CSS ) . $file;
				
				// theme file overrides non-theme file if it exists
				if ($theme && !empty($this->theme)) {
					$theThemedFile = VIEWS . 'themed' . DS . $this->theme . DS . 'webroot' . DS . $fileType . DS . $file;
					if(file_exists($theThemedFile)) $includeFile = $theThemedFile;
				}

				if (file_exists($includeFile)) {
					$file = str_replace('\\', '/', $file);
					switch($fileType) {
						case 'js' :
							$this->Html->script($file, array('inline' => false));
							break;
						case 'css':
							$this->Html->css($file, null, array('inline' => false));
							break;
						default:
							// just ignore anything else
					}
				}
			}
		}
	}
}
