<?php

namespace Kip\Ironx;

class Frame
{
    private $m = 0;
    private $i = 0;
    private $n;
    private $o = [];

    public function setI($val)
    {
        $this->i = $val;
        return $this;
    }

    public function setM($val)
    {
        $this->m = $val;
        return $this;
    }

    public function setN($val)
    {
        $this->n = $val;
        return $this;
    }

    public function setO(array $val)
    {
        $this->o = $val;
        return $this;
    }

    public function asJson()
    {
        return json_encode([
            'm' => $this->m,
            'i' => $this->i,
            'n' => $this->n,
            'o' => json_encode($this->o),
        ]);
    }

    public function __construct($str = '')
    {
        if ($str) {
            $obj = json_decode($str);

            $this->m = $obj->m;
            $this->i = $obj->i;
            $this->n = $obj->n;
            $this->o = json_decode($obj->o, true);
        }
    }

    public function getO()
    {
        return $this->o;
    }
}
