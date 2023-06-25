<?php

$snippets = array();

$tmp = array(
	'SCSS' => array(
		'name'=> 'scss',
		'properties' => array()
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