<?php
/*************************************************************************************/
/*      Module OrderNotification pour Thelia                                         */
/*                                                                                   */
/*      Copyright (©) Informatique Prog                                              */
/*      email : contact@informatiqueprog.net                                         */
/*                                                                                   */
/*                                                         test utf-8 ä,ü,ö,ç,é,â,µ  */
/*************************************************************************************/

namespace OrderNotification\EventListeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\ParserInterface;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MessageQuery;

class OnEventListener extends BaseAction implements EventSubscriberInterface
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    /**
     * @var MailerFactory
     */
    protected $mailer;

    /**
    * @param ParserInterface $parser
    */
    public function __construct(ParserInterface $parser, MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @param \Thelia\Core\Event\Order\OrderEvent $event
     */
    public function sendOrderConfirmationEmail(OrderEvent $event)
    {
        $store_email = ConfigQuery::read('store_email');

        $store_name = ConfigQuery::read('store_name');

        if ($store_email) {

            $Message = MessageQuery::create()->filterByName('order_confirmation')->findOne();

            if (false === $Message) {
                throw new \Exception("Failed to load message 'order_confirmation'.");
            }

            $Order = $event->getOrder();

            $this->parser->assign('order_id', $Order->getId());

            $this->parser->assign('order_ref', $Order->getRef());

            $Message->setLocale($Order->getLang()->getLocale());

            $Swift_Message = \Swift_Message::newInstance()
                ->addTo($store_email, $store_name)
                ->addFrom($store_email, $store_name);

            $Message->buildMessage($this->parser, $Swift_Message);

            $this->mailer->send($Swift_Message);

            Tlog::getInstance()->debug("Confirmation email sent to store_email:" . $store_email);
        }

    }

    /**
    * Returns an array of event names this subscriber wants to listen to.
    *
    * The array keys are event names and the value can be:
    *
    * * The method name to call (priority defaults to 0)
    * * An array composed of the method name to call and the priority
    * * An array of arrays composed of the method names to call and respective
    * priorities, or 0 if unset
    *
    * For instance:
    *
    * * array('eventName' => 'methodName')
    * * array('eventName' => array('methodName', $priority))
    * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
    *
    * @return array The event names to listen to
    *
    * @api
    */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::ORDER_BEFORE_PAYMENT  => ['sendOrderConfirmationEmail', 129]
        ];
    }
}
