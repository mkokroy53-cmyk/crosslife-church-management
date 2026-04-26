<?php
define('MAIL_FROM', 'noreply@crosslifechurch.org');
define('MAIL_FROM_NAME', 'Crosslife Church Eldoret');

function sendEmail($to, $subject, $body, $isHtml = true) {
    $headers  = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
    $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    if ($isHtml) {
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    return mail($to, $subject, $body, $headers);
}

function emailTemplate($title, $body, $footer = '') {
    return "
    <!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'></head>
    <body style='margin:0;padding:0;background:#f5f7fa;font-family:Inter,Arial,sans-serif;'>
      <div style='max-width:600px;margin:30px auto;background:white;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);'>
        <div style='background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:30px;text-align:center;'>
          <h1 style='color:white;margin:0;font-size:24px;'>⛪ Crosslife Church Eldoret</h1>
        </div>
        <div style='padding:30px;'>
          <h2 style='color:#2d3748;margin-bottom:20px;'>{$title}</h2>
          <div style='color:#555;line-height:1.7;font-size:15px;'>{$body}</div>
        </div>
        <div style='background:#f7fafc;padding:20px;text-align:center;font-size:12px;color:#999;'>
          " . ($footer ?: 'Crosslife Church Eldoret &mdash; Management System') . "
        </div>
      </div>
    </body>
    </html>";
}
?>
