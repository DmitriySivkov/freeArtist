<?php


namespace App\Services;


use Symfony\Component\HttpFoundation\Cookie;

class AuthCookieService
{
    /**
     * @var Cookie
     */
    protected $cookie;

    public function __construct($name, $value, $ttl = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {
        $this->cookie = Cookie::create(
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
     * @return Cookie
     */
    public function get()
    {
        return $this->cookie;
    }
}
