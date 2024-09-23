<?php
header('Content-Type: application/json');
session_start();

// Thời gian sống của key (24 giờ)
define('KEY_LIFETIME', 86400); // 86400 giây = 24 giờ

// Danh sách các API để lấy key
$apis = [
    ['url' => 'https://api1.example.com/generate-key', 'apiKey' => 'YOUR_API_KEY_1'],
    ['url' => 'https://api2.example.com/generate-key', 'apiKey' => 'YOUR_API_KEY_2'],
    ['url' => 'https://api3.example.com/generate-key', 'apiKey' => 'YOUR_API_KEY_3'],
    ['url' => 'https://api4.example.com/generate-key', 'apiKey' => 'YOUR_API_KEY_4']
];

// Hàm kiểm tra và lấy key từ session
function getKeyFromSession() {
    if (isset($_SESSION['api_key']) && isset($_SESSION['key_expiry'])) {
        if (time() < $_SESSION['key_expiry']) {
            return [
                'success' => true,
                'key' => $_SESSION['api_key']
            ];
        } else {
            // Key hết hạn
            unset($_SESSION['api_key']);
            unset($_SESSION['key_expiry']);
        }
    }
    return null;
}

// Hàm để lấy key từ API
function getKeyFromApi($url, $apiKey) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return json_decode($response, true);
    } else {
        return null; // Trả về null nếu có lỗi
    }
}

// Kiểm tra key đã tồn tại trong session
$result = getKeyFromSession();

if ($result) {
    echo json_encode($result);
    exit; // Nếu có key hợp lệ, trả về ngay
}

// Nếu không có key hợp lệ, thử lấy key từ các API
foreach ($apis as $api) {
    $apiResult = getKeyFromApi($api['url'], $api['apiKey']);

    // Kiểm tra kết quả trả về
    if ($apiResult && isset($apiResult['key'])) {
        // Lưu key vào session và thời gian hết hạn
        $_SESSION['api_key'] = $apiResult['key'];
        // Đặt thời gian hết hạn
        $_SESSION['key_expiry'] = time() + KEY_LIFETIME;

        echo json_encode([
            'success' => true,
            'key' => $_SESSION['api_key']
        ]);
        exit; // Thoát khi đã lấy được key
    }
}

// Nếu không lấy được key từ bất kỳ API nào, tạo key mới
$newKey = bin2hex(random_bytes(16)); // Tạo key ngẫu nhiên 32 ký tự
$_SESSION['api_key'] = $newKey;
$_SESSION['key_expiry'] = time() + KEY_LIFETIME; // Đặt thời gian hết hạn

echo json_encode([
    'success' => true,
    'key' => $newKey
]);
?>
