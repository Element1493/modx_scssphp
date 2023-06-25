<?php
$eventName = $modx->event->name;
if(!function_exists('remove_dir')){
    function remove_dir($dir){
    	if($objs = glob($dir . '/*')) foreach($objs as $obj) is_dir($obj)?remove_dir($obj):unlink($obj);
    	rmdir($dir);
    }
}
switch($eventName) {
    case 'OnBeforeCacheUpdate':
        $options['name'] 			= 'scss';
        $options['nameoptions'] 	= $options['name'].'.';
        $options['corePath'] 		= $modx->getOption($options['nameoptions'].'core_path', null, $modx->getOption('core_path'));
        $options['fileHash'] 		= $modx->getOption($options['nameoptions'].'fileHash', null, $options['corePath'] . 'cache/scss/');

        if(file_exists($options['fileHash'])) remove_dir($options['fileHash']);
    break;
}