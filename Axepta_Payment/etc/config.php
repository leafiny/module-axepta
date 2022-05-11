<?php

$config = [
    'model' => [
        Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD => [
            'class'        => Axepta_Model_Payment_Online_Axepta::class,
            'title'        => 'Credit Card',
            'description'  => 'Credit Card Payment',
            'merchant_id'  => '',
            'hmac_key'     => '',
            'blowfish_key' => '',
            'order_desc'   => 'Cart Payment',
            'is_enabled'   => true,
        ],
        'axepta' => [
            'class' => Axepta_Model_Payment_Axepta::class,
        ],
    ],

    'page' => [
        '/payment/axepta/notify/' => [
            'class'    => Axepta_Page_Notify::class,
            'template' => null,
        ],
        '/payment/axepta/success/' => [
            'class'      => Axepta_Page_Success::class,
            'template'   => 'Axepta_Payment::page/redirect.twig',
            'content'    => 'Axepta_Payment::page/complete/redirect.twig',
            'meta_title' => 'Payment Complete',
            'javascript' => [
                'Axepta_Payment::js/redirect.js' => 10,
            ],
        ],
        '/payment/axepta/failure/' => [
            'class'      => Axepta_Page_Failure::class,
            'template'   => 'Axepta_Payment::page/redirect.twig',
            'content'    => 'Axepta_Payment::page/complete/redirect.twig',
            'meta_title' => 'Payment Complete',
            'javascript' => [
                'Axepta_Payment::js/redirect.js' => 10,
            ],
        ],
        '/payment/axepta/cancel/' => [
            'class'      => Axepta_Page_Cancel::class,
            'template'   => 'Axepta_Payment::page/redirect.twig',
            'content'    => 'Axepta_Payment::page/complete/redirect.twig',
            'meta_title' => 'Payment Complete',
            'javascript' => [
                'Axepta_Payment::js/redirect.js' => 10,
            ],
        ],
        '/payment/axepta/complete/redirect/' => [
            'class'      => Axepta_Page_Complete_Redirect::class,
            'template' => null,
        ]
    ],

    'helper' => [
        'payment' => [
            'payment_methods' => [
                Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD => 100,
            ],
        ],
        'axepta' => [
            'class' => Axepta_Helper_Axepta::class,
        ]
    ],

    'block' => [
        Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD . '.payment.info' => [
            'template' => 'Axepta_Payment::block/axepta/info.twig',
        ],
        Axepta_Model_Payment_Online_Axepta::PAYMENT_METHOD . '.payment.complete' => [
            'template' => 'Axepta_Payment::block/axepta/complete.twig',
        ],
    ],

];