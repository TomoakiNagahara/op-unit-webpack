# WebPack As-Is

この文書は、`op-unit-webpack` の現行 As-Is 挙動を説明します。

## 対象範囲

これは、unit の現行 technical behavior と、`webpack` module との関係を説明する文書です。

## 関連 framework 文書

- `asset/docs/op/invariants.ja.md`
- `asset/docs/op/responsibility-boundaries.ja.md`
- `asset/docs/op/common-recipes.ja.md`

## 現行の責務分担

現行実装の責務分担は次です。

- `op-unit-webpack` は asset file の registration、response の preparation、grouped output の生成を担当する
- `asset/module/webpack` は request entry point を提供し、unit を呼び出して response を送信する

## request model

現行の module 側 delivery entry point には次があります。

- `asset/module/webpack/content/js/index.php`
- `asset/module/webpack/content/css/index.php`

これらの endpoint は次を行います。

- directory 名から extension を決める
- その extension から MIME を設定する
- layout 名を解決する
- layout 単位の asset directory を register する
- local directory を register する
- 最後に `OP::Unit()->WebPack()->Auto()` を呼んで output する

## unit の挙動

### `Auto()`

`Auto()` には 2 つの mode があります。

- 引数がある場合は file や directory を register する
- 引数が無い場合は grouped content の prepare と output を行う

### `Register()`

`Register()` は次を行います。

- 呼び出し元の directory へ移動する
- string または array の path を受け取る
- `asset:/...` のような meta path を扱える
- glob 展開を扱える
- hidden file や `_` で始まる file を無視する
- `js`, `css`, `md` だけを対象にする
- session-backed list に file path を保存する

CSS では `import.css` が優先され、list の先頭に置かれます。

### `Prepare()`

`Prepare()` は次を行います。

- layout 実行を無効化する
- request URL から extension を導出する
- extension から MIME を設定する
- `layout` request があれば layout-specific asset directory を register する

### `Output()`

`Output()` は次を行います。

- `WebPack` config を読む
- `OP()->isAdmin()` が true の場合は admin-specific config を merge する
- 必要に応じて APCu cache を使う
- register 済み file list から content hash を計算する
- register 済み directory を file list に展開する
- 各 file を読み込んで content を連結する
- 必要に応じて JS または CSS を minify する
- 最終 content を APCu に保存する
- 最終 content を echo する

## session ベースの file list

現行の file registration list は、unit 内の session-backed storage で管理されています。

つまり file grouping は、framework 管理下の current session context に対して stateful です。

## 現行設計の意味

現行 As-Is 実装では、grouped JS/CSS delivery は単なる静的 web-server の仕事ではありません。

これは次をまたぐ、unit 駆動かつ request-aware な asset aggregation flow です。

- `webpack` module
- `op-unit-webpack`
- layout に関連する directory convention
