<?php declare(strict_types=1);

namespace MauticPlugin\MauticColdEmailOutreachBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class ColdEmailOutreachIntegration extends AbstractIntegration
{
    public const INTEGRATION_NAME = 'ColdEmailOutreach';

    public function getName(): string
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName(): string
    {
        return 'Cold Email Outreach';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [
        ];
    }

    public function getIcon(): string
    {
        return 'plugins/MauticColdEmailOutreachBundle/Assets/img/logo.png';
    }
}
