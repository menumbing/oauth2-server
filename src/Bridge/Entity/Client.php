<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Bridge\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Client implements ClientEntityInterface
{
    use ClientTrait;

    /**
     * The client identifier.
     */
    protected string $identifier;

    /**
     * Create a new client instance.
     *
     * @param  string  $identifier
     * @param  string  $name
     * @param  string  $redirectUri
     * @param  bool  $isConfidential
     * @return void
     */
    public function __construct(string $identifier, string $name, string $redirectUri, bool $isConfidential = false)
    {
        $this->setIdentifier($identifier);

        $this->name = $name;
        $this->isConfidential = $isConfidential;
        $this->redirectUri = explode(',', $redirectUri);
    }

    /**
     * Get the client's identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Set the client's identifier.
     */
    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }
}
