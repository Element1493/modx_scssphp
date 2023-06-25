<?php
$options['name'] 			            = 'scss';
$options['nameoptions'] 	            = $options['name'].'.';

$options['corePath']                    = (!empty($core_path))?$core_path:$modx->getOption($options['nameoptions'].'core_path', null, $modx->getOption('core_path'));
$options['componentPath'] 	            = (!empty($component_path))?$component_path:$modx->getOption($options['nameoptions'].'component_path', null, $options['corePath'].'components/'.$options['name'] .'/');
$options['vendorPath'] 		            = (!empty($vendor_path))?$vendor_path:$modx->getOption($options['nameoptions'].'vendor_path', null, $options['componentPath'] . 'vendor/');
$options['basePath'] 		            = (!empty($base_path))?$base_path:$modx->getOption($options['nameoptions'].'base_path', null, $modx->getOption('base_path'));
$options['baseUrl'] 		            = (!empty($base_url))?$base_url:$modx->getOption($options['nameoptions'].'base_url', null, $modx->getOption('base_url'));
$options['siteUrl'] 		            = (!empty($site_url))?$site_url:$modx->getOption($options['nameoptions'].'site_url', null, $modx->getOption('site_url'));
$options['assetsUrl']  		            = (!empty($assets_url))?$assets_url:$modx->getOption($options['nameoptions'].'assets_url', null, $modx->getOption('assets_url'));
$options['admin'] 			            = (!empty($admin))?$admin:$modx->getOption($options['nameoptions'].'admin', null, true);
$options['fileScss']                    = (!empty($fileScss))?$fileScss:$modx->getOption($options['nameoptions'].'fileScss', null, $options['assetsUrl']. 'scss/styles.scss');
$options['fileCss']                     = (!empty($fileCss))?$fileCss:$modx->getOption($options['nameoptions'].'fileCss', null, $options['assetsUrl']. 'css/styles.css');
$options['importPaths']                 = (!empty($importPaths))?$importPaths:$modx->getOption($options['nameoptions'].'importPaths', null, '');
$options['outputStyle']  	            = (!empty($outputStyle))?$outputStyle:$modx->getOption($options['nameoptions'].'outputStyle', null, false);
$options['sourceMap'] 	                = (!empty($sourceMap))?$sourceMap:$modx->getOption($options['nameoptions'].'sourceMap', null, false);
$options['scssHash'] 	                = (!empty($scssHash))?scssHash:$modx->getOption($options['nameoptions'].'scssHash', null, true);
$options['autoprefixer'] 	            = (!empty($autoprefixer))?$autoprefixer:$modx->getOption($options['nameoptions'].'autoprefixer', null, true);
$options['autoprefixerVendor']          = (!empty($autoprefixerVendor))?$autoprefixerVendor:$modx->getOption($options['nameoptions'].'autoprefixerVendor', null, 'IE,Webkit,Mozilla');
$options['fileHash']                    = (!empty($fileHash))?$fileHash:$modx->getOption($options['nameoptions'].'fileHash', null, $options['corePath'] . 'cache/scss/'.$options['fileScss'].'.txt');

$options['pathUrlScss']                 = pathinfo($options['fileScss']);
$options['dirUrlScss']                  = $options['pathUrlScss']['dirname'];
$options['pathUrlCss']                  = pathinfo($options['fileCss']);
$options['dirUrlCss']                   = $options['pathUrlCss']['dirname'];
$options['filePathCss']                 = $options['basePath'].$options['fileCss'];
$options['fileUrlCss']                  = $options['baseUrl'].$options['fileCss'];

$options['map']['sourceMapURL']		    = $options['fileUrlCss'].'.map';
$options['map']['sourceMapFilename']    = $options['fileUrlCss'];
$options['map']['sourceMapBasepath']    = $options['basePath'];
$options['map']['sourceRoot']           = $options['baseUrl'];

