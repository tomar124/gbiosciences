<?php

namespace AstraPrefixed\GetAstra\Client\Controller\Auth;

use AstraPrefixed\GetAstra\Client\Helper\CommonHelper;
use AstraPrefixed\GetAstra\Client\Service\OAuthService;
use AstraPrefixed\Psr\Container\ContainerInterface;
use AstraPrefixed\Psr\Http\Message\ResponseInterface;
use AstraPrefixed\Psr\Http\Message\ServerRequestInterface;
use AstraPrefixed\Psr\Log\LoggerInterface;
use AstraPrefixed\Psr\SimpleCache\CacheInterface;
use AstraPrefixed\Psr\SimpleCache\InvalidArgumentException;
use AstraPrefixed\Slim\Csrf\Guard;
use AstraPrefixed\Slim\Http\Request;
use AstraPrefixed\Slim\Http\Response;
use AstraPrefixed\Slim\Http\StatusCode;
use AstraPrefixed\GuzzleHttp\Client;
use AstraPrefixed\GuzzleHttp\Psr7\Request as GuzzleRequest;
use AstraPrefixed\Respect\Validation\Validator as v;
class LoginController
{
    private $view;
    /**
     * @var Guard
     */
    private $csrf;
    /**
     * @var CacheInterface
     */
    private $options;
    /**
     * @var OAuthService
     */
    private $oauthService;
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $siteSettingsService;
    private $loginSubmitFormAction;
    private $authLoginPath;
    /**
     * @var ContainerInterface
     */
    private $container;
    private const LOGIN_FIELDS_REQUIRED = ['wafClientId', 'wafClientPassword', 'oauthClientId', 'oauthClientSecret', 'redirectUri', 'clientApiToken'];
    private $dashboardUrl;
    public const IP_HEADER_KEY = 'ipHeaderKey';
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->view = $container->get('view');
        $this->csrf = $container->get('csrf');
        $this->options = $container->get('options');
        $this->oauthService = $container->get('oauth');
        $this->siteSettingsService = $container->get('siteSettings');
        $this->logger = $container->get('logger');
        $this->authLoginPath = $container->get('router')->pathFor('auth.login');
        //for the routes (submit login form action) to work properly on nginx servers
        $route = $container->get('router')->pathFor('auth.login');
        $x = \strpos($route, 'api');
        $this->loginSubmitFormAction = \substr_replace($route, '?astraRoute=api/login', $x - 1);
        $this->dashboardUrl = CommonHelper::customGetEnv('ASTRA_DASHBOARD_URL_HTTPS');
    }
    public function login(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($this->oauthService->isLoggedIn(\true)) {
            if ($this->options->get('siteId')) {
                $successButtonHref = $this->dashboardUrl . 'waf/' . $this->options->get('siteId') . '/dashboard';
            } else {
                $successButtonHref = $this->dashboardUrl . 'waf/';
            }
            return $this->view->render($response, 'successfulLogin.php', [
                'csrfNameKey' => $this->csrf->getTokenNameKey(),
                'csrfValueKey' => $this->csrf->getTokenValueKey(),
                'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()),
                'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()),
                //'success' => 'Visit the dashboard.',
                'siteId' => $this->options->get('siteId'),
                'successButtonHref' => $successButtonHref,
                'authLoginPath' => $this->authLoginPath,
            ]);
        } else {
            return $this->view->render($response, 'login.php', ['csrfNameKey' => $this->csrf->getTokenNameKey(), 'csrfValueKey' => $this->csrf->getTokenValueKey(), 'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()), 'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()), 'loginSubmit' => $this->loginSubmitFormAction]);
        }
    }
    /**
     * @return ResponseInterface
     *
     * @throws InvalidArgumentException
     */
    public function token(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var Request $request */
        /** @var Response $response */
        $fullJsonString = $request->getParsedBodyParam('loginJson');
        // ip detection
        $detectedIpV4 = $request->getParsedBodyParam('detectedIpV4');
        $detectedIpV6 = $request->getParsedBodyParam('detectedIpV6');
        $detectionResult = $this->ipApi($detectedIpV4, $detectedIpV6);
        if ($detectionResult === \false) {
            // not stopping logins now
            // return $this->view->render($response, 'login.php', [
            //     'csrfNameKey' => $this->csrf->getTokenNameKey(),
            //     'csrfValueKey' => $this->csrf->getTokenValueKey(),
            //     'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()),
            //     'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()),
            //     'error' => "We were unable to determine the original visitor IP, because of a misconfiguration on your server. Know more.",
            //     'loginSubmit' => $this->loginSubmitFormAction,
            // ]);
        }
        $json = \json_decode(\base64_decode($fullJsonString), \true);
        $validate = v::key('wafClientId', v::stringType()->notEmpty())->key('wafClientPassword', v::stringType()->notEmpty())->key('oauthClientId', v::stringType()->notEmpty())->key('oauthClientSecret', v::stringType()->notEmpty())->key('redirectUri', v::stringType()->notEmpty())->key('clientApiToken', v::stringType()->notEmpty())->validate($json);
        if (!$validate) {
            return $this->view->render($response, 'login.php', ['csrfNameKey' => $this->csrf->getTokenNameKey(), 'csrfValueKey' => $this->csrf->getTokenValueKey(), 'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()), 'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()), 'error' => "Don't recognize this activation code. Try again?", 'loginSubmit' => $this->loginSubmitFormAction]);
        }
        if ($request->getParsedBodyParam('loginJson')) {
            $login = $this->oauthService->login($json, \true);
            if ($login['error'] == \false) {
                // success
                $this->options->set('siteId', $json['wafClientId']);
                $this->options->set('wafClientId', $json['wafClientId']);
                $this->options->set('wafClientPassword', $json['wafClientPassword']);
                $this->options->set('oauthClientId', $json['oauthClientId']);
                $this->options->set('oauthClientSecret', $json['oauthClientSecret']);
                $this->options->set('redirectUri', $json['redirectUri']);
                $this->options->set('clientApiToken', $json['clientApiToken']);
                $functionRes = $this->siteSettingsService->saveSiteSettingsLocally(\true);
                if ($functionRes['error']) {
                    return $this->view->render($response, 'login.php', ['csrfNameKey' => $this->csrf->getTokenNameKey(), 'csrfValueKey' => $this->csrf->getTokenValueKey(), 'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()), 'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()), 'error' => $functionRes['errorMessage'], 'loginSubmit' => $this->loginSubmitFormAction]);
                }
                return $this->view->render($response, 'successfulLogin.php', ['csrfNameKey' => $this->csrf->getTokenNameKey(), 'csrfValueKey' => $this->csrf->getTokenValueKey(), 'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()), 'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()), 'success' => 'Visit the Dashboard', 'siteId' => $json['wafClientId'], 'successButtonHref' => $this->dashboardUrl . "waf/" . $json['wafClientId'] . "/dashboard", 'authLoginPath' => $this->authLoginPath]);
            } else {
                //$this->logger->warning('OAuth login failed');
                return $this->view->render($response, 'login.php', ['csrfNameKey' => $this->csrf->getTokenNameKey(), 'csrfValueKey' => $this->csrf->getTokenValueKey(), 'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()), 'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()), 'error' => $login['errorMsg'], 'authLoginPath' => $this->authLoginPath]);
            }
        } else {
            return $this->view->render($response, 'login.php', ['csrfNameKey' => $this->csrf->getTokenNameKey(), 'csrfValueKey' => $this->csrf->getTokenValueKey(), 'csrfName' => $request->getAttribute($this->csrf->getTokenNameKey()), 'csrfValue' => $request->getAttribute($this->csrf->getTokenValueKey()), 'error' => 'Unable to parse credentials - please enter the credentials you received from the Astra Dashboard', 'authLoginPath' => $this->authLoginPath]);
        }
    }
    public function directLogin(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var Request $request */
        /** @var Response $response */
        $commonHelper = new CommonHelper();
        $verifySignedRequest = $commonHelper->verifySignedHttpRequest($request, $this->container);
        //verify signed requests
        if (!$verifySignedRequest['requestVerified']) {
            return $response->withJson(['error' => 'Unauthorized'], StatusCode::HTTP_UNAUTHORIZED);
        }
        $creds = $request->getParsedBodyParam('loginJson', null);
        if (\is_null($creds)) {
            return $response->withJson(['error' => 'Missing credentials'], StatusCode::HTTP_BAD_REQUEST);
        }
        foreach (self::LOGIN_FIELDS_REQUIRED as $checkKey) {
            if (!isset($creds[$checkKey])) {
                return $response->withJson(['error' => 'Missing login field'], StatusCode::HTTP_BAD_REQUEST);
            }
        }
        //store creds
        $this->options->set('directSymfonyLogin', $creds);
        return $response->withJson(['error' => 'Login Success'], StatusCode::HTTP_OK);
    }
    /**
     * Function checks if the detected IP is present in any of the $_SERVER header
     * if yes then sets that header in the options
     * gives preference to ipv4
     * @param string $realVisitorIpV4 ipv4 address
     * @param string $realVisitorIpV6 ipv6 address
     * @return bool
     */
    private function ipApi($realVisitorIpV4, $realVisitorIpV6)
    {
        if (empty($realVisitorIpV4) && empty($realVisitorIpV6)) {
            //  return false if ip detection failed in the front end login template
            return \false;
        }
        $ip_keys = ['REMOTE_ADDR', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_CF_CONNECTING_IP', 'HTTP_X_SUCURI_CLIENTIP', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($realVisitorIpV6)) {
            $useHeader = [];
            foreach ($ip_keys as $header) {
                if (isset($_SERVER[$header]) && $_SERVER[$header] == $realVisitorIpV6) {
                    \array_push($useHeader, $header);
                }
            }
            if (!empty($useHeader)) {
                $this->options->set(self::IP_HEADER_KEY, $useHeader);
                return \true;
            }
        }
        if (!empty($realVisitorIpV4)) {
            $useHeader = [];
            foreach ($ip_keys as $header) {
                if (isset($_SERVER[$header]) && $_SERVER[$header] == $realVisitorIpV4) {
                    \array_push($useHeader, $header);
                }
            }
            if (!empty($useHeader)) {
                $this->options->set(self::IP_HEADER_KEY, $useHeader);
                return \true;
            }
        }
        return \false;
    }
}
