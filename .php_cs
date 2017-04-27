<?php

$finder = PhpCsFixer\Finder::create()
	->in(__DIR__ . '/src');

$rules = [
	'@Symfony' => true,
	// why would anyone put braces on different lines
	'braces' => ['position_after_functions_and_oop_constructs' => 'same'],
	'function_declaration' => ['closure_function_spacing' => 'none'],
	// overwrite some Symfony rules
	'concat_space' => ['spacing' => 'one'],
	'phpdoc_align' => false,
	'phpdoc_no_empty_return' => false,
	'phpdoc_summary' => false,
	'trailing_comma_in_multiline_array' => false,
	// additional rules
	'array_syntax' => ['syntax' => 'short'],
	'phpdoc_order' => true,
];

return PhpCsFixer\Config::create()
	->setRules($rules)
	->setIndent("\t")
	->setFinder($finder);
