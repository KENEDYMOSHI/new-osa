## getting a user group

- manager
```php
auth()->user()->inGroup('manager');
```
- surveillance
```php
auth()->user()->inGroup('surveillance');
```
- dts
```php
auth()->user()->inGroup('dts');
```
- ceo
```php
auth()->user()->inGroup('ceo');
```

## getting user collection center/region code
```php
auth()->user()->collection_center;
```