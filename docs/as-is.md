# WebPack As-Is

This document describes the current As-Is behavior of `op-unit-webpack`.

## Scope

This is the current technical behavior of the unit and its relationship with the `webpack` module.

## Related Framework Documents

- `asset/docs/op/invariants.md`
- `asset/docs/op/responsibility-boundaries.md`
- `asset/docs/op/common-recipes.md`

## Current Responsibility Split

The current implementation is divided like this:

- `op-unit-webpack` registers asset files, prepares the response, and generates grouped output
- `asset/module/webpack` provides the request entry points that call the unit and send the response

## Request Model

The current module-side delivery entry points include:

- `asset/module/webpack/content/js/index.php`
- `asset/module/webpack/content/css/index.php`

These endpoints:

- determine the extension from the directory name
- set MIME from that extension
- resolve the layout name
- register layout-level asset directories
- register the local directory
- call `OP::Unit()->WebPack()->Auto()` for final output

## Unit Behavior

### `Auto()`

`Auto()` has two modes:

- if arguments are given, it registers files or directories
- if no arguments are given, it prepares and outputs the grouped content

### `Register()`

`Register()`:

- switches to the caller's directory
- accepts a string or array of paths
- supports meta paths such as `asset:/...`
- supports glob expansion
- ignores hidden or underscore-prefixed files
- accepts only `js`, `css`, and `md`
- stores file paths in session-backed lists

For CSS, `import.css` is given priority by being placed at the front of the list.

### `Prepare()`

`Prepare()`:

- disables layout execution
- derives the extension from the request URL
- sets MIME from the extension
- optionally registers layout-specific asset directories from the `layout` request

### `Output()`

`Output()`:

- reads `WebPack` config
- merges admin-specific config when `OP()->isAdmin()` is true
- optionally uses APCu cache
- computes a content hash from the registered file list
- expands registered directories into file lists
- loads each file and concatenates the contents
- optionally minifies JS or CSS
- stores the final content in APCu
- echoes the final content

## Session-Based File Lists

The current file registration lists are managed through session-backed storage in the unit.

That means file grouping is stateful across the current framework-managed session context.

## Meaning of the Current Design

In the current As-Is implementation, grouped JS/CSS delivery is not just a static web-server concern.

It is a unit-driven, request-aware asset aggregation flow coordinated between:

- the `webpack` module
- `op-unit-webpack`
- layout-related directory conventions
