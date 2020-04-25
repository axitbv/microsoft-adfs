<?php

namespace SocialiteProviders\ADFS;

use Lcobucci\JWT\Parser;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'ADFS';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['openid'];

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['adfs_host'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->config['adfs_host'].'/adfs/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return $this->config['adfs_host'].'/adfs/oauth2/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $token = (new Parser())->parse((string) $token);

        return $token->getClaims();
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        /*
         * $user = [
         *   "aud": "urn:oidc:some:app",
         *   "iss": "http:\/\/sts.somecorp.com\/adfs\/services\/trust",
         *   "iat": 1585036023,
         *   "exp": 1585039623,
         *   "upn": "some_user@example.com",
         *   "commonname": "some_user",
         *   "email": "some_user@example.com",
         *   "apptype": "Confidential",
         *   "appid": "50ce8764-some-guid-96c0-39cc601ea154",
         *   "authmethod": "urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport",
         *   "auth_time": "2020-03-24T07:30:55.566Z",
         *   "ver": "1.0",
         *   "scp": "openid"
         * ];
         */
        return (new User())->setRaw($user)->map([
            'id'       => null,
            'nickname' => null,
            'name'     => $user['commonname'],
            'email'    => $user['email'],
            'avatar'   => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
