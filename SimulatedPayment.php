<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace SimulatedPayment;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Model\Order;
use Thelia\Module\BaseModule;
use Thelia\Module\AbstractPaymentModule;
use Thelia\Tools\URL;

class SimulatedPayment extends AbstractPaymentModule
{
    public function isValidPayment()
    {
        return true;
    }

    public function pay(Order $order)
    {
        return new RedirectResponse(URL::getInstance()->absoluteUrl("simulatedpayment", [ 'order_id' => $order->getId() ]));
    }

    public function manageStockOnCreation()
    {
        return false;
    }
}
