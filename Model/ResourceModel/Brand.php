<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Brand extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('zeta_brand', 'id');
    }

    public function isUrlKeyExists($urlKey, $excludeId = null)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'id')
            ->where('url_key = ?', $urlKey);

        if ($excludeId) {
            $select->where('id != ?', $excludeId);
        }

        return (bool)$connection->fetchOne($select);
    }

    public function generateUrlKey($name, $excludeId = null)
    {
        $urlKey = $this->formatUrlKey($name);
        $origUrlKey = $urlKey;
        $i = 1;

        while ($this->isUrlKeyExists($urlKey, $excludeId)) {
            $urlKey = $origUrlKey . '-' . $i;
            $i++;
        }

        return $urlKey;
    }

    protected function formatUrlKey($str)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', strtolower($str));
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }
}