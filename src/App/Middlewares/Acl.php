<?php

namespace App1\Middleware;

class Acl implements \Pimvc\Http\Interfaces\Layer
{

    const ACL_DEFAULT_CONTROLLER = 'home';
    const ACL_DEFAULT_ACTION = 'index';
    const ACL_TRANSFO = 'ucfirst';
    const ACL_BS = '\\';
    const ACL_SL = '/';

    private $app;
    private $controller;
    private $action;
    private $role;
    private $allowed;
    private $aclTools;

    /**
     * peel
     *
     * @param mixed $object
     * @param \Closure $next
     * @return \Closure
     */
    public function peel($object, \Closure $next)
    {
        $this->process();
        return $next($object);
    }

    /**
     * getRessources
     *
     * @return array
     */
    public function getRessources(): array
    {
        return $this->aclTools->getRessources();
    }

    /**
     * process
     *
     */
    private function process()
    {
        $this->app = \Pimvc\App::getInstance();
        $this->aclTools = new \Pimvc\Tools\Acl();
        $this->setCAR();
        $this->allowed = $this->verify();
        if (!$this->allowed) {
            $this->app->getController()->setForbidden();
        }
        $this->log();
    }

    /**
     * verify
     *
     * @return boolean
     */
    private function verify()
    {
        return $this->aclTools->isAllowed(
            $this->controller,
            $this->action,
            $this->role
        );
    }

    /**
     * setCAR
     *
     */
    private function setCAR()
    {
        $this->role = \Pimvc\Tools\Session::getProfil();
        $prerouting = $this->app->getRouter()->compile();
        if (is_null($prerouting) || count($prerouting) === 0) {
            $prerouting[0] = self::ACL_DEFAULT_CONTROLLER;
            $prerouting[1] = self::ACL_DEFAULT_ACTION;
        } elseif (count($prerouting) === 1) {
            $prerouting[1] = self::ACL_DEFAULT_ACTION;
        }
        list($this->controller, $this->action) = $prerouting;
        unset($prerouting);
        $this->controller = $this->getNsControllerName($this->controller);
    }

    /**
     * getNsControllerName
     *
     * @param string $controller
     * @return string
     */
    private function getNsControllerName($controller)
    {
        $nsc = $this->aclTools->getNamespaceCtrlPrefix()
            . self::ACL_BS . str_replace(self::ACL_SL, self::ACL_BS, $controller);
        $nscparts = array_map(self::ACL_TRANSFO, explode(self::ACL_BS, $nsc));
        $nsc = implode(self::ACL_BS, $nscparts);
        return $nsc;
    }

    /**
     * log
     *
     */
    private function log()
    {
        $message = $this->role . '::' . $this->controller . self::ACL_BS . $this->action;
        $messageType = (!$this->allowed) ? \Pimvc\Logger::WARN : \Pimvc\Logger::INFO;
        if ($message != 'admin::App1\\Controller\\Log\\content') {
            $this->app->getLogger()->log(__CLASS__, $messageType, $message);
        }
    }
}
