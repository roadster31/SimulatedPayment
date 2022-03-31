<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace SimulatedPayment\EventListeners;

use SimulatedPayment\SimulatedPayment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;

/**
 * Class SendConfirmationEmail
 *
 * @package SimulatedPayment\EventListeners
 * @author  Franck Allimant <franck@cqfdev.fr>
 */
class SendConfirmationEmail implements EventSubscriberInterface
{
    /** @var MailerFactory */
    protected $mailer;

    public function __construct(MailerFactory $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param OrderEvent $event
     *
     * @throws \Exception if the message cannot be loaded.
     */
    public function checkSendEmail(OrderEvent $event)
    {
        // We send the order confirmation email only if the order is paid
        $order = $event->getOrder();

        if (! $order->isPaid() && $order->getPaymentModuleId() == SimulatedPayment::getModuleId()) {
            $event->stopPropagation();
        }
    }

    /**
     * Checks if order payment module is paypal and if order new status is paid, send an email to the customer.
     *
     * @param OrderEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function updateStatus(OrderEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $order = $event->getOrder();

        if ($order->isPaid() && $order->getPaymentModuleId() == SimulatedPayment::getModuleId()) {
            $dispatcher->dispatch(TheliaEvents::ORDER_SEND_CONFIRMATION_EMAIL, clone $event);
            $dispatcher->dispatch(TheliaEvents::ORDER_SEND_NOTIFICATION_EMAIL, clone $event);

            Tlog::getInstance()->debug("Confirmation email sent to customer " . $order->getCustomer()->getEmail());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS           => ['updateStatus'   , 128] ,
            TheliaEvents::ORDER_SEND_CONFIRMATION_EMAIL => ['checkSendEmail' , 130] ,
            TheliaEvents::ORDER_SEND_NOTIFICATION_EMAIL => ['checkSendEmail' , 130]
        );
    }
}
