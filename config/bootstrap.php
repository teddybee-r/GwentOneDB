<?php
declare(strict_types=1);

define('DOC_ROOT', realpath(__DIR__.'/..'));

include DOC_ROOT.'/config/database.php';
include DOC_ROOT.'/src/Database/CreateGwentDatabase.php';
include DOC_ROOT.'/tests/TestGwentDatabase.php';