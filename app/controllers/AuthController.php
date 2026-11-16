<?php

namespace App\Controllers;

use Auth0\SDK\Auth0;
use Auth0\SDK\Exception\Auth0Exception;

class AuthController
{
    public static function login()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $auth0 = self::getAuth0();
        $auth0->login();
    }

    public static function callback()
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $auth0 = self::getAuth0();
            $session = $auth0->getUser();

            if (empty($session)) {
                $_SESSION['auth_error'] = 'No se pudo completar la autenticación.';
                header('Location: ' . base_url('/'));
                exit;
            }

            $_SESSION['auth0_user'] = $session;
            $_SESSION['auth0_user_id'] = $session['sub'] ?? null;
            $_SESSION['auth0_user_email'] = $session['email'] ?? null;

            $role = self::extractRoleFromSession($session);
            $_SESSION['auth0_user_role'] = $role;
            $_SESSION['auth0_user_roles'] = $role !== '' ? [$role] : [];

            if (empty($_SESSION['auth0_user'])) {
                $_SESSION['auth_error'] = 'La sesión de Auth0 no quedó guardada correctamente.';
                header('Location: ' . base_url('/'));
                exit;
            }

            header('Location: ' . base_url('/dashboard'));
            exit;
        } catch (\Auth0\SDK\Exception\CoreException $e) {
            self::resetAuthSession();
            $_SESSION['auth_error'] = 'La sesión de autenticación expiró o es inválida. Intenta iniciar sesión nuevamente.';
            header('Location: ' . base_url('/'));
            exit;
        } catch (Auth0Exception $e) {
            $_SESSION['auth_error'] = $e->getMessage();
            header('Location: ' . base_url('/'));
            exit;
        } catch (\RuntimeException $e) {
            $_SESSION['auth_error'] = $e->getMessage();
            header('Location: ' . base_url('/'));
            exit;
        } catch (\InvalidArgumentException $e) {
            $_SESSION['auth_error'] = $e->getMessage();
            header('Location: ' . base_url('/'));
            exit;
        }
    }

    private static function extractRoleFromSession(array $session): string
    {
        $candidates = [];
        $flat = function ($value, &$items) use (&$flat): void {
            if (is_array($value)) {
                foreach ($value as $key => $item) {
                    $normalizedKey = strtolower((string) $key);
                    if (in_array($normalizedKey, ['role', 'roles', 'rol', 'tipo_usuario', 'tipousuario', 'userrole', 'user_role', 'perfil'], true)) {
                        $items[] = $item;
                    }

                    if (is_array($item)) {
                        $flat($item, $items);
                    }
                }
                return;
            }

            if ($value !== null && $value !== '') {
                $items[] = $value;
            }
        };

        foreach (array_keys($session) as $key) {
            $normalizedKey = strtolower((string) $key);
            if (in_array($normalizedKey, ['role', 'roles', 'rol', 'tipo_usuario', 'tipousuario', 'userrole', 'user_role', 'perfil'], true)) {
                $candidates[] = $session[$key];
            }
        }

        $flat($session, $candidates);

        foreach ($candidates as $candidate) {
            if (is_array($candidate)) {
                foreach ($candidate as $item) {
                    $role = trim((string) $item);
                    if ($role !== '') {
                        return strtolower($role);
                    }
                }
                continue;
            }

            $role = trim((string) $candidate);
            if ($role !== '') {
                return strtolower($role);
            }
        }

        return '';
    }
    

    public static function logout()
    {
        self::resetAuthSession();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $domain = env('AUTH0_DOMAIN');
        $clientId = env('AUTH0_CLIENT_ID');
        $returnTo = env('AUTH0_LOGOUT_REDIRECT_URI', base_url('/'));

        if (empty($returnTo) || $returnTo === 'http://localhost/redtutor') {
            $returnTo = base_url('/');
        }

        $logoutUrl = sprintf(
            'https://%s/v2/logout?client_id=%s&returnTo=%s',
            $domain,
            rawurlencode($clientId),
            rawurlencode($returnTo)
        );

        header('Location: ' . $logoutUrl);
        exit;
    }

    public static function resetAuthSession(): void
    {
        if (!isset($_SESSION)) {
            return;
        }

        foreach (['auth0_user', 'auth0_user_id', 'auth0_user_email', 'auth0_user_role', 'auth0_user_roles', 'auth_error'] as $key) {
            unset($_SESSION[$key]);
        }
    }

    private static function getAuth0()
    {
        $redirectUri = trim((string) env('AUTH0_REDIRECT_URI', ''));
        if ($redirectUri === '' || !preg_match('#/callback$#i', $redirectUri)) {
            $redirectUri = base_url('/callback');
        }

        $config = [
            'domain' => env('AUTH0_DOMAIN'),
            'client_id' => env('AUTH0_CLIENT_ID'),
            'client_secret' => env('AUTH0_CLIENT_SECRET'),
            'redirect_uri' => $redirectUri,
            'audience' => env('AUTH0_AUDIENCE', null),
            'scope' => env('AUTH0_SCOPES', 'openid profile email'),
        ];

        return new Auth0($config);
    }
}
