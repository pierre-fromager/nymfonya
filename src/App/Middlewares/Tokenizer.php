<?php

namespace App1\Middleware;

class Tokenizer implements \Pimvc\Http\Interfaces\Layer
{
    const TOKENIZER_TOKEN = 'token';
    const TOKENIZER_DBPOOL = 'dbPool';

    /**
     * peel
     *
     * @param type $object
     * @param \Closure $next
     * @return type
     */
    public function peel($object, \Closure $next)
    {
        $app = \Pimvc\App::getInstance();
        $params = $app->getRequest()->getQueryTupple();
        $token = (isset($params[self::TOKENIZER_TOKEN])) ? $params[self::TOKENIZER_TOKEN] : null;
        $validatedRoute = $app->getRouter()->compile();
        if ($token && $validatedRoute) {
            $authModel = new \App1\Model\Users(
                $app->getConfig()->getSettings(self::TOKENIZER_DBPOOL)
            );
            $allowed = false;
            if ($auth = $authModel->getAuthByToken($token)) {
                $auth = (new \Pimvc\Tools\User\Auth(null, null, $token));
                $allowed = $auth->isAllowed;
                unset($auth);
            }
            $app->getLogger()->log(
                __CLASS__,
                \Pimvc\Logger::DEBUG,
                [
                    'token' => $token,
                    'allow' => $allowed
                ]
            );
            unset($allowed);
            unset($authModel);
        }
        unset($token);
        unset($params);
        unset($app);
        return $next($object);
    }
}
