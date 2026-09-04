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
$stmt=$pdo->prepare('INSERT INTO settings (key, value, type) VALUES (:key,:value,:type) ON DUPLICATE KEY UPDATE value=VALUES(value), type=VALUES(type)');
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
$pdo->commit(); echo "AlbaTech baseline seed complete.\n";
}catch(Throwable $e){$pdo->rollBack();throw $e;}
