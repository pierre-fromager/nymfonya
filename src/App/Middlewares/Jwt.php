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
    protected function process()
    {
        if ($this->enabled) {
            $this->response->getHeaderManager()->add(
                self::_SIGN,
                microtime(true)
            );
            if ($this->required()) {
                if ($this->isValidAuthorization()) {
                    try {
                        $tokenFragments = explode(
                            ' ',
                            $this->headers[self::_AUTORIZATION]
                        );
                        $token = $tokenFragments[1];
                        $decodedToken = Token::decode($token);
                        if (isset($decodedToken->{Token::_DATA}->{Token::_DATA_ID})) {
                            $userId = $decodedToken->{Token::_DATA}->{Token::_DATA_ID};
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
    protected function isPreflight(): bool
    {
        $isOptionsMethod = ($this->request->getMethod() == Request::METHOD_OPTIONS);
        $corsHeadersKeys = array_keys($this->headers);
        $hasOrigin = in_array('Origin', $corsHeadersKeys);
        $hasACRequestMethod = in_array(
            'Access-Control-Request-Method',
            $corsHeadersKeys
        );
        return ($isOptionsMethod && $hasOrigin && $hasACRequestMethod);
    }

    /**
     * isValidCredential
     *
     * @param object $decodedToken
     * @param array $user
     * @return boolean
     */
    protected function isValidCredential($decodedToken, $user): bool
    {
        $login = $decodedToken->{Token::_DATA}->{Token::_DATA_LOGIN};
        $passwordHash = $decodedToken->{Token::_DATA}->{Token::_DATA_PASSWORD_HASH};
        $checkLogin = ($login === $user[Token::_DATA_LOGIN]);
        $checkPassword = password_verify($user[self::_PASSWORD], $passwordHash);
        $checkStatus = ($user[self::_USER_STATUS] === self::_STATUS_VALID);
        return ($checkLogin && $checkPassword && $checkStatus);
    }

    /**
     * getUser
     *
     * @param int $userId
     * @return array
     */
    protected function getUser(int $userId): array
    {
        $authModel = new \App\Model\Users($this->config);
        $userList = $authModel->getById($userId);
        return isset($userList[0]) ? $userList[0] : $userList;
    }

    /**
     * isValidAuthorization
     *
     * @return boolean
     */
    protected function isValidAuthorization(): bool
    {
        return (isset($this->headers[self::_AUTORIZATION])
            && !empty($this->headers[self::_AUTORIZATION]));
    }

    /**
     * required
     *
     * @return boolean
     */
    protected function required(): bool
    {
        return (!$this->isExclude()
            && $this->requestUriPrefix() === $this->prefix);
    }

    /**
     * isExclude
     *
     * @return boolean
     */
    protected function isExclude(): bool
    {
        $disallowed = $this->configParams['exclude'];
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
    protected function requestUriPrefix(): string
    {
        return substr($this->request->getUri(), 0, strlen($this->prefix));
    }
}
