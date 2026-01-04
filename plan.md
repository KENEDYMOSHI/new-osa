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

Kwenye module ya my applications inayoonesha License Approval Journey kwenye sehemu ya control number , License,Date,License Fee:App. Fee: sasa kwenye lisence fee utaweka fee ya leseni itakayo patikana na kwenye table ya license_types na column ya fee hii uantokea kutokana na aina ya leseni ulio chagua na app fee utaweka fee ya application

Kwenye initial application approval kwenye mfumo wa WMA-MIS kwenye sehemu ya applicant details kwenye card ya kwenza yenye applicant name ,license name Citizenship Status ,Application Type, Control na status utafanya marekebisho kwenye sehemu ya control number ambako control number iwe inaonekana ya application fee na chini ongeza ionekane ya lisence fee na hii itatokea baada ya applicant kugenarete mwisho kabisa akishakua approved na watu wote

Kwenye App fee CN itatokea badae na sio kurudia ya application mbili kuna malipo mawili yanafanyika kuna application fee na lisence fee so kwenye aplication fee after all approval applicant atarequest kweny applicant portal control number ndio itakuja kutokea hapo

kwenye tabs ya attachments uploaded kila document inavyokua apploaded au reuploaded iwe ina onekana katika category yake means kama ni attachments ionekane kwenye required attachments na kama ni qualification ionekane kwenye qualificatios document fix that condition mfumo uwe una filter documents kutokana na category ya document

In the WMA-MIS system, the Attachments Uploaded section shall display all documents uploaded by the applicant during the application process. It is noted that an applicant may submit more than one license application, and while Personal Information, Company Information, and common Attachments Uploaded may be shared across applications, each license application shall be treated independently in terms of License Details and Approval Workflow. Each submitted application must therefore have its own license record and its own approval process, without affecting other license applications submitted by the same applicant.

For Required Attachments and Qualification Documents, the system shall dynamically determine and display the required documents based on the document category linked to the specific license being applied for. The system must retrieve and validate these document requirements by referencing the license_application_attachments table, ensuring that only documents relevant to the selected license category are shown, requested, and evaluated. This approach guarantees that applicants upload the correct documents per license type, while maintaining data integrity, proper document separation, and accurate approval decisions for each individual license application fix very carefully

Kwenye mfumo wa applicant module ya notification badilisha design weka design hii ionekane hivi kama ilivyo kwenye picha
Kwenye module ya License Setting kwenye WMA-MIS engeza tabs moja ya support/help ambayo itakua na field yakuingiza hizi input Wakala wa Vipimo (WMA)
Vipimo House, Chief Chemist Street
S.L.P. 2014, Dodoma – Tanzania

📞 Simu:
+255 (26) 22610700

📧 Barua Pepe (Maswali ya Jumla):
info@wma.go.tz

📧 Barua Pepe (Msaada wa Kiufundi):
ictsupport@wma.go.tz

🌐 Tovuti:
www.wma.go.tz.

tengeneza sehemu hii na table yake yakusave data hizi tengeneza kwenye osa_app usibadili usiharibu chochote fanya kwa umakini

Kwenye mfumo wa applicantusivuruge chochote kwenye module ya notification wakati wa Message Preview kwenye kila anavyo priview itokee chini kile kisehem taarifa message inayokua preview na applicant za support/help kitokee muundo wa anuani itavuta kwenye table ya osa_support_details

impliment kwenye applicant portal kwenye module ya support-help chukua kwenye database kwenye table ya osa_support_details nakuonyesha hapo

Kwenye mfumo wa wma-mis module ya lisence setting kwenye tab ya License Type Configuration engeza column ya SELECTED INSTRUMENTS na criterial na sehemu ya kuadd engeza hizi option mbili

Kwenye select instrumrnt zinakua in list way hata kwenye sehemu yake yakuziweka

kwenye criteria ni seheme yakuSelect a minimum number measuring instruments ambazo zitakua kwenye selected instruments design zinaweza kua mfano kuna minimam na maximum design

sentensi itakayo kua ina ji generati kutokana na selection ni Select a minimum of two measuring instruments kwa istrument zenye instrumen number kamili au Select a minimum of two and a maximum of three instruments yenye nstrument zenye minimam na maximam implement

design License Fee na action icon zikae vizuri punguza font kidogo kwenye License Fee zikae sawa kwemye line moja

kwenye applicant portal kwenye module ya initial application kwenye card ya License Type Selection
Select the license class types you would like to apply for. design kila card iwe ina
 uwezo wakuonesha SELECTED INSTRUMENTS na kue na check box ambayo itafuata condtion zilizoko kwenye criteria weka design ambayo haito aribu iliko na kila kitu kifanye kazi kama kilivyo kuwepo ✅ Implimenteddes

impliment kwenye WMA-MIS module ya initial application approval kwenye tabs ya lisence sehemu ya License Class ioneshe na zile selected instrument applicant alizo select wakati wa application submittion usivuruge chochote 

Mfumo kwa sasa unashindwa kufanya approval pale ambapo applicant mmoja ana applications zaidi ya moja (leseni zaidi ya moja). Ingawa applicant anatumia Personal Information, Company Information na Attachments zilezile, kila application inapaswa kuwa na license yake na approval workflow yake binafsi. Tatizo linatokea pale ambapo application moja inapofanikiwa ku-approve, application nyingine za applicant yuleyule zinashindwa ku-approve na mfumo unarudisha error ya “Failed to approve application”. Hii inaonyesha kuwa approval logic ya mfumo imejengwa kwa kuangalia applicant-level badala ya application-level, hivyo mfumo unachukulia kuwa applicant tayari amesha-approve-iwa na kuzuia approvals za applications nyingine. Ili kurekebisha hili, approval logic inapaswa kubadilishwa ili itegemee Application ID na License ID pekee, na si Applicant ID, kuhakikisha kuwa kila application inapitia mchakato wake wa approval kwa kujitegemea bila kuathiri applications nyingine za applicant huyo.