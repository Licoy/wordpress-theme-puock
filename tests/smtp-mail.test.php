<?php

require_once __DIR__ . '/../inc/fun/mail.php';

function assert_same($expected, $actual, $message)
{
    if ($expected !== $actual) {
        fwrite(STDERR, $message . PHP_EOL);
        fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
        fwrite(STDERR, 'Actual:   ' . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}

function assert_true($actual, $message)
{
    assert_same(true, $actual, $message);
}

$config = pk_smtp_normalize_config([
    'smtp_open' => 'true',
    'smtp_ssl' => '1',
    'smtp_form' => ' sender@example.com ',
    'smtp_form_n' => ' Puock ',
    'smtp_host' => ' smtp.example.com ',
    'smtp_port' => '465',
    'smtp_u' => ' user@example.com ',
    'smtp_p' => ' secret ',
]);

assert_same([
    'open' => true,
    'ssl' => true,
    'from' => 'sender@example.com',
    'from_name' => 'Puock',
    'host' => 'smtp.example.com',
    'port' => 465,
    'username' => 'user@example.com',
    'password' => 'secret',
], $config, 'SMTP config should be normalized from option keys.');

assert_same([], pk_smtp_missing_required_fields($config), 'Complete SMTP config should not report missing fields.');

$missing = pk_smtp_missing_required_fields(pk_smtp_normalize_config([
    'smtp_open' => 'false',
    'smtp_host' => '',
]));
assert_same(['smtp_open', 'smtp_form', 'smtp_host', 'smtp_port', 'smtp_u', 'smtp_p'], $missing, 'Missing SMTP config should list required fields.');

assert_same('admin@example.com', pk_smtp_resolve_test_recipient('admin', '', 'admin@example.com', 'me@example.com'), 'Admin recipient should resolve to site admin email.');
assert_same('me@example.com', pk_smtp_resolve_test_recipient('current', '', 'admin@example.com', 'me@example.com'), 'Current recipient should resolve to current user email.');
assert_same('custom@example.com', pk_smtp_resolve_test_recipient('custom', ' custom@example.com ', 'admin@example.com', 'me@example.com'), 'Custom recipient should use sanitized input.');
assert_same('', pk_smtp_resolve_test_recipient('custom', 'not-an-email', 'admin@example.com', 'me@example.com'), 'Invalid custom recipient should be rejected.');
assert_true(pk_smtp_is_enabled_value('1'), 'String 1 should be treated as enabled.');
assert_true(!pk_smtp_is_enabled_value('false'), 'String false should be treated as disabled.');

$mailer = new class {
    public $From = '';
    public $FromName = '';
    public $Host = '';
    public $Port = 0;
    public $SMTPSecure = '';
    public $Username = '';
    public $Password = '';
    public $SMTPAuth = false;
    public $smtpEnabled = false;

    public function IsSMTP()
    {
        $this->smtpEnabled = true;
    }
};

pk_smtp_apply_config($mailer, $config);
assert_same('sender@example.com', $mailer->From, 'PHPMailer From should use normalized SMTP sender.');
assert_same('Puock', $mailer->FromName, 'PHPMailer FromName should use normalized sender name.');
assert_same('smtp.example.com', $mailer->Host, 'PHPMailer Host should use normalized SMTP host.');
assert_same(465, $mailer->Port, 'PHPMailer Port should use normalized SMTP port.');
assert_same('ssl', $mailer->SMTPSecure, 'PHPMailer SMTPSecure should follow SSL setting.');
assert_same('user@example.com', $mailer->Username, 'PHPMailer Username should use normalized SMTP account.');
assert_same('secret', $mailer->Password, 'PHPMailer Password should use normalized SMTP password.');
assert_true($mailer->SMTPAuth, 'PHPMailer SMTPAuth should be enabled.');
assert_true($mailer->smtpEnabled, 'PHPMailer SMTP mode should be enabled.');

echo "smtp mail tests passed\n";
