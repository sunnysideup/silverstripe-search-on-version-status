<?php

namespace Sunnnysideup\SearchOnVersionStatus\Forms;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Versioned\Versioned;

class PublishedStateDropdown extends DropdownField
{
    public function getSource()
    {
        $array = parent::getSource();
        $array['none'] = '-- any --';
        $array[Versioned::DRAFT] = 'Unpublished';
        $array[Versioned::LIVE] = 'Published';
        $array['MODIFIED'] = 'Published with modifications';
        $array['PUBLISHED_CLEAN'] = 'Published without modifications';
        $array['DRAFT_ERROR'] = 'Published but draft is missing (error!)';

        return $array;
    }
}
