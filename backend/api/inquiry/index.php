<?php
/**
 * JSM Digital — POST /api/inquiry
 *
 * Accepts a full project inquiry, stores it in the DB,
 * and sends notification emails to the company and the sender.
 */

require_once __DIR__ . '/../../helpers/functions.php';

setCorsHeaders();
ensureSchema();
checkRateLimit('inquiry');

/* ── Allowed enum values ────────────────────────────────────── */
const ALLOWED_PROJECT_TYPES = [
    'web-app', 'mobile-app', 'custom-software',
    'e-commerce', 'api', 'ui-ux', 'other',
];

const ALLOWED_BUDGETS = [
    'under-5k', '5k-15k', '15k-50k',
    '50k-100k', 'over-100k', 'not-sure', '',
];

const ALLOWED_TIMELINES = [
    'asap', '1-2-months', '3-6-months',
    '6-plus-months', 'flexible', '',
];

/* ── Parse & validate input ─────────────────────────────────── */
$body = getJsonBody();

$name         = cleanString($body['name']         ?? '', 255);
$email        = cleanString($body['email']        ?? '', 320);
$company      = cleanString($body['company']      ?? '', 255);
$phone        = cleanString($body['phone']        ?? '', 50);
$project_type = cleanString($body['project_type'] ?? '', 100);
$budget       = cleanString($body['budget']       ?? '', 100);
$timeline     = cleanString($body['timeline']     ?? '', 100);
$description  = cleanString($body['description']  ?? '', 8000);

$errors = [];

if ($name === '') {
    $errors['name'] = 'Name is required.';
}
if ($email === '' || !isValidEmail($email)) {
    $errors['email'] = 'A valid email address is required.';
}
if (!isAllowedValue($project_type, ALLOWED_PROJECT_TYPES)) {
    $errors['project_type'] = 'Please select a valid project type.';
}
if (!isAllowedValue($budget, ALLOWED_BUDGETS)) {
    $errors['budget'] = 'Invalid budget value.';
}
if (!isAllowedValue($timeline, ALLOWED_TIMELINES)) {
    $errors['timeline'] = 'Invalid timeline value.';
}
if (mb_strlen($description) < 20) {
    $errors['description'] = 'Please describe your project in at least 20 characters.';
}

// Validate phone loosely — only printable chars, no script injection
if ($phone !== '' && !preg_match('/^[\d\s\+\-\(\)\.]{0,50}$/', $phone)) {
    $errors['phone'] = 'Invalid phone number format.';
}

if (!empty($errors)) {
    jsonError('Validation failed.', 422, $errors);
}

/* ── Persist to database ─────────────────────────────────────── */
$pdo = getDbConnection();

$stmt = $pdo->prepare(
    'INSERT INTO inquiries
        (name, email, company, phone, project_type, budget, timeline, description, ip_address)
     VALUES
        (:name, :email, :company, :phone, :project_type, :budget, :timeline, :description, :ip)'
);
$stmt->execute([
    ':name'         => $name,
    ':email'        => $email,
    ':company'      => $company,
    ':phone'        => $phone,
    ':project_type' => $project_type,
    ':budget'       => $budget,
    ':timeline'     => $timeline,
    ':description'  => $description,
    ':ip'           => getClientIp(),
]);

/* ── Build human-readable labels for emails ──────────────────── */
$typeLabels = [
    'web-app'         => 'Web Application',
    'mobile-app'      => 'Mobile App (iOS / Android)',
    'custom-software' => 'Custom Software / System',
    'e-commerce'      => 'E-Commerce Platform',
    'api'             => 'API Development / Integration',
    'ui-ux'           => 'UI/UX Design',
    'other'           => 'Other',
];

$budgetLabels = [
    'under-5k'  => 'Under $5,000',
    '5k-15k'    => '$5,000 – $15,000',
    '15k-50k'   => '$15,000 – $50,000',
    '50k-100k'  => '$50,000 – $100,000',
    'over-100k' => '$100,000+',
    'not-sure'  => 'Not sure yet',
    ''          => 'Not specified',
];

$timelineLabels = [
    'asap'          => 'As soon as possible',
    '1-2-months'    => 'Within 1–2 months',
    '3-6-months'    => '3–6 months',
    '6-plus-months' => 'More than 6 months',
    'flexible'      => 'Flexible',
    ''              => 'Not specified',
];

$typeLabel     = $typeLabels[$project_type]   ?? $project_type;
$budgetLabel   = $budgetLabels[$budget]        ?? $budget;
$timelineLabel = $timelineLabels[$timeline]    ?? $timeline;
$companyRow    = $company !== '' ? "<tr><td style='padding:10px 0;color:#6b7280;width:130px;vertical-align:top;font-weight:600;'>Company</td><td style='padding:10px 0;color:#111827;'>{$company}</td></tr>" : '';
$phoneRow      = $phone   !== '' ? "<tr><td style='padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;'>Phone</td><td style='padding:10px 0;color:#111827;'>{$phone}</td></tr>" : '';

