<?php
declare(strict_types=1);
$root=dirname(__DIR__); $checks=[];
$routes=file_get_contents($root.'/routes/web.php'); $migration=file_get_contents($root.'/database/migrations/007_work_projects.sql');
$controller=file_get_contents($root.'/app/Controllers/WorkController.php'); $home=file_get_contents($root.'/resources/views/pages/home.php'); $js=file_get_contents($root.'/public_html/assets/js/work-carousel.js'); $sitemap=file_get_contents($root.'/app/Controllers/SeoController.php'); $service=file_get_contents($root.'/resources/views/services/show.php');
$checks['public work routes']=str_contains($routes,"get('/work'") && str_contains($routes,"get('/work/{slug}'");
$checks['admin CRUD routes']=str_contains($routes,"get('/admin/work/create'") && str_contains($routes,"post('/admin/work/{id}/delete'");
$checks['permission and project table']=str_contains($migration,"'work.manage'") && str_contains($migration,'CREATE TABLE IF NOT EXISTS work_projects');
$checks['draft and published lifecycle']=str_contains($controller,"['draft','published','archived']") && str_contains($controller,"WHERE w.status='published'");
$checks['secure image upload validation']=str_contains($controller,'is_uploaded_file($tmp)') && str_contains($controller,'8*1024*1024') && str_contains($controller,'image/jpeg');
$checks['homepage featured carousel']=str_contains($home,'data-work-carousel') && str_contains($home,'featuredProjects');
$checks['carousel pause and reduced motion']=str_contains($js,'prefers-reduced-motion') && str_contains($js,'data-carousel-toggle') && str_contains($js,'visibilitychange');
$checks['service related work']=str_contains($service,'Related work');
$checks['SEO sitemap projects']=str_contains($sitemap,"'/work/'");
$checks['client project enquiry']=str_contains(file_get_contents($root.'/resources/views/work/show.php'),'Request a similar project');
foreach($checks as $name=>$pass) echo ($pass?'PASS':'FAIL').': '.$name."\n";
if(in_array(false,$checks,true)) exit(1); echo 'PASS: '.count($checks).' portfolio regression checks.'."\n";
