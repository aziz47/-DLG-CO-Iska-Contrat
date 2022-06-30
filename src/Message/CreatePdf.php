<?php

namespace App\Message;

use App\Entity\User;

class CreatePdf
{


    private $name;
    private $html;
    /**
     * @var User
     */
    private $user;

    public function __construct($name, $html, User $user)
    {
        $this->name = $name;
        $this->html = $html;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getHtml()
    {
        return $this->html;
    }



}