<?php
declare(strict_types=1);

namespace Zeta\ShopByBrand\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['is_active'])) {
                    $item['is_active'] = $item['is_active'] ? __('Enabled') : __('Disabled');
                }
            }
        }

        return $dataSource;
    }
}