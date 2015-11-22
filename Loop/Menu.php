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

namespace Menu\Loop;

use Menu\Model\MenuQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;

use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Argument\Argument;

use Thelia\Type\TypeCollection;
use Thelia\Type;
use Thelia\Type\BooleanOrBothType;

/**
 *
 * Menu loop
 *
 *
 * Class Menu
 * @package Menu\Loop
 * @author Michaël Espeche <michael.espeche@gmail.com>
 */
class Menu extends BaseI18nLoop implements PropelSearchLoopInterface
{
    protected $timestampable = true;

    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createBooleanOrBothTypeArgument('visible', 1),
            new Argument(
                'identifier',
                new TypeCollection(
                    new Type\AlphaNumStringListType()
                )
            ),
            new Argument(
                'order',
                new TypeCollection(
                    new Type\EnumListType(array('alpha', 'alpha-reverse', 'manual', 'manual_reverse', 'random', 'given_id'))
                ),
                'alpha'
            )
        );
    }

    public function buildModelCriteria()
    {

        $search = MenuQuery::create();
        
        if ($this->getIdentifier()) {
            $search->filterByIdentifier($this->getIdentifier());
        }

        /* manage translations */
        $this->configureI18nProcessing($search, array('TITLE'));

        $id = $this->getId();

        if (!is_null($id)) {
            $search->filterById($id, Criteria::IN);
        }

        $visible = $this->getVisible();

        if ($visible !== BooleanOrBothType::ANY) $search->filterByVisible($visible ? 1 : 0);

        $orders  = $this->getOrder();

        foreach ($orders as $order) {
            switch ($order) {
                case "alpha":
                    $search->addAscendingOrderByColumn('i18n_TITLE');
                    break;
                case "alpha-reverse":
                    $search->addDescendingOrderByColumn('i18n_TITLE');
                    break;
                case "manual":
                    $search->orderByPosition(Criteria::ASC);
                    break;
                case "manual_reverse":
                    $search->orderByPosition(Criteria::DESC);
                    break;
                case "given_id":
                    if(null === $id)
                        throw new \InvalidArgumentException('Given_id order cannot be set without `id` argument');
                    foreach ($id as $singleId) {
                        $givenIdMatched = 'given_id_matched_' . $singleId;
                        $search->withColumn(ContentTableMap::ID . "='$singleId'", $givenIdMatched);
                        $search->orderBy($givenIdMatched, Criteria::DESC);
                    }
                    break;
                case "random":
                    $search->clearOrderByColumns();
                    $search->addAscendingOrderByColumn('RAND()');
                    break(2);
            }
        }

        return $search;

    }

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $menu) {

            // Find previous and next category
            $previous = MenuQuery::create()
                ->filterByPosition($menu->getPosition(), Criteria::LESS_THAN)
                ->orderByPosition(Criteria::DESC)
                ->findOne()
            ;

            $next = MenuQuery::create()
                ->filterByPosition($menu->getPosition(), Criteria::GREATER_THAN)
                ->orderByPosition(Criteria::ASC)
                ->findOne()
            ;

            $loopResultRow = new LoopResultRow($menu);                       

            $loopResultRow->set("ID", $menu->getId())
                ->set("IS_TRANSLATED",$menu->getVirtualColumn('IS_TRANSLATED'))
                ->set("LOCALE",$this->locale)
                ->set("TITLE",$menu->getVirtualColumn('i18n_TITLE'))
                ->set("IDENTIFIER",$menu->getIdentifier())                
                ->set("POSITION", $menu->getPosition())
                ->set("VISIBLE", $menu->getVisible())

                ->set("HAS_PREVIOUS", $previous != null ? 1 : 0)
                ->set("HAS_NEXT"    , $next != null ? 1 : 0)

                ->set("PREVIOUS", $previous != null ? $previous->getId() : -1)
                ->set("NEXT"    , $next != null ? $next->getId() : -1)
            ;

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;

    }

}
