# modx_scssphp
![version](https://img.shields.io/badge/version-1.0.0-orange "Version")
![dle](https://img.shields.io/badge/MODX-3.0.0%3C-green "MODX")
[![MIT License](https://img.shields.io/badge/license-MIT-blue "MIT License")](https://github.com/Element1493/dle_scssphp/blob/main/LICENSE)

Небольшой компонент, который автоматически компилирует файлы SCSS в файл CSS c помощью библиотеки [SCSSPHP](https://github.com/leafo/scssphp/).

### Системные настройки
Ключ| Название|По умолчанию
-|-|-
**scss.admin**|Выполнить код для авторизованных|**Да**
**scss.fileCss**|Путь к CSS|**{assets_path}css/styles.css**
**scss.fileScss**|Пути к SCSS|**{assets_path}scss/styles.scss**
**scss.importPaths**|Пути импорта|
**scss.outputStyle**|Сжать выходной файл CSS|**Да**
**scss.scssHash**|Hash SCSS|**Да**
**scss.sourceMap**|Генерировать sourceMap|**Нет**

### Инструкция:
Для работы данного компонента достаточно вставить сниппет в шаблон:
```html
[[!SCSS]]
```
**Fenom**
```html
{'!SCSS'|snippet}
```

## Ссылки:
1. [Библиотека SCSSPHP](https://github.com/leafo/scssphp/)
2. [Документация по SCSS](https://sass-lang.com/documentation)
