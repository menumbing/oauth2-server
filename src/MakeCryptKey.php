<?php

declare(strict_types=1);

namespace Menumbing\OAuth2\Server;

use League\OAuth2\Server\CryptKey;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait MakeCryptKey
{
    protected function makeKey(string $pathOrContent): CryptKey
    {
        $key = str_replace('\\n', "\n", $pathOrContent);

        if (file_exists($key)) {
            $key = file_get_contents($key);
        }

        return new CryptKey($key, null, false);
    }
}
