<?php
/*************************************************************************************/
/*      Module OrderNotification pour Thelia                                         */
/*                                                                                   */
/*      Copyright (©) Informatique Prog                                              */
/*      email : contact@informatiqueprog.net                                         */
/*                                                                                   */
/*                                                         test utf-8 ä,ü,ö,ç,é,â,µ  */
/*************************************************************************************/

namespace OrderNotification;

use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Module\BaseModule;

class OrderNotification extends BaseModule
{

    /**
     * This method is called before the module activation, and may prevent it by returning false.
     *
     * @param ConnectionInterface $con
     *
     * @return bool true to continue module activation, false to prevent it.
     */
    public function preActivation(ConnectionInterface $con = null)
    {
        /* THELIA VERSION */
        $thelia_major_version = ConfigQuery::read('thelia_major_version');

        $thelia_minus_version = ConfigQuery::read('thelia_minus_version');

        /* Check THELIA VERSION */
        if ($thelia_major_version == 2 && $thelia_minus_version < 1) {
            return true;
        }

        return false;
    }
}