require_once($options['vendorPath'].'autoload.php' );

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use Padaliyajay\PHPAutoprefixer\Autoprefixer;

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
        	    if(!mkdir($path['dirname'],$permissions, true)) $modx->log(1, 'SCSS - Не удалось создать директорию: '.$path['dirname']);
        	}
        }
    }
}
if(!function_exists('scssPaths')){
    function scssPaths($options){
        global $modx;
        $RESULT = array();
        if(!empty($options['fileScss'])){
            foreach (explode(',', $options['fileScss']) as $FILE){
                $FILE = str_replace($options['siteUrl'], "", parser($FILE));
                $FILE = str_replace('{assets_url}', $options['assetsUrl'], $FILE);
            	$FILE = $options['basePath'].ltrim($FILE,'/');
            	if (file_exists($FILE) && (strtolower(pathinfo($FILE,PATHINFO_EXTENSION))==$options['name'])) {
            		$RESULT[]= file_get_contents($FILE);
            	}else{
            	    $modx->log(1, 'SCSS - Не удалось найти файл: ' . $FILE);
            	}
            }
            return implode("", $RESULT);
        }else{
            return false;
        }
    }
}
if(!function_exists('importPaths')){
    function importPaths($options){
        global $modx;
        $RESULT = array();
		if(!empty($options['importPaths'])){
			foreach (explode(",", $options['importPaths']) as $FILE){
			    $FILE = str_replace($options['siteUrl'], "", parser($FILE));
			    $FILE = str_replace('{assets_url}', $options['assetsUrl'], $FILE);
			    $FILE = str_replace('{assets_url}', $options['assetsUrl'], $FILE);
        	    $FILE = $options['basePath'].ltrim($FILE,'/');
				if(file_exists($FILE)){
					$RESULT[] = $FILE;
				}else{
				    $modx->log(1,'SCSS - Не удалось найти путь к импорту: '.$FILE);
				}
			}
		}else{
			if(file_exists($options['basePath'].trim($options['dirUrlScss'],'/'))){
    			$RESULT[] = $importPaths.'/';
			}
		}
		return $RESULT;
	}
}
if(!function_exists('isHASH')){
    function isHASH($options){
        if($options['scssHash']){
    		return (!file_exists($options['fileHash']) || ( hash('md5',$options['fileScss']) != file_get_contents($options['fileHash'])) )?true:false;
    	}else{
    		return true;
    	}
    }
}
if(!function_exists('setHASH')){
    function setHASH($options){
        if($options['scssHash']){
		    createPath($options['fileHash']);
			file_put_contents($options['fileHash'], hash('md5',$options['fileScss']));
			return true;
		}else{
		    return false;
		}
    }
}
if(!function_exists('autoprefixer')){
    function autoprefixer($style,$options){
        global $modx;
        if($options['autoprefixer'] && !empty($style)){
            try{
    		    $autoprefixer = new Autoprefixer($style);
    		    if($options['autoprefixerVendor']){
    		        $vendors = array();
    			    $arVendors = explode(",", strtolower($options['autoprefixerVendor']));
    			    if (in_array("ie", $arVendors))         $vendors[] = \Padaliyajay\PHPAutoprefixer\Vendor\IE::class;
    			    if (in_array("webkit", $arVendors))     $vendors[] = \Padaliyajay\PHPAutoprefixer\Vendor\Webkit::class;
    			    if (in_array("mozilla", $arVendors))    $vendors[] = \Padaliyajay\PHPAutoprefixer\Vendor\Mozilla::class;
    			    if (in_array("opera", $arVendors))      $vendors[] = \Padaliyajay\PHPAutoprefixer\Vendor\Opera::class;
    				
    			    $autoprefixer->setVendors($vendors);
    		    }
    		    return ($options['outputStyle'])?$autoprefixer->compile(false):$autoprefixer->compile();
            }catch(\Exception $e){
                $modx->log(1, 'SCSS - Не удалось добавить префиксов: '.$e);
            }
		}
		return $style;
    }
}

if($options['admin']){
    $isAuth = $modx->user->isAuthenticated('mgr') && $modx->user->isAuthenticated($modx->context->key);
	if(!$isAuth && !$modx->user->isMember(array('Administrator'))) return;
}

if(scssPaths($options)){
    $options['fileScss'] = scssPaths($options);
	if (isHASH($options) || !file_exists($options['filePathCss'])){
		try{
			$compiler = new Compiler();
			createPath($options['filePathCss']);
			if($options['outputStyle']) {
				$compiler->setOutputStyle(OutputStyle::COMPRESSED);
			}else{
				$compiler->setOutputStyle(OutputStyle::EXPANDED);
			}
			
			$compiler->setImportPaths(importPaths($options));
			
			if($options['sourceMap']){
				$compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);	
				$compiler->setSourceMapOptions($options['map']);
			}
			
			$result	= $compiler->compileString($options['fileScss']);
			
			$css = autoprefixer($result->getCss(),$options);
			
			file_put_contents($options['filePathCss'], $css);
			if($options['sourceMap']) file_put_contents($options['filePathCss'].'.map', $result->getSourceMap());

			setHASH($options);
		}catch (\Exception $e){
			if($error = $e->getMessage()) $modx->log(modX::LOG_LEVEL_ERROR, 'SCSS - Не удалось скомпилировать: ' . $error);
		}
	}
}
return;