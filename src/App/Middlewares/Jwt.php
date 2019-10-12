<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Tools\User\Auth as authTools;
use App\Tools\Jwt\Token;
use App\Http\Interfaces\Middleware\ILayer;
use App\Container;

/**
 * App\Middleware\Jwt
 *
 * Intercept jwt header and auth if required
 */
class Jwt implements ILayer
{

    use \App\Middlewares\Reuse\TInit;

    const _SIGN = 'X-Middleware-Jwt';
    const _PASSWORD = 'password';
    const _USER_STATUS = 'status';
    const _STATUS_VALID = 'valid';
    const _AUTORIZATION = 'Authorization';
    const _ERROR = 'error';
    const _ERROR_MESSAGE = 'errorMessage';

    /**
     * peel
     *
     * @param array $container
     * @param \Closure $next
     * @return \Closure
     */
    public function peel(Container $container, \Closure $next)
    {
        $this->init($container);
        $this->process();
        return $next($container);
    }

    /**
     * process
     *
     */
    private function process()
    {
        if ($this->enabled) {
            $this->response->getHeaderManager()->add(
                self::_SIGN,
                microtime(true)
            );
            if ($this->required()) {
                if ($this->isValidAuthorization()) {
                    try {
                        $authorization = $this->headers[self::_AUTORIZATION];
                        list($bearer, $token) = explode(' ', $authorization);
                        $decodedToken = Token::decode($token);
                        if (isset($decodedToken->{Token::TOKEN_DATA}->{Token::TOKEN_DATA_ID})) {
                            $userId = $decodedToken->{Token::TOKEN_DATA}->{Token::TOKEN_DATA_ID};
                            $user = $this->getUser($userId);
                            if ($user !== false) {
                                if ($this->isValidCredential($decodedToken, $user)) {
                                    //new authTools(
                                    //    $user[Token::TOKEN_DATA_LOGIN],
                                    //    $user[self::_PASSWORD]
                                    //);
                                } else {
                                    $this->sendError(403);
                                }
                            } else {
                                $this->sendError(403);
                            }
                        } else {
                            $this->sendError(403);
                        }
                    } catch (\Exception $e) {
                        $this->sendError(500);
                    }
                } else {
                    if (!$this->isPreflight()) {
                        $this->sendError(401);
                    }
                }
            }
        }
    }

    /**
     * send response and die
     *
     * @param integer $errorCode
     * @return void
     */
    protected function sendError(int $errorCode)
    {
        $this->response
            ->setCode($errorCode)
            ->setContent(
                [self::_ERROR => true, self::_ERROR_MESSAGE => 'Auth failed']
            )->send();
        die;
    }

    /**
     * isPreflight
     *
     * @return bool
     */
    private function isPreflight(): bool
    {
        $isOptionsMethod = ($this->request->getMethod() == Request::METHOD_OPTIONS);
        $corsHeadersKeys = array_keys($this->headers);
        $hasOrigin = in_array('Origin', $corsHeadersKeys);
        $hasAccessControlRequestMethod = in_array(
            'Access-Control-Request-Method',
            $corsHeadersKeys
        );
        return ($isOptionsMethod && $hasOrigin && $hasAccessControlRequestMethod);
    }

    /**
     * isValidCredential
     *
     * @param object $decodedToken
     * @param array $user
     * @return boolean
     */
    private function isValidCredential($decodedToken, $user): bool
    {
        $login = $decodedToken->{Token::TOKEN_DATA}->{Token::TOKEN_DATA_LOGIN};
        $passwordHash = $decodedToken->{Token::TOKEN_DATA}->{Token::TOKEN_DATA_PASSWORD_HASH};
        $checkLogin = ($login === $user[Token::TOKEN_DATA_LOGIN]);
        $checkPassword = password_verify($user[self::_PASSWORD], $passwordHash);
        $checkStatus = ($user[self::_USER_STATUS] === self::_STATUS_VALID);
        return ($checkLogin && $checkPassword && $checkStatus);
    }

    /**
     * getUser
     *
     * @param int $userId
     * @return array | false
     */
    private function getUser(int $userId)
    {
        $authModel = new \App\Model\Users($this->config);
        $r = $authModel->getById($userId);
        return isset($r[0]) ? $r[0] : false;
    }

    /**
     * isValidAuthorization
     *
     * @return boolean
     */
    private function isValidAuthorization()
    {
        return (isset($this->headers[self::_AUTORIZATION])
            && !empty($this->headers[self::_AUTORIZATION]));
    }

    /**
     * required
     *
     * @return boolean
     */
    private function required(): bool
    {
        return (!$this->isExclude()
            && $this->requestUriPrefix() === $this->prefix);
    }

    /**
     * isExclude
     *
     * @return boolean
     */
    private function isExclude()
    {
        $disallowed = $this->configParams[\App\Middlewares\Jwt::class]['exclude'];
        for ($c = 0; $c < count($disallowed); ++$c) {
            $composed = $this->prefix . $disallowed[$c];
            $isAuth = ($composed == $this->request->getUri());
            if ($isAuth) {
                return true;
            }
        }
        return false;
    }

    /**
     * uriPrefix
     *
     * @return string
     */
    private function requestUriPrefix()
    {
        return substr($this->request->getUri(), 0, strlen($this->prefix));
    }
}
