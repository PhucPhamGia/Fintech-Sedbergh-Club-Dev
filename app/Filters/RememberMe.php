<?php

namespace App\Filters;

use App\Models\M_Auth;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class RememberMe implements FilterInterface
{
	private const COOKIE_NAME = 'remember_me';
	private const TTL_SECONDS = 2592000; // 30 days

	public function before(RequestInterface $request, $arguments = null)
	{
		$session = session();
		if ($session->get('logged_in')) {
			return;
		}

		/** @var \CodeIgniter\HTTP\IncomingRequest $incoming */
		$incoming = $request instanceof \CodeIgniter\HTTP\IncomingRequest ? $request : service('request');
		$raw      = (string) $incoming->getCookie(self::COOKIE_NAME);

		if ($raw === '') {
			return;
		}

		$parts = explode(':', $raw, 2);
		if (count($parts) !== 2) {
			service('response')->deleteCookie(self::COOKIE_NAME, '', '/', '');
			return;
		}

		[$selector, $validator] = $parts;
		if ($selector === '' || $validator === '') {
			service('response')->deleteCookie(self::COOKIE_NAME, '', '/', '');
			return;
		}

		$auth = new M_Auth();
		$user  = $auth->where('remember_selector', $selector)->first();
		if (! is_array($user) && ! is_object($user)) {
			return;
		}

		$userId = is_array($user) ? ($user['id'] ?? null) : ($user->id ?? null);
		if ($userId === null) {
			return;
		}

		$expiresAtRaw = is_array($user) ? ($user['remember_expires_at'] ?? null) : ($user->remember_expires_at ?? null);
		if (! is_string($expiresAtRaw) || $expiresAtRaw === '') {
			$this->clearRememberMe($auth, (int) $userId);
			return;
		}

		try {
			$expiresAt = Time::parse($expiresAtRaw);
			if ($expiresAt->isBefore(Time::now())) {
				$this->clearRememberMe($auth, (int) $userId);
				return;
			}
		} catch (\Throwable $e) {
			$this->clearRememberMe($auth, (int) $userId);
			return;
		}

		$expectedHash = (string) (is_array($user) ? ($user['remember_hash'] ?? '') : ($user->remember_hash ?? ''));
		$actualHash   = hash('sha256', $validator);

		if ($expectedHash === '' || ! hash_equals(strtolower($expectedHash), strtolower($actualHash))) {
			// Token mismatch: invalidate stored token and clear cookie.
			$this->clearRememberMe($auth, (int) $userId);
			return;
		}

		// Valid token: establish session.
		$session->regenerate(true);
		$session->set([
			'user_id'   => $userId,
			'username'  => is_array($user) ? ($user['username'] ?? null) : ($user->username ?? null),
			'logged_in' => true,
		]);

		// Rotate token on use.
		try {
			$newSelector  = bin2hex(random_bytes(9));
			$newValidator = bin2hex(random_bytes(32));

			$auth->update((int) $userId, [
				'remember_selector'   => $newSelector,
				'remember_hash'       => hash('sha256', $newValidator),
				'remember_expires_at' => Time::now()->addSeconds(self::TTL_SECONDS)->toDateTimeString(),
			]);

			service('response')->setCookie(
				self::COOKIE_NAME,
				$newSelector . ':' . $newValidator,
				self::TTL_SECONDS,
				'',
				'/',
				'',
				$incoming->isSecure(),
				true,
				'Lax'
			);
		} catch (\Throwable $e) {
			log_message('warning', 'Remember-me rotation failed: ' . $e->getMessage());
		}

		return;
	}

	public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
	{
		// no-op
	}

	private function clearRememberMe(M_Auth $auth, int $userId): void
	{
		try {
			$auth->update($userId, [
				'remember_selector'   => null,
				'remember_hash'       => null,
				'remember_expires_at' => null,
			]);
		} catch (\Throwable $e) {
			// ignore
		}

		service('response')->deleteCookie(self::COOKIE_NAME, '', '/', '');
	}
}

