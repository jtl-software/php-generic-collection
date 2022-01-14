<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create();
$finder->in(['src', 'testing']);

$config = new PhpCsFixer\Config();
$config->setFinder($finder);
$config->setRules(['@PSR2' => true]);

return $config;