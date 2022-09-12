<?php
$eventName = $modx->event->name;
switch($eventName) {
    case 'OnBeforeCacheUpdate':
        $options['name'] 			= 'scss';
        $options['nameoptions'] 	= $options['name'].'.';
        $options['corePath'] 		= $modx->getOption($options['nameoptions'].'core_path', null, $modx->getOption('core_path'));
        $options['fileHash'] 		= $modx->getOption($options['nameoptions'].'fileHash', null, $options['corePath'] . 'cache/default/scss/scssphp.php');
        
        $config['scssHash'] 	    = $modx->getOption($options['nameoptions'].'scssHash', null, true);
        
        if($config['scssHash']){
            if(!file_exists($options['fileHash']) || ($scss_hash!=file_get_contents($options['fileHash']))){
                unlink($options['fileHash']);
            }
        }
    break;
}