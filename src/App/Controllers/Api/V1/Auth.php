<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\Api;
use App\Container;
use App\Http\Headers;
use App\Http\Request;
use App\Http\Response;
use App\Model\Users;
use App\Tools\Jwt\Token;

final class Auth extends Api implements IApi
{

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * login action
     *
     * @Role anonymous
     * @return Auth
     */
    final public function login(): Auth
    {
        $config = $this->getService(\App\Config::class);
        $logger = $this->getService(\Monolog\Logger::class);
        $login = $this->request->getParam('login');
        $password = $this->request->getParam('password');
        if (false == $this->isValidLogin($login, $password)) {
            $logger->warning(__FUNCTION__ . ' Invalid arguments');
            return $this->setErrorResponse(
                Response::HTTP_BAD_REQUEST,
                'Invalid arguments'
            );
        }
        $userModel = new Users($config);
        if ($user = $userModel->auth($login, $password)) {
            $jwtToken = new Token($config, $this->request);
            $token = $jwtToken
                ->setIssueAt(time())
                ->setIssueAtDelay(0)
                ->setTtl(1200)
                ->encode(
                    $user[Users::_ID],
                    $user[Users::_EMAIL],
                    $user[Users::_PASSWORD]
                );
            $logger->info(__FUNCTION__ . ' Auth succeed');
            $this->response
                ->setCode(Response::HTTP_OK)
                ->setContent(
                    [Response::_ERROR => false, 'token' => $token]
                );
            return $this;
        }
        $logger->warning(__FUNCTION__ . ' Auth failed');
        return $this->setErrorResponse(
            Response::HTTP_UNAUTHORIZED,
            'Bad credentials'
        );
    }

    /**
     * return true if request methods are allowed
     *
     * @return boolean
     */
    private function isLoginMethodAllowed(): bool
    {
        return in_array(
            $this->request->getMethod(),
            [Request::METHOD_POST, Request::METHOD_TRACE]
        );
    }

    /**
     * return true if login action can be executed
     *
     * @param string $login
     * @param string $password
     * @return boolean
     */
    protected function isValidLogin(string $login, string $password): bool
    {
        return $this->isLoginMethodAllowed()
            && !empty($login)
            && !empty($password);
    }

    /**
     * return Auth and set response with http code and message
     *
     * @param integer $code
     * @param string $msg
     * @return Auth
     */
    protected function setErrorResponse(int $code, string $msg): Auth
    {
        $this->response
            ->setCode($code)
            ->setContent([
                Response::_ERROR => true,
                Response::_ERROR_CODE => $code,
                Response::_ERROR_MSG => $msg
            ])->getHeaderManager()->add(
                Headers::CONTENT_TYPE,
                'application/json'
            );
        return $this;
    }
}
