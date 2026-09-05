<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();
$config = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',$config['host'],$config['port'],$config['name'],$config['charset']),$config['user'],$config['pass'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->beginTransaction();
try {
$settings=['site_name'=>'AlbaTech Solutions','site_tagline'=>'Tell us the task. We’ll help with the next step.','contact_phone'=>'+254 792 159 806','whatsapp_number'=>'254792159806','site_country'=>'Kenya','site_currency'=>'KES','government_services_disclaimer'=>'AlbaTech Solutions provides independent assistance and is not a government agency.'];
$stmt=$pdo->prepare('INSERT INTO settings (`key`, `value`, `type`) VALUES (:key,:value,:type) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `type`=VALUES(`type`)');
foreach($settings as $key=>$value){$stmt->execute(['key'=>$key,'value'=>$value,'type'=>'string']);}
$categories=[['Government & Digital Assistance','government-digital-assistance',1],['Business Services','business-services',2],['Career Services','career-services',3],['Online Presence','online-presence',4],['IT Support','it-support',5],['Web & Software','web-software',6],['Design & Marketing','design-marketing',7]];
$stmt=$pdo->prepare('INSERT INTO service_categories (name,slug,sort_order) VALUES (:name,:slug,:sort_order) ON DUPLICATE KEY UPDATE name=VALUES(name),sort_order=VALUES(sort_order),deleted_at=NULL');
foreach($categories as [$name,$slug,$sort]){$stmt->execute(['name'=>$name,'slug'=>$slug,'sort_order'=>$sort]);}
$ids=[]; foreach($categories as [,$slug]){$q=$pdo->prepare('SELECT id FROM service_categories WHERE slug=:slug');$q->execute(['slug'=>$slug]);$ids[$slug]=(int)$q->fetchColumn();}
$services=[
['government-digital-assistance','KRA Returns Filing','kra-returns-filing','Help with KRA returns filing, including nil returns where applicable.','fa-file-invoice-dollar',1],
['government-digital-assistance','Tax Compliance','tax-compliance','Practical help organising routine tax compliance tasks.','fa-scale-balanced',0],
['government-digital-assistance','KRA Nil Returns','kra-nil-return','Help with KRA nil return filing where applicable.','fa-file-circle-check',0],
['government-digital-assistance','eCitizen Services','ecitizen-services','Independent help navigating selected eCitizen services.','fa-landmark',1],
['business-services','Business Registration','business-registration','Help with business registration steps and required information.','fa-building',1],
['business-services','CR12 Application','cr12-application','Help with CR12 application steps and company information.','fa-file-contract',0],
['government-digital-assistance','NTSA Services','ntsa-services','Help navigating selected NTSA services.','fa-car',0],
['government-digital-assistance','SHA Registration','sha-registration','Help preparing for SHA registration.','fa-notes-medical',0],
['government-digital-assistance','NSSF Services','nssf-services','Help with selected NSSF digital processes.','fa-piggy-bank',0],
['career-services','CV Writing','cv-writing','Clear professional CV writing tailored to your applications.','fa-file-lines',1],
['online-presence','Domain Registration','domain-registration','Help choosing and configuring a business domain.','fa-globe',0],
['online-presence','Web Hosting','web-hosting','Website hosting setup and practical support.','fa-server',0],
['online-presence','Business Email Setup','business-email-setup','Professional business email on your own domain.','fa-envelope',0],
['online-presence','Google Business Profile Setup','google-business-profile-setup','Help setting up and improving your Google Business Profile.','fa-map-location-dot',1],
['it-support','IT Support','it-support','Practical troubleshooting and technology support.','fa-headset',1],
['it-support','Computer Repair','computer-repair','Laptop and desktop diagnosis and repair.','fa-screwdriver-wrench',1],
['it-support','CCTV Installation','cctv-installation','CCTV installation and practical setup.','fa-video',0],
['it-support','Networking / Wi-Fi','wifi-networking','Wi-Fi and network setup for reliable connectivity.','fa-network-wired',0],
['web-software','Website Design and Development','website-design-kenya','Mobile-first website design and development for Kenyan businesses.','fa-laptop-code',1],
['web-software','Software Development','software-development','Custom software and business systems built around practical workflows.','fa-code',0],
['design-marketing','Graphic Design','graphic-design','Practical graphic design for business communication.','fa-palette',0],
['design-marketing','Digital Marketing','digital-marketing','Practical digital marketing support for visibility.','fa-bullhorn',0]];
$stmt=$pdo->prepare('INSERT INTO services (category_id,name,slug,summary,icon,price_type,status,is_featured,sort_order) VALUES (:category_id,:name,:slug,:summary,:icon,:price_type,:status,:is_featured,:sort_order) ON DUPLICATE KEY UPDATE category_id=VALUES(category_id),name=VALUES(name),summary=VALUES(summary),icon=VALUES(icon),status=VALUES(status),is_featured=VALUES(is_featured),sort_order=VALUES(sort_order),deleted_at=NULL');
foreach($services as $i=>[$category,$name,$slug,$summary,$icon,$featured]){$stmt->execute(['category_id'=>$ids[$category],'name'=>$name,'slug'=>$slug,'summary'=>$summary,'icon'=>$icon,'price_type'=>'quote','status'=>'published','is_featured'=>$featured,'sort_order'=>$i+1]);}

// Phase 3.3 priority service content. The shared public template reads this data dynamically.
$priority = [
'kra-returns-filing'=>[
'meta_title'=>'KRA Returns Filing Kenya | AlbaTech Solutions',
'meta_description'=>'Need help filing KRA returns or a nil return in Kenya? AlbaTech provides independent, practical help with the next step.',
'description'=>'<h2>KRA returns filing help in Kenya</h2><p>Need help understanding or filing your KRA return? Start by telling us your situation. We help you organise the information needed, identify the practical next step and complete the digital process where appropriate.</p><p>This service can be useful if you need help filing a return, are unsure whether a nil return applies, or want help getting your information ready before filing. The exact return and information needed depend on your tax situation.</p><h2>Who this is for</h2><p>Individuals, employees, small business owners and other taxpayers who need practical help with routine KRA return filing and related digital steps.</p><h2>What happens next</h2><p>Tell us what you are trying to file. We check the information available, explain what may be needed and confirm the assistance process before work starts. Do not send passwords, PINs or OTPs.</p><p>AlbaTech provides independent assistance and is not KRA. Official obligations, requirements and deadlines remain your responsibility and complex tax advice may require a qualified professional.</p>',
'requirements'=>['KRA PIN or relevant taxpayer details','Active phone number and email','Income, tax or supporting records relevant to the return','Any information needed for the specific return'],
'faqs'=>[['q'=>'Can you help me file a KRA nil return?','a'=>'Yes, where a nil return is applicable. Tell us your situation first so we can help with the correct next step.'],['q'=>'Do I need to give you my password?','a'=>'Do not send passwords, PINs or OTPs. We will explain the access or information needed for the service.'],['q'=>'Are you KRA?','a'=>'No. AlbaTech Solutions is an independent assistance service and is not a government agency.'],['q'=>'Can you give tax advice?','a'=>'We provide practical filing assistance. Complex tax advice may require a qualified tax professional.']]
],
'ecitizen-services'=>[
'meta_title'=>'eCitizen Services Help Kenya | AlbaTech Solutions',
'meta_description'=>'Get independent help with selected eCitizen services in Kenya. Tell AlbaTech the task and we will help with the next step.',
'description'=>'<h2>Practical eCitizen help in Kenya</h2><p>eCitizen can involve different services, forms and document requirements. You do not have to know every step before asking for help. Tell us what you are trying to apply for and we will help you understand the next step.</p><p>We provide independent assistance with selected digital processes, including checking what information may be needed, helping you prepare documents and guiding you through the online steps where appropriate.</p><h2>Who this is for</h2><p>People and businesses in Kenya who are having difficulty understanding an eligible eCitizen process or want practical help preparing for an application.</p><h2>What happens next</h2><p>Describe the service you need. We review the task, explain what may be required and confirm the assistance process before proceeding. Official requirements and fees can change and are set by the relevant institution.</p><p>AlbaTech is independent help, not a government office.</p>',
'requirements'=>['Relevant identification details','Active phone number and email','Documents required for the selected service','Accurate application information'],
'faqs'=>[['q'=>'What eCitizen services can you help with?','a'=>'Tell us the task first. We will confirm whether it is a service we can assist with.'],['q'=>'Are official fees included?','a'=>'Official fees are separate unless we clearly state otherwise.'],['q'=>'Are you a government office?','a'=>'No. AlbaTech provides independent assistance and is not a government agency.'],['q'=>'Should I send my password or OTP?','a'=>'No. Never send passwords, PINs or OTPs.']]
],
'business-registration'=>[
'meta_title'=>'Business Registration Kenya | AlbaTech Solutions',
'meta_description'=>'Need help registering a business in Kenya? AlbaTech provides practical, independent assistance with business registration steps and next actions.',
'description'=>'<h2>Business registration help in Kenya</h2><p>Starting a business can involve name choices, personal information and different registration steps. We help you organise what is needed and understand the practical next step before you begin.</p><p>This service is for entrepreneurs who want help navigating business registration and preparing the information required for the selected registration process.</p><h2>Who this is for</h2><p>New entrepreneurs, small businesses and people who need practical help understanding how to start the registration process in Kenya.</p><h2>What happens next</h2><p>Tell us what type of business you are planning and what stage you are at. We explain the next practical step, identify the information you may need and agree on the assistance process before work begins.</p><p>AlbaTech is an independent assistance service. Official registration decisions, fees and requirements are set by the relevant authority.</p>',
'requirements'=>['Relevant identification details','Active phone number and email','Preferred business name choices where applicable','Accurate business and owner information'],
'faqs'=>[['q'=>'Can you help me choose a business name?','a'=>'We can help you organise your preferred name options for the registration process, subject to official availability and approval.'],['q'=>'Do you guarantee name approval?','a'=>'No. Approval decisions are made by the relevant authority.'],['q'=>'Are government fees included?','a'=>'Official fees are separate unless clearly stated otherwise.'],['q'=>'Can you also help with a CR12?','a'=>'Yes, CR12 is available as a related service for eligible registered companies.']]
],
'cv-writing'=>[
'meta_title'=>'CV Writing Kenya | AlbaTech Solutions',
'meta_description'=>'Professional CV writing in Kenya. Get a clear, well-structured CV tailored to the roles you want to apply for.',
'description'=>'<h2>CV writing help for job applications in Kenya</h2><p>Your CV should make it easy for an employer to understand your experience, skills and strengths. We help you create or improve a clear, professional CV based on the roles you want to apply for.</p><p>We can help reorganise an existing CV, improve wording, highlight relevant experience and create a cleaner structure. If you are starting without a finished CV, tell us what experience and education you have so we can understand the best starting point.</p><h2>Who this is for</h2><p>Job seekers, recent graduates and professionals who want a clearer CV for current or future applications.</p><h2>What happens next</h2><p>Share your current CV if you have one, together with the type of role you are targeting. We review the available information and explain what we need before work starts.</p>',
'requirements'=>['Current CV if available','Work experience and responsibilities','Education and relevant training','The type of role you want to apply for'],
'faqs'=>[['q'=>'Can you write a CV from scratch?','a'=>'Yes. We can start with your experience, education and target role.'],['q'=>'Can you improve my existing CV?','a'=>'Yes. We can review and restructure an existing CV where appropriate.'],['q'=>'Do you guarantee a job?','a'=>'No. A CV can improve how you present your experience but hiring decisions remain with employers.'],['q'=>'Can you tailor a CV to a specific role?','a'=>'Yes, when you provide the role or job description you are targeting.']]
],
'website-design-kenya'=>[
'meta_title'=>'Website Design Kenya | AlbaTech Solutions',
'meta_description'=>'Website design and development for Kenyan businesses. Get a clear, mobile-friendly website built around your business and customer enquiries.',
'description'=>'<h2>Website design for businesses in Kenya</h2><p>Your website should help people understand what you do and make it easy for them to contact you. We design and develop practical, mobile-friendly websites around your business, services and customer journey.</p><p>Projects can range from a simple business website to a more custom web solution. We start by understanding what you want the website to achieve instead of forcing every business into the same template.</p><h2>What we can help with</h2><p>Website planning, page structure, design and development, domain and hosting guidance, business email setup and practical SEO foundations can all be considered depending on your project.</p><h2>What happens next</h2><p>Tell us about your business, what you want the website to do and whether you already have a domain, hosting or existing website. We then clarify scope and prepare the next step.</p>',
'requirements'=>['Business name and contact details','Services or products you want to show','Logo and brand assets if available','Preferred domain or existing website details'],
'faqs'=>[['q'=>'How much does a website cost in Kenya?','a'=>'Cost depends on the pages, features, content and integrations needed. We can prepare a quote after understanding the project.'],['q'=>'Will the website work on phones?','a'=>'Yes. Mobile usability is a core part of the build.'],['q'=>'Can you work with my existing domain?','a'=>'Yes. We can assess the existing domain or website setup.'],['q'=>'Do you provide hosting and business email?','a'=>'Yes, where required, these can be included as related services.']]
],
'computer-repair'=>[
'meta_title'=>'Computer & Laptop Repair Kenya | AlbaTech Solutions',
'meta_description'=>'Need laptop or desktop repair in Kenya? AlbaTech provides practical diagnosis, troubleshooting and repair support for computer problems.',
'description'=>'<h2>Laptop and desktop repair support</h2><p>A slow, faulty or damaged computer can interrupt work, school and business. We help diagnose common laptop and desktop problems and explain the practical repair options before work proceeds.</p><p>Depending on the issue, support may involve troubleshooting software problems, assessing hardware faults, replacing suitable components or recommending whether repair is worthwhile.</p><h2>What happens next</h2><p>Tell us the device type, what the problem is and when it started. If there are visible errors, share the exact message where safe to do so. We assess the information and advise the next practical step.</p><p>Repair needs can vary depending on the device and fault. Parts, onsite work and more complex repairs may require a quote.</p>',
'requirements'=>['Laptop or desktop make and model','Clear description of the problem','Any error message or recent change','Access to the affected device when needed'],
'faqs'=>[['q'=>'Do you repair laptops and desktops?','a'=>'Yes. Tell us the device and problem so we can assess the appropriate next step.'],['q'=>'Can you tell me the cost before repair?','a'=>'Where possible we explain the expected cost or provide a quote before proceeding.'],['q'=>'Will I lose my files?','a'=>'Some repairs carry data risk. We recommend backing up important files where possible and discussing data concerns before work starts.'],['q'=>'Do you provide onsite support?','a'=>'Availability depends on the location and nature of the issue.']]
],
'it-support'=>[
'meta_title'=>'IT Support Kenya | AlbaTech Solutions',
'meta_description'=>'Practical IT support in Kenya for troubleshooting, setup, maintenance and everyday technology problems for people and businesses.',
'description'=>'<h2>Practical IT support when technology gets in the way</h2><p>When your devices, software or office technology are not working properly, you need clear help rather than technical jargon. We provide practical IT support for troubleshooting, setup, maintenance and everyday technology issues.</p><p>Support can cover computers, software, device setup, connectivity and business technology needs depending on the situation.</p><h2>Who this is for</h2><p>Individuals, small offices and growing businesses that need help resolving a technology problem or setting up a practical IT solution.</p><h2>What happens next</h2><p>Describe the problem, device or system involved and where the issue is happening. We review the situation and explain the next practical step. More complex or onsite work may require a quote.</p>',
'requirements'=>['Description of the problem or requirement','Device or system details','Location or network details where applicable','Access to the affected equipment when required'],
'faqs'=>[['q'=>'Do you support small businesses?','a'=>'Yes. We provide practical support for individuals and small or growing businesses.'],['q'=>'Can you come onsite?','a'=>'Onsite availability depends on location, scheduling and the issue.'],['q'=>'Can you help remotely?','a'=>'Some issues can be assessed or resolved remotely, depending on the situation.'],['q'=>'Do you offer ongoing IT support?','a'=>'Tell us your needs and we can discuss a practical support arrangement.']]
],
'google-business-profile-setup'=>[
'meta_title'=>'Google Business Profile Setup Kenya | AlbaTech',
'meta_description'=>'Get help setting up or improving your Google Business Profile in Kenya so customers can find your business on Google Search and Maps.',
'description'=>'<h2>Help customers find your business on Google</h2><p>A complete Google Business Profile can make it easier for local customers to find your business information on Google Search and Maps. We help you set up or improve the information you control and prepare the profile for accurate customer enquiries.</p><p>Support can include business details, categories, services, hours, contact information and profile content. Verification and visibility decisions remain with Google.</p><h2>Who this is for</h2><p>Businesses that have no profile yet, have incomplete information or want help improving the accuracy and usefulness of an existing profile.</p><h2>What happens next</h2><p>Tell us your business name, location and whether you already have a profile. We review the current situation and explain what information may be needed before proceeding.</p>',
'requirements'=>['Business name','Accurate business address or service area','Business phone number and website where available','Business category, hours and service information'],
'faqs'=>[['q'=>'Can you guarantee my business will rank first on Google?','a'=>'No. Search and Maps rankings are controlled by Google and depend on many factors.'],['q'=>'Can you create a new profile?','a'=>'We can help prepare and set up an eligible profile. Verification requirements are controlled by Google.'],['q'=>'Can you improve an existing profile?','a'=>'Yes. We can review the information and help improve completeness and accuracy.'],['q'=>'Do I need a website first?','a'=>'A website is helpful but the exact setup depends on your business situation.']]
]
];
$updateService = $pdo->prepare('UPDATE services SET summary = :summary, description = :description, meta_title = :meta_title, meta_description = :meta_description, faqs = :faqs WHERE slug = :slug');
$upsertCommerce = $pdo->prepare('INSERT INTO service_commerce (service_id, pricing_mode, requirements, intake_questions, requires_quote, instant_request, active, government_fee_note, fee_disclaimer) VALUES (:service_id, :pricing_mode, :requirements, :intake_questions, 1, 1, 1, :government_fee_note, :fee_disclaimer) ON DUPLICATE KEY UPDATE requirements = VALUES(requirements), intake_questions = VALUES(intake_questions), government_fee_note = VALUES(government_fee_note), fee_disclaimer = VALUES(fee_disclaimer), active = VALUES(active)');
$findServiceId = $pdo->prepare('SELECT id FROM services WHERE slug = :slug LIMIT 1');
foreach ($priority as $slug => $data) {
    $summary = strip_tags((string)$data['description']);
    $summary = mb_substr($summary, 0, 220);
    $updateService->execute([
        'slug' => $slug,
        'summary' => $summary,
        'description' => $data['description'],
        'meta_title' => $data['meta_title'],
        'meta_description' => $data['meta_description'],
        'faqs' => json_encode($data['faqs'], JSON_UNESCAPED_UNICODE),
    ]);
    $findServiceId->execute(['slug' => $slug]);
    $serviceId = (int)$findServiceId->fetchColumn();
    if ($serviceId > 0) {
        $questions = [
            ['key'=>'task_details','label'=>'Please describe what you need help with.','type'=>'textarea','required'=>true,'help'=>'Use simple words and include any important context.'],
            ['key'=>'location','label'=>'Where are you located?','type'=>'text','required'=>false,'help'=>'Town, area or county where relevant.']
        ];
        $upsertCommerce->execute([
            'service_id'=>$serviceId,
            'pricing_mode'=>'quote',
            'requirements'=>json_encode($data['requirements'], JSON_UNESCAPED_UNICODE),
            'intake_questions'=>json_encode($questions, JSON_UNESCAPED_UNICODE),
            'government_fee_note'=>'Official fees, where applicable, are separate and set by the relevant institution.',
            'fee_disclaimer'=>'Any AlbaTech charge covers independent assistance and will be confirmed before work begins.',
        ]);
    }
}

$pdo->commit(); echo "AlbaTech baseline seed complete.\n";
}catch(Throwable $e){$pdo->rollBack();throw $e;}
