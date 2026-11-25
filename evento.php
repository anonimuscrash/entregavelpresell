<?php
/* =========================================================================
   ENDPOINT CONVERSIONS API
   Recebe o ping do navegador quando a contagem chega a zero e repassa o
   evento para a Meta pelo servidor. O token nunca sai daqui.
   ========================================================================= */

require __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Use POST']);
    exit;
}

$corpo = json_decode(file_get_contents('php://input'), true) ?: [];

$eventId = isset($corpo['event_id']) ? preg_replace('/[^a-zA-Z0-9\-_]/', '', $corpo['event_id']) : '';
if ($eventId === '') {
    http_response_code(400);
    echo json_encode(['erro' => 'event_id ausente']);
    exit;
}

/* ---------- IP real do visitante (atrás de Cloudflare / proxy) ---------- */
function ipVisitante(): string {
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $chave) {
        if (!empty($_SERVER[$chave])) {
            $ip = trim(explode(',', $_SERVER[$chave])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '';
}

/* ---------- Identificadores ---------- */
$fbp = $corpo['fbp'] ?? ($_COOKIE['_fbp'] ?? '');
$fbc = $corpo['fbc'] ?? ($_COOKIE['_fbc'] ?? '');

$userData = array_filter([
    'client_ip_address' => ipVisitante(),
    'client_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'fbp'               => $fbp,
    'fbc'               => $fbc,
    // external_id ajuda o Event Match Quality. Se um dia você tiver e-mail
    // ou telefone do lead, mande também em 'em' e 'ph' (sempre sha256).
    'external_id'       => $fbp ? hash('sha256', $fbp) : '',
]);

/* ---------- Monta o evento ---------- */
$evento = [
    'event_name'       => EVENTO_NOME,
    'event_time'       => time(),
    'event_id'         => $eventId,
    'event_source_url' => $corpo['url'] ?? '',
    'action_source'    => 'website',
    'user_data'        => $userData,
    'custom_data'      => [
        'currency'     => EVENTO_MOEDA,
        'value'        => (float) EVENTO_VALOR,
        'content_name' => EVENTO_CONTEUDO,
        'content_ids'  => [EVENTO_CONTENT_ID],
        'content_type' => 'product',
    ],
];

$payload = ['data' => [$evento], 'access_token' => CAPI_TOKEN];
if (TEST_EVENT_CODE !== '') {
    $payload['test_event_code'] = TEST_EVENT_CODE;
}

/* ---------- Envia ---------- */
$url = 'https://graph.facebook.com/' . GRAPH_VERSION . '/' . DATASET_ID . '/events';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 8,
]);
$resposta = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$erroCurl = curl_error($ch);
curl_close($ch);

if (LOG_ATIVO) {
    @file_put_contents(
        __DIR__ . '/../capi.log',
        date('c') . " | {$status} | {$eventId} | " . ($resposta ?: $erroCurl) . PHP_EOL,
        FILE_APPEND
    );
}

http_response_code($status === 200 ? 200 : 502);
echo $resposta ?: json_encode(['erro' => $erroCurl]);
