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

/**
 * Class MenuUpdateEvent
 * @package Menu\EventListeners
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class MenuUpdateEvent extends MenuEvents
{
    protected $menu_id;

    public function __construct($menu_id)
    {
        $this->menu_id = $menu_id;
    }    

    /**
     * @param mixed $menu_id
     *
     * @return $this
     */
    public function setMenuId($menu_id)
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMenuId()
    {
        return $this->menu_id;
    }

}
