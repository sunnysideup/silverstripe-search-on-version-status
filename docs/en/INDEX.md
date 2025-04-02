You can use it like this:

```php


use Sunnnysideup\SearchOnVersionStatus\Forms\PublishedStateDropdown;
use Sunnnysideup\SearchOnVersionStatus\Search\IsPublishedFilter;

class MyDataObject extends DataObject 
{
    private static $searchable_fields = [
        'ID' => [
            'field' => PublishedStateDropdown::class,
            'filter' => IsPublishedFilter::class,
            'title' => 'Status',
        ],
    ];

}
```
