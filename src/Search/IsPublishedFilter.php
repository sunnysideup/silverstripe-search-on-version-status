<?php

namespace Sunnnysideup\SearchOnVersionStatus\Search;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DataQuery;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Filters\SearchFilter;
use SilverStripe\Versioned\Versioned;

class IsPublishedFilter extends SearchFilter
{
    protected function applyOne(DataQuery $query)
    {
        $sql = '';
        $array = [0 => 0];
        $schema = DataObject::getSchema();
        $className = $query->dataClass();
        $baseTable = $schema->baseDataTable($className);        
        switch ($this->getValue()) {
            
            case 'MODIFIED':
                $className = $query->dataClass();
                $objects = $className::get();
                foreach ($objects as $obj) {
                    if ($obj->isModifiedOnDraft() && $obj->isPublished()) {
                        $array[$obj->ID] = $obj->ID;
                    }
                }

                break;
            
            case Versioned::LIVE:
                $sql = 'SELECT "ID" FROM "'.$baseTable.'_Live"';

                break;
            
            case Versioned::DRAFT:
                $sql = '
                    SELECT "'.$baseTable.'"."ID"
                    FROM "'.$baseTable.'"
                    LEFT JOIN "'.$baseTable.'_Live" ON "'.$baseTable.'_Live"."ID" = "'.$baseTable.'"."ID"
                    WHERE "'.$baseTable.'_Live"."ID" IS NULL';

                break;
            
            case 'DRAFT_ERROR':
                $sql = '
                    SELECT "'.$baseTable.'_Live"."ID"
                    FROM "'.$baseTable.'_Live"
                    LEFT JOIN '.$baseTable.' ON '.$baseTable.'_Live.ID = '.$baseTable.'.ID
                    WHERE '.$baseTable.'.ID IS NULL';

                break;

            case 'PUBLISHED_CLEAN':
                $objects = $className::get();
                foreach ($objects as $obj) {
                    if (!$obj->isModifiedOnDraft() && $obj->isPublished()) {
                        $array[$obj->ID] = $obj->ID;
                    }
                }                
                break;
            default:
                return $query;
        }

        if ($sql) {
            $rows = DB::query($sql);
            foreach ($rows as $row) {
                $array[$row['ID']] = $row['ID'];
            }
        }

        return $query->where("\"{$baseTable}\".\"ID\" IN (" . implode(',', $array) . ')');
    }

    protected function excludeOne(DataQuery $query)
    {
        $this->model = $query->applyRelation($this->relation);
        $predicate = sprintf('NOT MATCH (%s) AGAINST (?)', $this->getDbName());

        return $query->where([$predicate => $this->getValue()]);
    }
}
