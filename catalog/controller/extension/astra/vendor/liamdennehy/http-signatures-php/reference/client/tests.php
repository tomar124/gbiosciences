<?php

namespace AstraPrefixed;

require './vendor/autoload.php';
require __DIR__ . '/formatMessage.php';
$psr17Factory = new \AstraPrefixed\Nyholm\Psr7\Factory\Psr17Factory();
$psr18Client = new \AstraPrefixed\Buzz\Client\Curl($psr17Factory);
$refRequestFile = \file(__DIR__ . '/../request.http');
$referencePrivateKeyFile = \file_get_contents(__DIR__ . '/../keys/Test-private.pem');
$referencePrivateKey = ['Test' => $referencePrivateKeyFile];
$referenceRequest = formatMessage($refRequestFile);
print "==================================================" . \PHP_EOL;
print "Signing HTTP Messages v10 Reference Implementation" . \PHP_EOL;
print "==================================================" . \PHP_EOL;
print "Using Reference Request:--------------------------" . \PHP_EOL;
foreach ($refRequestFile as $line) {
    print $line;
}
print \PHP_EOL;
print "--------------------------------------------------" . \PHP_EOL;
print "Using Private Key:--------------------------------" . \PHP_EOL;
print $referencePrivateKeyFile;
print "--------------------------------------------------" . \PHP_EOL;
// Default Test
// @see https://tools.ietf.org/html/draft-cavage-http-signatures-10#appendix-C.1
print "Default Test:-------------------------------------" . \PHP_EOL;
$defaultTestContext = new \AstraPrefixed\HttpSignatures\Context(['keys' => $referencePrivateKey, 'algorithm' => 'rsa-sha256']);
$signedRequest = $defaultTestContext->signer()->sign($referenceRequest);
$expectedSignatureHeaderValue = \trim(\file_get_contents(__DIR__ . '/../headers/default-test-signature'));
if ($signedRequest->getHeader('Signature')[0] == $expectedSignatureHeaderValue) {
    print "Signature header correctly generated: '{$expectedSignatureHeaderValue}'" . \PHP_EOL;
} else {
    print "Signature header NOT correctly generated: '{$expectedSignatureHeaderValue}'" . \PHP_EOL;
}
$response = $psr18Client->sendRequest($signedRequest);
$resonseObject = \json_decode((string) $response->getBody());
if ($resonseObject->signatures->Signature) {
    print "Server reports Signature header validated" . \PHP_EOL;
} else {
    print "Server reports Signature header NOT validated !!!!!!!!!!!" . \PHP_EOL;
}
unset($signedRequest);
print "--------------------------------------------------" . \PHP_EOL;
$authorizedRequest = $defaultTestContext->signer()->authorize($referenceRequest);
$expectedAuthorizationHeaderValue = \trim(\file_get_contents(__DIR__ . '/../headers/default-test-authorization'));
if ($authorizedRequest->getHeader('Authorization')[0] == $expectedAuthorizationHeaderValue) {
    print "Authorization header correctly generated: '{$expectedAuthorizationHeaderValue}'" . \PHP_EOL;
} else {
    print "Authorization header NOT correctly generated: '{$expectedAuthorizationHeaderValue}'" . \PHP_EOL;
}
$response = $psr18Client->sendRequest($authorizedRequest);
$resonseObject = \json_decode((string) $response->getBody());
if ($resonseObject->signatures->Authorization) {
    print "Server reports Authorization header validated" . \PHP_EOL;
} else {
    print "Server reports Authorization header NOT validated !!!!!!!!!!!" . \PHP_EOL;
}
print "--------------------------------------------------" . \PHP_EOL;
unset($authorizedRequest);
// Basic Test
// @see https://tools.ietf.org/html/draft-cavage-http-signatures-10#appendix-C.2
print "Basic Test:---------------------------------------" . \PHP_EOL;
$basicTestContext = new \AstraPrefixed\HttpSignatures\Context(['keys' => $referencePrivateKey, 'algorithm' => 'rsa-sha256', 'headers' => ['(request-target)', 'host', 'date']]);
$authorizedRequest = $basicTestContext->signer()->authorize($referenceRequest);
$expectedAuthorizationHeaderValue = \trim(\file_get_contents(__DIR__ . '/../headers/basic-test-authorization'));
if ($authorizedRequest->getHeader('Authorization')[0] == $expectedAuthorizationHeaderValue) {
    print "Authorization header correctly generated: '{$expectedAuthorizationHeaderValue}'" . \PHP_EOL;
} else {
    print "Authorization header NOT correctly generated: '" . $authorizedRequest->getHeader('Authorization')[0] . "'" . \PHP_EOL;
}
$response = $psr18Client->sendRequest($authorizedRequest);
$resonseObject = \json_decode((string) $response->getBody());
if ($resonseObject->signatures->Authorization) {
    print "Server reports Authorization header validated" . \PHP_EOL;
} else {
    print "Server reports Authorization header NOT validated !!!!!!!!!!!" . \PHP_EOL;
}
print "--------------------------------------------------" . \PHP_EOL;
unset($authorizedRequest);
// All Headers Test
// @see https://tools.ietf.org/html/draft-cavage-http-signatures-10#appendix-C.3
print "All Headers Test:---------------------------------" . \PHP_EOL;
$allHeadersTestContext = new \AstraPrefixed\HttpSignatures\Context(['keys' => $referencePrivateKey, 'algorithm' => 'rsa-sha256', 'headers' => ['(request-target)', 'host', 'date', 'content-type', 'digest', 'content-length']]);
$signedRequest = $allHeadersTestContext->signer()->sign($referenceRequest);
$expectedSignatureHeaderValue = \trim(\file_get_contents(__DIR__ . '/../headers/all-headers-test-signature'));
if ($signedRequest->getHeader('Signature')[0] == $expectedSignatureHeaderValue) {
    print "Signature header correctly generated: '{$expectedSignatureHeaderValue}'" . \PHP_EOL;
} else {
    print "Signature header NOT correctly generated: '{$expectedSignatureHeaderValue}'" . \PHP_EOL;
}
$response = $psr18Client->sendRequest($signedRequest);
$resonseObject = \json_decode((string) $response->getBody());
if ($resonseObject->signatures->Signature) {
    print "Server reports Signature header validated" . \PHP_EOL;
} else {
    print "Server reports Signature header NOT validated !!!!!!!!!!!" . \PHP_EOL;
}
unset($signedRequest);
print "--------------------------------------------------" . \PHP_EOL;
$authorizedRequest = $allHeadersTestContext->signer()->authorize($referenceRequest);
$expectedAuthorizationHeaderValue = \trim(\file_get_contents(__DIR__ . '/../headers/all-headers-test-authorization'));
if ($authorizedRequest->getHeader('Authorization')[0] == $expectedAuthorizationHeaderValue) {
    print "Authorization header correctly generated: '{$expectedAuthorizationHeaderValue}'" . \PHP_EOL;
} else {
    print "Authorization header NOT correctly generated: '{$expectedAuthorizationHeaderValue}'" . \PHP_EOL;
}
$response = $psr18Client->sendRequest($authorizedRequest);
$resonseObject = \json_decode((string) $response->getBody());
if ($resonseObject->signatures->Authorization) {
    print "Server reports Authorization header validated" . \PHP_EOL;
} else {
    print "Server reports Authorization header NOT validated !!!!!!!!!!!" . \PHP_EOL;
}
print "--------------------------------------------------" . \PHP_EOL;
unset($authorizedRequest);
