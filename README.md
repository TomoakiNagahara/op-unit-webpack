Unit of WebPack
===

# Overview

 Multiple files are combined into one file and output.

# Usage

```
//  Add to One by One.
OP()->Unit('WebPack')->Auto('file_name.js');
OP()->Unit('WebPack')->Auto('file_name.css');

//  Add to Bulk.
OP()->Unit('WebPack')->Auto('file_name.js','file_name.css');
```

## Output the content from the registered file path

 Call from webpack directory. For example, `app/:webpack/index.php`.

```php
OP()->Unit('WebPack')->Out('css');
```

## For html.

```php
//  Get a hash of content. For browser cache.
$hash = Unit('WebPack')->FileContentHash('css');

//  Build URL.
$href = ConvertURL("app:/webpack/css/?hash={$hash}");

//  Display for html.
echo '<link type="text/css" href="'.$href.'" rel="stylesheet">';
```
