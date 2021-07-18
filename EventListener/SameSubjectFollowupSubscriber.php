<?php declare(strict_types=1);

namespace MauticPlugin\MauticColdEmailOutreachBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Entity\Campaign;
use Mautic\CampaignBundle\Entity\Event;
use Mautic\CampaignBundle\Entity\LeadEventLog;
use Mautic\CampaignBundle\Entity\LeadEventLogRepository;
use Mautic\CampaignBundle\Event\CampaignTriggerEvent;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Entity\EmailRepository;
use Mautic\EmailBundle\Entity\Stat;
use Mautic\EmailBundle\Entity\StatRepository;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticColdEmailOutreachBundle\Integration\ColdEmailOutreachIntegration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SameSubjectFollowupSubscriber implements EventSubscriberInterface
{
    private $enabled;
    private $eventLogRepository;
    private $emailRepository;
    private $statRepository;

    /** @var Campaign|null */
    private $campaign;

    public function __construct(IntegrationHelper $integrationHelper,
                                LeadEventLogRepository $eventLogRepository,
                                EmailRepository $emailRepository,
                                StatRepository $statRepository
    )
    {
        $integrationObject = $integrationHelper->getIntegrationObject(ColdEmailOutreachIntegration::INTEGRATION_NAME);
        $this->enabled = $integrationObject instanceof AbstractIntegration && $integrationObject->getIntegrationSettings()->getIsPublished();
        $this->eventLogRepository = $eventLogRepository;
        $this->emailRepository = $emailRepository;
        $this->statRepository = $statRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CampaignEvents::CAMPAIGN_ON_TRIGGER => ['onCampaignTriggered', 0],
            EmailEvents::EMAIL_ON_SEND => ['onEmailSend', 255],
        ];
    }

    public function onCampaignTriggered(CampaignTriggerEvent $event)
    {
        if (!$this->enabled) {
            return;
        }
        $this->campaign = $event->getCampaign();
    }

    public function onEmailSend(EmailSendEvent $event)
    {
        if (!$this->enabled || null === $this->campaign || null === $leadId = $this->getLeadId($event)) {
            return;
        }
        $email = $this->getFirstSentEmailVariant($this->campaign, $leadId);
        if (null !== $email) {
            $event->setSubject('Re: '.$email->getSubject());
        }
    }

    private function getFirstSentEmailVariant(Campaign $campaign, int $leadId): ?Email
    {
        /** @var LeadEventLog|null $firstSentEmailWithinCampaign */
        $firstSentEmailWithinCampaign = $this->eventLogRepository->findOneBy([
            'lead' => $leadId,
            'campaign' => $campaign,
            'channel' => 'email',
        ], ['dateTriggered' => 'ASC']);
        if (null === $firstSentEmailWithinCampaign) {
            return null;
        }
        /** @var Email $firstEmail */
        $firstEmail = $this->emailRepository->find($firstSentEmailWithinCampaign->getChannelId());
        if (null === $firstEmail) {
            return null;
        }
        /** @var Stat $stat */
        $stat = $this->statRepository->findOneBy([
            'email' => $this->getEmailVariants($firstEmail),
            'lead' => $leadId,
            'isFailed' => false,
            'source' => 'campaign.event',
            'sourceId' => $this->getCampaignEvents($campaign),
        ], ['dateSent' => 'ASC']);

        return null !== $stat ? $stat->getEmail() : null;
    }

    private function getEmailVariants(Email $email): array
    {
        $ids = [$email->getId()];
        /** @var Email $variantChild */
        foreach ($email->getVariantChildren() as $variantChild) {
            $ids[] = $variantChild->getId();
        }
        return $ids;
    }

    private function getCampaignEvents(Campaign $campaign): array
    {
        $ids = [];
        /** @var Event $event */
        foreach ($campaign->getEvents() as $event) {
            $ids[] = $event->getId();
        }
        return $ids;
    }

    private function getLeadId(EmailSendEvent $event): ?int
    {
        $lead = $event->getLead();
        if ($lead instanceof Lead) {
            return $lead->getId();
        }
        return isset ($lead['id']) ? (int) $lead['id'] : null;
    }
}
