<?php

$php_main_email = "shihabhossain1.me@gmail.com";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo "<span class='contact_error'>Invalid request method.</span>";
	exit;
}

function sanitize_text($value, $max_length = 3000) {
	$value = trim((string) $value);
	$value = str_replace(array("\r", "\n"), ' ', $value);
	if (function_exists('mb_substr')) {
		return mb_substr($value, 0, $max_length);
	}
	return substr($value, 0, $max_length);
}

$php_name = sanitize_text($_POST['ajax_name'] ?? '', 120);
$php_email = filter_var(trim((string) ($_POST['ajax_email'] ?? '')), FILTER_SANITIZE_EMAIL);
$php_phone = sanitize_text($_POST['ajax_phone'] ?? '', 40);
$php_subject = sanitize_text($_POST['ajax_subject'] ?? 'Project Inquiry from Portfolio', 140);
$php_message = trim((string) ($_POST['ajax_message'] ?? ''));
$php_website = trim((string) ($_POST['ajax_website'] ?? ''));

if ($php_website !== '') {
	echo "";
	exit;
}

if ($php_name === '' || $php_email === '' || $php_message === '') {
	echo "<span class='contact_error'>Please complete all required fields.</span>";
	exit;
}

if (!filter_var($php_email, FILTER_VALIDATE_EMAIL)) {
	echo "<span class='contact_error'>Please enter a valid email address.</span>";
	exit;
}

$host = isset($_SERVER['HTTP_HOST']) ? preg_replace('/[^a-z0-9\.\-]/i', '', $_SERVER['HTTP_HOST']) : '';
if ($host === '' || strpos($host, '.') === false) {
	$host = 'portfolio.local';
}
$from_email = "no-reply@" . $host;
$email_subject = "Portfolio Inquiry: " . $php_subject;

$php_headers = "MIME-Version: 1.0\r\n";
$php_headers .= "Content-type: text/html; charset=UTF-8\r\n";
$php_headers .= "From: Portfolio Contact <" . $from_email . ">\r\n";
$php_headers .= "Reply-To: " . $php_name . " <" . $php_email . ">\r\n";

$safe_name = htmlspecialchars($php_name, ENT_QUOTES, 'UTF-8');
$safe_email = htmlspecialchars($php_email, ENT_QUOTES, 'UTF-8');
$safe_phone = htmlspecialchars($php_phone, ENT_QUOTES, 'UTF-8');
$safe_subject = htmlspecialchars($php_subject, ENT_QUOTES, 'UTF-8');
$safe_message = nl2br(htmlspecialchars($php_message, ENT_QUOTES, 'UTF-8'));

$php_sendmessage = "<div style='font-family:Arial,sans-serif;color:#1f2937;line-height:1.6;padding:24px;'>"
	. "<h2 style='margin:0 0 16px;'>New Portfolio Contact Message</h2>"
	. "<p><strong>Name:</strong> " . $safe_name . "</p>"
	. "<p><strong>Email:</strong> " . $safe_email . "</p>"
	. "<p><strong>Phone:</strong> " . $safe_phone . "</p>"
	. "<p><strong>Subject:</strong> " . $safe_subject . "</p>"
	. "<p><strong>Message:</strong><br>" . $safe_message . "</p>"
	. "</div>";

if (mail($php_main_email, $email_subject, $php_sendmessage, $php_headers)) {
	echo "";
	exit;
}

echo "<span class='contact_error'>Message could not be sent right now. Please email me directly.</span>";

?>
