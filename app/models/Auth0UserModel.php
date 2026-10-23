<?php

namespace App\Models;

class Auth0UserModel
{
    public static function getAllUsers(): array
    {
        $token = self::getManagementToken();
        $domain = trim((string) env('AUTH0_DOMAIN'));
        $url = 'https://' . $domain . '/api/v2/users';

        $filter = trim((string) env('AUTH0_USER_FILTER', ''));
        if ($filter === '') {
            $connectionName = trim((string) env('AUTH0_CONNECTION_NAME', ''));
            if ($connectionName !== '') {
                $filter = 'identities.connection:"' . str_replace('"', '\\"', $connectionName) . '"';
            } else {
                $filter = 'app_metadata.tenant:"redtutor" OR app_metadata.app:"redtutor" OR app_metadata.node:"redtutor" OR identities.connection:"Username-Password-Authentication"';
            }
        }

        $params = [
            'per_page' => 50,
            'page' => 0,
            'q' => $filter,
            'fields' => implode(',', [
                'user_id',
                'name',
                'nickname',
                'given_name',
                'family_name',
                'email',
                'created_at',
                'last_login',
                'blocked',
                'logins_count',
                'app_metadata',
                'user_metadata',
                'identities',
                'picture',
            ]),
        ];

        $response = self::request('GET', $url . '?' . http_build_query($params), $token);
        $data = json_decode($response, true);

        if (!is_array($data)) {
            return [];
        }

        foreach ($data as &$user) {
            $user = self::normalizeUser($user);
        }

        return $data;
    }

    public static function toggleUserStatus(string $userId, bool $blocked): array
    {
        return self::updateUser($userId, ['blocked' => $blocked]);
    }

    public static function deleteUser(string $userId): bool
    {
        $token = self::getManagementToken();
        $domain = trim((string) env('AUTH0_DOMAIN'));
        $url = 'https://' . $domain . '/api/v2/users/' . rawurlencode($userId);

        self::request('DELETE', $url, $token);
        return true;
    }

    public static function updateUser(string $userId, array $data): array
    {
        $payload = [];

        foreach (['name', 'nickname', 'given_name', 'family_name', 'email'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== '') {
                $payload[$field] = trim((string) $data[$field]);
            }
        }

        if (array_key_exists('blocked', $data)) {
            $payload['blocked'] = (bool) $data['blocked'];
        }

        if (array_key_exists('user_metadata', $data) && is_array($data['user_metadata'])) {
            $payload['user_metadata'] = $data['user_metadata'];
        }

        // app_metadata es de solo lectura para este flujo; no se envía en el PATCH
        // para evitar el error 400 de Auth0.
        if (empty($payload)) {
            return [];
        }

        $token = self::getManagementToken();
        $domain = trim((string) env('AUTH0_DOMAIN'));
        $url = 'https://' . $domain . '/api/v2/users/' . rawurlencode($userId);
        $response = self::request('PATCH', $url, $token, $payload);
        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : [];
    }

    public static function normalizeUser(array $user): array
    {
        $user['name'] = $user['name'] ?? ($user['nickname'] ?? '');
        $user['created_at'] = $user['created_at'] ?? null;
        $user['last_login'] = $user['last_login'] ?? null;
        $user['blocked'] = !empty($user['blocked']);
        $user['user_metadata'] = is_array($user['user_metadata'] ?? null) ? $user['user_metadata'] : [];
        $user['app_metadata'] = is_array($user['app_metadata'] ?? null) ? $user['app_metadata'] : [];
        $user['metadata'] = array_merge($user['user_metadata'], $user['app_metadata']);

        return $user;
    }

    public static function getMetadataValue(array $source, string $key, $default = '')
    {
        if (is_array($source) && array_key_exists($key, $source)) {
            $value = $source[$key];
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        foreach (['user_metadata', 'app_metadata', 'metadata'] as $section) {
            if (!isset($source[$section]) || !is_array($source[$section])) {
                continue;
            }

            $value = self::getMetadataValue($source[$section], $key, $default);
            if ($value !== $default) {
                return $value;
            }
        }

        return $default;
    }

    public static function findMetadataValue(array $source, array $keys, $default = '')
    {
        $normalizedKeys = [];
        foreach ($keys as $key) {
            $normalizedKeys[] = strtolower(str_replace(['_', '-', ' '], '', (string) $key));
        }

        $search = function ($value) use (&$search, $normalizedKeys) {
            if (!is_array($value)) {
                return null;
            }

            foreach ($value as $key => $item) {
                $normalizedKey = strtolower(str_replace(['_', '-', ' '], '', (string) $key));

                if (in_array($normalizedKey, $normalizedKeys, true)) {
                    if ($item !== null && $item !== '') {
                        return trim((string) $item);
                    }
                }

                if (is_array($item)) {
                    $nested = $search($item);
                    if ($nested !== null) {
                        return $nested;
                    }
                }
            }

            return null;
        };

        $result = $search($source);

        return $result !== null ? $result : $default;
    }

    public static function prettyMetadata(array $metadata): string
    {
        if (empty($metadata)) {
            return 'Sin metadata';
        }

        $clean = [];
        foreach ($metadata as $key => $value) {
            $clean[$key] = is_array($value) || is_object($value)
                ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                : $value;
        }

        return json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public static function parseJsonMetadata(string $value): array
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return [];
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        throw new \InvalidArgumentException('La metadata debe ser un JSON válido.');
    }

    private static function getManagementToken(): string
    {
        $domain = trim((string) env('AUTH0_DOMAIN'));
        $clientId = trim((string) env('AUTH0_CLIENT_ID'));
        $clientSecret = trim((string) env('AUTH0_CLIENT_SECRET'));

        $url = 'https://' . $domain . '/oauth/token';
        $payload = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'audience' => 'https://' . $domain . '/api/v2/',
        ];

        $response = self::request('POST', $url, null, $payload, false);
        $data = json_decode($response, true);

        if (empty($data['access_token'])) {
            throw new \RuntimeException('No se pudo obtener el token de gestión de Auth0.');
        }

        return (string) $data['access_token'];
    }

    private static function request(string $method, string $url, ?string $token = null, ?array $payload = null, bool $useAuthHeader = true): string
    {
        $ch = curl_init($url);

        $headers = [];
        if ($useAuthHeader && !empty($token)) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }
        $headers[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $result = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlErrno !== 0) {
            throw new \RuntimeException('Error de conexión con Auth0: ' . $curlError);
        }

        if ($httpCode >= 300) {
            $message = $result ?: 'Error en la solicitud a Auth0';
            throw new \RuntimeException('Auth0 respondió con HTTP ' . $httpCode . ': ' . $message);
        }

        return (string) $result;
    }
}
