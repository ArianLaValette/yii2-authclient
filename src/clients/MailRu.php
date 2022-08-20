<?php
/**
 * yii2 mailru authclient
 */

namespace yii\authclient\clients;


use yii\authclient\OAuth2;

/**
 * In order to use Mail.ru OAuth you must register your application at <https://api.mail.ru/sites/my/add>.
 *
 * Example application configuration:
 *
 * components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'mailru' => [
 *                  'class' => 'yii\authclient\clients\Mailru',
 *                  'clientId' => 'mailru_app_id',
 *                  'clientSecret' => 'mailru_app_secret_key',
 *             ],
 *         ],
 *     ]
 *     ...
 *
 * @see    http://api.mail.ru/sites/my/add
 * @see    http://api.mail.ru/docs/guides/oauth/sites/
 * @see    http://api.mail.ru/docs/reference/js/users.getInfo/
 * 
 */

class MailRu extends OAuth2{

    /**
     * @inheritdoc
     */
    public $authUrl = 'https://connect.mail.ru/oauth/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://connect.mail.ru/oauth/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'http://www.appsmail.ru/platform/api?method=';

 
    /**
     * {@inheritdoc}
     */
    protected function initUserAttributes()
    {
        return $this->api('info', 'GET');
    }

    /**
     * {@inheritdoc}
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();
        if (!isset($data['format'])) {
            $data['format'] = 'json';
        }
        $data['oauth_token'] = $accessToken->getToken();
        $request->setData($data);
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultName()
    {
        return 'mailru';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'MailRu';
    }


}