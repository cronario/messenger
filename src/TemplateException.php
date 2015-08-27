<?php

namespace Messenger;

use \Cronario\Exception\ExceptionInterface;
use \Cronario\Exception\RuntimeException;

class TemplateException extends RuntimeException implements ExceptionInterface
{
}