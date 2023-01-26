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
                $sql = 'SELECT "ID" FROM "SiteTree_Live"';

                break;
            case Versioned::DRAFT:
                $sql = '
                    SELECT "SiteTree"."ID"
                    FROM "SiteTree"
                    LEFT JOIN SiteTree_Live ON SiteTree_Live.ID = SiteTree.ID
                    WHERE SiteTree_Live.ID IS NULL';

                break;
            case 'DRAFT_ERROR':
                $sql = '
                    SELECT "SiteTree_Live"."ID"
                    FROM "SiteTree_Live"
                    LEFT JOIN SiteTree ON SiteTree_Live.ID = SiteTree.ID
                    WHERE SiteTree.ID IS NULL';

                break;
            case 'PUBLISHED_CLEAN':
                $className = $query->dataClass();
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

        $schema = DataObject::getSchema();
        $baseTable = $schema->baseDataTable($query->dataClass());

        return $query->where("\"{$baseTable}\".\"ID\" IN (" . implode(',', $array) . ')');
    }

    protected function excludeOne(DataQuery $query)
    {
        $this->model = $query->applyRelation($this->relation);
        $predicate = sprintf('NOT MATCH (%s) AGAINST (?)', $this->getDbName());

        return $query->where([$predicate => $this->getValue()]);
    }
}
