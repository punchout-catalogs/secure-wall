<?php

namespace PunchoutCatalog\SecuredWall;

class Exception extends \Exception
{
    const SAVE_ERROR = 400;

    const EMPTY_SECRET = 10;
    const EMPTY_TOKEN = 20;
    const EMPTY_CLOUD_URL = 30;
    
    const EMPTY_ID = 200;
    const EMPTY_VALUE = 300;
    const EMPTY_VALUE_ENCODE = 301;
    const EMPTY_VALUE_DECODE = 302;
}
