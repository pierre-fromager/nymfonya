<?php

namespace App\Controllers\Api\V1;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use Nymfonya\Component\Config;
use Nymfonya\Component\Container;
use Nymfonya\Component\Http\Headers;
use Nymfonya\Component\Http\Request;
use Nymfonya\Component\Http\Response;
use App\Component\Db\Core;
use App\Model\Repository\Users;
use App\Component\Jwt\Token;
use App\Component\Auth\Factory;
use App\Component\Crypt;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
/*use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;*/
use App\Component\Mailer\Smtp;
use Exception;

final class Auth extends AbstractApi implements IApi
{

    /**
     * core db instance
     *
     * @var Core
     */
    protected $db;

    /**
     * user repository
     *
     * @var Users
     */
    protected $userRepository;

    /**
     * slugs
     *
     * @var array
     */
    protected $slugs;

    /**
     * sql
     *
     * @var String
     */
    protected $sql;

    /**
     * sql values to bind statement
     *
     * @var array
     */
    protected $bindValues;

    /**
     * error
     *
     * @var Boolean
     */
    protected $error;

    /**
     * error message
     *
     * @var String
     */
    protected $errorMessage;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->userRepository = new Users($container);
        $this->db = new Core($container);
        $this->db->fromOrm($this->userRepository);
        $this->error = false;
        $this->errorMessage = '';
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
        $config = $this->getService(Config::class);
        $logger = $this->getService(\Monolog\Logger::class);
        $login = filter_var($this->request->getParam('login'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($this->request->getParam('password'), FILTER_SANITIZE_STRING);
        if (false === $this->isValidLogin($login, $password)) {
            $logger->warning(__FUNCTION__ . ' Invalid arguments');
            return $this->setErrorResponse(
                Response::HTTP_BAD_REQUEST,
                'Invalid arguments'
            );
        }
        $authFactory = new Factory($this->getContainer());
        $authFactory->setAdapter();
        if ($user = $authFactory->auth($login, $password)) {
            $jwtToken = new Token($config, $this->request);
            $token = $jwtToken
                ->setIssueAt(time())
                ->setIssueAtDelay(0)
                ->setTtl(1200)
                ->encode(
                    $user[Factory::_ID],
                    $user[Factory::_EMAIL],
                    $user[Factory::_PASSWORD]
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
        unset($authFactory);
        return $this->setErrorResponse(
            Response::HTTP_UNAUTHORIZED,
            'Bad credentials'
        );
    }

    /**
     * register action
     *
     * @Role anonymous
     * @return Auth
     */
    final public function register(): Auth
    {
        $config = $this->getService(Config::class);
        $logger = $this->getService(\Monolog\Logger::class);
        $name = filter_var($this->request->getParam('name'), FILTER_SANITIZE_STRING);
        $email = filter_var($this->request->getParam('email'), FILTER_SANITIZE_EMAIL);
        $password = filter_var($this->request->getParam('password'), FILTER_SANITIZE_STRING);
        if (false === $this->isValidRegistration($name, $email, $password)) {
            $this->error = true;
            $this->errorMessage = 'Invalid arguments';
            $logger->warning(__FUNCTION__ . ' Invalid arguments');
            return $this->setRegistrationResponse(__CLASS__, __FUNCTION__);
        }
        $this->userRepository->emailExists($email);
        $this->sql = $this->userRepository->getSql();
        $this->bindValues = $this->userRepository->getBuilderValues();
        $this->db->run($this->sql, $this->bindValues)->hydrate();
        $emailCount = $this->db->getRowset();
        $emailCounter = (int) $emailCount[0]['counter'];
        if ($emailCounter !== 0) {
            $this->error = true;
            $this->errorMessage = 'Email exists';
            $logger->warning(__FUNCTION__ . $this->errorMessage);
            return $this->setRegistrationResponse(__CLASS__, __FUNCTION__);
        }
        $cryptEngine = new Crypt($config);
        $cryptedPassword = $cryptEngine->encrypt($password);
        unset($cryptEngine);
        $this->userRepository->register($name, $email, $cryptedPassword);
        $this->sql = $this->userRepository->getSql();
        $this->bindValues = $this->userRepository->getBuilderValues();
        $this->db->run($this->sql, $this->bindValues);
        return $this->setRegistrationResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * lost password action
     *
     * @Role anonymous
     * @return Auth
     */
    final public function lostpassword(): Auth
    {
        $logger = $this->getService(\Monolog\Logger::class);
        $email = filter_var($this->request->getParam('email'), FILTER_SANITIZE_EMAIL);
        $validator = new EmailValidator();
        $isValid = $validator->isValid($email, new RFCValidation());
        if (false === $isValid) {
            $this->error = true;
            $this->errorMessage = 'Invalid email';
            $logger->warning(__FUNCTION__ . ' Invalid email');
            return $this->setRegistrationResponse(__CLASS__, __FUNCTION__);
        }
        $this->userRepository->getByEmail($email);
        $this->sql = $this->userRepository->getSql();
        $this->bindValues = $this->userRepository->getBuilderValues();
        $this->db->run($this->sql, $this->bindValues)->hydrate();
        $users = $this->db->getRowset();
        $emailExists = isset($users[0]);
        if (false === $emailExists) {
            $this->error = true;
            $this->errorMessage = 'Email does not exists';
            $logger->warning(__FUNCTION__ . $this->errorMessage);
            return $this->setRegistrationResponse(__CLASS__, __FUNCTION__);
        }
        $user = $users[0];
        try {
            $mailer = new Smtp($this->getContainer());
            $mailer
                ->setTo([$email => $user['name']])
                ->setMessage('Password retrieval', $user['password'])
                ->sendMessage();
            if ($mailer->isError()) {
                throw new Exception('Lost password : Email was not sent');
            }
        } catch (Exception $e) {
            $this->error = true;
            $this->errorMessage = $e->getMessage();
            $logger->error($e);
        }
        return $this->setRegistrationResponse(__CLASS__, __FUNCTION__);
    }

    /**
     * return true if request methods are allowed
     *
     * @return boolean
     */
    protected function isLoginMethodAllowed(): bool
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
     * return true if registration process can be executed
     *
     * @param string $login
     * @param string $password
     * @return boolean
     */
    protected function isValidRegistration(string $name, string $email, string $password): bool
    {
        $notEmpty = (!empty($name) && !empty($email) && !empty($password));
        $isMethodAllow = $this->request->getMethod() === Request::METHOD_POST;
        return $notEmpty && $isMethodAllow;
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

    /**
     * set response with for a classname and action
     *
     * @param string $classname
     * @param string $action
     * @return Auth
     */
    protected function setRegistrationResponse(string $classname, string $action): Auth
    {
        $isError = $this->isError();
        $this->response
            ->setCode($this->getStatusCode())
            ->setContent(
                [
                    'error' => $this->error,
                    'errorMessage' => $this->errorMessage,
                    'datas' => [
                        'method' => $this->getRequest()->getMethod(),
                        'params' => $this->getParams(),
                        'controller' => $classname,
                        'action' => $action,
                        'query' => $isError
                            ? ''
                            : $this->sql,
                        'queryValues' => $isError
                            ? [] : $this->bindValues,
                        'rowset' => $isError
                            ? []
                            : $this->db->getRowset()
                    ]
                ]
            );
        return $this;
    }

    /**
     * returns true if error happened
     *
     * @return boolean
     */
    protected function isError(): bool
    {
        return $this->error === true;
    }

    /**
     * returns http status code
     *
     * @return int
     */
    protected function getStatusCode(): int
    {
        return (true === $this->isError())
            ? Response::HTTP_BAD_REQUEST
            : Response::HTTP_OK;
    }
}
