<?php
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
	if(!$isAuth && !$modx->user->isMember('Administrator')) return;
}

require_once($options['vendorPath']."scss.inc.php");

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

if(!function_exists('parser')){
    function parser($value){
        global $modx;
        $maxIterations = (integer)$modx->getOption('parser_max_iterations', null, 10);
        $modx->getParser()->processElementTags('', $value, false, false, '[[', ']]', array(), $maxIterations);
        $modx->getParser()->processElementTags('', $value, true, true, '[[', ']]', array(), $maxIterations);
        return $value;
    }
}

$config['fileScss']		    = $modx->getOption($options['nameoptions'].'fileScss', $input, $options['assetsUrl']. 'scss/styles.scss');
$config['fileCss'] 		    = $modx->getOption($options['nameoptions'].'fileCss', $input, $options['assetsUrl']. 'css/styles.css');
$config['importPaths'] 	    = $modx->getOption($options['nameoptions'].'importPaths', $input, '');
$config['outputStyle']  	= $modx->getOption($options['nameoptions'].'outputStyle', $input, false);
$config['sourceMap'] 	    = $modx->getOption($options['nameoptions'].'sourceMap', $input, false);
$config['scssHash'] 	    = $modx->getOption($options['nameoptions'].'scssHash', $input, true);

$resultScss = array();
foreach (explode(',', $config['fileScss']) as $file){
    $file = str_replace($options['siteUrl'], "", parser($file));
	$file = $options['basePath'].ltrim($file,'/');
	if (file_exists($file) && (strtolower(pathinfo($file,PATHINFO_EXTENSION))=='scss')) {
		$resultScss[]= file_get_contents($file);
	}else{
	    $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось найти файл: ' . $file);
	}
}
$config['fileScss'] = implode("", $resultScss);


if(!empty($config['fileScss'])){
	
/*SCSS Hash*/
	if($config['scssHash']){
		$scss_hash = hash('md5',$config['fileScss']);
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
			if($config['importPaths']){
			    $importPaths = str_replace($options['siteUrl'], "", parser($config['importPaths']));
                $importPaths = trim($importPaths,'/');
            	$compiler->setImportPaths($importPaths.'/');
			}
			
		/*Source Maps*/
			if($config['sourceMap']){
				$compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);	
				$compiler->setSourceMapOptions(array(
					'sourceMapURL'		=> $options['baseUrl'].$config['fileCss'].'.map',
					'sourceMapFilename' => $options['baseUrl'].$config['fileCss'],
					'sourceMapBasepath' => $options['basePath'],
					'sourceRoot'		=> $options['baseUrl']
				));
			}
			
			$result	= $compiler->compileString($config['fileScss']);
			if (!file_exists($options['basePath'].$config['fileCss'])) {
				$path = pathinfo($options['basePath'].$config['fileCss']);
				if (!mkdir($path['dirname'], 0700, true)) {
					$modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось создать директорию: '.$path['dirname']);
				}
			}
			file_put_contents($options['basePath'].$config['fileCss'], $result->getCss());
		/*Source Maps*/
			if($config['sourceMap']) file_put_contents($options['basePath'].$config['fileCss'].'.map', $result->getSourceMap());
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
			
		}catch (\Exception $e){
			if($error = $e->getMessage()) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось скомпилировать: ' . $error);
		}
	}
}
return;