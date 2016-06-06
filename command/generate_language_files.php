<?php

include(__DIR__ . '/../bootstrap.php');

$languageBatchBo = new \Language\LanguageBatchBo();
$languageBatchBo->generateLanguageFiles();
$languageBatchBo->generateAppletLanguageXmlFiles();