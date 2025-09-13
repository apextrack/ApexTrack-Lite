<?php
include 'layout/header.php';
include 'config.php';


if (!isset($_SESSION['auth_token'])) {
    die("Akses ditolak. Token otentikasi tidak ditemukan.");
}

$apiToken = $_SESSION['auth_token'];
$baseUrl = BASE_API_URL;
$limit = 50;


$activeView = $_GET['view'] ?? 'advance';

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$username = $_GET['username'] ?? 'all';
$breakdownBy = $_GET['breakdown_by'] ?? 'country_code';

$page = (int)($_GET['page'] ?? 1);

function callApi($url, $token, $params) {
    $queryString = http_build_query($params);
    $fullUrl = $url . '?' . $queryString;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return ['error' => true, 'message' => "HTTP Error: $httpCode"];
    }

    return json_decode($response, true);
}


$reportData = null;
$totalItems = 0;
$totalPages = 0;

switch ($activeView) {
    case 'advance':
        $reportData = callApi($baseUrl . '/reports/advance', $apiToken, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'username' => $username,
            'page' => $page,
            'limit' => $limit,
        ]);
        break;
    case 'leads':
        $reportData = callApi($baseUrl . '/reports/leads', $apiToken, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'username' => $username,
            'page' => $page,
            'limit' => $limit,
        ]);
        break;
    case 'clicks':
        $reportData = callApi($baseUrl . '/reports/clicks', $apiToken, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'username' => $username,
            'page' => $page,
            'limit' => $limit,
        ]);
        break;
    case 'breakdown':
        $reportData = callApi($baseUrl . '/reports/breakdown', $apiToken, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'username' => $username,
            'breakdown_by' => $breakdownBy,
            'page' => $page,
            'limit' => $limit,
        ]);
        break;
}

if (isset($reportData) && !isset($reportData['error'])) {
    $totalItems = $reportData['total'] ?? count($reportData['data']);
    $totalPages = ceil($totalItems / $limit);
}

function buildFilterUrl($view, $currentParams, $page = null) {
    $params = [
        'view' => $view,
        'start_date' => $currentParams['start_date'] ?? null,
        'end_date' => $currentParams['end_date'] ?? null,
        'username' => $currentsParams['username'] ?? null,
        'breakdown_by' => $currentParams['breakdown_by'] ?? null,
    ];
    
    if ($page !== null) {
        $params['page'] = $page;
    }

    $params = array_filter($params, function($value) {
        return $value !== null && $value !== '';
    });
    
    return '?' . http_build_query($params);
}

$advanceUrl = buildFilterUrl('advance', $_GET);
$clicksUrl = buildFilterUrl('clicks', $_GET);
$leadsUrl = buildFilterUrl('leads', $_GET);
$breakdownUrl = buildFilterUrl('breakdown', $_GET);

?>

