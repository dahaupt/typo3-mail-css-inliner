<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3 Mail CSS Inliner',
    'description' => 'Inlines CSS in all outgoing TYPO3 mails.',
    'category' => 'misc',
    'author' => 'Mathias Brodala',
    'author_email' => 'mbrodala@pagemachine.de',
    'author_company' => 'Pagemachine AG',
    'state' => 'stable',
    'version' => '2.1.1',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-7.99.99',
            'typo3' => '9.5.0-11.5.99',
        ],
    ],
];
