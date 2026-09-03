(() => {
    'use strict';
    const meta=document.querySelector('meta[name="csrf-token"]');
    const token=meta?meta.getAttribute('content'):'';
    if(!token)return;
    const qs=new URLSearchParams(window.location.search);
    const payload=(extra={})=>({
        _csrf_token:token,path:window.location.pathname,title:document.title,referrer:document.referrer||'',
        utm_source:qs.get('utm_source')||'',utm_medium:qs.get('utm_medium')||'',utm_campaign:qs.get('utm_campaign')||'',page_type:document.body.getAttribute('data-analytics-page-type')||'',entity_id:document.body.getAttribute('data-analytics-entity-id')||'',...extra
    });
    const send=(url,data)=>{const encoded=JSON.stringify(data);try{if(navigator.sendBeacon){const blob=new Blob([encoded],{type:'application/json'});if(navigator.sendBeacon(url,blob))return;}}catch(_){}fetch(url,{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},body:encoded,keepalive:true,credentials:'same-origin'}).catch(()=>{});};
    send('/analytics/collect',payload());
    document.addEventListener('click',(event)=>{const target=event.target.closest('a,button');if(!target)return;let name=null;if(target.matches('.js-whatsapp,.js-whatsapp-service,[href*="wa.me"],[href*="whatsapp"]'))name='cta_whatsapp';else if(target.matches('[href="/get-help"],[href^="/get-help?"]'))name='cta_get_help';else if(target.matches('[href="/assistant"]'))name='cta_assistant';if(!name)return;send('/analytics/event',payload({event_name:name,metadata:{label:(target.textContent||'').trim().slice(0,120)}}));},{passive:true});
})();
