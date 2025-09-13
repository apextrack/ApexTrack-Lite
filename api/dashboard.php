<?php
session_start();

require_once '../config.php';

header('Content-Type: application/json');

$token = $_SESSION['auth_token'] ?? null;
if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Token tidak valid atau kedaluwarsa.']);
    exit();
}

$endpoint = '/dashboard';

function fetchData($url, $token)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        "Authorization: Bearer {$token}"
    ]);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception("Kesalahan saat mengambil data: " . $curlError);
    }
    
    if ($httpStatus === 401) {
        throw new Exception("Unauthorized. Token tidak valid atau kedaluwarsa.");
    }
    
    if ($httpStatus === 404) {
        throw new Exception("Endpoint tidak ditemukan: {$url}.");
    }
    
    if ($httpStatus !== 200) {
        $responseData = json_decode($response, true);
        $errorMessage = $responseData['error'] ?? "Gagal memuat data. Status: {$httpStatus}.";
        throw new Exception("Gagal memuat data. Respons: {$errorMessage}");
    }
    
    $decodedResponse = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Respons API tidak valid: " . json_last_error_msg());
    }

    return $decodedResponse;
}

try {
    $dashboardData = fetchData(BASE_API_URL . $endpoint, $token);
    echo json_encode($dashboardData);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>