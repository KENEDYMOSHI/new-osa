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

The WMA-MIS system should be updated in the Initial Application Approval module, specifically under the Attachments Uploaded tab, to ensure that all attachments uploaded by an applicant are treated as shared attachments across all of the applicant’s applications. These attachments should be reused for all related applications without duplication. If an attachment is marked as returned in any one application, the returned status should automatically apply to all applications that reference the same attachment. Additionally, if the applicant re-uploads a returned attachment, the updated version and status should be reflected consistently across all associated applications in real time, eliminating the need for multiple uploads and ensuring data consistency na angalia mpka kwenye database kuweka sawa na ikubali ku approval applicant mwenye leseni zaid ya moja please usivuruge mpangilio wowote 

The applicant portal system should be enhanced to restrict applicants from re-selecting a license card or re-submitting a license for which approval has already been granted. Once a license application has received CEO approval, the applicant shall be prevented from selecting or submitting the same license again until one (1) full year has elapsed. This restriction period must be calculated automatically based on the CEO approval date. The system should only allow the applicant to select and submit the same license again after the one-year period has fully expired. so show all application lisence that have alredy applay  

Kwenye hidden module inayoitwa license application kwenye side menu ilioko kwenye applicant portal rename na itwe complite license application usibadili kwingine zaid ya hapo 

In the Complete License Application module, under the License Details tab, the Select License Types (Type Of License Being Applied For) dropdown should display and allow selection of only approved licenses. For a New License Application, a license should appear in the dropdown only if it has been approved by the Manager, approved by Surveillance, and the applicant has passed the interview/assessment. For a Renew License Application, a license should appear in the dropdown only if it has been approved by the Manager and approved by Surveillance, without requiring a PASS condition. The system must filter the license list based on the application type (New or Renew) to ensure that applicants can select only valid and fully approved licenses.

Kwenye kucomplite lisence application module kwenye tabs  Preview & Confirm stage wakati applicant ana submitt hii isiwe kama application mpya ni application ya application ile ile iangalie id ya application na isiwe dublicate application intakiwa iwe kwenye application moja na isitengeneze dublicate inamaliziwa so isitengeneze application mpya ila inatakiwa wakati ina submitt itokee control number ya license fee itakayo chukuliwa kwenye table license_types kwenye colomn ya fee kutokana na application iliokua submitted na isitengeneze 

In the Complete License Application module, under the Preview & Confirm tab, when the applicant submits the application, the system must not create a new or duplicate application. Instead, the submission should finalize the existing application by referencing and using the same application ID that is being completed. The application should continue to appear as a single record in My Applications and in WMA-MIS, without generating a new application entry or showing a new application status. Upon submission, the system should generate a Control Number for the license fee, where the fee amount is retrieved from the license_types table based on the selected license and its corresponding fee column. The process must ensure that the application remains a single, continuous workflow and does not result in duplicate applications.

Kwenye kucomplite lisence application module kwenye tabs  Preview & Confirm stage wakati applicant ana submitt hii isiwe kama application mpya ni application ya application ile ile iangalie id ya application na isiwe dublicate application intakiwa iwe kwenye application moja na isitengeneze dublicate inamaliziwa so isitengeneze application mpya ila inatakiwa wakati ina submitt itokee control number ya license fee itakayo chukuliwa kwenye table license_types kwenye colomn ya fee kutokana na application iliokua submitted na isitengeneze 


Boresha business flow of complite license application module kwenye applicant portal after applicant approved by manager and survillence kutokatana na condition hii module itaonekana na kwasasa ndivyo inavyo fanya kazi pili baada yakuonekana ukiingia kuna tabs tofauti kinachotakiwa tabs ya kwanza kuonesha zile leseni alizo kua approved kisha ataselect leseni na kujaza taarifa kwenye tabs zote mpaka ya mwisho Preview & Confirm then ata submit maombi remember hili ni ombi moja toka limeanza after that kama ni leseni moja complite license application module ita potea lakini kama kuna leseni zaid ya moja itabaki mpaka leseni zote ziwe ziwe complite submitted Noted wakati ana submitt hakuna kutengeneza malipo yoyote ya control number kwenye stage hii bali ni submittion to yakumalizia kwenye usajili wa leseni .

