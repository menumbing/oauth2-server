<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server\Model;

use Menumbing\OAuth2\Server\Contract\TokenModelInterface;
use Menumbing\Orm\Relation\BelongsTo;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AccessToken extends Token implements TokenModelInterface
{
    public null|string $table = 'oauth_access_tokens';

    protected array $casts = [
        'scopes' => 'array',
        'revoked' => 'bool',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determines if the given scope is allowed based on the list of available scopes.
     *
     * @param  string  $scope  The scope to be checked for permission.
     *
     * @return bool Returns true if the given scope is allowed, otherwise false.
     */
    public function can(string $scope): bool
    {
        $scopes = $this->getAttribute('scopes');

        if (in_array('*', $scopes)) {
            return true;
        }

        return array_key_exists($scope, array_flip($scopes));
    }

    /**
     * Determines if the given scope is not allowed based on the list of available scopes.
     *
     * @param  string  $scope  The scope to be checked for lack of permission.
     *
     * @return bool Returns true if the given scope is not allowed, otherwise false.
     */
    public function cant(string $scope): bool
    {
        return !$this->can($scope);
    }
}
