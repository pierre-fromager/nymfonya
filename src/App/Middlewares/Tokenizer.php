<?php

namespace App\Middlewares;

use App\Http\Interfaces\Middleware\ILayer;
use App\Http\Request;
use App\Container;

class Tokenizer implements ILayer
{
    const TOKENIZER_TOKEN = 'token';
    const TOKENIZER_DBPOOL = 'dbPool';

    /**
     * peel
     *
     * @param Container $container
     * @param \Closure $next
     * @return \Closure
     */
    public function peel(Container $container, \Closure $next)
    {
        /*
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
        unset($app);*/
        return $next($container);
    }
}