Improve the business flow of the Complete License Application Module on the Applicant Portal by ensuring that this module is displayed only after the applicant has been approved by both the Manager and Surveillance, with this visibility condition remaining unchanged as per the current system behavior. Once the module becomes visible, it shall contain four existing functional tabs, where the first tab displays a drop-down list of all License Types that the applicant applied for and have been approved, based on the already established conditions. The applicant shall select one license at a time from the list. After selecting a license, the applicant will complete all required information across the subsequent tabs until reaching the final Preview & Confirm tab, which contains a Submit button at the bottom of the application. Upon submission, the system must recognize that this submission does not create a new application, but rather completes the same original application initiated at the beginning, maintaining a single application flow from start to finish without generating any new or duplicate applications in either the Applicant Portal or the internal system. After a successful submission, if the applicant had only one approved license, the Complete License Application Module shall be hidden or removed; however, if the applicant has multiple approved licenses, the module shall remain visible until all approved licenses are fully completed and submitted. Furthermore, it must be clearly enforced that no control number is generated and no payment is initiated at this submission stage, as this step strictly represents the final completion of license registration details and not a payment process.

Then after applicant kusubmitt itakua approved na DTS na CEO baada ya kua approve kwenye module ya my application kwenye applicant portal hidden button ilio andikwa view license kwa itakua kwanza imekua active itakua imeandikwa request control number aki click hapo ndipo control number ya leseni itakapo toka kisha baad ykutoke button itabadilika na kua view license ila itakua hifai kuview mpka control number ya malipo itakapo kua paid ndio view license iwe active kuview leseni 


make improvement kwenye module ya Complete License Application Module  isiwe inachanganya na module ya initial application kwenye applicant portal kwanza hili ukumbuke 

Kwemye Preview & Confirm Application tabs ya mwisho kwenye module ya complete license application hapa utaonesha taarifa zote za mteja Applicant Particulars,Company Information,License Information,Attachments,Qualifications & Experience,Tools & Equipment,Declaration na hivi vyote vinaonekana vizuri isipokua sehemu ya Attachments haioneshe document zile applicant alizo appload boresha sehemu hiiyo vizuri na mfumo uoeze onesha document za Qualification Documents na Required Attachments zilizokua apploaded na wakati ana sumbitt mfumo usitengeneze control number na malipo yoyte ioneshe tu application complte submitted na leseni ilioko submitted itoke kweny hiyo module 
 
 In the Preview & Confirm Application, which is the final tab in the Complete License Application module, the system is required to display a complete summary of all applicant information, including Applicant Particulars, Company Information, License Information, Qualifications & Experience, Tools & Equipment, Declaration, and Attachments. However, the Attachments card currently does not display any uploaded information, therefore the system should be enhanced to properly retrieve and display all documents uploaded by the applicant in earlier stages of the application, based on the corresponding application_id. The documents should be presented in a clear and structured manner, grouped into Qualification Documents and Required Attachments, with each document showing the file name, document type, upload date, and providing View and Download actions to support effective review before submission.

When the applicant clicks Submit Application on this tab, the system must not generate a control number or initiate any payment process. Instead, the system should simply save the application by updating its status to “Submitted”, record the submission date and time, and display a success message confirming that the application has been submitted successfully. After submission, the license application should appear in the Submitted Applications module with a status of Submitted or Pending Review, ready for the next review and decision-making stages according to the system workflow, while the payment process should only be triggered at a later stage when applicable.

At the Complete License Application stage, specifically on the final submission tab, when the applicant submits the application, the system should not generate a license control number at that time.

The license control number shall only be generated after the CEO has approved the application.

In the My Applications module, the View License button should initially appear as Request Control Number. When the applicant clicks Request Control Number, the system will then generate the license control number, and the button will change from Request Control Number to View License.

However, the applicant will not be able to view the license until the generated control number has been paid. Only after successful payment will the View License functionality be enabled. fix bila kuvuruga kitu 

