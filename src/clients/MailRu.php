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
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        $request = $this->createApiRequest()->setMethod('GET')->setUrl('users.getInfo');
        $response = $request->send();
        $response->setFormat('json');

        if ($response->isOk && $response->data && $response->data['0']) {
            return $response->data['0'];
        }

        throw new InvalidResponseException($response);
    }

    /**
     * @inheritdoc
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        parent::applyAccessTokenToRequest($request, $accessToken);

        $data = $request->getData();

        $data['method'] = str_replace('/', '', $request->getUrl());
        $data['uids'] = $accessToken->getParam('x_mailru_vid');
        $data['app_id'] = $this->clientId;
        $data['secure'] = 1;
        $data['sig'] = $this->sig($data, $this->clientSecret);

        $request->setUrl('');
        $request->setData($data);
    }

    /**
     * Generate signature for API mail.ru
     *
     * @return string
     */
    public function sig(array $request_params, $secret_key) {
        ksort($request_params);
        $params = '';

        foreach ($request_params as $key => $value) {
            $params .= "$key=$value";
        }

        return md5($params . $secret_key);
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
    
    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'id' => 'uid'
        ];
    }
}