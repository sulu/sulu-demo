<?php

$config = new PhpCsFixer\Config();

$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'ordered_imports' => true,
        'concat_space' => ['spacing' => 'one'],
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_align' => false,
        'class_definition' => [
            'multi_line_extends_each_single_line' => true,
        ],
        'linebreak_after_opening_tag' => true,
        'no_php4_constructor' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'php_unit_strict' => true,
        'phpdoc_order' => true,
        'semicolon_after_instruction' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('var')
            ->exclude('public/build')
            ->exclude('public/bundles')
            ->exclude('public/uploads')
            ->in(__DIR__)
    )
    ->setCacheFile(__DIR__.'/var/.php-cs-fixer.cache');

return $config;