Kwenye mfumo wa WMA-MIS kwenye module ya license report tengeneza report ya leseni na ueke na sehemu ya kuview hiyo tengeneza report nzuri 

kwenye my application boresha condition kwa sas naona napata control number ya leseni kama nilivyokua natakaka but boresha baada ya leseni kutoka ile button ya view inakua kama inavyotaki hiden mpaka malipo yatakapo kua paid ndipo view lesen button iwe inaweza kufanya kazi kwa sasa malipo yako paid but button still haioneshe leseni fix that iwe kam ilivyokua usivuruge kitu chochote 

Implement Forgot Password functionality where a user initiates the process by entering their registered phone number on the login page. The system should verify that the phone number exists and then generate a temporary numeric token (OTP) with an expiry time. This token must be saved in the database and sent to the user via SMS using SmsLibrary.php. After receiving the SMS, the user enters the token into the system, which validates that the token is correct, not expired, not previously used, and linked to the correct user account. If the token is valid, the system should allow the user to proceed to a password reset page where they must create and confirm a new password. The new password must meet security requirements and must not be the same as the previous password. Once submitted successfully, the system should hash and update the new password in the database, mark the token as used to prevent reuse, and then redirect the user to the login page with a success message indicating that the password has been changed successfully

Implement an automated SMS notification feature in the OSA system to inform an applicant whenever their submitted license application documents are reviewed and returned for corrections. Once an officer marks the application documents as “Returned” in the system, the system should immediately trigger two notifications: an in-system notification displayed on the applicant’s profile and an SMS sent to the applicant’s registered phone number. The SMS message content should be: “Habari, maombi yako ya leseni uliyowasilisha kwenye mfumo wa OSA yamefanyiwa mapitio na yamerudishwa kwa marekebisho kutokana na makosa kwenye nyaraka ulizowasilisha. Tafadhali rekebisha na uwasilishe tena kupitia mfumo. Asante.” The SMS must be sent automatically using the system’s SMS service and should only be triggered when the application status changes to “Returned” to avoid duplicate or unnecessary messages. \

Fix the document re-upload functionality in the OSA system when an application is returned for correction. Currently, when an applicant uploads a corrected document, the previously returned document is being deleted instead of being replaced. The system should be updated so that once a document is marked as “Returned,” the applicant is allowed to re-upload a new version of the same document, and the new upload should replace the returned document while preserving the original document record, metadata, and history. The re-uploaded document must be correctly linked to the same document type and application, maintaining continuity as it was before the return action. This ensures proper document version control, prevents unintended deletions, and allows officers to review the updated document seamlessly without breaking the original application flow

Revert the document re-upload behavior in the OSA system to its previously working implementation. The document re-upload functionality for returned applications was already implemented and functioning correctly, allowing applicants to re-upload corrected documents and properly replace the returned document without deleting the original document record, metadata, or history. The system should be restored to this original behavior so that when a document is marked as “Returned,” any subsequent upload by the applicant replaces the returned document while maintaining the same document reference, type, and application linkage as before. This reversion must be limited strictly to this functionality only and should not affect any other system processes or features.

Kwenye upande wa return attachment apploaded work flow in akua hiv baada ya document kua return kwa applicant mfumo unaonesha document iliokua return vizuri na applicant kwa sasa ana upload vizuri na kusave but rekebisha kwanzia hapa applicant akisha save tu ile document ilikua mwanzo return iwe deleted automatic na iwe replaced na hii mpya na status yake itakua under revew kama sasa kisha kwa upande wa WMA-MIS document ioneshe status ya resubmitted na iongezeke button moja ya acceptna wakati ana view iwe ile new document na akisha view akaona iko sawa ndipo atarudi kuclick submitt na status ya ile card itakua upload na ile accept button itapotea na huku kwa applicatant status  itachange kutoka kwa under review kwenda uploaded

