<?php


namespace App\Services;


use Illuminate\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;

class CookieService
{
    /**
     * @var CookieJar|Cookie
     */
    protected $cookie;

    public function __construct($name, $value, $ttl = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {
        $this->cookie = cookie(
            $name,
            $value,
            $ttl,
            $path,
            $domain,
            $secure,
            $httpOnly,
            $raw,
            $sameSite
        );
    }

    /**
     * @return CookieJar|Cookie
     */
    public function getCookie()
    {
        return $this->cookie;
    }
}
