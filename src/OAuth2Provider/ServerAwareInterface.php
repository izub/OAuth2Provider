<?php
namespace OAuth2Provider;

interface ServerAwareInterface
{
    /**
     * Set the server instance
     *
     * @param Server $server
     */
    public function setServer(Server $server);
}