Implement the Return Attachment Upload Workflow in the OSA system so that when a document is marked as “Returned” for correction, the applicant can view the returned document and upload a corrected version. Once the applicant uploads the new document and clicks Save, the system should automatically delete the previously returned document and replace it with the new upload, setting its status to “Under Review”. On the WMA-MIS officer side, the newly uploaded document should appear with a status of “Resubmitted” and a single Accept button. The officer can click View to verify the document, and once confirmed, clicks Submit, which updates the document status to “Uploaded” and removes the Accept button. On the applicant side, the document status should change from “Under Review” to “Uploaded”, indicating that the corrected document has been successfully approved. This workflow ensures proper version control, maintains consistent document references, and logs all status transitions for audit purposes.

applicant portal should be modified in the way it handles applicant sessions in order to eliminate the inconvenience of displaying a Session Expired message while the user is still logged in. Instead, the system should monitor user inactivity for a period of 10 minutes, and if no action is performed within that time, a warning card should be displayed to inform the applicant that their session is about to expire, including a seconds countdown showing the remaining time. If the applicant resumes activity before the countdown ends, the session should continue normally; however, if the countdown finishes without any user activity, the system should automatically log the applicant out. This approach will improve the user experience and prevent abrupt interruptions without prior notice.

Implement pattern approval ifanye kazi kama ilivyo kwenye license application kwenye Personal Information na kwenye Business Information Tax Number (TIN) , Region *
District *

Ward *

Postal Code *

Select Postal Code
Street/Village * hivi ziwe lazima sehem hii lakini Company Name *

Company Email *


Company Phone * hizi sio lazima  kwenye Contact & Security
 kama ilivyo na Create Your Practitioner Account
 iwe kama ilivyo usichanganye hii ni pattern approval



kwenye upande wa maneno kuliko andikwa Eligibility Requirements Before You Begin
 weka haya REQUEST FOR A PATTERN APPROVAL

We hereby submit our request for Pattern Approval for the measuring instrument/device as per the requirements of the Weights and Measures regulations.

To process this approval, the applicant is required to upload and submit the following documents through the system:
	1.	Pattern Approval Certificate from the Country of Origin
(Issued by the relevant National Metrology Authority or a recognized body).
	2.	Operation Manual of the Device, which must clearly include:
	•	Installation procedures
	•	Operation instructions
	•	Maintenance guidelines
	•	Testing and verification procedures
	3.	Calibration Manual of the Instrument, detailing:
	•	Calibration procedures
	•	Calibration intervals
	•	Reference standards used
	4.	Technical Specifications of the Instrument, including but not limited to:
	•	Measurement range
	•	Accuracy class
	•	Resolution
	•	Environmental operating conditions
	5.	Information on the Means of Sealing, where not obvious, including:
	•	Description of sealing points
	•	Instructions on sealing and removal of seals

Please note that all documents must be clear, complete, and uploaded in the system to enable timely review and processing of the Pattern Approval application.


 Kwenye pattern approval module ya pattern-approval/application iwe active na ukiclick ionesha Personal Information, Company Information applicant alizo register implement 

 card kwenye module ya pattern approvalinayofuata itakua select of pattern tayp to be approved then kutakea weighting Istrument ,Fuel pump,water meter ,capacity measures , then baada yakua zote tunaanza tengeneza iput ya kila moja tukianza na weighting Istrument ukiclick itokee drop down yakuselect Types of weighing Instruments ambazo ni Counter Scale cIs
Platform scale P/m
balance scale S/B
Spring balance BS 
Woighbridge W/B

then ukiselect Types of weighing Instruments pattern itatokea sehemu yakuapplod document ambayo ni manual calibratio, na specification of isntrument then kutakua na input za kujaza ambazo ni brand name, Make, serial number, na maximam capacity 


kwenye card ya fuel pump kwenye category ya standard fuel pump uki click implement this form kwa umakini . General Form Structure
	•	The application shall be implemented as a multi-section digital form (step-based wizard or collapsible sections).
	•	Each section shall have:
	•	Clearly defined mandatory and optional fields
	•	Input validation rules
	•	The system shall support saving applications as drafts prior to final submission.
	•	Certain fields shall be conditionally displayed or enabled based on user selections.
	•	All submitted data shall be stored in a structured and auditable format.

⸻

2. Manufacturer Details

