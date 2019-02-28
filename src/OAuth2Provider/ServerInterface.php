<?php
namespace OAuth2Provider;

interface ServerInterface
{
    /**
     * Set the Oauth2 server instance
     *
     * @param \OAuth2\Server $server
     */
    public function setOAuth2Server(\OAuth2\Server $server);

    public function setRequest(\OAuth2\Request $request): Server;

    public function setResponse(\OAuth2\Response $response): Server;
}
