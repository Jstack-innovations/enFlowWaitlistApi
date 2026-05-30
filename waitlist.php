<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'resendMail.php'; // adjust path if needed

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid data"]);
    exit;
}

$host = getenv("MYSQLHOST");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$db   = getenv("MYSQLDATABASE");
$port = (int) getenv("MYSQLPORT");

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed: " . $conn->connect_error
    ]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO waitlist 
(full_name, business_name, business_type, city, phone, email, challenge, volume, ready_to_adopt) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssssss",
    $input['fullName'],
    $input['businessName'],
    $input['businessType'],
    $input['city'],
    $input['phone'],
    $input['email'],
    $input['challenge'],
    $input['volume'],
    $input['readyToAdopt']
);

if ($stmt->execute()) {

    // ── TELEGRAM NOTIFICATION ──
    $botToken = getenv("TELEGRAM_BOT_TOKEN");
    $chatId   = getenv("TELEGRAM_CHAT_ID");

    $message = "
🍽️ *New Waitlist Signup!*

👤 *Name:* {$input['fullName']}
🏢 *Business:* {$input['businessName']}
📂 *Type:* {$input['businessType']}
📍 *City:* {$input['city']}
📞 *Phone:* {$input['phone']}
📧 *Email:* {$input['email']}
📊 *Monthly Volume:* {$input['volume']}
⚡ *Ready in 14 days:* {$input['readyToAdopt']}
💬 *Challenge:* {$input['challenge']}
    ";

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $payload = http_build_query([
        "chat_id"    => $chatId,
        "text"       => $message,
        "parse_mode" => "Markdown"
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    // ── END TELEGRAM ──


    // ── WAITLIST CONFIRMATION EMAIL ──
    $firstName = explode(' ', trim($input['fullName']))[0]; // extract first name

    $emailBody = str_replace('{{first_name}}', htmlspecialchars($firstName), <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>You're on the Enflow Waitlist</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');
  * { box-sizing: border-box; }
  body { margin: 0; padding: 0; background-color: #eae6de; font-family: 'DM Sans', Arial, sans-serif; -webkit-font-smoothing: antialiased; }
  @media only screen and (max-width: 620px) {
    .email-wrapper { padding: 16px 12px !important; }
    .main-card { border-radius: 16px !important; }
    .hero-block { padding: 40px 24px 36px !important; }
    .body-block { padding: 36px 24px 32px !important; }
    .footer-block { padding: 24px 20px !important; }
    .hero-title { font-size: 26px !important; line-height: 34px !important; }
    .step-cell { display: block !important; width: 100% !important; padding: 0 0 16px 0 !important; }
    .cta-btn { width: 90% !important; }
    .feature-cell { display: block !important; width: 100% !important; padding: 0 0 12px 0 !important; }
  }
</style>
</head>
<body style="margin:0; padding:0; background-color:#eae6de;">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#eae6de">
<tr>
<td class="email-wrapper" align="center" style="padding: 40px 16px;">
  <table class="main-card" width="600" border="0" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;border-radius:20px;overflow:hidden;box-shadow:0 4px 6px rgba(26,24,20,0.04),0 20px 60px rgba(26,24,20,0.10);">

    <!-- HERO -->
    <tr>
      <td class="hero-block" align="center" style="background:#1a1814;padding:52px 48px 48px;position:relative;">
        <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom:32px;position:relative;z-index:1;">
          <tr><td align="center">
            <img src="https:/getenflowai.online/assets/logo.png" alt="EnflowAI" width="140" style="display:block;height:auto;max-height:48px;width:auto;max-width:140px;object-fit:contain;" />
          </td></tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" width="48" style="margin:0 auto 28px;position:relative;z-index:1;">
          <tr><td height="1" style="background:linear-gradient(90deg,transparent,rgba(201,168,112,0.6),transparent);line-height:1px;font-size:1px;">&nbsp;</td></tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" style="margin:0 auto 24px;position:relative;z-index:1;">
          <tr><td align="center" style="background:rgba(160,120,72,0.12);border:1px solid rgba(201,168,112,0.28);border-radius:100px;padding:7px 18px;font-family:'DM Sans',Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.14em;text-transform:uppercase;color:#c9a870;">
            ✦ &nbsp; Early Access Confirmed
          </td></tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-bottom:32px;position:relative;z-index:1;">
          <tr><td align="center" style="border-radius:14px;overflow:hidden;line-height:0;">
            <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&q=80&auto=format&fit=crop" alt="Premium Restaurant" width="504" style="display:block;width:100%;max-width:504px;height:auto;border-radius:14px;filter:brightness(0.75) saturate(0.9);" />
          </td></tr>
        </table>
        <h1 class="hero-title" style="margin:0 0 14px;font-family:'DM Serif Display',Georgia,serif;font-size:34px;font-weight:400;line-height:1.2;color:rgba(255,255,255,0.90);letter-spacing:-0.01em;position:relative;z-index:1;">
          You're on the list,<br><em style="font-style:italic;color:#c9a870;">{{first_name}}</em>
        </h1>
        <p style="margin:0 auto;font-family:'DM Sans',Arial,sans-serif;font-size:15px;line-height:1.75;color:rgba(255,255,255,0.42);font-weight:300;max-width:380px;position:relative;z-index:1;">
          Welcome to the future of food business operations. We're thrilled to have you as one of our early access members.
        </p>
      </td>
    </tr>

    <!-- BODY -->
    <tr>
      <td class="body-block" style="background:#ffffff;padding:48px 48px 44px;">
        <p style="margin:0 0 10px;font-family:'DM Sans',Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.16em;text-transform:uppercase;color:#a07848;">What's next for you</p>
        <h2 style="margin:0 0 20px;font-family:'DM Serif Display',Georgia,serif;font-size:24px;font-weight:400;line-height:1.4;color:#1a1814;">Here's what happens from here</h2>
        <p style="margin:0 0 32px;font-family:'DM Sans',Arial,sans-serif;font-size:15px;line-height:1.85;color:#6b6560;font-weight:300;">
          You're among a select group of food businesses getting first access to EnflowAI. We'll be in touch within <strong style="color:#2c2924;font-weight:500;">24 hours</strong> to confirm your spot and walk you through the next steps.
        </p>

        <!-- Steps -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:40px;">
          <tr>
            <td class="step-cell" valign="top" width="33%" style="padding-right:12px;">
              <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center" style="background:#faf9f7;border:1px solid rgba(26,24,20,0.08);border-radius:14px;padding:22px 16px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.22);margin:0 auto 12px;line-height:36px;text-align:center;font-family:'DM Serif Display',Georgia,serif;font-size:16px;color:#a07848;">1</div>
                <p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;font-weight:500;color:#1a1814;line-height:1.5;text-align:center;">Confirmation<br>Email</p>
                <p style="margin:6px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:11.5px;color:#9e9589;line-height:1.6;text-align:center;font-weight:300;">Within 24 hrs</p>
              </td></tr></table>
            </td>
            <td class="step-cell" valign="top" width="33%" style="padding:0 6px;">
              <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center" style="background:#faf9f7;border:1px solid rgba(26,24,20,0.08);border-radius:14px;padding:22px 16px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.22);margin:0 auto 12px;line-height:36px;text-align:center;font-family:'DM Serif Display',Georgia,serif;font-size:16px;color:#a07848;">2</div>
                <p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;font-weight:500;color:#1a1814;line-height:1.5;text-align:center;">Onboarding<br>Call</p>
                <p style="margin:6px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:11.5px;color:#9e9589;line-height:1.6;text-align:center;font-weight:300;">15-min setup</p>
              </td></tr></table>
            </td>
            <td class="step-cell" valign="top" width="33%" style="padding-left:12px;">
              <table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td align="center" style="background:#faf9f7;border:1px solid rgba(26,24,20,0.08);border-radius:14px;padding:22px 16px;">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.22);margin:0 auto 12px;line-height:36px;text-align:center;font-family:'DM Serif Display',Georgia,serif;font-size:16px;color:#a07848;">3</div>
                <p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;font-weight:500;color:#1a1814;line-height:1.5;text-align:center;">Get Access<br>Credentials</p>
                <p style="margin:6px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:11.5px;color:#9e9589;line-height:1.6;text-align:center;font-weight:300;">Start same day</p>
              </td></tr></table>
            </td>
          </tr>
        </table>

        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:36px;"><tr><td height="1" style="background:rgba(26,24,20,0.07);line-height:1px;font-size:1px;">&nbsp;</td></tr></table>

        <p style="margin:0 0 10px;font-family:'DM Sans',Arial,sans-serif;font-size:11px;font-weight:600;letter-spacing:0.16em;text-transform:uppercase;color:#a07848;">What's included</p>
        <h2 style="margin:0 0 20px;font-family:'DM Serif Display',Georgia,serif;font-size:22px;font-weight:400;line-height:1.4;color:#1a1814;">Your early access perks</h2>

        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-bottom:28px;"><tr><td align="center" style="border-radius:12px;overflow:hidden;line-height:0;">
          <img src="https://images.unsplash.com/photo-1551218808-94e220e084d2?w=800&q=80&auto=format&fit=crop" alt="Restaurant Technology" width="504" style="display:block;width:100%;max-width:504px;height:200px;object-fit:cover;border-radius:12px;filter:brightness(0.88) saturate(0.85);" />
        </td></tr></table>

        <!-- Features -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:36px;">
          <tr><td style="padding-bottom:14px;"><table border="0" cellspacing="0" cellpadding="0"><tr>
            <td valign="top" style="padding-right:14px;padding-top:2px;"><div style="width:22px;height:22px;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.25);border-radius:50%;text-align:center;line-height:22px;font-size:10px;color:#a07848;">✓</div></td>
            <td><p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1814;line-height:1.5;">Full platform access from day one</p><p style="margin:4px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;color:#9e9589;font-weight:300;line-height:1.6;">Smart automation, real-time insights, and universal integrations.</p></td>
          </tr></table></td></tr>
          <tr><td style="padding-bottom:14px;"><table border="0" cellspacing="0" cellpadding="0"><tr>
            <td valign="top" style="padding-right:14px;padding-top:2px;"><div style="width:22px;height:22px;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.25);border-radius:50%;text-align:center;line-height:22px;font-size:10px;color:#a07848;">✓</div></td>
            <td><p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1814;line-height:1.5;">10-day free trial, no credit card needed</p><p style="margin:4px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;color:#9e9589;font-weight:300;line-height:1.6;">Experience everything EnflowAI offers before committing.</p></td>
          </tr></table></td></tr>
          <tr><td style="padding-bottom:14px;"><table border="0" cellspacing="0" cellpadding="0"><tr>
            <td valign="top" style="padding-right:14px;padding-top:2px;"><div style="width:22px;height:22px;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.25);border-radius:50%;text-align:center;line-height:22px;font-size:10px;color:#a07848;">✓</div></td>
            <td><p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1814;line-height:1.5;">Priority support &amp; exclusive feature previews</p><p style="margin:4px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;color:#9e9589;font-weight:300;line-height:1.6;">Be first to test new tools and shape the product roadmap.</p></td>
          </tr></table></td></tr>
          <tr><td><table border="0" cellspacing="0" cellpadding="0"><tr>
            <td valign="top" style="padding-right:14px;padding-top:2px;"><div style="width:22px;height:22px;background:rgba(160,120,72,0.10);border:1px solid rgba(160,120,72,0.25);border-radius:50%;text-align:center;line-height:22px;font-size:10px;color:#a07848;">✓</div></td>
            <td><p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1814;line-height:1.5;">Private early access community</p><p style="margin:4px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:13px;color:#9e9589;font-weight:300;line-height:1.6;">Connect with other food business operators and co-build with us.</p></td>
          </tr></table></td></tr>
        </table>

        <!-- CTA -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:36px;">
          <tr><td align="center">
            <a href="#" class="cta-btn" style="display:inline-block;background:#1a1814;color:#ede9e1;text-decoration:none;padding:16px 36px;border-radius:10px;font-family:'DM Sans',Arial,sans-serif;font-size:14.5px;font-weight:500;letter-spacing:0.02em;">
              Join the Early Community &nbsp;→
            </a>
          </td></tr>
        </table>

        <!-- Brand strip -->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr><td style="background:#1a1814;border-radius:14px;padding:28px 30px;text-align:center;">
            <img src="https://getenflowai.online/assets/logo.png" alt="" style="display:block;margin:0 auto 14px;width:150px;height:auto;opacity:0.6;" />
            <p style="margin:0;font-family:'DM Serif Display',Georgia,serif;font-size:16px;font-style:italic;color:rgba(255,255,255,0.70);line-height:1.7;">"Built for Nigeria's food industry — adaptive, intelligent, and designed to scale with you."</p>
            <p style="margin:12px 0 0;font-family:'DM Sans',Arial,sans-serif;font-size:11px;letter-spacing:0.12em;text-transform:uppercase;color:#c9a870;font-weight:500;">The EnflowAI Team</p>
          </td></tr>
        </table>
      </td>
    </tr>

    <!-- FOOTER -->
    <tr>
      <td class="footer-block" align="center" style="background:#f5f2ec;border-top:1px solid rgba(26,24,20,0.07);padding:32px 40px;">
        <table border="0" cellspacing="0" cellpadding="0" style="margin:0 auto 20px;">
          <tr><td align="center">
            <img src="https://getenflowai.online/assets/icon.png" alt="EnflowAI" width="90" style="display:block;height:auto;max-height:30px;width:auto;max-width:90px;opacity:0.45;" />
          </td></tr>
        </table>
        <p style="margin:0 0 6px;font-family:'DM Sans',Arial,sans-serif;font-size:12.5px;line-height:1.75;color:#b8b0a4;font-weight:300;">You're receiving this because you joined the EnflowAI waitlist.</p>
        <p style="margin:0 0 16px;font-family:'DM Sans',Arial,sans-serif;font-size:12.5px;line-height:1.75;color:#b8b0a4;font-weight:300;">Questions? Reply to this email or reach us at <a href="mailto:hello@enflowai.online" style="color:#a07848;text-decoration:none;">hello@enflowai.online</a></p>
        <table border="0" cellspacing="0" cellpadding="0" style="margin:0 auto 16px;">
          <tr>
            <td style="padding:0 8px;"><a href="#" style="font-family:'DM Sans',Arial,sans-serif;font-size:12px;color:#b8b0a4;text-decoration:none;">Privacy Policy</a></td>
            <td style="color:#d9d3c7;font-size:12px;">·</td>
            <td style="padding:0 8px;"><a href="#" style="font-family:'DM Sans',Arial,sans-serif;font-size:12px;color:#b8b0a4;text-decoration:none;">Unsubscribe</a></td>
            <td style="color:#d9d3c7;font-size:12px;">·</td>
            <td style="padding:0 8px;"><a href="#" style="font-family:'DM Sans',Arial,sans-serif;font-size:12px;color:#b8b0a4;text-decoration:none;">Terms</a></td>
          </tr>
        </table>
        <p style="margin:0;font-family:'DM Sans',Arial,sans-serif;font-size:11.5px;color:#c8c2b8;font-weight:300;letter-spacing:0.02em;">© 2026 EnflowAI. All rights reserved.</p>
      </td>
    </tr>

  </table>
</td>
</tr>
</table>
</body>
</html>
HTML);

    sendEmail(
        $input['email'],
        "You're on the EnflowAI Waitlist 🎉",
        $emailBody
    );
    // ── END EMAIL ──

    echo json_encode(["success" => true]);

} else {
    echo json_encode(["success" => false]);
}

$stmt->close();
$conn->close();
?>
