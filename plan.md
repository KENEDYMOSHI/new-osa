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

Kwenye ku subumitt application kuna aina mbili za applicant citizen and non citizen kwenye mudule ya initial application citizen analipa 50000 na non citizen 200000 utaangalia kwenye table ya application_type_fees utaangalia na hapa ni application usichanganye na lisence fee kinachokua kina tolewa bill ni application fee impliment 

Kwenye module ya my applications inayoonesha License Approval Journey kwenye sehemu ya control number , License,Date,License Fee:App. Fee: sasa kwenye lisence fee utaweka fee ya leseni itakayo patikana  na kwenye table ya license_types na column ya fee hii uantokea kutokana na aina ya leseni ulio chagua na  app fee utaweka fee ya application  
 