<?php

namespace NWT\OpenSpout\Reader\ODS\Creator;

use NWT\OpenSpout\Reader\Common\Manager\RowManager;

/**
 * Factory to create managers.
 */
class ManagerFactory
{
    /**
     * @param InternalEntityFactory $entityFactory Factory to create entities
     *
     * @return RowManager
     */
    public function createRowManager($entityFactory)
    {
        return new RowManager($entityFactory);
    }
}
