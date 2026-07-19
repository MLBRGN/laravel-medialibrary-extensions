// Synced with package resources/js/shared/tinymce-custom-file-picker-iframe.js
document.getElementById('insert-selected').addEventListener('click',()=>{
  const tinymce = window.parent?.tinymce;
  if(!tinymce){console.error('TinyMCE not found on window.parent');return}
  const selected = Array.from(document.querySelectorAll('[data-mle-media-select-checkbox]:checked')).map(checkbox=>({url:checkbox.dataset.url,alt:checkbox.dataset.alt||'',vspace:'1rem',hspace:'1rem',border:0,borderstyle:'none'}));
  if(!selected.length){tinymce.activeEditor.windowManager.alert('Please select one image.');return}
  if(selected.length>1){tinymce.activeEditor.windowManager.alert('Please select only one image.');return}
  const file = selected[0];
  window.close();
  let instanceId=null;try{const configEl=document.querySelector('.mle-media-manager-config');if(configEl&&configEl.value){const cfg=JSON.parse(configEl.value);instanceId=cfg.instanceId||null}}catch(e){}
  window.parent.postMessage({mce:true,type:'mle:picker:insert',instanceId:instanceId,content:file},'*')
});
