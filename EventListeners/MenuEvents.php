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

namespace Menu\EventListeners;

use Menu\Model\Menu;
use Thelia\Core\Event\ActionEvent;

/**
 *
 * This class contains all Menu events identifiers used by Menu Core
 *
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */

class MenuEvents extends ActionEvent
{

    const MENU_CREATE            = 'menu.action.create';
    const MENU_UPDATE            = 'menu.action.update';
    const MENU_DELETE            = 'menu.action.delete';
    const MENU_TOGGLE_VISIBILITY = 'menu.action.toggleVisibility';
    const MENU_UPDATE_POSITION   = 'menu.action.updatePosition';

    protected $locale;
    protected $title;
    protected $identifier;
    protected $visible;
    protected $menu;

    public function __construct($title, $identifier, $visible, $locale)
    {        
        $this->title = $title;
        $this->identifier = $identifier;
        $this->visible = $visible;
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param mixed $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {        
        return $this->title;
    }

    /**
     * @param mixed $identifier
     *
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $visible
     *
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    
    /**
     * @param  \Menu\Model\Menu $menu
     * @return $this
     */
    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return \Menu\Model\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * check if menu exists
     *
     * @return bool
     */
    public function hasMenu()
    {        
        return null !== $this->menu;
    }
}
