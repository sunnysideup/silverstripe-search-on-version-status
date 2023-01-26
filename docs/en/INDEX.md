You can use it like this:

```php
    private static $searchable_fields = [
        'ID' => [
            'field' => PublishedStatus::class,
            'filter' => IsPublishedFilter::class,
            'title' => 'Status',
        ],
    ];
```