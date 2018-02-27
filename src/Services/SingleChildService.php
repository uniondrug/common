<?php
/**
 * 框架级通用服务
 * @author wsfuyibing <websearch@163.com>
 * @date 2017-11-16
 */
namespace Uniondrug\Common\Services;

use Uniondrug\Common\Interfaces\SingleChildInterface;

/**
 * 有上下级但无隶属关系的通用Service
 * @package Pails\Services
 */
abstract class SingleChildService extends SingleService implements SingleChildInterface
{
    /**
     * @inheritdoc
     */
    public function fetchChild($parentName, $parentValue)
    {
        return $this->fetchOneByColumn($parentName, $parentValue);
    }

    /**
     * @inheritdoc
     */
    public function fetchChildren($parentName, $parentValue, $columns = "*")
    {
        return $this->fetchAllByColumn($parentName, $parentValue, $columns);
    }

    /**
     * @inheritdoc
     */
    public function fetchTree($parentName, $parentValue, $primaryColumn = 'id', $columns = "*")
    {
        $tree = [];
        $children = $this->fetchChildren($parentName, $parentValue, $columns);
        foreach ($children as $child) {
            $temp = $child->toArray();
            $temp['children'] = $this->fetchTree($parentName, $temp[$primaryColumn], $primaryColumn, $columns);
            $tree[] = $temp;
        }
        unset($children);
        return $tree;
    }

    /**
     * @inheritdoc
     */
    public function hasChild($parentName, $parentValue)
    {
        $parameters = [];
        if (is_array($parentValue)) {
            $parameters['conditions'] = "{$parentName} IN ('".implode("', '", $parentValue)."')";
        } else {
            $parameters['conditions'] = "{$parentName} = :{$parentName}:";
            $parameters['bind'] = [$parentName => $parentValue];
        }
        return 0 < (int) $this->fetchCount($parameters);
    }
}