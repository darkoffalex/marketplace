<?php
use app\models\User;
use app\modules\admin\helpers\Access;
?>

<?php /* @var $user User */ ?>
<?php $user = Yii::$app->user->identity; ?>

<aside class="main-sidebar">

    <section class="sidebar">
        <?php $c = Yii::$app->controller->id; ?>
        <?php $a = Yii::$app->controller->action->id; ?>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                'items' => [
                    [
                        'label' => Yii::t('app','Main'),
                        'icon' => 'home',
                        'active' => $c == 'main' && $a == 'index',
                        'visible' => Access::has($user,'main'),
                        'url' => ['/admin/main/index'],
                    ],
                    [
                        'label' => Yii::t('app','Users'),
                        'icon' => 'users',
                        'active' => $c == 'users',
                        'visible' => Access::has($user,'users'),
                        'url' => ['/admin/users/index'],
                    ],
                    [
                        'label' => Yii::t('app','Localization'),
                        'icon' => 'globe',
                        'active' => in_array($c,['languages','labels']),
                        'visible' => Access::has($user,'languages') || Access::has($user,'labels'),
                        'items' => [
                            [
                                'label' => Yii::t('app','Languages'),
                                'active' => $c == 'languages',
                                'visible' => Access::has($user,'languages'),
                                'url' => ['/admin/languages/index'],
                            ],
                            [
                                'label' => Yii::t('app','Labels'),
                                'active' => $c == 'labels',
                                'visible' => Access::has($user,'labels'),
                                'url' => ['/admin/labels/index'],
                            ],

                        ],
                    ],

                    [
                        'label' => Yii::t('app','Countries'),
                        'icon' => 'map-marker',
                        'active' => $c == 'countries',
                        'visible' => Access::has($user,'countries'),
                        'url' => ['/admin/countries/index'],
                    ],

                    [
                        'label' => Yii::t('app','Categories'),
                        'icon' => 'folder',
                        'active' => $c == 'categories',
                        'visible' => true,
                        'url' => ['/admin/categories/index'],
                    ],
                    
                    [
                        'label' => Yii::t('app','Settings'),
                        'icon' => 'gear',
                        'active' => $c == 'settings',
                        'visible' => Access::has($user,'settings'),
                        'url' => ['/admin/settings/index'],
                    ],
                    [
                        'label' => Yii::t('app','Exit'),
                        'icon' => 'sign-out',
                        'url' => ['/admin/main/logout']
                    ]
                ],
            ]
        ) ?>
    </section>

</aside>
