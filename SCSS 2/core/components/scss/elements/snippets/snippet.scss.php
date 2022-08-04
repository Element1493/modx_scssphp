<?php
if(!function_exists('parser')){
    function parser($value){
        global $modx;
        $maxIterations = (integer)$modx->getOption('parser_max_iterations', null, 10);
        $modx->getParser()->processElementTags('', $value, false, false, '[[', ']]', array(), $maxIterations);
        $modx->getParser()->processElementTags('', $value, true, true, '[[', ']]', array(), $maxIterations);
        return $value;
    }
}
if(!function_exists('createPath')){
    function createPath($filename,$permissions=0700){
        global $modx;
        if (!file_exists($filename)) {
        	$path = pathinfo($filename);
        	if (!file_exists($path['dirname'])){
        	    if(!mkdir($path['dirname'],$permissions, true)) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось создать директорию: '.$path['dirname']);
        	}
        }
    }
}
if(!function_exists('mergeFile')){
    function mergeFile($fileScss,$siteUrl,$basePath){
        global $modx;
        $resultScss = array();
        foreach (explode(',', $fileScss) as $file){
            $file = str_replace($siteUrl, "", parser($file));
        	$file = $basePath.ltrim($file,'/');
        	if (file_exists($file) && (strtolower(pathinfo($file,PATHINFO_EXTENSION))=='scss')) {
        		$resultScss[]= file_get_contents($file);
        	}else{
        	    $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $file);
        	}
        }
        return implode("", $resultScss);
    }
}

$options['name'] 			= 'scss';
$options['nameoptions'] 	= $options['name'].'.';
$options['corePath'] 		= $modx->getOption($options['nameoptions'].'core_path', $input, $modx->getOption('core_path'));
$options['componentPath'] 	= $modx->getOption($options['nameoptions'].'component_path', $input, $options['corePath'].'components/'.$options['name'] .'/');
$options['vendorPath'] 		= $modx->getOption($options['nameoptions'].'vendor_path', $input, $options['componentPath'] . 'vendor/');
$options['basePath'] 		= $modx->getOption($options['nameoptions'].'base_path', $input, $modx->getOption('base_path'));
$options['baseUrl'] 		= $modx->getOption($options['nameoptions'].'base_url', $input, $modx->getOption('base_url'));
$options['siteUrl'] 		= $modx->getOption($options['nameoptions'].'site_url', $input, $modx->getOption('site_url'));
$options['assetsUrl']  		= $modx->getOption($options['nameoptions'].'assets_url', $input, $modx->getOption('assets_url'));
$options['fileHash'] 		= $modx->getOption($options['nameoptions'].'fileHash', $input, $options['corePath'] . 'cache/default/scss/scssphp.php');
$options['admin'] 			= $modx->getOption($options['nameoptions'].'admin', $input, true);

if($options['admin']){
    $isAuth = $modx->user->isAuthenticated('mgr') && $modx->user->isAuthenticated($modx->context->key);
	if(!$isAuth && !$modx->user->isMember(array('Administrator'))) return;
}

require_once($options['vendorPath']."scss.inc.php");

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

$config['fileScss']		    = $modx->getOption($options['nameoptions'].'fileScss', $input, $options['assetsUrl']. 'scss/styles.scss');
$config['fileCss'] 		    = $modx->getOption($options['nameoptions'].'fileCss', $input, $options['assetsUrl']. 'css/styles.css');
$config['importPaths'] 	    = $modx->getOption($options['nameoptions'].'importPaths', $input, '');
$config['outputStyle']  	= $modx->getOption($options['nameoptions'].'outputStyle', $input, false);
$config['sourceMap'] 	    = $modx->getOption($options['nameoptions'].'sourceMap', $input, false);
$config['scssHash'] 	    = $modx->getOption($options['nameoptions'].'scssHash', $input, true);

$pathUrlScss = pathinfo($config['fileScss']);
$dirUrlScss = $pathUrlScss['dirname'];

$pathUrlCss = pathinfo($config['fileCss']);
$dirUrlCss = $pathUrlCss['dirname'];

$filePathCss = $options['basePath'].$config['fileCss'];
$fileUrlCss = $options['baseUrl'].$config['fileCss'];

$config['fileScss'] = mergeFile($config['fileScss'],$options['siteUrl'],$options['basePath']);

if(!empty($config['fileScss'])){
	
/*SCSS Hash*/
	if($config['scssHash']){
		$scss_hash = hash('md5',$config['fileScss']);
		$is_hash = (!file_exists($options['fileHash']) || ($scss_hash!=file_get_contents($options['fileHash'])))?true:false;
	}else{
		$is_hash = true;
	}
	if ($is_hash || !file_exists($filePathCss)){
		try{
			$compiler = new Compiler();
			
			createPath($filePathCss);
			
		/*Output Formatting*/
			if($config['outputStyle']) {
				$compiler->setOutputStyle(OutputStyle::COMPRESSED);
			}else{
				$compiler->setOutputStyle(OutputStyle::EXPANDED);
			}
			
		/*Import Paths*/
			if($config['importPaths']){
			    $importPaths = str_replace($options['siteUrl'], "", parser($config['importPaths']));
			}else{
			    $importPaths = $dirUrlScss;
			}
			$importPaths = trim($importPaths,'/');

			if(file_exists($options['basePath'].$importPaths)){
    			$compiler->setImportPaths($importPaths.'/');
			}else{
			    $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Путь к файлам импорта отсутствует: '.$importPaths);
			}
			
		/*Source Maps*/
			if($config['sourceMap']){
				$compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);	
				$compiler->setSourceMapOptions(array(
					'sourceMapURL'		=> $fileUrlCss.'.map',
					'sourceMapFilename' => $fileUrlCss,
					'sourceMapBasepath' => $options['basePath'],
					'sourceRoot'		=> $options['baseUrl']
				));
			}
			
			$result	= $compiler->compileString($config['fileScss']);
			
			file_put_contents($filePathCss, $result->getCss());
		/*Source Maps*/
			if($config['sourceMap']) file_put_contents($filePathCss.'.map', $result->getSourceMap());
		/*SCSS Hash*/
			if($config['scssHash']){
			    createPath($options['fileHash']);
				file_put_contents($options['fileHash'], $scss_hash);
			}
			
		}catch (\Exception $e){
			if($error = $e->getMessage()) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось скомпилировать: ' . $error);
		}
	}
}
return;