<?php

namespace App\Exceptions;

class ArticleNotFoundException extends \Exception
{
    protected $message = "Couldn't find this article!";
}