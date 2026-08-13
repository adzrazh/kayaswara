<?php
/**
 * Public Invoice Download (Token-based)
 * URL: /invoice/{token}
 */
// Token bisa datang dari $_GET['token'] (via .htaccess rewrite)
// atau dari path URL langsung sebagai fallback
$token = '';
if (!empty($_GET['token'])) {
    $token = preg_replace('/[^a-f0-9]/', '', $_GET['token']);
} else {
    $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
    foreach ($segments as $i => $seg) {
        if ($seg === 'invoice' && isset($segments[$i + 1])) {
            $token = preg_replace('/[^a-f0-9]/', '', $segments[$i + 1]);
            break;
        }
    }
}
if (empty($token)) {
    http_response_code(404);
    exit('Invoice tidak ditemukan.');
}

$inv = fetch("SELECT * FROM invoices WHERE token = ?", [$token]);
if (!$inv || $inv['status'] === 'cancelled') {
    http_response_code(404);
    exit('Invoice tidak ditemukan atau sudah dibatalkan.');
}

$order = fetch("SELECT * FROM orders WHERE id = ?", [$inv['order_id']]);
if (!$order) {
    http_response_code(404);
    exit('Data pesanan tidak ditemukan.');
}

// Determine PDF file path
$pdfDir  = defined('ROOT_PATH') ? ROOT_PATH . '/invoices' : __DIR__ . '/../invoices';
$pdfFile = $pdfDir . '/' . preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '', $inv['invoice_number'])) . '.pdf';

// Regenerate if missing
if (!file_exists($pdfFile)) {
    try {
        generateInvoicePDF($inv, $order);
    } catch (Exception $e) {
        http_response_code(500);
        exit('PDF tidak dapat digenerate. Hubungi admin.');
    }
}

if (!file_exists($pdfFile)) {
    http_response_code(404);
    exit('File invoice tidak ditemukan.');
}

// Serve the PDF
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $inv['invoice_number'] . '.pdf"');
header('Content-Length: ' . filesize($pdfFile));
header('Cache-Control: private, max-age=3600');
readfile($pdfFile);
exit;
