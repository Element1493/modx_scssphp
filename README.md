# modx_scssphp
![version](https://img.shields.io/badge/version-1.0.0-orange "Version")
![dle](https://img.shields.io/badge/MODX-2.8.3%3C-green "MODX")
[![MIT License](https://img.shields.io/badge/license-MIT-blue "MIT License")](https://github.com/Element1493/dle_scssphp/blob/main/LICENSE)

Небольшой компонент, который автоматически компилирует файлы SCSS в файл CSS c помощью библиотеки [SCSSPHP](https://github.com/leafo/scssphp/).

### Версии
MODX 1 для MODX 2.8.3
MODX 2 для MODX 3.0.0

### Системные настройки
Ключ| Название|По умолчанию
-|-|-
**scss.admin**|Выполнить код для авторизованных|**Да**
**scss.fileCss**|Путь к файлу CSS|**{assets_path}css/styles.css**
**scss.fileScss**|Список файлов SCSS|**{assets_path}scss/styles.scss**
**scss.importPaths**|Путь к файлам импорта|
**scss.outputStyle**|Сжимать CSS?|**Да**
**scss.scssHash**|Hash SCSS|**Да**
**scss.sourceMap**|Сгенерировать Source Map|**Нет**

### Инструкция:
Для работы данного компонента достаточно вставить сниппет в шаблон:
```html
<!--MODX-->
[[!SCSS]]
<!--Fenom-->
{'!SCSS'|snippet}
```

## Ссылки:
1. [Библиотека SCSSPHP](https://github.com/leafo/scssphp/)
2. [Документация по SCSS](https://sass-lang.com/documentation)
