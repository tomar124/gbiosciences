<?php

namespace AstraPrefixed\HttpSignatures;

class Key
{
    /** @var string */
    private $id;
    /** @var string */
    private $secret;
    /** @var resource */
    private $certificate;
    /** @var resource */
    private $publicKey;
    /** @var resource */
    private $privateKey;
    /** @var string */
    private $type;
    /**
     * @param string       $id
     * @param string|array $secret
     */
    public function __construct($id, $keys)
    {
        $this->opensslVersion = \explode(' ', \OPENSSL_VERSION_TEXT)[1];
        $this->opensslMajor = \explode('.', $this->opensslVersion)[0];
        $this->opensslMinor = \explode('.', $this->opensslVersion)[1];
        $this->opensslPatch = \explode('.', $this->opensslVersion)[2];
        $this->id = $id;
        $publicKey = null;
        $privateKey = null;
        $secret = null;
        if (!\is_array($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $key) {
            $pkiKey = Key::getPKIKeys($key);
            if (!$pkiKey) {
                if (0 != \strpos($key, 'BEGIN')) {
                    throw new KeyException('Input looks like PEM but key not understood using OpenSSL ' . $this->opensslVersion . ': ' . $key, 1);
                }
                if (empty($secret)) {
                    $secret = $key;
                } else {
                    throw new KeyException('Multiple secrets provided', 1);
                }
            } else {
                if (!empty($pkiKey['public']) && !empty($publicKey)) {
                    if (\openssl_pkey_get_details($publicKey)['key'] != \openssl_pkey_get_details($pkiKey['public'])['key']) {
                        throw new KeyException('Multiple different public keys provided', 1);
                    }
                } elseif (!empty($pkiKey['private']) && !empty($privateKey)) {
                    if (\openssl_pkey_get_details($privateKey)['key'] != \openssl_pkey_get_details($pkiKey['private'])['key']) {
                        throw new KeyException('Multiple different private keys provided', 1);
                    }
                } elseif (!empty($pkiKey['public']) && empty($publicKey)) {
                    $publicKey = $pkiKey['public'];
                } elseif (!empty($pkiKey['private']) && empty($privateKey)) {
                    $privateKey = $pkiKey['private'];
                }
            }
        }
        if ($publicKey || $privateKey) {
            if (!empty($secret)) {
                throw new KeyException(!empty($secret) . 'Input has secret(s) and PKI keys, cannot process', 1);
            }
            $this->class = 'asymmetric';
            $this->privateKey = $privateKey;
            $this->publicKey = $publicKey;
            $this->type = $pkiKey['type'];
            if ('ec' == $pkiKey['type']) {
                $this->curve = $pkiKey['curve'];
            }
        } else {
            $this->class = 'secret';
            $this->secret = $secret;
        }
    }
    public static function getPKIKeys($item)
    {
        $keyTypes = ['rsa', 'ec', 'dsa'];
        $eCCurves = [];
        $key['public'] = null;
        $key['private'] = null;
        $key['curve'] = null;
        if (Key::hasPrivateKey($item)) {
            $key['private'] = Key::getPrivateKey($item);
        } elseif (Key::isX509Certificate($item)) {
            $key['public'] = Key::fromX509Certificate($item);
        } elseif (Key::isPublicKey($item)) {
            $key['public'] = Key::getPublicKey($item);
        } else {
            return \false;
        }
        if (!empty($key['public'])) {
            $keyDetails = \openssl_pkey_get_details($key['public']);
        } else {
            $keyDetails = \openssl_pkey_get_details($key['private']);
        }
        unset($keyDetails['key']);
        unset($keyDetails['bits']);
        unset($keyDetails['type']);
        $type = \array_intersect($keyTypes, \array_keys($keyDetails));
        if (\sizeof($type) > 1) {
            throw new KeyException("Unknown key semantics, multiple recognised key types found: '" . \implode(',' . $type) . "'", 1);
        } elseif (0 == \sizeof($type)) {
            throw new KeyException('Unknown key semantics, no recognised key types found: ' . \implode(',', \array_keys(\openssl_pkey_get_details($key['private']))) . ':' . $item, 1);
        }
        $key['type'] = \array_keys($keyDetails)[0];
        if ('ec' == $key['type']) {
            $key['curve'] = $keyDetails[$key['type']]['curve_name'];
        }
        return $key;
    }
    /**
     * Retrieves private key resource from a input string or
     * array of strings.
     *
     * @param string|array $object PEM-format Private Key or file path to same
     *
     * @return resource|false
     */
    private static function getPrivateKey($key)
    {
        // OpenSSL libraries don't have detection methods, so try..catch
        try {
            $privateKey = \openssl_get_privatekey($key);
            return $privateKey;
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * Retrieves public key resource from a input string or
     * array of strings.
     *
     * @param string|array $object PEM-format Public Key or file path to same
     *
     * @return resource|false
     */
    private static function getPublicKey($object)
    {
        if (\is_array($object)) {
            // If we implement key rotation in future, this should add to a collection
            foreach ($object as $candidateKey) {
                $publicKey = Key::getPublicKey($candidateKey);
                if ($publicKey) {
                    return $publicKey;
                }
            }
        } else {
            // OpenSSL libraries don't have detection methods, so try..catch
            try {
                $publicKey = \openssl_get_publickey($object);
                return $publicKey;
            } catch (\Exception $e) {
                return null;
            }
        }
    }
    public static function fromX509Certificate($certificate)
    {
        $publicKey = \openssl_get_publickey($certificate);
        return $publicKey;
    }
    /**
     * Signing HTTP Messages 'keyId' field.
     *
     * @return string
     *
     * @throws KeyException
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Retrieve Verifying Key - Public Key for Asymmetric/PKI, or shared secret for HMAC.
     *
     * @return string Shared Secret or PEM-format Public Key
     *
     * @throws KeyException
     */
    public function getVerifyingKey()
    {
        switch ($this->class) {
            case 'asymmetric':
                if ($this->publicKey) {
                    return \openssl_pkey_get_details($this->publicKey)['key'];
                } else {
                    return null;
                }
                break;
            case 'secret':
                return $this->secret;
            default:
                throw new KeyException("Unknown key class {$this->class}");
        }
    }
    /**
     * Retrieve Signing Key - Private Key for Asymmetric/PKI, or shared secret for HMAC.
     *
     * @return string Shared Secret or PEM-format Private Key
     *
     * @throws KeyException
     */
    public function getSigningKey()
    {
        switch ($this->class) {
            case 'asymmetric':
                if ($this->privateKey) {
                    \openssl_pkey_export($this->privateKey, $pem);
                    return $pem;
                } else {
                    return null;
                }
                break;
            case 'secret':
                return $this->secret;
            default:
                throw new KeyException("Unknown key class {$this->class}");
        }
    }
    /**
     * @return string 'secret' for HMAC or 'asymmetric' for RSA/EC
     */
    public function getClass()
    {
        return $this->class;
    }
    public function getType()
    {
        switch ($this->class) {
            case 'secret':
                return 'hmac';
                break;
            case 'asymmetric':
                return $this->type;
                break;
            default:
                throw new KeyException("Unknown key class '{$this->class}' fetching algorithm", 1);
                break;
        }
    }
    public function getCurve()
    {
        return $this->curve;
    }
    /**
     * Test if $object is, points to or contains, X.509 PEM-format certificate.
     *
     * @param string|array $object PEM Format X.509 Certificate or file path to one
     *
     * @return bool
     */
    public static function isX509Certificate($object)
    {
        try {
            $errorLevel = \error_reporting(\E_ERROR);
            \openssl_x509_export($object, $test);
            \error_reporting($errorLevel);
            return !empty($test);
        } catch (\Exception $e) {
            \error_reporting($errorLevel);
            return \false;
        }
    }
    public static function isPublicKey($object)
    {
        return Key::hasPublicKey($object) && !Key::hasPrivateKey($object) && !Key::isX509Certificate($object);
    }
    public static function isPrivateKey($object)
    {
        return Key::hasPrivateKey($object) && !Key::isPublicKey($object);
    }
    public static function hasPKIKey($item)
    {
        return Key::hasPublicKey($item) || Key::hasPrivateKey($item);
    }
    public static function hasPublicKey($object)
    {
        if (Key::isX509Certificate($object)) {
            return \true;
        }
        try {
            $errorLevel = \error_reporting(\E_ERROR);
            $result = \openssl_pkey_get_public($object);
            \error_reporting($errorLevel);
            return !empty($result);
        } catch (\Exception $e) {
            \error_reporting($errorLevel);
            return \false;
        }
    }
    /**
     * Test if $object is, points to or contains, PEM-format Private Key.
     *
     * @param string|array $object PEM-format Private Key or file path to one
     *
     * @return bool
     */
    public static function hasPrivateKey($object)
    {
        try {
            $errorLevel = \error_reporting(\E_ERROR);
            $result = \openssl_pkey_get_private($object);
            \error_reporting($errorLevel);
            return !empty($result);
        } catch (\Exception $e) {
            \error_reporting($errorLevel);
            return \false;
        }
    }
    public static function isPKIKey($item)
    {
        return Key::isPrivateKey($item) || Key::isPublicKey($item);
    }
}
