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

namespace Menu\Form;

use Menu\Model\MenuQuery;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\ExecutionContextInterface;
use Thelia\Form\StandardDescriptionFieldsTrait;

/**
 * Class MenuModificationForm
 * @package Thelia\Form
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class MenuModificationForm extends MenuCreationForm
{    
    use StandardDescriptionFieldsTrait;
    
    protected function buildForm()
    {
        parent::buildForm();

        $this->formBuilder
            ->add("id", "hidden", array("constraints" => array(new GreaterThan(array('value' => 0)))))
        ;

        // Add standard description fields, excluding title and locale, which a re defined in parent class        
        $this->addStandardDescFields(array('title', 'locale'));
    }

    public function verifyExisting($value, ExecutionContextInterface $context)
    {
        $menuId = $this->getRequest()->get('menu_id');
        $menuUpdated = MenuQuery::create()->findPk($menuId);

        // If the sent identifier isn't identical to the menu identifier being updated
        if ($menuUpdated->getIdentifier() !== $value) {

            // Check if menu with this identifier exist
            parent::verifyExisting($value, $context);
        }
    }

    public function getName()
    {
        return "admin_menu_modification";
    }
}
