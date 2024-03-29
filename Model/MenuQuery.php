<?php

namespace Menu\Model;

use Menu\Model\Base\MenuQuery as BaseMenuQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'menu' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class MenuQuery extends BaseMenuQuery
{
    public static function getMenuByIdentifier($identifier)
    {
        return self::create()->findOneByIdentifier($identifier);
    }
} // MenuQuery
