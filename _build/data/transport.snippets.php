<?php

$snippets = array();

$tmp = array(
	'SCSS' => array(
		'name'=> 'scss',
		'properties' => array(
			array(
				'name' => 'fileScss',
				'desc' => 'Путь к файлу или файлам scss, каждый новый путь к файлу прописываем через запятую.',
				'type' => 'textfield',
				'options' => '',
				'value' => '{assets_path}scss/styles.scss',
			),
			array(
				'name' => 'fileCss',
				'desc' => 'Путь к файлу или файлам scss, каждый новый путь к файлу прописываем через запятую.',
				'type' => 'textfield',
				'options' => '',
				'value' => '{assets_path}css/styles.css',
			),
			array(
				'name' => 'importPaths',
				'desc' => 'Если файлы для импорта @import находятся в другой папке, то указав путь при компиляции подхватить их из указанной папке',
				'type' => 'textfield',
				'options' => '',
				'value' => '',
			),
			array(
				'name' => 'outputStyle',
				'desc' => 'Сжать выходной файл CSS',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => false,
			),
			array(
				'name' => 'scssHash',
				'desc' => 'Компилировать в файл CSS, только после изменение одного или несколько файлов SCSS (Для снижения нагрузки рекомендую включить).',
				'type' => 'combo-boolean',
				'options' => '',
				'value' => false,
			),
			array(
				'name' => 'sourceMap',
				'desc' => 'Генерировать sourceMap, в папку с компилированным файлом CSS',
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