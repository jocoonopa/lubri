<?php

namespace App\Utility\Chinghwa\Flap\CCS_OrderIndex;

class OrderNoTree
{
    protected $rootSerNo;
    protected $firstName;
    protected $originFirstName;
    protected $children = [];

    public function __construct($serNo, $firstName, array $children)
    {
        $this
            ->setRootSerNo($serNo)
            ->setFirstName($firstName)
            ->setOriginFirstName($firstName)
            ->setChildren($children)
        ;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getOriginFirstName()
    {
        return $this->originFirstName;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getRootSerNo()
    {
        return $this->rootSerNo;
    }

    public function setFirstName($val)
    {
        $this->firstName = $val;

        array_walk($this->children, [$this, 'alterChildFirstName'], $this->firstName);

        return $this;
    }

    public function setOriginFirstName($val)
    {
        $this->originFirstName = $val;

        return $this;
    }

    public function setChildren(array $children)
    {
        $this->children = $children;
        
        return $this;
    }

    public function setRootSerNo($serNo)
    {
        $this->rootSerNo = $serNo;

        return $this;
    }

    public function alterChildFirstName(&$child, $key, $alter)
    {
        $child = str_replace($this->fetchFirstNameOfChild($child), $alter, $child);
    }

    public function fetchFirstNameOfChild($child)
    {
        $pos = strpos($child, '-');

        return (false !== $pos) ? substr($child, 0, $pos) : $child; 
    }

    public function fetchTailOfChild($child)
    {
        $pos = strpos($child, '-');

        return (false !== $pos) ? substr($child, $pos) : ''; 
    }
}