<?php

declare(strict_types=1);

namespace TikTok\Exception;

class NotAllowHttpMethod extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'Not allowed http method!';
}
