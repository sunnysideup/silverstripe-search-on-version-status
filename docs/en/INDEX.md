You can use it like this:

```php
    private static $searchable_fields = [
        'ID' => [
            'field' => PublishedStateDropdown::class,
            'filter' => IsPublishedFilter::class,
            'title' => 'Status',
        ],
    ];
```
