<?php

if (!file_exists(__DIR__ . '/src'))
{
    exit(0);
}

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                   => true,
        '@Symfony:risky'             => true,
        'php_unit_dedicate_assert'   => ['target' => '5.6'],
        'array_syntax'               => ['syntax' => 'short'],
        'array_indentation'          => true,
        'binary_operator_spaces'     => [
            'operators' => [
                '=>' => 'align_single_space',
            ],
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'fopen_flags'                   => false,
        'protected_to_private'          => false,
        'native_constant_invocation'    => true,
        'single_quote'                  => true,
        'single_space_around_construct' => [
            'constructs_followed_by_a_single_space' => [
                'abstract',
                'as',
                'attribute',
                'break',
                'case',
                'catch',
                'class',
                'clone',
                'comment',
                'const',
                'const_import',
                'continue',
                'do',
                'echo',
                'else',
                'elseif',
                'enum',
                'extends',
                'final',
                'finally',
                'for',
                'foreach',
                'function',
                'function_import',
                'global',
                'goto',
                'if',
                'implements',
                'include',
                'include_once',
                'instanceof',
                'insteadof',
                'interface',
                'match',
                'named_argument',
                // 'namespace', // 兼容性移除
                'new',
                'open_tag_with_echo',
                'php_doc',
                'php_open',
                'print',
                'private',
                'protected',
                'public',
                'readonly',
                'require',
                'require_once',
                'return',
                'static',
                'switch',
                'throw',
                'trait',
                'try',
                'type_colon',
                'use',
                'use_lambda',
                'use_trait',
                'var',
                'while',
                'yield',
                'yield_from',
            ],
        ],
        'control_structure_continuation_position' => [
            'position' => 'next_line',
        ],
        'curly_braces_position'            => [
            'control_structures_opening_brace' => 'next_line_unless_newline_at_signature_end',
        ],
        'no_superfluous_phpdoc_tags'   => false,
        'single_line_comment_style'    => false,
        'combine_nested_dirname'       => false,
        'backtick_to_shell_exec'       => false,
        'visibility_required'          => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude(__DIR__ . '/vendor')
            ->in(__DIR__ . '/.github')
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/examples')
            ->in(__DIR__ . '/tests')
            ->append([__FILE__])
    )
;
