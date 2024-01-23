Unit of WebPack
===

# Overview

 Multiple files are combined into one file and output.

# Usage

```php
//  Add to One by One.
OP()->WebPack()->Auto('file_name.js');
OP()->WebPack()->Auto('file_name.css');

//  Add in Bulk.
OP()->WebPack()->Auto('file_name.js', 'file_name.css');

//  Add of directory.
OP()->WebPack()->Auto('./');

//  You can use meta-path.
OP()->WebPack()->Auto('asset:/webpack/css/');
```

 Output the source code from the registered file path.

```php
OP()->WebPack()->Auto();
```

## For HTML

 Get a hash of packed source code. That use for browser cache.

```php
$hash = OP()->WebPack()->Hash('js');
```

```html
<script src="/js/?hash=<?= $hash ?>"></script>
```
