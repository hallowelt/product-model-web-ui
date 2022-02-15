# WebBoMTool

## Installation
Execute

    composer require hallowelt/webbomtool dev-REL1_35
within MediaWiki root or add `hallowelt/webbomtool` to the
`composer.json` file of your project

## Activation
Add

    wfLoadExtension( 'WebBoMTool' );
to your `LocalSettings.php` or the appropriate `settings.d/` file.