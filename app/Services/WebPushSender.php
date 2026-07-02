<?php

namespace App\Services;

class WebPushSender
{
    private string $publicKey;
    private string $privateKey;
    private string $subject;

    public function __construct()
    {
        $this->publicKey  = config('services.vapid.public_key');
        $this->privateKey = config('services.vapid.private_key');
        $this->subject    = config('services.vapid.subject');
    }

    /**
     * Kirim push notification ke satu subscriber.
     * Kembalikan HTTP status code (201 = sukses, 410 = subscription expired).
     */
    public function send(string $endpoint, string $p256dh, string $auth, array $payload): int
    {
        $origin  = parse_url($endpoint, PHP_URL_SCHEME) . '://' . parse_url($endpoint, PHP_URL_HOST);
        $jwt     = $this->buildVapidJwt($origin);
        $headers = ['Authorization: vapid t=' . $jwt . ',k=' . $this->publicKey, 'TTL: 86400'];

        $body = '';
        $bodyHeaders = [];
        if (!empty($payload)) {
            $encrypted = $this->encryptPayload(json_encode($payload), $p256dh, $auth);
            if ($encrypted !== null) {
                $body        = $encrypted['body'];
                $bodyHeaders = $encrypted['headers'];
            }
        }

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array_merge($headers, $bodyHeaders),
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $code;
    }

    // ──────────────────────────────────────────────────────────────
    // VAPID JWT (ES256)
    // ──────────────────────────────────────────────────────────────
    private function buildVapidJwt(string $audience): string
    {
        $header  = $this->b64u(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $payload = $this->b64u(json_encode([
            'aud' => $audience,
            'exp' => time() + 3600,
            'sub' => $this->subject,
        ]));
        $signingInput = $header . '.' . $payload;

        $rawKey = $this->b64uDecode($this->privateKey);
        $pem    = $this->rawPrivateKeyToPem($rawKey);
        $pk     = openssl_pkey_get_private($pem);

        openssl_sign($signingInput, $derSig, $pk, OPENSSL_ALGO_SHA256);

        return $signingInput . '.' . $this->b64u($this->derToRaw($derSig));
    }

    // ──────────────────────────────────────────────────────────────
    // Payload encryption — RFC 8291 (aes128gcm)
    // ──────────────────────────────────────────────────────────────
    private function encryptPayload(string $content, string $p256dhBase64Url, string $authBase64Url): ?array
    {
        $recipientKey = $this->b64uDecode($p256dhBase64Url); // 65-byte uncompressed P-256 point
        $authSecret   = $this->b64uDecode($authBase64Url);   // 16 bytes

        // Generate ephemeral key pair
        $ephKey = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        if (!$ephKey) return null;

        $ephDetails = openssl_pkey_get_details($ephKey);
        $ephPublic  = "\x04"
            . str_pad($ephDetails['ec']['x'], 32, "\x00", STR_PAD_LEFT)
            . str_pad($ephDetails['ec']['y'], 32, "\x00", STR_PAD_LEFT);

        // ECDH shared secret
        $recipientPKey = openssl_pkey_get_public($this->rawPublicKeyToPem($recipientKey));
        if (!$recipientPKey) return null;

        $ecdhSecret = openssl_pkey_derive($recipientPKey, $ephKey);
        if (!$ecdhSecret) return null;

        // IKM via HKDF (RFC 8291 §3.1)
        $keyInfo = "WebPush: info\x00" . $recipientKey . $ephPublic;
        $ikm     = hash_hkdf('sha256', $ecdhSecret, 32, $keyInfo, $authSecret);

        // Salt, CEK, nonce (RFC 8291 §3.3)
        $salt  = random_bytes(16);
        $cek   = hash_hkdf('sha256', $ikm, 16, "Content-Encoding: aes128gcm\x00", $salt);
        $nonce = hash_hkdf('sha256', $ikm, 12, "Content-Encoding: nonce\x00", $salt);

        // Encrypt: content + padding delimiter \x02
        $plaintext  = $content . "\x02";
        $ciphertext = openssl_encrypt($plaintext, 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16);
        if ($ciphertext === false) return null;

        // aes128gcm body: salt(16) + rs(4) + idlen(1) + keyid(65) + ciphertext + tag(16)
        $body = $salt . pack('N', 4096) . "\x41" . $ephPublic . $ciphertext . $tag;

        return [
            'body'    => $body,
            'headers' => [
                'Content-Type: application/octet-stream',
                'Content-Encoding: aes128gcm',
                'Content-Length: ' . strlen($body),
            ],
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // Key helpers
    // ──────────────────────────────────────────────────────────────

    // Raw 32-byte EC private key scalar → PEM (RFC 5915, P-256)
    private function rawPrivateKeyToPem(string $raw): string
    {
        $oid = "\x2a\x86\x48\xce\x3d\x03\x01\x07"; // P-256 OID
        $der = "\x30\x31\x02\x01\x01\x04\x20" . $raw . "\xa0\x0a\x06\x08" . $oid;
        return "-----BEGIN EC PRIVATE KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END EC PRIVATE KEY-----\n";
    }

    // 65-byte uncompressed P-256 public key → PEM (SubjectPublicKeyInfo)
    private function rawPublicKeyToPem(string $point): string
    {
        $der = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42\x00" . $point;
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    // DER ECDSA signature → raw r||s (64 bytes, for JWT ES256)
    private function derToRaw(string $der): string
    {
        $offset = 2;
        if (strlen($der) > 2 && (ord($der[1]) & 0x80)) {
            $offset += ord($der[1]) & 0x7f;
        }
        $offset++;
        $rLen = ord($der[$offset++]);
        $r    = substr($der, $offset, $rLen);
        $offset += $rLen + 1;
        $sLen = ord($der[$offset++]);
        $s    = substr($der, $offset, $sLen);

        return str_pad(ltrim($r, "\x00"), 32, "\x00", STR_PAD_LEFT)
             . str_pad(ltrim($s, "\x00"), 32, "\x00", STR_PAD_LEFT);
    }

    private function b64u(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64uDecode(string $data): string
    {
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
