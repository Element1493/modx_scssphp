<?php
$options['name'] 			= 'scss';
$options['nameoptions'] 	= $options['name'].'.';
$options['corePath'] 		= $modx->getOption($options['nameoptions'].'core_path', $input, $modx->getOption('core_path'));
$options['componentPath'] 	= $options['corePath'].'components/'.$options['name'] .'/';
$options['assetsPath'] 		= $modx->getOption($options['nameoptions'].'assets_path', $input, $modx->getOption('assets_path'));
$options['vendorPath'] 		= $options['componentPath'] . 'vendor/';
$options['basePath'] 		= $modx->getOption($options['nameoptions'].'base_path', $input, $modx->getOption('base_path'));
$options['fileHash'] 		= $modx->getOption($options['nameoptions'].'fileHash', $input, $options['corePath'] . '/cache/default/scss/scssphp.php');
$options['admin'] 			= $modx->getOption($options['nameoptions'].'admin', $input, true);

if($options['admin']){
    $isAuth = $modx->user->isAuthenticated('mgr') && $modx->user->isAuthenticated($modx->context->key);
	if(!$isAuth && !$modx->user->isMember('Administrator')) return;
}

require_once($options['vendorPath']."scss.inc.php");

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

if (!function_exists('fileScss')){
    function fileScss($files) {
		$arScss = array();
		$arError = array();
		$arFiles = explode(',', $files);
		
		foreach ($arFiles as $file){
			$file = ltrim($file,'/');
			if (is_file($file) && (strtolower(pathinfo($file,PATHINFO_EXTENSION))=='scss')) {
				$arScss[]= file_get_contents($file);
			}else{
				$arError[] = $file;
			}
		}
		return array('result' => implode("", $arScss),'error' => implode(",", $arError));
	}
}

$config['fileScss']		    = $modx->getOption($options['nameoptions'].'fileScss', $input, $options['assetsPath']. 'scss/styles.scss');
$config['fileCss'] 		    = $modx->getOption($options['nameoptions'].'fileCss', $input, $options['assetsPath']. 'css/styles.css');
$config['outputStyle']  	= $modx->getOption($options['nameoptions'].'outputStyle', $input, false);
$config['importPaths'] 	    = $modx->getOption($options['nameoptions'].'importPaths', $input, false);
$config['sourceMap'] 	    = $modx->getOption($options['nameoptions'].'sourceMap', $input, false);
$config['scssHash'] 	    = $modx->getOption($options['nameoptions'].'scssHash', $input, true);

/*SCSS Hash*/
if($config['scssHash']){
	$scss_hash = hash('md5',file_get_contents($config['fileScss']));
	$is_hash = (!file_exists($options['fileHash']) || ($scss_hash!=file_get_contents($options['fileHash'])))?true:false;
}else{
	$is_hash = true;
}

if ($is_hash){
	$arScss = fileScss($config['fileScss']);
	
	if(!empty($arScss['result'])){
		try{
			
			$compiler = new Compiler();
			
		/*Output Formatting*/
			if($config['outputStyle']) {
				$compiler->setOutputStyle(OutputStyle::COMPRESSED);
			}else{
				$compiler->setOutputStyle(OutputStyle::EXPANDED);
			}
			
		/*Import Paths*/
			if($config['importPaths']) $compiler->setImportPaths($config['importPaths']);
			
		/*Source Maps*/
			if($config['sourceMap']){
				$compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);	
				$compiler->setSourceMapOptions(array(
					'sourceMapURL'		=> $config['fileCss'].'.map',
					'sourceMapFilename' => $config['fileCss'],
					'sourceMapBasepath' => $this->options['basePath'],
					'sourceRoot'		=> '/'
				));
			}
			
			$result	= $compiler->compileString($arScss['result']);
			if (!file_exists($config['fileCss'])) {
				$path = pathinfo($config['fileCss']);
				if (!mkdir($path['dirname'], 0700, true)) {
					$modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось создать директории: '.$path['dirname']);
				}
			}
			file_put_contents($config['fileCss'], $result->getCss());
		/*Source Maps*/
			if($config['sourceMap']) file_put_contents($config['fileCss'].'.map', $result->getSourceMap());
		/*SCSS Hash*/
			if($this->options['fileHash']) file_put_contents($this->options['fileHash'], $scss_hash);
			
			if(!empty($arScss['error'])) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $arScss['error']);
		}catch (\Exception $e){
			if(!empty($arScss['error'])) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $arScss['error']);
			if($error = $e->getMessage()) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось скомпилировать: ' . $error);
		}
	}else{
		$this->modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $arScss['error'], '', 'scss');
	}	
}
return;