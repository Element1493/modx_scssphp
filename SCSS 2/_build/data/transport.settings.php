<?php

$settings = array();

$tmp = array(
	'autoprefixer' => array(	
        'xtype'    =>'combo-boolean',
        'value'    =>true,
        'area'     =>'scss_autoprefixer'
    ),
	'autoprefixerVendor' => array(	
        'xtype'    =>'textfield',
        'value'    =>'IE,Webkit,Mozilla',
        'area'     =>'scss_autoprefixer'
    ),
	'admin' => array(	
        'xtype'    =>'combo-boolean',
        'value'    =>true,
        'area'     =>'scss_main'
    ),
	'fileScss' => array(	
        'xtype'    =>'textfield',
        'value'    =>'{assets_url}scss/styles.scss',
        'area'     =>'scss_main'
    ),
    'fileCss' => array(	
        'xtype'    =>'textfield',
        'value'    =>'{assets_url}css/styles.css',
        'area'     =>'scss_main'
    ),
	'importPaths' => array(	
        'xtype'    =>'textfield',
        'value'    =>false,
        'area'     =>'scss_main'
    ),
	'outputStyle' => array(	
        'xtype'    =>'combo-boolean',
        'value'    =>true,
        'area'     =>'scss_main'
    ),
	'scssHash' => array(	
        'xtype'    =>'combo-boolean',
        'value'    =>true,
        'area'     =>'scss_main'
    ),
	'sourceMap' => array(	
        'xtype'    =>'combo-boolean',
        'value'    =>false,
        'area'     =>'scss_main'
    )
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => PKG_NAME_LOWER.'.' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
