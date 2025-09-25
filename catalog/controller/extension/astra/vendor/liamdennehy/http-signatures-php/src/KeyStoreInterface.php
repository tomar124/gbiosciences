<?php

namespace AstraPrefixed\HttpSignatures;

interface KeyStoreInterface
{
    /**
     * return the secret for the specified $keyId.
     *
     * @param string $keyId
     *
     * @return Key
     */
    public function fetch($keyId);
}
