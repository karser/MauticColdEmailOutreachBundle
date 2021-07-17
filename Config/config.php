<?php declare(strict_types=1);

return [
    'name'        => 'MauticColdEmailOutreachBundle',
    'description' => 'Cold email outreach features for Mautic',
    'version'     => '1.0',
    'author'      => 'Dmitrii Poddubnyi',

    'routes' => [
    ],

    'services'   => [
        'events'       => [
            'coldemailoutreach.same_subject_followup.subscriber' => [
                'class'     => \MauticPlugin\MauticColdEmailOutreachBundle\EventListener\SameSubjectFollowupSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.campaign.repository.lead_event_log',
                    'mautic.email.repository.email',
                    'mautic.email.repository.stat',
                ],
            ],
        ],
        'forms'        => [
        ],
        'models'       => [
        ],
        'integrations' => [
            'mautic.integration.coldemailoutreach' => [
                'class' => \MauticPlugin\MauticColdEmailOutreachBundle\Integration\ColdEmailOutreachIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
        'others'       => [
        ],
        'controllers'  => [
        ],
        'commands'     => [
        ],
    ],
    'parameters' => [
    ],
];
