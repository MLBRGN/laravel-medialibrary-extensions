var e=class e{static isLiteralObject(e){return!!e&&e.constructor===Object}static get defaultConfig(){return{rootElement:document,fallbackTranslations:null,fallbackLanguage:`en`,detectLanguage:!0,persist:!1,persistKey:`active_language`,languagesSupported:[`en`,`nl`],languagesPath:null,exposeFnName:`__`,debug:!1}}errorMessages={INVALID_CONSTRUCTOR_ARGUMENTS:`Invalid constructor arguments`};#e;#t;#n;#r=new Map;constructor(t={}){if(console.log(`test`),console.log(`test4`),this.debug=(...e)=>{this.#e?.debug&&console.log(`[Translator]`,...e)},!e.isLiteralObject(t)){console.error(`[Translator]`,this.errorMessages.INVALID_CONSTRUCTOR_ARGUMENTS);return}this.#e=Object.assign({},e.defaultConfig,t),this.debug(`Initialized with config:`,this.#e),this.determineAndSetActiveLanguage(),this.#e.exposeFnName&&(window[this.#e.exposeFnName]=this._.bind(this),this.debug(`Exposed translate function as window.${this.#e.exposeFnName}`)),this.#a().catch(console.error)}setupMutationObserver(){let e=this.#e.rootElement;new MutationObserver(e=>{for(let t of e)this.#d(t.target)}).observe(e,{attributes:!0,childList:!0,subtree:!0})}determineAndSetActiveLanguage(){this.config.persist?this.activeLanguage=localStorage.getItem(this.#e.persistKey)??this.config.fallbackLanguage:this.config.detectLanguage?this.activeLanguage=this.#i()??this.config.fallbackLanguage:this.activeLanguage=this.config.fallbackLanguage,this.debug(`Active language =`,this.#t)}#i(){let e=navigator.languages?.[0]??navigator.language??null;return e?e.substring(0,2):!1}async#a(){let e=this.#s();await this.#o(e),this.#l(),this.setupMutationObserver()}async#o(e){if(this.#r.has(this.#t)){this.debug(`Loaded translations from cache`),this.#n=JSON.parse(this.#r.get(this.#t));return}await this.#c(e)}#s(){return this.#e.languagesPath&&null`${this.#e.languagesPath}${this.#t}.json`,null}async#c(e){if(!e){this.debug(`[Translator] invalid languageUrl ${e}, not loading translations`);return}try{let t=await fetch(e);t.ok||console.warn(`[Translator] Fetch failed: ${t.status} ${t.statusText} languageUrl: ${e}`),this.#n=await t.json(),this.#r.has(this.#t)||this.#r.set(this.#t,JSON.stringify(this.#n))}catch(e){console.error(`[Translator] Fetch error:`,e),this.#e.fallbackTranslations&&(console.warn(`[Translator] Using fallback translations`),this.#n=this.#e.fallbackTranslations)}}#l(){let e=this.#e.rootElement.querySelectorAll(`[data-i18n]`);for(let t of e)this.#u(t)}#u(e){let t=[];e.dataset.i18nAndAttr?t.push(`inNode`,e.dataset.i18nAndAttr):e.dataset.i18nAttr?t.push(e.dataset.i18nAttr):t.push(`inNode`);let n=e.hasAttribute(`data-i18n-html`),r=e.hasAttribute(`data-i18n-replacements`),i=null;if(r)try{i=JSON.parse(e.dataset.i18nReplacements)}catch{throw Error(`Error parsing replacements JSON: ${e.dataset.i18nReplacements}`)}let a=e.dataset.i18n,o=this._(a,i);if(o)for(let r of t)r===`inNode`?n?e.innerHTML=o:e.innerText=o:e.setAttribute(r,o)}#d(e){if(!e.querySelectorAll)return;let t=e.querySelectorAll(`[data-i18n]`);for(let e of t)this.#u(e)}get config(){return this.#e}get activeLanguage(){return this.#t}set activeLanguage(e){this.#e.languagesSupported.includes(e)||(this.debug(`[Translator] Unsupported language "${e}", using fallback "${this.config.fallbackLanguage}"`),e=this.#e.fallbackLanguage),this.#t=e,this.#e.persist&&localStorage.setItem(this.#e.persistKey,this.#t)}_(e,t=null){let n=this.#n?.[e]||e;return t&&t.forEach(e=>{let t=`%{${Object.keys(e)[0]}}`,r=Object.values(e)[0];n=n.replace(t,r)}),n}},t=class e{#e;#t;#n;#r;#i={};constructor(e,t,n,r,i){this.set(e,t,n,r,i)}pointIsInsideArea(e){return e.x>this.x&&e.x<this.x+this.w&&e.y>this.y&&e.y<this.y+this.h}scale(t){return new e(this.x*t,this.y*t,this.w*t,this.h*t,this.#i)}set(e,t,n,r,i){this.#e=e,this.#t=t,this.#n=n,this.#r=r,i&&typeof i==`object`&&(this.#i=i),this.#a()}get x(){return this.#e}get y(){return this.#t}get w(){return this.#n}get h(){return this.#r}get top(){return this.#t}get right(){return this.#e+this.#n}get bottom(){return this.#t+this.#r}get left(){return this.#e}get aspectRatio(){return this.#n/this.#r}getOption(e){return this.#i[e]}set x(e){this.#e=e}set y(e){this.#t=e}set w(e){this.#n=e,this.#a()}set h(e){this.#r=e,this.#a()}get xHalfway(){return this.x+(this.right-this.left)/2}get yHalfway(){return this.y+(this.bottom-this.top)/2}setOption(e,t){return this.#i[e]=t,t}#a(){this.w<0&&(this.w=Math.abs(this.w),this.x-=this.w),this.h<0&&(this.h=Math.abs(this.h),this.y-=this.h)}get cloned(){return new e(this.#e,this.#t,this.#n,this.#r,this.#i)}},n=e=>e.charAt(0).toUpperCase()+e.slice(1),r=e=>Number(e)===e&&e%1==0,i=e=>Number(e)===e&&e%1!=0,a=e=>e.replace(/[A-Z]+(?![a-z])|[A-Z]/g,(e,t)=>(t?`-`:``)+e.toLowerCase()),o=function(e,t,n=500){let r;return(...i)=>{clearTimeout(r),r=setTimeout(()=>{e.apply(t,i)},n)}},s=class{#e;#t;constructor(e=!1,t=``){this.#e=e,this.#t=t}enable(){this.#e=!0}disable(){this.#e=!1}log(...e){this.#e&&console.log(this.#t?`[${this.#t}]`:``,...e)}info(...e){this.#e&&console.info(this.#t?`[${this.#t}]`:``,...e)}warn(...e){this.#e&&console.warn(this.#t?`[${this.#t}]`:``,...e)}error(...e){this.#e&&console.error(this.#t?`[${this.#t}]`:``,...e)}},c=(e=!1,t=``)=>new s(e,t),l=class extends HTMLElement{elements={};#e=null;configuration={};#t={debug:!1};constructor(){if(super(),this.setConfiguration(this.#t),!new.target)throw TypeError(`invalid instantiation`);let t=this.#e=new.target;if(this.logger=c(this.debug,t.name),t.shadowTemplate){let n=t.shadowMode||{mode:`open`,delegatesFocus:!0};this.attachShadow(n).innerHTML=t.shadowTemplate,t.elementLookup?.forEach(e=>{let t=a(e),n=this.shadowRoot.querySelector(t);n||console.warn(`element with "${t}" not found`),this.elements[e.slice(1)]=n}),t.translationsPath&&(this.translator=new e({rootElement:this.shadowRoot,fallbackTranslations:{},persist:!0,debug:!1,languagesSupported:[`nl`,`en`],fallbackLanguage:`nl`,detectLanguage:!0,languagesPath:t.translationsPath}))}}attributeChangedCallback(e,t,n){}shadowTemplateAdded(){}getAttributeAsBoolean(e){return this.getBooleanAttributeOrDefault(e,!1)}getAttributeAsBooleanDefaultWhenFalse(e,t=null){return this.getBooleanAttributeOrDefault(e,t)}getBooleanAttributeOrDefault(e,t=!1){if(!this.hasAttribute(e))return t;let n=this.getAttribute(e);return n===``?!0:n.toLowerCase()===`false`?!1:n.toLowerCase()===`true`?!0:!!n}setAttributeAsBoolean(e,t){if(typeof t!=`boolean`)throw Error(`set ${e} must be set to a boolean value.`);this.toggleAttribute(e,t)}getAttributeAsInteger(e,t){return this.hasAttribute(e)?parseInt(this.getAttribute(e)):typeof t<`u`?t:null}setAttributeAsInteger(e,t){if(!r(t))throw Error(`set ${e} must be set to a number value.`);this.setAttribute(e,t)}getAttributeAsFloat(e,t){return this.hasAttribute(e)?parseFloat(this.getAttribute(e)):typeof t<`u`?t:null}setAttributeAsFloat(e,t){if(!i(t))throw Error(`set ${e} must be set to a number value.`);this.setAttribute(e,t)}getAttributeAsCSV(e,t){let n=this.hasAttribute(e)?this.getAttribute(e):null;return t=typeof t<`u`?t:null,n?n.replace(/\s/g,``).split(`,`):t}setAttributeAsCSV(e,t){if(!Array.isArray(t))throw Error(`set ${e} must called with an array as value.`);let n=t.join(`, `);this.setAttribute(e,n)}setAttributeToString(e,t){if(typeof t!=`string`)throw Error(`set ${e} must be set to a string value.`);this.setAttribute(e,t)}getAttributeOrDefault(e,t){return this.hasAttribute(e)?this.getAttribute(e):typeof t<`u`?t:null}setConfiguration(e){this.configuration=Object.assign(this.configuration,e)}getConfiguration(){return this.configuration}get config(){return this.configuration}get subClassName(){return this.#e.name}get debug(){return this.getAttributeAsBooleanDefaultWhenFalse(`debug`,this.configuration.debug)}set debug(e){this.setAttributeAsBoolean(`debug`,e)}},u=class extends l{static formAssociated=!0;formElement;#e=[`ElementInternals`,`FormDataEvent`,`Callback`];#t;internals;#n=null;#r=[];#i={required:!1};constructor(){super(),this.setConfiguration(this.#i),this.formElement=this.findContainingForm(),this.forceSubmitMode?this.setFormSubmitMode(this.forceSubmitMode):this.determineFormSubmitMode(),this.#t===`ElementInternals`&&(this.internals=this.attachInternals(),this.formElement=this.internals.form)}attributeChangedCallback(e,t,n){if(super.attributeChangedCallback(e,t,n),e=e.toLowerCase(),t!==n)switch(e){case`required`:let e=n;this.internals?this.internals.ariaRequired=e:this.setAttribute(`aria-required`,e);break;case`disabled`:let t=n;this.internals?this.internals.ariaDisabled=t:this.setAttribute(`aria-disabled`,t),this.enableControls(!t);break}}determineFormSubmitMode(){let e=`ElementInternals`in window&&`setFormValue`in window.ElementInternals.prototype,t=`FormDataEvent`in window;this.#o()&&e?this.setFormSubmitMode(`ElementInternals`):this.#o()&&t?this.setFormSubmitMode(`FormDataEvent`):this.setFormSubmitMode(`Callback`)}handleSubmitUsingFormDataEvent(){throw Error(`Must override handleSubmitUsingFormDataEvent for DormDataEvent submit method to work.`)}updateValue(){this.updateValidity(),this.#t===`ElementInternals`&&(this.internals.setFormValue(this.value),this.logger.log(`update ElementInternals value`))}updateValidity(){throw Error(`Must override updateValidity`)}connectedCallback(){this.#t===`FormDataEvent`&&this.formElement&&(this.formElement.addEventListener(`formdata`,this.handleSubmitUsingFormDataEvent),this.formElement.addEventListener(`submit`,this.formSubmitHandler,!1))}disconnectedCallback(){this.formElement&&=(this.#t===`FormDataEvent`&&(this.formElement.removeEventListener(`formdata`,this.handleSubmitUsingFormDataEvent),this.formElement.removeEventListener(`submit`,this.formSubmitHandler)),null)}findContainingForm(){let e=this.getRootNode();return Array.from(e.querySelectorAll(`form`)).find(e=>e.contains(this))||null}formAssociatedCallback(e){this.formAssociated(e)}formDisabledCallback(e){this.#a(e)}formStateRestoreCallback(e,t){t===`restore`?this.formStateRestore(e):this.logger.log(`formStateRestoreCallback ignored. mode:`,t)}formResetCallback(){this.formReset()}formAssociated(e){}formStateRestore(e){this.value=e}#a(e){this.enableControls(!e)}formReset(){}formSubmitHandler=e=>{if(this.#t===`FormDataEvent`&&!this.#n){e.preventDefault(),this.logger.log(`formSubmitHandler message: `,this.#r[0]);let{hiddenFileUpload:t}=this.elements;t.focus(),t.setCustomValidity(this.translator._(this.#r[0])),t.reportValidity()}this.logger.log(`formSubmitHandler, FormDataEvent form submit detected`)};enableControls(e){throw Error(`enableControls must be overridden`)}#o(){return this.formElement!==null}setFormSubmitMode(e){if(!this.#e.includes(e))throw Error(`Not a valid submit mode ${e}. Use ${this.#e.join(`, `)}`);!this.#o()&&e!==`Callback`?(this.#t=`Callback`,console.warn(`Could not find containing form. Falling back to submit using callbacks.`)):(this.#t=e,this.logger.log(`Submit mode for ${this.subClassName}`,this.#t))}set validity(e){this.#n=e}get validity(){}set validityMessages(e){this.#r=e,this.#t===`ElementInternals`&&(this.#n?this.internals.setValidity({}):(this.internals.setValidity({customError:!0},this.translator._(this.#r[0]),this.formValidationAnchor),this.internals.reportValidity()))}get validityMessages(){}get forceSubmitMode(){return this.getAttributeOrDefault(`force-submit-mode`,null)}set forceSubmitMode(e){this.setAttributeToString(`force-submit-mode`,e),this.setFormSubmitMode(e)}get submitMode(){return this.#t}get type(){return this.getAttributeOrDefault(`type`,`input`)}set type(e){this.setAttributeToString(`type`,e)}get value(){return this.getAttributeOrDefault(`value`,``)}set value(e){this.setAttributeToString(`value`,String(e))}get name(){return this.getAttributeOrDefault(`name`,`name`)}set name(e){this.setAttributeToString(`name`,e)}get required(){return this.getAttributeAsBooleanDefaultWhenFalse(`required`,this.config.required)}set required(e){this.setAttributeAsBoolean(`required`,e)}get formValidationAnchor(){return this}set disabled(e){this.setAttributeAsBoolean(`disabled`,e)}get disabled(){}setConfiguration(e){super.setConfiguration(Object.assign(this.configuration,e)),this.config.disabled&&(this.disabled=!0)}},d=new class{#e;constructor(){this.#e=document.createElement(`div`)}register(e,t){this.#e.addEventListener(e,t)}remove(e,t){this.#e.removeEventListener(e,t)}fire(e,t={}){this.#e.dispatchEvent(new CustomEvent(e,{detail:t}))}},f=async e=>new Promise((t,n)=>{let r=new Image;r.onload=()=>{t(r)},r.onerror=e=>{n(r,e)},r.src=e}),p=e=>new Promise((t,n)=>{let r=new Image;r.onload=()=>{t({width:r.width,height:r.height})},r.onerror=e=>{n(e)},r.src=e.imageObjectURL}),m=async(e,t,n)=>{let r;try{let i=await fetch(e,{signal:n}),a=await i.blob(),o=i.headers.get(`content-type`);r=new File([a],t,{type:o})}catch(e){console.log(e)}return r},h=(e,t)=>{let n=document.createElement(`a`);n.href=e,n.download=t,n.click(),n.remove()},g=class{configuration={formatsRegex:/.png|.jpg|.jpeg|.webp/,forceAspectRatio:null,aspectRatioTolerance:.01,minWidth:100,maxWidth:3500,minHeight:100,maxHeight:3500,maxUploadFileSize:1e6,maxEditFileSize:5e6};#e=null;#t=null;#n=null;#r=null;#i=`pending`;#a=null;#o=null;#s=null;#c=null;#l=null;#u=null;#d=null;#f={valid:!0,rejectionMessages:[],validityMessages:[],rejected:!1};constructor(e={}){this.logger=c(!0,`ImageFile`),this.configuration=Object.assign({},this.configuration,e)}async load(e,t,n,r=`no_name`,i=()=>{}){this.#e=e,this.#n=t,this.#t=n,this.name=r,await this.#p(this.#n,this.#t,null,i)}loadDefer(e,t,n,r=`no_name`){this.#e=e,this.#n=t,this.#t=n,this.name=r}async loadDeferred(e,t=()=>{}){return this.#p(this.#n,this.#t,e,t)}async#p(e,t,n,r=()=>{}){this.#i=`loading`,d.fire(`onImageFileLoadStart`,{imageFile:this,intId:this.#e}),e?await this.#m(e,r):t?await this.#h(t,n,r):this.logger.log(`ImageFile #load must be called with either src or file`)}async#m(e,t=()=>{}){this.#n=e,this.#t=URL.createObjectURL(e),this.#g(e);try{this.#i=`loaded`,this.#r=URL.createObjectURL(this.#n),await this.#v()}catch(e){this.#i=`loadError`,this.logger.log(`#loadSrc: could not load image dimensions ${e}`)}this.#b(),t(this.#e,this),d.fire(`onImageFileLoadEnd`,{imageFile:this,intId:this.#e})}async#h(e,t,n=()=>{}){this.#i=`loading`,d.fire(`onImageFileLoadStart`,{imageFile:this,intId:this.#e}),this.#t=e;try{let e=await m(this.#t,this.name,t);this.#n=e,this.#g(e),this.#i=`loaded`,this.#r=URL.createObjectURL(this.#n);try{await this.#v()}catch(e){this.logger.log(`#loadSrc: could not load image dimensions: ${e}`,this.#i)}}catch(e){this.#i=`loadError`,this.logger.log(`#loadSrc: could not load src: ${e}`,this.#i)}this.#b(),n(this.#e,this),d.fire(`onImageFileLoadEnd`,{imageFile:this,intId:this.#e})}#g(e){this.#o=e.type,this.#s=this.#y(e.type),this.#c=e.size,this.#a=e.name}#_=e=>{this.#l=e.width,this.#u=e.height,this.#d=e.width/e.height};async#v(){try{let e=await p(this);this.#_(e)}catch(e){this.logger.log(`#getImageDimension: could not get image dimensions: ${e}`,this.#i)}}#y(e){return e.substring(e.lastIndexOf(`/`)+1)}#b(){let e=this.#f,{minWidth:t,minHeight:n,maxWidth:r,maxHeight:i}=this.configuration,{maxUploadFileSize:a,maxEditFileSize:o}=this.configuration,s=t=>{e.valid=!1,e.rejected=!0,e.validityMessages.push(t),e.rejectionMessages.push(t)},c=t=>{e.valid=!1,e.validityMessages.push(t)};if(this.#i===`loadError`){s(`Load error`);return}if(this.#l<t&&s(`Width too small`),this.#u<n&&s(`Height too small`),this.#l>r&&s(`Width too large`),this.#u>i&&s(`Height too large`),this.configuration.formatsRegex||console.log(`empty formatsRegex!`),this.configuration.formatsRegex.test(`.${this.#s}`)||s(`Wrong file format`),this.#c>o&&s(`Filesize too large`),this.#c>a&&c(`Filesize too large`),this.configuration.forceAspectRatio!==null){let e=this.configuration.forceAspectRatio,t=this.configuration.aspectRatioTolerance;(this.#d<e-t||this.#d>e+t)&&c(`Wrong aspect ratio`)}d.fire(`onImageFileValidated`,{imageFile:this,intId:this.#e})}destroy(){URL.revokeObjectURL(this.#r)}get file(){return this.#n}get validity(){return this.#f}get loadStatus(){return this.#i}get name(){return this.#a}set name(e){this.#a=e}get width(){return this.#l}get src(){return this.#t}get height(){return this.#u}get mimeType(){return this.#o}get imageObjectURL(){return this.#r}},_=class{#e;#t;#n;constructor(e,t,n){this.validate(e,t,n),this.#t=e,this.#n=t,this.#e=n}validate(e,t,n){if(!e||!t||!n)throw Error(`ImageSource(name, src, id), invalid parameters.`)}get id(){return this.#e}get name(){return this.#t}get src(){return this.#n}},v=e=>{for(;e?.firstChild;)e.removeChild(e.firstChild)},y=[{name:`free`,label:`Free`,value:-1,active:!0},{name:`16:10`,label:`16:10`,value:16/10,active:!0},{name:`16:9`,label:`16:9`,value:16/9,active:!0},{name:`5:3`,label:`5:3`,value:5/3,active:!0},{name:`4:3`,label:`4:3`,value:4/3,active:!0},{name:`3:2`,label:`3:2`,value:3/2,active:!0},{name:`2:1`,label:`2:1`,value:2,active:!0},{name:`10:16`,label:`10:16`,value:10/16,active:!0},{name:`9:16`,label:`9:16`,value:9/16,active:!0},{name:`3:5`,label:`3:5`,value:3/5,active:!0},{name:`3:4`,label:`3:4`,value:3/4,active:!0},{name:`2:3`,label:`2:3`,value:2/3,active:!0},{name:`1:2`,label:`1:2`,value:1/2,active:!0},{name:`1:1`,label:`1:1`,value:1,active:!0},{name:`locked`,label:`Locked`,value:null,active:!0}],b=[{name:`JPEG`,label:`JPEG`,value:`image/jpeg`},{name:`WebP`,label:`WebP`,value:`image/webp`},{name:`PNG`,label:`PNG`,value:`image/png`},{name:`GIF`,label:`GIF`,value:`image/gif`},{name:`BMP`,label:`BMP`,value:`image/bmp`}],x=class e{#e;#t;#n;#r;#i={};constructor(e,t,n,r,i){this.set(e,t,n,r,i)}pointIsInsideArea(e){return e.x>this.x&&e.x<this.x+this.w&&e.y>this.y&&e.y<this.y+this.h}scale(t){return new e(this.x*t,this.y*t,this.w*t,this.h*t,this.#i)}set(e,t,n,r,i){this.#e=e,this.#t=t,this.#n=n,this.#r=r,i&&typeof i==`object`&&(this.#i=i),this.#a()}get x(){return this.#e}get y(){return this.#t}get w(){return this.#n}get h(){return this.#r}get top(){return this.#t}get right(){return this.#e+this.#n}get bottom(){return this.#t+this.#r}get left(){return this.#e}get aspectRatio(){return this.#n/this.#r}getOption(e){return this.#i[e]}set x(e){this.#e=e}set y(e){this.#t=e}set w(e){this.#n=e,this.#a()}set h(e){this.#r=e,this.#a()}get xHalfway(){return this.x+(this.right-this.left)/2}get yHalfway(){return this.y+(this.bottom-this.top)/2}setOption(e,t){return this.#i[e]=t,t}#a(){this.w<0&&(this.w=Math.abs(this.w),this.x-=this.w),this.h<0&&(this.h=Math.abs(this.h),this.y-=this.h)}get cloned(){return new e(this.#e,this.#t,this.#n,this.#r,this.#i)}},S=class e{#e;#t;constructor(e,t){this.set(e,t)}scale(t){return new e(this.x*t,this.y*t)}get x(){return this.#e}get y(){return this.#t}set x(e){this.#e=e}set y(e){this.#t=e}set(e,t){this.#e=e,this.#t=t}},C=`

    :host {
        --component-bg-color:#F4F4F7;
        --gap-between-editor-and-menu: 1em;
        --editor-panel-bg-color: #666;
        
        --base-white-bg-color: #FFFFFF;
        --base-white-fg-color: var(--base-fg-color);
        --base-lightest-bg-color: rgb(252, 252, 252);
        --base-lightest-fg-color:rgba(60, 60, 67, 0.3);
        --base-lighter-bg-color: rgb(250, 250, 250);
        --base-lighter-fg-color:rgba(60, 60, 67, 0.5);
        --base-light-bg-color: rgb(243, 243, 243);
        --base-light-fg-color:rgba(60, 60, 67, 0.98);
        --base-bg-color: rgb(241, 241, 241);
        --base-fg-color: rgba(0, 0, 0, 0.98);
        --base-dark-bg-color: rgb(236, 236, 236);
        --base-dark-fg-color: rgb(0, 0, 0);
        --base-darker-bg-color: rgb(220, 220, 220);
        --base-darker-fg-color: rgb(0, 0, 0);
    
        --base-bg-color-glass: rgba(233, 233, 233, 0.4);
        --base-fg-color-glass: rgba(0, 0, 0);
        --base-disabled-bg-color: rgba(239, 239, 239, 0.1);
        --base-disabled-fg-color: var(--base-lighter-fg-color);
        --base-active-bg-color: var(--base-darker-bg-color);
        --base-active-fg-color: var(--base-darker-fg-color);
        --base-hover-bg-color: rgb(208, 208, 215);
        --base-hover-fg-color: rgb(0, 0, 0);
        --base-active-hover-bg-color: rgba(0, 0, 0, 0.5);
        --base-active-hover-fg-color: rgba(256, 256, 256, 1);
        --base-disabled-hover-bg-color: rgba(239, 239, 239, 0.1);
        --base-disabled-hover-fg-color: rgba(16, 16, 16, 0.3);
        --base-border-color: #D1D1D6;
        --base-border: 1px solid var(--base-border-color);
        --base-border-radius: 5px;
        --base-accent-color: var(--base-active-bg-color);
        --base-accent-color-lighter: var(--base-hover-bg-color);
        --base-font-size: 1em;
        --base-font-size-smaller: 0.8em;
        --base-icon-width: 1rem;
        --base-icon-height: 1rem;
        --base-padding:0.3rem;
       
        --base-light-hover-bg-color: rgb(255, 255, 255);
        --base-light-hover-fg-color: rgb(0, 0, 0);
        
        --control-bg-color: var(--base-dark-bg-color);
        --control-fg-color: var(--base-dark-fg-color);
        --control-light-bg-color: var(--base-light-bg-color);
        --control-light-fg-color: var(--base-light-fg-color);
        --control-disabled-bg-color: var(--base-disabled-bg-color);
        --control-disabled-fg-color: var(--base-disabled-fg-color);
        --control-active-bg-color: var(--base-active-bg-color);
        --control-active-fg-color: var(--base-active-fg-color);
        --control-hover-bg-color: var(--base-hover-bg-color);
        --control-hover-fg-color: var(--base-hover-fg-color);
        --control-active-hover-bg-color: var(--base-active-hover-bg-color);
        --control-active-hover-fg-color: var(--base-active-hover-fg-color);
        --control-border: var(--base-border);
        --control-border-radius: var(--base-border-radius);
        --control-flex-gap: .5em;
        --control-padding: 5px;
        --control-accent-color: var(--base-accent-color);
        --control-accent-alt-color: var(--base-accent-color-lighter);
        
        --field-bg-color: var(--base-white-bg-color);
        --field-fg-color: var(--base-white-fg-color);
        --field-disabled-bg-color: var(--base-disabled-bg-color);
        --field-disabled-fg-color: var(--base-disabled-fg-color);
        --field-hover-bg-color: var(--base-light-hover-bg-color);
        --field-hover-fg-color: var(--base-light-hover-fg-color);
        --field-border: var(--base-border);
        --field-border-radius: var(--base-border-radius);
        --field-padding: 0;/* non zero value causes controls to grow wrapper */
        --field-gap:.5em;

        --button-bg-color: var(--control-bg-color);
        --button-fg-color: var(--control-fg-color);
        --button-disabled-fg-color: var(--control-disabled-fg-color);
        --button-disabled-bg-color: var(--control-disabled-bg-color);
        --button-active-bg-color: var(--control-active-bg-color);
        --button-active-fg-color: var(--control-active-fg-color);
        --button-hover-bg-color: var(--control-hover-bg-color);
        --button-hover-fg-color: var(--control-hover-fg-color);
        --button-active-hover-bg-color: var(--control-active-hover-bg-color);
        --button-active-hover-fg-color: var(--control-active-hover-fg-color);
        --button-padding: var(--control-padding);
        --button-border-radius: var(--control-border-radius);
        --button-border: var(--control-border);
        --button-font-size: var(--base-font-size-smaller);

        --heading-color: var(--base-light-fg-color);

        --icon-width: var(--base-icon-width);
        --icon-height: var(--base-icon-height);
        --icon-bg-color: transparent;
        --icon-fg-color: var(--control-fg-color);

        --dialog-bg-color: var(--base-lighter-bg-color);
        --dialog-fg-color: var(--base-lighter-fg-color);

        --dialog-help-width: 50vw;
        --dialog-help-height: 50vh;

        --status-bg-color: var(--base-lighter-bg-color);

        display: block;
        width: 100%;
        height: 100%;
    }

    :host([icon-delete]) .tileIconDelete {
        display: inline-block;
    }

    :host([hidden]) {
        display: none;
    }

    *, *:before, *:after {
        box-sizing: border-box;
    }


    /* Headings */

    h1, h2, h3 {
        font-weight: 600;
        line-height: 1.2;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        letter-spacing: -0.015em;
    }

    /* Sizes — can be adjusted or made responsive */
    h1 {
        font-size: clamp(1.5em, 1.5vw, 2em);
        color: var(--heading-color);
    }

    h2 {
     font-size: 1em;
     font-weight: 500;
     color: var(--heading-color);
     display:inline-block;
    }

    h3 {
        font-size: 1em;
        color: var(--heading-color);
        font-weight: 500;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    
    .fieldset {
        padding: 0;
        margin: 0;
        border: 0;
    }

    .fieldset:disabled .label {
        color: var(--field-disabled-fg-color);
    }

    .fieldset:disabled .icon {
        color: var(--field-disabled-fg-color);
    }
    
    .fieldset-filter {
        margin-block:1rem;
    }

#free-rotation {
margin-block:1rem;
}
    .li {
        list-style: inside;
    }

    /* dialog */

    .dialog {
        background-color: var(--base-lighter-bg-color);
        border-radius: 10px;
        color: var(--base-lighter-fg-color);
        border-width: 0;
        padding: 0;
        overflow: hidden;
    }

    .dialog.asModal {
        position: fixed;
        inset: 0;
    }

    .dialog[open] {
        animation: slideUp 0.4s ease-out;
    }

    .dialog h1,
    .dialog h2 {
        margin-top: 0;
        margin-bottom: 5px;
        color: var(--base-lighter-fg-color);
    }

    .dialog .ul {
        padding: 0;
    }

    .dialog.backdrop::backdrop {
        background: #000a !important;
    }

    .dialog-inner {
        display: flex;
        flex-direction: column;
        padding: 0;
        width: 100%;
        height: 100%;
    }

    .dialog-header {
        display: flex;
        align-items: start;
        justify-content: end;
        flex: 0 0 auto;
        position: relative;
        width: 100%;
        padding: .5em 0.5em 0;
    }

    .dialog-body {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 0 1em 1em;
        min-height:0;
    }

    .dialog-body .paragraph {
        margin-top: 0;
        font-size: var(--base-font-size-smaller);
    }

    .dialog-help {
        width: var(--dialog-help-width);
        height: var(--dialog-help-height);
    }

    /* input */

    .input {
        background-color: var(--field-bg-color);
        color: var(--field-fg-color);
        border: var(--field-border);
        border-radius: var(--field-border-radius);
        padding: var(--field-padding);
        padding-left: 1em;
        padding-right: 1em;
    }
    
    .input:disabled {
        background-color: var(--base-disabled-hover-bg-color);
        color: var(--base-disabled-hover-fg-color);
        cursor: not-allowed;
    }

    .input:focus-visible,
    .input:not(:disabled):hover {
        background-color: var(--field-hover-bg-color);
        color: var(--field-hover-fg-color);
    }

    .input-checkbox {
        vertical-align: middle;
        margin-left:0;
    }

    /* input range styles */

    /*********** Baseline, reset styles ***********/

    .input-range {
        -webkit-appearance: none;
        appearance: none;
        background: transparent;
    }

    /* Removes default focus */
    .input-range:not(:disabled):hover,
    .input-range:focus {
        outline: none;
    }

    /******** Chrome, Safari, Opera and Edge Chromium styles ********/

    /* slider track */

    .input-range::-webkit-slider-runnable-track {
        background-color: var(--control-light-bg-color);
        border-radius: 0.5rem;
        height: 0.5rem;
    }

    .input-range:disabled::-webkit-slider-runnable-track {
        background-color: var(--control-disabled-bg-color);
    }
    
    
    .input-range:hover::-webkit-slider-runnable-track {
        background-color: var(--base-light-hover-bg-color);
    }

    /* slider thumb */

    .input-range::-webkit-slider-thumb {
        -webkit-appearance: none; /* Override default look */
        appearance: none;
        margin-top: -4px; /* Centers thumb on the track */
        background-color: var(--control-accent-color);
        border-radius: 0.5rem;
        height: 1rem;
        width: 1rem;
    }

    .input-range:disabled::-webkit-slider-thumb {
        background-color: var(--control-disabled-fg-color);
    }

    .input-range:focus::-webkit-slider-thumb {
        outline: 3px solid var(--control-accent-alt-color);
        outline-offset: 0.125rem;
    }

    /*********** Firefox styles ***********/

    /* slider track */

    .input-range::-moz-range-track {
        background-color: var(--control-bg-color);
        border-radius: 0.5rem;
        height: 0.5rem;
    }

    .input-range:disabled::-moz-range-track {
        background-color: var(--control-disabled-bg-color);
    }

    /* slider thumb */

    .input-range::-moz-range-thumb {
        background-color: var(--control-accent-color);
        border: none; /*Removes extra border that FF applies*/
        border-radius: 0.5rem;
        height: 1rem;
        width: 1rem;
    }

    .input-range:disabled::-moz-range-thumb {
        background-color: var(--control-disabled-fg-color);
    }

    .input-range:focus::-moz-range-thumb {
        outline: 3px solid var(--control-accent-alt-color);
        outline-offset: 0.125rem;
    }
    
    /* button */

    .button {
        background-color: var(--button-bg-color);
        color: var(--button-fg-color);
        border-radius: var(--button-border-radius);
        border: var(--button-border);
        padding: var(--button-padding);
        font-size: var(--button-font-size);
        transition: background-color 0.2s ease;
    }

    .button:disabled {
        background-color: var(--button-disabled-bg-color);
        color: var(--button-disabled-fg-color);
        cursor: not-allowed;
    }

    .button:focus-visible:not(:disabled),
    .button:not(:disabled):hover {
        background-color: var(--button-hover-bg-color);
        color: var(--button-hover-fg-color);
    }

    .button-icon {
        display: inline-block;
        padding: 5px;
        aspect-ratio:1;
    }

    .button-icon .icon {
        vertical-align: middle;
        color: inherit;
    }

    .button-icon-text {
        display: inline-block;
        margin-bottom: 5px;
    }

    .button-icon-text .icon {
        margin-right: 5px;
        vertical-align: middle;
    }

    .button-icon-close {
        padding: 0;
        margin: 0;
    }

    /* icon */

    .icon {
        background-color: var(--icon-bg-color);
        color: var(--icon-fg-color);
        display: inline-block;
        width: var(--icon-width);
        height: var(--icon-height);
        vertical-align: middle;
    }
    
    .icon-as-icon-button {
        display: inline-block;
        width: var(--icon-width);
        height: var(--icon-height);
        padding: var(--button-padding);
        border: var(--button-border);
        border-color: transparent;
        box-sizing: content-box;
    }

    /* label */

    .label {
        display:inline;
        font-size:var(--base-font-size);
    }

    .label-small {
        display:block;
        font-size:var(--base-font-size-smaller);
    }

    .label-checkbox {
        display: inline-block;
        padding-right: 10px;
        white-space: nowrap;
        margin-bottom:1em;
    }

    .label-checkbox .label-span {
        vertical-align: middle;
    }

    /* select */

    .select {
        background-color: var(--field-bg-color);
        color: var(--field-fg-color);
        border-radius: var(--field-border-radius);
        border: var(--field-border);
        padding: var(--field-padding);
        min-width:50px;
    }

    .select:disabled {
        background-color: var(--field-disabled-bg-color);
        color: var(--field-disabled-fg-color);
    }
    .select:focus-visible,
    .select:hover {
        background-color: var(--field-hover-bg-color);
        color: var(--field-hover-fg-color);
    }

    .is-hidden:not(:focus):not(:active) {
        clip: rect(0 0 0 0);
        clip-path: inset(50%);
        height: 1px;
        overflow: hidden;
        position: absolute;
        white-space: nowrap;
        width: 1px;
    }

    @media (prefers-reduced-motion) {
        .dialog[open] {
            animation: none;
        }
    }

    /* main */

    .main {
        display: flex;
        background-color: var(--component-bg-color);
        flex-direction: row;
        justify-content: stretch;
        align-items: stretch;
        gap: var(--gap-between-editor-and-menu);
        height: 100%;
        width: 100%;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    /* when no image loaded, canvases are not displayed */
    .canvas-image,
    .canvas-draw {
        display: none;
    }

    .canvas-image {
        image-rendering: pixelated; /* Most modern browsers */
        image-rendering: crisp-edges; /* Fallback */
    }

    /* when an image loaded, canvases are displayed */
    .canvases-image-loaded .canvas-image,
    .canvases-image-loaded .canvas-draw {
        display: block;
    }

    /* canvases */

    .editor-panel {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1 1 600px;
        background-color: var(--editor-panel-bg-color);
        overflow: hidden;
        aspect-ratio: 4/3;
        min-width: 0;
        min-height: 0;
        width: 100%;
        height: 100%;
    }

    .canvas-image {
        background-color: #cdcdcd;
        background-image: repeating-linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white), repeating-linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white);
        background-position: 0 0, 10px 10px;
        background-size: calc(2 * 10px) calc(2 * 10px);
    }

    .canvas-draw {
        z-index: 1;
        position: absolute;
        left: 0;
        top: 0;
    }

    /* wrappers */

    .wrapper-canvases {
        position: relative;
    }

    .wrapper-field {
        display: flex;
        align-items: center;
        max-width: 300px;
        /*white-space: nowrap;*/
        margin-bottom: 1em;
        gap: var(--field-gap);
    }

    .wrapper-field-composed {
        display: flex;
        max-width: 300px;
        align-items: center;
        justify-content: start;
        font-size: var(--base-font-size-smaller);
        gap: 0.4em;
        flex-wrap: wrap;
    }
   

    .section-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .section-group > section {
        flex: 1 1 45%;
        min-width: 200px;
    }
    
    .wrapper-resize,
    .wrapper-file-format-select,
    .wrapper-aspect-ratio-select {
        margin-bottom: 5px;
        flex-wrap: nowrap;
    }
    
    .wrapper-glass {
        background-color: var(--base-bg-color-glass);
        color: var(--base-fg-color-glass);
        backdrop-filter: blur(9.5px);
        -webkit-backdrop-filter: blur(9.5px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--base-border-radius);
        padding: var(--base-padding);
        backdrop-filter: url(#glass-distortion);
        overflow:hidden;
    }
    .wrapper-glass.ee {
        filter: url(#glass-distortion);
    }
    

    /* zoom buttons and zoom level */

    .canvases-buttons {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: end;
        justify-content: center;
        gap: var(--control-flex-gap);
        top: 1em;
        right: 1em;
        z-index: 2;
    }

    .canvases-zoom-buttons {
        display: flex;
        flex-direction: column;
        gap: var(--control-flex-gap);
        align-items: end;
        justify-content: center;
    }

    .file-properties-wrapper,
    .file-size-wrapper {
        display: flex;
        gap:0.5rem;
        align-items: center;
        justify-content: start;
    }
    
    /* file type */

    .fields-composed-label {
        min-width: 0;
        flex: 1 1;
    }

    .fields-composed-field {
        flex: 1 0;
    }

    .fields-composed-label {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        flex: 1 1;
    }

    .fields-composed-uom {
        flex: 0 1;
        white-space: nowrap;
    }

    .fields-composed-button {
        flex: 0 1;
    }

    /* */
    .legend {
        min-width: 0;
    }

    .value-display {
        margin-left: 0.5em;
    }

    .fieldset:disabled .uom,
    .fieldset:disabled .heading,
    .fieldset:disabled .value-display {
        color: var(--field-disabled-fg-color);
    }

    /* buttons */

    button {
        user-select: none;
    }

    .button-selection-lock {
        background-color: var(--button-bg-color);
        color: var(--button-fg-color);
        fill: pink;
        margin-left: 5px;
        padding: 0 5px;
    }

    .button-selection-lock .icon-locked {
        display: none;
    }

    .button-selection-lock.locked .icon-unlocked {
        display: none;
    }

    .button-selection-lock.locked .icon-locked {
        display: initial;
    }

    .button-label {
        display: none;
        vertical-align:middle;
    }

    .button.is-active:not(:disabled),
    .button-icon.is-active:not(:disabled) {
        background-color: var(--button-active-bg-color); /* or your primary */
        color: var(--button-active-fg-color);
        border-color: transparent;
        box-shadow: 1px 1px 1px 0 #ccc;
    }

    .button-icon.is-active:not(:disabled):hover {
        background-color: var(--button-active-hover-bg-color); /* or your primary */
        color: var(--button-active-hover-fg-color);
    }

    /* select */

    .select-file-type,
    .select-aspect-ratios {
        padding: 0;
    }

    /* menu */

    .editor-menu {
        padding: 0 1em 1em;
        flex: 1 1 300px;
        min-width: 0;
        min-height: 0;
        overflow-y: auto;
        user-select: none;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        gap: 1rem;
        /*max-width: 420px; !* adjust based on your preference *!*/
    }

    /* range */

    input[type=range] {
        padding-inline: 0;
    }

    .input-range {
        min-width: 0;
        flex: 4 1;
    }

    .input-number {
        min-width: 50px;
    }

    /* containers */

    .container {
        width:fit-content;
        background-color: var(--base-light-bg-color);
        color: var(--base-light-fg-color);
        padding: var(--base-padding);
        border-radius: var(--base-border-radius);
    }

    .container-buttons .button-icon-text {
        margin-bottom:0;
    }
    
    .wrapper-form-text {
        font-size: var(--base-font-size-smaller);
        display: block;
        padding: 1em 0;
    }

    .container-filter {
        background-color: var(--base-bg-color);
        color: var(--base-fg-color);
        transition: background-color .2s ease-in-out, border-color .2s ease-in-out, box-shadow .2s ease-in-out;
    }

    .container-filter.is-active {
        background-color: var(--base-active-bg-color);
        color: var(--base-active-fg-color);
    }

    .container-info {
        /*display: none;*/
        flex-direction: column;
        align-items: start;
        /*font-size: var(--base-font-size);*/
        font-size: 0.9rem;
        flex: 1 1;
        gap: .4em;
        flex-wrap: wrap;
        margin-bottom: .4em;
        width: fit-content;
    }
    
    .container-filters {
        display: none;
        grid-template-columns: 1fr;
        gap: 1em;
    }

    .container-filters.show {
        display: grid;
    }

    .filters-menu {
        margin-bottom:1rem;
    }
    
    .filters-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filters-grid .container-filter {
        flex: 1 1 calc(50% - 1rem);
        min-width: 250px;
    }

    @media (min-width: 768px) {
        .filters-grid .container-filter {
            flex: 1 1 calc(33.333% - 1rem);
        }
    }
    
    @media (min-width: 1200px) {
        .filters-grid .container-filter {
            flex: 1 1 calc(25% - 1rem);
        }
    }

    .custom-details {
        margin-block:1rem;
        overflow: hidden; 
        overflow: hidden; /* Keep this line to prevent an odd blue outline around the element in Safari. */
        margin-bottom:0;
    }

    .custom-details summary {
        cursor: pointer;
        font-size: var(--button-font-size);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        user-select: none;
        transition: background-color 0.3s ease;
        background-color: var(--button-bg-color);
        color: var(--button-fg-color);
        border-radius: var(--button-border-radius);
        border: var(--button-border);
        padding: var(--button-padding);
    }
    
    .custom-details summary::-webkit-details-marker {
        display: none;
    }
    
    /* Hide the default triangle */
    .custom-details summary::marker {
        display: none;
    }
    
    .custom-details summary:hover {
        background-color: #e0e0e0;
    }
    
    .custom-details .icon {
        transition: transform 0.3s ease;
        display: inline-block;
    }

    .custom-details[open] .filters-expand,
    .custom-details:not([open]) .filters-collapse {
        display: none;
    }

    .custom-details[open] .filters-collapse,
    .custom-details:not([open]) .filters-expand {
        display: initial;
    }
    
    .custom-details[open] .icon-filters-toggle {
        transform: rotate(-90deg);
    }

    .custom-details:not([open]) .icon-filters-toggle {
        transform: rotate(90deg);
    }
    
    .custom-details + .details-content {
            box-sizing: border-box;
            max-height: 0;
            overflow: hidden;
            padding: 0 10px;
            transition: max-height 400ms ease-out, border 0ms 400ms linear;
            background-color: var(--base-lighter-bg-color);
            border: 1px solid transparent;
            color: var(--base-light-fg-color);
      }
      
    details[open] + .details-content {
            max-height: 800px; /* Set a max-height value enough to show all the content */
            border: var(--base-border);
            transition: max-height 400ms ease-out, border 0ms linear;
    }
    
    .filters {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .filter-menu {
        background-color: var(--base-darker-bg-color);
        color: var(--base-darker-fg-color);
        margin:0;
        display:flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.3rem 0.5rem;
    }
    
    .filter-menu label {
        margin:0;
    }
    
    .filter-modifiers {
        padding: .5rem;
    }

    /* svg filters */
    .svgFilter {
        width: 0;
        height: 0;
        position: absolute;
    }

    .image-orientation-icon .landscape-or-portrait {
        display:none;
    }
    
    .image-orientation-icon .square {
        display:none;
    }
    
    .image-orientation-icon-square .square {
        display:initial;
    }
    
    .image-orientation-icon-landscape {
        rotate: 90deg;
    }

    .image-orientation-icon-landscape .landscape-or-portrait {
        display:initial;
    }

    .image-orientation-icon-portrait .landscape-or-portrait {
        display:initial;
    }

    .display-inline-block {
        display: inline-block;
        margin-left: 1em;
    }
    
    #ee {
        cursor: no-drop;
    }
    
    .wrapper-glass.ee #ee {
        cursor: pointer;
    }
    
    .version-text {
        opacity:0.5;
        font-size:0.5rem;
        padding: .5rem;
    }

    @media only screen and (max-width: 576px) {
        .main {
            flex-direction: column;
        }

        .editor-panel {
            flex: 1 2 auto;
        }

        .editor-menu {
            flex: 1 1 auto;
            padding: 1em;
        }
    }

    @media only screen and (max-width: 768px) {
        .main {
            gap: 0;
        }

        .editor-panel {
            flex: 1 1 auto;
        }

        .editor-menu {
            flex: 1 1 180px;
            overflow-y: auto;
            padding-top: 1em;
        }

        .heading {
            display: none;
        }

    }

    @media only screen and (min-width: 768px) {
        .show-button-labels .button-label {
            display: initial;
        }

    }
`,w={crop:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-crop" viewBox="0 0 16 16">' +
        <path d="M3.5.5A.5.5 0 0 1 4 1v13h13a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2H3.5a.5.5 0 0 1-.5-.5V4H1a.5.5 0 0 1 0-1h2V1a.5.5 0 0 1 .5-.5zm2.5 3a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4H6.5a.5.5 0 0 1-.5-.5z"/>
    </svg>`,download:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
    </svg>`,arrowClockwise:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
    </svg>`,arrowCounterclockwise:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
        <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
    </svg>`,arrowDownUp:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/>
    </svg>`,arrowLeftRight:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
    </svg>`,grid3x3:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid-3x3" viewBox="0 0 16 16">
        <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v13a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13zM1.5 1a.5.5 0 0 0-.5.5V5h4V1H1.5zM5 6H1v4h4V6zm1 4h4V6H6v4zm-1 1H1v3.5a.5.5 0 0 0 .5.5H5v-4zm1 0v4h4v-4H6zm5 0v4h3.5a.5.5 0 0 0 .5-.5V11h-4zm0-1h4V6h-4v4zm0-5h4V1.5a.5.5 0 0 0-.5-.5H11v4zm-1 0V1H6v4h4z"/>
    </svg>`,check:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
    </svg>`,xLg:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
    </svg>`,x:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
    </svg>`,eraser:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16">
        <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414l-3.879-3.879zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/>
    </svg>`,aspectRatio:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-aspect-ratio" viewBox="0 0 16 16">
        <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5v-9zM1.5 3a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z"/>
        <path d="M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H3v2.5a.5.5 0 0 1-1 0v-3zm12 7a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H13V8.5a.5.5 0 0 1 1 0v3z"/>
    </svg>`,questionCircle:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
    </svg>`,plus:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
    </svg>`,dash:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
        <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
        </svg>`,lock:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
        </svg>`,unlock:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock" viewBox="0 0 16 16">
        <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2zM3 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1H3z"/>
        </svg>`,file:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file" viewBox="0 0 16 16">
        <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
        </svg>`,arrowRepeat:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
        </svg>`,boundingBox:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bounding-box" viewBox="0 0 16 16">
        <path d="M5 2V0H0v5h2v6H0v5h5v-2h6v2h5v-5h-2V5h2V0h-5v2H5zm6 1v2h2v6h-2v2H5v-2H3V5h2V3h6zm1-2h3v3h-3V1zm3 11v3h-3v-3h3zM4 15H1v-3h3v3zM1 4V1h3v3H1z"/>
        </svg>`,arrowsAngleExpand:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-angle-expand" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707m4.344-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707"/>
    </svg>`,arrowsAngleContract:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-angle-contract" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M.172 15.828a.5.5 0 0 0 .707 0l4.096-4.096V14.5a.5.5 0 1 0 1 0v-3.975a.5.5 0 0 0-.5-.5H1.5a.5.5 0 0 0 0 1h2.768L.172 15.121a.5.5 0 0 0 0 .707M15.828.172a.5.5 0 0 0-.707 0l-4.096 4.096V1.5a.5.5 0 1 0-1 0v3.975a.5.5 0 0 0 .5.5H14.5a.5.5 0 0 0 0-1h-2.768L15.828.879a.5.5 0 0 0 0-.707"/>
    </svg>`,arrows:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows" viewBox="0 0 16 16">
        <path d="M1.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L2.707 7.5h10.586l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L13.293 8.5H2.707l1.147 1.146a.5.5 0 0 1-.708.708z"/>
    </svg>`,arrowsVertical:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-vertical" viewBox="0 0 16 16">
        <path d="M8.354 14.854a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 13.293V2.707L6.354 3.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 2.707v10.586l1.146-1.147a.5.5 0 0 1 .708.708z"/>
    </svg>`,arrowRightShort:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8"/>
    </svg>`,fileEarmark:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
    </svg>`,textareaResize:`
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-textarea-resize" viewBox="0 0 16 16">
        <path d="M0 4.5A2.5 2.5 0 0 1 2.5 2h11A2.5 2.5 0 0 1 16 4.5v7a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 0 11.5zM2.5 3A1.5 1.5 0 0 0 1 4.5v7A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 13.5 3zm10.854 4.646a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708l3-3a.5.5 0 0 1 .708 0m0 2.5a.5.5 0 0 1 0 .708l-.5.5a.5.5 0 0 1-.708-.708l.5-.5a.5.5 0 0 1 .708 0"/>
    </svg>`,square:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-square" viewBox="0 0 16 16">
        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
    </svg>`,boxSeam:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
    </svg>`,checkbox:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-square" viewBox="0 0 16 16">
        <path d="M3 14.5A1.5 1.5 0 0 1 1.5 13V3A1.5 1.5 0 0 1 3 1.5h8a.5.5 0 0 1 0 1H3a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h10a.5.5 0 0 0 .5-.5V8a.5.5 0 0 1 1 0v5a1.5 1.5 0 0 1-1.5 1.5z"/>
        <path d="m8.354 10.354 7-7a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0"/>
    </svg>`};function T(e=[]){let t=Array.from(e??[]),n={};return t.forEach(e=>{let t=ee(e.getAttribute(`data-modifier`));t&&(n[t]=e.value)}),n}function ee(e){return e.replace(/-([a-z])/g,(e,t)=>t.toUpperCase())}function E(e){return e.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i,(e,t,n,r)=>`#`+t+t+n+n+r+r).substring(1).match(/.{2}/g).map(e=>parseInt(e,16))}function te(e,t){let n=e.querySelector(`feFuncR`),r=e.querySelector(`feFuncG`),i=e.querySelector(`feFuncB`),a=T(t),[o,s,c]=E(a.darkColor),[l,u,d]=E(a.lightColor);return n.setAttribute(`tableValues`,`${o/255} ${l/255}`),r.setAttribute(`tableValues`,`${s/255} ${u/255}`),i.setAttribute(`tableValues`,`${c/255} ${d/255}`),`duotone-effect`}var ne=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="duotone-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="duotone" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span">Duotone</span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label for="dark-color" data-i18n="Replace darker colors with:">Replace darker colors with:</label>
            <input id="dark-color" type="color" class="input input-color" data-modifier="dark-color" value="#004F67" data-default="#004F67">
            <label for="light-color" data-i18n="Replace lighter colors with:">Replace lighter colors with:</label>
            <input id="light-color" type="color" class="input input-color" data-modifier="light-color" value="#FFDC33" data-default="#FFDC33">
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="duotone-effect" color-interpolation-filters="sRGB">
                    <feColorMatrix type="matrix" values=".33 .33 .33 0 0
                          .33 .33 .33 0 0
                          .33 .33 .33 0 0
                          0 0 0 1 0">
                    </feColorMatrix>
                    <feComponentTransfer>
                        <feFuncR type="table" tableValues=".996078431 .984313725"></feFuncR>
                        <feFuncG type="table" tableValues=".125490196 .941176471"></feFuncG>
                        <feFuncB type="table" tableValues=".552941176 .478431373"></feFuncB>
                    </feComponentTransfer>
                </filter>
            </defs>
        </svg>
    </span>
    
`;function re(e,t=[],n){let r=T(t),i=r.inkblotScale||50,a=r.inkblotFrequency||`0.01`;console.log(i,a);let o=e.querySelector(`feTurbulence`),s=e.querySelector(`feDisplacementMap`);return o&&o.setAttribute(`baseFrequency`,a),s&&s.setAttribute(`scale`,i),`inkblot-effect`}var ie=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="inkblot-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="ink-blot" type="checkbox" id="duo-tone" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Inkblot"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label data-i18n="Inkblot scale">Inkblot Scale:</label>
            <input type="range" class="input input-range" min="0" max="100" value="50" data-modifier="inkblot-scale" data-default="50">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="inkblot-scale" min="0" max="100" step="1" value="50" data-default="50">
                <label data-i18n="val"></label>
            </span>
            <label data-i18n="Inkblot frequency">Inkblot Frequency:</label>
            <input type="range" class="input input-range" min="0.01" max="0.1" step="0.01" value="0.01" data-modifier="inkblot-frequency" data-default="0.01">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="inkblot-frequency" min="0.01" max="0.1" step="0.01" value="0.01" data-default="0.01">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="inkblot-effect">
                    <feTurbulence type="turbulence" baseFrequency="0.05" numOctaves="2" result="turbulence"></feTurbulence>
                    <feDisplacementMap in2="turbulence" in="SourceGraphic" scale="20" xChannelSelector="R" yChannelSelector="G"></feDisplacementMap>
                </filter>
            </defs>
        </svg>
    </span>
`;function ae(e,t=[],n){let r=targetModifier.getAttribute(`data-modifier`),i=targetModifier.value;return t.forEach(e=>{e.getAttribute(`data-modifier`)===r&&e!==targetModifier&&(e.value=i)}),r===`gamma-exponent`&&e.querySelectorAll(`[exponent]`).forEach(e=>e.setAttribute(`exponent`,i)),r===`gamma-amplitude`&&e.querySelectorAll(`[amplitude]`).forEach(e=>e.setAttribute(`amplitude`,i)),`gamma-effect`}var oe=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="gamma-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="gamma" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Gamma"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="exponent" data-i18n="Exponent">
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
            <input type="range" id="exponent" class="input input-range" data-modifier="gamma-exponent"
            min="0" max="3" step="0.01" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" id="exponent-value" class="input input-number number-value"
                data-modifier="gamma-exponent" min="0" max="3" step="0.01" value="1" data-default="1">
                <label for="exponent-value" data-i18n="exp"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="amplitude" data-i18n="Amplitude"></label>
            <input type="range" id="amplitude" class="input input-range" data-modifier="gamma-amplitude"
            min="0" max="3" step="0.01" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" id="amplitude-value" class="input input-number number-value"
                data-modifier="gamma-amplitude" min="0" max="3" step="0.01" value="1" data-default="1">
                <label for="amplitude-value" data-i18n="amp"></label>
            </span>
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="gamma-effect">
                    <feComponentTransfer>
                        <feFuncR type="gamma" exponent="1" amplitude="1" offset="0"></feFuncR>
                        <feFuncG type="gamma" exponent="1" amplitude="1" offset="0"></feFuncG>
                        <feFuncB type="gamma" exponent="1" amplitude="1" offset="0"></feFuncB>
                    </feComponentTransfer>
                </filter>
            </defs>
        </svg>
    </span>
`;function se(e,t=[],n){let r=T(t),i=r.staticNoiseFrequency||`0.2`,a=r.staticNoiseK2||`1`,o=r.staticNoiseK3||`0.05`,s=e.querySelector(`feTurbulence`),c=e.querySelector(`feComposite`);return s&&s.setAttribute(`baseFrequency`,i),c&&(c.setAttribute(`k2`,a),c.setAttribute(`k3`,o)),`static-noise-effect`}var ce=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="static-noise-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="static-noise" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Static noise"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <label class="label label-small" for="noise-frequency" data-i18n="Base frequency"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Base frequency" class="is-hidden"></legend>
            <input type="range" id="noise-frequency" class="input input-range" data-modifier="static-noise-frequency" min="0.01" max="0.5" step="0.01" value="0.2" data-default="0.2">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="static-noise-frequency" min="0.01" max="0.5" step="0.01" value="0.2" data-default="0.2">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="k2-value" data-i18n="Intensity blend"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Intensity blend" class="is-hidden"></legend>
            <input type="range" id="k2-value" class="input input-range" data-modifier="static-noise-k2" min="0" max="2" step="0.01" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="static-noise-k2" min="0" max="2" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="k3-value" data-i18n="Noise blend"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Noise blend" class="is-hidden"></legend>
            <input type="range" id="k3-value" class="input input-range" data-modifier="static-noise-k3" min="0" max="2" step="0.05" value="0.1" data-default="0.05">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="static-noise-k3" min="0" max="2" step="0.05" value="0.1" data-default="0.05">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="static-noise-effect">
                    <feTurbulence type="fractalNoise" baseFrequency="0.8" numOctaves="1" result="noise"></feTurbulence>
                    <feComposite in="SourceGraphic" in2="noise" operator="arithmetic" k2="1" k3="1"></feComposite>
                </filter>
            </defs>
        </svg>
    </span>
`;function le(e,t=[],n){let r=T(t),i=r.watercolorFrequency||`0.05`,a=r.watercolorScale||`30`,o=r.watercolorSaturation||`1`,s=e.querySelector(`feTurbulence`),c=e.querySelector(`feDisplacementMap`),l=e.querySelector(`feColorMatrix`);return s&&s.setAttribute(`baseFrequency`,i),c&&c.setAttribute(`scale`,a),l&&l.setAttribute(`values`,o),`watercolor-effect`}var ue=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="watercolor-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="water-color" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Watercolor"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <label class="label label-small" for="watercolor-frequency" data-i18n="Base frequency"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Base frequency" class="is-hidden"></legend>
            <input type="range" id="watercolor-frequency" class="input input-range" data-modifier="watercolor-frequency" min="0.001" max="0.1" step="0.001" value="0.05" data-default="0.05">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="watercolor-frequency" min="0.001" max="0.1" step="0.001" value="0.05" data-default="0.05">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="watercolor-scale" data-i18n="Displacement scale"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Displacement scale" class="is-hidden"></legend>
            <input type="range" id="watercolor-scale" class="input input-range" data-modifier="watercolor-scale" min="0" max="100" step="1" value="30" data-default="30">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="watercolor-scale" min="0" max="100" step="1" value="30" data-default="30">
                <label data-i18n="px"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="watercolor-saturation" data-i18n="Saturation"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Saturation" class="is-hidden"></legend>
            <input type="range" id="watercolor-saturation" class="input input-range" data-modifier="watercolor-saturation" min="0" max="1" step="0.01" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="watercolor-saturation" min="0" max="1" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="watercolor-effect">
                    <feTurbulence type="fractalNoise" baseFrequency="0.02" numOctaves="4" result="noise"></feTurbulence>
                    <feDisplacementMap in="SourceGraphic" in2="noise" scale="20"></feDisplacementMap>
                    <feColorMatrix type="saturate" values="0.3"></feColorMatrix>
                </filter>
            </defs>
        </svg>
    </span>
`;function de(e,t=[],n){let r=T(t),i=r.rippleFrequency||`0.02`,a=r.rippleScale||`10`,o=e.querySelector(`feTurbulence`),s=e.querySelector(`feDisplacementMap`);return o&&o.setAttribute(`baseFrequency`,i),s&&s.setAttribute(`scale`,a),`ripple-effect`}var D=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="ripple-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="ripple" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Ripple"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <label class="label label-small" for="ripple-frequency" data-i18n="Frequency"></label>
        <input type="range" id="ripple-frequency" class="input input-range" data-modifier="ripple-frequency" min="0.001" max="0.1" step="0.001" value="0.02" data-default="0.02">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="ripple-frequency" min="0.001" max="0.1" step="0.001" value="0.02" data-default="0.02">
            <label data-i18n="val"></label>
        </span>
        <label class="label label-small" for="ripple-scale" data-i18n="Scale"></label>
        <input type="range" id="ripple-scale" class="input input-range" data-modifier="ripple-scale" min="0" max="50" step="1" value="10" data-default="10">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="ripple-scale" min="0" max="50" step="0.1" value="10" data-default="10">
            <label data-i18n="val"></label>
        </span>
        <svg class="svgFilter">
            <defs>
                <filter id="ripple-effect" color-interpolation-filters="sRGB">
                    <feTurbulence type="turbulence" baseFrequency="0.02" numOctaves="2" result="rippleNoise"></feTurbulence>
                    <feDisplacementMap in="SourceGraphic" in2="rippleNoise" scale="10" xChannelSelector="R" yChannelSelector="B"></feDisplacementMap>
                </filter>
            </defs>
        </svg>
    </span>
`;function fe(e,t=[],n){let r=T(t).glitchShift||`5`;return e.querySelectorAll(`feOffset`).forEach((e,t)=>e.setAttribute(t===0?`dx`:`dy`,r)),`glitch-effect`}var pe=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="glitch-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="glitch" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Glitch"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <label class="label label-small" for="glitch-shift" data-i18n="Shift amount"></label>
        <input type="range" id="glitch-shift" class="input input-range" data-modifier="glitch-shift" min="1" max="20" value="5" data-default="5">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="glitch-shift" min="1" max="20" step="0.1" value="5" data-default="5">
            <label data-i18n="val"></label>
        </span>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="glitch-effect">
                    <feColorMatrix type="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 1 0" result="original"></feColorMatrix>
                    <feOffset in="original" dx="5" result="r1"></feOffset>
                    <feOffset in="original" dy="5" result="r2"></feOffset>
                    <feBlend in="r1" in2="r2" mode="difference"></feBlend>
                </filter>
            </defs>
        </svg>
    </span>
`;function me(e,t=[],n){let r=T(t),i=r.glowBlur||`3`,a=r.glowOpacity||`0.4`,o=e.querySelector(`feGaussianBlur`),s=e.querySelector(`feComponentTransfer feFuncA`);return o&&o.setAttribute(`stdDeviation`,i),s&&s.setAttribute(`slope`,a),`soft-glow-effect`}var he=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="soft-glow-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="soft-glow" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Soft glow"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <label class="label label-small" for="glow-blur" data-i18n="Blur"></label>
        <input type="range" id="glow-blur" class="input input-range" data-modifier="glow-blur" min="0" max="20" step="1" value="3" data-default="3">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="glow-blur" min="0" max="28" step="1" value="3" data-default="3">
            <label data-i18n="val"></label>
        </span>
        <label class="label label-small" for="glow-opacity" data-i18n="Opacity"></label>
        <input type="range" id="glow-opacity" class="input input-range" data-modifier="glow-opacity" min="0" max="1" step="0.01" value="0.4" data-default="0.4">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="glow-opacity" min="0" max="1" step="0.01" value="0.4" data-default="0.4">
            <label data-i18n="val"></label>
        </span>
        <svg class="svgFilter">
            <defs>
                <filter id="soft-glow-effect" x="-50%" y="-50%" width="200%" height="200%" color-interpolation-filters="sRGB">
                    <!-- Blur the source -->
                    <feGaussianBlur in="SourceGraphic" stdDeviation="3" result="blur"></feGaussianBlur>
            
                    <!-- Adjust alpha of the blur -->
                    <feComponentTransfer in="blur" result="faded-blur">
                        <feFuncA type="linear" slope="0.4"></feFuncA>
                    </feComponentTransfer>
            
                    <!-- Merge faded glow with original -->
                    <feMerge>
                        <feMergeNode in="faded-blur"></feMergeNode>
                        <feMergeNode in="SourceGraphic"></feMergeNode>
                    </feMerge>
                </filter>
            </defs>
        </svg>
    </span>
`;function ge(e,t=[],n){let r=T(t),i=Number(r.sketchThreshold)||1,a=e.querySelector(`feConvolveMatrix`);if(a){a.setAttribute(`order`,3);let e=[-i,-i,-i,-i,8*i,-i,-i,-i,-i];a.setAttribute(`kernelMatrix`,e.join(` `)),a.setAttribute(`divisor`,1)}return`sketch-effect`}var _e=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="sketch-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="sketch" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Sketch"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="sketch-threshold" data-i18n="Edge threshold"></label>
            <input type="range" id="sketch-threshold" class="input input-range" data-modifier="sketch-threshold" min="1" max="5" step="1" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="sketch-threshold" min="1" max="5" step="1" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="sketch-effect" color-interpolation-filters="sRGB">
                    <!-- Convert image to grayscale -->
                    <feColorMatrix in="SourceGraphic" type="matrix"
                        values="0.33 0.34 0.33 0 0
                              0.33 0.34 0.33 0 0
                              0.33 0.34 0.33 0 0
                              0    0    0    1 0"
                        result="gray"></feColorMatrix>
                    
                    <!-- Apply edge detection -->
                    <feConvolveMatrix in="gray" order="3" kernelMatrix="-1 -1 -1 -1 8 -1 -1 -1 -1" divisor="1" result="edges"></feConvolveMatrix>
                    
                    <!-- Invert edges to white background -->
                    <feComponentTransfer in="edges" result="sketch">
                        <feFuncR type="table" tableValues="1 0"></feFuncR>
                        <feFuncG type="table" tableValues="1 0"></feFuncG>
                        <feFuncB type="table" tableValues="1 0"></feFuncB>
                    </feComponentTransfer>
                    
                    <!-- Output result -->
                    <feBlend in="sketch" in2="SourceGraphic" mode="multiply"></feBlend>
                </filter>
            </defs>
        </svg>
    </span>
`;function ve(e,t=[],n){let r=T(t),i=Number(r.pixelateX)||4,a=Number(r.colorLevels)||5,o=e.querySelector(`svg.svgFilter`);if(!o)return`pixelate-effect`;let s=o.querySelector(`feFlood`),c=o.querySelector(`feMorphology[operator="erode"]`),l=o.querySelector(`feMorphology[result="raster2"]`),u=o.querySelector(`feGaussianBlur`),d=o.querySelector(`feMorphology[operator="dilate"]`),f=o.querySelectorAll(`feComponentTransfer :not(feFuncA)`);if(!s||!c||!l||!u||!d||f.length===0)return`pixelate-effect`;let p=1+i*4;s.setAttribute(`width`,p),s.setAttribute(`height`,p),c.setAttribute(`width`,p),c.setAttribute(`height`,p),c.setAttribute(`radius`,i),l.setAttribute(`radius`,i),d.setAttribute(`radius`,i*2),u.setAttribute(`stdDeviation`,Math.max(0,i-.5));let m=Math.max(1,a),h=[...Array(m+1)].map((e,t)=>(t/m).toFixed(2)).join(` `);return f.forEach(e=>e.setAttribute(`tableValues`,h)),`pixelate-effect`}var ye=`
<span class="container container-filter"
      data-filter
      data-filter-type="svg"
      data-filter-name="pixelate-effect"
      data-requires-checkbox="true">

    <div class="filter-menu">
        <label class="label label-checkbox">
            <input id="pixelate" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
            <span class="label-span" data-i18n="Pixelate"></span>
        </label>
        <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
            ${w.arrowRepeat}
        </span>
    </div>

    <fieldset class="fieldset fieldset-filter wrapper-field-composed">
        <label class="label label-small" for="pixelate-x" data-i18n="Pixel size"></label>
        <input type="range" id="pixelate-x" class="input input-range" data-modifier="pixelateX" min="1" max="20" step="1" value="4" data-default="4">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="pixelateX" min="1" max="20" step="1" value="4" data-default="4">
            <label data-i18n="val"></label>
        </span>
    </fieldset>

    <fieldset class="fieldset fieldset-filter wrapper-field-composed">
        <label class="label label-small" for="color-levels" data-i18n="Color levels"></label>
        <input type="range" id="color-levels" class="input input-range" data-modifier="colorLevels" min="1" max="4096" step="1" value="5" data-default="64">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="colorLevels" min="1" max="4096" step="1" value="5" data-default="64">
            <label data-i18n="val"></label>
        </span>
    </fieldset>

    <svg class="svgFilter" color-interpolation-filters="sRGB">
        <defs>
            <filter id="pixelate-effect" color-interpolation-filters="sRGB" x="0" y="0" width="100%" height="100%">
                <feFlood x="0" y="0" width="9" height="9" />
                <feMorphology operator="erode" radius="2" x="0" y="0" width="9" height="9" />
                <feTile result="raster1" />
                <feMorphology operator="erode" radius="2" result="raster2" />
                <feComposite operator="in" in="SourceGraphic" in2="raster1" />
                <feGaussianBlur stdDeviation="1.5" />
                <feComposite operator="in" in2="raster2" />
                <feComponentTransfer>
                    <feFuncR type="discrete" tableValues="0 .2 .4 .6 .8 1" />
                    <feFuncG type="discrete" tableValues="0 .2 .4 .6 .8 1" />
                    <feFuncB type="discrete" tableValues="0 .2 .4 .6 .8 1" />
                    <feFuncA type="discrete" tableValues="0 1" />
                </feComponentTransfer>
                <feMorphology operator="dilate" radius="4" />
            </filter>
        </defs>
    </svg>
</span>
`;function be(e,t=[],n){let r=targetModifier.getAttribute(`data-modifier`),i=targetModifier.value,a=(t,n,r)=>{let i=e.querySelector(t);i&&i.setAttribute(n,r)},[o,s,c]=r.split(`-`);return a(`feFunc${s.toUpperCase()}`,c,i),`frosted-glass-effect`}var O=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="gamma-advanced-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label">
                <input id="gamma-advanced" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Gamma (RGB)"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <!-- R Channel -->
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Red">Red</legend>
            <div>
                <label class="label label-small" data-i18n="Exponent">Exponent</label>
                <input type="range" class="input input-range" data-modifier="gamma-r-exponent" min="0.1" max="3" step="0.01" value="1" data-default="1">
            </div>
            <div>
                <label class="label label-small" data-i18n="Amplitude">Amplitude</label>
                <input type="range" class="input input-range" data-modifier="gamma-r-amplitude" min="0" max="3" step="0.01" value="1" data-default="1">
            </div>
            <div>
                <label class="label label-small" data-i18n="Offset">Offset</label>
                <input type="range" class="input input-range" data-modifier="gamma-r-offset" min="-1" max="1" step="0.01" value="0" data-default="0">
            </div>
        </fieldset>
        
          <!-- G Channel -->
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Green">Green</legend>
            <div>
                <label class="label label-small" data-i18n="Exponent">Exponent</label>
                <input type="range" class="input input-range" data-modifier="gamma-g-exponent" min="0.1" max="3" step="0.01" value="1" data-default="1">
            </div>
            <div>
                <label class="label label-small" data-i18n="Amplitude">Amplitude</label>
                <input type="range" class="input input-range" data-modifier="gamma-g-amplitude" min="0" max="3" step="0.01" value="1" data-default="1">
            </div>
            <div>
                <label class="label label-small" data-i18n="Offset">Offset</label>
                <input type="range" class="input input-range" data-modifier="gamma-g-offset" min="-1" max="1" step="0.01" value="0" data-default="0">
            </div>
        </fieldset>
        
          <!-- B Channel -->
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Blue">Blue</legend>
            <div>
                <label class="label label-small" data-i18n="Exponent">Exponent</label>
                <input type="range" class="input input-range" data-modifier="gamma-b-exponent" min="0.1" max="3" step="0.01" value="1" data-default="1">
            </div>
            <div>
                <label class="label label-small" data-i18n="Amplitude">Amplitude</label>
                <input type="range" class="input input-range" data-modifier="gamma-b-amplitude" min="0" max="3" step="0.01" value="1" data-default="1">
            </div>
            <div>
                <label class="label label-small" data-i18n="Offset">Offset</label>
                <input type="range" class="input input-range" data-modifier="gamma-b-offset" min="-1" max="1" step="0.01" value="0" data-default="0">
            </div>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="gamma-advanced-effect" color-interpolation-filters="sRGB">
                    <feComponentTransfer>
                        <feFuncR type="gamma" exponent="1" amplitude="1" offset="0"></feFuncR>
                        <feFuncG type="gamma" exponent="1" amplitude="1" offset="0"></feFuncG>
                        <feFuncB type="gamma" exponent="1" amplitude="1" offset="0"></feFuncB>
                    </feComponentTransfer>
                </filter>
            </defs>
        </svg>
    </span>
`,k={width:null,height:null},A={};function j(e){A=e}function M(){return A}function N(e,t=[],n,r){console.log(`filterConfiguration`,M());let i=r.getImageEditorInstance(),a=i.canvasImageWidth,o=i.canvasImageHeight,s=a/2,c=o/2;P(e,`vignette-light-x`,a,s),P(e,`vignette-light-y`,o,c);let l=T(t),u=l.vignetteShape||`vignette-circle`,d=l.vignetteOpacity??.6,f=l.vignetteColor??`#000000`;if(console.log(a,o),u===`vignette-square`){let t=e.querySelector(`#vignette-square-offset`);t&&(t.setAttribute(`dx`,l.vignetteLightX-a/2),t.setAttribute(`dy`,l.vignetteLightY-o/2)),l.vignetteSquareBlur;let n=l.vignetteSquareRadius??100,r=e.querySelector(`#vignette-square-erode`),i=e.querySelector(`#vignette-square-blur`),s=e.querySelector(`#vignette-square-flood`);s&&(s.setAttribute(`flood-opacity`,d),s.setAttribute(`flood-color`,f)),s&&s.setAttribute(`flood-opacity`,d),r&&r.setAttribute(`radius`,n),i&&i.setAttribute(`stdDeviation`,l.vignetteLightZ)}else{let t=l.vignetteLightX??s,n=l.vignetteLightY??c,r=l.vignetteLightZ??800,i=e.querySelector(`#vignette-flood`),a=e.querySelector(`#vignette-pointlight`);i&&(i.setAttribute(`flood-opacity`,d),i.setAttribute(`flood-color`,f)),a&&(a.setAttribute(`x`,t),a.setAttribute(`y`,n),a.setAttribute(`z`,r))}return u}function P(e,t,n,r){console.log(e,t,n,r);let i=e.querySelector(`#${t}`);if(console.log(`input`,i),!i)return;i.max=n,console.log(`set max to `+n);let a=parseFloat(i.value),o=a===parseFloat(i.dataset.default);i.dataset.default=r,(isNaN(a)||o)&&(i.value=r)}var xe=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="vignette-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="vignette" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Vignette"></span>
            <label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
          <label class="label label-small" for="vignette-color" data-i18n="Color"></label>
          <input
            type="color"
            id="vignette-color"
            class="input input-color"
            data-modifier="vignette-color"
            value="#000000"
            data-default="#000000"
          >
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
             <label class="label label-small" for="morph-operator" data-i18n="Mode">Mode</label>
            <select id="vignette-shape" class="select" data-modifier="vignette-shape" data-default="vignette-circle">
                <option value="vignette-circle" data-i18n="Circle">Circle</option>
                <option value="vignette-square" data-i18n="Square">Square</option>
            </select>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="vignette-opacity" data-i18n="Opacity"></label>
            <input type="range" id="vignette-opacity" class="input input-range" data-modifier="vignette-opacity" min="0" max="1"
                   step="0.01" value="0.6" data-default="0.6">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="vignette-opacity" min="0" max="1" step="0.01" value="0.6" data-default="0.6">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="vignette-light-x" data-i18n="Light X"></label>
            <input type="range" id="vignette-light-x" class="input input-range" data-modifier="vignette-light-x" min="0"
                   max="1000" step="1" value="256" data-default="256">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="vignette-light-x" min="0" max="1000" step="10" value="256" data-default="256">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="vignette-light-y" data-i18n="Light Y"></label>
            <input type="range" id="vignette-light-y" class="input input-range" data-modifier="vignette-light-y" min="0"
                   max="1000" step="1" value="170" data-default="170">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="vignette-light-y" min="0" max="1000" step="10" value="170" data-default="170">
                <label data-i18n="val"></label>
            </span></fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="vignette-light-z" data-i18n="Light Z"></label>
            <input type="range" id="vignette-light-z" class="input input-range" data-modifier="vignette-light-z" min="0"
                   max="2000" step="10" value="170" data-default="170">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="vignette-light-z" min="0" max="2000" step="10" value="170" data-default="170">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="vignette-square-radius" data-i18n="Square Radius"></label>
            <input type="range" id="vignette-square-radius" class="input input-range" data-modifier="vignette-square-radius"
                   min="0" max="300" step="1" value="100" data-default="100">
        </fieldset>
        
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="vignette-square-blur" data-i18n="Square Blur"></label>
            <input type="range" id="vignette-square-blur" class="input input-range" data-modifier="vignette-square-blur"
                   min="0" max="200" step="1" value="50" data-default="50">
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="vignette-circle">
                    <feFlood id="vignette-circle-flood" flood-color="#000000" flood-opacity="1" result="circle-flood" />
                    <feFlood id="vignette-flood" x="0" y="0" result="circle-flood" flood-color="#000000" flood-opacity="1"></feFlood>
                    <feSpecularLighting id="vignette-specular" result="spotlight" lighting-color="#FFFFFF" surfaceScale="1" specularConstant="1" specularExponent="120">
                        <fePointLight id="vignette-pointlight" x="256" y="170" z="800"></fePointLight>
                    </feSpecularLighting>
                    <feBlend id="vignette-mask" result="mask" in="circle-flood" in2="spotlight" mode="lighten"></feBlend>
                    <feBlend id="vignette-mask-multiply" in="mask" in2="SourceGraphic" mode="multiply"
                             result="vignette-result"></feBlend>
                </filter>
                <filter id="vignette-square" x="0" y="0" width="100%" height="100%">
                   <feFlood id="vignette-flood" flood-color="#000000" flood-opacity="1" result="square-flood" />
                    <feOffset id="vignette-square-offset" in="SourceAlpha" dx="0" dy="0" result="offsetted" />
                    <feMorphology id="vignette-square-erode" operator="erode" radius="100" in="offsetted" result="eroded" />
                    <feGaussianBlur id="vignette-square-blur" in="eroded" stdDeviation="50" result="blurred" />
                    <feComponentTransfer in="blurred" result="inverted">
                      <feFuncA type="table" tableValues="1 0" />
                    </feComponentTransfer>
                    <feComposite in="square-flood" in2="inverted" operator="in" result="vignette" />
                    <feBlend in="SourceGraphic" in2="vignette" mode="multiply" />
                  </filter>
            </defs>
        </svg>
    </span>
`;function Se(e,t=[],n){let r=T(t),i=Number(r.morphRadius)||1.5,a=r.morphOperator||`dilate`,o=e.querySelector(`feMorphology`);return o&&o.setAttribute(`radius`,i),o.setAttribute(`operator`,a),`morph-effect`}var Ce=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="morph-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="morph" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Morph"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="morph-radius" data-i18n="Radius"></label>
            <input type="range" id="morph-radius" class="input input-range" data-modifier="morph-radius" min="0.1" max="50" step="0.1" value="1.5" data-default="1.5">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="morph-radius" min="0.1" max="50" step="0.1" value="1.5" data-default="1.5">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="morph-operator" data-i18n="Mode">Mode</label>
        <select id="morph-operator" class="select" data-modifier="morph-operator" data-default="dilate">
            <option value="dilate" data-i18n="Dilate">Dilate</option>
            <option value="erode" data-i18n="Erode">Erode</option>
        </select>
        <svg class="svgFilter">
            <defs>
                <filter id="morph-effect" x="-10%" y="-10%" width="120%" height="120%" color-interpolation-filters="sRGB">
                    <feMorphology operator="dilate" radius="10"></feMorphology>
                </filter>
            </defs>
        </svg>
    </span>
`;function we(e,t=[],n){let r=T(t),i=r.edgeDetectType||`edge-detect-1`;console.log(`edgeDetectType`,i);let a=Number(r.edgeStrength)||1;return e.querySelectorAll(`feConvolveMatrix`).forEach(e=>{if(e.hasAttribute(`data-not-modifiable`))return;let t=[0,1,0,1,-4,1,0,1,0].map(e=>e*a).join(` `);e.setAttribute(`kernelMatrix`,t),e.setAttribute(`order`,`3 3`),e.setAttribute(`divisor`,`1`),e.setAttribute(`bias`,`0`),e.setAttribute(`preserveAlpha`,`true`)}),i}var Te=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="edge-detect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="edge-detect" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Edge detect"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
          <label class="label label-small" for="edge-detect-type" data-i18n="Edge detect type"></label>
            <select id="edge-detect-type" class="input input-select" data-modifier="edge-detect-type" data-default="edge-detect-1">
                <option value="edge-detect-1" data-i18n="Edge detect"></option>
                <option value="sobel" data-i18n="Sobel"></option>
            </select>
            <label class="label label-small" for="edge-strength" data-i18n="Edge strength"></label>
            <input type="range" id="edge-strength" class="input input-range" data-modifier="edge-strength" min="1" max="5" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="edge-strength" min="1" max="5" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="edge-detect-1" x="-10%" y="-10%" width="120%" height="120%">
                  <feConvolveMatrix 
                    kernelMatrix="0 1 0 1 -4 1 0 1 0" 
                    order="3 3" 
                    bias="0" 
                    divisor="1" 
                    preserveAlpha="true"
                  ></feConvolveMatrix>
                </filter>
                <filter id="sobel" x="0%" y="0%" width="100%" height="100%">
                    <!-- convert source image to luminance map-->
                    <feColorMatrix in="SourceGraphic" type="matrix" values="0 0 0 0 1 
                    0 0 0 0 1 
                    0 0 0 0 1 
                    1 0 0 0 0" result="RChan" />
                
                    <feColorMatrix in="SourceGraphic" type="matrix" values="0 0 0 0 1 
                    0 0 0 0 1 
                    0 0 0 0 1 
                    0 1 0 0 0" result="GChan" />
                
                    <feColorMatrix in="SourceGraphic" type="matrix" values="0 0 0 0 1 
                    0 0 0 0 1 
                    0 0 0 0 1 
                    0 0 1 0 0" result="BChan" />
                
                    <!-- sobel edge detection-->
                    <feConvolveMatrix in="RChan" order="3" 
                    kernelMatrix="-1 -2 -1  
                    0 0 0  
                    1 2 1 "
                    result="Rhor" data-not-modifiable />
                    
                    <feConvolveMatrix in="RChan" order="3" kernelMatrix="-1 0 1  
                    -2 0 2 
                    -1 0 1"  result="Rver" data-not-modifiable />
                    
                    <feComposite operator="arithmetic" k2="1" k3="1" in="Rhor" in2="Rver" />
                    <feColorMatrix type="matrix" values="0 0 0 1 0
                    0 0 0 0 0 
                    0 0 0 0 0 
                    0 0 0 0 1" result="rededge"/>
                    
                    <feConvolveMatrix in="GChan" order="3" kernelMatrix="-1 -2 -1  
                    0 0 0  
                    1 2 1"
                    result="Ghor" data-not-modifiable />
                    
                    <feConvolveMatrix in="GChan" order="3" kernelMatrix="-1 0 1 
                    -2 0 2 
                    -1 0 1"  result="Gver" data-not-modifiable  />
                    
                    <feComposite operator="arithmetic" k2="1" k3="1" in="Ghor" in2="Gver" />
                    <feColorMatrix type="matrix" values="0 0 0 0 0
                    0 0 0 1 0 
                    0 0 0 0 0 
                    0 0 0 0 1" result="greenedge"/>
                    
                    <feConvolveMatrix in="BChan" order="3" kernelMatrix="-1 -2 -1  
                    0 0 0  
                    1 2 1 " result="Bhor" data-not-modifiable />
                    
                    <feConvolveMatrix in="BChan" order="3" kernelMatrix="-1 0 1  
                    -2 0 2 
                    -1 0 1"  result="Bver" data-not-modifiable  />
                    
                    <feComposite operator="arithmetic" k2="1" k3="1" in="Bhor" in2="Bver"/>
                    <feColorMatrix type="matrix" values="0 0 0 0 0
                    0 0 0 0 0 
                    0 0 0 1 0 
                    0 0 0 0 1" result="blueedge"/>
                
                    <feComposite operator="arithmetic" in="blueedge" in2="rededge" k2="1" k3="1"/>
                    <feComposite operator="arithmetic" in2="greenedge" k2="1" k3="1" result="finaledges"/>
                    
                    <feFlood flood-color="black" result="black"/>
                    <feComposite operator="over" in="finaledges"/>
                </filter>
            </defs>
        </svg>
    </span>
`;function Ee(e){let t=parseInt(e,10)||4,n=[];for(let e=0;e<t;e++)n.push((e/(t-1)).toFixed(4));return n.join(` `)}function De(e,t=[],n){let r=T(t),i=Math.max(2,Math.min(8,parseInt(r.posterizeLevels)||4));console.log(i);let a=[`feFuncR`,`feFuncG`,`feFuncB`],o=Ee(i);return a.forEach(t=>{let n=e.querySelector(t);n&&(n.setAttribute(`type`,`table`),n.setAttribute(`tableValues`,o))}),`posterize-effect`}var Oe=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="posterize-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="posterize" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Posterize"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="posterize-levels" data-i18n="Levels"></label>
            <input type="range" id="posterize-levels" class="input input-range" data-modifier="posterize-levels" min="2" max="8" value="4" data-default="4">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="posterize-levels" min="2" max="8" step="1" value="4" data-default="4">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="posterize-effect" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
                    <feComponentTransfer>
                        <feFuncR type="table" tableValues="0 0.33 0.66 1"></feFuncR>
                        <feFuncG type="table" tableValues="0 0.33 0.66 1"></feFuncG>
                        <feFuncB type="table" tableValues="0 0.33 0.66 1"></feFuncB>
                    </feComponentTransfer>
                </filter>
            </defs>
        </svg>
    </span>
`;function ke(e,t=[],n){let r=T(t),i=Number(r.frostedGlassBlur)||6,a=Number(r.frostedGlassOpacity)||.9,o=e.querySelector(`feGaussianBlur`),s=e.querySelector(`feFuncA`);return o&&o.setAttribute(`stdDeviation`,i),s&&s.setAttribute(`slope`,a),`frosted-glass-effect`}var Ae=`
    <span class="container container-filter"
        data-filter
        data-filter-type="svg"
        data-filter-name="frosted-glass-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="frosted-glass" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Frosted Glass"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
        <label class="label label-small" for="frosted-glass-blur" data-i18n="Blur amount"></label>
            <input type="range" id="frosted-glass-blur" class="input input-range" data-modifier="frosted-glass-blur" min="0"
                   max="20" step="0.5" value="6" data-default="6">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="frosted-glass-blur" min="0" max="20" step="0.01" value="6" data-default="6">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="frosted-glass-opacity" data-i18n="Opacity"></label>
            <input type="range" id="frosted-glass-opacity" class="input input-range" data-modifier="frosted-glass-opacity"
                   min="0" max="1" step="0.05" value="0.9" data-default="0.9">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="frosted-glass-opacity" min="0" max="1" step="0.01" value="0.9" data-default="0.9">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="frosted-glass-effect" x="0%" y="0%" width="100%" height="100%">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="6" result="blur"></feGaussianBlur>
                    <feComponentTransfer>
                        <feFuncA type="linear" slope="0.5"></feFuncA>
                    </feComponentTransfer>
                </filter>
            </defs>
        </svg>
    </span>
`;function je(e,t=[],n){let r=T(t),i=Number(r[`glow-blur`])||4,a=e.querySelector(`feGaussianBlur`);return a&&a.setAttribute(`stdDeviation`,i),`glow-effect`}var Me=`
    <span class="container container-filter"
        data-filter
        data-filter-type="svg"
        data-filter-name="glow-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="glow" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Glow"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="glow-blur" data-i18n="Blur amount"></label>
            <input type="range" id="glow-blur" class="input input-range" data-modifier="glow-blur" min="0" max="20" step="0.5" value="4" data-default="4">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="glow-blur" min="0" max="20" step="0.5" value="4" data-default="4">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="glow-effect" x="-50%" y="-50%" width="200%" height="200%" color-interpolation-filters="sRGB">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="4" result="blur"></feGaussianBlur>
                    <feMerge>
                        <feMergeNode in="blur"></feMergeNode>
                        <feMergeNode in="SourceGraphic"></feMergeNode>
                    </feMerge>
                </filter>
            </defs>
        </svg>
    </span>
`;function Ne(e,t=[],n){let r=T(t),i=Number(r.intensityR)||1,a=Number(r.intensityG)||1,o=Number(r.intensityB)||1;console.log(i,a,o);let s=[i,0,0,0,0,0,a,0,0,0,0,0,o,0,0,0,0,0,1,0].join(` `),c=e.querySelector(`feColorMatrix`);return c&&c.setAttribute(`values`,s),`channel-manipulation-effect`}var Pe=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="channel-manipulation-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="channel-manipulation" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Color balance"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed channel-slider">
            <label class="label label-small" for="intensity-r" style=" color: cyan;">Cyan</label>
            <input type="range" id="intensity-r" class="input input-range" data-modifier="intensity-r" min="0.01" max="2" step="0.01" value="1" data-default="1">
            <label class="label label-small" for="intensity-r" style="color: red;">Red</label>
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="intensity-r" min="0.01" max="2" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed channel-slider">
            <label class="label label-small" for="intensity-g"style=" color: magenta;">Magenta</label>
            <input type="range" id="intensity-g" class="input input-range" data-modifier="intensity-g" min="0.01" max="2" step="0.01" value="1" data-default="1">
            <label class="label label-small" for="intensity-g" style="color: green;">Green</label>
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="intensity-g" min="0.01" max="2" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed channel-slider">
            <label class="label label-small" for="intensity-b" style="color: yellow;">Yellow</label>
            <input type="range" id="intensity-b" class="input input-range" data-modifier="intensity-b" min="0.01" max="2" step="0.01" value="1" data-default="1">
            <label class="label label-small" for="intensity-b" style="color: blue;">Blue</label>
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="intensity-b" min="0.01" max="2" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="channel-manipulation-effect" color-interpolation-filters="sRGB" x="0%" y="0%" width="100%" height="100%">
                    <feColorMatrix type="matrix" values="
                      1 0 0 0 0
                      0 1 0 0 0
                      0 0 1 0 0
                      0 0 0 1 0
                    " ></feColorMatrix>
                </filter>
            </defs>
        </svg>
    </span>
`;function Fe(e,t=[],n){let r=T(t),i=Number(r.redDx)||0,a=Number(r.redDy)||0,o=Number(r.greenDx)||0,s=Number(r.greenDy)||0,c=Number(r.blueDx)||0,l=Number(r.blueDy)||0,u=e.querySelector(`feOffset[inkscape\\:label="red-offset"]`),d=e.querySelector(`feOffset[inkscape\\:label="green-offset"]`),f=e.querySelector(`feOffset[inkscape\\:label="blue-offset"]`);return u&&(u.setAttribute(`dx`,i),u.setAttribute(`dy`,a)),d&&(d.setAttribute(`dx`,o),d.setAttribute(`dy`,s)),f&&(f.setAttribute(`dx`,c),f.setAttribute(`dy`,l)),`rgb-shift-effect`}var Ie=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="rgb-shift-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="rgb-shift" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="RGB shift"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <!-- Red channel -->
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="red-dx" data-i18n="Red DX"></label>
            <input type="range" id="red-dx" class="input input-range" data-modifier="red-dx" min="-20" max="20" step="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="red-dx" min="-20" max="20" step="0.01" value="0" data-default="0">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="red-dy" data-i18n="Red DY"></label>
            <input type="range" id="red-dy" class="input input-range" data-modifier="red-dy" min="-20" max="20" step="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="red-dy" min="-20" max="20" step="0.01" value="0" data-default="0">
                <label data-i18n="val"></label>
            </span>
        </fieldset>

        <!-- Green channel -->
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="green-dx" data-i18n="Green DX"></label>
            <input type="range" id="green-dx" class="input input-range" data-modifier="green-dx" min="-20" max="20" step="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="green-dx" min="-20" max="20" step="0.01" value="0" data-default="0">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="green-dy" data-i18n="Green DY"></label>
            <input type="range" id="green-dy" class="input input-range" data-modifier="green-dy" min="-20" max="20" step="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="green-dy" min="-20" max="20" step="0.01" value="0" data-default="0">
                <label data-i18n="val"></label>
            </span>
        </fieldset>

        <!-- Blue channel -->
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="blue-dx" data-i18n="Blue DX"></label>
            <input type="range" id="blue-dx" class="input input-range" data-modifier="blue-dx" min="-20" max="20" step="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="blue-dx" min="-20" max="20" step="0.01" value="0" data-default="0">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="blue-dy" data-i18n="Blue DY"></label>
            <input type="range" id="blue-dy" class="input input-range" data-modifier="blue-dy" min="-20" max="20" step="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="blue-dy" min="-20" max="20" step="0.01" value="0" data-default="0">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="rgb-shift-effect" x="-40%" y="-40%" width="180%" height="180%" color-interpolation-filters="sRGB">
                    <!-- Red channel -->
                    <feColorMatrix in="SourceGraphic" type="matrix" values="
                      1 0 0 0 0
                      0 0 0 0 0
                      0 0 0 0 0
                      0 0 0 1 0
                    " result="red"></feColorMatrix>
                    <feOffset inkscape:label="red-offset" in="red" dx="0" dy="0" result="redShift"></feOffset>
                    
                    <!-- Green channel -->
                    <feColorMatrix in="SourceGraphic" type="matrix" values="
                      0 0 0 0 0
                      0 1 0 0 0
                      0 0 0 0 0
                      0 0 0 1 0
                    " result="green"></feColorMatrix>
                    <feOffset inkscape:label="green-offset" in="green" dx="0" dy="0" result="greenShift"></feOffset>
                
                    <!-- Blue channel -->
                    <feColorMatrix in="SourceGraphic" type="matrix" values="
                      0 0 0 0 0
                      0 0 0 0 0
                      0 0 1 0 0
                      0 0 0 1 0
                    " result="blue"></feColorMatrix>
                    <feOffset inkscape:label="blue-offset" in="blue" dx="0" dy="0" result="blueShift"></feOffset>
                
                    <!-- Combine shifted channels -->
                    <feBlend in="redShift" in2="greenShift" mode="screen" result="rgBlend"></feBlend>
                    <feBlend in="rgBlend" in2="blueShift" mode="screen"></feBlend>
                </filter>
            </defs>
        </svg>
    </span>
`;function Le(e,t=[],n){let r=T(t),i=Number(r.embossIntensity)||1,a=e.querySelector(`feConvolveMatrix`);if(a){let e=[-2*i,-1*i,0,-1*i,1,1*i,0,1*i,2*i].join(` `);a.setAttribute(`kernelMatrix`,e)}return`emboss-effect`}var Re=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="svg"
        data-filter-name="emboss-effect"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="emboss" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Emboss"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="emboss-intensity" data-i18n="Intensity"></label>
            <input type="range" id="emboss-intensity" class="input input-range" data-modifier="emboss-intensity" min="0.1" max="3" step="0.1" value="1" data-default="1">
           <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="emboss-intensity" min="0.1" max="3" step="0.01" value="1" data-default="1">
                <label data-i18n="val"></label>
            </span>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                <filter id="emboss-effect" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
                    <feConvolveMatrix in="SourceGraphic" order="3" kernelMatrix="
                      -1 -1 0
                      -1 2 1
                       0 1 1" result="embossed"></feConvolveMatrix>
                    <feComposite in="embossed" in2="SourceGraphic" operator="arithmetic" k1="0" k2="0.5" k3="0.5" k4="0"></feComposite>
                </filter>
            </defs>
        </svg>
    </span>
`;function ze(e,t=[],n){let r=T(t),i=r.blurType||`gaussian-blur`,a=parseFloat(r.blurX)||3,o=parseFloat(r.blurY)||3;return e.querySelectorAll(`feGaussianBlur`).forEach(e=>{e.hasAttribute(`data-not-modifiable`)||e.setAttribute(`stdDeviation`,`${a} ${o}`)}),i}var Be=`
       <span class="container container-filter" 
        data-filter
        data-filter-type="svg" 
        data-filter-name="blur-effect" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="blur" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Blur"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>  
        <div class="filter-modifiers">
            <label class="label label-small" for="blur-type" data-i18n="Blur type"></label>
            <select id="blur-type" class="input input-select" data-modifier="blur-type" data-default="gaussian-blur">
                <option value="gaussian-blur" data-i18n="Blur"></option>
                <option value="motion-blur" data-i18n="Motion blur"></option>
                <option value="circle-blur" data-i18n="Circle blur"></option>
                <option value="cross-blur" data-i18n="Cross blur"></option>
<!--                <option value="average-blur" data-i18n="Average blur"></option>-->
            </select>
            <label class="label label-small" for="blur-x" data-i18n="Horizontal blur"></label>
            <input type="range" id="blur-x" class="input input-range" data-modifier="blur-x" min="1" max="20" step="0.1" value="3" data-default="3">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="blur-x" min="1" max="20" step="0.1" value="3" data-default="3">
                <label data-i18n="val"></label>
            </span>
            <label class="label label-small" for="blur-y" data-i18n="Vertical blur"></label>
            <input type="range" id="blur-y" class="input input-range" data-modifier="blur-y" min="1" max="20" step="0.1" value="3" data-default="3">
            <span class="wrapper">
                <input type="number" class="input input-number number-value" data-modifier="blur-y" min="1" max="20" step="0.1" value="3" data-default="3">
                <label data-i18n="val"></label>
            </span>
        </div>
        <svg class="svgFilter" color-interpolation-filters="sRGB">
            <defs>
                <filter id="gaussian-blur" x="0%" y="0%" width="100%" height="100%">
                      <feGaussianBlur in="SourceGraphic" stdDeviation="20 20" result="blurred" />
                      <feComponentTransfer in="blurred" result="opaqueBlurred">
                            <feFuncA type="discrete" tableValues="1" />
                      </feComponentTransfer>
                </filter>
                 <filter id="motion-blur">
                    <feGaussianBlur in="SourceGraphic" stdDeviation="10,0" data-not-modifiable/>
                </filter>
                <filter id="circle-blur" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
                    <feOffset dx="14" dy="0" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o0" result="cT0">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="14" dy="3" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o1" result="cT1">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="13" dy="6" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o2" result="cT2">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="11" dy="8" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o3" result="cT3">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="9" dy="10" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o4" result="cT4">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="7" dy="12" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o5" result="cT5">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="4" dy="13" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o6" result="cT6">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="1" dy="14" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o7" result="cT7">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-1" dy="14" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o8" result="cT8">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-4" dy="13" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o9" result="cT9">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-7" dy="12" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o10" result="cT10">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-9" dy="10" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o11" result="cT11">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-11" dy="8" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o12" result="cT12">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-13" dy="6" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o13" result="cT13">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-14" dy="3" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o14" result="cT14">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-14" dy="0" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o15" result="cT15">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-14" dy="-3" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o16" result="cT16">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-13" dy="-6" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o17" result="cT17">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-11" dy="-8" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o18" result="cT18">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-9" dy="-10" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o19" result="cT19">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-7" dy="-12" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o20" result="cT20">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-4" dy="-13" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o21" result="cT21">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="-1" dy="-14" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o22" result="cT22">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="1" dy="-14" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o23" result="cT23">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="4" dy="-13" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o24" result="cT24">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="7" dy="-12" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o25" result="cT25">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="9" dy="-10" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o26" result="cT26">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="11" dy="-8" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o27" result="cT27">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="13" dy="-6" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o28" result="cT28">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feOffset dx="14" dy="-3" in="SourceGraphic" result="o{id}"></feOffset>
                    <feComponentTransfer in="o29" result="cT29">
                        <feFuncA type="table" tableValues="0 0.06666666666666667"></feFuncA>
                    </feComponentTransfer>
                    <feMerge result="merge">
                        <feMergeNode in="cT0"></feMergeNode>
                        <feMergeNode in="cT1"></feMergeNode>
                        <feMergeNode in="cT2"></feMergeNode>
                        <feMergeNode in="cT3"></feMergeNode>
                        <feMergeNode in="cT4"></feMergeNode>
                        <feMergeNode in="cT5"></feMergeNode>
                        <feMergeNode in="cT6"></feMergeNode>
                        <feMergeNode in="cT7"></feMergeNode>
                        <feMergeNode in="cT8"></feMergeNode>
                        <feMergeNode in="cT9"></feMergeNode>
                        <feMergeNode in="cT10"></feMergeNode>
                        <feMergeNode in="cT11"></feMergeNode>
                        <feMergeNode in="cT12"></feMergeNode>
                        <feMergeNode in="cT13"></feMergeNode>
                        <feMergeNode in="cT14"></feMergeNode>
                        <feMergeNode in="cT15"></feMergeNode>
                        <feMergeNode in="cT16"></feMergeNode>
                        <feMergeNode in="cT17"></feMergeNode>
                        <feMergeNode in="cT18"></feMergeNode>
                        <feMergeNode in="cT19"></feMergeNode>
                        <feMergeNode in="cT20"></feMergeNode>
                        <feMergeNode in="cT21"></feMergeNode>
                        <feMergeNode in="cT22"></feMergeNode>
                        <feMergeNode in="cT23"></feMergeNode>
                        <feMergeNode in="cT24"></feMergeNode>
                        <feMergeNode in="cT25"></feMergeNode>
                        <feMergeNode in="cT26"></feMergeNode>
                        <feMergeNode in="cT27"></feMergeNode>
                        <feMergeNode in="cT28"></feMergeNode>
                        <feMergeNode in="cT29"></feMergeNode>
                    </feMerge>
                    <feGaussianBlur stdDeviation="1.4" in="merge" result="blur"></feGaussianBlur>
                </filter>
                <filter id="cross-blur" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
                    <feGaussianBlur stdDeviation="0 20" in="SourceGraphic" result="blur" data-not-modifiable/>
                    <feGaussianBlur stdDeviation="16 0" in="SourceGraphic" result="blur2" data-not-modifiable/>
                    <feBlend mode="lighten" in="blur" in2="blur1" result="blend" />
                    <feMerge result="merge">
                        <feMergeNode in="blend" />
                        <feMergeNode in="blend" />
                    </feMerge>
                </filter>
<!--                <filter id="average-blur">-->
<!--                    <feConvolveMatrix-->
<!--                    kernelMatrix="1 1 1 1 1 1 1 1 1"-->
<!--                    order="3 3"-->
<!--                    bias="0"-->
<!--                    divisor="9"-->
<!--                    preserveAlpha="true" />-->
<!--                </filter>-->
            </defs>
        </svg>
    </span>
    
`;function Ve(e,t=[],n){let r=T(t).svgFilterId||`blur-effect`;return console.log(r,r,t),r}var He=`
<span class="container container-filter"
        data-filter
        data-filter-type="svg"
        data-filter-name="experimental-effects" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="generic-svg-filter" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Experimental"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label class="label label-small" for="svg-filter-select" data-i18n="Select Filter"></label>
            <select id="svg-filter-select" class="input input-select" data-modifier="svg-filter-id" data-default="binaryFrost">

                
                <option value="binaryFrost" data-i18n="Binary Frost"></option>
                <option value="byteCrush" data-i18n="Byte Crush"></option>
                <option value="dataDrift" data-i18n="Data Drift"></option>
                <option value="circuitPulse" data-i18n="Circuit Pulse"></option>
                <option value="quantumBurst" data-i18n="Quantum Burst"></option>
                <option value="tvTurnOff" data-i18n="TV Turn-Off"></option>
                <option value="colorPop" data-i18n="Color Pop"></option>
                <option value="grayscale" data-i18n="Grayscale"></option>
                <option value="highContrastBW" data-i18n="High Contrast B/W"></option>
                <option value="softBW" data-i18n="Soft B/W"></option>
                <option value="bwWithBlur" data-i18n="B/W with Blur"></option>
                <option value="sharpBW" data-i18n="Sharp B/W"></option>
                <option value="techNoir" data-i18n="Tech Noir"></option>
                <option value="neonSurge" data-i18n="Neon Surge"></option>
                <option value="hackerGlow" data-i18n="Hacker Glow"></option>
                <option value="keepRed" data-i18n="Keep red"></option>
                <option value="tiltShift" data-i18n="Tilt shift"></option>
                <option value="barrelDistortion" data-i18n="Barrel distortion"></option>
                <option value="thermalVision" data-i18n="Thermal vision"></option>
                <option value="protanopia" data-i18n="Protanopia"></option>
                <option value="protanomaly" data-i18n="Protanomaly"></option>
                <option value="deuteranopia" data-i18n="Deuteranopia"></option>
                <option value="deutranomaly" data-i18n="Deutranomaly"></option>
                <option value="tritanopia" data-i18n="Tritanopia"></option>
                <option value="tritanomaly" data-i18n="Tritanomaly"></option>
                <option value="achromatopsia" data-i18n="Achromatopsia"></option>
                <option value="achromatomaly" data-i18n="Achromatomaly"></option>
                <option value="insetShadow" data-i18n="Inset shadow"></option>

          
                <option value="sharpen" data-i18n="Sharpen"></option>
                <option value="squareVignette" data-i18n="Square Vignette"></option>
                <option value="pastelEffect" data-i18n="Pastel colors"></option>
                <option value="cartoonEffect" data-i18n="Cartoon"></option>
                <option value="salt" data-i18n="Salt"></option>
                <option value="salt-more" data-i18n="Salt more"></option>
                <option value="pepper" data-i18n="Pepper"></option>
                <option value="pepper-more" data-i18n="Pepper more"></option>
                <option value="sand_light-soft" data-i18n="Light sand (soft)"></option>
                <option value="sand_light-medium" data-i18n="Light sand (medium)"></option>
                <option value="sand_light-hard" data-i18n="Light sand (hard)"></option>
                <option value="sand_dark-soft" data-i18n="Dark sand (soft)"></option>
                <option value="sand_dark-medium" data-i18n="Dark sand (medium)"></option>
                <option value="sand_dark-hard" data-i18n="Dark sand (hard)"></option>
                <option value="half-tone" data-i18n="half tone"></option>
                <option value="black-filter" data-i18n="Black filter"></option>
                <option value="distort" data-i18n="Distort"></option>
                <option value="channel-swap" data-i18n="Channel-swap"></option>
                <option value="zero-select" data-i18n="Zero select"></option>
                <option value="selective" data-i18n="Selective"></option>
                <option value="edge-grain" data-i18n="Edge grain"></option>
                <option value="hand-drawn-edge" data-i18n="Hand drawn edge"></option>
                <option value="painting" data-i18n="Painting"></option>
                <option value="displace" data-i18n="Displace"></option>
                <option value="Apollo" data-i18n="Apollo"></option>
                <option value="BlueNight" data-i18n="BlueNight"></option>
                <option value="GreenFall" data-i18n="GreenFall"></option>
                <option value="Noir" data-i18n="Noir"></option>
                <option value="NoirLight" data-i18n="NoirLight"></option>
                <option value="Rustic" data-i18n="Rustic"></option>
                <option value="Summer84" data-i18n="Summer84"></option>
                <option value="XPro" data-i18n="XPro"></option>
                <option value="emboss" data-i18n="Emboss"></option>
                <option value="roughPaper" data-i18n="Rough paper"></option>
                <option value="dotted" data-i18n="Dotted"></option>
                <option value="oldPhotoPaper" data-i18n="Old photo paper"></option>
                <option value="lineNoise" data-i18n="Line noise"></option>
                <option value="liquidMetal" data-i18n="Liquid metal"></option>
                <option value="ct-test" data-i18n="CT test"></option>
                <option value="starrySky" data-i18n="Starry Sky"></option>
                <option value="oil-slick" data-i18n="Oil slick"></option>
                <option value="scanline-filter" data-i18n="Scanlines"></option>
                <option value="scanlines-filter" data-i18n="Scan lines"></option>
                <option value="posterize" data-i18n="Posterize (Eight bit)"></option>
                <option value="cross-stitch" data-i18n="Cross stitch"></option>
                <option value="crumple-effect" data-i18n="Crumple effect"></option>
                <option value="cel-shade" data-i18n="Cel shade"></option>
                <option value="EmbossFilter" data-i18n="Emboss"></option>
                <option value="goovey" data-i18n="Goovey (water)"></option>
                <option value="dotted" data-i18n="Dotted"></option>
                <option value="sharp-edges" data-i18n="Sharp edges"></option>
                <option value="3d" data-i18n="3d"></option>
                <option value="led-screen" data-i18n="LED screen"></option>
               
                <option value="zebra" data-i18n="Zebra"></option>
                <option value="vesper" data-i18n="Vesper"></option>
                <option value="watercolor-v2" data-i18n="Watercolor v2"></option>
                <option value="pixel-v2" data-i18n="Pixel v2"></option>
                <option value="soft-paper" data-i18n="Soft paper"></option>
                <option value="sea-effect" data-i18n="Sea effect"></option>
                <option value="redcoat" data-i18n="Red coat color knockout"></option>
                <option value="scatter" data-i18n="Scatter"></option>
                <option value="squiggle" data-i18n="Squiggle"></option>
                <option value="pseudo3d" data-i18n="Pseudo3d"></option>
                <option value="half-tone-luminance" data-i18n="half-tone-luminance"></option>
                
                <option value="orton-effect" data-i18n="Orton"></option>
                <option value="orton-3x" data-i18n="Orton 3x"></option>
                <option value="old-map" data-i18n="Old map"></option>
                <option value="old-map-2" data-i18n="Old map 2"></option>
                <option value="gothamish" data-i18n="Gothamish"></option>
                <option value="hightlight-blur" data-i18n="Hightlight-blur"></option>
                <option value="broken" data-i18n="Broken"></option>
                <option value="tile-filter" data-i18n="Tile filter"></option>
                <option value="heat-map-2" data-i18n="Heat map 2"></option>
                <option value="duotone01" data-i18n="Duotone 1"></option>
                <option value="duotone02" data-i18n="Duotone 2"></option>
                <option value="duotone03" data-i18n="Duotone 3"></option>
                <option value="duotone04" data-i18n="Duotone 4"></option>
                <option value="duotone05" data-i18n="Duotone 5"></option>
                <option value="light-effect" data-i18n="Light effect"></option>
                <option value="b-and-w" data-i18n="Pure black and white"></option>
                <option value="outline-colored" data-i18n="Outline colored"></option>
                <option value="drip" data-i18n="Drip"></option>
                <option value="engraving-effect" data-i18n="Engraving"></option>
                <option value="engraving-2-effect" data-i18n="Engraving 2"></option>
                <option value="comic-ink-outline" data-i18n="Comic ink outline"></option>
                <option value="halftone-line" data-i18n="Halftone line"></option>
                <option value="halftone-wave" data-i18n="Halftone wave"></option>
                <option value="halftone-cmyk" data-i18n="Halftone cmyk"></option>
                <option value="neon-engraving-effect" data-i18n="Neon engraving"></option>
               
            </select>
        </fieldset>
        <svg xmlns="http://www.w3.org/2000/svg" width="500" height="500">
            <defs>
        <filter id="distort">
        <feTurbulence baseFrequency="0.01 0.01" numOctaves="1" result="noise"  />
        <feDisplacementMap in="SourceGraphic" in2="noise" scale="10" xChannelSelector="R" yChannelSelector="R">
        </filter>
               
                <!-- Tech Noir (Inspired by X-Pro II) -->
<!--                <filter id="techNoir">-->
                <filter id="techNoir">
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="1.5" />
                        <feFuncG type="linear" slope="1.2" />
                        <feFuncB type="linear" slope="1.1" />
                    </feComponentTransfer>
                    <feGaussianBlur in="SourceAlpha" stdDeviation="5" result="blur" />
                    <feOffset dx="0" dy="0" result="offsetBlur" />
                    <feFlood flood-color="black" result="color" />
                    <feComposite in2="offsetBlur" operator="in" />
                    <feMerge>
                        <feMergeNode />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
        
                <!-- Neon Surge (Inspired by Lo-Fi) -->
                <filter id="neonSurge">
                    <feColorMatrix type="matrix" values="1.2 0 0 0 0
                                                   0 1.1 0 0 0
                                                   0 0 1.5 0 0
                                                   0 0 0 1 0" />
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="1.2" intercept="-0.2" />
                        <feFuncG type="linear" slope="1.2" intercept="-0.2" />
                        <feFuncB type="linear" slope="1.2" intercept="-0.2" />
                    </feComponentTransfer>
                </filter>
        
                <!-- HackerGlow (Inspired by Clarendon) -->
                <filter id="hackerGlow">
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="1.5" intercept="0.2" />
                        <feFuncG type="linear" slope="1.5" intercept="0.2" />
                        <feFuncB type="linear" slope="1.5" intercept="0.2" />
                    </feComponentTransfer>
                    <feGaussianBlur stdDeviation="2" result="blurred" />
                    <feMerge>
                        <feMergeNode in="blurred" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
        
                <!-- Binary Frost (Inspired by Hudson) -->
                <filter id="binaryFrost">
                    <feColorMatrix type="matrix" values="1.0 0.0 0.0 0 0
                                                   0.0 1.0 0.0 0 0
                                                   0.0 0.0 1.5 0 0
                                                   0.0 0.0 0.0 1 0" />
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="0.8" />
                        <feFuncG type="linear" slope="0.8" />
                        <feFuncB type="linear" slope="0.9" />
                    </feComponentTransfer>
                </filter>
        
                <!-- Byte Crush (Inspired by Juno) -->
                <filter id="byteCrush">
                    <feColorMatrix type="matrix" values="1.5 0.2 0.2 0 0
                                                   0.2 1.2 0.2 0 0
                                                   0.2 0.2 1.0 0 0
                                                   0   0   0   1 0" />
                    <feGaussianBlur stdDeviation="1" result="blurred" />
                    <feMerge>
                        <feMergeNode in="blurred" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
        
                <!-- Data Drift (Inspired by Earlybird) -->
                <filter id="dataDrift">
                    <feColorMatrix type="matrix" values="1.2 0.9 0.5 0 0
                                                   0.9 1.0 0.6 0 0
                                                   0.6 0.6 0.9 0 0
                                                   0   0   0   1 0" />
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="0.9" />
                        <feFuncG type="linear" slope="0.9" />
                        <feFuncB type="linear" slope="0.9" />
                    </feComponentTransfer>
                </filter>
        
                <!-- Circuit Pulse (Inspired by Valencia) -->
                <filter id="circuitPulse">
                    <feColorMatrix type="matrix" values="1.4 0.4 0.2 0 0
                                                   0.4 1.2 0.4 0 0
                                                   0.2 0.4 1.0 0 0
                                                   0   0   0   1 0" />
                    <feGaussianBlur stdDeviation="0.8" result="blurred" />
                    <feMerge>
                        <feMergeNode in="blurred" />
                        <feMergeNode in="SourceGraphic" />
                    </feMerge>
                </filter>
        
                <!-- Quantum Burst (Inspired by Amaro) -->
                <filter id="quantumBurst">
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="1.3" intercept="0.2" />
                        <feFuncG type="linear" slope="1.3" intercept="0.2" />
                        <feFuncB type="linear" slope="1.3" intercept="0.2" />
                    </feComponentTransfer>
                    <feColorMatrix type="matrix" values="0.9 0 0 0 0
                                                   0 0.9 0 0 0
                                                   0 0 0.9 0 0
                                                   0 0 0 1 0" />
                </filter>
        
                <!-- Additional TikTok-like Effects -->
                <!-- TV Turn-Off Effect -->
                <filter id="tvTurnOff">
                    <feComponentTransfer>
                        <feFuncA type="table" tableValues="1 0">
                            <animate attributeName="tableValues" values="1;0.2;0" dur="1s" begin="0s" repeatCount="1" fill="freeze"/>
                        </feFuncA>
                    </feComponentTransfer>
                    <feGaussianBlur in="SourceGraphic" stdDeviation="0 0">
                        <animate attributeName="stdDeviation" values="0 0;0 5;0 100" dur="1s" begin="0s" fill="freeze" repeatCount="1"/>
                    </feGaussianBlur>
                </filter>
        
                <!-- Color Pop Filter -->
                <filter id="colorPop">
                    <feColorMatrix type="matrix" values="0.33 0.33 0.33 0 0 
                                                   0.33 0.33 0.33 0 0 
                                                   0.33 0.33 0.33 0 0 
                                                   0    0    0    1 0" result="grayscale" />
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="1.5" />
                        <feFuncG type="linear" slope="0.5" />
                        <feFuncB type="linear" slope="0.5" />
                    </feComponentTransfer>
                </filter>
        
                <!-- Grayscale Filter -->
                <filter id="grayscale">
                    <feColorMatrix type="matrix" values="0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0    0    0    1 0" />
                </filter>
        
                <!-- High Contrast Black & White -->
                <filter id="highContrastBW">
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="2" intercept="-1" />
                        <feFuncG type="linear" slope="2" intercept="-1" />
                        <feFuncB type="linear" slope="2" intercept="-1" />
                    </feComponentTransfer>
                </filter>
        
                <!-- Soft Black & White -->
                <filter id="softBW">
                    <feColorMatrix type="matrix" values="0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0    0    0    1 0" />
                    <feComponentTransfer>
                        <feFuncR type="linear" slope="1.1" />
                        <feFuncG type="linear" slope="1.1" />
                        <feFuncB type="linear" slope="1.1" />
                    </feComponentTransfer>
                </filter>
        
                <!-- Black & White with Blur -->
                <filter id="bwWithBlur">
                    <feColorMatrix type="matrix" values="0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0    0    0    1 0" />
                    <feGaussianBlur stdDeviation="2" />
                </filter>
        
                <!-- Sharp Black & White -->
                <filter id="sharpBW">
                    <feColorMatrix type="matrix" values="0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0.33 0.33 0.33 0 0
                                                   0    0    0    1 0" />
                    <feConvolveMatrix order="3" kernelMatrix="-1 -1 -1 -1 9 -1 -1 -1 -1" />
                </filter>
            
            <filter id="keepRed">
  <!-- Desaturate everything -->
  <feColorMatrix type="matrix"
    values="0.33 0.33 0.33 0 0
            0.33 0.33 0.33 0 0
            0.33 0.33 0.33 0 0
            0    0    0    1 0"
    result="gray"/>

  <!-- Extract red component -->
  <feColorMatrix type="matrix"
    values="1 0 0 0 0
            0 0 0 0 0
            0 0 0 0 0
            0 0 0 1 0"
    in="SourceGraphic"
    result="onlyRed" />

  <!-- Blend grayscale + red -->
  <feBlend in="gray" in2="onlyRed" mode="screen" />
</filter>

<filter id="tiltShift" x="0" y="0" width="100%" height="100%">
  <!-- Blur entire image -->
  <feGaussianBlur in="SourceGraphic" stdDeviation="5" result="blurred" />

  <!-- Create vertical alpha gradient for masking (sharp center band) -->
  <feComponentTransfer in="SourceAlpha" result="alpha">
    <feFuncA type="linear" slope="1" intercept="0"/>
  </feComponentTransfer>

  <!-- Create a mask using a feImage of an SVG linearGradient -->
  <feImage xlink:href="data:image/svg+xml;utf8,
    <svg xmlns='http://www.w3.org/2000/svg' width='1' height='100'>
      <linearGradient id='grad' x1='0' y1='0' x2='0' y2='1'>
        <stop offset='0%' stop-color='white' stop-opacity='1'/>
        <stop offset='40%' stop-color='white' stop-opacity='1'/>
        <stop offset='50%' stop-color='black' stop-opacity='0'/>
        <stop offset='60%' stop-color='white' stop-opacity='1'/>
        <stop offset='100%' stop-color='white' stop-opacity='1'/>
      </linearGradient>
      <rect width='1' height='100' fill='url(#grad)' />
    </svg>"
    result="mask" 
    x="0" y="0" width="100%" height="100%" />

  <!-- Blend sharp original and blurred based on mask -->
  <feComposite in="SourceGraphic" in2="mask" operator="in" result="sharpPart" />
  <feComposite in="blurred" in2="mask" operator="out" result="blurredPart" />
  <feMerge>
    <feMergeNode in="sharpPart" />
    <feMergeNode in="blurredPart" />
  </feMerge>
</filter>

<filter id="barrelDistortion" x="0" y="0" width="100%" height="100%">
  <!-- Generate displacement map with radial distortion -->
  <feImage xlink:href="data:image/svg+xml;utf8,
    <svg xmlns='http://www.w3.org/2000/svg' width='256' height='256' viewBox='0 0 256 256'>
      <radialGradient id='rg' cx='0.5' cy='0.5' r='0.7'>
        <stop offset='0%' stop-color='128' />
        <stop offset='100%' stop-color='255' />
      </radialGradient>
      <rect width='256' height='256' fill='url(#rg)' />
    </svg>"
    result="dispMap"
    width="100%" height="100%" />
  
  <!-- Apply displacement -->
  <feDisplacementMap in="SourceGraphic" in2="dispMap" scale="20" xChannelSelector="R" yChannelSelector="G" />
</filter>
        
  
                  <filter id="thermalVision" color-interpolation-filters="sRGB">
                    <feComponentTransfer>
                      <feFuncR type="table" tableValues="0  0.125  0.8    1      1" />
                      <feFuncG type="table" tableValues="0  0      0      0.843  1" />
                      <feFuncB type="table" tableValues="0  0.549  0.466  0      1" />
                    </feComponentTransfer>
                  </filter>
                  <filter id="protanopia">
  <feColorMatrix
    type="matrix"
    values=".56667 .43333 0      0 0
            .55833 .44167 0      0 0
            0      .24167 .75833 0 0
            0      0      0      1 0" />
</filter>

<filter id="protanomaly">
  <feColorMatrix
    type="matrix"
    values=".81667 .18333 0    0 0
            .33333 .66667 0    0 0
            0      .125   .875 0 0
            0      0      0    1 0" />
</filter>

<filter id="deuteranopia">
  <feColorMatrix
    type="matrix"
    values=".625 .375 0  0 0
            .7   .3   0  0 0
            0    .3   .7 0 0
            0    0    0  1 0" />
</filter>

<filter id="deutranomaly">
  <feColorMatrix
    type="matrix"
    values=".8     .2     0      0 0
            .25833 .74167 0      0 0
            0      .14167 .85833 0 0
            0      0      0      1 0" />
</filter>

<filter id="tritanopia">
  <feColorMatrix
    type="matrix"
    values=".95 .5     0      0 0
            0   .43333 .56667 0 0
            0   .475   .525   0 0
            0   0      0      1 0" />
</filter>

<filter id="tritanomaly">
  <feColorMatrix
    type="matrix"
    values=".96667 .3333  0      0 0
            0      .73333 .26667 0 0
            0      .18333 .81667 0 0
            0      0      0      1 0" />
</filter>

<filter id="achromatopsia">
  <feColorMatrix
    type="matrix"
    values=".299 .587 .114 0 0
            .299 .587 .114 0 0
            .299 .587 .114 0 0
            0    0    0    1 0" />
</filter>

<filter id="achromatomaly">
  <feColorMatrix
    type="matrix"
    values=".618 .32  .62  0 0
            .163 .775 .62  0 0
            .163 .320 .516 0 0
            0    0    0    1 0" />
</filter>
                    
<filter id="insetShadow" x="-50%" y="-50%" width="200%" height="200%">
    <feComponentTransfer in=SourceAlpha>
        <feFuncA type="table" tableValues="1 0" />
    </feComponentTransfer>
    <feGaussianBlur stdDeviation="3"/>
    <feOffset dx="5" dy="5" result="offsetblur"/>
    <feFlood flood-color="rgb(20, 0, 0)" result="color"/>
    <feComposite in2="offsetblur" operator="in"/>
    <feComposite in2="SourceAlpha" operator="in" />
    <feMerge>
        <feMergeNode in="SourceGraphic" />
        <feMergeNode />
    </feMerge>
</filter>
    
    <filter id="sharpen">
        <feConvolveMatrix order="3" kernelMatrix="
        0 -1 0
        -1 5 -1
        0 -1 0" />
    </filter>

<filter id="squareVignette" x="0" y="0" width="100%" height="100%">
  <!-- Black rectangle covering entire area -->
  <feFlood flood-color="black" result="black" />
  
  <!-- Create an alpha mask from a centered white rectangle -->
  <feComposite in="black" in2="SourceAlpha" operator="in" result="base" />
  
  <!-- Create a white rectangle in the center for the vignette "hole" -->
  <feMorphology operator="erode" radius="100" in="SourceAlpha" result="eroded" />
  
  <!-- Blur the edges of the rectangle to soften -->
  <feGaussianBlur in="eroded" stdDeviation="50" result="blurred" />
  
  <!-- Invert the blurred mask -->
  <feComponentTransfer in="blurred" result="inverted">
    <feFuncA type="table" tableValues="1 0" />
  </feComponentTransfer>
  
  <!-- Multiply the inverted mask with black color -->
  <feComposite in="black" in2="inverted" operator="in" result="vignette" />
  
  <!-- Blend vignette with original image -->
  <feBlend in="SourceGraphic" in2="vignette" mode="multiply" />
</filter>

        <filter id="pastel-effect" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
                <!-- Desaturate and soften color matrix -->
                <feColorMatrix id="pastel-colormatrix" type="matrix"
                    values="0.8 0.1 0.1 0 0
                            0.1 0.8 0.1 0 0
                            0.1 0.1 0.8 0 0
                            0   0   0   1 0"
                    result="soft" />
                
                <!-- Brighten all channels -->
                <feComponentTransfer in="soft" result="pastel">
                    <feFuncR type="gamma" amplitude="1.5" exponent="1" offset="0" />
                    <feFuncG type="gamma" amplitude="1.5" exponent="1" offset="0" />
                    <feFuncB type="gamma" amplitude="1.5" exponent="1" offset="0" />
                </feComponentTransfer>
            </filter>
            

            <filter id="pastelEffect" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
              <!-- Step 1: Slight desaturation with a soft hue bias -->
              <feColorMatrix type="matrix"
                values="0.9 0.05 0.05 0 0.03
                        0.05 0.85 0.1  0 0.03
                        0.1  0.1  0.85 0 0.03
                        0    0    0    1 0"
                result="tinted" />
            
              <!-- Step 2: Brighten using exponent < 1 + offset -->
              <feComponentTransfer in="tinted" result="bright">
                <feFuncR type="gamma" amplitude="1" exponent="0.75" offset="0.04"/>
                <feFuncG type="gamma" amplitude="1" exponent="0.75" offset="0.04"/>
                <feFuncB type="gamma" amplitude="1" exponent="0.75" offset="0.04"/>
              </feComponentTransfer>
            
              <!-- Step 3: Overlay a soft light wash (e.g., pink or peach) -->
              <feFlood flood-color="#ffe6f0" flood-opacity="0.2" result="tint"/>
              <feBlend in="bright" in2="tint" mode="screen" result="pastel"/>
            
              <!-- Output -->
              <feComposite in="pastel" in2="SourceAlpha" operator="in"/>
            </filter> 
            
            <filter id="cartoon-effect" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
  <!-- 1. Desaturate + flatten colors slightly -->
  <feColorMatrix type="matrix" result="flatColors"
    values="0.6 0.2 0.2 0 0
            0.2 0.6 0.2 0 0
            0.2 0.2 0.6 0 0
            0   0   0   1 0" />

  <!-- 2. Edge detection using Laplacian kernel -->
  <feConvolveMatrix in="flatColors" order="3" kernelMatrix="
      -1 -1 -1
      -1  8 -1
      -1 -1 -1" divisor="1" result="edges" />

  <!-- 3. Threshold edges to make bold lines -->
  <feComponentTransfer in="edges" result="edgeLines">
    <feFuncR type="table" tableValues="0 1" />
    <feFuncG type="table" tableValues="0 1" />
    <feFuncB type="table" tableValues="0 1" />
  </feComponentTransfer>

  <!-- 4. Invert lines (white background, black lines) -->
  <feComponentTransfer in="edgeLines" result="invertedLines">
    <feFuncR type="table" tableValues="1 0" />
    <feFuncG type="table" tableValues="1 0" />
    <feFuncB type="table" tableValues="1 0" />
  </feComponentTransfer>

  <!-- 5. Blend edges on top of flat colors -->
  <feBlend in="flatColors" in2="invertedLines" mode="multiply" result="cartoon" />

  <!-- Output -->
  <feComposite in="cartoon" in2="SourceAlpha" operator="in" />
</filter>
<filter x="0%" y="0%" width="100%" height="100%" id="salt" filterUnits="objectBoundingBox">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.3" numOctaves="1" seed="1" />
<feColorMatrix type="matrix" values="-22 0 0 0 6      -22 0 0 0 6     -22 0 0 0 6      0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="f3" in2="SourceGraphic" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 1      0 1 0 0 1     0 0 1 0 1     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="salt-more" filterUnits="objectBoundingBox">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.3" numOctaves="1" seed="1" />
<feColorMatrix type="matrix" values="-23 0 0 0 7      -23 0 0 0 7     -23 0 0 0 7      0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="f3" in2="SourceGraphic" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 1      0 1 0 0 1     0 0 1 0 1     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="pepper" filterUnits="objectBoundingBox">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.28" numOctaves="1" seed="2" />
<feColorMatrix type="matrix" values="-21 0 0 0 6      -21 0 0 0 6     -21 0 0 0 6      0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="f3" in2="SourceGraphic" result="f4" operator="in" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f4" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="pepper-more" filterUnits="objectBoundingBox">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.3" numOctaves="1" seed="2" />
<feColorMatrix type="matrix" values="-22 0 0 0 7      -22 0 0 0 7     -22 0 0 0 7      0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="f3" in2="SourceGraphic" result="f4" operator="in" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f4" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="sand_light-soft" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.6" numOctaves="1" seed="0" />
<feColorMatrix type="matrix" values="-18 0 0 0 8      -18 0 0 0 8     -18 0 0 0 8      0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="SourceGraphic" in2="f3" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 0.12      0 1 0 0 0.12     0 0 1 0 0.12     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="sand_light-medium" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.6" numOctaves="1" seed="0" />
<feColorMatrix type="matrix" values="-18 0 0 0 8     -18 0 0 0 8     -18 0 0 0 8     0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="SourceGraphic" in2="f3" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 0.22      0 1 0 0 0.22      0 0 1 0 0.22      0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="sand_light-hard" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.6" numOctaves="1" seed="0" />
<feColorMatrix type="matrix" values="-18 0 0 0 8     -18 0 0 0 8     -18 0 0 0 8     0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="SourceGraphic" in2="f3" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 0.32     0 1 0 0 0.32     0 0 1 0 0.32     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="sand_dark-soft" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.6" numOctaves="1" seed="0" />
<feColorMatrix type="matrix" values="-17 0 0 0 8      -17 0 0 0 8     -17 0 0 0 8      0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="SourceGraphic" in2="f3" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 -0.1     0 1 0 0 -0.1     0 0 1 0 -0.1     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="sand_dark-medium" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.6" numOctaves="1" seed="0" />
<feColorMatrix type="matrix" values="-17 0 0 0 8     -17 0 0 0 8     -17 0 0 0 8     0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="SourceGraphic" in2="f3" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 -0.18     0 1 0 0 -0.18     0 0 1 0 -0.18     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
<filter  x="0%" y="0%" width="100%" height="100%" id="sand_dark-hard" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
<feTurbulence type="fractalNoise" result="f1" stitchTiles="noStitch" baseFrequency="0.6" numOctaves="1" seed="0" />
<feColorMatrix type="matrix" values="-17 0 0 0 8     -17 0 0 0 8     -17 0 0 0 8     0 0 0 0 1" in="f1" result="f2" />
<feColorMatrix type="luminanceToAlpha" in="f2" result="f3" />
<feComposite in="SourceGraphic" in2="f3" result="f4" operator="in" />
<feColorMatrix type="matrix" values="1 0 0 0 -0.26     0 1 0 0 -0.26     0 0 1 0 -0.26     0 0 0 1 0" in="f4" result="f5" />
<feMerge>
<feMergeNode in="SourceGraphic" />
<feMergeNode in="f5" />
</feMerge>
</filter>
        <filter id="black-filter" color-interpolation-filters="sRGB">
            <feColorMatrix
            type="matrix"
            values="1 0 0 0 0 
            0 1 0 0 0 
            0 0 1 0 0 
            -1 -1 -1 0 1"
            result="black-pixels"
            ></feColorMatrix>
            <feMorphology
            in="black-pixels"
            operator="dilate"
            radius="0.5"
            result="smoothed"
            ></feMorphology>
            <feComposite
            in="SourceGraphic"
            in2="smoothed"
            operator="out"
            ></feComposite>
        </filter>
        <filter id='channel-swap' color-interpolation-filters='sRGB'>
        <feColorMatrix values='0 1 0 0 0 1 0 0 0 0 0 0 1 0 0 0 0 0 1 0'/>
        </filter>
        
    <filter id='zero-select' color-interpolation-filters='sRGB'>
    <feColorMatrix values='1 0 0 0 0 0 1 0 0 0 0 0 0 0 0 0 0 0 1 0'/>
    </filter>
    
     <filter id="selective" color-interpolation-filters="sRGB">
    <feColorMatrix values="2 -3 -2 0 0
            0  0  0 0 0
            0  0  0 0 0
            0  0  0 1 0"></feColorMatrix>
                <feColorMatrix values="  0 0 0 0 0
              0 0 0 0 0
              0 0 0 0 0
            999 0 0 0 0"></feColorMatrix>
    <feComposite operator="in" in="SourceGraphic"></feComposite>
  </filter>
  
    <filter id="edge-grain" x="-.5" y="-.5" width="2" height="2" color-interpolation-filters="sRGB">
    <!-- shrink filter input area by 3x desired blur radius-->
    <feMorphology radius="48"></feMorphology>
    <!-- blur it after & save result-->
    <feGaussianBlur stdDeviation="16" result="blur"></feGaussianBlur>
    <!-- generate fine noise-->
    <feTurbulence type="fractalNoise" baseFrequency=".713" numOctaves="4"></feTurbulence>
    <!-- use noise as displacement map-->
    <feDisplacementMap in="blur" scale="64" xChannelSelector="R"></feDisplacementMap>
    <!-- use the filter input to paint the grainy paded edge shape-->
    <feComposite in="SourceGraphic" operator="in"></feComposite>
  </filter>
  
   <filter id="hand-drawn-edge">
      <feTurbulence type="fractalNoise" baseFrequency=".025" numOctaves="5"></feTurbulence>
      <feDisplacementMap in="SourceGraphic" scale="15" xChannelSelector="G"></feDisplacementMap>
      <feGaussianBlur stdDeviation="2"></feGaussianBlur>
      <feComponentTransfer>
        <feFuncA type="table" tableValues="-1 2"></feFuncA>
      </feComponentTransfer>
    </filter>
    
    <filter id="painting">
<feMorphology radius="2"/>
</filter>

  <filter id="displace" color-interpolation-filters="sRGB">
    <feFlood flood-color="#888800" result="neutral-back"/>
    <feFlood flood-color="#8888FF" height="57%"/>
    <feComposite operator="over" in2="neutral-back"/>
    <feDisplacementMap scale="0.02" in="SourceGraphic" xChannelSelector="B" yChannelSelector="R" result="displaced"/>
    <feFlood flood-color="white" y="47%" height="0.5%"/>
    <feComposite operator="over" in2="displaced"/>
    
  </filter>
  
  
  <filter id="Apollo" filterUnits="objectBoundingBox" color-interpolation-filters="sRGB">
    <feColorMatrix values="0.8 0.6 -0.4 0.1 0,
      0 1.2 0.05 0 0,
      0 -1 3 0.02 0,
      0 0 0 50 0" result="final" in="SourceGraphic"></feColorMatrix>
  </filter>

  <filter id="BlueNight" filterUnits="objectBoundingBox"
   color-interpolation-filters="sRGB">
    <feColorMatrix
      type="matrix"
      values="1.000 0.000 0.000 0.000 0.000
                    0.000 1.000 0.000 0.000 0.05
                    0.000 0.000 1.000 0.000 0.400
                    0.000 0.000 0.000 1.000 0.000"
    ></feColorMatrix>
  </filter>
  
  <filter
    id="GreenFall"
    color-interpolation-filters="linearRGB"
  >
    <feColorMatrix
      type="matrix"
      values="0.5 -0.4 0.3332 0 0
          0 0.4 0.3 0 0
          0 0 0.5 0 0
          0 0 0 500 -20"
      in="SourceGraphic"
      result="colormatrix"></feColorMatrix>
  </filter>
  
  <filter
    id="Noir"
    color-interpolation-filters="linearRGB"
  >
    <feColorMatrix type="saturate" values="0" in="SourceGraphic" result="colormatrix1"
    ></feColorMatrix>
    <feBlend mode="lighten" in="colormatrix1" in2="colormatrix1" result="blend"></feBlend>
    <feBlend mode="multiply" in="colormatrix1" in2="diffuseLighting" result="blend1"></feBlend>
  </filter>
  
  <filter
    id="NoirLight"
    color-interpolation-filters="linearRGB"
  >
    <feColorMatrix type="saturate" values="0" in="SourceGraphic" result="colormatrix2"
    ></feColorMatrix>
    <feBlend mode="saturation" in="SourceGraphic" in2="colormatrix2" result="blend2"></feBlend>
    <feBlend mode="screen" in="colormatrix2" in2="blend2" result="blend3"></feBlend>
    <feColorMatrix type="luminanceToAlpha" in="blend3" result="colormatrix3"></feColorMatrix>
    <feBlend mode="exclusion" in="blend3" in2="colormatrix3" result="blend5"></feBlend>
  </filter>


  <filter id="Rustic" color-interpolation-filters="sRGB">
    <feColorMatrix
      type="matrix"
      in="SourceGraphic"
      result="colormatrix"
      values="0.39215686274509803 0.39215686274509803 0.39215686274509803  0 0
          0.3333333333333333 0.3333333333333333 0.3333333333333333  0 0
          0.30980392156862746 0.30980392156862746 0.30980392156862746  0 0
          0 0 0 1 0"
    ></feColorMatrix>
  </filter>
  
  <filter
    id="Summer84"
    color-interpolation-filters="sRGB"
  >
    <feColorMatrix
      type="matrix"
      values="1.300 0.200 0.000 0.000 0.000
          0.300 0.600 0.200 0.000 0.000
          0.200 1.000 0.200 0.000 0.000
          0.000 0.000 0.000 1.000 0.000"
    ></feColorMatrix>
  </filter>
  
  <filter id="XPro" color-interpolation-filters="sRGB">
    <feColorMatrix
      type="matrix"
      values="1.70 -0.20 0.00 0.00 0.00
                    0.10 0.800 0.30 0.00 0.00
                    0.20 0.300 0.50 0.00 0.00
                    0.00 0.00 0.00 1.00 0.00"
    ></feColorMatrix>
  </filter>
  
   <filter id="emboss">
    <feConvolveMatrix order="5 5"
      preserveAlpha="true" 
      kernelMatrix="-1 0 0 0 0 0 -2 0 0 0 0 0 3 0 0 0 0 0 0 0 0 0 0 0 0"/>
  </filter>
  
            <filter id='roughPaper'>
                <feTurbulence type="fractalNoise"
                baseFrequency="0.04"
                numOctaves="5"
                result="turbulence"/>

              <!-- 2. Lighting for bump map -->
              <feDiffuseLighting in="turbulence"
                                 lighting-color="#ffffff"
                                 surfaceScale="2"
                                 result="bump">
                    <feDistantLight azimuth="45" elevation="60"/>
              </feDiffuseLighting>
        
<!--                 <feDisplacementMap in="SourceGraphic"-->
<!--                     in2="bump"-->
<!--                     scale="40"-->
<!--                     xChannelSelector="R"-->
<!--                     yChannelSelector="R"-->
<!--                     result="displaced"/>-->

                <feBlend in="bump" in2="SourceGraphic" mode="multiply" result="blended" />
                    <!-- 4. Merge final image -->
                      <feMerge>
                        <feMergeNode in="blended"/>
                      </feMerge>
        
            </filter>
            
            
            <filter id='dotted'>
              <feTurbulence
                type="fractalNoise"
                baseFrequency="0.45"
                numOctaves="1"
                seed="1"
                stitchTiles="stitch"
                result="turbulence"/>

              <!-- 2. Lighting for bump map -->
              <feDiffuseLighting in="turbulence"
                                 lighting-color="#ffffff"
                                 surfaceScale="2"
                                 result="bump">
                    <feDistantLight azimuth="45" elevation="60"/>
              </feDiffuseLighting>
        
<!--                 <feDisplacementMap in="SourceGraphic"-->
<!--                     in2="bump"-->
<!--                     scale="40"-->
<!--                     xChannelSelector="R"-->
<!--                     yChannelSelector="R"-->
<!--                     result="displaced"/>-->

                <feBlend in="bump" in2="SourceGraphic" mode="multiply" result="blended" />
                    <!-- 4. Merge final image -->
                      <feMerge>
                        <feMergeNode in="blended"/>
                      </feMerge>
        
            </filter>
            

<filter id="oldPhotoPaper" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
  <!-- 1. Fine grainy noise pattern -->
  <feTurbulence type="fractalNoise" baseFrequency="0.03" numOctaves="3" result="grain" seed="3" />

  <!-- 2. Apply soft lighting to simulate light scattering on the paper texture -->
  <feDiffuseLighting in="grain" lighting-color="#ffffff" surfaceScale="2" result="paperLighting">
    <feDistantLight azimuth="45" elevation="65" />
  </feDiffuseLighting>

  <!-- 3. Blend the grainy lighting with the image  -->
  <feBlend in="SourceGraphic" in2="paperLighting" mode="multiply" result="photoTexture" />

  <!-- Optional: subtle distortion using the grain texture -->
<!--  <feDisplacementMap in="photoTexture" in2="grain" scale="5" xChannelSelector="R" yChannelSelector="R" result="distorted" />-->

  <!-- Output final image -->
  <feMerge>
    <feMergeNode in="photoTexture" />
  </feMerge>
</filter>

<filter id="lineNoise" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
  <!-- Generate turbulence that looks like lines -->
  <feTurbulence
    type="turbulence"
    baseFrequency="0.0 0.08"
    numOctaves="1"
    seed="2"
    result="linePattern"
  ></feTurbulence>
  
   <feBlend in="SourceGraphic" in2="linePattern" mode="multiply" result="photoLines" />
     <feMerge>
        <feMergeNode in="photoLines" />
  </feMerge>
</filter>

 <filter id="liquidMetal">
        <feTurbulence baseFrequency=".01"/>
        <feComposite operator="arithmetic" k1="-.6" k2="1" result="n"/>
        <feDiffuseLighting lighting-color="#333" surfaceScale="99" result="d">
            <feDistantLight azimuth="225" elevation="45"/>
        </feDiffuseLighting>
        <feSpecularLighting in="n" surfaceScale="99" specularExponent="32">
            <feDistantLight azimuth="225" elevation="45"/>
        </feSpecularLighting>
        <feGaussianBlur stdDeviation="3"/>
<!--        <feBlend in="d" mode="hue"/>-->
                         <feDisplacementMap in="SourceGraphic"
                     in2="d"
                     scale="40"
                     xChannelSelector="R"
                     yChannelSelector="R"
                     result="displaced"/>
                      <feMerge>
        <feMergeNode in="displaced" />
  </feMerge>
    </filter>
    <rect width="100%" height="100%" filter="url(#filter)"/>

<filter id="ct-test">
    <feComponentTransfer>
             <feFuncR type="discrete" tableValues="0 .25 .4 .5 .75 1" />
        <feFuncG type="discrete" tableValues="0 .25 .4 .5 .75 1" />
        <feFuncB type="discrete" tableValues="0 .25 .4 .5 .75 1" />
    </feComponentTransfer>
</filter>

<!--<filter id="strongOldPhotoPaper" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">-->
<!--  &lt;!&ndash; 1. Grain: slightly larger, more octaves &ndash;&gt;-->
<!--  <feTurbulence type="fractalNoise" baseFrequency="0.03" numOctaves="5" seed="2" result="grain" />-->

<!--  &lt;!&ndash; 2. Add contrast to noise &ndash;&gt;-->
<!--  <feComponentTransfer in="grain" result="contrastedGrain">-->
<!--    <feFuncR type="gamma" amplitude="1.5" exponent="1.4" offset="0" />-->
<!--    <feFuncG type="gamma" amplitude="1.5" exponent="1.4" offset="0" />-->
<!--    <feFuncB type="gamma" amplitude="1.5" exponent="1.4" offset="0" />-->
<!--  </feComponentTransfer>-->

<!--  &lt;!&ndash; 3. Create light + shadow on the grain &ndash;&gt;-->
<!--  <feDiffuseLighting in="contrastedGrain" lighting-color="#ffffff" surfaceScale="4" result="paperLight">-->
<!--    <feDistantLight azimuth="45" elevation="60" />-->
<!--  </feDiffuseLighting>-->

<!--  &lt;!&ndash; 4. Slight distortion using the same grain &ndash;&gt;-->
<!--  <feDisplacementMap in="SourceGraphic" in2="contrastedGrain" scale="5" xChannelSelector="R" yChannelSelector="G" result="displaced" />-->

<!--  &lt;!&ndash; 5. Blend the lighting into the displaced image &ndash;&gt;-->
<!--  <feBlend in="displaced" in2="paperLight" mode="multiply" result="photoPaper" />-->

<!--  &lt;!&ndash; 6. Optional: merge as output &ndash;&gt;-->
<!--  <feMerge>-->
<!--    <feMergeNode in="photoPaper" />-->
<!--  </feMerge>-->
<!--</filter>-->

  <filter id="starrySky">
        <feTurbulence baseFrequency="0.2"/>
        <feColorMatrix type="matrix" values="0 0 0 9 -4
                               0 0 0 9 -4
                               0 0 0 9 -4
                               0 0 0 0 1" result="stars"/>
                               
       <feBlend in="SourceGraphic" in2="stars" mode="multiply" />
                               
    </filter>
    
    <filter id="oil-slick">
        <feComponentTransfer in="yellowish" result="oil-slick">
        <feFuncR type="table" tableValues="0 0 0.9 0 0.91 1"/>
        <feFuncG type="table" tableValues="0 0 0.9 0 0.91 1"/>
        <feFuncB type="table" tableValues="0 0 0.9 0 0.91 1"/>
        </feComponentTransfer>
    </filter>
    
    <filter id="scanline-filter">
      <!-- Make stripe-like turbulence -->
<!--      <feTurbulence type="turbulence" baseFrequency="0 0.02" numOctaves="1" result="turb" />-->
      <feTurbulence type="turbulence" baseFrequency="0.1 0.1" numOctaves="5" result="turb" />
    
      <!-- Increase contrast -->
      <feComponentTransfer in="turb" result="stripes">
        <feFuncR type="table" tableValues="0 1" />
        <feFuncG type="table" tableValues="0 1" />
        <feFuncB type="table" tableValues="0 1" />
      </feComponentTransfer>
    
      <!-- Blend with original image -->
      <feBlend in="SourceGraphic" in2="stripes" mode="multiply" />
    
    </filter>
 
    <filter id="scanlines-filter" x="0%" y="0%" width="100%" height="100%">
      <!-- Step 1: Generate stripes -->
      <feTurbulence type="turbulence" baseFrequency="0 0.02" numOctaves="1" result="turbulence"/>
      
      <!-- Step 2: Sharpen the stripes into solid lines -->
      <feComponentTransfer in="turbulence" result="stripes">
        <feFuncR type="discrete" tableValues="0 1"/>
        <feFuncG type="discrete" tableValues="0 1"/>
        <feFuncB type="discrete" tableValues="0 1"/>
      </feComponentTransfer>
      
      <!-- Step 3: Blend the scanlines on top of the original image -->
      <feBlend in="SourceGraphic" in2="stripes" mode="multiply"/>
    </filter>
    
    <filter id="cross-stitch">
    <feFlood id="filterPxl" x="7" y="7" width="2" height="2" result="flood"></feFlood>
    <feComposite id="filterCs" in="flood" in2="flood" operator="over" x="0" y="0" width="16" height="16" result="composite"></feComposite>
    <feTile x="0" y="0" width="10000" height="10000" in="composite" result="tile"></feTile>
    <feComposite in="SourceGraphic" in2="tile" operator="in"></feComposite>
    <feMorphology id="filterMorph" operator="dilate" radius="7" in="composite2" result="morphology"></feMorphology>
    <feImage id="filterCsImg" xlink:href="data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cellipse transform='rotate(-45 50 50)' stroke='%23000' ry='25' rx='64' id='svg_1' cy='50' cx='50' /%3E%3C/svg%3E" x="0" y="0" width="16" height="16" preserveAspectRatio="xMidYMid meet" crossorigin="anonymous" result="image"></feImage>
    <feTile x="0" y="0" width="10000" height="10000" in="image" result="tile1"></feTile>
    <feComposite in="morphology" in2="tile1" operator="in" result="composite3"></feComposite>
    
    <feImage id="filterCsImg2" xlink:href="data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cellipse transform='rotate(45 50 50)' stroke='%23000' ry='25' rx='64' id='svg_3' cy='50' cx='50' /%3E%3C/svg%3E" x="0" y="0" width="16" height="16" preserveAspectRatio="xMidYMid meet" crossorigin="anonymous" result="image2"></feImage>
    <feTile x="0" y="0" width="10000" height="10000" in="image2" result="tile2"></feTile>
    <feComposite in="morphology" in2="tile2" operator="in" result="composite4"></feComposite>
    
    <feDropShadow stdDeviation="1.25" in="composite3" dx="0" dy="0" flood-color="#000" flood-opacity=".8" result="dropShadow" />
    
    <feComposite in="composite3" in2="dropShadow" operator="over" result="composite5"></feComposite>
    
    <feDropShadow stdDeviation="1.1" in="composite4" dx="0" dy="0" flood-color="#000" flood-opacity=".8" result="dropShadow" />
    
    <feComposite in="composite3" in2="dropShadow" operator="over" result="composite6"></feComposite>
    
    <!-- Canva -->
    <feFlood flood-color="#888" id="canva" result="flood1"></feFlood>
    <feMerge result="merge">
    <feMergeNode in="flood1"></feMergeNode>
    <feMergeNode in="composite6"></feMergeNode>
    <feMergeNode in="composite5"></feMergeNode>
    </feMerge>

</filter>
<filter id="posterize" color-interpolation-filters="sRGB">
<feComponentTransfer>
<feFuncR type="discrete" tableValues="0 0.125 0.251 0.376 0.502 0.627 0.753 0.878" />
<feFuncG type="discrete" tableValues="0 0.125 0.251 0.376 0.502 0.627 0.753 0.878" />
<feFuncB type="discrete" tableValues="0 0.251 0.502 0.753" />
<feFuncA type="identity" />
</feComponentTransfer>
</filter>

<filter id="crumple-effect">
<feTurbulence type="fractalNoise" baseFrequency="0.01" numOctaves="20" result="turbulence" />
<feDisplacementMap in2="turbulence" in="SourceGraphic" scale="50" />
</filter>

<filter id="cel-shade" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse" primitiveUnits="userSpaceOnUse" x="0%" y="0%" width="100%" height="100%">
   
<feGaussianBlur stdDeviation="1" edgeMode="duplicate"  in="SourceGraphic" result="bg_blur"/>

<feComponentTransfer in="bg_blur" result="bg_color_reduce">
<feFuncR type="discrete" tableValues="0 .1 .2 .3 .4 .5 .6 .7 .8 .9 1"/>
<feFuncG type="discrete" tableValues="0 1"/>  
<feFuncB type="discrete" tableValues="0 1"/>  
<feFuncA type="discrete" tableValues="1 1"/>
</feComponentTransfer>

<feColorMatrix type="matrix" values="1 0 0 0 0.1 0 1 0 0 0.35 0 0 1 0 0.1 0 0 0 1 0" in="bg_color_reduce" result="bg_color_corrected"/>
    
<feColorMatrix type="matrix" values="0.33 0.59 0.11 0 0 0.33 0.59 0.11 0 0 0.33 0.59 0.11 0 0 0 0 0 1 0" in="SourceGraphic" result="grayscale"/>

<feConvolveMatrix order="5 5" bias="1" divisor="10" kernelUnitLength="1" kernelMatrix="-5 -8 -10 -8 -5 -4 -10 -20 -10 -4 0 0 0 0 0 4 10 20 10 4 5 8 10 8 5" edgeMode="duplicate" in="grayscale" resullt="sobel_90"/>
<feColorMatrix type="matrix" values="-1 0 0 0 1 0 -1 0 0 1 0 0 -1 0 1 0 0 0 1 0" in="sobel_90" result="sobel_90_inverted"/>
    
<feConvolveMatrix order="5 5" bias="1" divisor="10" kernelUnitLength="1" kernelMatrix="5 8 10 8 5 4 10 20 10 4 0 0 0 0 0 -4 -10 -20 -10 -4 -5 -8 -10 -8 -5" edgeMode="duplicate" in="grayscale" resullt="sobel_270"/>
<feColorMatrix type="matrix" values="-1 0 0 0 1 0 -1 0 0 1 0 0 -1 0 1 0 0 0 1 0" in="sobel_270" result="sobel_270_inverted"/>  
    
<feConvolveMatrix order="5 5" bias="1" divisor="10" kernelUnitLength="1" kernelMatrix="5 4 0 -4 -5 8 10 0 -10 -8 10 20 0 -20 -10 8 10 0 -10 -8 5 4 0 -4 -5" edgeMode="duplicate" in="grayscale" resullt="sobel_0"/>
<feColorMatrix type="matrix" values="-1 0 0 0 1 0 -1 0 0 1 0 0 -1 0 1 0 0 0 1 0" in="sobel_0" result="sobel_0_inverted"/>   

<feConvolveMatrix order="5 5" bias="1" divisor="10" kernelUnitLength="1" kernelMatrix="-5 -4 0 4 5 -8 -10 0 10 8 -10 -20 0 20 10 -8 -10 0 10 8 -5 -4 0 4 5" edgeMode="duplicate" in="grayscale" resullt="sobel_180"/>
<feColorMatrix type="matrix" values="-1 0 0 0 1 0 -1 0 0 1 0 0 -1 0 1 0 0 0 1 0" in="sobel_180" result="sobel_180_inverted"/>    
    
<feBlend in="sobel_90_inverted" in2="sobel_270_inverted" mode="lighten" result="edges_h"/>
<feBlend in="sobel_0_inverted" in2="sobel_180_inverted" mode="lighten" result="edges_v"/>
<feBlend in="edges_h" in2="edges_v" mode="lighten" result="edges"/>
    
<feColorMatrix type="matrix" values="-1 0 0 0 1 0 -1 0 0 1 0 0 -1 0 1 0 0 0 1 0" in="edges" result="edges_inverted"/>

<feComponentTransfer in="edges_inverted" result="edges_cleaned">
<feFuncR type="table" tableValues="0 0 0 0 0 0 0 1 1 1"/>
<feFuncG type="table" tableValues="0 0 0 0 0 0 0 1 1 1"/>
<feFuncB type="table" tableValues="0 0 0 0 0 0 0 1 1 1"/>
</feComponentTransfer>    

<feBlend in="edges_cleaned" in2="bg_color_corrected" mode="multiply" result="out"/>

</filter>

    <filter id="blue">
                    <feGaussianBlur in="SourceGraphic" stdDeviation=".8" />
      <feConvolveMatrix  preserveAlpha="true"
          kernelMatrix="-2 -2 -2
                        -2 16 -2
                        -2 -2 -2"/>
        <filter id="EmbossFilter" >
    <feConvolveMatrix order="5 5"
      preserveAlpha="true" 
      kernelMatrix="-1 0 0 0 0 0 -2 0 0 0 0 0 3 0 0 0 0 0 0 0 0 0 0 0 0"/>
  </filter>

            <feColorMatrix
        type="matrix"
        values="0  0  0  0  0
                0  .95  0  0  0
                0  1 0  0  0
                0  0  0  1  0"/>

    </filter>
    
        <filter id="red">
              <feGaussianBlur in="SourceGraphic" stdDeviation=".8" />
      <feConvolveMatrix  preserveAlpha="true"
          kernelMatrix="-2 -2 -2
                        -2 16 -2
                        -2 -2 -2"/>
  <filter id="EmbossFilter" >
  <filter id="EmbossFilter" >
    <feConvolveMatrix order="5 5"
      preserveAlpha="true" 
      kernelMatrix="-1 0 0 0 0 0 -2 0 0 0 0 0 3 0 0 0 0 0 0 0 0 0 0 0 0"/>
  </filter>
  </filter>
            <feColorMatrix
        type="matrix"
        values="0  1  0  0  0
                0  0  0  0  0
                0  0  0  0  0
                0  0  0  1  0"/>

    </filter>
    
    <filter id="goovey">
    <feTurbulence type="fractalNoise" baseFrequency="0.01" numOctaves="1" result="warpper"/>
          <feColorMatrix in="warpper" type="hueRotate">
                <animate attributeType="XML" attributeName="values" values="0;110;150;210;360" dur="5s" repeatCount="indefinite"/>
          </feColorMatrix>
          <feDisplacementMap xChannelSelector="R" yChannelSelector="G" scale="70" in="SourceGraphic"/>
    </filter>
    
        <filter id="dotted">
     <feColorMatrix type="luminanceToAlpha" result="alpha"/>

     <feComponentTransfer in="alpha" result="a1">
      <feFuncA type="discrete" tableValues="1,1,1,0,1"/>
     </feComponentTransfer>
     <feComponentTransfer in="alpha" result="a2">
      <feFuncA type="discrete" tableValues="1,1,0,1,1"/>
     </feComponentTransfer>
     <feComponentTransfer in="alpha" result="a3">
      <feFuncA type="discrete" tableValues="1,0,1,1,1"/>
     </feComponentTransfer>
     <feComponentTransfer in="alpha" result="a4">
      <feFuncA type="discrete" tableValues="0,1,1,1,1"/>
     </feComponentTransfer>

     <feFlood id="dotBg" x="0" y="0" width="2" height="2" flood-color="#fff" flood-opacity="0" result="dotTileBg"/>
     <feFlood id="dotFr" x="0" y="0" width="1" height="1" flood-color="#000" result="dot"/>
     <feMerge result="dotUnit">
      <feMergeNode in="dotTileBg"/>
      <feMergeNode in="dot"/>
     </feMerge>

     <feTile in="dotUnit" x="0" y="0" width="500" height="500" result="dotTile1"/>
     <feOffset in="dotTile1" dx="1" dy="0" result="predotTile2"/>
     <feOffset in="dotTile1" dx="0" dy="1" result="predotTile3"/>
     <feMerge result="dotTile2">
      <feMergeNode in="dotTile1"/>
      <feMergeNode in="predotTile2"/>
     </feMerge>
     <feMerge result="dotTile3">
      <feMergeNode in="dotTile1"/>
      <feMergeNode in="predotTile2"/>
      <feMergeNode in="predotTile3"/>
     </feMerge>
     <feFlood flood-color="black" result="dotTile4"/>

     <feComposite in="dotTile1" in2="a1" operator="out" result="b1"/>
     <feComposite in="dotTile2" in2="a2" operator="out" result="b2"/>
     <feComposite in="dotTile3" in2="a3" operator="out" result="b3"/>
     <feComposite in="dotTile4" in2="a4" operator="out" result="b4"/>
     <feMerge>
      <feMergeNode in="b1"/>
      <feMergeNode in="b2"/>
      <feMergeNode in="b3"/>
      <feMergeNode in="b4"/>
     </feMerge>
    </filter>
    
    <filter id="sharp-edges"> <feConvolveMatrix order="3 3" preserveAlpha="true" divisor="1" bias="0" kernelMatrix="-1,-1,-1 -1,9,-1 -1,-1,-1" /></filter>
  
<!--  <filter id='3d'>-->
<!--    <feGaussianBlur in="SourceAlpha" stdDeviation="9"/>-->
<!--    <feSpecularLighting surfaceScale="3" specularConstant=".75" specularExponent="17" lighting-color="#fefefe">-->
<!--            <fePointLight x="50%" y="-30000" z="20000"/>-->
<!--      </feSpecularLighting>-->
<!--    <feComposite in2="SourceAlpha" operator="in"/>-->
<!--    <feComposite in="SourceGraphic" operator="arithmetic" -->
<!--          k1="0" k2="1" k3="1" k4="0"/>-->
<!--    </filter>-->

<filter id="led-screen">
<feFlood id="filterPxl" x="7" y="7" width="2" height="2" result="flood"></feFlood>
<feComposite id="filterCs" in="flood" in2="flood" operator="over" x="0" y="0" width="16" height="16" result="composite"></feComposite>
<feTile x="0" y="0" width="10000" height="10000" in="composite" result="tile"></feTile>
<feComposite in="SourceGraphic" in2="tile" operator="in"></feComposite>
<feMorphology id="filterMorph" operator="dilate" radius="7" in="composite2" result="morphology"></feMorphology>
<feImage id="filterCsImg" xlink:href="data:image/svg+xml,%3Csvg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cellipse ry='25' rx='25' id='svg_1' cy='50' cx='50' /%3E%3C/svg%3E" x="0" y="0" width="16" height="16" preserveAspectRatio="xMidYMid meet" crossorigin="anonymous" result="image"></feImage>
<feTile x="0" y="0" width="10000" height="10000" in="image" result="tile1"></feTile>
<feComposite in="morphology" in2="tile1" operator="in" result="composite3"></feComposite>

<feGaussianBlur stdDeviation="25" in="composite3" result="blur3" />

<feBlend mode="screen" in="blur3" in2="composite3" result="blend" />

<feComposite in="blend" in2="composite3" operator="over" result="composite5"></feComposite>

<!-- Canva -->
<feFlood flood-color="#000" id="canva" result="flood1"></feFlood>
<feMerge result="merge">
<feMergeNode in="flood1"></feMergeNode>
<feMergeNode in="composite6"></feMergeNode>
<feMergeNode in="composite5"></feMergeNode>
</feMerge>

</filter>
  <filter id="zebra">
    <feTurbulence type="turbulence" baseFrequency="0.03 0.01" numOctaves="2" seed="4" stitchTiles="stitch" result="turbulence"/>
  <feColorMatrix type="matrix" 
     values="1 0 0 0 0
             0 1 0 0 0
             0 0 1 0 0
             0 0 0 500 -100" 
   in="turbulence" result="colormatrix"/>
    <feFlood flood-color="#FFF" flood-opacity="1" result="flood"/>
    <feComposite in="flood" in2="colormatrix" operator="in" result="composite"/>
    <feComposite in="composite" in2="SourceGraphic" operator="in" result="composite1"/>
  </filter>
  
          <filter id="vesper"
                color-interpolation-filters="sRGB"
                filterUnits="objectBoundingBox"
                primitiveUnits="objectBoundingBox"
                x="0" y="0" width="100%" height="100%">
            <!--hue-rotate -->
            <feColorMatrix type="hueRotate" values="-10"/>
            <!-- contrast -->
            <feComponentTransfer>
                <feFuncR type="linear" slope="0.9" intercept="0.05"/>
                <feFuncG type="linear" slope="0.9" intercept="0.05"/>
                <feFuncB type="linear" slope="0.9" intercept="0.05"/>
            </feComponentTransfer>
            <!-- saturate -->
            <feColorMatrix type="saturate" values=".9"/>
            <!-- brightness -->
            <feComponentTransfer>
                <feFuncR type="linear" slope="1.2"/>
                <feFuncG type="linear" slope="1.2"/>
                <feFuncB type="linear" slope="1.2"/>
            </feComponentTransfer>
            <!-- sepia 0.1 -->
            <feColorMatrix result="hcsbs" type="matrix"
                         values="
                0.9393 0.0769 0.0189 0 0
                0.0349 0.9686 0.0168 0 0
                0.0272 0.0534 0.9131 0 0
                0 0 0 1 0"/>
            <feFlood flood-color="rgba(220, 250, 40, .1)" flood-opacity="1"/>
            <feBlend mode="darken" in2="hcsbs"/>
        </filter>
        
        <filter id="watercolor-v2"
<!-- procedural textures -->
<feTurbulence result="noise-lg"
type="fractalNoise" baseFrequency=".04" NumOctaves="2" seed="1458" />
<feTurbulence result="noise-md"
type="fractalNoise" baseFrequency=".2" NumOctaves="3" seed="7218" />

<!-- BaseGraphic w/ chroma variation -->
<feComposite result="BaseGraphic"
in="SourceGraphic" in2="noise-lg"
operator="arithmetic" k1="0.5" k2="0.6" k4="-.07" />

<!-- 1st layer of paint w/more water -->
<feMorphology id="water" result="layer-1"
in="BaseGraphic"
operator="dilate" radius="1" />
<feDisplacementMap result="layer-1"
in="layer-1" in2="noise-lg"
xChannelSelector="R" yChannelSelector="B" scale="2" />
<feDisplacementMap result="layer-1"
in="layer-1" in2="noise-md"
xChannelSelector="R" yChannelSelector="B" scale="4" />
<feDisplacementMap result="mask"
in="layer-1" in2="noise-lg"
xChannelSelector="A" yChannelSelector="A" scale="6" />
<feGaussianBlur result="mask"
in="mask" stdDeviation="1" />
<feComposite result="layer-1"
in="layer-1" in2="mask"
operator="arithmetic" k1="1.2" k2="-.25" k3="-.25" k4="0" />

<!-- 2nd layer of paint w/more pigment -->
<feDisplacementMap result="layer-2"
in="BaseGraphic" in2="noise-lg"
xChannelSelector="G" yChannelSelector="R" scale="4" />
<feDisplacementMap result="layer-2"
in="layer-2" in2="noise-md"
xChannelSelector="A" yChannelSelector="G" scale="2" />
<feDisplacementMap result="glow"
in="BaseGraphic" in2="noise-lg"
xChannelSelector="R" yChannelSelector="A" scale="16" />
<feMorphology result="glow-diff"
in="glow"
operator="erode" radius="1" />
<feComposite result="glow"
in="glow" in2="glow-diff"
operator="out" />
<feGaussianBlur result="glow"
in="glow" stdDeviation="1.6" />
<feComposite id="color" result="layer-2"
in="layer-2" in2="glow"
operator="arithmetic" k1="1.5" k2="0" k3=".3" />
<!-- k1="-.1" k2="1" k3="-.6" />-->

<!-- merge 'em all (like multiply) -->
<feComposite
in="layer-1" in2="layer-2"
operator="arithmetic" k1="-0.8" k2="0.8" k3="1.4" />
</filter>

<filter id="pixel-v2" x="0%" y="0%" width="160%" height="160%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="linearRGB">
  
	<feFlood flood-color="#000000" flood-opacity="1" x="0" y="0" width="1" height="1" result="flood"/>
  
  
	<feComposite class="compFilter" in="flood" in2="flood" operator="in" x="0%" y="0%" width="10" height="10" result="composite4"/>
  
  
	<feTile x="0" y="0" width="100%" height="100%" in="composite4" result="tile2"/>
 
  
	<feComposite in="SourceGraphic" in2="tile2" operator="in" x="0%" y="0%" width="100%" height="100%" result="composite5"/>
  
  
	<feMorphology class="morphFilter" operator="dilate" radius="5" x="0%" y="0%" width="100%" height="100%" in="composite5" result="morphology1"/>
  
  
  <feGaussianBlur result="blurOut" in="morphology1" stdDeviation="2" edgeMode="none"></feGaussianBlur>
  <feBlend in="morphology1" in2="blurOut" mode="lighten" result="blend"></feBlend>
<!--     <feBlend in="blend" in2="blurOut" mode="lighten"></feBlend> -->
</filter>

 <filter id='soft-paper' x='0%' y='0%' width='100%' height="100%" color-interpolation-filters="linearRGB">

      <feTurbulence type="fractalNoise" baseFrequency='0.75' result='noise' numOctaves="3" stitchTiles="stitch" />

      <feDiffuseLighting in='noise' lighting-color="#fff9f2" surfaceScale='0.8'>
        <fePointLight x="300" y="300" z="600" />
      </feDiffuseLighting>

      <feMerge x="0%" y="0%" width="100%" height="100%" result="merge">
        <feMergeNode in="diffuseLighting" />
        <feMergeNode in="SourceGraphic" />
      </feMerge>

    </filter>

    <filter id="watercolorFilter" x='0%' y='0%' width='100%' height="100%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="linearRGB">
      <feTurbulence type="fractalNoise" baseFrequency="0.05 0.05" numOctaves="5" seed="1" stitchTiles="stitch" result="turbulence" />
      <feDiffuseLighting surfaceScale="0.5" diffuseConstant="3.2" lighting-color="#ffffff" in="turbulence" result="diffuseLighting">
        <feDistantLight azimuth="150" elevation="16" />
      </feDiffuseLighting>
      <feTurbulence type="fractalNoise" baseFrequency="0.011 0.004" numOctaves="2" seed="3" stitchTiles="noStitch" result="turbulence1" />
      <feColorMatrix type="saturate" values="3" in="turbulence1" result="colormatrix" />
      <feColorMatrix type="matrix" values="2 0 0 0 0
0 1.5 0 0 0
0 0 2 0 0
0 0 0 2 0" in="colormatrix" result="colormatrix1" />

      <feBlend mode="multiply" in="diffuseLighting" in2="colormatrix1" result="blend" />
      <feMerge x="0%" y="0%" width="100%" height="100%" result="merge">
        <feMergeNode in="blend" />
        <feMergeNode in="SourceGraphic" />
      </feMerge>
    </filter>
    
      <filter id="sea-effect" x="-20%" y="-20%" width="140%" height="140%">
        <feTurbulence numOctaves="3" seed="2" baseFrequency="0.02 0.05"></feTurbulence>
        <feDisplacementMap scale="20" in="SourceGraphic"></feDisplacementMap>
      </filter>
      
       <filter id="redcoat" color-interpolation-filters="linearRGB">

            <feColorMatrix in="SourceGraphic" result="BigRed"type="matrix" values="0 0 0 0 0
            0 0 0 0 0
            0 0 0 0 0
            3.8 -4 -4 0 -0.5" >
                
               
              </feColorMatrix>
                     
                <feColorMatrix type="saturate" values="0" in="SourceGraphic" result="GreySource"/>
                          <feComposite operator="in" in="SourceGraphic" in2="BigRed" result="RedOriginal"/>
                
                <feComposite operator="atop" in="RedOriginal" in2="GreySource" result="final"/>
                
        </filter>
 <filter id="scatter">

      <feTurbulence baseFrequency=".2" type="fractalNoise" numOctaves="3"/>

      <feDisplacementMap in="SourceGraphic" xChannelSelector="G" yChannelSelector="B" scale="300"/>
      <feComposite operator="in" in2="finalMask"/>
    </filter>

         <filter id="squiggle">
            <feTurbulence type="fractalNoise" baseFrequency="0.01" numOctaves="3" seed="46">
              </feTurbulence>
            <feDisplacementMap xChannelSelector="R" yChannelSelector="G" in="SourceGraphic" scale="10" />
          </filter>

























    <linearGradient id="redg" x1="50%" y1="0%" x2="50%" y2="100%">
      <stop offset="0%" stop-color="red" />
      <stop offset="100%" stop-color="black" />
    </linearGradient>
    
    <linearGradient id="blueg" x1="0%" y1="100%" x2="75%" y2="50%">
      <stop offset="0%" stop-color="blue"  />
      <stop offset="100%" stop-color="black" />
    </linearGradient>
    
    <linearGradient id="greeng" x1="100%" y1="100%" x2="25%" y2="50%">
      <stop offset="0%" stop-color="green"  />
      <stop offset="100%" stop-color="black" />
    </linearGradient>
    
    <path id="rtrgl" d="M300 0 600 600 0 600Z" fill="url(#redg)"/> 
    <path id="gtrgl" d="M300 0 600 600 0 600Z" fill="url(#blueg)"/> 
    <path id="btrgl" d="M300 0 600 600 0 600Z" fill="url(#greeng)"/> 
    
    
 <filter id="pseudo3d">
      <feColorMatrix type="matrix" values=".5 .5 .5 0 0 
                                           .5 .5 .5 0 0
                                           .5 .5 .5 0 0
                                           0 0 0 1 0"/>
      <feGaussianBlur stdDeviation="2"/>
      <feDisplacementMap xChannelSelector="R" yChannelSelector="G" scale="0"
                         in="SourceGraphic">
        <animate attributeName="scale" values="0;100;0" dur="1s" repeatCount="indefinite"/>
        <feDisplacementMap>
    </filter>
    
     <circle id="two" cx="3" cy="3" r="0.5" fill="blue"/>
    <circle id="three" cx="3" cy="3" r="1" fill="blue"/>    
    <circle id="four" cx="3" cy="3" r="1.5" fill="blue"/>    
    <circle id="five" cx="3" cy="3" r="2" fill="blue"/>    
    <circle id="six" cx="3" cy="3" r="2.5" fill="black"/>    
    <circle id="seven" cx="3" cy="3" r="3" fill="black"/>
    <circle id="eight" cx="3" cy="3" r="3.5" fill="black"/>   
    
    <filter id="half-tone-luminance" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
      
      <!-- Generate half-tone screens -->
      
      <feImage width="3" height="3" xlink:href="#two"/>
      <feTile result="2dot"/>
      <feImage width="3" height="3" xlink:href="#three"/>
      <feTile result="3dot"/>
      <feImage width="3" height="3" xlink:href="#four"/>
      <feTile result="4dot"/>
      <feImage width="3" height="3" xlink:href="#five"/>
      <feTile result="5dot"/>
      <feImage width="3" height="3" xlink:href="#six"/>
      <feTile result="6dot"/>
      <feImage width="3" height="3" xlink:href="#seven"/>
      <feTile result="7dot"/>
      <feImage width="3" height="3" xlink:href="#eight"/>
      <feTile result="8dot"/>

      <!-- Generate luminance map & tweak gamma levels -->    
     <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="neg-lum-map"/>
      <feComponentTransfer in="neg-lum-map" result="contrast-lum-map">
        <feFuncA type="gamma" offset="-.1" amplitude="1.1" exponent="2">
          <animate attributeName="exponent" values="1.8;2.2;1.8" dur="5s" repeatCount="10" />
        </feFuncA>
      </feComponentTransfer>
    <feComponentTransfer result="lum-map">
      <feFuncA type="table" tableValues="1 0"/>
      </feComponentTransfer>
      
     <!-- Split luminance levels into separate images -->  
       <feComponentTransfer in="lum-map" result="2r-thresh">
         <feFuncA type="discrete" tableValues="0 1 0 0 0 0 0 0" />
      </feComponentTransfer>
       <feComponentTransfer in="lum-map" result="3r-thresh">
         <feFuncA type="discrete" tableValues="0 0 1 0 0 0 0 0" />
       </feComponentTransfer>
       <feComponentTransfer in="lum-map" result="4r-thresh">
         <feFuncA type="discrete" tableValues="0 0 0 1 0 0 0 0" />
       </feComponentTransfer>      
       <feComponentTransfer in="lum-map" result="5r-thresh">
         <feFuncA type="discrete" tableValues="0 0 0 0 1 0 0 0" />
       </feComponentTransfer>      
       <feComponentTransfer in="lum-map" result="6r-thresh">
         <feFuncA type="discrete" tableValues="0 0 0 0 0 1 0 0" />
       </feComponentTransfer>
       <feComponentTransfer in="lum-map" result="7r-thresh">
         <feFuncA type="discrete" tableValues="0 0 0 0 0 0 1 0" />
       </feComponentTransfer>
       <feComponentTransfer in="lum-map" result="8r-thresh">
         <feFuncA type="discrete" tableValues="0 0 0 0 0 0 0 1" />
       </feComponentTransfer>      
      
      <!-- Composite screens with luminance levels -->     
      <feComposite operator="in" in2="2r-thresh" in="2dot" result="lev2"/>
      <feComposite operator="in" in2="3r-thresh" in="3dot" result="lev3"/>   
      <feComposite operator="in" in2="4r-thresh" in="4dot" result="lev4"/>
      <feComposite operator="in" in2="5r-thresh" in="5dot" result="lev5"/>   
      <feComposite operator="in" in2="6r-thresh" in="6dot" result="lev6"/>
      <feComposite operator="in" in2="7r-thresh" in="7dot" result="lev7"/>  
      <feComposite operator="in" in2="8r-thresh" in="8dot" result="lev8"/>  

      <!-- Merge half-tone fragments together -->  
      <feMerge>
        <feMergeNode in="lev8"/>
        <feMergeNode in="lev7"/>
        <feMergeNode in="lev6"/>
        <feMergeNode in="lev5"/>
        <feMergeNode in="lev4"/>
        <feMergeNode in="lev3"/>
        <feMergeNode in="lev2"/>
      </feMerge>
      
      <!-- Clip to the original -->  
      <feComposite operator="in" in2="SourceGraphic"/>


    </filter>

<filter id="orton-effect">
    <feColorMatrix type="matrix" in="SourceGraphic" result="brighter" values="1.4 0 0 0 .1                                         0 1.4 0 0 .1                                         0 0 1.4 0 .1                                         0 0 0 1 0"/>
    <feGaussianBlur in="brighter" stdDeviation="3" result="brightblur"/>
  <feBlend mode="multiply" in="brighter" in2="brightblur"/>
</filter>

<filter id="orton-3x">
  <feColorMatrix type="matrix" in="SourceGraphic" result="brighter2" values="2 0 0 0 0                                         0 2 0 0 .0                                         0 0 2 0 .0                                         0 0 0 1 0"/>
    <feGaussianBlur in="brighter2" stdDeviation="1" result="brightblur2"/>
  <feBlend mode="multiply" in="brighter2" in2="brightblur2" result="inter2"/>
  
  <feColorMatrix type="matrix" in="inter2" result="brighter3" values="2 0 0 0 .0                                         0 2 0 0 .0                                         0 0 2 0 .0                                         0 0 0 1 0"/>
    <feGaussianBlur in="brighter3" stdDeviation="1" result="brightblur3"/>
  <feBlend mode="multiply" in="brighter3" in2="brightblur3" result="inter3"/>
  
  <feColorMatrix type="matrix" in="inter3" result="brighter4" values="2 0 0 0 .0                                         0 2 0 0 .0                                         0 0 2 0 .0                                         0 0 0 1 0"/>
    <feGaussianBlur in="brighter4" stdDeviation="1" result="brightblur4"/>
  <feBlend mode="multiply" in="brighter4" in2="brightblur4" result="inter5"/>
  
</filter>


        <filter id="old-map" x="0%" y="0%" width="100%" height="100%">
            <feTurbulence id="b1" type="fractalNoise" baseFrequency="0.075" numOctaves="3"/>
               <feColorMatrix type="matrix" values=".33 .33 .33 0 0 
                                                 .33 .33 .33 0 0 
                                                 .33 .33 .33 0 0 
                                                 0 0 0 1 0"/>
             <feComponentTransfer result="texture">
                <feFuncA type="discrete" tableValues="0 0 0 0 0 0 0 .15 .7 .9 1 1"/>
            </feComponentTransfer>
          <feComponentTransfer result="colored-texture">
            <feFuncR type="discrete" tableValues="0 .93 .93 .93 .93"/>
            <feFuncG type="discrete" tableValues="0 .84 .84 .84 .84"/>
            <feFuncB type="discrete" tableValues="0 .63 .63 .63 .63"/>
            </feComponentTransfer>
              <feBlend mode="darken" in="SourceGraphic" in2="colored-texture"/>
        </filter>   
        
         <filter id="gothamish" color-interpolation-filters="sRGB">
      <feComponentTransfer in="SourceGraphic" result="midtoneContrast">
        <feFuncR type="table" tableValues="0 0.05 0.1 0.2 0.3  0.5 0.7 0.8  0.9 0.95 1.0"/>
     </feComponentTransfer>
     <feColorMatrix in="midtoneContrast" result="redBWandblue" type="matrix" 
                               values="1 0 0 0 0
                                       1 0 0 0 0
                                       1 0 0 0 0.03
                                       0 0 0 1 0"/>
     <feGaussianBlur in="redBWandblue" stdDeviation="1" result="blurMask"/>
     <feComposite operator="arithmetic" in="redBWandblue" in2="blurMask" k2="1.3" k3="-0.3" result="postsharp"/>
     <feComponentTransfer result="finalImage" in="postsharp">
       <feFuncB type="table" tableValues="0 0.047 0.118 0.251 0.318 0.392 0.42 0.439 0.475 0.561 0.58 0.627 0.671 0.733 0.847 0.925 1"/>
     </feComponentTransfer>
  </filter>


     <filter id="highlight-blur" color-interpolation-filters="sRGB">

<feColorMatrix type="luminanceToAlpha" in="SourceGraphic" result="lumMap"/>
    <feComponentTransfer in="lumMap" result="highlightMask">
      <feFuncA type="discrete" tableValues="0 0 0 0 0 0 0 1"/>
    </feComponentTransfer>
    
     <feComposite operator="in" in="SourceGraphic" in2="highlightMask" result="highlights"/>

      <feGaussianBlur in="highlights" stdDeviation="3" result="highBlur"/>

  <feComposite operator="over" in="highBlur" in2="SourceGraphic" result="final"/>

</filter>


  <filter id="broken">
    <feTurbulence type="turbulence" baseFrequency="0.002 0.008" numOctaves="2" seed="2" stitchTiles="stitch" result="turbulence"/>
    <feColorMatrix type="saturate" values="30" in="turbulence" result="colormatrix"/>
    <feColorMatrix type="matrix" values="1 0 0 0 0
  0 1 0 0 0
  0 0 1 0 0
  0 0 0 150 -15" in="colormatrix" result="colormatrix1"/>
    <feComposite in="SourceGraphic" in2="colormatrix1" operator="in" result="composite"/>
    <feDisplacementMap in="SourceGraphic" in2="colormatrix1" scale="15" xChannelSelector="R" yChannelSelector="A" result="displacementMap"/>
</filter>

    <filter id="tile-effect" x="0" y="0" width="200%" height="200%" filterUnits="objectBoundingBox">
      <!-- Limit the tile area using feCrop in modern specs or just use feTile directly -->
      <feTile in="SourceGraphic" result="tiled" />
    </filter>

<filter id="heat-map-2">
    <fecomponenttransfer>
        <feFuncR type="table" tableValues="0.17 0.09 0.19 0.95 0.99" />
        <feFuncG type="table" tableValues="0.24 0.35 0.67 0.88 0.03" />
        <feFuncB type="table" tableValues="0.36 0.65 0.31 0.13 0.08" />
    </fecomponenttransfer>
</filter>


<filter id="duotone01" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feColorMatrix type="matrix" values=".33 .33 .33 0 0 .33 .33 .33 0 0 .33 .33 .33 0 0 0 0 0 1 0" in="SourceGraphic" result="colormatrix"/>
    <feComponentTransfer in="colormatrix" result="componentTransfer">
      <feFuncR type="table" tableValues="0.97 0.99"/>
      <feFuncG type="table" tableValues="0.17 0.94"/>
      <feFuncB type="table" tableValues="0.41 0.27"/>
      <feFuncA type="table" tableValues="0 1"/>
    </feComponentTransfer>
    <feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"/>
  </filter>
  <filter id="duotone02" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feColorMatrix type="matrix" values=".33 .33 .33 0 0 .33 .33 .33 0 0 .33 .33 .33 0 0 0 0 0 1 0" in="SourceGraphic" result="colormatrix"/>
    <feComponentTransfer in="colormatrix" result="componentTransfer">
    		<feFuncR type="table" tableValues="0 0.58"/>
		<feFuncG type="table" tableValues="0.75 0.98"/>
		<feFuncB type="table" tableValues="0.84 1"/>
		<feFuncA type="table" tableValues="0 1"/>
    </feComponentTransfer>
    <feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"/>
  </filter>
  <filter id="duotone03" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feColorMatrix type="matrix" values=".33 .33 .33 0 0 .33 .33 .33 0 0 .33 .33 .33 0 0 0 0 0 1 0" in="SourceGraphic" result="colormatrix"/>
    <feComponentTransfer in="colormatrix" result="componentTransfer">
      <feFuncR type="table" tableValues="0.65 1"/>
      <feFuncG type="table" tableValues="0.65 1"/>
      <feFuncB type="table" tableValues="0.65 1"/>
      <feFuncA type="table" tableValues="0 1"/>
    </feComponentTransfer>
    <feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"/>
  </filter>
  <filter id="duotone04" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feColorMatrix type="matrix" values="0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0 0 1 0" in="SourceGraphic" result="colormatrix"/>
    <feComponentTransfer in="colormatrix" result="componentTransfer">
      <feFuncR type="table" tableValues="0 0.5" />
      <feFuncG type="table" tableValues="0 0.5"/>
      <feFuncB type="table" tableValues="0 0.5"/>
      <feFuncA type="table" tableValues="0 1"/>
      </feComponentTransfer>
    <feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"/>
  </filter>
  <filter id="duotone05" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feColorMatrix type="matrix" values=".33 .33 .33 0 0 .33 .33 .33 0 0 .33 .33 .33 0 0 0 0 0 1 0" in="SourceGraphic" result="colormatrix"/>
    <feComponentTransfer in="colormatrix" result="componentTransfer">
      <feFuncR type="table" tableValues="0.13 1"/>
      <feFuncG type="table" tableValues="0.3 0.47"/>
      <feFuncB type="table" tableValues="1 0.96"/>
      <feFuncA type="table" tableValues="0 1"/>
    </feComponentTransfer>
    <feBlend mode="soft-light" in="componentTransfer" in2="SourceGraphic" result="blend"/>
  </filter>

<filter id="light-effect" x="-50%" y="-50%" width="200%" height="200%">
    <feDiffuseLighting in="SourceGraphic" lighting-color="white" result="light"
      surfaceScale="5" diffuseConstant="1">
      <feDistantLight azimuth="45" elevation="45" />
    </feDiffuseLighting>
    <feComposite in="SourceGraphic" in2="light" operator="arithmetic"
      k1="0" k2="1" k3="1" k4="0" />
  </filter>
  
          <filter id="b-and-w" color-interpolation-filters="sRGB">
              <feColorMatrix type="saturate" values="0" result="grayscale"/>
          
              <feComponentTransfer in="grayscale">
                <feFuncR type="discrete" tableValues="0 0 0 0 0 1 1 1 1 1" />
                <feFuncG type="discrete" tableValues="0 0 0 0 0 1 1 1 1 1" />
                <feFuncB type="discrete" tableValues="0 0 0 0 0 1 1 1 1 1" />
              </feComponentTransfer>
            </filter>
            
              <filter id="outline-colored" x="-20%" y="-20%" width="140%" height="140%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feTurbulence type="fractalNoise" baseFrequency="0.01" numOctaves="1" seed="2" stitchTiles="stitch" result="turbulence" />
    <feColorMatrix type="matrix" values="1 0 0 0 0
0 1 0 0 0
0 0 1 0 0
0 0 0 0 1" in="turbulence" result="colormatrix" />
    <feColorMatrix type="saturate" values="4" in="colormatrix" result="colormatrix1" />
    <feComponentTransfer in="colormatrix1" result="componentTransfer">
      <feFuncR type="table" tableValues="1 0 -1" />
      <feFuncG type="table" tableValues="1 0 1" />
      <feFuncB type="table" tableValues="1 -1 1" />
      <feFuncA type="identity" />
    </feComponentTransfer>
    <feMorphology operator="dilate" radius="2 2" in="SourceAlpha" result="morphology" />
    <feFlood flood-color="#ffffff" flood-opacity="1" result="flood" />
    <feComposite in="flood" in2="morphology" operator="in" result="composite" />
    <feComposite in="composite" in2="SourceAlpha" operator="out" result="composite1" />
    <feComposite in="componentTransfer" in2="composite1" operator="in" result="composite2" />
  </filter>
   <filter id="drip" x="-20%" y="-20%" width="140%" height="140%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
	<feGaussianBlur stdDeviation="0 10" in="SourceGraphic" edgeMode="none" result="blur"/>
	<feTurbulence type="turbulence" baseFrequency="0.09 0.005" numOctaves="1" seed="2" stitchTiles="stitch" result="turbulence"/>
	<feComposite in="turbulence" in2="blur" operator="in" result="composite"/>
	<feColorMatrix type="matrix" values="1 0 0 0 0
0 1 0 0 0
0 0 1 0 0
0 0 0 100 -10" in="composite" result="colormatrix"/>
	<feMorphology operator="dilate" radius="0 36" in="colormatrix" result="morphology"/>
	<feOffset dx="0" dy="45" in="morphology" result="offset"/>
	<feComposite in="offset" in2="SourceGraphic" operator="xor" result="composite1"/>
	<feFlood flood-color="teal" flood-opacity="1" result="flood"/>
	<feComposite in="flood" in2="composite1" operator="in" result="composite2"/>
</filter>


<filter id="engraving-effect" color-interpolation-filters="sRGB" x="0" y="0" width="100%" height="100%">
  <!-- Luminance -->
  <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />
  <feComponentTransfer in="lum" result="lum-map">
    <feFuncA type="table" tableValues="1 0" />
  </feComponentTransfer>

  <!-- Line patterns -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg width='4' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0H4' stroke='black'/%3E%3C/svg%3E" result="horizontal" />
  <feTile in="horizontal" result="tile-horizontal" />
  
  <feImage xlink:href="data:image/svg+xml,%3Csvg width='4' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0V4' stroke='black'/%3E%3C/svg%3E" result="vertical" />
  <feTile in="vertical" result="tile-vertical" />

  <feImage xlink:href="data:image/svg+xml,%3Csvg width='4' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 4L4 0' stroke='black'/%3E%3C/svg%3E" result="diag1" />
  <feTile in="diag1" result="tile-diag1" />

  <feImage xlink:href="data:image/svg+xml,%3Csvg width='4' height='4' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0L4 4' stroke='black'/%3E%3C/svg%3E" result="diag2" />
  <feTile in="diag2" result="tile-diag2" />

  <!-- Thresholds -->
  <feComponentTransfer in="lum-map" result="thresh1">
    <feFuncA type="discrete" tableValues="1 0 0 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh2">
    <feFuncA type="discrete" tableValues="0 1 0 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh3">
    <feFuncA type="discrete" tableValues="0 0 1 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh4">
    <feFuncA type="discrete" tableValues="0 0 0 1" />
  </feComponentTransfer>

  <!-- Combine -->
  <feComposite in="thresh1" in2="tile-horizontal" operator="in" result="level1" />
  <feComposite in="thresh2" in2="tile-vertical" operator="in" result="level2" />
  <feComposite in="thresh3" in2="tile-diag1" operator="in" result="level3" />
  <feComposite in="thresh4" in2="tile-diag2" operator="in" result="level4" />

  <!-- Merge -->
  <feMerge result="engraved">
    <feMergeNode in="level4" />
    <feMergeNode in="level3" />
    <feMergeNode in="level2" />
    <feMergeNode in="level1" />
  </feMerge>

  <!-- Clip with original shape -->
<!--  <feComposite in="engraved" in2="SourceGraphic" operator="in" />-->
  <feBlend in="engraved" in2="SourceGraphic" mode="multiply" />

</filter>
<filter id="engraving-2-effect" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
  <!-- Convert luminance to alpha -->
  <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />
  <feComponentTransfer in="lum" result="lum-map">
    <feFuncA type="table" tableValues="1 0" />
  </feComponentTransfer>

  <!-- Horizontal lines -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%2716%27%20height%3D%2716%27%3E%3Cpath%20d%3D%27M0%208H16%27%20stroke%3D%27black%27%20stroke-width%3D%270.5%27/%3E%3C/svg%3E" result="horizontal" />
  <feTile in="horizontal" result="tile-horizontal" />

  <!-- Vertical lines -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%2716%27%20height%3D%2716%27%3E%3Cpath%20d%3D%27M8%200V16%27%20stroke%3D%27black%27%20stroke-width%3D%270.5%27/%3E%3C/svg%3E" result="vertical" />
  <feTile in="vertical" result="tile-vertical" />

  <!-- Diagonal lines: \\ -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%2716%27%20height%3D%2716%27%3E%3Cpath%20d%3D%27M0%2016L16%200%27%20stroke%3D%27black%27%20stroke-width%3D%270.5%27/%3E%3C/svg%3E" result="diag1" />
  <feTile in="diag1" result="tile-diag1" />

  <!-- Diagonal lines: / -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20width%3D%2716%27%20height%3D%2716%27%3E%3Cpath%20d%3D%27M0%200L16%2016%27%20stroke%3D%27black%27%20stroke-width%3D%270.5%27/%3E%3C/svg%3E" result="diag2" />
  <feTile in="diag2" result="tile-diag2" />

  <!-- 4 levels of thresholds -->
  <feComponentTransfer in="lum-map" result="thresh1">
    <feFuncA type="discrete" tableValues="1 0 0 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh2">
    <feFuncA type="discrete" tableValues="0 1 0 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh3">
    <feFuncA type="discrete" tableValues="0 0 1 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh4">
    <feFuncA type="discrete" tableValues="0 0 0 1" />
  </feComponentTransfer>

  <!-- Combine patterns with thresholds -->
  <feComposite in="thresh1" in2="tile-horizontal" operator="in" result="level1" />
  <feComposite in="thresh2" in2="tile-vertical" operator="in" result="level2" />
  <feComposite in="thresh3" in2="tile-diag1" operator="in" result="level3" />
  <feComposite in="thresh4" in2="tile-diag2" operator="in" result="level4" />

  <!-- Merge patterns -->
  <feMerge result="engraved">
    <feMergeNode in="level4" />
    <feMergeNode in="level3" />
    <feMergeNode in="level2" />
    <feMergeNode in="level1" />
  </feMerge>

  <!-- Clip final result to shape of original graphic -->
  <feComposite in="engraved" in2="SourceGraphic" operator="in" />
</filter>

<filter id="neon-engraving-effect" color-interpolation-filters="sRGB" x="0" y="0" width="100%" height="100%">
  <!-- Luminance -->
  <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />
  <feComponentTransfer in="lum" result="lum-map">
    <feFuncA type="table" tableValues="1 0" />
  </feComponentTransfer>

  <!-- Neon Horizontal line - magenta -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='6'%20height='6'%3E%3Cpath%20d='M0%203H6'%20stroke='%23FF00FF'%20stroke-width='1.5'%20stroke-linecap='round'/%3E%3C/svg%3E" result="neon-horizontal" />
  <feTile in="neon-horizontal" result="tile-neon-horizontal" />

  <!-- Neon Vertical line - cyan -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='6'%20height='6'%3E%3Cpath%20d='M3%200V6'%20stroke='%2300FFFF'%20stroke-width='1.5'%20stroke-linecap='round'/%3E%3C/svg%3E" result="neon-vertical" />
  <feTile in="neon-vertical" result="tile-neon-vertical" />

  <!-- Neon Circle - yellow -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='6'%20height='6'%3E%3Ccircle%20cx='3'%20cy='3'%20r='1.2'%20fill='%23FFFF33'/%3E%3C/svg%3E" result="neon-circle" />
  <feTile in="neon-circle" result="tile-neon-circle" />

  <!-- Neon Diagonal line - magenta -->
  <feImage xlink:href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='6'%20height='6'%3E%3Cpath%20d='M0%206L6%200'%20stroke='%23FF33FF'%20stroke-width='1.5'%20stroke-linecap='round'/%3E%3C/svg%3E" result="neon-diag2" />
  <feTile in="neon-diag2" result="tile-neon-diag2" />

  <!-- Thresholds -->
  <feComponentTransfer in="lum-map" result="thresh1">
    <feFuncA type="discrete" tableValues="1 0 0 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh2">
    <feFuncA type="discrete" tableValues="0 1 0 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh3">
    <feFuncA type="discrete" tableValues="0 0 1 0" />
  </feComponentTransfer>
  <feComponentTransfer in="lum-map" result="thresh4">
    <feFuncA type="discrete" tableValues="0 0 0 1" />
  </feComponentTransfer>

  <!-- Composite masked neon tiles -->
  <feComposite in="thresh1" in2="tile-neon-horizontal" operator="in" result="level1" />
  <feComposite in="thresh2" in2="tile-neon-vertical" operator="in" result="level2" />
  <feComposite in="thresh3" in2="tile-neon-circle" operator="in" result="level3" />
  <feComposite in="thresh4" in2="tile-neon-diag2" operator="in" result="level4" />

  <!-- Merge all neon effects -->
  <feMerge result="neon-patterns">
    <feMergeNode in="level4" />
    <feMergeNode in="level3" />
    <feMergeNode in="level2" />
    <feMergeNode in="level1" />
  </feMerge>

  <!-- Blend neon patterns over original -->
  <feBlend in="neon-patterns" in2="SourceGraphic" mode="screen" />
</filter>



<filter id="comic-ink-outline" x="0" y="0" width="100%" height="100%" color-interpolation-filters="sRGB">
  <!-- Step 1: Convert to grayscale -->
  <feColorMatrix in="SourceGraphic" type="matrix" result="gray"
    values="0.2126 0.7152 0.0722 0 0
            0.2126 0.7152 0.0722 0 0
            0.2126 0.7152 0.0722 0 0
            0      0      0      1 0" />

  <!-- Step 2: Edge detection via Laplacian kernel -->
  <feConvolveMatrix in="gray" result="edges"
    kernelMatrix="-1 -1 -1
                  -1  8 -1
                  -1 -1 -1"
    divisor="1"
    bias="0" />

  <!-- Step 3: Threshold to create clean black lines -->
  <feComponentTransfer in="edges" result="edges-thresh">
    <feFuncR type="table" tableValues="0 0 0 0 1 1 1 1" />
    <feFuncG type="table" tableValues="0 0 0 0 1 1 1 1" />
    <feFuncB type="table" tableValues="0 0 0 0 1 1 1 1" />
  </feComponentTransfer>

  <!-- Optional: Posterize the original -->
  <feComponentTransfer in="SourceGraphic" result="posterized">
    <feFuncR type="discrete" tableValues="0 0.5 1" />
    <feFuncG type="discrete" tableValues="0 0.5 1" />
    <feFuncB type="discrete" tableValues="0 0.5 1" />
  </feComponentTransfer>

  <!-- Step 4: Composite edges on top of posterized image -->
  <feComposite in="edges-thresh" in2="posterized" operator="over" />
</filter>
<filter id="halftone-lines" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
  <!-- Convert image to luminance mask -->
  <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />

  <!-- Create horizontal line pattern as data URI -->
  <feImage width="4" height="4" result="lines"
    xlink:href="data:image/svg+xml;utf8,
      <svg xmlns='http://www.w3.org/2000/svg' width='4' height='4'>
        <rect width='4' height='2' fill='black'/>
        <rect y='2' width='4' height='2' fill='white'/>
      </svg>" />

  <!-- Tile pattern -->
  <feTile in="lines" result="pattern" />

  <!-- Apply the pattern only where luminance is visible -->
  <feComposite in="pattern" in2="lum" operator="in" result="masked-pattern" />

  <!-- Blend with the original shape -->
  <feComposite in="masked-pattern" in2="SourceGraphic" operator="in" />
</filter>

<filter id="halftone-wave" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
  <!-- Base grayscale conversion -->
  <feColorMatrix type="luminanceToAlpha" in="SourceGraphic" result="gray" />

  <!-- Wave pattern generation -->
  <feTurbulence type="turbulence" baseFrequency="0.05 0.2" numOctaves="1" result="waves" />

  <!-- Displace luminance by waves -->
  <feDisplacementMap in="gray" in2="waves" scale="20" xChannelSelector="R" yChannelSelector="G" result="wavy" />

  <!-- Threshold to enhance contrast -->
  <feComponentTransfer in="wavy" result="contrast">
    <feFuncA type="table" tableValues="1 0" />
  </feComponentTransfer>
  
  <feMerge>
  <feMergeNode in="SourceGraphic"></feMergeNode>
  <feMergeNode in="contrast"></feMergeNode>
</feMerge>
</filter>

    <filter id="halftone-cmyk" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
      <!-- Cyan -->
      <feImage result="cyan-dot" width="8" height="8"
        xlink:href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8Y2lyY2xlIGN4PSI0LjAiIGN5PSI0LjAiIHI9IjEuNSIgZmlsbD0iY3lhbiIgLz4KPC9zdmc+" />
      <feTile in="cyan-dot" result="cyan-tiles" />
      <feColorMatrix in="cyan-tiles" type="matrix"
        values="1 0 0 0 0
                0 0 0 0 0
                0 0 0 0 0
                0 0 0 1 0"
        result="cyan" />

      <!-- Magenta -->
      <feImage result="magenta-dot" width="8" height="8"
        xlink:href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8Y2lyY2xlIGN4PSI0LjAiIGN5PSI0LjAiIHI9IjEuNSIgZmlsbD0ibWFnZW50YSIgLz4KPC9zdmc+" />
      <feTile in="magenta-dot" result="magenta-tiles" />
      <feColorMatrix in="magenta-tiles" type="matrix"
        values="0 0 0 0 0
                0 1 0 0 0
                0 0 0 0 0
                0 0 0 1 0"
        result="magenta" />

      <!-- Yellow -->
      <feImage result="yellow-dot" width="8" height="8"
        xlink:href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8Y2lyY2xlIGN4PSI0LjAiIGN5PSI0LjAiIHI9IjEuNSIgZmlsbD0ieWVsbG93IiAvPgo8L3N2Zz4=" />
      <feTile in="yellow-dot" result="yellow-tiles" />
      <feColorMatrix in="yellow-tiles" type="matrix"
        values="0 0 0 0 0
                0 0 0 0 0
                0 0 1 0 0
                0 0 0 1 0"
        result="yellow" />

      <!-- Black -->
      <feImage result="black-dot" width="8" height="8"
        xlink:href="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8Y2lyY2xlIGN4PSI0LjAiIGN5PSI0LjAiIHI9IjEuNSIgZmlsbD0iYmxhY2siIC8+Cjwvc3ZnPg==" />
      <feTile in="black-dot" result="black-tiles" />
      <feColorMatrix in="black-tiles" type="matrix"
        values="0 0 0 0 0
                0 0 0 0 0
                0 0 0 0 0
                0 0 0 1 0"
        result="black" />

      <!-- Merge CMYK dots -->
      <feMerge result="cmyk-halftone">
        <feMergeNode in="cyan" />
        <feMergeNode in="magenta" />
        <feMergeNode in="yellow" />
        <feMergeNode in="black" />
      </feMerge>

      <!-- Mask with original shape -->
      <feComposite in="cmyk-halftone" in2="SourceGraphic" operator="in" />
    </filter>




            </defs>
        </svg>
    </span>
`;function Ue(e,t=[],n){let r=T(t),i=Number(r.pastelStrength)||.5,a=e.querySelector(`feColorMatrix`),o=e.querySelector(`feFuncR`),s=e.querySelector(`feFuncG`),c=e.querySelector(`feFuncB`);if(a){let e=1-i,t=[e+.1,.1,.1,0,0,.1,e+.1,.1,0,0,.1,.1,e+.1,0,0,0,0,0,1,0];a.setAttribute(`values`,t.join(` `))}return o&&o.setAttribute(`amplitude`,1+i),s&&s.setAttribute(`amplitude`,1+i),c&&c.setAttribute(`amplitude`,1+i),`pastel-effect`}var We=`
<span class="container container-filter"
      data-filter
      data-filter-type="svg"
      data-filter-name="pastel-effect"
      data-requires-checkbox="true">
      
     <div class="filter-menu">
        <label class="label label-checkbox">
            <input id="pastel" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
            <span class="label-span" data-i18n="Pastel"></span>
        </label>
        
        <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
            ${w.arrowRepeat}
        </span>
    </div>
    
    <fieldset class="fieldset fieldset-filter wrapper-field-composed">
        <label class="label label-small" for="pastel-strength" data-i18n="Pastel strength"></label>
        <input type="range" id="pastel-strength" class="input input-range"
               data-modifier="pastel-strength" min="0" max="1" step="0.01" value="0.5" data-default="0.5">
        <span class="wrapper">
            <input type="number" class="input input-number number-value"
                   data-modifier="pastel-strength" min="0" max="1" step="0.01" value="0.5" data-default="0.5">
            <label data-i18n="val"></label>
        </span>
    </fieldset>
    <svg class="svgFilter" color-interpolation-filters="sRGB">
        <defs>
            <filter id="pastel-effect" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
              <!-- Step 1: Slight desaturation with a soft hue bias -->
              <feColorMatrix type="matrix"
                values="0.9 0.05 0.05 0 0.03
                        0.05 0.85 0.1  0 0.03
                        0.1  0.1  0.85 0 0.03
                        0    0    0    1 0"
                result="tinted" />
            
              <!-- Step 2: Brighten using exponent < 1 + offset -->
              <feComponentTransfer in="tinted" result="bright">
                <feFuncR type="gamma" amplitude="1" exponent="0.75" offset="0.04"/>
                <feFuncG type="gamma" amplitude="1" exponent="0.75" offset="0.04"/>
                <feFuncB type="gamma" amplitude="1" exponent="0.75" offset="0.04"/>
              </feComponentTransfer>
            
              <!-- Step 3: Overlay a soft light wash (e.g., pink or peach) -->
              <feFlood flood-color="#ffe6f0" flood-opacity="0.2" result="tint"/>
              <feBlend in="bright" in2="tint" mode="screen" result="pastel"/>
            
              <!-- Output -->
              <feComposite in="pastel" in2="SourceAlpha" operator="in"/>
            </filter>

        
        
<!--        <filter id="pastel-effect" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">-->
<!--  &lt;!&ndash; Step 1: Slight desaturation with a soft pink/peach hue &ndash;&gt;-->
<!--  <feColorMatrix in="SourceGraphic" type="matrix"-->
<!--    values="0.85 0.05 0.1 0 0-->
<!--            0.05 0.85 0.1 0 0-->
<!--            0.1  0.1  0.8 0 0-->
<!--            0    0    0   1 0"-->
<!--    result="soft" />-->

<!--  &lt;!&ndash; Step 2: Brighten midtones &ndash;&gt;-->
<!--  <feComponentTransfer in="soft" result="bright">-->
<!--    <feFuncR type="gamma" exponent="0.8" offset="0.03" />-->
<!--    <feFuncG type="gamma" exponent="0.8" offset="0.03" />-->
<!--    <feFuncB type="gamma" exponent="0.8" offset="0.03" />-->
<!--  </feComponentTransfer>-->

<!--  &lt;!&ndash; Step 3 (optional): Add a soft saturation boost &ndash;&gt;-->
<!--  <feColorMatrix type="saturate" values="1.2" result="pastel" />-->

<!--  &lt;!&ndash; Output &ndash;&gt;-->
<!--  <feComposite in="pastel" in2="SourceAlpha" operator="in" />-->
<!--</filter>-->
<!--            <filter id="pastel-effect" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">-->
<!--                &lt;!&ndash; Desaturate and soften color matrix &ndash;&gt;-->
<!--                <feColorMatrix id="pastel-colormatrix" type="matrix"-->
<!--                    values="0.8 0.1 0.1 0 0-->
<!--                            0.1 0.8 0.1 0 0-->
<!--                            0.1 0.1 0.8 0 0-->
<!--                            0   0   0   1 0"-->
<!--                    result="soft" />-->
<!--                -->
<!--                &lt;!&ndash; Brighten all channels &ndash;&gt;-->
<!--               <feComponentTransfer in="soft" result="pastel">-->
<!--                    <feFuncR type="gamma" amplitude="1" exponent="0.7" offset="0.05" />-->
<!--                    <feFuncG type="gamma" amplitude="1" exponent="0.7" offset="0.05" />-->
<!--                    <feFuncB type="gamma" amplitude="1" exponent="0.7" offset="0.05" />-->
<!--                </feComponentTransfer>-->
<!--            </filter>-->
        </defs>
    </svg>
</span>
`,F={naturalWidth:null,naturalHeight:null,aspectRatio:null,orientation:null};function Ge(e,t=[],n,r){let i=r.getImageEditorInstance();return console.log(i),console.log(i.canvas),console.log(i.canvasImageWidth),console.log(i.canvasImageHeight),n?n.innerHTML=`<p>hello world</p>`:alert(`no container`),`half-tone`}var Ke=`
<span class="container container-filter"
      data-filter
      data-filter-type="svg"
      data-filter-name="half-tone-effect"
      data-requires-checkbox="true">
    
    <div class="filter-menu">
        <label class="label label-checkbox">
            <input id="cartoon" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
            <span class="label-span" data-i18n="Half tone 1"></span>
        </label>
        <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
            ${w.arrowRepeat}
        </span>
    </div>
    
<!--    <fieldset class="fieldset fieldset-filter wrapper-field-composed">-->
<!--        <label class="label label-small" for="dot-size-scale" data-i18n="Dot size"></label>-->
<!--        <input type="range" id="dot-size-scale" class="input input-range" data-modifier="dot-size-scale"-->
<!--               min="0.5" max="10" step="0.1" value="1" data-default="4">-->
<!--        <span class="wrapper">-->
<!--            <input type="number" class="input input-number number-value" data-modifier="dot-size-scale"-->
<!--                   min="0.5" max="10" step="0.1" value="1" data-default="4">-->
<!--            <label data-i18n="val"></label>-->
<!--        </span>-->
<!--    </fieldset>-->
<!--    -->
<!--    <fieldset class="fieldset fieldset-filter wrapper-field-composed">-->
<!--        <label for="halftone-color" data-i18n="Color">Color</label>-->
<!--        <input id="halftone-color" type="color" class="input input-color" data-modifier="half-tone-color" value="#004F67" data-default="#004F67">-->
<!--    </fieldset>-->

    <svg class="svgFilter">
        <defs>
            <filter id="half-tone" color-interpolation-filters="sRGB" primitiveUnits="userSpaceOnUse">

              <!-- Inline <feImage> per dot size -->
            ${[.5,1,1.5,2,2.5,3,3.5,4].map((e,t)=>{let n=e*1.2;return`
                <feImage width="8" height="8"
                  xlink:href="data:image/svg+xml;utf8,${encodeURIComponent(`<svg xmlns='http://www.w3.org/2000/svg' width='8' height='8'>
                       <circle cx='4' cy='4' r='${n}' fill='black'/>
                     </svg>`)}" />
                <feTile result="dot${t+1}-tile" />
              `}).join(``)}

              <!-- Luminance mapping -->
              <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />
              <feComponentTransfer in="lum" result="lum-map">
                <feFuncA type="table" tableValues="1 0" />
              </feComponentTransfer>

              <!-- Discrete thresholds -->
              ${Array.from({length:8},(e,t)=>`
              <feComponentTransfer in="lum-map" result="thresh${t+1}">
                <feFuncA type="discrete" tableValues="${Array.from({length:8},(e,n)=>t===n?1:0).join(` `)}" />
              </feComponentTransfer>`).join(``)}

              <!-- Combine dots with thresholds -->
              ${Array.from({length:8},(e,t)=>`
              <feComposite in="thresh${t+1}" in2="dot${t+1}-tile" operator="in" result="level${t+1}" />`).join(``)}

              <!-- Merge everything -->
              <feMerge result="merged">
                ${Array.from({length:8},(e,t)=>`
                <feMergeNode in="level${8-t}" />`).join(``)}
              </feMerge>

              <!-- Clip to original shape -->
              <feComposite in="merged" in2="SourceGraphic" operator="in" result="masked" />

              <!-- Apply color (optional) -->
              <feFlood flood-color="#166496" result="color" />
              <feComposite in="color" in2="masked" operator="in" result="half-tone-transparent" />

              <feFlood flood-color="#ffffff" result="background" />
              <feMerge>
                <feMergeNode in="background" />
                <feMergeNode in="half-tone-transparent" />
              </feMerge>
            </filter>
        </defs>
    </svg>
</span>
`,qe=`
<span class="container container-filter"
      data-filter
      data-filter-type="svg"
      data-filter-name="half-tone-two-effect"
      data-requires-checkbox="true">
    
    <div class="filter-menu">
        <label class="label label-checkbox">
            <input id="cartoon" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
            <span class="label-span" data-i18n="Cartoon"></span>
        </label>
        <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
            ${w.arrowRepeat}
        </span>
    </div>
    
    <fieldset class="fieldset fieldset-filter wrapper-field-composed">
        <label class="label label-small" for="cartoon-outline-strength" data-i18n="Outline strength"></label>
        <input type="range" id="cartoon-outline-strength" class="input input-range" data-modifier="cartoon-outline-strength"
               min="0.1" max="2" step="0.1" value="1" data-default="1">
        <span class="wrapper">
            <input type="number" class="input input-number number-value" data-modifier="cartoon-outline-strength"
                   min="0.1" max="2" step="0.1" value="1" data-default="1">
            <label data-i18n="val"></label>
        </span>
    </fieldset>

    <svg class="svgFilter">
        <defs>
      
 <!-- Circles from r=0.5 to r=4 -->
        <circle id="dot1" cx="4" cy="4" r="0.5" />
        <circle id="dot2" cx="4" cy="4" r="1" />
        <circle id="dot3" cx="4" cy="4" r="1.5" />
        <circle id="dot4" cx="4" cy="4" r="2" />
        <circle id="dot5" cx="4" cy="4" r="2.5" />
        <circle id="dot6" cx="4" cy="4" r="3" />
        <circle id="dot7" cx="4" cy="4" r="3.5" />
        <circle id="dot8" cx="4" cy="4" r="4" />

        <filter id="half-tone-two-effect" color-interpolation-filters="sRGB" primitiveUnits="userSpaceOnUse">
          <!-- Tiled circles -->
          <feImage width="8" height="8" xlink:href="#dot1" />
          <feTile result="dot1-tile" />
          <feImage width="8" height="8" xlink:href="#dot2" />
          <feTile result="dot2-tile" />
          <feImage width="8" height="8" xlink:href="#dot3" />
          <feTile result="dot3-tile" />
          <feImage width="8" height="8" xlink:href="#dot4" />
          <feTile result="dot4-tile" />
          <feImage width="8" height="8" xlink:href="#dot5" />
          <feTile result="dot5-tile" />
          <feImage width="8" height="8" xlink:href="#dot6" />
          <feTile result="dot6-tile" />
          <feImage width="8" height="8" xlink:href="#dot7" />
          <feTile result="dot7-tile" />
          <feImage width="8" height="8" xlink:href="#dot8" />
          <feTile result="dot8-tile" />
    
          <!-- Luminance mapping -->
          <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />
          <feComponentTransfer in="lum" result="lum-map">
            <feFuncA type="table" tableValues="1 0" />
          </feComponentTransfer>
    
          <!-- Discrete thresholds -->
          <feComponentTransfer in="lum-map" result="thresh1">
            <feFuncA type="discrete" tableValues="1 0 0 0 0 0 0 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh2">
            <feFuncA type="discrete" tableValues="0 1 0 0 0 0 0 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh3">
            <feFuncA type="discrete" tableValues="0 0 1 0 0 0 0 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh4">
            <feFuncA type="discrete" tableValues="0 0 0 1 0 0 0 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh5">
            <feFuncA type="discrete" tableValues="0 0 0 0 1 0 0 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh6">
            <feFuncA type="discrete" tableValues="0 0 0 0 0 1 0 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh7">
            <feFuncA type="discrete" tableValues="0 0 0 0 0 0 1 0" />
          </feComponentTransfer>
          <feComponentTransfer in="lum-map" result="thresh8">
            <feFuncA type="discrete" tableValues="0 0 0 0 0 0 0 1" />
          </feComponentTransfer>
    
          <!-- Combine dots with thresholds -->
          <feComposite in="thresh1" in2="dot1-tile" operator="in" result="level1" />
          <feComposite in="thresh2" in2="dot2-tile" operator="in" result="level2" />
          <feComposite in="thresh3" in2="dot3-tile" operator="in" result="level3" />
          <feComposite in="thresh4" in2="dot4-tile" operator="in" result="level4" />
          <feComposite in="thresh5" in2="dot5-tile" operator="in" result="level5" />
          <feComposite in="thresh6" in2="dot6-tile" operator="in" result="level6" />
          <feComposite in="thresh7" in2="dot7-tile" operator="in" result="level7" />
          <feComposite in="thresh8" in2="dot8-tile" operator="in" result="level8" />
    
          <!-- Merge everything -->
          <feMerge result="merged">
            <feMergeNode in="level8" />
            <feMergeNode in="level7" />
            <feMergeNode in="level6" />
            <feMergeNode in="level5" />
            <feMergeNode in="level4" />
            <feMergeNode in="level3" />
            <feMergeNode in="level2" />
            <feMergeNode in="level1" />
          </feMerge>
    
          <!-- Clip to original shape -->
          <feComposite in="merged" in2="SourceGraphic" operator="in" result="masked" />
    
          <!-- Apply color (optional) -->
          <feFlood id="flood" flood-color="#166496" result="color" />
          <feComposite in="color" in2="masked" operator="in" />
        </filter>
        </defs>
    </svg>
    <svg class="svg" x="0" y="0" width="0" height="0">
  <defs>
    <circle id="two" cx="5" cy="5" r="0.5"></circle>
    <circle id="three" cx="1" cy="1" r="1"></circle>
    <circle id="four" cx="1" cy="1" r="1.5"></circle>
    <circle id="five" cx="1" cy="1" r="2"></circle>
    <circle id="six" cx="1" cy="1" r="2.5"></circle>
    <circle id="seven" cx="1" cy="1" r="3"></circle>
    <circle id="eight" cx="1" cy="1" r="4"></circle>
    <filter id="half-tone" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">
      <feImage width="3" height="3" xlink:href="#two"></feImage>
      <feTile result="2dot"></feTile>
      <feImage width="3" height="3" xlink:href="#three"></feImage>
      <feTile result="3dot"></feTile>
      <feImage width="3" height="3" xlink:href="#four"></feImage>
      <feTile result="4dot"></feTile>
      <feImage width="3" height="3" xlink:href="#five"></feImage>
      <feTile result="5dot"></feTile>
      <feImage width="3" height="3" xlink:href="#six"></feImage>
      <feTile result="6dot"></feTile>
      <feImage width="3" height="3" xlink:href="#seven"></feImage>
      <feTile result="7dot"></feTile>
      <feImage width="3" height="3" xlink:href="#eight"></feImage>
      <feTile result="8dot"></feTile>
      <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="neg-lum-map" width="100%" height="100%"></feColorMatrix>
      <feComponentTransfer result="lum-map">
        <feFuncA type="table" tableValues="1 0"></feFuncA>
        <feFuncR id="r" type="table" tableValues="0.125 0"></feFuncR>
        <feFuncG id="g" type="table" tableValues="0.078125 0"></feFuncG>
        <feFuncB id="b" type="table" tableValues="0.32421875 0"></feFuncB>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="2r-thresh">
        <feFuncA type="discrete" tableValues="0 1 0 0 0 0 0 0"></feFuncA>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="3r-thresh">
        <feFuncA type="discrete" tableValues="0 0 1 0 0 0 0 0"></feFuncA>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="4r-thresh">
        <feFuncA type="discrete" tableValues="0 0 0 1 0 0 0 0"></feFuncA>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="5r-thresh">
        <feFuncA type="discrete" tableValues="0 0 0 0 1 0 0 0"></feFuncA>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="6r-thresh">
        <feFuncA type="discrete" tableValues="0 0 0 0 0 1 0 0"></feFuncA>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="7r-thresh">
        <feFuncA type="discrete" tableValues="0 0 0 0 0 0 1 0"></feFuncA>
      </feComponentTransfer>
      <feComponentTransfer in="lum-map" result="8r-thresh">
        <feFuncA type="discrete" tableValues="0 0 0 0 0 0 0 1"></feFuncA>
      </feComponentTransfer>
      <feComposite operator="in" in="2r-thresh" in2="2dot" result="lev2"></feComposite>
      <feComposite operator="in" in="3r-thresh" in2="3dot" result="lev3"></feComposite>
      <feComposite operator="in" in="4r-thresh" in2="4dot" result="lev4"></feComposite>
      <feComposite operator="in" in="5r-thresh" in2="5dot" result="lev5"></feComposite>
      <feComposite operator="in" in="6r-thresh" in2="6dot" result="lev6"></feComposite>
      <feComposite operator="in" in="7r-thresh" in2="7dot" result="lev7"></feComposite>
      <feComposite operator="in" in="8r-thresh" in2="8dot" result="lev8"></feComposite>
      <feMerge>
        <feMergeNode in="lev8"></feMergeNode>
        <feMergeNode in="lev7"></feMergeNode>
        <feMergeNode in="lev6"></feMergeNode>
        <feMergeNode in="lev5"></feMergeNode>
        <feMergeNode in="lev4"></feMergeNode>
        <feMergeNode in="lev3"></feMergeNode>
        <feMergeNode in="lev2"></feMergeNode>
      </feMerge>
      <feComposite operator="in" in2="SourceGraphic"></feComposite>
    </filter>
</svg>
</span>
`,I=[{label:`orange isolation test`,value:`test_only_orange`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  -1 1 0 0 0`,filter:`color-isolation-filter-orange`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`test cyan`,value:`test_only_cyan`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  -1 1 0 0 0`,filter:`color-isolation-filter-cyan`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`test 2`,value:`test_only_2`,matrix:`1 0 0 -.5 0   0 1 0 -1 0   0 0 1 -1 0  1 -1 -1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`test`,value:`test_only`,matrix:`1 0 0 -.5 0   0 1 0 -1 0   0 0 1 -1 0  -1 1 1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`No greens and blues`,value:`no_greens_and_blues`,matrix:`1 -1 -1 0 0   1 -1 -1 0 0   1 -1 -1 0 0  1 -1 -1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`no reds and oranges, but purple and yellows`,value:`no_reds_and_oranges_but_purples_and_yellows`,matrix:`1 0 0 -1 0   0 1 0 0 0   0 0 1 -1 0  -1 1 1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Only yellows, greens and blues`,value:`only_yellows_greens_blues`,matrix:`.65 .35 0 0 0   .65 .35 0 0 0   .65 .35 0 0 0  -1 .65 -1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Only yellow 2`,value:`only_yellow-2`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 -1 -1 0 0`,filter:`color-isolation-filter-yellow-2`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Only yellow 3`,value:`only_yellow-3`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 -1 -1 0 0`,filter:`color-isolation-filter-yellow-3`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Red`,value:`red`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 -1 -1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`red including purples / pinks`,value:`no_yellow_green_blue`,matrix:`1 0 1 0 0  1 -1 1 0 0  1 0 1 0 0  1 -1 1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Red 2`,value:`red-2`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 -1 -1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0,experimental:!0},{label:`No reds`,value:`no_reds`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  -1 0 0 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Green`,value:`green`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  -1 1 -1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Green 2`,value:`green-2`,matrix:`0 0 0 0 0  0 2 0 0 0  0 0 0 0 0  -1 1 -1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0,experimental:!0},{label:`No greens`,value:`no_greens`,matrix:`1 0 0 0 0  0 0 0 0 0  0 0 1 0 0  1 0 1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Blue`,value:`blue`,matrix:`-1 0 1 0 0  0 0 0 0 0  0 0 0 0 0  -1 0 1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Blue 2`,value:`blue-2`,matrix:`0 0 0 0 0  0 0 0 0 0  -1 0 1 0 0  0  1 1 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0,experimental:!0},{label:`Blue 3`,value:`blue-3`,matrix:`0 0 0 0 0  0 0 0 0 0  -1 0 1 0 0  -1 -1 1 1 0`,filter:`color-isolation-filter-3`,blendMode:`normal`,saturationBoost:!0},{label:`No blues`,value:`no blues`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 0 -1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`No blues, purples or pinks`,value:`no_blues_purples_or_pinks`,matrix:`1  0  0  0  0 0  1  0 -0.500  0  0  0  1 -1  0 0  0 -1  1  0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`No red`,value:`no_red`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  -1 1 1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0},{label:`No green`,value:`no_green`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 -1 1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0},{label:`No blue`,value:`no_blue`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 1 -1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0},{label:`Warm colors`,value:`warm_colors`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 0 -1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0},{label:`Purple/Magenta/Pink`,value:`purple_magenta_pink`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 -1 1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Purple/Magenta/Pink/Blue`,value:`purple_magenta_pink_blue`,matrix:`1 0 0 0 0   0 0 0 0 0  1 0 1 0 0  -1 -1 1 1 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Yellow`,value:`yellow`,matrix:`1 1 -1 0 0  1 1 -1 0 0  1 1 -1 0 0  1 1 0 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0,experimental:!0},{label:`Blues / Greens`,value:`blues_greens`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  -1 1 0 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Only yellows / oranges / greens`,value:`only_yellows_oranges_greens`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 1 -1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`Yellows / oranges / reds`,value:`yellow_oranges_reds`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 0 -1 0 0`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`No pinks / purples`,value:`no_pinks_purples`,matrix:`1 0 0 0 0  0 1 0 0 0  0 0 -1 0 0  -1 0.7 -1 0 2`,filter:`color-isolation-filter-original`,blendMode:`normal`,saturationBoost:!0,experimental:!1},{label:`filter 2 no yellows / greens / blues`,value:`filter_2_tester`,matrix:`0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  1 -1 1 0 0`,filter:`color-isolation-filter-2`,blendMode:`normal`,saturationBoost:!0,experimental:!1}];function L(e,t=[],n){let r=T(t),i=r.presets||`red`,a=r.blendMode||`normal`,o=I.find(e=>e.value===i)??I[0],s=o.matrix,c=a===`no-selection`?o.blendMode:a;if(console.log(o),console.log(a),o.filter===`color-isolation-filter-1`){let t=e.querySelector(`#f1-isolation-matrix`),n=e.querySelector(`#f1-blend`);return console.log(`set isolationMatrix values to `,s),t.setAttribute(`values`,s),console.log(`set blendMode to `,c),n.setAttribute(`mode`,c),`color-isolation-filter-1`}if(o.filter===`color-isolation-filter-2`){let t=e.querySelector(`#f2-saturation-matrix`),n=e.querySelector(`#f2-isolation-matrix`);e.querySelector(`#f2-blend`),console.log(`saturationBoost modifier value`,r.saturationBoost);let i=r.saturationBoost===`yes`;return console.log(`saturationBoost`,i),console.log(`set saturationBoost to `,i),t.setAttribute(`values`,i?`10`:`1`),console.log(`set isolationMatrix values to `,s),n.setAttribute(`values`,s),console.log(`set blendMode to `,c),`color-isolation-filter-2`}if(o.filter===`color-isolation-filter-3`)return`color-isolation-filter-3`;if(o.filter===`color-isolation-filter-original`){let t=e.querySelector(`#original-saturation-matrix`),n=e.querySelector(`#original-isolation-matrix`),i=e.querySelector(`#original-blend`);console.log(`saturationBoost modifier value`,r.saturationBoost);let a=r.saturationBoost===`yes`;return console.log(`saturationBoost`,a),console.log(`set saturationBoost to `,a),t.setAttribute(`values`,a?`10`:`1`),console.log(`set isolationMatrix values to `,s),n.setAttribute(`values`,s),i.setAttribute(`mode`,c),`color-isolation-filter-original`}return o.filter}var Je=`
    <span class="container container-filter" 
          data-filter
          data-filter-type="svg"
          data-filter-name="color-isolation-filter"
          data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input id="color-isolation" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Color isolation"></span>
            </label>
    
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>

        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
          <label for="saturation-boost-select" data-i18n="Saturation boost"></label>
          <select id="saturation-boost-select" class="input input-select" data-modifier="saturation-boost" data-default="yes">
            <option value="no" data-i18n="Normal">Normal</option>
            <option value="yes" data-i18n="Over-saturated">Over-saturated</option>
          </select>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
          <label for="blend-mode-select" data-i18n="Blend mode"></label>
         <select id="blend-mode-select" class="input input-select" data-modifier="blend-mode" data-default="normal">
            <option value="no-selection" data-i18n="">---</option>
            <option value="normal" data-i18n="Normal" selected>Normal</option>
            <option value="multiply" data-i18n="Multiply">Multiply</option>
            <option value="screen" data-i18n="Screen">Screen</option>
            <option value="darken" data-i18n="Darken">Darken</option>
            <option value="lighten" data-i18n="Lighten">Lighten</option>
          </select>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <label for="color-channel-select" data-i18n="Color Channel"></label>
            <select id="color-channel-select" class="input input-select" data-modifier="presets" data-default="red">
            ${I.filter(e=>`experimental`in e&&e.experimental===!1).map(e=>`<option value="${e.value}" data-i18n="${e.label}">${e.label}</option>`).join(``)}
            </select>
        </fieldset>
        <svg class="svgFilter">
            <defs>
                  <filter id="color-isolation-filter-1" color-interpolation-filters="sRGB">
                    <feColorMatrix
                      id="f1-isolation-matrix"
                      type="matrix"
                      values=" 1 -1 -1  0  0 
                               0  0  0  0  0 
                               0  0  0  0  0 
                               1 -1 -1  1  0"
                               result="isolation">
                    </feColorMatrix>
                     <feColorMatrix
                            in="SourceGraphic" 
                            type="saturate" 
                            values="0" 
                            result="grayScale" />
                    <feBlend id="f1-blend" in="grayScale" in2="isolation" mode="screen"/>
                  </filter>
                 <filter id="color-isolation-filter-2" color-interpolation-filters="sRGB">
                      <!-- Step 1: optional saturation boost -->
                      <feColorMatrix 
                        id="f2-saturation-matrix"
                        type="saturate" 
                        values="10" 
                        result="saturated" />
                    
                      <!-- Step 2: color channel isolation -->
                      <feColorMatrix 
                        id="f2-isolation-matrix"
                        values="0 0 0 0 0 
                                0 0 0 0 0 
                                0 0 0 0 0 
                                0 0 0 0 0"
                        in="saturated"
                        result="isolated" />
                         
<!--                         &lt;!&ndash; Step 3: isolated color to white &ndash;&gt;-->
<!--                      <feColorMatrix -->
<!--                        id="f2-isolation-matrix"-->
<!--                        values="0 0 0 0 0 -->
<!--                                0 0 0 0 0 -->
<!--                                0 0 0 0 0 -->
<!--                                0 0 0 0 0"-->
<!--                        in="saturated"-->
<!--                        result="isolated" />-->
                    
                    <feComponentTransfer in="isolated" result="thresholded">
<!--                    <feFuncR type="table" tableValues="0 1" />-->
<!--                    <feFuncG type="table" tableValues="0 1" />-->
                    <feFuncB type="table" tableValues="0 .5 1" />
                  </feComponentTransfer>
                  
<!--                  <feMerge>-->
<!--                        <feMergeNode in="thresholded"></feMergeNode>-->
<!--                    </feMerge>-->
<!--                       Step 3: mask isolated channel from the original image -->
                      <feComposite 
                        in="SourceGraphic" 
                        in2="isolated" 
                        operator="in" 
                        result="cut" />
<!--                    -->
<!--                      &lt;!&ndash; Step 4: desaturate the full image to use as the background &ndash;&gt;-->
                      <feColorMatrix 
                        in="SourceGraphic" 
                        type="saturate" 
                        values="0" 
                        result="grayscale" />
<!--                    -->
<!--                      &lt;!&ndash; Step 5: blend isolated color on top of grayscale &ndash;&gt;-->
                      <feBlend 
                        id="f2-blend"
                        in="cut" 
                        in2="grayscale" 
                        mode="normal" />
                </filter>
                   <filter id="color-isolation-filter-3" color-interpolation-filters="sRGB">
            <feColorMatrix
                in="SourceGraphic"
                type="matrix"
                values="0 0 0 0 0
                        0 0 0 0 0
                       -1 0 1 0 0
                       0 0 1 1 0"
                       result="isolation"
                />
                  <!-- 🔧 Step 2: force alpha to 1 -->
              <feComponentTransfer in="isolation" result="opaqueIsolation">
                <feFuncA type="discrete" tableValues="1"/>
              </feComponentTransfer>
                <feColorMatrix
                            in="SourceGraphic" 
                            type="saturate" 
                            values="0" 
                            result="grayScale" />
                    <feBlend id="f1-blend" in="grayScale" in2="opaqueIsolation" mode="screen"/>
                
                

  <!-- Force alpha to 1 -->
<!--  <feComponentTransfer in="isolated">-->
<!--    <feFuncA type="discrete" tableValues="1"/>-->
<!--  </feComponentTransfer>-->
<!--              <feColorMatrix-->
<!--    in="SourceGraphic"-->
<!--    type="matrix"-->
<!--    values="0 0 0 0 0-->
<!--            0 0 0 0 0-->
<!--           -1 0 1 0 0-->
<!--           -1 -1 1 1 0"-->
<!--    result="isolated" />-->

<!--  &lt;!&ndash; 2. Extract alpha from isolated image &ndash;&gt;-->
<!--  <feColorMatrix-->
<!--    in="isolated"-->
<!--    type="matrix"-->
<!--    values="0 0 0 0 0-->
<!--            0 0 0 0 0-->
<!--            0 0 0 0 0-->
<!--            0 0 0 1 0"-->
<!--    result="isolated-alpha" />-->

<!--  &lt;!&ndash; 3. Invert the alpha: we want to fill where the original is NOT opaque &ndash;&gt;-->
<!--  <feComponentTransfer in="isolated-alpha" result="inverted-alpha">-->
<!--    <feFuncA type="table" tableValues="1 0"/>-->
<!--  </feComponentTransfer>-->

<!--  &lt;!&ndash; 4. Create solid fill color &ndash;&gt;-->
<!--  <feFlood flood-color="white" result="fill" />-->

<!--  &lt;!&ndash; 5. Apply the inverted alpha to the fill &ndash;&gt;-->
<!--  <feComposite in="fill" in2="inverted-alpha" operator="in" result="fill-under" />-->

<!--  &lt;!&ndash; 6. Merge the fill and isolated layer &ndash;&gt;-->
<!--  <feMerge>-->
<!--    <feMergeNode in="fill-under"/>-->
<!--    <feMergeNode in="isolated"/>-->
<!--  </feMerge>-->
                <!-- Step 1: Isolate a channel from the original image -->
<!--                  <feColorMatrix-->
<!--                    in="SourceGraphic"-->
<!--                    type="matrix"-->
<!--                    values="0 0.000 0.000 0.000 0.000 -->
<!--                            0.000 0.000 0.000 0.000 0.000 -->
<!--                           -1.000 0.000 1.000 0.000 0.000 -->
<!--                           -1.000 -1.000 1.000 1.000 0.000"-->
<!--                    result="isolated" />-->
<!--                -->
<!--                  &lt;!&ndash; Step 2: Fill entire area with solid white &ndash;&gt;-->
<!--                  <feFlood flood-color="white" result="flood" />-->
<!--                -->
<!--                  &lt;!&ndash; Step 3: Use alpha of the isolated layer to cut out the inverse (transparent) part &ndash;&gt;-->
<!--                  <feComposite in="flood" in2="isolated" operator="out" result="backgroundFill" />-->
<!--                -->
<!--                  &lt;!&ndash; Step 4: Merge background fill and isolated image &ndash;&gt;-->
<!--                  <feMerge>-->
<!--                    <feMergeNode in="backgroundFill" />-->
<!--                    <feMergeNode in="isolated" />-->
<!--                  </feMerge>-->
              </filter>
              <filter id="color-isolation-filter-original" color-interpolation-filters="sRGB">
                    <feColorMatrix id="original-saturation-matrix"
                    type="saturate" values="10" result="boosted"/>
                        
                    <!-- Selective channel mask -->
                    <feColorMatrix 
                    id="original-isolation-matrix"
                    in="boosted" result="isolated"
                    values="-1 0 1 0 0
                            0 0 0 0 0
                            0 0 0 0 0
                            -1 0 1 0 0"
                             />
                    
                    <!-- Mask original with selective channel -->
                    <feComposite in="SourceGraphic" in2="isolated" operator="in" result="cut" />
                    
                    <!-- Desaturate original to grayscale -->
                    <feColorMatrix in="SourceGraphic" type="saturate" values="0" result="grayscale" />
                    
                    <!-- Blend colored parts on grayscale -->
                    <feBlend 
                    id="original-blend"
                    in="cut" in2="grayscale" mode="normal"  />
                </filter>
                <filter id="isolate-yellow" color-interpolation-filters="sRGB">
                  <!-- Step 1: Optional boost saturation -->
                  <feColorMatrix type="saturate" values="5" result="saturated" />
                
                  <!-- Step 2: Isolate yellow — emphasize red + green, suppress blue -->
                  <feColorMatrix
                    in="saturated"
                    type="matrix"
                    values="
                      1  1  0  0  0
                      1  1  0  0  0
                      0  0  0  0  0
                      0  0  0  1  0"
                    result="yellowish" />
                
                  <!-- Step 3: Make isolated yellow pure white, everything else black -->
                  <feComponentTransfer in="yellowish" result="white-on-black">
                    <feFuncR type="table" tableValues="0 1"/>
                    <feFuncG type="table" tableValues="0 1"/>
                    <feFuncB type="table" tableValues="0 0"/>
                  </feComponentTransfer>
                
                  <!-- Optional: Use as mask or blend on grayscale -->
                </filter>
                 <filter id="color-isolation-filter-yellow" color-interpolation-filters="sRGB">
                
                    
<!--                    GOOD CODE, but picky for yellow and leaks rads-->
                    <!-- Step 1: Isolate green using ComponentTransfer -->
                    <feComponentTransfer in="SourceGraphic" result="isolated">
                        <feFuncR type="table" tableValues="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1"/>
                        <feFuncG type="table" tableValues="0 0 0 0 0 0 0 0 0 1"/>
                        <feFuncB type="table" tableValues="0"/>
                    </feComponentTransfer>
                    
                    <!-- Step 2: Optional threshold -->
                    <feComponentTransfer in="isolated" result="isolated-2">
                        <feFuncG type="table" tableValues="0 0 0 1 1"/>
                    </feComponentTransfer>
                    

                    <!-- Step 3: Convert green channel to alpha (for masking) -->
                    <feColorMatrix in="isolated-2" type="matrix" result="alpha-mask"
                    values="0 0 0 0 0
                            0 0 0 0 0
                            0 0 0 0 0
                            1 1 0 0 0" />
                    
                    <!-- Step 4: Apply the alpha mask to the original image -->
                    <feComposite in="SourceGraphic" in2="alpha-mask" operator="in" result="yellows"/>
                    
                    <!-- Desaturate original to grayscale -->
                    <feColorMatrix in="SourceGraphic" type="saturate" values="0" result="grayscale" />
                    
                    <!-- Blend colored parts on grayscale -->
                    <feBlend 
                    id="original-blend"
                    in="yellows" in2="grayscale" mode="normal"  />
                </filter>
                <filter id="color-isolation-filter-test" color-interpolation-filters="sRGB">
                  
                  

                </filter>
             <filter id="color-isolation-filter-yellow-2" color-interpolation-filters="sRGB">
                                
                    <!-- VERY GOOD CODE FOR YELLOW EXTRACTION, but a little picky leaks a little green-->
                    <feColorMatrix
                        type="matrix"
                        values="1 0 0 0 0
                                0 1 0 0 0
                                0 0 0 0 0
                                0 0 0 1 0 "
                        result="yellowEmphasis"/>
                
                    <feColorMatrix
                        in="yellowEmphasis"
                        type="matrix"
                        values="0.299 0.587 0.114 0 0
                                    0.299 0.587 0.114 0 0
                                    0.299 0.587 0.114 0 0
                                    0     0     0     1 0"
                        result="yellow-emphasis-2" />
                
                    <feComponentTransfer in="yellow-emphasis-2" result="thresholded">
                        <feFuncR type="discrete" tableValues="0 0 0 1" />
                        <feFuncG type="discrete" tableValues="0 0 0 0 1" />
                        <feFuncB type="discrete" tableValues="0 0 0 0 0 0 0 1" />
                        <feFuncA type="identity" />
                     </feComponentTransfer>
                     
                     <feComponentTransfer in="thresholded" result="thresholded-2">
                        <feFuncR type="discrete" tableValues="0 1" />
                        <feFuncG type="discrete" tableValues="0 1" />
                        <feFuncB type="discrete" tableValues="0 1" />
                        <feFuncA type="identity" />
                     </feComponentTransfer>
                     
                       <!-- Step 3: Convert green channel to alpha (for masking) -->
                    <feColorMatrix in="thresholded-2" type="matrix" result="alpha-mask"
                    values="0 0 0 0 0
                            0 0 0 0 0
                            0 0 0 0 0
                            1 1 0 0 0" />
                    
                    <!-- Step 4: Apply the alpha mask to the original image -->
                    <feComposite in="SourceGraphic" in2="alpha-mask" operator="in" result="yellows"/>
                    
                    <!-- Desaturate original to grayscale -->
                    <feColorMatrix in="SourceGraphic" type="saturate" values="0" result="grayscale" />
                    
                    <!-- Blend colored parts on grayscale -->
                    <feBlend 
                    id="original-blend"
                    in="yellows" in2="grayscale" mode="normal"  />
<!--                     <feMerge>-->
<!--                     <feMergeNode in="thresholded-2" />-->
<!--                    </feMerge>-->
                       
        </filter>
        
          <filter id="color-isolation-filter-yellow-3" color-interpolation-filters="sRGB">
                                
                <!-- BEST CODE SO FAR FOR YELLOW EXTRACTION, more yellow tints extracted, only leaks a small amount of green  -->
                                
                 <feColorMatrix id="original-saturation-matrix"
                    type="saturate" values="10" result="boosted"/>
                        
                    <feColorMatrix
                    in="boosted"
                        type="matrix"
                        values="1 0 0 0 0
                                0 1 0 0 0
                                0 0 0 0 0
                                0 0 0 1 0 "
                        result="yellowEmphasis"/>
                
                    <feColorMatrix
                        in="yellowEmphasis"
                        type="matrix"
                        values="0.299 0.587 0.114 0 0
                                    0.299 0.587 0.114 0 0
                                    0.299 0.587 0.114 0 0
                                    0     0     0     1 0"
                        result="yellow-emphasis-2" />
                
                    <feComponentTransfer in="yellow-emphasis-2" result="thresholded">
                        <feFuncR type="discrete" tableValues="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1 1 1 1 1" />
                        <feFuncG type="discrete" tableValues="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1 1 1 1 1" />
                        <feFuncB type="identity" />
                        <feFuncA type="identity" />
                     </feComponentTransfer>
                     
                     <feComponentTransfer in="thresholded" result="thresholded-2">
                        <feFuncR type="discrete" tableValues="0 1" />
                        <feFuncG type="discrete" tableValues="0 1" />
                        <feFuncB type="discrete" tableValues="0 1" />
                        <feFuncA type="identity" />
                     </feComponentTransfer>
                     
                       <!-- Step 3: Convert green channel to alpha (for masking) -->
                    <feColorMatrix in="thresholded-2" type="matrix" result="alpha-mask"
                    values="0 0 0 0 0
                            0 0 0 0 0
                            0 0 0 0 0
                            1 1 0 0 0" />
                    
                    <!-- Step 4: Apply the alpha mask to the original image -->
                    <feComposite in="SourceGraphic" in2="alpha-mask" operator="in" result="yellows"/>
                    
                    <!-- Desaturate original to grayscale -->
                    <feColorMatrix in="SourceGraphic" type="saturate" values="0" result="grayscale" />
                    
                    <!-- Blend colored parts on grayscale -->
                    <feBlend 
                    id="original-blend"
                    in="yellows" in2="grayscale" mode="normal"  />
<!--                     <feMerge>-->
<!--                     <feMergeNode in="thresholded-2" />-->
<!--                    </feMerge>-->
                       
        </filter>
                  <filter id="color-isolation-filter-cyan" color-interpolation-filters="sRGB">
                                
                    <feColorMatrix
                        result="cyan-isolation-1"
                        type="matrix"
                        values="-1.000  0.500  0.500  0.000  0.000 
                              -1.000  0.500  0.500  0.000  0.000 
                              -1.000  0.500  0.500  0.000  0.000 
                               0.000  0.000  0.000  1.000  0.000">
                    </feColorMatrix>

                  
                    <feColorMatrix in="cyan-isolation-1" type="saturate" values="0" result="cyan-isolation-2" />
                  
                    <feColorMatrix type="luminanceToAlpha" in="cyan-isolation-2" result="cyan-isolation-3" />
                     
                     <feComponentTransfer result="cyan-isolation-4">
                                         <feFuncA type="discrete" tableValues="0 1 1 1 1"/>
                    </feComponentTransfer>
                     
<!--                     <feMerge>-->
<!--                        <feMergeNode in="cyan-isolation-4" />-->
<!--                    </feMerge>-->
                    <feComposite in="SourceGraphic" in2="cyan-isolation-4" operator="in" result="cyan-isolation-5"/>

                    <feColorMatrix in="SourceGraphic" type="saturate" values="0" result="grayscale" />
                    <feBlend 
                    id="cyans-on-gray"
                    in="cyan-isolation-5" in2="grayscale" mode="normal"  />

                       
        </filter>
        
              <filter id="color-isolation-filter-orange" color-interpolation-filters="sRGB">
                                
                <!-- BEST CODE SO FAR FOR YELLOW EXTRACTION, more yellow tints extracted, only leaks a small amount of green  -->
                                
<!--                 <feColorMatrix id="original-saturation-matrix"-->
<!--                    type="saturate" values="10" result="boosted"/>-->
                        
                     <feColorMatrix
                          result="orange-isolation"
                          type="matrix"
                          values=" 0  1  0  0  0 
                                   1  0  0  0  0 
                                   0  0  1  0  0 
                                   0  0 -1  1  0">
                        </feColorMatrix>
                
                        <feComponentTransfer in="orange-isolation" result="thresholded">
                        <feFuncR type="discrete" tableValues="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1" />
                        <feFuncG type="discrete" tableValues="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 1 1" />
                        <feFuncB type="identity" />
                        <feFuncA type="identity" />
                        </feComponentTransfer>
                
                     <feMerge>
                     <feMergeNode in="orange-isolation" />
                    </feMerge>

<!--                     -->
<!--    -->
<!--                     -->

<!--                       &lt;!&ndash; Step 3: Convert green channel to alpha (for masking) &ndash;&gt;-->
<!--                -->
<!--                    <feColorMatrix-->
<!--                        in="thresholded"-->
<!--                        type="matrix"-->
<!--                        values="0 -1 2 0 0-->
<!--                                0 -1 2 0 0-->
<!--                                0 -1 2 0 0-->
<!--                                0 0 1 1 0"-->
<!--                        result="cyan-isolation-2" />-->
<!--&lt;!&ndash;                    &lt;!&ndash; Step 4: Apply the alpha mask to the original image &ndash;&gt;&ndash;&gt;-->
<!--                    <feComposite in="SourceGraphic" in2="thresholded" operator="in" result="cyans"/>-->
<!--                    -->
<!--                    &lt;!&ndash; Desaturate original to grayscale &ndash;&gt;-->
<!--                    <feColorMatrix in="SourceGraphic" type="saturate" values="0" result="grayscale" />-->
<!--                    -->
<!--                    &lt;!&ndash; Blend colored parts on grayscale &ndash;&gt;-->
<!--                    <feBlend -->
<!--                    id="original-blend"-->
<!--                    in="cyans" in2="grayscale" mode="normal"  />-->
<!--                     <feMerge>-->
<!--                     <feMergeNode in="thresholded-2" />-->
<!--                    </feMerge>-->
                       
        </filter>
        
     
            </defs>
            
        </svg>
    </span>
`;function Ye(e,t=[],n){return T(t),`half-tone-newspaper`}var Xe=`
<span class="container container-filter"
      data-filter
      data-filter-type="svg"
      data-filter-name="half-tone-newspaper-effect"
      data-requires-checkbox="true">
    
    <div class="filter-menu">
        <label class="label label-checkbox">
            <input id="halftone-newspaper" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
            <span class="label-span" data-i18n="Half tone newspaper"></span>
        </label>
        <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
            ${w.arrowRepeat}
        </span>
    </div>

    <svg class="svgFilter">
        <defs>
          <!-- Dot shapes -->
        <circle id="two" cx="5" cy="5" r="0.5"></circle>
        <circle id="three" cx="1" cy="1" r="1"></circle>
        <circle id="four" cx="1" cy="1" r="1.5"></circle>
        <circle id="five" cx="1" cy="1" r="2"></circle>
        <circle id="six" cx="1" cy="1" r="2.5"></circle>
        <circle id="seven" cx="1" cy="1" r="3"></circle>
        <circle id="eight" cx="1" cy="1" r="4"></circle>

        <!-- Halftone filter -->
        <filter id="half-tone-newspaper" x="0%" y="0%" width="100%" height="100%" color-interpolation-filters="sRGB">

            <!-- Tiled dot images -->
            <feImage width="3" height="3" xlink:href="#two"/>
            <feTile result="2dot"/>
            <feImage width="3" height="3" xlink:href="#three"/>
            <feTile result="3dot"/>
            <feImage width="3" height="3" xlink:href="#four"/>
            <feTile result="4dot"/>
            <feImage width="3" height="3" xlink:href="#five"/>
            <feTile result="5dot"/>
            <feImage width="3" height="3" xlink:href="#six"/>
            <feTile result="6dot"/>
            <feImage width="3" height="3" xlink:href="#seven"/>
            <feTile result="7dot"/>
            <feImage width="3" height="3" xlink:href="#eight"/>
            <feTile result="8dot"/>

            <!-- Convert to luminance -->
            <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="neg-lum-map"/>
            <feComponentTransfer result="lum-map">
                <feFuncA type="table" tableValues="1 0"/>
                <feFuncR type="table" tableValues="0.125 0"/>
                <feFuncG type="table" tableValues="0.078125 0"/>
                <feFuncB type="table" tableValues="0.32421875 0"/>
            </feComponentTransfer>

            <!-- Threshold layers -->
            <feComponentTransfer in="lum-map" result="2r-thresh">
                <feFuncA type="discrete" tableValues="0 1 0 0 0 0 0 0"/>
            </feComponentTransfer>
            <feComponentTransfer in="lum-map" result="3r-thresh">
                <feFuncA type="discrete" tableValues="0 0 1 0 0 0 0 0"/>
            </feComponentTransfer>
            <feComponentTransfer in="lum-map" result="4r-thresh">
                <feFuncA type="discrete" tableValues="0 0 0 1 0 0 0 0"/>
            </feComponentTransfer>
            <feComponentTransfer in="lum-map" result="5r-thresh">
                <feFuncA type="discrete" tableValues="0 0 0 0 1 0 0 0"/>
            </feComponentTransfer>
            <feComponentTransfer in="lum-map" result="6r-thresh">
                <feFuncA type="discrete" tableValues="0 0 0 0 0 1 0 0"/>
            </feComponentTransfer>
            <feComponentTransfer in="lum-map" result="7r-thresh">
                <feFuncA type="discrete" tableValues="0 0 0 0 0 0 1 0"/>
            </feComponentTransfer>
            <feComponentTransfer in="lum-map" result="8r-thresh">
                <feFuncA type="discrete" tableValues="0 0 0 0 0 0 0 1"/>
            </feComponentTransfer>

            <!-- Combine each dot with corresponding threshold -->
            <feComposite in="2r-thresh" in2="2dot" operator="in" result="lev2"/>
            <feComposite in="3r-thresh" in2="3dot" operator="in" result="lev3"/>
            <feComposite in="4r-thresh" in2="4dot" operator="in" result="lev4"/>
            <feComposite in="5r-thresh" in2="5dot" operator="in" result="lev5"/>
            <feComposite in="6r-thresh" in2="6dot" operator="in" result="lev6"/>
            <feComposite in="7r-thresh" in2="7dot" operator="in" result="lev7"/>
            <feComposite in="8r-thresh" in2="8dot" operator="in" result="lev8"/>

            <!-- Merge into one -->
            <feMerge>
                <feMergeNode in="lev8"/>
                <feMergeNode in="lev7"/>
                <feMergeNode in="lev6"/>
                <feMergeNode in="lev5"/>
                <feMergeNode in="lev4"/>
                <feMergeNode in="lev3"/>
                <feMergeNode in="lev2"/>
            </feMerge>

            <!-- Clip to original -->
            <feComposite operator="in" in2="SourceGraphic"/>
        </filter>
           
        </defs>
    </svg>
</span>
`;function Ze(e,t=[],n){return T(t),`half-tone-three-effect`}var Qe=`
<span class="container container-filter"
      data-filter
      data-filter-type="svg"
      data-filter-name="half-tone-three-effect"
      data-requires-checkbox="true">
    
    <div class="filter-menu">
        <label class="label label-checkbox">
            <input id="halftone-newspaper" type="checkbox" class="input input-checkbox" data-filter-active-checkbox>
            <span class="label-span" data-i18n="Half tone 3"></span>
        </label>
        <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
            ${w.arrowRepeat}
        </span>
    </div>

    <svg class="svgFilter">
        <defs>
            <filter id="half-tone-three-effect" color-interpolation-filters="sRGB" primitiveUnits="userSpaceOnUse">
                <!-- Tiled circles as data URIs -->
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='0.5' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot1-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='1' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot2-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='1.5' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot3-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='2' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot4-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='2.5' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot5-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='3' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot6-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='3.5' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot7-tile" />
                <feImage width="8" height="8" xlink:href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Ccircle cx='4' cy='4' r='4' fill='black'/%3E%3C/svg%3E" />
                <feTile result="dot8-tile" />

                <!-- Luminance mapping -->
                <feColorMatrix in="SourceGraphic" type="luminanceToAlpha" result="lum" />
                <feComponentTransfer in="lum" result="lum-map">
                    <feFuncA type="table" tableValues="1 0" />
                </feComponentTransfer>

                <!-- Discrete thresholds -->
                ${[1,2,3,4,5,6,7,8].map(e=>`
                <feComponentTransfer in="lum-map" result="thresh${e}">
                    <feFuncA type="discrete" tableValues="${Array(8).fill(0).map((t,n)=>n+1===e?1:0).join(` `)}" />
                </feComponentTransfer>`).join(``)}

                <!-- Combine dots with thresholds -->
                ${[1,2,3,4,5,6,7,8].map(e=>`
                <feComposite in="thresh${e}" in2="dot${e}-tile" operator="in" result="level${e}" />`).join(``)}

                <!-- Merge everything -->
                <feMerge result="merged">
                    ${[8,7,6,5,4,3,2,1].map(e=>`<feMergeNode in="level${e}" />`).join(`
`)}
                </feMerge>

                <!-- Clip to original shape -->
                <feComposite in="merged" in2="SourceGraphic" operator="in" result="masked" />

                <!-- Apply color (optional) -->
                <feFlood flood-color="#166496" result="color" />
                <feComposite in="color" in2="masked" operator="in" />
            </filter>
        </defs>
    </svg>
</span>
`,$e=Object.freeze(Object.defineProperty({__proto__:null,blurEffectHTML:Be,channelManipulationEffectHTML:Pe,colorIsolationEffectHTML:Je,duotoneEffectHTML:ne,edgeDetectEffectHTML:Te,embossEffectHTML:Re,experimentalEffectHtml:He,frostedGlassEffectHTML:Ae,gammaAdvancedEffectHTML:O,gammaEffectHTML:oe,glitchEffectHTML:pe,glowEffectHTML:Me,halfTone2EffectHTML:qe,halfToneEffectHTML:Ke,halfToneNewspaperEffectHTML:Xe,halfToneThreeEffectHTML:Qe,inkBlotEffectHTML:ie,morphEffectHTML:Ce,pastelEffectHTML:We,pixelateEffectHTML:ye,posterizeEffectHTML:Oe,rgbShiftEffectHTML:Ie,rippleEffectHTML:D,sketchEffectHTML:_e,softGLowHTML:he,staticNoiseEffectHTML:ce,vignetteEffectHTML:xe,waterColorEffectHTML:ue},Symbol.toStringTag,{value:`Module`})),et=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="number" 
        data-filter-string="brightness(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="brightness" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Brightness"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Brightness" class="is-hidden"></legend>
            <input type="range" id="brightness-range" class="input input-range" data-modifier="brightness" min="0" max="3" step="0.01" value="1" data-default="1">
            <!--            <input type="range" id="brightness-range" class="input input-range" data-modifier="brightness" min="0" max="200" value="100" data-default="100">-->
            <span class="wrapper">
                <input type="number" id="brightness-value" class="input input-number number-value" data-modifier="brightness" min="0" step="0.01" max="3" value="1" data-default="1">
                <!--                <label class="" for="brightness-value" data-i18n="per"></label>-->
            </span>
        </fieldset>
    </span>
`,tt=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="number" 
        data-filter-string="contrast(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="contrast" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Contrast"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Contrast" class="is-hidden"></legend>
            <input type="range" id="contrast-range" class="input input-range" data-modifier="contrast" min="0" max="3" step="0.01" value="0.1" data-default="1">
            <span class="wrapper">
                <input type="number" id="contrast-value" class="input input-number number-value" data-modifier="contrast" min="0" max="3" step="0.01" value="1" data-default="1">
                <!-- <label for="contrast-value" class="" data-i18n="per"></label>-->
            </span>
        </fieldset>
    </span>
`,nt=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="number" 
        data-filter-string="saturate(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="saturate" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Saturate"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Saturate" class="is-hidden"></legend>
            <input type="range" id="saturate-range" class="input input-range" data-modifier="saturation" min="0" max="10" step="0.01" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" id="saturate-value" class="input input-number number-value" data-modifier="saturation" min="0" step="0.01" max="10" value="1" data-default="1">
                <!-- <label for="saturate-value" class="" data-i18n="per"></label>-->
            </span>
        </fieldset>
    </span>
`,rt=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="number" 
        data-filter-string="grayscale(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="grayscale" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Grayscale"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Grayscale" class="is-hidden"></legend>
            <input type="range" id="grayscale-range" class="input input-range" data-modifier="grayscale" min="0" step="0.01" max="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" id="grayscale-value" class="input input-number number-value" data-modifier="grayscale" min="0" step="0.01" max="1" value="0" data-default="0">
                <!-- <label for="grayscale-value" class="" data-i18n="per"></label>-->
            </span>
        </fieldset>
    </span>
`,it=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="number" 
        data-filter-string="sepia(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="sepia" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Sepia"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Sepia" class="is-hidden"></legend>
            <input type="range" id="sepia-range" class="input input-range" data-modifier="sepia" min="0" step="0.01" max="1" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" id="sepia-value" class="input input-number number-value" data-modifier="sepia" min="0" step="0.01" max="1" value="0" data-default="0">
                <!-- <label for="sepia-value" class="" data-i18n="per"></label>-->
            </span>
        </fieldset>
    </span>
`,at=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="number" 
        data-filter-string="invert(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="invert" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Invert"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Invert" class="is-hidden"></legend>
            <input type="range" id="invert-range" class="input input-range" data-modifier="invert" min="0" step="0.01" max="1" value="1" data-default="1">
            <span class="wrapper">
                <input type="number" id="invert-value" class="input input-number number-value" data-modifier="invert" min="0" step="0.01" max="1" value="1" data-default="1">
            </span>
        </fieldset>
    </span>
`,ot=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="angle" 
        data-filter-string="hue-rotate(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="hue-rotate" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Hue rotate"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Hue rotate" class="is-hidden"></legend>
            <input type="range" id="hue-rotate-range" class="input input-range" data-modifier="hue-rotate" min="0" max="360" value="0" data-default="0">
            <span class="wrapper">
                <input type="number" id="hue-rotate-value" class="input input-number number-value" data-modifier="hue-rotate" min="0" max="360" value="0" data-default="0">
                <label for="hue-rotate-value" class="" data-i18n-html="true" data-i18n="deg"></label>
            </span>
        </fieldset>
    </span>
`,st=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="percentage" 
        data-filter-string="opacity(value)"
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="opacity" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Opacity"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Opacity" class="is-hidden"></legend>
            <input type="range" id="opacity-range" class="input input-range" data-modifier="opacity" min="0" max="100" value="100" data-default="100">
            <span class="wrapper">
                <input type="number" id="opacity-value" class="input input-number number-value" data-modifier="opacity" min="0" max="100" value="100" data-default="100">
                <label for="opacity-value" class="" data-i18n-html="true" data-i18n="per"></label>
            </span>
        </fieldset>
    </span>
`,ct=`
    <span class="container container-filter" 
        data-filter
        data-filter-type="drop-shadow" 
        data-filter-string="drop-shadow(length-1 length-2 length-3 color)" 
        data-requires-checkbox="true"
    >
        <div class="filter-menu">
            <label class="label label-checkbox">
                <input type="checkbox" id="drop-shadow" class="input input-checkbox" data-filter-active-checkbox>
                <span class="label-span" data-i18n="Drop shadow"></span>
            </label>
            <span class="label-span" data-reset-effect data-i18n-attr="title" data-i18n="Reset filter">
                ${w.arrowRepeat}
            </span>
        </div>
        <span class="container-form-text" data-i18n="Only works with (partially) transparent images"></span>
        <label class="label label-small" for="offset-x-range" data-i18n="Offset x"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Offset x" class="is-hidden"></legend>
            <input type="range" id="offset-x-range" class="input input-range" data-modifier="drop-shadow-x-offset" min="-100" max="100" value="0" data-i18n="Offset x" data-i18n-attr="title" data-default="0">
            <span class="wrapper">
                <input type="number" id="offset-x-value" class="input input-number number-value" data-modifier="drop-shadow-x-offset" min="-100" max="100" value="0" data-default="0">
                <label for="offset-x-value" class="" data-i18n="px"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="offset-y-range" data-i18n="Offset y"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Offset y" class="is-hidden"></legend>
            <input type="range" id="offset-y-range" class="input input-range" data-modifier="drop-shadow-y-offset" min="-100" max="100" value="0" data-i18n="Offset y" data-i18n-attr="title" data-default="0">
            <span class="wrapper">
                <input type="number" id="offset-y-value" class="input input-number number-value" data-modifier="drop-shadow-y-offset" min="-100" max="100" value="0" data-default="0">
                <label for="offset-y-value" class="" data-i18n="px"></label>
            </span>
        </fieldset>
        <label class="label label-small" for="blur-radius-range" data-i18n="Blur radius"></label>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Blur radius" class="is-hidden"></legend>
            <input type="range" id="blur-radius-range" class="input input-range" data-modifier="drop-shadow-blur-radius" min="0" max="100" value="0" data-i18n="Blur radius" data-i18n-attr="title" data-default="0">
            <span class="wrapper">
                <input type="number" id="blur-radius-value" class="input input-number number-value" data-modifier="drop-shadow-blur-radius" min="0" max="100" value="0" data-default="0">
                <label for="blur-radius-value" class="" data-i18n="px"></label>
            </span>
        </fieldset>
        <fieldset class="fieldset fieldset-filter wrapper-field-composed">
            <legend data-i18n="Shadow color" class="is-hidden"></legend>
            <label class="label label-small" for="drop-shadow-color-value" data-i18n="Shadow color"></label>
            <input type="color" id="drop-shadow-color-value" class="input input-color" data-modifier="drop-shadow-color" value="#ffffff" data-default="#000000">
        </fieldset>
    </span>
`,lt=[...Object.values(Object.freeze(Object.defineProperty({__proto__:null,brightnessHTML:et,contrastHTML:tt,dropShadowHTML:ct,grayScaleHTML:rt,hueRotateHTML:ot,invertHTML:at,opacityHTML:st,saturateHTML:nt,sepiaHTML:it},Symbol.toStringTag,{value:`Module`}))),...Object.values($e)].join(``),ut=`
  <div id="filters" class="filters">
    <div class="container filters-menu">
      <button type="button" id="deactivate-all-filters" class="button button-icon-text" 
          data-click-action="deactivate-all-filters" data-i18n="Deactivate all filters" data-i18n-attr="title">
          <span class="icon">${w.checkbox}</span>
          <span class="button-label" data-i18n="Deactivate all filters">Deactivate all filters</span>
      </button>
      <button type="button" id="clear-all-filters" class="button button-icon-text" 
          data-click-action="clear-all-filters" data-i18n="Clear all filters" data-i18n-attr="title">
          <span class="icon">${w.arrowRepeat}</span>
          <span class="button-label" data-i18n="Clear all filters">Clear all filters</span>
      </button>
    </div>
    <div id="filter-container" class="filters-grid">
      ${lt}
    </div>
    <div id="svg-filters-container">
    
    </div>
  </div>
`,dt=`
    <div id="image-properties">
        <h2 data-i18n="Image properties"></h2>
        <div class="container">
            <div class="file-properties-wrapper">
<!--                <h3 data-i18n="Image properties"></h3>-->
                <span id="image-orientation-icon" class="icon icon-as-icon-button image-orientation-icon">
                    <span class="landscape-or-portrait">${w.file}</span>
                    <span class="square">${w.square}</span>
                </span>
                <span id="image-orientation" class="label"></span>
                <span id="image-orientation-icon" class="icon icon-as-icon-button">${w.fileEarmark}</span>
                <span id="file-format-current" class="label"></span>&nbsp;
                <span class="icon icon-as-icon-button image-aspect-ratio-icon" data-i18n-attr="title" data-i18n="Aspect ratio">${w.aspectRatio}</span>
                <span id="image-aspect-ratio" class="label"></span>
            </div>
            <div class="file-size-wrapper">
                <span class="icon icon-as-icon-button image-aspect-ratio-icon" data-i18n-attr="title" data-i18n="File size">${w.boxSeam}</span>
                <span id="file-size-original-label" class="label" data-i18n="Original">Original</span>:
                <span id="file-size-original" class="value-display fields-composed-uom"></span>
                <span id="file-size-altered-label" class="label" data-i18n="Altered">Altered</span>:
                <span id="file-size-altered" class="value-display fields-composed-uom"></span>
                <span id="file-size-difference-label" class="label" data-i18n="Difference">Difference</span>:
                <span id="file-size-difference" class="value-display fields-composed-uom"></span>
            </div>
        </div>
    </div>
`,ft=`
    <div id="file-format">
<!--        <h2 data-i18n="File format"></h2>-->
        <div class="container container-buttons">
            <div class="wrapper wrapper-field-composed wrapper-file-format-select">
                <span class="icon icon-as-icon-button" data-i18n-attr="title" data-i18n="File format">${w.fileEarmark}</span>
                <select id="file-format-select" class="select select-file-type fields-composed-field"></select>
            </div>
        </div>
    </div>
`,pt=`
 <fieldset id="menu-fieldset" class="fieldset">
        <h1 class="heading is-hidden" data-i18n="Tools"></h1>
        ${dt}
        ${`
    <div id="resize">
        <h2 data-i18n="Resize"></h2>
        <div class="container">
            <div class="wrapper wrapper-field-composed wrapper-resize">
                <span class="icon icon-as-icon-button">${w.textareaResize}</span>
                <label for="aspect-ratio-select" class="label fields-composed-label" data-i18n-attr="title" data-i18n="Aspect ratio"></label>
                <input type="number" class="input input-number" id="resize-width" size="5">
                <label for="resize-width">×</label>
                <input type="number" class="input input-number" id="resize-height" size="5">
                <label for="resize-height" data-i18n="px"></label>
                <button id="resize-aspect-ratio-lock" class="button button-icon button-selection-lock fields-composed-button" data-click-action="toggleResizeAspectRatioLock" data-i18n="Maintain aspect ratio" data-i18n-attr="title">
                    <span class="icon icon-unlocked">${w.unlock}</span>
                    <span class="icon icon-locked">${w.lock}</span>
                </button>
            </div>
        </div>
    </div>
`}
        ${`
    <div id="rotation">
        <h2 data-i18n="Rotation"></h2>
        <div class="container container-buttons">
            <button type="button" id="rotate-ccw" class="button button-icon-text" data-click-action="rotateCcw" data-i18n="Rotate ccw" data-i18n-attr="title">
                <span class="icon">${w.arrowCounterclockwise}</span>
                <span class="button-label" data-i18n="Rotate ccw"></span>
            </button>
            <button type="button" id="rotate-cw" class="button button-icon-text" data-click-action="rotateCw" data-i18n="Rotate cw" data-i18n-attr="title">
                <span class="icon">${w.arrowClockwise}</span>
                <span class="button-label" data-i18n="Rotate cw"></span>
            </button>
            <div id="free-rotation">
<!--                <h2 data-i18n="Free rotate" class="is-hidden"></h2>-->
                <div class="wrapper-field-composed">
                    <input type="range" id="free-rotation-range" class="input input-range" min="0" max="360" value="0">
                    <input type="number" id="free-rotation-range-value" class="input input-number number-value" min="0" max="360" value="0">
                    <span data-i18n-html="true" data-i18n="deg"></span>
                </div>
            </div>
        </div>
    </div>
`}
        ${`
    <div id="mirroring">
        <h2 data-i18n="Mirroring"></h2>
        <div class="container container-buttons">
            <button type="button" id="flip" class="button button-icon-text" data-click-action="flip" data-i18n="Flip" data-i18n-attr="title">
                <span class="icon">${w.arrowLeftRight}</span>
                <span class="button-label" data-i18n="Flip"></span>
            </button>
            <button type="button" id="flop" class="button button-icon-text" data-click-action="flop" data-i18n="Flop" data-i18n-attr="title">
                <span class="icon">${w.arrowDownUp}</span>
                <span class="button-label" data-i18n="Flop"></span>
            </button>
        </div>
    </div>
`}
        ${`
    <div id="selecting">
        <h2 data-i18n="Selection"></h2>
        <div class="container container-buttons">
            <span id="selection-info">
                <span class="icon icon-as-icon-button" data-i18n-attr="title" data-i18n="Selection size">${w.boundingBox}</span>
                <span id="image-selection-size"></span>
                (<span id="image-selection-aspect-ratio"></span>)
            </span>
            <div id="selection-aspect-ratio" class="wrapper wrapper-field-composed wrapper-aspect-ratio-select display-inline-block">
                <span class="icon icon-as-icon-button" data-i18n-attr="title" data-i18n="Aspect ratio">${w.aspectRatio}</span>
                <select id="aspect-ratio-select" class="select select-aspect-ratios fields-composed-field"></select>
                <button id="selection-aspect-ratio-lock" class="button button-icon button-selection-lock fields-composed-button" data-click-action="toggleSelectionAspectRatioLock" data-i18n="Maintain aspect ratio" data-i18n-attr="title" disabled>
                    <span class="icon icon-unlocked">${w.unlock}</span>
                    <span class="icon icon-locked">${w.lock}</span>
                </button>
            </div>
            <button type="button" id="clear-selection" data-click-action="clearSelection" class="button button-icon" data-i18n="Clear selection" data-i18n-attr="title">
                <span class="icon">${w.eraser}</span>
            </button>
            <button type="button" id="crop" data-click-action="crop" class="button button-icon-text" data-i18n="Crop" data-i18n-attr="title">
                <span class="icon">${w.crop}</span>
                <span class="button-label" data-i18n="Crop"></span>
            </button>
        </div>
    </div>
`}
        ${`
    <div id="other">
        <h2 data-i18n="Other"></h2>
        <div class="container container-buttons">
            <button type="button" id="reset" class="button button-icon-text" data-click-action="reset" data-i18n="Reset" data-i18n-attr="title">
                <span class="icon">${w.arrowRepeat}</span>
                <span class="button-label" data-i18n="Reset"></span>
            </button>
            <button type="button" id="toggle-grid" class="button button-icon" data-click-action="toggleGrid" data-i18n="Toggle grid" data-i18n-attr="title">
                <span class="icon">${w.grid3x3}</span>
            </button>
            <button type="button" id="download" class="button button-icon" data-click-action="download" data-i18n="Download" data-i18n-attr="title">
                <span class="icon">${w.download}</span>
            </button>
            <button type="button" id="edit-help" data-click-action="editHelp" class="button button-icon button-iconHelp" data-i18n="Help" data-i18n-attr="title">
                <span class="icon">${w.questionCircle}</span>
            </button>
        </div>
    </div>
`}
        ${`
    <h2 data-i18n="Save"></h2>
    ${ft}
    <div class="container container-buttons">
        <button type="button" id="save" class="button button-icon-text" data-click-action="save" data-i18n="OK" data-i18n-attr="title">
            <span class="icon">${w.check}</span>
            <span data-i18n="Save"></span>
        </button>
        <button type="button" id="cancel" class="button button-icon-text" data-click-action="cancel" data-i18n="Cancel" data-i18n-attr="title">
            <span class="icon">${w.x}</span>
            <span data-i18n="Cancel"></span>
        </button>
    </div>
`}
        <details id="filter-section" class="custom-details">
            <summary>
                <span aria-details="filter-details">
                    <span class="icon icon-filters-toggle">${w.arrowRightShort}</span>
                    <span class="filters-expand" data-i18n="Filters expand"></span>
                    <span class="filters-collapse" data-i18n="Filters collapse"></span>
                </span>
            </summary>
        </details>
        <div role="definition" id="filter-details" class="details-content">
            ${ut}
        </div>
    </fieldset>
`,mt=`
    <style>
        ${C}
    </style>
    <main id="main" class="main">
        <div id="editor-panel" class="editor-panel" tabindex="0">
            <div id="canvases-wrapper" class="wrapper wrapper-canvases"></div>
            <fieldset id="canvases-buttons" class="fieldset canvases-buttons">
                <span class="wrapper wrapper-field-composed wrapper-glass">
                    <label for="zoom-percentage" class="is-hidden" data-i18n="Zoom level"></label>
                    <input type="number" id="zoom-percentage" class="input input-number" min="1" max="1000" step="1" value="100">
                    <label for="zoom-percentage" id="ee" class="" data-i18n="per" data-action="ee">%</label>
                    <button
                        type="button"
                        id="zoom-fit"
                        class="button button-icon"
                        data-zoom-mode="fit"
                        data-click-action="zoomFit"
                        data-i18n-attr="title"
                        data-i18n="Fit"
                    >
                        <span class="icon">${w.arrowsAngleContract}</span>
                    </button>
                   <button
                        type="button"
                        id="zoom-actual-size"
                        class="button button-icon"
                        data-zoom-mode="actual-size"
                        data-click-action="zoomActualSize"
                        data-i18n-attr="title"
                        data-i18n="Actual size"
                    >
                        <span class="icon">${w.arrowsAngleExpand}</span>
                    </button>
                   <button
                        type="button"
                        id="zoom-fit-width"
                        class="button button-icon"
                        data-zoom-mode="fit-width"
                        data-click-action="zoomFitWidth"
                        data-i18n-attr="title"
                        data-i18n="Fit width"
                    >
                        <span class="icon">${w.arrows}</span>
                    </button>
                   <button
                        type="button"
                        id="zoom-fit-height"
                        class="button button-icon"
                        data-zoom-mode="fit-height"
                        data-click-action="zoomFitHeight"
                        data-i18n-attr="title"
                        data-i18n="Fit height"
                    >
                        <span class="icon">${w.arrowsVertical}</span>
                    </button>
                    
                </span>
                <div class="canvases-zoom-buttons wrapper wrapper-field-composed wrapper-glass">
                    <button type="button" id="zoom-in" class="button button-icon shadow" data-click-action="zoomIn" data-i18n="Zoom in" data-i18n-attr="title">
                        <span class="icon">${w.plus}</span>
                    </button>
                    <button type="button" id="zoom-out" class="button button-icon shadow" data-click-action="zoomOut" data-i18n="Zoom out" data-i18n-attr="title">
                        <span class="icon">${w.dash}</span>
                    </button>
                </div>
            </fieldset>
        </div>
        <div id="editor-menu" class="editor-menu">
            ${pt}
            <p class="version-text">v 1.0.126</p>
            
        </div>
    </main>
    <dialog id="dialog-help" class="dialog dialog-help">
         <div class="dialog-inner">
               <div class="dialog-header">
                    <button type="button" id="dialog-help-close" class="button button-icon button-icon-close" data-click-action="dialogHelpClose" data-i18n="Close" data-i18n-attr="title" autofocus>
                        <span class="icon">${w.xLg}</span>
                    </button>
               </div>
               <div class="dialog-body">
                    <h1>Help</h1>
                    <h2 data-i18n="Crop"></h2>
                    <p class="paragraph" data-i18n="Crop help"></p>
               </div>
         </div>
    </dialog>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
      <defs id="global-svg-defs"></defs>
    </svg>
    <svg style="display: none">
        <defs>
            <filter id="glass-distortion"
                x="0%"
                y="0%"
                width="100%"
                height="100%"
                filterUnits="objectBoundingBox">
                <feTurbulence
                type="fractalNoise"
                baseFrequency="0.015 0.015"
                numOctaves="1"
                seed="5"
                result="turbulence"
                ></feTurbulence>
                <feComponentTransfer in="turbulence" result="mapped">
                    <feFuncR type="gamma" amplitude="1" exponent="1" offset="0"></feFuncR>
                    <feFuncG type="gamma" amplitude="1" exponent="1" offset="0"></feFuncG>
                    <feFuncB type="gamma" amplitude="1" exponent="1" offset="0"></feFuncB>
                </feComponentTransfer>
                <feGaussianBlur in="mapped" stdDeviation="3" result="softMap"></feGaussianBlur>
                <feSpecularLighting
                    in="softMap"
                    surfaceScale="5"
                    specularConstant="1"
                    specularExponent="100"
                    lighting-color="white"
                    result="specLight"
                >
                <fePointLight x="-200" y="-200" z="300"></fePointLight>
                </feSpecularLighting>
                <feComposite
                    in="specLight"
                    operator="arithmetic"
                    k1="0"
                    k2="1"
                    k3="1"
                    k4="0"
                    result="litImage"
                ></feComposite>
                <feDisplacementMap
                    in="SourceGraphic"
                    in2="softMap"
                    scale="60"
                    xChannelSelector="R"
                    yChannelSelector="G"
                    result="distorted"
                ></feDisplacementMap>
            </filter>
        </defs>
    </svg>
`,R={lineWidth:1,selectionLineDashSize:14,crossLineSize:30,handleCornerSize:60,handleEdgeSize:40,handleEdgeMargin:0,touchHandleMultiplier:2,touchHandleMultiplierBreakpoint:`992px`,aspectRatioTolerance:.01,snapThresholdPercentage:.01,zoomPercentageMin:1,zoomPercentageMax:1e3,zoomPercentageStep:10,gridLineCount:10,showSubGrid:!0,drawCanvasWidth:1500,animateSelection:!0,animateFPS:60,selectionHandleStrokeStyle:`rgba(230,230,230,0.9)`,selectionHandleLineDashStrokeStyle:`rgba(0,0,0,0.9)`,selectionHandleOverFillStyle:`rgba(230,230,230, 0.5)`,gridStrokeStyle:`#ccc`,selectionBoxStrokeStyle:`rgba(33,33,33,0.9)`,selectionBoxLineDashStrokeStyle:`rgba(222,222,222,0.9)`,selectionBoxInvalidLineDashStrokeStyle:`red`,subGridStrokeStyle:`#ccc7`,crossStrokeStyle:`#ccc`,debug:!1,selectionAspectRatios:[`free`,`16:10`,`16:9`,`5:3`,`4:3`,`3:2`,`2:1`,`1:1`,`locked`],selectionAspectRatio:`free`,fileFormats:[`image/png`,`image/jpeg`,`image/webp`],rotateDegreesStep:30,minWidth:100,minHeight:100,maxWidth:7040,maxHeight:3960,buttonLabelsEnabled:!0,imagePropertiesEnabled:!0,fileFormatEnabled:!0,rotationEnabled:!0,mirroringEnabled:!0,selectingEnabled:!0,selectionInfoEnabled:!0,selectionAspectRatioEnabled:!0,croppingEnabled:!0,gridEnabled:!0,downloadingEnabled:!0,freeSelectEnabled:!0,freeRotationEnabled:!0,resizingEnabled:!0,filtersEnabled:!0,helpEnabled:!0,buttonLabels:!1,defaultZoomMode:`fit`,defaultActiveAspectRatios:[`free`,`16:10`,`16:9`,`5:3`,`4:3`,`3:2`,`2:1`,`1:1`,`locked`],fallbackFileFormat:[`image/jpeg`],filters:{duotone:{darkColor:`#004F67`,lightColor:`#5ab8a5`}}},ht=class extends u{constructor(){super(),super.setConfiguration(R),j(R.filters)}};function gt(e,t,n,r,i){let a={};r.forEach(e=>{a[e.getAttribute(`data-modifier`)]=e.value}),i(n.replace(`length-1`,`${a[`drop-shadow-x-offset`]}px`).replace(`length-2`,`${a[`drop-shadow-y-offset`]}px`).replace(`length-3`,`${a[`drop-shadow-blur-radius`]}px`).replace(`color`,a[`drop-shadow-color`]||`black`))}function z(e,t,n,r){r(n?e.replace(`value`,`${t[0].value}${n}`):e.replace(`value`,`${t[0].value}`))}var _t={"drop-shadow":gt,percentage:(e,t,n,r,i)=>z(n,r,`%`,i),angle:(e,t,n,r,i)=>z(n,r,`deg`,i),length:(e,t,n,r,i)=>z(n,r,`px`,i),number:(e,t,n,r,i)=>z(n,r,null,i)},vt={"duotone-effect":te,"gamma-effect":ae,"gamma-advanced-effect":be,"inkblot-effect":re,"static-noise-effect":se,"watercolor-effect":le,"ripple-effect":de,"glitch-effect":fe,"soft-glow-effect":me,"vignette-effect":N,"sketch-effect":ge,"pixelate-effect":ve,"morph-effect":Se,"edge-detect":we,"posterize-effect":De,"frosted-glass-effect":ke,"glow-effect":je,"channel-manipulation-effect":Ne,"rgb-shift-effect":Fe,"emboss-effect":Le,"blur-effect":ze,"experimental-effects":Ve,"pastel-effect":Ue,"half-tone-effect":Ge,"half-tone-newspaper-effect":Ye,"half-tone-three-effect":Ze,"color-isolation-filter":L},yt=class{#e;#t=[];canvasImageFilter=null;shadowRoot;elements;constructor(e){if(!e)throw Error(`Must be used with an ImageEditor instance`);this.#e=e,this.shadowRoot=e.shadowRoot,this.elements=e.elements,this.logger=e.logger,this.applyFilterDefaultsFromConfig()}#n(e,t=!1){this.#t=[];let{filterContainer:n}=this.elements;n.querySelectorAll(`[data-filter]`).forEach(n=>{let r=n.querySelector(`[data-filter-active-checkbox]`),{filterType:i,filterString:a}=n.dataset,o=n.querySelectorAll(`[data-modifier]`);this.#a(e,o);let s=[...o].some(e=>e.type===`color`?e.value.toLowerCase()!==e.dataset.default.toLowerCase():e.value!==e.dataset.default);s&&console.log(n,`is modified`),r&&t&&e?.type!==`checkbox`&&s&&(r.checked=!0);let c=r?.checked;n.classList.toggle(`is-active`,c),c&&(i===`svg`?this.#r(n,o):this.#i(n,e,a,i,o))}),this.canvasImageFilter=this.#t.join(` `)||`none`,console.log(`applying filters:`,this.canvasImageFilter),this.#e.updateFilter(this.canvasImageFilter)}#r(e,t){let n=e.getAttribute(`data-filter-name`),r=vt[n];if(r){let{svgFiltersContainer:n}=this.elements,i=r(e,t,n,this);this.#t.push(`url(#${i})`)}else console.log(`No SVG handler for effect: ${n}`)}#i(e,t,n,r,i){let a=_t[r];a?a(e,t,n,i,e=>this.#t.push(e)):console.log(`No CSS handler for filter type: ${r}`)}#a(e,t){e&&t.forEach(t=>{t!==e&&t.getAttribute(`data-modifier`)===e.getAttribute(`data-modifier`)&&(t.value=e.value)})}addFilterEventListeners(){let{filterContainer:e}=this.elements;e.addEventListener(`click`,e=>{if(e.target){if(e.target.closest(`[data-reset-effect]`)){let t=e.target.closest(`.container-filter`);this.clearFilter(t),e.stopPropagation(),e.preventDefault();return}e.target.tagName===`INPUT`&&this.#n(e.target,e.isTrusted??!1)}}),e.addEventListener(`input`,e=>{(e.target.tagName===`INPUT`||e.target.tagName===`SELECT`)&&e.target.type!==`checkbox`&&this.#n(e.target,e.isTrusted??!1)}),e.addEventListener(`contextmenu`,e=>e.preventDefault())}applyFilterDefaultsFromConfig(){}#o(e=this.shadowRoot){e.querySelectorAll(`[data-default]`).forEach(e=>{e.value=e.dataset.default}),this.#n()}clearFilter(e){this.#o(e)}clearAllFilters(){this.#o()}deactivateAllFilters(){this.shadowRoot.querySelectorAll(`[data-filter] [data-filter-active-checkbox]`).forEach(e=>{e.checked=!1}),this.#n()}getImageEditorInstance(){return this.#e}},B={flipped:!1,flopped:!1,flipXAxisDirection:null,flipYAxisDirection:null,flipXOrigin:null,flipYOrigin:null},V={imageFilter:`none`,imageWidth:null,imageHeight:null,imageXOrigin:null,imageYOrigin:null,imageDrawStart:new S(0,0),drawRatio:null,drawWidth:null,drawHeight:null},H={CSSWidth:null,CSSHeight:null,CSSScaleRatio:null},U={canvasImage:null,canvasDraw:null,ctxImage:null,ctxDraw:null},W={ratio:1,percentage:null},bt=class{fps=null;delay=null;time=null;frameCount=-1;rafReference=null;isPlaying=!1;animationCallback=()=>{};constructor(e,t){if(!e||!t)throw Error(`Must provide FPS and animationCallback`);this.fps=e,this.delay=1e3/this.fps,this.animationCallback=t}loop(e){this.time===null&&(this.time=e);let t=Math.floor((e-this.time)/this.delay);t>this.frameCount&&(this.frameCount=t,this.animationCallback({time:e,frameCount:this.frameCount})),this.rafReference=requestAnimationFrame(this.loop.bind(this))}start(){this.isPlaying||(this.isPlaying=!0,this.rafReference=requestAnimationFrame(this.loop.bind(this)))}pause(){this.isPlaying&&(this.isPlaying=!1,this.time=null,this.frameCount=-1,cancelAnimationFrame(this.rafReference))}},G=class e extends t{#e;#t;#n;#r;#i;#a;#o;constructor(e=null,t=null,n=null,r=null,i=null,a=!1,o=null,s=null,c=null,l=null,u=null){super(e,t,n,r,u),this.#e=i,this.#t=a,this.#n=o,this.#r=s,this.#i=c,this.#a=l,this.#o=!1}get name(){return this.#e}set name(e){this.#e=e}get over(){return this.#t}set over(e){this.#t=e}get type(){return this.#n}set type(e){this.#n=e}get cursor(){return this.#r}set cursor(e){this.#r=e}get mode(){return this.#i}set mode(e){this.#i=e}get action(){return this.#a}set action(e){this.#a=e}get active(){return this.#o}set active(e){this.#o=e}get cloned(){return new e(this.x,this.y,this.w,this.h,this.#e,this.#t,this.#n,this.#r,this.#i,this.#a)}};function xt(){return{grab:new G(0,0,0,0,`grab`,!1,`selection`,`grabbing`,`grab`,`grab`),nw:new G(0,0,0,0,`nw`,!1,`corner`,`nwse-resize`,`resize`,`nw-resize`),n:new G(0,0,0,0,`n`,!1,`edge`,`ns-resize`,`resize`,`n-resize`),ne:new G(0,0,0,0,`ne`,!1,`corner`,`nesw-resize`,`resize`,`ne-resize`),e:new G(0,0,0,0,`e`,!1,`edge`,`ew-resize`,`resize`,`e-resize`),se:new G(0,0,0,0,`se`,!1,`corner`,`nwse-resize`,`resize`,`se-resize`),s:new G(0,0,0,0,`s`,!1,`edge`,`ns-resize`,`resize`,`s-resize`),sw:new G(0,0,0,0,`sw`,!1,`corner`,`nesw-resize`,`resize`,`sw-resize`),w:new G(0,0,0,0,`w`,!1,`edge`,`ew-resize`,`resize`,`w-resize`)}}var K={mode:null,action:``,startPointerOver:null,valid:!0,handleAreas:xt(),pointerStart:new S(0,0),pointerCurrent:new S(0,0),area:new x(0,0,0,0),areaScaled:new x(0,0,0,0),wasTouchEvent:!1,aspectRatioLocked:!1,lineDashOffset:0},q={handleCornerSize:null,handleEdgeSize:null,handleEdgeMargin:null,crossLineSize:null,selectionLineDashSize:null},J={show:!1,gap:null,lines:[]},Y={angle:0},X={};function Z(e,t=2,n=!1){if(typeof e!=`number`||isNaN(e)||e===0)return`0 Bytes`;let r=e<0;e=Math.abs(e);let i=1024,a=t<0?0:t,o=[`Bytes`,`KB`,`MB`,`GB`,`TB`,`PB`],s=Math.floor(Math.log(e)/Math.log(i)),c=parseFloat((e/i**+s).toFixed(a));return`${n?r?`−`:`+`:``}${c} ${o[s]}`}function Q(e,t){e&&(t?e.style.removeProperty(`display`):e.style.display=`none`)}function St(e,t,n){e&&e.classList.toggle(t,n)}function Ct(e){let{elements:t}=e;Q(t.imageProperties,e.imagePropertiesEnabled),Q(t.fileFormat,e.fileFormatEnabled),Q(t.rotation,e.rotationEnabled),Q(t.mirroring,e.mirroringEnabled),St(t.main,`show-button-labels`,e.buttonLabelsEnabled),Q(t.selecting,e.selectingEnabled),Q(t.cropping,e.croppingEnabled&&e.selectingEnabled),Q(t.toggleGrid,e.gridEnabled),Q(t.download,e.downloadingEnabled),Q(t.resize,e.resizingEnabled),Q(t.freeRotation,e.freeRotationEnabled),Q(t.filters,!e.filtersEnabled),Q(t.editHelp,e.helpEnabled),Q(t.download,e.downloadingEnabled),Q(t.selectionAspectRatio,e.selectionAspectRatioEnabled),Q(t.selectionInfo,e.selectionInfoEnabled)}var $=class extends ht{static shadowTemplate=mt;static elementLookup=`#main.#canvasesWrapper.#canvasesButtons.#editorPanel.#menuFieldset.#aspectRatioSelect.#editHelp.#dialogHelp.#freeRotation.#freeRotationRange.#freeRotationRangeValue.#imageProperties.#fileFormat.#resizeAspectRatioLock.#selectionAspectRatioLock.#selectionAspectRatio.#selectionInfo.#resize.#resizeWidth.#ee.#resizeHeight.#zoomPercentage.#imageAspectRatio.#imageOrientation.#imageOrientationIcon.#rotation.#mirroring.#selecting.#download.#toggleGrid.#fileFormatCurrent.#fileSizeOriginal.#fileSizeAltered.#fileSizeDifference.#fileFormatSelect.#imageSelectionSize.#imageSelectionAspectRatio.#filterSection.#filterContainer.#svgFiltersContainer`.split(`.`);static translationsPath=`src/lang`;#e=null;#t=0;#n=null;static get observedAttributes(){return[`disabled`]}#r=null;#i;#a;#o;#s;#c=null;#l=null;#u=`fit`;constructor(){super(),this.setup(),this.#d(),this.enableControls(!1),this.#n=new yt(this),this.determineFileFormatSupport(),this.#he(),this.selectionBoxAnimator=new bt(this.config.animateFPS,this.animateSelection.bind(this))}setup(){this.#Ae(this.config.defaultZoomMode)}getImageFileConfiguration(){return{formatsRegex:/.png|.jpg|.jpeg|.webp/,aspectRatioTolerance:.01,minWidth:100,maxWidth:3500,minHeight:100,maxHeight:3500,maxUploadFileSize:3e5,maxEditFileSize:3e5}}connectedCallback(){super.connectedCallback(),console.log(this.supportsCanvasColorSpace(`srgb`)),console.log(this.supportsCanvasColorSpace(`display-p3`)),!this._hasConnected&&(this._hasConnected=!0,this.logger.log(`ImageEditor connectedCallback`),requestAnimationFrame(()=>{this.dispatchCustomEvent(`imageEditorReady`,{})}))}dispatchCustomEvent(e,t={}){this.dispatchEvent(new CustomEvent(e,{bubbles:!0,composed:!0,detail:{...t,imageEditorInstance:this}}))}disconnectedCallback(){super.disconnectedCallback(),this.logger.log(`imageEditor disconnectedCallback`)}supportsCanvasColorSpace(e){return document.createElement(`canvas`).getContext(`2d`,{colorSpace:e})?.getContextAttributes?.()?.colorSpace===e}enableControls(e){let{menuFieldset:t,canvasesButtons:n}=this.elements;e?(t.removeAttribute(`disabled`),n.removeAttribute(`disabled`)):(t.setAttribute(`disabled`,`disabled`),n.setAttribute(`disabled`,`disabled`))}#d(){let{canvasesWrapper:e}=this.elements;v(e),[`image`,`draw`].forEach(e=>{let{canvasesWrapper:t}=this.elements,r=n(e),i=`canvas${r}`,a=`ctx${r}`,o=document.createElement(`canvas`);o.id=`canvas${r}`,o.className=`canvas-${e}`,e===`image`&&(o.innerText=this.translator._(`Image editor canvas`)),t.appendChild(o),U[i]=o,U[a]=o.getContext(`2d`,{colorSpace:`display-p3`})})}#f(){let e=new ResizeObserver(()=>{this.#w()}),{editorPanel:t}=this.elements;e.observe(t)}#p(e){let t=U.canvasImage.toDataURL(e),n=`data:${e};base64,`;return Math.round((t.length-n.length)*3/4)}#m(e){let t=document.createElement(`canvas`);return t.width=t.height=1,t.toDataURL(e)?.includes(`data:${e};base64,`)}#h(e,t){if(isNaN(e)||isNaN(t)||t===0)return`-`;let n=this.config.aspectRatioTolerance,r=e/t,i=`${r.toFixed(2)}:1`;for(let e of Object.values(y))r>e.value-n&&r<e.value+n&&(i+=` (${e.label})`);return i}#g(e,t,n){let r={},i=n*Math.PI/180,a=Math.abs(Math.cos(i)),o=Math.abs(Math.sin(i));return r.width=Math.round(t*o+e*a),r.height=Math.round(t*a+e*o),r}#_(){if(K.mode===`select`)return;let{editorPanel:e}=this.elements;k.width=e.offsetWidth,k.height=e.offsetHeight,F.naturalWidth=this.#a.naturalWidth,F.naturalHeight=this.#a.naturalHeight;let t=this.#g(F.naturalWidth,F.naturalHeight,Y.angle);V.imageWidth=t.width,V.imageHeight=t.height,V.drawRatio=this.config.drawCanvasWidth/V.imageWidth;let n=this.config.drawCanvasWidth/V.imageWidth;V.drawWidth=Math.round(V.imageWidth*n),V.drawHeight=Math.round(V.imageHeight*n),H.CSSScaleRatio=this.#y();let r=this.#v();H.CSSWidth=r.width,H.CSSHeight=r.height,V.imageXOrigin=V.imageWidth/2,V.imageYOrigin=V.imageHeight/2,V.imageDrawStart.x=V.imageWidth/2-F.naturalWidth/2,V.imageDrawStart.y=V.imageHeight/2-F.naturalHeight/2,B.flipXAxisDirection=B.flipped?-1:1,B.flipYAxisDirection=B.flopped?-1:1,B.flipXOrigin=B.flipped?V.imageWidth:0,B.flipYOrigin=B.flopped?V.imageHeight:0,F.aspectRatio=this.#h(F.naturalWidth,F.naturalHeight),F.orientation=V.imageWidth>V.imageHeight?`Landscape`:V.imageHeight>V.imageWidth?`Portrait`:`Square`,J.gap=Math.round(F.naturalWidth/this.config.gridLineCount*V.drawRatio),this.lineWidth=this.#b(this.config.lineWidth),q.selectionLineDashSize=this.#b(this.config.selectionLineDashSize),q.crossLineSize=this.#x(this.config.crossLineSize),q.handleCornerSize=this.#x(this.config.handleCornerSize),q.handleEdgeSize=this.#x(this.config.handleEdgeSize),q.handleEdgeMargin=this.#x(this.config.handleEdgeMargin),window.matchMedia(`(max-width: ${this.config.touchHandleMultiplierBreakpoint})`).matches&&(this.logger.log(`small viewport`),q.handleCornerSize*=this.config.touchHandleMultiplier,q.handleEdgeSize*=this.config.touchHandleMultiplier),this.#ke(),this.#ve()}#v(){let e=Math.round(V.imageWidth*H.CSSScaleRatio),t=Math.round(V.imageHeight*H.CSSScaleRatio);return e>k.width&&(e=k.width),t>k.height&&(t=k.height),{width:e,height:t}}#y(){let e=k.width/V.imageWidth,t=k.height/V.imageHeight;return Math.min(e,t)}#b(e){return Math.ceil(e/W.ratio/H.CSSScaleRatio*V.drawRatio)}#x(e){return Math.ceil(e/W.ratio/H.CSSScaleRatio)}#S(e){this.dispatchCustomEvent(`onCanvasStatusMessage`,{message:e})}#C(){let{canvasesWrapper:e}=this.elements,{canvasImage:t,canvasDraw:n}=U;[t.width,t.height]=[V.imageWidth,V.imageHeight],[n.width,n.height]=[V.drawWidth,V.drawHeight],e.style.width=t.style.width=n.style.width=H.CSSWidth*W.ratio+`px`,e.style.height=t.style.height=n.style.height=H.CSSHeight*W.ratio+`px`}#w(){this.#s.loadStatus===`loaded`&&(this.#T(),this.#E())}#T(){this.#_(),this.#C();let{canvasImage:e,ctxImage:t}=U,n=this.#a;t.clearRect(0,0,e.width,e.height),t.save(),t.imageSmoothingEnabled=!1,t.webkitImageSmoothingEnabled=!1,t.mozImageSmoothingEnabled=!1,t.translate(V.imageXOrigin,V.imageYOrigin),(B.flipped||B.flopped)&&t.scale(B.flipXAxisDirection,B.flipYAxisDirection),t.rotate(Math.PI/180*Y.angle),t.translate(-V.imageXOrigin,-V.imageYOrigin),V.imageFilter&&(t.filter=V.imageFilter),t.drawImage(n,V.imageDrawStart.x,V.imageDrawStart.y,n.naturalWidth,n.naturalHeight),t.restore()}#E(){let{ctxDraw:e,canvasDraw:t}=U;e.clearRect(0,0,t.width,t.height),J.show&&this.#z(),!(K.area.w===0&&K.area.h===0)&&(K.mode===`select`?(this.#P(),this.#F()):this.#Be()&&(this.#P(),this.#F(),this.#O(),this.#R()))}async#D(e,t){if(!this.#_e())return;let{fileFormatSelect:n}=this.elements;n.value||this.#S(this.translator._(`Select a file format first`));let r=n.value,i=document.createElement(`canvas`),a=i.getContext(`2d`,{colorSpace:`display-p3`});i.width=e,i.height=t,a.drawImage(this.#r,0,0,i.width,i.height);try{let e=await m(i.toDataURL(r,1),this.#s.name),t=new g(this.getImageFileConfiguration());await t.load(this.#o,e,null,this.#s.name),this.#we(t,()=>{this.#Te()}).catch(console.error)}catch(e){console.warn(`Error during resizing of image ${e}`)}}#O(){let e=q.handleCornerSize,t=q.handleEdgeSize,n=q.handleEdgeMargin,{x:r,y:i,w:a,h:o}=K.areaScaled,s=a/(2*e+n)>1&&o/(2*e+n)>1,c=new S(r,i),l=new S(r+a-e,i),u=new S(r+a-e,i+o-e),d=new S(r,i+o-e),f=new S(r+e+n,i),p=new S(r+a-t,i+e+n),m=new S(r+e+n,i+o-t),h=new S(r,i+e+n),g=a-2*n-2*e,_=t,v=t,y=o-2*n-2*e;s||(c.x-=e,c.y-=e,l.x+=e,l.y-=e,u.x+=e,u.y+=e,d.x-=e,d.y+=e,f.x=r,f.y=i-t,p.x=r+a,p.y=i,m.x=r,m.y=i+o,h.x=r-t,h.y=i,g=a,y=o),g<50&&(g=0,_=0,f.set(r,i),m.set(r,i)),y<50&&(v=0,y=0,p.set(r,i),h.set(r,i));let b=K.handleAreas;b.grab.set(r,i,a,o),b.nw.set(c.x,c.y,e,e),b.n.set(f.x,f.y,g,_),b.ne.set(l.x,l.y,e,e),b.e.set(p.x,p.y,v,y),b.se.set(u.x,u.y,e,e),b.s.set(m.x,m.y,g,_),b.sw.set(d.x,d.y,e,e),b.w.set(h.x,h.y,v,y)}#k(){let e=V.drawWidth,t=V.drawHeight,n=Math.round(J.gap/5);for(let t=0;t<e;t+=n)J.lines.push({from:new S(t,0),to:new S(t,e),isGridLine:t%J.gap===0});for(let r=0;r<t;r+=n)J.lines.push({from:new S(0,r),to:new S(e,r),isGridLine:r%J.gap===0})}#A(e){let t=this.config.snapThresholdPercentage,n=t*V.imageWidth,r=t*V.imageHeight;return e.x<n&&(e.x=0),e.y<r&&(e.y=0),e.right>V.imageWidth-n&&(e.x=V.imageWidth-e.w),e.bottom>V.imageHeight-r&&(e.y=V.imageHeight-e.h),e}#j(e){let t=V.imageWidth,n=V.imageHeight;return e.x<0||e.x>t||e.w>t||e.right>t||e.y<0||e.y>n||e.h>n||e.bottom>n}#M(e){let{selectionAspectRatioLock:t}=this.elements;K.area.set(e.x,e.y,e.w,e.h),K.areaScaled=K.area.scale(V.drawRatio),t.removeAttribute(`disabled`),this.#ye()}#N(e){let{w:t,h:n}=e;K.valid=t>=this.minWidth&&t<=this.maxWidth&&n>=this.minHeight&&n<=this.maxHeight}#P(){let{ctxDraw:e}=U;e.save();let{x:t,y:n,h:r,w:i}=K.areaScaled;e.lineWidth=this.lineWidth,e.strokeStyle=this.config.selectionBoxStrokeStyle,e.beginPath(),e.rect(t,n,i,r),e.stroke(),e.setLineDash([q.selectionLineDashSize]),e.lineDashOffset=-K.lineDashOffset,e.strokeStyle=K.valid?this.config.selectionBoxLineDashStrokeStyle:this.config.selectionBoxInvalidLineDashStrokeStyle,e.stroke(),e.restore()}#F(){this.config.animateSelection&&this.selectionBoxAnimator.start()}animateSelection(){K.lineDashOffset++,K.lineDashOffset>q.selectionLineDashSize*2&&(K.lineDashOffset=0),this.#P()}#I(){this.selectionBoxAnimator.pause()}#L(){let{ctxDraw:e}=U;e.save(),e.strokeStyle=this.config.crossStrokeStyle,e.lineWidth=this.lineWidth;let{x:t,y:n,w:r,h:i}=K.area,a=new S(t+r/2,n+i/2),o=new S(a.x-q.crossLineSize/2,a.y).scale(V.drawRatio),s=new S(a.x+q.crossLineSize/2,a.y).scale(V.drawRatio),c=new S(a.x,a.y-q.crossLineSize/2).scale(V.drawRatio),l=new S(a.x,a.y+q.crossLineSize/2).scale(V.drawRatio);e.beginPath(),e.moveTo(o.x,o.y),e.lineTo(s.x,s.y),e.moveTo(c.x,c.y),e.lineTo(l.x,l.y),e.stroke(),e.restore()}#R(){let e=U.ctxDraw;e.save(),e.lineWidth=this.lineWidth,e.strokeStyle=this.config.selectionHandleStrokeStyle,e.fillStyle=this.config.selectionHandleOverFillStyle;for(let t of Object.values(K.handleAreas))K.mode===`resize`&&!t.active||(e.beginPath(),e.rect(t.x,t.y,t.w,t.h),t.over===!0&&e.fill(),(t.type===`corner`||t.over===!0||K.wasTouchEvent===!0)&&(e.stroke(),e.save(),e.strokeStyle=this.config.selectionHandleLineDashStrokeStyle,e.setLineDash([15,15]),e.stroke(),e.restore()));e.restore()}#z(){let e=U.ctxDraw;e.save(),e.lineWidth=this.lineWidth;for(let t of J.lines)e.beginPath(),e.moveTo(t.from.x,t.from.y),e.lineTo(t.to.x,t.to.y),t.isGridLine===!0?(e.strokeStyle=this.config.gridStrokeStyle,e.stroke()):this.config.showSubGrid&&(e.strokeStyle=this.config.subGridStrokeStyle,e.stroke());e.restore()}#B(e,t){let{canvasesWrapper:n}=this.elements,r=n.getBoundingClientRect();return new S((e-r.left)/H.CSSScaleRatio/W.ratio,(t-r.top)/H.CSSScaleRatio/W.ratio)}#V(e,t){let n=this.#B(e,t);K.pointerStart.set(n.x,n.y)}#H(e,t){let n=this.#B(e,t);K.pointerCurrent.set(n.x,n.y)}#U(){let e={selectionHandle:!1,resizeHandle:!1},t=K.handleAreas;for(let n of Object.keys(t)){let r=t[n];if(r.pointIsInsideArea(K.pointerCurrent.scale(V.drawRatio))){r.mode===`grab`?(e.selectionHandle=r,e.resizeHandle=!1):r.mode===`resize`&&(e.resizeHandle=r,e.selectionHandle=!1,K.handleAreas[r.name].over=!0);let{canvasesWrapper:t}=this.elements;t.style.cursor=r.cursor}else K.handleAreas[r.name].over=!1}if(!e.selectionHandle&&!e.resizeHandle){let{canvasesWrapper:e}=this.elements;e.style.cursor=`crosshair`}return e}#W(){let{w:e,h:t}=K.area;return{aspectRatio:e/t,aspectRatioLabel:this.#h(e,t)}}#G(){let{aspectRatioSelect:e}=this.elements,t=e.value;return this.#qe(t).value}#K(e){let t=U.ctxImage;t.save(),t.fillStyle=`orange`,t.fillRect(e.x-10,e.y-10,20,20),t.restore()}#q(e){let t=this.#G();return t>-1&&(e.w=e.h*t),e}#J(e){let t=this.#G();return t>-1&&(e.h=e.w/t),e}#Y(){let e=this.#U();return e.resizeHandle?(K.mode=`resize`,K.action=e.resizeHandle.action,K.startPointerOver=e,e.resizeHandle.active=!0):e.selectionHandle?(K.mode=`grab`,K.action=e.selectionHandle.action,K.startPointerOver=e):(K.mode=`select`,K.startPointerOver=null),K.mode}#X(){this.clearSelection()}#Z(e=!1){e?this.#e=1:this.#e!==null&&(this.#e=null);let t=this.#se();this.#N(t),this.#M(t),this.#E()}#Q(){switch(this.#c=K.area.cloned,this.#l=new S(this.#c.xHalfway,this.#c.yHalfway),K.action){case`nw-resize`:case`w-resize`:K.pointerStart.x=K.area.right,K.pointerStart.y=K.area.bottom;break;case`ne-resize`:case`n-resize`:K.pointerStart.x=K.area.left,K.pointerStart.y=K.area.bottom;break;case`se-resize`:case`e-resize`:K.pointerStart.x=K.area.left,K.pointerStart.y=K.area.top;break;case`sw-resize`:case`s-resize`:K.pointerStart.x=K.area.right,K.pointerStart.y=K.area.top;break}}#$(e=!1){e?this.#e===null&&(this.#e=this.#W().aspectRatio):this.#e!==null&&(this.#e=null);let t=this.#ce();this.#j(t)||(this.#N(t),this.#M(t),this.#E())}#ee(){this.#c=K.area.cloned}#te(){let e=this.#c,t=e.cloned;t.x=e.x+K.pointerCurrent.x-K.pointerStart.x,t.y=e.y+K.pointerCurrent.y-K.pointerStart.y,this.#j(t)||(this.#M(t),this.#E(),this.#L())}#ne(e){if(K.mode===`select`)return;let t=e.clientX||e.touches[0].clientX,n=e.clientY||e.touches[0].clientY;this.#V(t,n),e.pointerType===`touch`?(K.wasTouchEvent=!0,this.#H(t,n)):K.wasTouchEvent=!1;let r=this.#Y();r===`select`?this.#X():r===`resize`?this.#Q():r===`grab`&&this.#ee()}#re(e){let t=e.clientX||e.touches[0].clientX,n=e.clientY||e.touches[0].clientY;this.#H(t,n),this.#U();let r=K.mode;r===`select`?this.#Z(e.shiftKey):r===`resize`?this.#$(e.shiftKey):r===`grab`?this.#te():this.#E()}#ie(){K.mode=null;let e=K.handleAreas;for(let t of Object.keys(e)){let n=e[t];n.active=!1}this.#c=null,this.#E()}#ae(e){let t=U.canvasImage,n=U.ctxImage;n.save(),n.clearRect(0,0,t.width,t.height),n.fillRect(K.pointerStart.x-10,K.pointerStart.y-10,20,20),n.strokeStyle=`blue`,n.strokeRect(e.x,e.y,e.w,e.h),n.restore()}#oe(){let e=K.pointerStart;this.#K(e)}#se(){let e=K.pointerStart.x,t=K.pointerStart.y,n=new x(e,t,K.pointerCurrent.x-K.pointerStart.x,K.pointerCurrent.y-K.pointerStart.y);return this.debug&&this.#oe(),n=this.#q(n),n=this.#A(n),this.debug&&this.#ae(n),n}#ce(){let e=this.#c,t=K.action,n=this.#G();this.debug&&this.#oe();let r=[`e-resize`,`w-resize`],i=[`n-resize`,`s-resize`];if(n>-1?(i.push(`ne-resize`),r.push(`nw-resize`,`se-resize`,`sw-resize`)):(r.push(`nw-resize`,`ne-resize`,`se-resize`,`sw-resize`),i.push(`nw-resize`,`ne-resize`,`se-resize`,`sw-resize`)),r.includes(t)&&(e.x=K.pointerStart.x,e.w=K.pointerCurrent.x-K.pointerStart.x),i.includes(t)&&(e.y=K.pointerStart.y,e.h=K.pointerCurrent.y-K.pointerStart.y),n>-1){if((t===`n-resize`||t===`s-resize`)&&(e.x=this.#l.x-e.w/2,e=this.#q(e)),(t===`w-resize`||t===`e-resize`)&&(e.y=this.#l.y-e.h/2,e=this.#J(e)),t===`ne-resize`&&(e=this.#q(e)),t===`nw-resize`){e=this.#J(e);let t=K.pointerStart;e.y=t.y-e.h}(t===`sw-resize`||t===`se-resize`)&&(e=this.#J(e))}return e=this.#A(e),this.debug&&this.#ae(e),e}#le={cancel:()=>{d.fire(`onCloseImageEditor`),this.dispatchCustomEvent(`onCloseImageEditor`,{})},clearSelection:()=>{this.clearSelection()},crop:()=>{this.crop().catch(console.error)},dialogHelpClose:()=>{let{dialogHelp:e}=this.elements;e.close()},download:()=>{this.download()},editHelp:()=>{let{dialogHelp:e}=this.elements;e.showModal()},flip:()=>{this.flip()},flop:()=>{this.flop()},reset:()=>{this.reset()},clearAllFilters:()=>{this.#Ze(),V.imageFilter=`none`,this.#w()},deactivateAllFilters:()=>{this.#Qe(),V.imageFilter=`none`,this.#w()},ee:e=>{e.target.closest(`.wrapper-glass`).classList.toggle(`ee`)},resizeAspectRatioLock:()=>{this.#De()},rotateCcw:()=>{this.rotate(`ccw`)},rotateCw:()=>{this.rotate(`cw`)},save:()=>{this.save()},selectionAspectRatioLock:()=>{this.#He()},toggleGrid:()=>{this.toggleGrid()},zoomIn:()=>{this.zoomIn()},zoomOut:()=>{this.zoomOut()},zoomFit:()=>{this.zoomFit()},zoomActualSize:()=>{this.zoomActualSize()},zoomFitWidth:()=>{this.zoomFitWidth()},zoomFitHeight:()=>{this.zoomFitHeight()}};#ue(e,t=1,n=999,r=!1,i){let a=r?parseFloat(e):parseInt(e),o=Math.min(Math.max(a,t),n);return o!==a&&i(o),o}#de=o((e,t)=>{this.#D(e,t).catch(console.error)},this);#fe=o(()=>{this.#Te()},this);#pe={"+":()=>this.zoomIn(),"-":()=>this.zoomOut(),"=":()=>this.zoomIn(),L:()=>this.rotate(`ccw`),R:()=>this.rotate(`cw`)};#me=e=>{let t=this.#pe[e.key];t&&(e.preventDefault(),t(e))};#he(){let{canvasDraw:e}=U,{canvasesWrapper:t,fileFormatSelect:n}=this.elements,{freeRotationRange:r,freeRotationRangeValue:i}=this.elements,{resizeWidth:o,resizeHeight:s}=this.elements,{zoomPercentage:c}=this.elements;this.addEventListener(`onCanvasStatusMessage`,e=>{console.log(`onCanvasStatusMessage`,e)}),this.addEventListener(`onCloseImageEditor`,e=>{console.log(`onCloseImageEditor`,e)}),this.addEventListener(`onImageSave`,e=>{console.log(`onImageSave`,e)}),Object.entries(this.#le).forEach(([e,t])=>{let n=a(e),r=this.shadowRoot.querySelector(`#${n}`);if(!r){console.error(`element with id "${n}" not found, cannot add event listener`);return}r.addEventListener(`click`,e=>{e.stopPropagation(),e.preventDefault(),t(e)},!1)}),this.shadowRoot.addEventListener(`click`,e=>{let{dialogHelp:t}=this.elements;e.target===t&&this.#le.dialogHelpClose(e)}),this.shadowRoot.addEventListener(`keydown`,this.#me,!1),c.addEventListener(`input`,()=>{let e=c.value,t=this.#ue(e,1,1e3,!0,e=>{c.value=e});W.ratio=this.#Me(t),this.#w()}),n.addEventListener(`change`,()=>{this.#Te()}),r.addEventListener(`input`,e=>{e.preventDefault(),this.clearSelection(!1),Y.angle=parseInt(e.target.value),this.#Le(),this.#w()}),i.addEventListener(`change`,e=>{e.preventDefault(),this.clearSelection(!1),this.#Ie(),Y.angle=this.#ue(e.target.value,0,360,!1,t=>{e.target.value=t}),this.#w()}),o.addEventListener(`input`,e=>{e.preventDefault();let{resizeWidth:t,resizeHeight:n}=this.elements;this.clearSelection(!1),X.aspectRatioLocked&&(n.value=Math.round(t.value/this.#ge())),this.#de(t.value,n.value)}),s.addEventListener(`input`,e=>{e.preventDefault();let{resizeWidth:t,resizeHeight:n}=this.elements;this.clearSelection(!1),X.aspectRatioLocked&&(t.value=Math.round(n.value*this.#ge())),this.#de(t.value,n.value)}),r.addEventListener(`contextmenu`,e=>{e.preventDefault(),e.stopPropagation()}),this.#n.addFilterEventListeners(),e.addEventListener(`pointerdown`,e=>{e.preventDefault(),this.#ne(e)}),e.addEventListener(`pointerup`,e=>{e.preventDefault(),this.#ie()}),e.addEventListener(`pointermove`,e=>{e.preventDefault(),this.#re(e)}),e.addEventListener(`pointerenter`,e=>{e.preventDefault(),t.style.cursor=`crosshair`}),e.addEventListener(`pointerleave`,e=>{e.preventDefault(),this.#ie(),t.style.cursor=`default`}),e.addEventListener(`touchstart`,e=>e.preventDefault()),e.addEventListener(`contextmenu`,e=>{e.preventDefault(),e.stopPropagation()},!0)}#ge(){return this.#r.width/this.#r.height}download(){if(this.#_e()){let e=this.getFileFormatSelectValue();h(U.canvasImage.toDataURL(e),this.#Se(this.#s.name,e))}}#_e(){let{fileFormatSelect:e}=this.elements;return e.value?this.#be()?!0:(e.focus(),this.#S(this.translator._(`Conversion to this file format not supported`)),!1):this.fileFormatEnabled?(e.focus(),this.#S(this.translator._(`Select a file format first`)),!1):(e.value=this.config.fallbackFileFormat,!0)}reset(){this.resetProperties(),this.#we(this.#i).catch(console.error)}toggleGrid(){J.show=!J.show,this.#E()}async crop(){if(!this.#Be()){this.#S(this.translator._(`Make selection before cropping`));return}if(!K.valid){this.#S(this.translator._(`Invalid crop siz`));return}let e=this.getSelectionAsDataUrl();try{let t=await m(e,this.#s.name),n=new g(this.getImageFileConfiguration());await n.load(this.#o,t,null,this.#s.name),this.#we(n,()=>{this.#Te()}).catch(console.error)}catch{this.logger.log(`Something went wrong during processing of cropped ImageFile`)}this.resetProperties(),this.#Qe()}getSelectionAsDataUrl(){let e=document.createElement(`canvas`),t=e.getContext(`2d`,{colorSpace:`display-p3`});t.imageSmoothingEnabled=!1;let{x:n,y:r,w:i,h:a}=K.area;return e.width=i,e.height=a,t.drawImage(U.canvasImage,n,r,i,a,0,0,i,a),e.toDataURL(`image/png`,1)}#ve(){let{fileFormatCurrent:e,resizeWidth:t,resizeHeight:n,zoomPercentage:r,imageAspectRatio:i,imageOrientation:a,imageOrientationIcon:o}=this.elements;e.innerText=this.#i.mimeType,t.value=F.naturalWidth,n.value=F.naturalHeight,r.value=W.percentage,i.innerText=F.aspectRatio,a.innerText=this.translator._(F.orientation),F.orientation===`Landscape`?(o.classList.add(`image-orientation-icon-landscape`),o.classList.remove(`image-orientation-icon-portrait`),o.classList.remove(`image-orientation-icon-square`)):F.orientation===`Portrait`?(o.classList.remove(`image-orientation-icon-landscape`),o.classList.add(`image-orientation-icon-portrait`),o.classList.remove(`image-orientation-icon-square`)):F.orientation===`Square`&&(o.classList.remove(`image-orientation-icon-landscape`),o.classList.remove(`image-orientation-icon-portrait`),o.classList.add(`image-orientation-icon-square`)),this.#fe()}#ye(){let{imageSelectionSize:e,imageSelectionAspectRatio:t}=this.elements,{w:n,h:r}=K.area;e.innerText=Math.floor(n)+` x `+Math.floor(r),t.innerText=this.#h(n,r)}clearSelection(e=!0){let{selectionAspectRatioLock:t}=this.elements;K.area.set(0,0,0,0),K.areaScaled.set(0,0,0,0);let n=K.handleAreas;return Object.keys(n).forEach(e=>{n[e].set(0,0,0,0)}),this.#ye(),this.#I(),t.setAttribute(`disabled`,`disabled`),e&&this.#E(),this}#be(){let{fileFormatSelect:e}=this.elements;return!e.options[e.selectedIndex].hasAttribute(`disabled`)}#xe(e){let{fileFormatSelect:t}=this.elements,n=!1;for(let r of t.options)r.value===e&&!r.hasAttribute(`disabled`)&&(n=!0);return n}#Se(e,t){let n=t.split(`/`)[1];return`${e.replace(/\.[^/.]+$/,``)}.${n}`}save(){U.canvasImage.toBlob(e=>{let{fileFormatSelect:t}=this.elements,n=t.value;if(this.#_e()){let t=this.#Se(this.#s.name,n),r=new File([e],t,{type:n});this.dispatchCustomEvent(`onImageSave`,{file:r})}else console.log(`requirements not met`)},this.getFileFormatSelectValue())}#Ce(e){let{editorPanel:t}=this.elements;t.classList.toggle(`canvases-image-loaded`,!e)}async#we(e,t){if(e instanceof g){this.#Ce(!0),this.#s=e;try{let n=await f(e.imageObjectURL);this.#a=n,this.#w(),this.#Ce(!1),t?.(n)}catch(e){this.logger.log(`error`,e),console.error(`error`,e)}}}setImageAsImageFile(e,t){if(!e||!(t instanceof g))throw Error(`setImageAsImageFile(imageFile). Not all arguments passed / valid.`);this.#i=t,this.#o=e,this.#we(t,e=>{this.#r=e,this.enableControls(!0),this.resetProperties(),this.#f(),this.#Je(),this.#Ue(),this.#et(),this.enableFilters(),this.#tt(t.mimeType),this.#Ee(),this.#Te(),this.#Ve(),this.#k(),Ct(this)}).catch(console.error)}async setImageAsImageSource(e){if(!(e instanceof _))throw Error(`setImageAsImageSource(imageSource), Not all arguments passed / valid.`);let t=new g(this.getImageFileConfiguration());try{await t.load(e.id,null,e.src,e.name),this.setImageAsImageFile(e.id,t)}catch{console.warn(`setImageAsImageSource: Could not create ImageFile`)}}setImage(e,t,n){let r=new _(e,`${t}${t.includes(`?`)?`&`:`?`}v=${Date.now()}`,n);this.setImageAsImageSource(r).catch(console.error)}#Te(){let{fileSizeAltered:e,fileSizeDifference:t}=this.elements,{fileFormatCurrent:n}=this.elements,{fileFormatSelect:r}=this.elements,i=r.value||this.#s.mimeType,a=this.#p(i),o=a-this.#t;e.innerText=Z(a,1);let s;s=`${Z(o,1,!0)}`,t.innerHTML=s}#Ee(){let{fileSizeOriginal:e}=this.elements,t=this.#s.mimeType;this.#t=this.#p(t),e.innerText=Z(this.#t,1)}resetProperties(){this.#Re().#$e().#We().#Ge().#Fe().#ze().#Xe().#Ze().#Oe().clearSelection().#w()}updateResizeRatioLockState(){let{resizeAspectRatioLock:e}=this.elements,t=X.aspectRatioLocked;e.classList.toggle(`locked`,t),e.classList.toggle(`is-active`,t)}#De(){X.aspectRatioLocked=!X.aspectRatioLocked,this.updateResizeRatioLockState()}#Oe(){let e={"actual-size":this.zoomActualSize,fit:this.zoomFit,"fit-width":this.zoomFitWidth,"fit-height":this.zoomFitHeight}[this.config.defaultZoomMode];return e&&e.call(this),this}zoom(e){let{zoomPercentageMin:t,zoomPercentageMax:n,zoomPercentageStep:r}=this.config,i=this.#Pe();e>0?i<=n-r&&(i+=r):i>=t+r&&(i-=r),W.ratio=this.#Me(i),this.#w()}zoomIn(){this.zoom(1)}zoomOut(){this.zoom(-1)}#ke(){W.percentage=this.#Ne(W.ratio)}zoomFit(){this.#Ae(`fit`),W.ratio=1,this.#w()}zoomFitWidth(){this.#Ae(`fit-width`),W.ratio=k.width/F.naturalWidth/H.CSSScaleRatio,W.percentage=this.#Ne(W.ratio),this.#w()}zoomFitHeight(){this.#Ae(`fit-height`),W.ratio=k.height/F.naturalHeight/H.CSSScaleRatio,W.percentage=this.#Ne(W.ratio),this.#w()}zoomActualSize(){this.#Ae(`actual-size`),W.ratio=F.naturalWidth/H.CSSWidth,W.percentage=this.#Ne(W.ratio),this.#w()}#Ae(e){this.#u=e,this.#je()}#je(){this.shadowRoot.querySelectorAll(`[data-zoom-mode]`).forEach(e=>{let t=e.getAttribute(`data-zoom-mode`);e.classList.toggle(`is-active`,t===this.#u)})}#Me(e){return e/100/H.CSSScaleRatio}#Ne(e){return(e*H.CSSScaleRatio*100).toFixed(0)}#Pe(){let{zoomPercentage:e}=this.elements;return parseFloat(e.value)}#Fe(){return B.flipped=!1,B.flopped=!1,this}flip(){B.flipped=!B.flipped,this.clearSelection(!1),this.#w()}flop(){B.flopped=!B.flopped,this.clearSelection(!1),this.#w()}#Ie(){let{freeRotationRange:e}=this.elements;e.value=Y.angle}#Le(){let{freeRotationRangeValue:e}=this.elements;e.value=Y.angle}#Re(){let{freeRotationRange:e,freeRotationRangeValue:t}=this.elements;return e.value=Y.angle,t.value=Y.angle,this}#ze(){return Y.angle=0,this}rotate(e){this.clearSelection(!1),e===`ccw`?(Y.angle-=this.rotateDegreesStep,Y.angle<0&&(Y.angle=360-Math.abs(Y.angle))):e===`cw`&&(Y.angle+=this.rotateDegreesStep,Y.angle>360&&(Y.angle-=360)),this.#Ie(),this.#Le(),this.#w()}#Be(){let{w:e,h:t}=K.area;return e!==0&&t!==0}#Ve(){let e=this.shadowRoot.querySelector(`option[id="ratio_locked"]`),{aspectRatioSelect:t,selectionAspectRatioLock:n}=this.elements;if(this.freeSelectEnabled)if(K.aspectRatioLocked){let{aspectRatio:r,aspectRatioLabel:i}=this.#W();n.classList.add(`locked`,`is-active`),e.removeAttribute(`disabled`),e.label=`${i}`,this.#qe(`locked`).value=r,t.value=`locked`}else n.classList.remove(`locked`,`is-active`),e.setAttribute(`disabled`,`disabled`),e.label=this.translator._(`Locked`),this.#We()}#He(){K.aspectRatioLocked=!K.aspectRatioLocked,this.#Ve()}#Ue(){let{aspectRatioSelect:e}=this.elements;v(e);for(let t of y)if(t.active){let n=document.createElement(`template`);n.innerHTML=`<option value="${t.name}" id="ratio_${t.name}">${this.translator._(t.label)}</option>`,e.appendChild(n.content)}this.#We()}#We(){let{aspectRatioSelect:e}=this.elements;return e.value=this.#Ke().name,this}#Ge(){return K.aspectRatioLocked&&this.#He(),this}#Ke(){let e=this.#qe(this.selectionAspectRatio);return e?.active?e:y.find(e=>e.active)||y[0]}#qe(e){return y.find(t=>t.name===e)}#Je(){let e=this.selectionAspectRatios;y.forEach(t=>{t.active=e.includes(t.name)});let t=y.find(e=>e.name===`free`);t.active=this.freeSelectEnabled;let n=y.find(e=>e.name===`locked`);n.active=this.freeSelectEnabled}enableFilters(){let{filterSection:e}=this.elements;e.style.display=this.shouldEnableFilters()?`block`:`none`}#Ye(){return!!U?.canvasImage?.getContext(`2d`)?.filter}shouldEnableFilters(){return this.#Ye()&&this.filtersEnabled}#Xe(){return J.show=!1,this}#Ze(){return this.#n.clearAllFilters(),this}#Qe(){return this.#n.deactivateAllFilters(),this}updateFilter(e){V.imageFilter=e,this.#w()}determineFileFormatSupport(){for(let e of b)e.supported=this.#m(e.value)}getFileFormatSelectValue(){let{fileFormatSelect:e}=this.elements;return e.value}#$e(){let{fileFormatSelect:e}=this.elements;return e.value=this.#i.mimeType,this}#et(){let e=this.fileFormats;for(let t of b)t.active=e.includes(t.value)}#tt(e){let{fileFormatSelect:t}=this.elements;v(t);let n=document.createElement(`template`);n.innerHTML=`<option hidden value="" data-i18n="Choose file format"></option>`,t.appendChild(n.content);for(let e of b){let n=document.createElement(`template`);n.innerHTML=`<option value="${e.value}" id="fileFormat_${e.name}" ${e.supported&&e.active?``:`data-i18n-attr="title" data-i18n="Conversion to this file format not supported" disabled`}>${e.label}</option>`,t.appendChild(n.content)}e&&this.#xe(e)?t.value=e:t.value=``}get freeSelectEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`free-select`,this.config.freeSelectEnabled)}set freeSelectEnabled(e){this.setAttributeAsBoolean(`free-select`,e)}get imagePropertiesEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`image-properties`,this.config.imagePropertiesEnabled)}set imagePropertiesEnabled(e){this.setAttributeAsBoolean(`image-properties`,e)}get fileFormatEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`file-format`,this.config.fileFormatEnabled)}set fileFormatEnabled(e){this.setAttributeAsBoolean(`file-format`,e)}get rotationEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`rotation`,this.config.rotationEnabled)}set rotationEnabled(e){this.setAttributeAsBoolean(`rotation`,e)}get mirroringEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`mirroring`,this.config.mirroringEnabled)}set mirroringEnabled(e){this.setAttributeAsBoolean(`mirroring`,e)}get buttonLabelsEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`button-labels`,this.config.buttonLabelsEnabled)}set buttonLabelsEnabled(e){this.setAttributeAsBoolean(`button-labels`,e)}get selectingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`selecting`,this.config.selectingEnabled)}set selectingEnabled(e){this.setAttributeAsBoolean(`selecting`,e)}get croppingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`cropping`,this.config.croppingEnabled)}set croppingEnabled(e){this.setAttributeAsBoolean(`cropping`,e)}get gridEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`grid-enabled`,this.config.gridEnabled)}set gridEnabled(e){this.setAttributeAsBoolean(`grid-enabled`,e)}get downloadingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`downloading`,this.config.downloadingEnabled)}set downloadingEnabled(e){this.setAttributeAsBoolean(`downloading`,e)}get resizingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`resizing`,this.config.resizingEnabled)}set resizingEnabled(e){this.setAttributeAsBoolean(`resizing`,e)}get freeRotationEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`free-rotation`,this.config.freeRotationEnabled)}set freeRotationEnabled(e){this.setAttributeAsBoolean(`free-rotation`,e)}get helpEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`help-enabled`,this.config.helpEnabled)}set helpEnabled(e){this.setAttributeAsBoolean(`help-enabled`,e)}get selectionAspectRatioEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`selection-aspect-ratio-enabled`,this.config.selectionAspectRatioEnabled)}set selectionAspectRatioEnabled(e){this.setAttributeAsBoolean(`selection-aspect-ratio-enabled`,e)}get selectionInfoEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`selection-info-enabled`,this.config.selectionInfoEnabled)}set selectionInfoEnabled(e){this.setAttributeAsBoolean(`selection-info-enabled`,e)}get filtersEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse(`filters`,this.config.filtersEnabled)}set filtersEnabled(e){this.setAttributeAsBoolean(`filters`,e)}get minWidth(){return this.getAttributeAsInteger(`min-width`,this.config.minWidth)}set minWidth(e){this.setAttributeAsInteger(`min-width`,e)}get minHeight(){return this.getAttributeAsInteger(`min-height`,this.config.minHeight)}set minHeight(e){this.setAttributeAsInteger(`min-height`,e)}get maxWidth(){return this.getAttributeAsInteger(`max-width`,this.config.maxWidth)}set maxWidth(e){this.setAttributeAsInteger(`max-width`,e)}get maxHeight(){return this.getAttributeAsInteger(`max-height`,this.config.maxHeight)}set maxHeight(e){this.setAttributeAsInteger(`max-height`,e)}get selectionAspectRatios(){let e=this.config.selectionAspectRatios??this.config.defaultActiveAspectRatios;return this.getAttributeAsCSV(`selection-aspect-ratios`,e)}set selectionAspectRatios(e){this.setAttributeAsCSV(`selection-aspect-ratios`,e)}get selectionAspectRatio(){let e=this.getAttributeOrDefault(`selection-aspect-ratio`,this.config.selectionAspectRatio);return this.selectionAspectRatios.includes(e)||console.warn(`ImageEditor: ${e} not in list with selectionAspectRatios ${this.selectionAspectRatios.join(`, `)}`),e}set selectionAspectRatio(e){this.setAttributeToString(`selection-aspect-ratio`,e),this.#We()}get fileFormats(){return this.getAttributeAsCSV(`file-formats`,this.config.fileFormats)}set fileFormats(e){return this.setAttributeAsCSV(`file-formats`,e)}get rotateDegreesStep(){return this.getAttributeAsInteger(`rotate-degrees-step`,this.config.rotateDegreesStep)}set rotateDegreesStep(e){this.setAttributeAsInteger(`rotate-degrees-step`,e)}get canvasImageWidth(){return V.imageWidth}get canvasImageHeight(){return V.imageHeight}};customElements.define(`image-editor`,$),$.translationsPath=`/js/vendor/image-editor/lang`;