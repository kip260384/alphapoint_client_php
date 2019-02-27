<?php

namespace Kip\Ironx;

/**
 * Class RepositoryHandler
 * @package Ironx
 *
 * @mixin IronxRepository
 */
class RepositoryHandler
{
    /** @var IronxRepository */
    private $repo;

    public function __construct(IronxRepository $repo)
    {
        $this->repo = $repo;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this->repo, $name)) {
            call_user_func_array([$this->repo, $name], $arguments);
            $resp = $this->repo->send();

            if (isset($resp['errormsg']) && $resp['errormsg'] != '') {
                throw new IronxException($resp['errormsg']);
            }
        } else {
            throw new IronxException("Method $name doesn't exists.");
        }

        return $resp;
    }
}
