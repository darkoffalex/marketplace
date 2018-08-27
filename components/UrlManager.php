<?php

namespace app\components;

use yii\web\UrlManager as BaseUrlManager;
use Yii;

/**
 * Class UrlManager
 * @package app\components
 */
class UrlManager extends BaseUrlManager
{
    /**
     * Переопредление для форимирующего URL метода (для поддержки мульти-язычности)
     * @param array|string $params
     * @return string
     */
    public function createUrl($params)
    {
        $lng = Yii::$app->controller->module->id == 'admin' ? null : Yii::$app->language;
        $domain = Yii::$app->request->get('domain');
        $subDomain = Yii::$app->request->get('subDomain');
        $protocol = Yii::$app->request->get('protocol');

        $url = parent::createUrl($params + [
                'language' => $lng,
                'domain' => $domain,
                'subDomain' => $subDomain,
                'protocol' => $protocol]);

        return $url;
    }
}