Purpose: To identify the origin of the fuel pump.
	•	Manufacturer Name
	•	Input type: Text
	•	Mandatory
	•	Stores the official name of the manufacturer.
	•	Country of Manufacture
	•	Input type: Dropdown or Text
	•	Mandatory
	•	Used for regulatory traceability and international conformity assessment.

⸻

3. Fuel Pump Identification

Purpose: To uniquely identify the fuel pump(s) covered by the application.
	•	Make / Brand
	•	Input type: Text
	•	Mandatory
	•	Model / Type Designation
	•	Input type: Text
	•	Mandatory
	•	Quantity of Pumps
	•	Input type: Number (integer, minimum value = 1)
	•	Mandatory
	•	Determines how many serial numbers must be provided.
	•	Serial Number
	•	Input type: Dynamically generated text fields
	•	Mandatory
	•	System Rule:
	•	The number of serial number fields shall automatically match the entered quantity of pumps.
	•	Each serial number shall be unique.
	•	Manufacturing Year
	•	Input type: Number or Date (Year only)
	•	Optional
	•	Number of Nozzles / Measuring Units
	•	Input type: Number
	•	Mandatory
	•	Dispenser Type
	•	Input type: Dropdown (single selection)
	•	Mandatory
	•	Options:
	•	Single hose
	•	Multi-hose
	•	Multi-product

⸻

4. Metrological Characteristics

Purpose: To capture the measurement-related technical parameters.
	•	Measured Quantity
	•	Fixed value: Volume
	•	Display only (non-editable)
	•	Fuel Type(s)
	•	Input type: Dropdown (single or multi-select, as defined by regulation)
	•	Mandatory
	•	Options:
	•	Petrol
	•	Diesel
	•	Kerosene
	•	Other (if selected, a text field shall appear to specify)
	•	Minimum Flow Rate (Qmin)
	•	Input type: Numeric (L/min)
	•	Mandatory
	•	Maximum Flow Rate (Qmax)
	•	Input type: Numeric (L/min)
	•	Mandatory
	•	Validation: Qmax must be greater than Qmin.
	•	Minimum Measured Volume (Vmin)
	•	Input type: Numeric (Litres)
	•	Mandatory
	•	Operating Temperature Range
	•	Input type: Numeric range (°C)
	•	Mandatory

⸻

5. Accuracy & Performance

Purpose: To define declared metrological performance.
	•	Declared Accuracy Class
	•	Input type: Numeric or Text (percentage format)
	•	Mandatory
	•	Maximum Permissible Error (Declared)
	•	Input type: Numeric or Text
	•	Mandatory
	•	Used for conformity assessment and verification decisions.

⸻

6. Indicating & Power System

Purpose: To describe indication, display, and power characteristics.
	•	Volume Indicator Type
	•	Input type: Checkbox (single selection only)
	•	Mandatory
	•	Options:
	•	Mechanical
	•	Electronic
	•	Price Display
	•	Input type: Checkbox / Radio button
	•	Mandatory
	•	Options:
	•	Yes
	•	No
	•	Display Location
	•	Input type: Checkbox (single or multiple as allowed)
	•	Mandatory
	•	Options:
	•	Customer side
	•	Operator side
	•	Both
	•	Power Supply
	•	Input type: Checkbox (multiple selection allowed)
	•	Mandatory
	•	Options:
	•	Mains
	•	Generator
	•	Solar
	•	Battery backup

⸻

7. Software Information (Electronic Pumps Only)

Purpose: To capture legally relevant software information.
	•	Conditional Rule:
	•	This section shall be visible only if “Electronic” indicator type is selected.
	•	Software Version
	•	Input type: Text
	•	Mandatory (for electronic pumps)
	•	Software Legally Relevant
	•	Input type: Radio button
	•	Mandatory
	•	Options:
	•	Yes
	•	No
	•	Software Protection Method
	•	Input type: Checkbox (multiple selection allowed)
	•	Mandatory
	•	Options:
	•	Password
	•	Hardware seal
	•	Secure module
	•	Event Log Available
	•	Input type: Radio button
	•	Mandatory
	•	Options:
	•	Yes
	•	No

⸻

8. Sealing & Security

