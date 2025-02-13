<?php
/*************************************************************************************/
/*      Copyright (c) Franck Allimant, CQFDev                                        */
/*      email : thelia@cqfdev.fr                                                     */
/*      web : http://www.cqfdev.fr                                                   */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE      */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

/**
 * Created by Franck Allimant, CQFDev <franck@cqfdev.fr>
 * Date: 21/11/2019 15:42
 */

namespace SimulatedPayment\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Model\OrderQuery;
use Thelia\Model\OrderStatusQuery;
use Thelia\Tools\URL;

class PaymentController extends BaseFrontController
{
    public function pay(int $orderId, EventDispatcherInterface $dispatcher): RedirectResponse
    {
        if (null !== $order = OrderQuery::create()->findPk($orderId)) {
            // On ne peut payer que des commande "non payées"
            if ($order->isNotPaid()) {
                $event = new OrderEvent($order);

                $event->setStatus(OrderStatusQuery::getPaidStatus()->getId());

                $dispatcher->dispatch($event, TheliaEvents::ORDER_UPDATE_STATUS);

                return $this->generateRedirect(URL::getInstance()->absoluteUrl("/order/placed/$orderId"));
            }
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl("/order/failed/$orderId/Order%20was%20not%20found%20or%20is%20already%20paid%20or%20canceled"));
    }

    public function cancel(int $orderId, EventDispatcherInterface $dispatcher): RedirectResponse
    {
        if (null !== $order = OrderQuery::create()->findPk($orderId)) {
            // On ne peut payer que des commande "non payées"
            if ($order->isNotPaid()) {
                $event = new OrderEvent($order);

                $event->setStatus(OrderStatusQuery::getCancelledStatus()->getId());

                $dispatcher->dispatch($event, TheliaEvents::ORDER_UPDATE_STATUS);
            }
        }

        return $this->generateRedirect(URL::getInstance()->absoluteUrl("/order/failed/$orderId/You%20cenceled%20the%20payment"));
    }
}
