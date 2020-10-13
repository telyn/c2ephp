[![Build Status](https://travis-ci.org/telyn/c2ephp.svg?branch=master)](https://travis-ci.org/telyn/c2ephp)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/telyn/c2ephp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/telyn/c2ephp/?branch=master)

To use:
checkout to a folder
require_once c2ephp.php

read the docs at http://telyn.github.io/c2ephp/

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

Licensing
=========

All PHP code in this repository is licensed under the MIT license, copyright of
Joey (GameFreak7744) & Telyn (a.k.a Norn Albion)

The following files are NOT copyright of Telyn or Joey and most likely it's not
strictly speaking legal for us to distribute. Distribution is done only to enable
regression testing of c2ephp. MIT license provided in LICENSE.txt

* tests/ant.cos - copyright of whoever owns Creature Labs's copyrights these days
* tests/hot chocolate.cob / .rcb - Copyright of Helen (of Helen's Bibble Directory)
* hpup.spr - I have no idea what this is or where I got it from off the top of my head.
* hstf.s16 - same
* lilo.creature - impossible for me to say, not being a lawyer.
  this is a .creature export from Creatures 3. Copyright probably depends on
  who owns the copyright on the genome, whether or not Creature Labs gave away
  the copyright for what are ultimately engine-internals stored in the CREA
  blocks, etc.
* puppy.cob - Don't know who made this
* rubber_ball.agents & rubber_ball.c16 - Copyright Creature Labs, same as ant.cos
* silky tailed fish.cob - Sprite by Sam (feralchicken), cob by Helen
