<?php
$options['name'] 			= 'scss';
$options['nameoptions'] 	= $options['name'].'.';
$options['corePath'] 		= $modx->getOption($options['nameoptions'].'core_path', $input, $modx->getOption('core_path'));
$options['componentPath'] 	= $modx->getOption($options['nameoptions'].'component_path', $input, $options['corePath'].'components/'.$options['name'] .'/');
$options['assetsUrl']  		= $modx->getOption($options['nameoptions'].'assets_url', $input, $modx->getOption('assets_url'));
$options['vendorPath'] 		= $modx->getOption($options['nameoptions'].'vendor_path', $input, $options['componentPath'] . 'vendor/');
$options['basePath'] 		= $modx->getOption($options['nameoptions'].'base_path', $input, $modx->getOption('base_path'));
$options['fileHash'] 		= $modx->getOption($options['nameoptions'].'fileHash', $input, $options['corePath'] . 'cache/default/scss/scssphp.php');
$options['admin'] 			= $modx->getOption($options['nameoptions'].'admin', $input, true);

if($options['admin']){
    $isAuth = $modx->user->isAuthenticated('mgr') && $modx->user->isAuthenticated($modx->context->key);
	if(!$isAuth && !$modx->user->isMember('Administrator')) return;
}

require_once($options['vendorPath']."scss.inc.php");

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

if (!function_exists('arrayFiles')){
    function arrayFiles($files) {
		global $options;
		$arFiles = array();
		$arError = array();
		
		foreach (explode(',', $files) as $file){
			$file = ltrim($options['basePath'].$file,'/');
			if (is_file($file) && (strtolower(pathinfo($file,PATHINFO_EXTENSION))=='scss')) {
				$arFiles[]= file_get_contents($file);
			}else{
				$arError[] = $file;
			}
		}
		return array('result' => implode("", $arFiles),'error' => implode(",", $arError));
	}
}

$config['fileScss']		    = $modx->getOption($options['nameoptions'].'fileScss', $input, $options['assetsUrl']. 'scss/styles.scss');
$config['fileCss'] 		    = $modx->getOption($options['nameoptions'].'fileCss', $input, $options['assetsUrl']. 'css/styles.css');
$config['importPaths'] 	    = $modx->getOption($options['nameoptions'].'importPaths', $input, '');
$config['outputStyle']  	= $modx->getOption($options['nameoptions'].'outputStyle', $input, false);
$config['sourceMap'] 	    = $modx->getOption($options['nameoptions'].'sourceMap', $input, false);
$config['scssHash'] 	    = $modx->getOption($options['nameoptions'].'scssHash', $input, true);

$config['fileScss'] 		= arrayFiles($config['fileScss']);
$config['fileCss'] 			= $options['basePath'].$config['fileCss'];

if(!empty($config['fileScss']['result'])){
	
/*SCSS Hash*/
	if($config['scssHash']){
		$scss_hash = hash('md5',$config['fileScss']['result']);
		$is_hash = (!file_exists($options['fileHash']) || ($scss_hash!=file_get_contents($options['fileHash'])))?true:false;
	}else{
		$is_hash = true;
	}
	if ($is_hash){
		try{
			$compiler = new Compiler();
			
		/*Output Formatting*/
			if($config['outputStyle']) {
				$compiler->setOutputStyle(OutputStyle::COMPRESSED);
			}else{
				$compiler->setOutputStyle(OutputStyle::EXPANDED);
			}
			
		/*Import Paths*/
			if($config['importPaths']) $compiler->setImportPaths(arrayFiles($config['importPaths']));
			
		/*Source Maps*/
			if($config['sourceMap']){
				$compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);	
				$compiler->setSourceMapOptions(array(
					'sourceMapURL'		=> $config['fileCss'].'.map',
					'sourceMapFilename' => $config['fileCss'],
					'sourceMapBasepath' => $options['basePath'],
					'sourceRoot'		=> '/'
				));
			}
			
			$result	= $compiler->compileString($config['fileScss']['result']);
			if (!file_exists($config['fileCss'])) {
				$path = pathinfo($config['fileCss']);
				if (!mkdir($path['dirname'], 0700, true)) {
					$modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось создать директорию: '.$path['dirname']);
				}
			}
			file_put_contents($config['fileCss'], $result->getCss());
		/*Source Maps*/
			if($config['sourceMap']) file_put_contents($config['fileCss'].'.map', $result->getSourceMap());
		/*SCSS Hash*/
			if($config['scssHash']){
				if (!file_exists($options['fileHash'])) {
					$path = pathinfo($options['fileHash']);
					if (!mkdir($path['dirname'], 0700, true)) {
						$modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось создать директорию: '.$path['dirname']);
					}
				}
				file_put_contents($options['fileHash'], $scss_hash);
			}
			
			if(!empty($arScss['error'])) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $arScss['error']);
		}catch (\Exception $e){
			if(!empty($arScss['error'])) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $arScss['error']);
			if($error = $e->getMessage()) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось скомпилировать: ' . $error);
		}
	}
}else{
	$this->modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $arScss['error'], '', 'scss');
}
return;