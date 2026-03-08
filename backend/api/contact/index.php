<?php
/**
 * JSM Digital — POST /api/contact
 *
 * Accepts a general contact message, stores it in the DB,
 * and sends notification emails to the company and the sender.
 */

require_once __DIR__ . '/../../helpers/functions.php';

setCorsHeaders();
ensureSchema();
checkRateLimit('contact');

/* ── Parse & validate input ─────────────────────────────────── */
$body = getJsonBody();

$name    = cleanString($body['name']    ?? '', 255);
$email   = cleanString($body['email']   ?? '', 320);
$subject = cleanString($body['subject'] ?? '', 500);
$message = cleanString($body['message'] ?? '', 5000);

$errors = [];

if ($name === '') {
    $errors['name'] = 'Name is required.';
}
if ($email === '' || !isValidEmail($email)) {
    $errors['email'] = 'A valid email address is required.';
}
if ($subject === '') {
    $errors['subject'] = 'Subject is required.';
}
if (mb_strlen($message) < 10) {
    $errors['message'] = 'Message must be at least 10 characters.';
}

if (!empty($errors)) {
    jsonError('Validation failed.', 422, $errors);
}

/* ── Persist to database ─────────────────────────────────────── */
$pdo = getDbConnection();

$stmt = $pdo->prepare(
    'INSERT INTO contacts (name, email, subject, message, ip_address)
     VALUES (:name, :email, :subject, :message, :ip)'
);
$stmt->execute([
    ':name'    => $name,
    ':email'   => $email,
    ':subject' => $subject,
    ':message' => $message,
    ':ip'      => getClientIp(),
]);

/* ── Send notification email to the company ──────────────────── */
$notifyHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Contact Message</title></head>
<body style="font-family:Inter,Arial,sans-serif;background:#f4f4f4;padding:40px 0;">
  <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#4f46e5,#06b6d4);padding:32px 40px;">
      <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">New Contact Message</h1>
      <p style="color:rgba(255,255,255,.8);margin:4px 0 0;font-size:14px;">JSM Digital Website</p>
    </div>
    <div style="padding:32px 40px;">
      <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <tr><td style="padding:10px 0;color:#6b7280;width:110px;vertical-align:top;font-weight:600;">Name</td><td style="padding:10px 0;color:#111827;">{$name}</td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Email</td><td style="padding:10px 0;color:#111827;"><a href="mailto:{$email}" style="color:#4f46e5;">{$email}</a></td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Subject</td><td style="padding:10px 0;color:#111827;">{$subject}</td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Message</td><td style="padding:10px 0;color:#111827;white-space:pre-wrap;">{$message}</td></tr>
      </table>
    </div>
    <div style="background:#f9fafb;padding:20px 40px;font-size:12px;color:#9ca3af;">
      Received at: {$_SERVER['REQUEST_TIME_FLOAT']} UTC &nbsp;|&nbsp; IP: {$_SERVER['REMOTE_ADDR']}
    </div>
  </div>
</body>
</html>
HTML;

sendMail(MAIL_TO_ADDRESS, "New Contact: {$subject}", $notifyHtml);

/* ── Send auto-reply to the sender ───────────────────────────── */
$autoReplyHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Thanks for contacting JSM Digital</title></head>
<body style="font-family:Inter,Arial,sans-serif;background:#f4f4f4;padding:40px 0;">
  <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#4f46e5,#06b6d4);padding:32px 40px;">
      <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">Thanks, {$name}!</h1>
      <p style="color:rgba(255,255,255,.8);margin:4px 0 0;font-size:14px;">We've received your message.</p>
    </div>
    <div style="padding:32px 40px;">
      <p style="color:#374151;font-size:15px;line-height:1.7;">
        Thank you for reaching out to <strong>JSM Digital</strong>. We've received your message and a member of our team will get back to you within <strong>1 business day</strong>.
      </p>
      <div style="background:#f9fafb;border-left:4px solid #4f46e5;border-radius:4px;padding:16px 20px;margin:24px 0;font-size:14px;color:#6b7280;">
        <strong style="color:#111827;">Your message:</strong><br><br>
        <em style="white-space:pre-wrap;">{$message}</em>
      </div>
      <p style="color:#6b7280;font-size:14px;line-height:1.7;">
        If you need immediate assistance, please email us directly at <a href="mailto:hello@jsmdigital.com" style="color:#4f46e5;">hello@jsmdigital.com</a>.
      </p>
    </div>
    <div style="background:#f9fafb;padding:20px 40px;text-align:center;font-size:12px;color:#9ca3af;">
      &copy; JSM Digital &nbsp;|&nbsp; <a href="https://jsmdigital.com" style="color:#9ca3af;">jsmdigital.com</a>
    </div>
  </div>
</body>
</html>
HTML;

sendMail($email, 'We received your message — JSM Digital', $autoReplyHtml);

jsonSuccess('Message sent successfully. We\'ll be in touch within 1 business day.');
