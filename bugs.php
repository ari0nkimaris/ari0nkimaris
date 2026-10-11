<?php
declare(strict_types=1);

/**
 * Bug Report Form - Web-Engine
 * Изпраща bug report директно на email.
 * НЕ изисква база данни.
 */

/* ==========================================================
   CONFIG
   ========================================================== */
$ADMIN_EMAIL = 'bugs@web-engine.info';   // ← смени с твоя email
$SITE_NAME   = 'Web-Engine';

/* ==========================================================
   HANDLE FORM
   ========================================================== */
$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* Вземи стойностите */
    $title       = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $steps       = trim((string)($_POST['steps'] ?? ''));
    $expected    = trim((string)($_POST['expected'] ?? ''));
    $actual      = trim((string)($_POST['actual'] ?? ''));
    $os          = trim((string)($_POST['os'] ?? ''));
    $browser     = trim((string)($_POST['browser'] ?? ''));
    $phpVersion  = trim((string)($_POST['php_version'] ?? ''));
    $weVersion   = trim((string)($_POST['we_version'] ?? ''));
    $email       = trim((string)($_POST['email'] ?? ''));

    /* Валидации */
    if ($title === '' || $description === '') {
        $error = 'Заглавие и описание са задължителни.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Моля, въведи валиден email.';
    } else {
        /* Изпрати email */
        $subject = "🐛 [BUG] $title";

        $body = "Нов Bug Report от $SITE_NAME\n";
        $body .= str_repeat('=', 50) . "\n\n";
        $body .= "📧 Email: $email\n";
        $body .= "📌 Заглавие: $title\n\n";
        $body .= "📝 Описание:\n$description\n\n";

        if ($steps !== '') {
            $body .= "🔁 Стъпки за възпроизвеждане:\n$steps\n\n";
        }
        if ($expected !== '') {
            $body .= "✅ Очаквано поведение:\n$expected\n\n";
        }
        if ($actual !== '') {
            $body .= "❌ Реално поведение:\n$actual\n\n";
        }

        $body .= "🖥️ Environment:\n";
        $body .= "  OS:          $os\n";
        $body .= "  Browser:     $browser\n";
        $body .= "  PHP Version: $phpVersion\n";
        $body .= "  WE Version:  $weVersion\n\n";
        $body .= str_repeat('=', 50) . "\n";
        $body .= "Изпратено: " . date('Y-m-d H:i:s') . "\n";
        $body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

        $headers = [
            'From' => "noreply@web-engine.info",
            'Reply-To' => $email,
            'X-Mailer' => 'PHP/' . phpversion(),
            'Content-Type' => 'text/plain; charset=UTF-8',
        ];

        $headerString = '';
        foreach ($headers as $key => $value) {
            $headerString .= "$key: $value\r\n";
        }

        if (@mail($ADMIN_EMAIL, $subject, $body, $headerString)) {
            $success = true;
        } else {
            $error = 'Възникна грешка при изпращане. Опитай отново или ни пиши директно.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🐛 Bug Report - <?= htmlspecialchars($SITE_NAME) ?></title>

<style>
/* ==========================================================
   RESET
   ========================================================== */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ==========================================================
   BASE
   ========================================================== */
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: radial-gradient(
        circle at 50% 0%,
        rgba(13, 110, 253, 0.12) 0%,
        rgba(139, 92, 246, 0.06) 40%,
        #0a0e1a 80%
    );
    color: #e2e8f0;
    min-height: 100vh;
    padding: 40px 20px;
    line-height: 1.6;
}

/* ==========================================================
   CONTAINER
   ========================================================== */
.container {
    max-width: 800px;
    margin: 0 auto;
}

/* ==========================================================
   HEADER
   ========================================================== */
.header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.15);
}

.header h1 {
    font-size: 2.2rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, #0d6efd, #8b5cf6);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    margin-bottom: 10px;
}

.header p {
    color: #94a3b8;
    font-size: 0.95rem;
    max-width: 500px;
    margin: 0 auto;
}

/* ==========================================================
   ALERTS
   ========================================================== */
.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    font-weight: 700;
    font-size: 0.92rem;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert.success {
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.35);
    color: #10b981;
}

.alert.error {
    background: rgba(239, 68, 68, 0.12);
    border: 1px solid rgba(239, 68, 68, 0.35);
    color: #ef4444;
}

/* ==========================================================
   FORM
   ========================================================== */
form {
    background: rgba(30, 41, 59, 0.4);
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 16px;
    padding: 32px 28px;
    backdrop-filter: blur(10px);
}

