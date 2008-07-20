<?php
chdir('..');
include('caos/highlight/highlight.php');
$cos = file_get_contents('tests/ant.cos');
echo <<<HTML
<html>
    <head>
        <title>Caos Test</title>
        <link rel="stylesheet" type="text/css" href="highlight.css" />
    </head>
    <body>
HTML;


echo HighlightCaos($cos);

echo <<<HTML
    </body>
</html>
HTML;
?>