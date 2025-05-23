<?php



namespace humhub\interfaces;

/**
 * Description of Application
 *
 * @since 1.15
 */
interface ApplicationInterface
{
    /**
     * @event ActionEvent an event raised on init of application.
     */
    public const EVENT_ON_INIT = 'onInit';

    public function getMailer(): MailerInterface;
}