/* ── Notify the company ──────────────────────────────────────── */
$notifyHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Project Inquiry</title></head>
<body style="font-family:Inter,Arial,sans-serif;background:#f4f4f4;padding:40px 0;">
  <div style="max-width:640px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#4f46e5,#06b6d4);padding:32px 40px;">
      <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">🚀 New Project Inquiry</h1>
      <p style="color:rgba(255,255,255,.8);margin:4px 0 0;font-size:14px;">JSM Digital Website</p>
    </div>
    <div style="padding:32px 40px;">
      <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <tr><td style="padding:10px 0;color:#6b7280;width:130px;vertical-align:top;font-weight:600;">Name</td><td style="padding:10px 0;color:#111827;">{$name}</td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Email</td><td style="padding:10px 0;"><a href="mailto:{$email}" style="color:#4f46e5;">{$email}</a></td></tr>
        {$companyRow}
        {$phoneRow}
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Project Type</td><td style="padding:10px 0;color:#111827;">{$typeLabel}</td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Budget</td><td style="padding:10px 0;color:#111827;">{$budgetLabel}</td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Timeline</td><td style="padding:10px 0;color:#111827;">{$timelineLabel}</td></tr>
        <tr><td style="padding:10px 0;color:#6b7280;vertical-align:top;font-weight:600;">Description</td><td style="padding:10px 0;color:#111827;white-space:pre-wrap;">{$description}</td></tr>
      </table>
    </div>
    <div style="background:#f9fafb;padding:20px 40px;font-size:12px;color:#9ca3af;">
      IP: {$_SERVER['REMOTE_ADDR']}
    </div>
  </div>
</body>
</html>
HTML;

sendMail(MAIL_TO_ADDRESS, "New Project Inquiry from {$name} — {$typeLabel}", $notifyHtml);

/* ── Auto-reply to the client ────────────────────────────────── */
$autoReplyHtml = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Your project inquiry — JSM Digital</title></head>
<body style="font-family:Inter,Arial,sans-serif;background:#f4f4f4;padding:40px 0;">
  <div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
    <div style="background:linear-gradient(135deg,#4f46e5,#06b6d4);padding:32px 40px;">
      <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">We've Got Your Inquiry!</h1>
      <p style="color:rgba(255,255,255,.8);margin:4px 0 0;font-size:14px;">JSM Digital</p>
    </div>
    <div style="padding:32px 40px;">
      <p style="color:#374151;font-size:15px;line-height:1.7;">
        Hi <strong>{$name}</strong>,<br><br>
        Thank you for submitting your project inquiry to <strong>JSM Digital</strong>. We're excited to learn more about your <strong>{$typeLabel}</strong> project and will review your requirements carefully.
      </p>
      <p style="color:#374151;font-size:15px;line-height:1.7;">
        One of our senior consultants will reach out to you at <a href="mailto:{$email}" style="color:#4f46e5;">{$email}</a> within <strong>1 business day</strong> to schedule a free consultation call.
      </p>
      <div style="background:#f9fafb;border-left:4px solid #4f46e5;border-radius:4px;padding:16px 20px;margin:24px 0;font-size:14px;">
        <strong style="color:#111827;">Inquiry summary:</strong><br><br>
        <span style="color:#6b7280;">Project Type:</span> <span style="color:#111827;">{$typeLabel}</span><br>
        <span style="color:#6b7280;">Budget:</span> <span style="color:#111827;">{$budgetLabel}</span><br>
        <span style="color:#6b7280;">Timeline:</span> <span style="color:#111827;">{$timelineLabel}</span>
      </div>
      <p style="color:#6b7280;font-size:14px;line-height:1.7;">
        In the meantime, feel free to browse our <a href="https://jsmdigital.com/portfolio" style="color:#4f46e5;">portfolio</a> or email us at <a href="mailto:hello@jsmdigital.com" style="color:#4f46e5;">hello@jsmdigital.com</a>.
      </p>
    </div>
    <div style="background:#f9fafb;padding:20px 40px;text-align:center;font-size:12px;color:#9ca3af;">
      &copy; JSM Digital &nbsp;|&nbsp; <a href="https://jsmdigital.com" style="color:#9ca3af;">jsmdigital.com</a>
    </div>
  </div>
</body>
</html>
HTML;

sendMail($email, 'We received your project inquiry — JSM Digital', $autoReplyHtml);

jsonSuccess('Inquiry submitted successfully. We\'ll be in touch within 1 business day.');
