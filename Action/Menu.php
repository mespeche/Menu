<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Menu\Action;

use Menu\EventListeners\MenuDeleteEvent;
use Menu\EventListeners\MenuEvents;

use Menu\EventListeners\MenuToggleVisibilityEvent;
use Menu\EventListeners\MenuUpdateEvent;
use Menu\Model\MenuQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\UpdatePositionEvent;

/**
 *
 * Menu class where all actions are managed
 *
 * Class Menu
 * @package Menu\Action
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class Menu implements EventSubscriberInterface
{

    public function createMenu(MenuEvents $event)
    {                
        $menu = new \Menu\Model\Menu();

        $menu->setLocale($event->getLocale())
            ->setName($event->getName())
            ->setIdentifier($event->getIdentifier())
            ->setVisible($event->getVisible())
            ->create();

        $event->setMenu($menu);
    }

    /**
     * process update menu
     *
     * @param MenuUpdateEvent $event
     */
    public function updateMenu(MenuUpdateEvent $event)
    {
        if (null !== $menu = MenuQuery::create()->findPk($event->getMenuId())) {

            $menu
                ->setVisible($event->getVisible())
                ->setLocale($event->getLocale())
                ->setName($event->getName())
                ->setIdentifier($event->getIdentifier())
                ->save()
            ;

            $event->setMenu($menu);
        }
    }

    public function deleteMenu(MenuDeleteEvent $event)
    {
        if (null !== $menu = MenuQuery::create()->findPk($event->getMenuId())) {

            $menu->delete();

            $event->setMenu($menu);
        }
    }

    public function updateMenuPosition(UpdatePositionEvent $event)
    {
        if (null !== $menu = MenuQuery::create()->findPk($event->getObjectId())) {

            $menu->setDispatcher($event->getDispatcher());

            switch ($event->getMode()) {
                case UpdatePositionEvent::POSITION_ABSOLUTE:
                    $menu->changeAbsolutePosition($event->getPosition());
                    break;
                case UpdatePositionEvent::POSITION_DOWN:
                    $menu->movePositionDown();
                    break;
                case UpdatePositionEvent::POSITION_UP:
                    $menu->movePositionUp();
                    break;
            }
        }
    }

    public function toggleVisibilityMenu(MenuToggleVisibilityEvent $event)
    {
        $menu = $event->getMenu();

        $menu
            ->setVisible(!$menu->getVisible())
            ->save();

        $event->setMenu($menu);

    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvents::MENU_CREATE                => array('createMenu', 128),
            MenuEvents::MENU_UPDATE                => array('updateMenu', 128),
            MenuEvents::MENU_DELETE                => array('deleteMenu', 128),
            MenuEvents::MENU_UPDATE_POSITION       => array('updateMenuPosition', 128),
            MenuEvents::MENU_TOGGLE_VISIBILITY     => array('toggleVisibilityMenu', 128)
        );
    }
}
