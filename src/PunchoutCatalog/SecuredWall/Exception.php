<?php

namespace PunchoutCatalog\SecuredWall;

class Exception extends \Exception
{
    const EMPTY_SECRET = 0;
    const EMPTY_DB_PARAM = 100;
    const EMPTY_ID = 200;
    const EMPTY_VALUE = 300;
    const EMPTY_VALUE_ENCODE = 301;
    const EMPTY_VALUE_DECODE = 302;
}
