<?php
include_once('config.php');
include_once('utils.php');

$dateFrom = isset($_GET['date_from']) && strtotime($_GET['date_from']) ? $_GET['date_from'] : Config::DEFAULT_DATE_FROM;
$dateTo = isset($_GET['date_to']) && strtotime($_GET['date_to']) ? $_GET['date_to'] : Config::DEFAULT_DATE_TO;

$dateFrom = date("Y-m-d H:i:s", strtotime($dateFrom));
$dateTo = date("Y-m-d H:i:s", strtotime($dateTo));

$page = max((int) ($_GET['page'] ?? 1), 1);
$limit = 10;
$offset = ($page - 1) * $limit;

$headers = [
    "token: " . Config::LEAD_TOKEN,
    "Content-Type: application/json",
];

$params = json_encode([
    "date_from" => $dateFrom,
    "date_to" => $dateTo,
    "page" => $page,
    "limit" => $limit,
]);

$response = sendCurlRequest(Config::LEAD_LIST_URL, $headers, $params, 'POST');
$data = json_decode($response, true);

if (!empty($data) && $data['status'] === true) {
    $statuses = $data['data'];
    $hasNextPage = count($statuses) >= $limit;
} else {
    $statuses = [];
    $hasNextPage = false;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead statuses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <nav><a href="index.php">Form</a> | <a href="statuses.php">Lead statuses</a></nav>
        <h2 class="mt-4">Lead statuses</h2>

        <form method="GET" class="mb-3">
            <label for="date_from" class="form-label">Date from</label>
            <input type="datetime-local" id="date_from" name="date_from" class="form-control w-25"
                value="<?= safe_string(date('Y-m-d\TH:i', strtotime($dateFrom))) ?>">
            <label for="date_to" class="form-label">Date to</label>
            <input type="datetime-local" id="date_to" name="date_to" class="form-control w-25"
                value="<?= safe_string(date('Y-m-d\TH:i', strtotime($dateTo))) ?>">

            <button type="submit" class="btn btn-primary mt-2">Apply</button>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>FTD</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($statuses)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No rows found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($statuses as $lead): ?>
                        <tr>
                            <td><?= safe_string($lead['id']) ?></td>
                            <td><?= safe_string($lead['email']) ?></td>
                            <td><?= safe_string($lead['status']) ?></td>
                            <td><?= safe_string($lead['ftd']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link"
                            href="?date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>&page=<?= $page - 1 ?>">
                            &lt; Previous</a>
                    </li>
                <?php endif; ?>

                <li class="page-item <?= $hasNextPage ? '' : 'disabled' ?>">
                    <a class="page-link"
                        href="?date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>&page=<?= $page + 1 ?>">Next
                        &gt;</a>
                </li>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>