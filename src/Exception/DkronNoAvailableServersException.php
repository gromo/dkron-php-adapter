<?php

namespace Dkron\Exception;

class DkronNoAvailableServersException extends DkronException
{
    protected $message = 'No available dkron agent';
}
