<pre>

<?php

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

require 'PHPUnit/Autoload.php';
$_SERVER['argv']=array();
PHPUnit_TextUI_Command::main();
