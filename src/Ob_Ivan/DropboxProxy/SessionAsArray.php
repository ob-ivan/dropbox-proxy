<?php
namespace Ob_Ivan\DropboxProxy;

use ArrayAccess;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionAsArray implements ArrayAccess
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    // ArrayAccess //

    public function offsetExists($offset)
    {
        $this->boot();
        return $this->session->has($offset);
    }

    public function offsetGet($offset)
    {
        $this->boot();
        return $this->session->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->boot();
        return $this->session->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->boot();
        return $this->session->remove($offset);
    }

    // protected //

    protected function boot()
    {
        if (! $this->session->isStarted()) {
            $this->session->start();
        }
    }
}
