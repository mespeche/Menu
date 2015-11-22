<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia                                                                       */
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
/*      along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace Menu\Controller\Admin;

use Menu\EventListeners\MenuDeleteEvent;
use Menu\EventListeners\MenuEvents;
use Menu\EventListeners\MenuToggleVisibilityEvent;
use Menu\EventListeners\MenuUpdateEvent;
use Menu\Form\MenuCreationForm;
use Menu\Form\MenuModificationForm;
use Menu\Model\MenuQuery;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Controller\Admin\unknown;
use Thelia\Core\Event\UpdatePositionEvent;

/**
 * Class MenuController
 * @package Menu\Controller\Admin
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class MenuController extends AbstractCrudController
{

    public function __construct()
    {
        parent::__construct(
            'menu',
            'manual',
            'menu_order',

            'admin.menu',

            MenuEvents::MENU_CREATE,
            MenuEvents::MENU_UPDATE,
            MenuEvents::MENU_DELETE,
            MenuEvents::MENU_TOGGLE_VISIBILITY,
            MenuEvents::MENU_UPDATE_POSITION
        );
    }

    public function viewAction()
    {
        if (null !== $this->getExistingObject()) {
            $menu = $this->getExistingObject();

            return $this->render('menu-view', array('menu_id' => $menu->getId()));
        }
    }

    /**
     * Return the creation form for this object
     */
    protected function getCreationForm()
    {
        return new MenuCreationForm($this->getRequest());
    }

    /**
     * Return the update form for this object
     */
    protected function getUpdateForm()
    {
        return new MenuModificationForm($this->getRequest());
    }

    /**
     * @param $positionChangeMode
     * @param $positionValue
     * @return UpdatePositionEvent|void
     */
    protected function createUpdatePositionEvent($positionChangeMode, $positionValue)
    {
        return new UpdatePositionEvent(
            $this->getRequest()->get('menu_id', null),
            $positionChangeMode,
            $positionValue
        );
    }

    /**
     * @return MenuToggleVisibilityEvent|void
     */
    protected function createToggleVisibilityEvent()
    {
        return new MenuToggleVisibilityEvent($this->getExistingObject());
    }

    /**
     * Hydrate the update form for this object, before passing it to the update template
     *
     * @param  unknown $object
     * @return \Menu\Form\MenuModificationForm
     */
    protected function hydrateObjectForm($object)
    {
        // Prepare the data that will hydrate the form
        $data = array(
            'id'           => $object->getId(),
            'locale'       => $object->getLocale(),
            'title'        => $object->getTitle(),
            'identifier'         => $object->getIdentifier(),            
            'visible'      => $object->getVisible()
        );

        // Setup the object form
        return new MenuModificationForm($this->getRequest(), "form", $data);
    }

    /**
     * Creates the creation event with the provided form data
     *
     * @param unknown $formData
     */
    protected function getCreationEvent($formData)
    {
        $menuCreateEvent = new MenuEvents(
            $formData['title'],
            $formData['identifier'],
            $formData['visible'],
            $formData['locale']
        );

        return $menuCreateEvent;

    }

    /**
     * Creates the update event with the provided form data
     *
     * @param unknown $formData
     */
    protected function getUpdateEvent($formData)
    {
        $menuUpdateEvent = new MenuUpdateEvent($formData['id']);

        $menuUpdateEvent
            ->setLocale($formData['locale'])
            ->setTitle($formData['title'])
            ->setCode($formData['identifier'])
            ->setVisible($formData['visible']);

        return $menuUpdateEvent;
    }

    /**
     * Creates the delete event with the provided form data
     */
    protected function getDeleteEvent()
    {
        return new MenuDeleteEvent($this->getRequest()->get('menu_id'), 0);
    }

    /**
     * Return true if the event contains the object, e.g. the action has updated the object in the event.
     *
     * @param  \Menu\EventListeners\MenuEvents $event
     * @return bool
     */
    protected function eventContainsObject($event)
    {        
        return $event->hasMenu();
    }

    /**
     * Get the created object from an event.
     *
     * @param unknown $event
     */
    protected function getObjectFromEvent($event)
    {
        // TODO: Implement getObjectFromEvent() method.
    }

    /**
     * Load an existing object from the database
     */
    protected function getExistingObject()
    {        
        $menu = MenuQuery::create()
            ->findOneById($this->getRequest()->get('menu_id', 0));

        if (null !== $menu) {
            $menu->setLocale($this->getCurrentEditionLocale());
        }

        return $menu;

    }

    /**
     * Returns the object label form the object event (name, title, etc.)
     *
     * @param unknown $object
     */
    protected function getObjectLabel($object)
    {
        // TODO: Implement getObjectLabel() method.
    }

    /**
     * Returns the object ID from the object
     *
     * @param unknown $object
     */
    protected function getObjectId($object)
    {
        // TODO: Implement getObjectId() method.
    }

    /**
     * Render the main list template
     *
     * @param unknown $currentMenu , if any, null otherwise.
     */
    protected function renderListTemplate($currentMenu)
    {
        return $this->render('module-configure',
            array(
                'module_code' => 'Menu',
                'code' => 'menu',
                'menu_order' => $currentMenu
            ));
    }

    protected function getEditionArguments()
    {
        return array(
            'menu_id' => $this->getRequest()->get('menu_id', 0)
        );
    }

    /**
     * Render the edition template
     */
    protected function renderEditionTemplate()
    {
        return $this->render('menu-edit', $this->getEditionArguments());
    }

    /**
     * Redirect to the edition template
     */
    protected function redirectToEditionTemplate()
    {
        $args = $this->getEditionArguments();

        return $this->generateRedirect('/admin/module/Menu/update?menu_id='.$args['menu_id']);
    }

    /**
     * Redirect to the list template
     */
    protected function redirectToListTemplate()
    {
        return $this->generateRedirect('/admin/module/Menu');
    }

    protected function performAdditionalUpdateAction($updateEvent)
    {
        if ($this->getRequest()->get('save_mode') != 'stay') {
            return $this->redirectToListTemplate();
        }
    }

}
