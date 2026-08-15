<?php

function assert_contains($needle, $haystack, $message)
{
    if (strpos($haystack, $needle) === false) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected to contain: ' . $needle . PHP_EOL);
        exit(1);
    }
}

$parser = file_get_contents(__DIR__ . '/../inc/user-agent-parse.php');
assert_contains("if (!function_exists(__NAMESPACE__ . '\\\\parse_user_agent'))", $parser, 'User-agent parser should guard namespaced parse_user_agent against vendor redeclare.');
assert_contains("if (!defined(__NAMESPACE__ . '\\\\PLATFORM'))", $parser, 'User-agent parser should guard namespaced PLATFORM constant against vendor redeclare.');

echo "user-agent parse compat tests passed\n";