.section-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0d6efd;
    margin: 24px 0 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(148, 163, 184, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-title:first-child {
    margin-top: 0;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: 700;
    margin-bottom: 8px;
    color: #e2e8f0;
    font-size: 0.9rem;
}

.required {
    color: #ef4444;
    margin-left: 3px;
}

input,
textarea,
select {
    width: 100%;
    padding: 12px 16px;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 10px;
    color: #e2e8f0;
    font-size: 0.92rem;
    font-family: inherit;
    transition: all 0.2s ease;
}

input::placeholder,
textarea::placeholder {
    color: #64748b;
}

input:focus,
textarea:focus,
select:focus {
    outline: none;
    border-color: #0d6efd;
    background: rgba(15, 23, 42, 0.8);
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

textarea {
    min-height: 110px;
    resize: vertical;
    line-height: 1.5;
}

/* ==========================================================
   ROW (2 columns)
   ========================================================== */
.row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

/* ==========================================================
   SUBMIT BUTTON
   ========================================================== */
.submit-wrap {
    margin-top: 32px;
    text-align: center;
}

button {
    padding: 15px 40px;
    background: linear-gradient(135deg, #0d6efd, #8b5cf6);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-weight: 800;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 10px 30px -10px rgba(13, 110, 253, 0.6);
    letter-spacing: 0.02em;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 36px -10px rgba(13, 110, 253, 0.8);
}

button:active {
    transform: translateY(0);
}

/* ==========================================================
   FOOTER
   ========================================================== */
.footer {
    text-align: center;
    margin-top: 40px;
    padding-top: 24px;
    border-top: 1px solid rgba(148, 163, 184, 0.1);
    font-size: 0.82rem;
    color: #64748b;
}

.footer a {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 700;
}

.footer a:hover {
    text-decoration: underline;
}

/* ==========================================================
   MOBILE
   ========================================================== */
@media (max-width: 600px) {
    body { padding: 24px 14px; }
    .header h1 { font-size: 1.6rem; }
    form { padding: 22px 18px; }
    .row { grid-template-columns: 1fr; gap: 0; }
    button { width: 100%; justify-content: center; }
}
</style>
</head>
<body>

<div class="container">

    <!-- ==========================================================
         HEADER
         ========================================================== -->
    <div class="header">
        <h1>🐛 Bug Report</h1>
        <p>
            Намери проблем в <?= htmlspecialchars($SITE_NAME) ?>?
            Попълни формата и ние ще го разгледаме възможно най-бързо.
        </p>
    </div>

    <!-- ==========================================================
         ALERTS
         ========================================================== -->
    <?php if ($success): ?>
        <div class="alert success">
            <span>✅</span>
            <span>
                Благодарим! Твоят bug report беше изпратен успешно.
                Ще се свържем с теб на <strong><?= htmlspecialchars($email ?? '') ?></strong>.
            </span>
        </div>
    <?php elseif ($error !== ''): ?>
        <div class="alert error">
            <span>❌</span>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- ==========================================================
         FORM
         ========================================================== -->
    <form method="POST" action="">

        <!-- ===================== BASIC ===================== -->
        <h2 class="section-title">
            📝 Basic Information
        </h2>

        <div class="form-group">
            <label>Заглавие <span class="required">*</span></label>
            <input type="text"
                   name="title"
                   required
                   maxlength="200"
                   placeholder="Кратко описание на проблема"
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Email <span class="required">*</span></label>
            <input type="email"
                   name="email"
                   required
                   placeholder="твоят@email.com"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Описание на проблема <span class="required">*</span></label>
            <textarea name="description"
                      required
                      placeholder="Какво се случи? Колко често се случва?"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <!-- ===================== REPRODUCTION ===================== -->
        <h2 class="section-title">
            🔁 Reproduction
        </h2>

        <div class="form-group">
            <label>Стъпки за възпроизвеждане</label>
            <textarea name="steps"
                      placeholder="1. Отиди на...&#10;2. Кликни на...&#10;3. Виж грешката"><?= htmlspecialchars($_POST['steps'] ?? '') ?></textarea>
        </div>

        <div class="row">
            <div class="form-group">
                <label>✅ Очаквано поведение</label>
                <textarea name="expected"
                          placeholder="Какво трябваше да се случи?"><?= htmlspecialchars($_POST['expected'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>❌ Реално поведение</label>
                <textarea name="actual"
                          placeholder="Какво реално се случи?"><?= htmlspecialchars($_POST['actual'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- ===================== ENVIRONMENT ===================== -->
        <h2 class="section-title">
            🖥️ Environment
        </h2>

        <div class="row">
            <div class="form-group">
                <label>Operating System</label>
                <input type="text"
                       name="os"
                       placeholder="напр. Ubuntu 22.04"
                       value="<?= htmlspecialchars($_POST['os'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Browser</label>
                <input type="text"
                       name="browser"
                       placeholder="напр. Chrome 120"
                       value="<?= htmlspecialchars($_POST['browser'] ?? '') ?>">
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <label>PHP Version</label>
                <input type="text"
                       name="php_version"
                       placeholder="напр. 8.2"
                       value="<?= htmlspecialchars($_POST['php_version'] ?? PHP_VERSION) ?>">
            </div>

            <div class="form-group">
                <label>Web-Engine Version</label>
                <input type="text"
                       name="we_version"
                       placeholder="напр. 1.0"
                       value="<?= htmlspecialchars($_POST['we_version'] ?? '1.0') ?>">
            </div>
        </div>

        <!-- ===================== SUBMIT ===================== -->
        <div class="submit-wrap">
            <button type="submit">
                🚀 Изпрати Bug Report
            </button>
        </div>

    </form>

    <!-- ==========================================================
         FOOTER
         ========================================================== -->
    <div class="footer">
        Need help? Contact us on
        <a href="https://discord.gg/5wfHHfG5kk" target="_blank" rel="noopener">Discord</a>
        or
        <a href="mailto:support@web-engine.info">support@web-engine.info</a>
    </div>

</div>

</body>
</html>