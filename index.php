<?php
include_once('config.php');
include_once('utils.php');

$result = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (empty($firstName) || empty($lastName) || empty($phone) || empty($email)) {
        $result['error'] = "All fields are required!";
    } else {
        $headers = [
            "token: " . Config::LEAD_TOKEN,
            "Content-Type: application/json",
        ];

        $data = json_encode([
            "firstName" => $firstName,
            "lastName" => $lastName,
            "phone" => $phone,
            "email" => $email,
            "countryCode" => "RU",
            "box_id" => "28",
            "offer_id" => "3",
            "landingUrl" => getRealReferer(),
            "ip" => getRealIp(),
            "password" => "qwerty12",
            "language" => "ru",
            "clickId" => "",
            "quizAnswers" => "",
            "custom1" => "",
            "custom2" => "",
            "custom3" => ""
        ]);

        $response = sendCurlRequest(Config::LEAD_CREATE_URL, $headers, $data, "POST");
        $json = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $result['error'] = "JSON decoding error";
        } else if (empty($json['status']) || $json['status'] !== true) {
            $result['error'] = $json['error'] ?? 'Unknown error';
        } else {
            $result['success'] = 'Data sent successfully!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sending data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <nav><a href="index.php">Form</a> | <a href="statuses.php">Lead statuses</a></nav>
        <h2 class="mt-4">Sending data</h2>
        <?php if (!empty($result['error'])): ?>
            <div class="alert alert-danger"><?= safe_string($result['error']) ?></div>
        <?php elseif (!empty($result['success'])): ?>
            <div class="alert alert-success"><?= safe_string($result['success']) ?></div>
        <?php endif; ?>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label for="firstName" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="mb-3">
                <label for="lastName" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>