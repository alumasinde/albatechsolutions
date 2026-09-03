<?php
$title='Digital Assistant Kenya | AlbaTech Solutions';
$metaDescription='Tell AlbaTech what you are trying to get done online. Our digital assistant helps you find the right service and request practical help in Kenya.';
$canonicalUrl=rtrim(config('app.url'),'/').'/assistant';
$jsonLd=[[
 '@context'=>'https://schema.org','@type'=>'WebApplication','name'=>'AlbaTech Digital Assistant','url'=>$canonicalUrl,'applicationCategory'=>'BusinessApplication','description'=>$metaDescription,'operatingSystem'=>'Any'
]];
ob_start();
?>
<section class="assistant-hero">
  <div class="public-container assistant-hero__inner">
    <span class="public-kicker">AlbaTech digital assistant</span>
    <h1>Not sure what service you need?</h1>
    <p>Just describe what you are trying to do. The assistant will help narrow it down, explain the next step, and hand your request to AlbaTech when you are ready.</p>
    <div class="assistant-trust"><span><i class="fa-solid fa-language"></i> Plain language</span><span><i class="fa-solid fa-list-check"></i> Service matching</span><span><i class="fa-solid fa-user-headset"></i> Human handoff</span></div>
  </div>
</section>
<section class="assistant-section">
  <div class="public-container">
    <div class="assistant-shell" id="assistant-app" data-csrf="<?= e($csrf) ?>">
      <div class="assistant-head"><div><span class="assistant-dot"></span><strong>AlbaTech Assistant</strong><small>Digital help for everyday tasks</small></div><button type="button" id="assistant-new" class="assistant-reset">New conversation</button></div>
      <div class="assistant-messages" id="assistant-messages" aria-live="polite"></div>
      <div class="assistant-suggestions" id="assistant-suggestions">
        <button type="button" data-prompt="I want to register a business in Kenya">Register a business</button>
        <button type="button" data-prompt="I need help with KRA returns">KRA help</button>
        <button type="button" data-prompt="I need a CV for a job">CV / job help</button>
        <button type="button" data-prompt="I need a website for my business">Business website</button>
      </div>
      <form class="assistant-composer" id="assistant-form">
        <label class="sr-only" for="assistant-input">Tell AlbaTech what you need</label>
        <textarea id="assistant-input" rows="2" maxlength="2000" placeholder="What are you trying to get done?"></textarea>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-arrow-up"></i><span>Send</span></button>
      </form>
      <p class="assistant-safety"><i class="fa-solid fa-shield-halved"></i> Never send passwords, M-Pesa PINs, OTPs or banking secrets. The assistant will tell you what information is needed.</p>
    </div>
    <div class="assistant-below"><a href="/get-help" class="btn btn-secondary">Prefer a direct request? Get assistance</a><span>AlbaTech is an independent digital assistance business, not a government agency.</span></div>
  </div>
</section>
<script>
(function(){
 const app=document.getElementById('assistant-app'), messages=document.getElementById('assistant-messages'), form=document.getElementById('assistant-form'), input=document.getElementById('assistant-input'), suggestions=document.getElementById('assistant-suggestions'), csrf=app.dataset.csrf;
 let token='';
 const escapeHtml=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
 function add(role,text){const el=document.createElement('div');el.className='assistant-message assistant-message--'+role;el.innerHTML='<div class="assistant-bubble">'+escapeHtml(text).replace(/\n/g,'<br>')+'</div>';messages.appendChild(el);messages.scrollTop=messages.scrollHeight;}
 async function post(url,data){const res=await fetch(url,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(Object.assign({_csrf_token:csrf},data))});return res.json();}
 async function start(){messages.innerHTML='';suggestions.hidden=false;const data=await post('/assistant/start',{});if(data.success){token=data.token;add('assistant',data.message);}else add('assistant','I could not start the assistant. Please use the direct help form.');}
 async function send(text){if(!text||!token)return;add('user',text);input.value='';suggestions.hidden=true;input.disabled=true;const data=await post('/assistant/message',{token:token,message:text});input.disabled=false;if(data.message)add('assistant',data.message);if(data.handoff){const a=document.createElement('div');a.className='assistant-handoff';a.innerHTML='<a class="btn btn-primary" href="/get-help">Get Assistance</a>';messages.appendChild(a);}input.focus();}
 form.addEventListener('submit',e=>{e.preventDefault();send(input.value.trim());});
 suggestions.querySelectorAll('button').forEach(b=>b.addEventListener('click',()=>send(b.dataset.prompt)));
 document.getElementById('assistant-new').addEventListener('click',start); start();
})();
</script>
<?php $pageContent=ob_get_clean(); require dirname(__DIR__).'/layout.php';
