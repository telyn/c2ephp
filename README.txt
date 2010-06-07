To use:
checkout to a folder
require_once c2ephp.php

The tests are command-line-only and expect to be run in the following manner:
php <file> [arguments]

pray.php
--------
Takes a PRAY file (.agent,.agents,.creature,.family) file as its only argument
Outputs a print_r of the PRAY file's structure.

pray-glst.php
-------------
Takes a .creature or .family file as its only argument.
Outputs a print_r of the creature's history.

pray-extract.php
---------------
Takes a PRAY file as defined above as its only argument
Extracts all the PRAY blocks in the file to a set of binary
files in the format NAME.BLOCKTYPE

pray-frankenstein.php
--------------------
Takes a set of binary files in the format NAME.BLOCKTYPE as its arguments. 
Outputs a pray file called 'output.pray' by using each file as a PRAY block.
