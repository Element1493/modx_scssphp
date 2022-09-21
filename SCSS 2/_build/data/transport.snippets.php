<?php

$snippets = array();

$tmp = array(
	'SCSS' => array(
		'name'=> 'scss',
		'properties' => array(
			array(
				'name' => 'autoprefixer',
				'desc' => 'Автопрефиксер',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => true,
			),
			array(
				'name' => 'autoprefixerVendor',
				'desc' => 'Список префиксoв',
				'type' => 'textfield',
				'options' => '',
				'value' => 'IE,Webkit,Mozilla',
			),
			array(
				'name' => 'admin',
				'desc' => 'Выполнить код для авторизованных',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => true,
			),
			array(
				'name' => 'fileScss',
				'desc' => 'Список файлов SCSS',
				'type' => 'textfield',
				'options' => '',
				'value' => '{assets_url}scss/styles.scss',
			),
			array(
				'name' => 'fileCss',
				'desc' => 'Путь к файлу CSS',
				'type' => 'textfield',
				'options' => '',
				'value' => '{assets_url}css/styles.css',
			),
			array(
				'name' => 'importPaths',
				'desc' => 'Путь к файлам импорта',
				'type' => 'textfield',
				'options' => '',
				'value' => '',
			),
			array(
				'name' => 'outputStyle',
				'desc' => 'Сжать выходной файл CSS',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => true,
			),
			array(
				'name' => 'scssHash',
				'desc' => 'Hash SCSS',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => true,
			),
			array(
				'name' => 'sourceMap',
				'desc' => 'Сгенерировать Source Map',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => false,
			)
		)
	)
);

/** @var modx $modx */
/** @var array $sources */
foreach ($tmp as $key => $value) {
    /** @var modSnippet $snippet */
    $snippet = $modx->newObject('modSnippet');
    $snippet->fromArray(array(
        'name' => $key,
        'description' => '',
        'snippet' => getSnippetContent($sources['source_core'] . '/elements/snippets/snippet.' . $value['name'] . '.php'),
        'static' => BUILD_SNIPPET_STATIC,
        'source' => 1,
        'static_file' => 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/snippet.' . $value['name'] . '.php',
    ), '', true, true);

    /** @noinspection PhpIncludeInspection */
    $snippet->setProperties($value['properties']);
    $snippets[] = $snippet;
}
unset($properties);

return $snippets;