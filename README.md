# Mautic Cold Email Outreach Plugin

## Send follow-ups in the same email thread for any A/B test variation

This plugin turns your regular Mautic instance into a direct outreach machine.

Say you have an email offer with several A/B test variations.
Each variation has an individual subject, so you can see which one converts better.
All is well unless you add a follow-up sequence to this campaign.
The problem is that the subject of A/B tests and follow-up emails is different, so follow-ups are sent as **separate** messages, but you want them to stay in the **same** thread.

That's where the Mautic Cold Email Outreach Plugin comes in!
- Any follow-ups to an A/B test will appear in the same thread belonging to the initial email
- The subject line will start with "Re:". For example: "Re: Hello!"
- It will not change the subject if the initial email was not found. 
- This applies at the campaign level. It will look for the first sent email in the campaign the follow-up belongs to.
- It also supports tokens in subject, e.g:  "Hi {contactfield=firstname|there}"


## Installation

### Console

1. `composer require karser/mautic-cold-email-outreach-bundle`
2. `php bin/console mautic:plugins:reload`

### Manual

1. Download last version https://github.com/karser/MauticColdEmailOutreachBundle
2. Unzip files to plugins/MauticColdEmailOutreachBundle
3. Clear cache (var/cache/prod/)
4. Go to /s/plugins/reload

## Usage

1. Go to Mautic > Settings > Plugins
2. You should see the new `Cold Email Outreach Bundle`
3. Enable it
