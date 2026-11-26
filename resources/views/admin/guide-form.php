<?php use AlbaTech\Core\View; $g=$guide ?? []; ?>
<div class="form-page-head"><a class="back-link" href="/admin/guides"><i class="fa-solid fa-arrow-left"></i> Back to guides</a><p>Write and format your article. The editor toolbar stays visible while you scroll through the content.</p></div>
<?php if (!empty($errors)): ?><div class="form-error"><strong>Please fix these items:</strong><ul><?php foreach($errors as $error): ?><li><?= View::escape($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" action="<?= empty($g['id'])?'/admin/guides/create':'/admin/guides/'.(int)$g['id'].'/edit' ?>" class="guide-editor-form">
<input type="hidden" name="_csrf" value="<?= View::escape($csrf) ?>">
<div class="guide-admin-grid"><div class="guide-admin-main">
<section class="panel editor-section"><div class="section-heading"><span>01</span><div><h3>Article details</h3><p>Give readers a clear title and a useful summary.</p></div></div>
<div class="form-grid">
<label class="field full">Article title<input id="guide-title" name="title" maxlength="220" required value="<?= View::escape($g['title'] ?? '') ?>" placeholder="e.g. How to prepare for a business website"></label>
<label class="field full">URL slug<input id="guide-slug" name="slug" maxlength="220" required value="<?= View::escape($g['slug'] ?? '') ?>" placeholder="how-to-prepare-for-a-business-website"><small>Used in the public URL. Keep it short, descriptive and lowercase.</small></label>
<label class="field">Category<input name="category" maxlength="100" required value="<?= View::escape($g['category'] ?? 'General') ?>"></label>
<label class="field">Publication status<select name="status"><option value="draft" <?= ($g['status']??'draft')==='draft'?'selected':'' ?>>Draft (private)</option><option value="published" <?= ($g['status']??'')==='published'?'selected':'' ?>>Published (public)</option><option value="archived" <?= ($g['status']??'')==='archived'?'selected':'' ?>>Archived</option></select></label>
<label class="field full">Short summary<textarea name="summary" rows="2" maxlength="500" required><?= View::escape($g['summary'] ?? '') ?></textarea><small>Shown on the Guides listing page.</small></label>
<label class="field full">Introduction<textarea name="intro" rows="3" maxlength="5000" required><?= View::escape($g['intro'] ?? '') ?></textarea><small>Shown below the article title.</small></label>
</div></section>
<section class="panel editor-section"><div class="section-heading"><span>02</span><div><h3>Article content</h3><p>Select text and use the toolbar to format it. Changes are saved with the article.</p></div></div>
<div class="rich-editor" id="guide-rich-editor">
<div class="rich-toolbar" role="toolbar" aria-label="Article formatting tools">
<label class="sr-only" for="format-block">Text style</label><select id="format-block" aria-label="Text style"><option value="p">Paragraph</option><option value="h1">Heading 1</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="h4">Heading 4</option><option value="h5">Heading 5</option><option value="h6">Heading 6</option><option value="blockquote">Quote</option><option value="pre">Code block</option></select>
<button type="button" data-command="bold" title="Bold (Ctrl+B)"><b>B</b></button><button type="button" data-command="italic" title="Italic (Ctrl+I)"><i>I</i></button><button type="button" data-command="underline" title="Underline"><u>U</u></button><button type="button" data-command="strikeThrough" title="Strikethrough"><s>S</s></button>
<span class="toolbar-separator"></span><button type="button" data-command="justifyLeft" title="Align left"><i class="fa-solid fa-align-left"></i></button><button type="button" data-command="justifyCenter" title="Align center"><i class="fa-solid fa-align-center"></i></button><button type="button" data-command="justifyRight" title="Align right"><i class="fa-solid fa-align-right"></i></button>
<span class="toolbar-separator"></span><button type="button" data-action="link" title="Insert link"><i class="fa-solid fa-link"></i></button><button type="button" data-action="image" title="Insert image by URL"><i class="fa-regular fa-image"></i></button><button type="button" data-action="table" title="Insert table"><i class="fa-solid fa-table"></i></button><button type="button" data-command="insertUnorderedList" title="Bulleted list"><i class="fa-solid fa-list-ul"></i></button><button type="button" data-command="insertOrderedList" title="Numbered list"><i class="fa-solid fa-list-ol"></i></button><button type="button" data-command="outdent" title="Decrease indent"><i class="fa-solid fa-outdent"></i></button><button type="button" data-command="indent" title="Increase indent"><i class="fa-solid fa-indent"></i></button><span class="toolbar-separator"></span><button type="button" data-command="undo" title="Undo"><i class="fa-solid fa-rotate-left"></i></button><button type="button" data-command="redo" title="Redo"><i class="fa-solid fa-rotate-right"></i></button>
</div>
<div id="guide-editor" class="rich-editor__canvas" contenteditable="true" role="textbox" aria-label="Guide article content" aria-multiline="true"><?= $g['content_html'] ?? '' ?></div>
<textarea hidden name="content_html" id="guide-content-html"></textarea>
<p class="editor-help">Tip: keep one page title, then use Heading 2 and Heading 3 to organise sections. For images, use a public HTTPS image URL or a site-relative path such as /uploads/guides/example.webp.</p>
</div></section>
<section class="panel editor-section"><div class="section-heading"><span>03</span><div><h3>Search appearance</h3><p>Optional metadata for search results.</p></div></div><div class="form-grid"><label class="field full">SEO title<input name="meta_title" maxlength="255" value="<?= View::escape($g['meta_title'] ?? '') ?>" placeholder="Leave blank to use the article title"></label><label class="field full">Meta description<textarea name="meta_description" rows="2" maxlength="320"><?= View::escape($g['meta_description'] ?? '') ?></textarea></label></div></section>
</div>
<aside class="guide-admin-side"><section class="panel editor-section"><h3>Publish your guide</h3><p class="guide-side-note">Save as a draft while you work, or publish when the article is ready.</p><button class="btn btn-primary btn-full" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save guide</button><a class="btn btn-light btn-full" href="/admin/guides">Cancel</a></section><section class="panel editor-section"><h3>Writing checklist</h3><ul class="guide-checklist"><li>Use a clear, helpful title.</li><li>Break long content into headings.</li><li>Use descriptive link text.</li><li>Add alternative text to informative images.</li><li>Preview before publishing.</li></ul></section></aside></div>
</form>
<script nonce="<?= View::escape($cspNonce ?? '') ?>">
(function(){
 const editor=document.getElementById('guide-editor'), output=document.getElementById('guide-content-html'), form=editor.closest('form'), toolbar=document.querySelector('.rich-toolbar');
 const block=document.getElementById('format-block');
 function sync(){output.value=editor.innerHTML;}
 function run(cmd,value=null){editor.focus();document.execCommand(cmd,false,value);sync();}
 toolbar.querySelectorAll('[data-command]').forEach(b=>b.addEventListener('click',()=>run(b.dataset.command)));
 block.addEventListener('change',()=>run('formatBlock','<'+block.value+'>'));
 toolbar.querySelector('[data-action="link"]').addEventListener('click',()=>{const url=prompt('Enter the link URL (https:// or a site-relative path):');if(url && (/^https?:\/\//i.test(url)||url.startsWith('/'))){run('createLink',url)}else if(url){alert('Use an HTTPS URL or a site-relative path.')}});
 toolbar.querySelector('[data-action="image"]').addEventListener('click',()=>{const url=prompt('Enter a public HTTPS image URL or site-relative image path:');if(url && (/^https:\/\//i.test(url)||url.startsWith('/'))){const alt=prompt('Describe the image for accessibility:','')||'';run('insertHTML','<img src="'+url.replace(/&/g,'&amp;').replace(/"/g,'&quot;')+'" alt="'+alt.replace(/&/g,'&amp;').replace(/"/g,'&quot;')+'" loading="lazy">')}else if(url){alert('Use an HTTPS URL or a site-relative path.')}});
 toolbar.querySelector('[data-action="table"]').addEventListener('click',()=>{const rows=Math.min(10,Math.max(1,parseInt(prompt('Number of rows (1–10):','3'),10)||3));const cols=Math.min(6,Math.max(1,parseInt(prompt('Number of columns (1–6):','2'),10)||2));let html='<table><thead><tr>';for(let c=0;c<cols;c++)html+='<th scope="col">Heading</th>';html+='</tr></thead><tbody>';for(let r=1;r<rows;r++){html+='<tr>';for(let c=0;c<cols;c++)html+='<td>Cell</td>';html+='</tr>'}html+='</tbody></table><p></p>';run('insertHTML',html)});
 editor.addEventListener('input',sync);form.addEventListener('submit',sync);
 document.getElementById('guide-title').addEventListener('input',function(){const slug=document.getElementById('guide-slug');if(!slug.dataset.edited)slug.value=this.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'')});
 document.getElementById('guide-slug').addEventListener('input',function(){this.dataset.edited='1'});
 sync();
})();
</script>