<main class="flex-grow p-6 md:p-10 lg:p-12">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Reports</h2>

    <div class="border-b border-gray-200 mb-8">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="<?php echo htmlspecialchars($advanceUrl); ?>" class="
                <?php echo $activeView === 'advance' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>
                whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
            ">
                advance
            </a>
            <a href="<?php echo htmlspecialchars($clicksUrl); ?>" class="
                <?php echo $activeView === 'clicks' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>
                whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
            ">
                Clicks
            </a>
            <a href="<?php echo htmlspecialchars($leadsUrl); ?>" class="
                <?php echo $activeView === 'leads' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>
                whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
            ">
                Conversion
            </a>
            <a href="<?php echo htmlspecialchars($breakdownUrl); ?>" class="
                <?php echo $activeView === 'breakdown' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>
                whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
            ">
                Breakdown
            </a>
        </nav>
    </div>

    <div class="mx-auto bg-white p-8 shadow-lg">
        <form action="" method="GET" class="mb-8 p-4 bg-gray-50  shadow-inner flex flex-wrap items-end gap-4">
            <input type="hidden" name="view" value="<?php echo htmlspecialchars($activeView); ?>">
            <div class="flex-grow">
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
            </div>
            <div class="flex-grow">
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
            </div>
            <div class="flex-grow">
                <label for="username" class="block text-sm font-medium text-gray-700">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
            </div>
            <div class="flex-grow">
                <label for="breakdown_by" class="block text-sm font-medium text-gray-700">Breakdown Berdasarkan:</label>
                <select id="breakdown_by" name="breakdown_by" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                    <option value="country_code" <?php echo $breakdownBy === 'country_code' ? 'selected' : ''; ?>>Country</option>
                    <option value="device_type" <?php echo $breakdownBy === 'device_type' ? 'selected' : ''; ?>>Device</option>
                    <option value="username" <?php echo $breakdownBy === 'username' ? 'selected' : ''; ?>>Sub ID</option>
                </select>
            </div>
            <div class="self-end">
                <button type="submit" class="px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terapkan Filter</button>
            </div>
        </form>

        <?php if ($activeView === 'advance'): ?>
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">advance</h2>
                <?php if (isset($reportData['error'])): ?>
                    <p class="text-red-500"><?php echo htmlspecialchars($reportData['message']); ?></p>
                <?php elseif (empty($reportData['data'])): ?>
                    <p class="text-gray-500">Tidak ada data advance yang ditemukan.</p>
                <?php else: ?>
                    <div class="overflow-x-auto shadow-md ">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hits</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CR (%)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payout</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reportData['data'] as $row): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['hits']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['unique_clicks']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['leads']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['approved_leads']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['cr']); ?>%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo htmlspecialchars($row['total_payout']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($activeView === 'clicks'): ?>
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Clicks</h2>
                <?php if (isset($reportData['error'])): ?>
                    <p class="text-red-500"><?php echo htmlspecialchars($reportData['message']); ?></p>
                <?php elseif (empty($reportData['data'])): ?>
                    <p class="text-gray-500">Tidak ada data clicks yang ditemukan.</p>
                <?php else: ?>
                    <div class="overflow-x-auto shadow-md ">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Negara</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reportData['data'] as $row): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['created_at']))); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['ip_address']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['country_code']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['device_type']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($activeView === 'leads'): ?>
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Conversion</h2>
                <?php if (isset($reportData['error'])): ?>
                    <p class="text-red-500"><?php echo htmlspecialchars($reportData['message']); ?></p>
                <?php elseif (empty($reportData['data'])): ?>
                    <p class="text-gray-500">Tidak ada data Conversion yang ditemukan.</p>
                <?php else: ?>
                    <div class="overflow-x-auto shadow-md ">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Negara</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payout</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reportData['data'] as $row): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($row['created_at']))); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['click_subid'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['click_country_code'] ?? 'N/A'); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $row['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($row['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo htmlspecialchars($row['payout']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($activeView === 'breakdown'): ?>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Breakdown (<?php echo ucwords(str_replace('_', ' ', $breakdownBy)); ?>)</h2>
                <?php if (isset($reportData['error'])): ?>
                    <p class="text-red-500"><?php echo htmlspecialchars($reportData['message']); ?></p>
                <?php elseif (empty($reportData['data'])): ?>
                    <p class="text-gray-500">Tidak ada data breakdown yang ditemukan.</p>
                <?php else: ?>
                    <div class="overflow-x-auto shadow-md ">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo ucwords(str_replace('_', ' ', $breakdownBy)); ?></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hits</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unique Clicks</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conversions</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CR (%)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payout</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reportData['data'] as $row): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($row['dimension_value']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['hits']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['unique_clicks']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['leads']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['approved_leads']); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['cr']); ?>%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo htmlspecialchars($row['total_payout']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    
        <?php if (!isset($reportData['error']) && !empty($reportData['data']) && $totalPages > 1): ?>
            <div class="mt-8 flex justify-between items-center">
                <?php if ($page > 1): ?>
                    <a href="<?php echo htmlspecialchars(buildFilterUrl($activeView, $_GET, $page - 1)); ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Sebelumnya
                    </a>
                <?php else: ?>
                    <span class="px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 rounded-md">
                        Sebelumnya
                    </span>
                <?php endif; ?>
    
                <span class="text-sm text-gray-700">
                    Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?>
                </span>
    
                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo htmlspecialchars(buildFilterUrl($activeView, $_GET, $page + 1)); ?>" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                        Berikutnya
                    </a>
                <?php else: ?>
                    <span class="px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 border border-gray-300 rounded-md">
                        Berikutnya
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    
    </div>
</main>

<?php
include 'layout/footer.php';
?>