Purpose: To document tamper-prevention measures.
	•	Adjustment Points Requiring Sealing
	•	Input type: Text area
	•	Mandatory
	•	Seal Type
	•	Input type: Checkbox (multiple selection allowed)
	•	Mandatory
	•	Options:
	•	Wire seal
	•	Lead seal
	•	Electronic seal
	•	Seal Locations
	•	Input type: Text area or reference to uploaded diagram
	•	Mandatory

⸻

9. Installation Information

Purpose: To define intended installation and usage conditions.
	•	Intended Installation
	•	Input type: Checkbox (multiple selection allowed)
	•	Mandatory
	•	Options:
	•	Fixed fuel station
	•	Mobile tanker
	•	Intended Country of Use
	•	Input type: Text or Dropdown
	•	Mandatory
	•	Installation Manual Available
	•	Input type: Radio button
	•	Mandatory
	•	Options:
	•	Yes
	•	No

⸻

10. Supporting Documents (Upload Section)

Purpose: To collect all required technical and regulatory evidence.
	•	File Upload Fields (multiple files allowed where applicable):
	•	Calibration manual
	•	User / installation manual
	•	Drawings or photographs:
	•	Pump exterior
	•	Nameplate
	•	Display
	•	Sealing points
	•	Type examination certificate (if available)
	•	Software documentation (if applicable)
	•	System Rules:
	•	Accept only approved file formats (PDF, JPG, PNG).
	•	Enforce maximum file size limits.
	•	Uploaded documents shall be linked to the specific application record.

1.	implement kwenye Meter card kwenye category za water meter , flow meter ,bulk flow meter form hii form
 Brand Meter Name: ___________________________
Quntity of meter 
Serial Number: ( idadi ya sehemu zakuingiza serial number zina tegemeana na quantity of meter ) 
Nominal flow rate (Q₃) in m³/h: 
Class of meter /
Ratio (R): (optional)
Maximum Admissible Pressure (MAP) in bar/Mpa: ___________________
Maximum Temperature (T) in °C: 
Meter Size (DN)
Size / Diameter of the meter: ________________
Position of measurement H/V:  check box yes or no 
Sealing Mechanism / Adjustment knob (checkbox inayoonesh provided or not provided)
Direction of flow ( checkbox inayoonesh indicated or not indicated 


design kwenye category ya Standard Electrical Meter hii form t
Brand meter name………..
Meter manufacturer: ___________________________
Meter model: _________________________________
Quantity of meter
Serial number(s): (serial number idadi  inategemeana na quantity of meter)
Meter type (electromechanical / static): ___________________________

Accuracy class: select class i,ii,iii,iv
 
Nominal voltage, Unom: ____________ V
Nominal frequency, fnom: __________ Hz
Maximum current, Imax: ____________ A
Transitional current, Itr: __________ A
Minimum current, Imin: ____________ A
Starting current, Ist: _____________ A
 
☐ Direct-connected
☐ Current transformer
☐ Current and Voltage transformers
 
Connection mode (select phases, wires, elements):
Alternative connection mode(s):
 
Direction of energy flow / registers: unaselect kwenye 
☐ Single-register, bi-directional
☐ Single-register, positive direction only
☐ Two-register, bi-directional
☐ Single-register, uni-directional
 
Meter constant: _______________________________
(include units of measurement)

Specified clock frequencies: __________________
(include units of measurement)

Indoor / Outdoor: _____________________________

IP Rating: ___________________________________

Terminal arrangement (e.g. BS, DIN): ___________

Insulation protection class: ___________________
 
Lower specified temperature:
☐ -55°C  ☐ -40°C  ☐ -25°C  ☐ -10°C  ☐ +5°C
Upper specified temperature:
☐ +30°C  ☐ +40°C  ☐ +55°C  ☐ +70°C
 Humidity class:
☐ H1  ☐ H2  ☐ H3
 
Hardware version(s): ___________________________

Software version(s): ___________________________
 
REMARKS:
 
 
 
1.2 Test values

When ranges of values are specified by the manufacturer, the values used for testing shall be specified below.

Test voltage: _______________________ V

Test frequency: _____________________ Hz

Test connection mode: _______________
 
REMARKS:

