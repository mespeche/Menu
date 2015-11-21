<?php

namespace Menu\Model;

use Menu\Model\Base\Menu as BaseMenu;
use Menu\Model\Map\MenuTableMap;
use Propel\Runtime\Propel;

class Menu extends BaseMenu
{
    use \Thelia\Model\Tools\ModelEventDispatcherTrait;
    use \Thelia\Model\Tools\PositionManagementTrait;

    /**
     * Create a new menu.
     *
     * Here pre and post insert event are fired
     *
     * @throws \Exception
     */
    public function create()
    {
        $con = Propel::getWriteConnection(MenuTableMap::DATABASE_NAME);

        $con->beginTransaction();

        try {
            $this->save($con);
            $this->setPosition($this->getNextPosition())->save($con);
            $con->commit();

        } catch (\Exception $ex) {

            $con->rollback();
            throw $ex;
        }

        return $this;
    }
}
