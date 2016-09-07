# WordpressComposerScripts
Helpful scripts for use with Wordpress managed via Composer

## Installation
`composer require ethanclevenger91/wordpress-composer-scripts`.

Or manually add it to your `composer.json` file.

## Usage
This package provides several [scripts](https://getcomposer.org/doc/articles/scripts.md) designed to do useful things/provide useful information when managing your WordPress installation via [composer](https://getcomposer.org).

To use, assign functions to composer events, like so:

```
"scripts": {
  "pre-update-cmd": "WordpressComposerScripts\\Updates::preUpdateCommand",
  "post-update-cmd":"WordpressComposerScripts\\Updates::postUpdateCommand"
}
```

## Available Scripts

The table below provides information as to what scripts are available, what they do, and what composer event they should be attached to. All classes are under the namespace `WordpressComposerScripts`.

| Function  | Composer Event | Description |
| --------- | -------------- | ----------- |
| `Updates::preUpdateCommand`  | `pre-update-cmd`  | Write plugin information to temp file before updating. To be used with `Updates::postUpdateCommand` |
| `Updates::preUpdateCommand`  | `post-update-cmd`  | Compare plugin information post-update with plugin information pre-update and print a table with the results. To be used with `Updates::preUpdateCommand` |
