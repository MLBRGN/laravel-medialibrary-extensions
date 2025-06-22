var oi=Object.defineProperty;var Zt=u=>{throw TypeError(u)};var ni=(u,s,e)=>s in u?oi(u,s,{enumerable:!0,configurable:!0,writable:!0,value:e}):u[s]=e;var d=(u,s,e)=>ni(u,typeof s!="symbol"?s+"":s,e),ut=(u,s,e)=>s.has(u)||Zt("Cannot "+e);var n=(u,s,e)=>(ut(u,s,"read from private field"),e?e.call(u):s.get(u)),g=(u,s,e)=>s.has(u)?Zt("Cannot add the same private member more than once"):s instanceof WeakSet?s.add(u):s.set(u,e),c=(u,s,e,t)=>(ut(u,s,"write to private field"),t?t.call(u,e):s.set(u,e),e),o=(u,s,e)=>(ut(u,s,"access private method"),e);import{b as P}from"./image-editor-listener.js";var me,be;const qt=class qt{constructor(s,e){g(this,me);g(this,be);this.set(s,e)}scale(s){return new qt(this.x*s,this.y*s)}get x(){return n(this,me)}get y(){return n(this,be)}set x(s){c(this,me,s)}set y(s){c(this,be,s)}set(s,e){c(this,me,s),c(this,be,e)}};me=new WeakMap,be=new WeakMap;let w=qt;var B,V,N,j,X,ve,Ue;const ot=class ot{constructor(s,e,t,i,r){g(this,ve);g(this,B);g(this,V);g(this,N);g(this,j);g(this,X,{});this.set(s,e,t,i,r)}pointIsInsideArea(s){return s.x>this.x&&s.x<this.x+this.w&&s.y>this.y&&s.y<this.y+this.h}scale(s){return new ot(this.x*s,this.y*s,this.w*s,this.h*s,n(this,X))}set(s,e,t,i,r){c(this,B,s),c(this,V,e),c(this,N,t),c(this,j,i),r&&typeof r=="object"&&c(this,X,r),o(this,ve,Ue).call(this)}get x(){return n(this,B)}get y(){return n(this,V)}get w(){return n(this,N)}get h(){return n(this,j)}get top(){return n(this,V)}get right(){return n(this,B)+n(this,N)}get bottom(){return n(this,V)+n(this,j)}get left(){return n(this,B)}get aspectRatio(){return n(this,N)/n(this,j)}getOption(s){return n(this,X)[s]}set x(s){c(this,B,s)}set y(s){c(this,V,s)}set w(s){c(this,N,s),o(this,ve,Ue).call(this)}set h(s){c(this,j,s),o(this,ve,Ue).call(this)}get xHalfway(){return this.x+(this.right-this.left)/2}get yHalfway(){return this.y+(this.bottom-this.top)/2}setOption(s,e){return n(this,X)[s]=e,e}get cloned(){return new ot(n(this,B),n(this,V),n(this,N),n(this,j),n(this,X))}};B=new WeakMap,V=new WeakMap,N=new WeakMap,j=new WeakMap,X=new WeakMap,ve=new WeakSet,Ue=function(){this.w<0&&(this.w=Math.abs(this.w),this.x=this.x-this.w),this.h<0&&(this.h=Math.abs(this.h),this.y=this.y-this.h)};let Fe=ot;var we,xe;class ri{constructor(s=!1,e=""){g(this,we,!1);g(this,xe,"");c(this,we,s),c(this,xe,e)}log(...s){n(this,we)&&console.log(n(this,xe),...s)}error(...s){n(this,we)&&console.error(n(this,xe),...s)}}we=new WeakMap,xe=new WeakMap;function Ut(u,s){return new ri(u,s)}const li=async u=>new Promise((s,e)=>{const t=new Image;t.onload=()=>{s(t)},t.onerror=i=>{e(t,i)},t.src=u}),ci=u=>new Promise((s,e)=>{const t=new Image;t.onload=()=>{s({width:t.width,height:t.height})},t.onerror=i=>{e(i)},t.src=u.imageObjectURL}),pt=async(u,s,e)=>{let t;try{const i=await fetch(u,{signal:e}),r=await i.blob(),l=i.headers.get("content-type");t=new File([r],s,{type:l})}catch(i){console.log(i)}return t},di=(u,s)=>{const e=document.createElement("a");e.href=u,e.download=s,e.click(),e.remove()};var E,H,_,te,R,ye,De,Pe,Se,ae,ie,Ae,Oe,y,ft,Jt,Qt,mt,nt,bt,ea,vt;class Le{constructor(s={}){g(this,y);d(this,"configuration",{formatsRegex:/.png|.jpg|.jpeg|.webp/,forceAspectRatio:null,aspectRatioTolerance:.01,minWidth:100,maxWidth:3500,minHeight:100,maxHeight:3500,maxUploadFileSize:1e6,maxEditFileSize:5e6});g(this,E,null);g(this,H,null);g(this,_,null);g(this,te,null);g(this,R,"pending");g(this,ye,null);g(this,De,null);g(this,Pe,null);g(this,Se,null);g(this,ae,null);g(this,ie,null);g(this,Ae,null);g(this,Oe,{valid:!0,rejectionMessages:[],validityMessages:[],rejected:!1});g(this,nt,s=>{c(this,ae,s.width),c(this,ie,s.height),c(this,Ae,s.width/s.height)});this.logger=Ut(!0,"ImageFile"),this.configuration=Object.assign({},this.configuration,s)}async load(s,e,t,i="no_name",r=()=>{}){c(this,E,s),c(this,_,e),c(this,H,t),this.name=i,await o(this,y,ft).call(this,n(this,_),n(this,H),null,r)}loadDefer(s,e,t,i="no_name"){c(this,E,s),c(this,_,e),c(this,H,t),this.name=i}async loadDeferred(s,e=()=>{}){return o(this,y,ft).call(this,n(this,_),n(this,H),s,e)}destroy(){URL.revokeObjectURL(n(this,te))}get file(){return n(this,_)}get validity(){return n(this,Oe)}get loadStatus(){return n(this,R)}get name(){return n(this,ye)}set name(s){c(this,ye,s)}get width(){return n(this,ae)}get src(){return n(this,H)}get height(){return n(this,ie)}get mimeType(){return n(this,De)}get imageObjectURL(){return n(this,te)}}E=new WeakMap,H=new WeakMap,_=new WeakMap,te=new WeakMap,R=new WeakMap,ye=new WeakMap,De=new WeakMap,Pe=new WeakMap,Se=new WeakMap,ae=new WeakMap,ie=new WeakMap,Ae=new WeakMap,Oe=new WeakMap,y=new WeakSet,ft=async function(s,e,t,i=()=>{}){c(this,R,"loading"),P.fire("onImageFileLoadStart",{imageFile:this,intId:n(this,E)}),s?await o(this,y,Jt).call(this,s,i):e?await o(this,y,Qt).call(this,e,t,i):this.logger.log("ImageFile #load must be called with either src or file")},Jt=async function(s,e=()=>{}){c(this,_,s),c(this,H,URL.createObjectURL(s)),o(this,y,mt).call(this,s);try{c(this,R,"loaded"),c(this,te,URL.createObjectURL(n(this,_))),await o(this,y,bt).call(this)}catch(t){c(this,R,"loadError"),this.logger.log(`#loadSrc: could not load image dimensions ${t}`)}o(this,y,vt).call(this),e(n(this,E),this),P.fire("onImageFileLoadEnd",{imageFile:this,intId:n(this,E)})},Qt=async function(s,e,t=()=>{}){c(this,R,"loading"),P.fire("onImageFileLoadStart",{imageFile:this,intId:n(this,E)}),c(this,H,s);try{const i=await pt(n(this,H),this.name,e);c(this,_,i),o(this,y,mt).call(this,i),c(this,R,"loaded"),c(this,te,URL.createObjectURL(n(this,_)));try{await o(this,y,bt).call(this)}catch(r){this.logger.log(`#loadSrc: could not load image dimensions: ${r}`,n(this,R))}}catch(i){c(this,R,"loadError"),this.logger.log(`#loadSrc: could not load src: ${i}`,n(this,R))}o(this,y,vt).call(this),t(n(this,E),this),P.fire("onImageFileLoadEnd",{imageFile:this,intId:n(this,E)})},mt=function(s){c(this,De,s.type),c(this,Pe,o(this,y,ea).call(this,s.type)),c(this,Se,s.size),c(this,ye,s.name)},nt=new WeakMap,bt=async function(){try{const s=await ci(this);n(this,nt).call(this,s)}catch(s){this.logger.log(`#getImageDimension: could not get image dimensions: ${s}`,n(this,R))}},ea=function(s){return s.substring(s.lastIndexOf("/")+1)},vt=function(){const s=n(this,Oe),{minWidth:e,minHeight:t,maxWidth:i,maxHeight:r}=this.configuration,{maxUploadFileSize:l,maxEditFileSize:h}=this.configuration,p=b=>{s.valid=!1,s.rejected=!0,s.validityMessages.push(b),s.rejectionMessages.push(b)},m=b=>{s.valid=!1,s.validityMessages.push(b)};if(n(this,R)==="loadError"){p("Load error");return}if(n(this,ae)<e&&p("Width too small"),n(this,ie)<t&&p("Height too small"),n(this,ae)>i&&p("Width too large"),n(this,ie)>r&&p("Height too large"),this.configuration.formatsRegex||console.log("empty formatsRegex!"),this.configuration.formatsRegex.test(`.${n(this,Pe)}`)||p("Wrong file format"),n(this,Se)>h&&p("Filesize too large"),n(this,Se)>l&&m("Filesize too large"),this.configuration.forceAspectRatio!==null){const b=this.configuration.forceAspectRatio,A=this.configuration.aspectRatioTolerance;(n(this,Ae)<b-A||n(this,Ae)>b+A)&&m("Wrong aspect ratio")}P.fire("onImageFileValidated",{imageFile:this,intId:n(this,E)})};const gt=u=>{for(;u!=null&&u.firstChild;)u.removeChild(u.firstChild)},hi=u=>!!u&&u.constructor===Object,ui=u=>u.charAt(0).toUpperCase()+u.slice(1),gi=u=>Number(u)===u&&u%1===0,pi=u=>Number(u)===u&&u%1!==0,ta=u=>u.replace(/[A-Z]+(?![a-z])|[A-Z]/g,(s,e)=>(e?"-":"")+s.toLowerCase()),fi=function(u,s,e=500){let t;return(...i)=>{clearTimeout(t),t=setTimeout(()=>{u.apply(s,i)},e)}},mi=`
    :host {
        --canvas-wrapper-bg-color: #666;
        --container-info-bg-color: #fafafa;
        --container-info-fg-color: var(--container-fg-color);
        --container-info-border: 1px dotted #333;
        --container-info-padding: .5em;

        display: block;
    }

    :host([icon-delete]) .tileIconDelete {
        display: inline-block;
    }

    /* main */

    .main {
        display: flex;
        background-color: var(--container-bg-color);
        flex-direction: row;
        justify-content: stretch;
        align-items: stretch;
        gap: var(--container-gap);
        height: 100%;
        width: 100%;
    }

    /* when no image loaded, canvases are not displayed */
    .canvas--image,
    .canvas--draw {
        display: none;
    }

    /* when an image loaded, canvases are displayed */
    .canvases--image-loaded .canvas--image,
    .canvases--image-loaded .canvas--draw {
        display: block;
    }

    /* canvases */

    .editor__canvases {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1 1 600px;
        height: auto;
        background-color: var(--canvas-wrapper-bg-color);
        overflow: hidden;
        /*aspect-ratio: 16/9;*/
        min-width: 0;
        min-height: 0;
    }

    .canvas--image {
        background-color: #cdcdcd;
        background-image: repeating-linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white), repeating-linear-gradient(45deg, white 25%, transparent 25%, transparent 75%, white 75%, white);
        background-position: 0 0, 10px 10px;
        background-size: calc(2 * 10px) calc(2 * 10px);
    }

    .canvas--draw {
        z-index: 1;
        position: absolute;
        left: 0;
        top: 0;
    }

    /* wrappers */

    .wrapper {
    }

    .wrapper--canvases {
        position: relative;
    }

    .wrapper--canvases {

    }

    .wrapper--field {
        display: flex;
        align-items: center;
        max-width: 300px;
        /*white-space: nowrap;*/
        margin-bottom: 1em;
        gap: var(--field-gap);
    }

    .wrapper--field-composed {
        display: flex;
        max-width: 300px;
        align-items: center;
        justify-content: start;
        background-color: var(--button-bg-color);
        color: var(--button-fg-color);
        border-radius: var(--border-radius);
        padding: 0 .5em;
        font-size: var(--font-size-smaller);
        gap: 0.4em;
    }

    .wrapper--field-uom {
        display: inline-block;
        position: relative;
    }

    .wrapper--file-format-select,
    .wrapper--aspect-ratio-select {
        margin-bottom: 5px;
    }

    /* zoom buttons and zoom level */

    .canvases__buttons {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: end;
        justify-content: center;
        gap: var(--control-gap);
        top: 1em;
        right: 1em;
        z-index: 2;
    }

    .canvases__zoom-buttons {
        display: flex;
        flex-direction: column;
        gap: var(--control-gap);
        align-items: end;
        justify-content: center;
    }

    /* file type */

    .fields-composed__label {
        min-width: 0;
        flex: 1 1;
    }

    .fields-composed__field {
        flex: 1 0;
    }

    .fields-composed__label {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        flex: 1 1;
    }

    .fields-composed__uom {
        flex: 0 1;
        white-space: nowrap;
    }

    .fields-composed__button {
        flex: 0 1;
    }

    /* */
    .legend {
        min-width: 0;
    }

    .number_uom {
        min-width: 0;
    }

    .value-display {
        margin-left: 0.5em;
    }

    .fieldset:disabled .uom,
    .fieldset:disabled .header,
    .fieldset:disabled .value-display {
        color: var(--field-disabled-fg-color);
    }

    .uom {
    }


    /* buttons */

    .button--selection-lock {
        background-color: var(--button-bg-color);
        color: var(--button-fg-color);
        fill: pink;
        margin-left: 5px;
        padding: 0 5px;
    }

    .button--selection-lock .icon--locked {
        display: none;
    }

    .button--selection-lock.locked .icon--unlocked {
        display: none;
    }

    .button--selection-lock.locked .icon--locked {
        display: initial;
    }

    /* select */

    .select--file-type,
    .select--selection-aspect-ratios {
        padding: 0;
        border: 0;
    }

    /* menu */

    .editor__menu {
        padding: 0 1em 1em;
        flex: 1 1 300px;
        min-width: 0;
        min-height: 0;
        overflow-y: auto;
    }

    /* range */

    .input--range {
        min-width: 0;
        flex: 4 1;
    }

    .input--number {
        min-width: 50px;
        /*flex: 0 1;*/
    }

    /* containers */

    .container {

    }

    .container--buttons {
        display: block;
        flex: 1 1;
        gap: .4em;
        flex-wrap: wrap;
        margin-bottom: .4em;
    }

    .container--form-text {
        font-size: var(--font-size-smaller);
        display: block;
        padding: 1em 0;
    }

    .container--filter,
    .container--info {
        background-color: var(--container-info-bg-color);
        color: var(--container-info-fg-color);
        border: var(--container-info-border);
        padding: var(--container-info-padding);
        border-radius: var(--border-radius);
    }

    .container--filter {
        padding-left: 1em;
        padding-right: 1em;
    }

    .container--info {
        /*display: none;*/
        flex-direction: column;
        align-items: start;
        /*font-size: var(--font-size-normal);*/
        font-size: 0.9rem;
        flex: 1 1;
        gap: .4em;
        flex-wrap: wrap;
        margin-bottom: .4em;
    }

    .container--filters {
        display: none;
        grid-template-columns: 1fr;
        gap: 1em;
    }

    .container--filters.show {
        display: grid;
    }

    .imageInfoText {
        font-size: var(--font-size-smaller);
    }

    .filters {
        max-height: 400px;
        overflow-y: auto;
    }
    
    /* svg filters */
    .svgFilter {
        width:0;
        height:0;
        position:absolute;
    }
    
    @media only screen and (max-width: 576px) {
        .main {
            flex-direction: column;
        }

        .editor__canvases {
            flex: 1 2 auto;
        }

        .editor__menu {
            flex: 1 1 auto;
            padding: 1em;
        }
    }

    @media only screen and (max-width: 768px) {
        .main {
            gap: 0;
        }

        .editor__canvases {
            flex: 1 1 auto;
        }

        .editor__menu {
            flex: 1 1 180px;
            overflow-y: auto;
            padding-top: 1em;
        }

        .header {
            display: none;
        }

        /*.button__label {*/
        /*    display: none;*/
        /*}*/

    }`,v={crop:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-crop" viewBox="0 0 16 16">  <path d="M3.5.5A.5.5 0 0 1 4 1v13h13a.5.5 0 0 1 0 1h-2v2a.5.5 0 0 1-1 0v-2H3.5a.5.5 0 0 1-.5-.5V4H1a.5.5 0 0 1 0-1h2V1a.5.5 0 0 1 .5-.5zm2.5 3a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V4H6.5a.5.5 0 0 1-.5-.5z"/></svg>',download:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>  <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>',arrowClockwise:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">  <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>  <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/></svg>',arrowCounterclockwise:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-counterclockwise" viewBox="0 0 16 16">  <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>  <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/></svg>',arrowDownUp:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">  <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/></svg>',arrowLeftRight:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">  <path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/></svg>',grid3x3:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid-3x3" viewBox="0 0 16 16">  <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h13A1.5 1.5 0 0 1 16 1.5v13a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13zM1.5 1a.5.5 0 0 0-.5.5V5h4V1H1.5zM5 6H1v4h4V6zm1 4h4V6H6v4zm-1 1H1v3.5a.5.5 0 0 0 .5.5H5v-4zm1 0v4h4v-4H6zm5 0v4h3.5a.5.5 0 0 0 .5-.5V11h-4zm0-1h4V6h-4v4zm0-5h4V1.5a.5.5 0 0 0-.5-.5H11v4zm-1 0V1H6v4h4z"/></svg>',check:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">  <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>',xLg:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">  <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/></svg>',x:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>',eraser:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eraser" viewBox="0 0 16 16">  <path d="M8.086 2.207a2 2 0 0 1 2.828 0l3.879 3.879a2 2 0 0 1 0 2.828l-5.5 5.5A2 2 0 0 1 7.879 15H5.12a2 2 0 0 1-1.414-.586l-2.5-2.5a2 2 0 0 1 0-2.828l6.879-6.879zm2.121.707a1 1 0 0 0-1.414 0L4.16 7.547l5.293 5.293 4.633-4.633a1 1 0 0 0 0-1.414l-3.879-3.879zM8.746 13.547 3.453 8.254 1.914 9.793a1 1 0 0 0 0 1.414l2.5 2.5a1 1 0 0 0 .707.293H7.88a1 1 0 0 0 .707-.293l.16-.16z"/></svg>',aspectRatio:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-aspect-ratio" viewBox="0 0 16 16">  <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5v-9zM1.5 3a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z"/>  <path d="M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H3v2.5a.5.5 0 0 1-1 0v-3zm12 7a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H13V8.5a.5.5 0 0 1 1 0v3z"/></svg>',questionCircle:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>  <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/></svg>',plus:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>',dash:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">  <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/></svg>',lock:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">  <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/></svg>',unlock:'<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-unlock" viewBox="0 0 16 16">  <path d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2zM3 8a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1H3z"/></svg>',arrowRepeat:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
  <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
  <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
</svg>`,boxArrowInRight:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
  <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
</svg>`,boxArrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
</svg>`,boundingBox:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bounding-box" viewBox="0 0 16 16">
  <path d="M5 2V0H0v5h2v6H0v5h5v-2h6v2h5v-5h-2V5h2V0h-5v2H5zm6 1v2h2v6h-2v2H5v-2H3V5h2V3h6zm1-2h3v3h-3V1zm3 11v3h-3v-3h3zM4 15H1v-3h3v3zM1 4V1h3v3H1z"/>
</svg>`,arrowDownShort:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-short" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v5.793l2.146-2.147a.5.5 0 0 1 .708.708l-3 3a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L7.5 10.293V4.5A.5.5 0 0 1 8 4"/>
</svg>`,arrowUpShort:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-short" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5"/>
</svg>`},bi=`

    :host {

        /* semantic colors */
        --error-bg-color: white;
        --error-fg-color: red;
        --error-hover-bg-color:red;
        --error-hover-fg-color:white;

        --warning-bg-color: white;
        --warning-fg-color: #fd7e14;
        --warning-hover-bg-color: #fd7e14;
        --warning-hover-fg-color: white;

        --success-bg-color: white;
        --success-fg-color: darkgreen;
        --success-hover-bg-color: darkgreen;
        --success-hover-fg-color: white;

        --info-bg-color: white;
        --info-fg-color: blue;
        --info-hover-bg-color: blue;
        --info-hover-fg-color: white;

        /* neutral colors */
        --neutral-bg-color: rgb(233, 233, 237);
        --neutral-fg-color: rgb(0, 0, 0);
        --neutral-disabled-bg-color: rgba(239, 239, 239, 0.3);
        --neutral-disabled-fg-color: rgba(16, 16, 16, 0.3);
        --neutral-hover-bg-color: rgb(208, 208, 215);
        --neutral-hover-fg-color: rgb(0, 0, 0);

        --neutral-field-bg-color: white;
        --neutral-field-fg-color: black;
        --neutral-field-disabled-bg-color: white;
        --neutral-field-disabled-fg-color: rgb(170, 170, 170);
        --neutral-field-hover-bg-color: initial;
        --neutral-field-hover-fg-color: initial;

        /* primary / secondary not implemented */

        /* accent color */
        --accent-color: blue;
        --accent-color-lighter: royalBlue;

        --border: 1px solid rgb(227, 227, 227);
        --border-radius: 5px;

        --container-bg-color: #fff;
        --container-fg-color: #000;

        --control-bg-color: var(--neutral-bg-color);
        --control-fg-color: var(--neutral-fg-color);
        --control-disabled-bg-color: var(--neutral-disabled-bg-color);
        --control-disabled-fg-color: var(--neutral-disabled-fg-color);
        --control-hover-bg-color: var(--neutral-hover-bg-color);
        --control-hover-fg-color: var(--neutral-hover-fg-color);
        --control-border: var(--border);
        --control-border-radius: var(--border-radius);
        --control-gap: .5em;
        --control-padding: 5px;

        --field-bg-color: var(--neutral-field-bg-color);
        --field-fg-color: var(--neutral-field-fg-color);
        --field-disabled-bg-color: var(--neutral-field-disabled-bg-color);
        --field-disabled-fg-color: var(--neutral-field-disabled-fg-color);
        --field-hover-bg-color: var(--neutral-field-hover-bg-color);
        --field-hover-fg-color: var(--neutral-field-hover-fg-color);
        --field-border: var(--border);
        --field-border-radius: var(--border-radius);
        --field-accent-color: var(--accent-color);
        --field-accent-alt-color: var(--accent-color-lighter);
        --field-padding: 0;/* non zero value causes controls to grow wrapper */
        --field-gap:.5em;

        --container-gap: 1em;

        --font-size-normal: 1em;
        --font-size-smaller: 0.8em;
        --grid-gap: 1em;

        --button-bg-color: var(--control-bg-color);
        --button-fg-color: var(--control-fg-color);
        --button-disabled-fg-color: var(--control-disabled-fg-color);
        --button-disabled-bg-color: var(--control-disabled-bg-color);
        --button-hover-bg-color: var(--control-hover-bg-color);
        --button-hover-fg-color: var(--control-hover-fg-color);
        --button-padding: var(--control-padding);
        --button-border-radius: var(--control-border-radius);
        --button-border: var(--control-border);

        --h1-color: var(--container-fg-color);
        --h2-color: var(--container-fg-color);

        --range-track-color: var(--field-bg-color);
        --range-thumb-color: var(--field-accent-color);
        --range-thumb-color-focussed: var(--field-accent-alt-color);
        --range-disabled-track-color: var(--field-disabled-bg-color);
        --range-disabled-thumb-color: var(--field-disabled-fg-color);

        --icon-width: 16px;
        --icon-height: 16px;
        --icon-bg-color: transparent;
        --icon-fg-color: var(--control-fg-color);

        --dialog-bg-color: var(--container-bg-color);
        --dialog-fg-color: var(--container-fg-color);

        --dialog-help-width: 50vw;
        --dialog-help-height: 50vh;

        --status-bg-color: var(--container-bg-color);

    }

    :host([hidden]) {
        display: none;
    }

    *, *:before, *:after {
        box-sizing: border-box;
    }

    .header {

    }

    .header--1 {
        font-size: clamp(1.5em, 1.5vw, 2em);
        color: var(--h1-color);
    }

    .header--2 {
        font-size: clamp(1em, 1vw, 1.7em);
        margin-top: 10px;
        margin-bottom: 10px;
        color: var(--h2-color);
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

    .li {
        list-style: inside;
    }

    /* dialog */

    .dialog {
        background-color: var(--container-bg-color);
        border-radius: 10px;
        color: var(--container-fg-color);
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

    .dialog .header--1,
    .dialog .header--2 {
        margin-top: 0;
        margin-bottom: 5px;
        color: var(--container-fg-color);
    }

    .dialog .ul {
        padding: 0;
    }

    .dialog.backdrop::backdrop {
        background: #000a !important;
    }

    .dialog__inner {
        display: flex;
        flex-direction: column;
        padding: 0;
        width: 100%;
        height: 100%;
    }

    .dialog__header {
        display: flex;
        align-items: start;
        justify-content: end;
        flex: 0 0 auto;
        position: relative;
        width: 100%;
        padding: .5em 0.5em 0;
    }

    .dialog__body {
        flex: 1 1 auto;
        overflow-y: auto;
        padding: 0 1em 1em;
        min-height:0;
    }

    .dialog__body .paragraph {
        margin-top: 0;
        font-size: var(--font-size-smaller);
    }

    .dialog--help {
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
    }

    .input:disabled {
        background-color: var(--field-disabled-bg-color);
        color: var(--field-disabled-fg-color);
    }

    .input:focus-visible,
    .input:hover {
        background-color: var(--field-hover-bg-color);
        color: var(--field-hover-fg-color);
    }

    .input--checkbox {
          vertical-align: middle;
          margin-left:0;
    }

    /* input range styles */

    /*********** Baseline, reset styles ***********/

    .input--range {
        -webkit-appearance: none;
        appearance: none;
        background: transparent;
    }

    /* Removes default focus */
    .input-range:hover,
    .input--range:focus {
        outline: none;
    }

    /******** Chrome, Safari, Opera and Edge Chromium styles ********/

    /* slider track */

    .input--range::-webkit-slider-runnable-track {
        background-color: var(--range-track-color);
        border-radius: 0.5rem;
        height: 0.5rem;
    }

    .input--range:disabled::-webkit-slider-runnable-track {
        background-color: var(--range-disabled-track-color);
    }

    /* slider thumb */

    .input--range::-webkit-slider-thumb {
        -webkit-appearance: none; /* Override default look */
        appearance: none;
        margin-top: -4px; /* Centers thumb on the track */
        background-color: var(--range-thumb-color);
        border-radius: 0.5rem;
        height: 1rem;
        width: 1rem;
    }

    .input--range:disabled::-webkit-slider-thumb {
        background-color: var(--range-disabled-thumb-color);
    }

    .input--range:focus::-webkit-slider-thumb {
        outline: 3px solid var(--range-thumb-color-focussed);
        outline-offset: 0.125rem;
    }

    /*********** Firefox styles ***********/

    /* slider track */

    .input--range::-moz-range-track {
        background-color: var(--range-track-color);
        border-radius: 0.5rem;
        height: 0.5rem;
    }

    .input--range:disabled::-moz-range-track {
        background-color: var(--range-disabled-track-color);
    }

    /* slider thumb */

    .input--range::-moz-range-thumb {
        background-color: var(--range-thumb-color);
        border: none; /*Removes extra border that FF applies*/
        border-radius: 0.5rem;
        height: 1rem;
        width: 1rem;
    }

    .input--range:disabled::-moz-range-thumb {
        background-color: var(--range-disabled-thumb-color);
    }

    .input--range:focus::-moz-range-thumb {
        outline: 3px solid var(--range-thumb-color-focussed);
        outline-offset: 0.125rem;
    }

    /* button */

    .button {
        --_bg-color: var(--button-bg-color);
        --_fg-color: var(--button-fg-color);
        background-color: var(--_bg-color);
        color: var(--_fg-color);
        border-radius: var(--button-border-radius);
        border: var(--button-border);
        padding: var(--button-padding);
        /*user-select: none;*/
    }

    .button .icon {
        vertical-align: middle;
    }

    .button:disabled {
        --_disabled-bg-color: var(--button-disabled-bg-color);
        --_disabled-fg-color: var(--button-disabled-fg-color);
        background-color: var(--_disabled-bg-color);
        color: var(--_disabled-fg-color);
    }

    .button:focus-visible:not([disable]),
    .button:hover:not([disabled]) {
        --_hover-bg-color: var(--button-hover-bg-color);
        --_hover-fg-color: var(--button-hover-fg-color);
        background-color: var(--_hover-bg-color);
        color: var(--_hover-fg-color);
    }

    .button--icon {
        display: inline-block;
        padding: 5px;
        aspect-ratio:1;
    }

    .button--icon .icon {
        vertical-align: middle;
        color: inherit;
    }

    .button--icon-text {
        display: inline-block;
        margin-bottom: 5px;
    }

    .button--icon-text .icon {
        margin-right: 5px;
        vertical-align: middle;
    }

    .button--icon-close {
        padding: 0;
        margin: 0;
    }

    .button__label {
        /*white-space:nowrap;*/
        /*text-overflow: ellipsis;*/
        /*overflow: hidden;*/
    }

    .button--warning {
        --button-bg-color: var(--warning-bg-color);
        --button-fg-color: var(--warning-fg-color);
        --button-hover-bg-color: var(--warning-hover-bg-color);
        --button-hover-fg-color: var(--warning-hover-fg-color);
        --button-disabled-bg-color: var(--button-disabled-bg-color);
        --button-disabled-fg-color: var(--warning-fg-color);
    }

    .button--error {
        --button-bg-color: var(--error-bg-color);
        --button-fg-color: var(--error-fg-color);
        --button-hover-bg-color: var(--error-hover-bg-color);
        --button-hover-fg-color: var(--error-hover-fg-color);
        --button-disabled-bg-color: var(--button-disabled-bg-color);
        --button-disabled-fg-color: var(--error-fg-color);
    }

    .button--success {
        --button-bg-color: var(--success-bg-color);
        --button-fg-color: var(--success-fg-color);
        --button-hover-bg-color: var(--success-hover-bg-color);
        --button-hover-fg-color: var(--success-hover-fg-color);
        --button-disabled-bg-color: var(--button-disabled-bg-color);
        --button-disabled-fg-color: var(--success-fg-color);
    }

    .button--info {
        --button-bg-color: var(--info-bg-color);
        --button-fg-color: var(--info-fg-color);
        --button-hover-bg-color: var(--info-hover-bg-color);
        --button-hover-fg-color: var(--info-hover-fg-color);
        --button-disabled-bg-color: var(--button-disabled-bg-color);
        --button-disabled-fg-color: var(--info-fg-color);
    }

    /* icon */

    .icon {
        --_icon-bg-color: var(--icon-bg-color);
        --_icon-fg-color: var(--icon-fg-color);
        background-color: var(--_icon-bg-color);
        color: var(--_icon-fg-color);
        display: inline-block;
        width: var(--icon-width);
        height: var(--icon-height);
        vertical-align: middle;
    }

    .icon--warning {
        --icon-bg-color: var(--warning-bg-color);
        --icon-fg-color: var(--warning-fg-color);
    }

    .icon--error {
        --icon-bg-color: var(--error-bg-color);
        --icon-fg-color: var(--error-fg-color);
    }

    .icon--success {
        --icon-bg-color: var(--success-bg-color);
        --icon-fg-color: var(--success-fg-color);
    }

    .icon--info {
        --icon-bg-color: var(--info-bg-color);
        --icon-fg-color: var(--info-fg-color);
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

    .icon--stretch {
        width:100%;
        height:100%;
    }

    .icon--stretch svg {
        width:100%;
        height:100%;
    }

    /* label */

    .label {
        display:inline;
        font-size:var(--font-size-normal);
    }

    .label--small {
        display:block;
        font-size:var(--font-size-smaller);
    }

    .label--checkbox {
        display: inline-block;
        padding-right: 10px;
        white-space: nowrap;
        margin-bottom:1em;
    }

    .label--checkbox .label__span {
        vertical-align: middle;
    }

    /* select */

    .select {
        background-color: var(--field-bg-color);
        color: var(--field-fg-color);
        border-radius: var(--field-border-radius);
        border: var(--field-border);
        padding: var(--field-padding);
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

    /* state modifiers
     https://github.com/chris-pearce/css-guidelines#state-hooks
     */

    .is-active {}
    .has-loaded {}
    .is-loading {}
    .is-visible {}
    .is-disabled {}
    .is-expanded {}
    .is-collapsed {}

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
`,vi=`
    <div id="filters" class="filters">
        <h2 class="header header--2" part="header-2" data-i18n="Filters"></h2>
        <label class="label label--checkbox">
            <input type="checkbox" class="input input--checkbox" id="show-filters">
                <span class="label__span" data-i18n="Show filters"></span>
        </label>
        <div id="filter-container" class="container container--filters">
             <span class="container--filter" data-filter-type="percentage" data-filter-string="brightness(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="brightness" class="input input--checkbox">
                    <span class="label__span" data-i18n="Brightness"></span>
                </label>
                 <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Brightness" class="is-hidden"></legend>
                    <input type="range" id="brightness-range" class="input input--range" data-modifier="brightness"part="range" min="0" max="200" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="brightness-value" class="input input--number number__value" data-modifier="brightness" part="input" min="0" max="200" value="100" data-default="100">
                        <label class="" for="brightness-value" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="percentage" data-filter-string="contrast(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="contrast" class="input input--checkbox">
                    <span class="label__span" data-i18n="Contrast"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Contrast" class="is-hidden"></legend>
                    <input type="range" id="contrast-range" class="input input--range" data-modifier="contrast" min="0" part="range" max="200" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="contrast-value" class="input input--number number__value" data-modifier="contrast" part="input" min="0" max="200" value="100" data-default="100">
                        <label for="contrast-value" class="" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="percentage" data-filter-string="saturate(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="saturate" class="input input--checkbox">
                    <span class="label__span" data-i18n="Saturate"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Saturate" class="is-hidden"></legend>
                    <input type="range" id="saturate-range" class="input input--range" data-modifier="saturation" part="range" min="0" max="200" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="saturate-value" class="input input--number number__value" data-modifier="saturation" part="input" min="0" max="200" value="100" data-default="100">
                        <label for="saturate-value" class="" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="svg" data-svg-filter-effect="gamma-effect">
                <label class="label label--checkbox">
                    <input type="checkbox" id="gamma" class="input input--checkbox">
                    <span class="label__span" data-i18n="Gamma"></span>
                </label>
                <label class="label label--small" for="exponent" data-i18n="Exponent"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Exponent" class="is-hidden"></legend>
                    <input type="range" id="exponent" class="input input--range" data-modifier="gamma-exponent" part="range" min="0" max="3" step="0.01" value="1" data-i18n="Exponent" data-i18n-attr="title" data-default="1">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="exponent-value" class="input input--number number__value" data-modifier="gamma-exponent" part="input" min="0" max="3" value="1" data-default="1">
                        <label for="exponent-value" class="" data-i18n="exp"></label>
                    </span>
                </fieldset>
                <label class="label label--small"  for="amplitude" data-i18n="Amplitude"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Amplitude" class="is-hidden"></legend>
                    <input type="range" id="amplitude" class="input input--range" data-modifier="gamma-amplitude" part="range" min="0" max="3" step="0.01" value="1" data-i18n="Amplitude" data-i18n-attr="title" data-default="1">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="amplitude-value" class="input input--number number__value" data-modifier="gamma-amplitude" part="input" min="0" max="2.5" value="1" data-default="1">
                        <label for="amplitude-value" class="" data-i18n="amp"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="percentage" data-filter-string="grayscale(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="grayscale" class="input input--checkbox">
                    <span class="label__span" data-i18n="Grayscale"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Grayscale" class="is-hidden"></legend>
                    <input type="range" id="grayscale-range" class="input input--range" data-modifier="grayscale" part="range" min="0" max="100" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="grayscale-value" class="input input--number number__value" data-modifier="grayscale" part="input" min="0" max="100" value="0" data-default="0">
                        <label for="grayscale-value" class="" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="percentage" data-filter-string="sepia(value)">
                 <label class="label label--checkbox">
                    <input type="checkbox" id="sepia" class="input input--checkbox">
                    <span class="label__span" data-i18n="Sepia"></span>
                 </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Sepia" class="is-hidden"></legend>
                    <input type="range" id="sepia-range" class="input input--range" data-modifier="sepia" part="range" min="0" max="100" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="sepia-value" class="input input--number number__value" data-modifier="sepia" part="input" min="0" max="100" value="0" data-default="0">
                        <label for="sepia-value" class="" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="percentage" data-filter-string="invert(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="invert" class="input input--checkbox">
                    <span class="label__span" data-i18n="Invert"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Invert" class="is-hidden"></legend>
                    <input type="range" id="invert-range" class="input input--range" data-modifier="invert" part="range" min="0" max="100" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="invert-value" class="input input--number number__value" data-modifier="invert" part="input" min="0" max="100" value="0" data-default="0">
                        <label for="invert-value" class="" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="length" data-filter-string="blur(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="blur" class="input input--checkbox">
                    <span class="label__span" data-i18n="Blur"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Blur" class="is-hidden"></legend>
                    <input type="range" id="blur-range" class="input input--range" data-modifier="blur" part="range" min="0" max="10" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="blur-value" class="input input--number number__value" data-modifier="blur" part="input" min="0" max="100" value="0" data-default="0">
                        <label for="blur-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="drop-shadow" data-filter-string="drop-shadow(length-1 length-2 length-3 color)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="drop-shadow" class="input input--checkbox">
                    <span class="label__span" data-i18n="Drop shadow"></span>
                </label>
                <span class="container--form-text" data-i18n="Only works with (partially) transparent images"></span>
                <label class="label label--small" for="offset-x-range" data-i18n="Offset x"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Offset x" class="is-hidden"></legend>
                    <input type="range" id="offset-x-range" class="input input--range" data-modifier="drop-shadow-x-offset" part="range" min="-100" max="100" value="0" data-i18n="Offset x" data-i18n-attr="title" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="offset-x-value" class="input input--number number__value" data-modifier="drop-shadow-x-offset" part="input" min="-100" max="100" value="0" data-default="0">
                        <label for="offset-x-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
                <label class="label label--small" for="offset-y-range" data-i18n="Offset y"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Offset y" class="is-hidden"></legend>
                    <input type="range" id="offset-y-range" class="input input--range" data-modifier="drop-shadow-y-offset" part="range" min="-100" max="100" value="0" data-i18n="Offset y" data-i18n-attr="title" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="offset-y-value" class="input input--number number__value" data-modifier="drop-shadow-y-offset" part="input" min="-100" max="100" value="0" data-default="0">
                        <label for="offset-y-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
                <label class="label label--small" for="blur-radius-range" data-i18n="Blur radius"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Blur radius" class="is-hidden"></legend>
                    <input type="range" id="blur-radius-range" class="input input--range" data-modifier="drop-shadow-blur-radius" part="range" min="0" max="100" value="0" data-i18n="Blur radius" data-i18n-attr="title" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="blur-radius-value" class="input input--number number__value" data-modifier="drop-shadow-blur-radius" part="input" min="0" max="100" value="0" data-default="0">
                        <label for="blur-radius-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Shadow color" class="is-hidden"></legend>
                    <label class="label label--small" for="drop-shadow-color-value" data-i18n="Shadow color"></label>
                    <input type="color" id="drop-shadow-color-value" class="input input--color" data-modifier="drop-shadow-color" part="input" value="#ffffff" data-default="#ffffff">
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="svg" data-svg-filter-effect="duotone-effect">
                <label class="label label--checkbox">
                    <input type="checkbox" id="duo-tone" class="input input--checkbox">
                    <span class="label__span" data-i18n="Duotone"></span>
                </label>
                <fieldset class="fieldset">
                    <label class="label label--small" for="duo-tone-color1" data-i18n="Replace darker colors with"></label>
                    <input type="color" id="duo-tone-color1" class="input input--color" data-modifier="color1" part="input" value="#ff0000" data-default="#ff0000">
                    <label class="label label--small" for="duo-tone-color2" data-i18n="Replace lighter colors with"></label>
                    <input type="color" id="duo-tone-color2" class="input input--color" data-modifier="color2" part="input" value="#0000ff" data-default="#0000ff">
                </fieldset>
            </span>
<!--            <span class="container&#45;&#45;filter" data-filter-type="svg" data-svg-filter-effect="noiseEffect">-->
<!--                <label class="label label&#45;&#45;checkbox">-->
<!--                    <input type="checkbox" id="noise" class="input input&#45;&#45;checkbox">-->
<!--                    <span class="label__span" data-i18n="Noise"></span>-->
<!--                </label>-->
<!--            </span>-->
            <span class="container--filter" data-filter-type="angle" data-filter-string="hue-rotate(value)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="hue-rotate" class="input input--checkbox">
                    <span class="label__span" data-i18n="Hue rotate"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Hue rotate" class="is-hidden"></legend>
                    <input type="range" id="hue-rotate-range" class="input input--range" data-modifier="hue-rotate" part="range" min="0" max="360" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="hue-rotate-value" class="input input--number number__value" data-modifier="hue-rotate" part="input" min="0" max="360" value="0" data-default="0">
                        <label for="hue-rotate-value" class="" data-i18n-html="true" data-i18n="deg"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="percentage" data-filter-string="opacity(value)">
                 <label class="label label--checkbox">
                    <input type="checkbox" id="opacity" class="input input--checkbox">
                    <span class="label__span" data-i18n="Opacity"></span>
                </label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Opacity" class="is-hidden"></legend>
                    <input type="range" id="opacity-range" class="input input--range" data-modifier="opacity" part="range" min="0" max="100" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="opacity-value" class="input input--number number__value" data-modifier="opacity" part="input" min="0" max="100" value="100" data-default="100">
                        <label for="opacity-value" class="" data-i18n-html="true" data-i18n="per"></label>
                    </span>
                </fieldset>
            </span>
        </div>
    </div>
    <svg class="svgFilter">
      <filter id="gamma-effect">
        <feComponentTransfer>
           <feFuncR type="gamma" exponent="1.5" amplitude="2.5" offset="0" />
           <feFuncG type="gamma" exponent="1.5" amplitude="2.5" offset="0" />
           <feFuncB type="gamma" exponent="1.5" amplitude="2.5" offset="0" />
        </feComponentTransfer>
       </filter>
    </svg>
    <svg class="svgFilter">
        <filter id="duotone-effect">
            <feColorMatrix type="matrix" values=".33 .33 .33 0 0
                  .33 .33 .33 0 0
                  .33 .33 .33 0 0
                  0 0 0 1 0">
            </feColorMatrix>
            <feComponentTransfer color-interpolation-filters="sRGB">
                <feFuncR type="table" tableValues=".996078431 .984313725"></feFuncR>
                <feFuncG type="table" tableValues=".125490196 .941176471"></feFuncG>
                <feFuncB type="table" tableValues=".552941176 .478431373"></feFuncB>
            </feComponentTransfer>
        </filter>
    </svg>
    <svg class="svgFilter">
        <filter id="noise-effect">
             <feTurbulence baseFrequency="0.60" result="colorNoise" />
             <feColorMatrix in="colorNoise" type="matrix" values=".33 .33 .33 0 0 .33 .33 .33 0 0 .33 .33 .33 0 0 0 0 0 1 0"/>
             <feComposite operator="in" in2="SourceGraphic" result="monoNoise"/>
             <feBlend in="SourceGraphic" in2="monoNoise" mode="multiply" />
        </filter>
     </svg>
`,wi=`
    <fieldset id="menu-fieldset" class="fieldset">
        <h1 class="header header--1 is-hidden" data-i18n="Tools"></h1>
        <h2 class="header header--2" part="header-2" data-i18n="File format"></h2>
        <div class="container container--buttons">
            <div class="container--info">
                <span class="icon icon-as-icon-button">${v.boxArrowInRight}</span>
                <span id="file-format-current" class="button__label"></span>&nbsp;
                <span id="image-orientation"></span>
                (<span id="image-aspect-ratio"></span>)
                <span id="original-image-file-size"></span>
                <div class="wrapper wrapper--field-composed wrapper--file-format-select">
                    <span class="icon icon-as-icon-button">${v.boxArrowRight}</span>
                    <select id="file-format-select" class="select select--file-type fields-composed__field"></select>
                    <span id="target-image-file-size" class="value-display fields-composed__uom"></span>
                    <span id="target-image-file-size-change" class="value-display fields-composed__uom"></span>
                </div>
            </div>
            <h2 class="header header--2" part="header-2" data-i18n="Resize"></h2>
            <div class="wrapper wrapper--field-composed wrapper--resize">
                <label for="aspect-ratio-select" class="label fields-composed__label" data-i18n-attr="title" data-i18n="Aspect ratio"></label>
                <input type="number" class="input input--number" id="resize-width" size="5"><label for="resize-width" class="" data-i18n="px"></label>
                <input type="number" class="input input--number" id="resize-height" size="5"><label for="resize-height" class="" data-i18n="px"></label>
                <button id="resize-aspect-ratio-lock" class="button button--icon button--selection-lock fields-composed__button" part="aspectRatioLockButton" data-click-action="toggleResizeAspectRatioLock" data-i18n="Maintain aspect ratio" data-i18n-attr="title">
                    <span class="icon icon--unlocked">${v.unlock}</span>
                    <span class="icon icon--locked">${v.lock}</span>
                </button>
            </div>
        </div>
        <h2 class="header header--2" part="header-2" data-i18n="Rotate and flip"></h2>
        <div class="container container-buttons">
            <button type="button" part="button" id="rotate-ccw" class="button button--icon-text" data-click-action="rotateCcw" data-i18n="Rotate ccw" data-i18n-attr="title">
                <span class="icon">${v.arrowCounterclockwise}</span>
                <span class="button__label" data-i18n="Rotate ccw"></span>
            </button>
            <button type="button" part="button" id="rotate-cw" class="button button--icon-text" data-click-action="rotateCw" data-i18n="Rotate cw" data-i18n-attr="title">
                <span class="icon">${v.arrowClockwise}</span>
                <span class="button__label" data-i18n="Rotate cw"></span>
            </button>
            <fieldset id="free-rotation" class="fieldset wrapper--field">
                <legend data-i18n="Free rotate" class="is-hidden"></legend>
                <input type="range" id="free-rotation-range" class="input input--range" part="range" min="0" max="360" value="0">
                <span class="wrapper wrapper--field-composed">
                    <input type="number" id="free-rotation-range-value" class="input input--number number__value" part="input" min="0" max="360" value="0">
                    <span class="" data-i18n-html="true" data-i18n="deg"></span>
                </span>
            </fieldset>
        </div>
        <div class="container container-buttons">
            <button type="button" part="button" id="flip" class="button button--icon-text" data-click-action="flip" data-i18n="Flip" data-i18n-attr="title">
                <span class="icon">${v.arrowLeftRight}</span>
                <span class="button__label" data-i18n="Flip"></span>
            </button>
            <button type="button" part="button" id="flop" class="button button--icon-text" data-click-action="flop" data-i18n="Flop" data-i18n-attr="title">
                <span class="icon">${v.arrowDownUp}</span>
                <span class="button__label" data-i18n="Flop"></span>
            </button>
        </div>
        <h2 class="header header--2" part="header-2" data-i18n="Select and crop"></h2>
        <div class="container container-buttons">
            <div class="container container--info">
                <span class="icon icon-as-icon-button">${v.boundingBox}</span>
                <span id="image-selection-size"></span>
                (<span id="image-selection-aspect-ratio"></span>)
                <div class="wrapper wrapper--field-composed wrapper--aspect-ratio-select">
                    <span class="icon icon-as-icon-button">${v.aspectRatio}</span>
                    <select id="aspect-ratio-select" part="select select--selection-aspect-ratios" class="select select--aspect-ratio fields-composed__field"></select>
                    <button id="selection-aspect-ratio-lock" class="button button--icon button--selection-lock fields-composed__button" part="aspectRatioLockButton" data-click-action="toggleSelectionAspectRatioLock" data-i18n="Maintain aspect ratio" data-i18n-attr="title">
                        <span class="icon icon--unlocked">${v.unlock}</span>
                        <span class="icon icon--locked">${v.lock}</span>
                    </button>
                </div>
            </div>
            <button type="button" part="button" id="clear-selection" data-click-action="clearSelection" class="button button--icon" data-i18n="Clear selection" data-i18n-attr="title">
                <span class="icon">${v.eraser}</span>
            </button>
            <button type="button" part="button" id="crop" data-click-action="crop" class="button button--icon-text" data-i18n="Crop" data-i18n-attr="title">
                <span class="icon">${v.crop}</span>
                <span class="button__label" data-i18n="Crop"></span>
            </button>
            <button type="button" part="button" id="edit-help" data-click-action="editHelp" class="button button--icon button--iconHelp" data-i18n="Help" data-i18n-attr="title">
                <span class="icon">${v.questionCircle}</span>
            </button>
        </div>
        <h2 class="header header--2" part="header-2" data-i18n="Other"></h2>
        <div class="container container-buttons">
            <button type="button" part="button" id="reset" class="button button--icon-text" data-click-action="reset" data-i18n="Reset" data-i18n-attr="title">
                <span class="icon">${v.arrowRepeat}</span>
                <span class="button__label" data-i18n="Reset"></span>
            </button>
            <button type="button" part="button" id="toggle-grid" class="button button--icon" data-click-action="toggleGrid" data-i18n="Toggle grid" data-i18n-attr="title">
                <span class="icon">${v.grid3x3}</span>
            </button>
            <button type="button" part="button" id="download" class="button button--icon" data-click-action="download" data-i18n="Download" data-i18n-attr="title">
                <span class="icon">${v.download}</span>
            </button>
        </div>
        ${vi}
        <h2 class="header header--2" part="header-2" data-i18n="Save"></h2>
        <div class="container container-buttons">
            <button type="button" part="button" id="save" class="button button--icon-text" data-click-action="save" data-i18n="OK" data-i18n-attr="title">
                <span class="icon">${v.check}</span>
                <span class="" data-i18n="Save"></span>
            </button>
            <button type="button" part="button" id="cancel" class="button button--icon-text" data-click-action="cancel" data-i18n="Cancel" data-i18n-attr="title">
                <span class="icon">${v.x}</span>
                <span class="" data-i18n="Cancel"></span>
            </button>
        </div>
    </fieldset>
`,xi=`
     <style>
        ${bi} ${mi}
    </style>
    <main class="main">
        <div id="editor-canvases" class="editor__canvases" tabindex="0">
            <div id="canvases-wrapper" class="wrapper wrapper--canvases"></div>
            <fieldset id="canvases-buttons" class="fieldset canvases__buttons">
                <span class="wrapper wrapper--field-composed">
                    <label for="zoom-percentage" class="is-hidden" data-i18n="Zoom level"></label>
                    <input type="number" id="zoom-percentage" class="input input--number" min="1" max="1000" step="1" value="100">
                    <label for="zoom-percentage" class="" data-i18n="per">%</label>
                    <button type="button" part="button" id="zoom-reset" class="button button--icon" data-click-action="zoomReset" data-i18n="Fit" data-i18n-attr="title"">
                        <span class="icon">${v.arrowRepeat}</span>
                    </button>
                </span>
                <div class="canvases__zoom-buttons">
                    <button type="button" part="button" id="zoom-in" class="button button--icon" data-click-action="zoomIn" data-i18n="Zoom in" data-i18n-attr="title">
                        <span class="icon">${v.plus}</span>
                    </button>
                    <button type="button" part="button" id="zoom-out" class="button button--icon" data-click-action="zoomOut" data-i18n="Zoom out" data-i18n-attr="title">
                        <span class="icon">${v.dash}</span>
                    </button>
                </div>
            </fieldset>
        </div>
        <div id="editor-menu" class="editor__menu">
            ${wi}
        </div>
    </main>
    <dialog id="dialog-help" class="dialog dialog--help">
         <div class="dialog__inner">
               <div class="dialog__header">
                    <button type="button" part="button" id="dialog-help-close" class="button button--icon button--icon-close" data-click-action="dialogHelpClose" data-i18n="Close" data-i18n-attr="title" autofocus>
                        <span class="icon">${v.xLg}</span>
                    </button>
               </div>
               <div class="dialog__body">
                    <h1 class="header header--1" part="header-1" >Help</h1>
                    <h2 class="header header--2" part="header-2" data-i18n="Crop"></h2>
                    <p class="paragraph" part="paragraph" data-i18n="Crop help"></p>
               </div>
         </div>
    </dialog>
        `;var z,D,Y,se,S,aa,ia,sa,oa,na,ra,xt,la;const Xt=class Xt{constructor(s){g(this,S);d(this,"errorMessages",{INVALID_CONSTRUCTOR_ARGUMENTS:"Invalid constructor arguments"});g(this,z);g(this,D);g(this,Y);g(this,se,new Map);d(this,"logger");if(this.logger=Ut((s==null?void 0:s.debug)??!0,"Translator"),!hi(s)){this.logger.error(this.errorMessages.INVALID_CONSTRUCTOR_ARGUMENTS);return}c(this,z,Object.assign({},Xt.defaultConfig,s)),this.determineAndSetActiveLanguage(),n(this,z).exposeFnName&&(window[n(this,z).exposeFnName]=this._.bind(this)),o(this,S,ia).call(this).catch(console.error)}static get defaultConfig(){return{rootElement:document,fallbackTranslations:null,fallbackLanguage:"en",detectLanguage:!0,persist:!1,persistKey:"active_language",languagesSupported:["en","nl"],languagesPath:null,exposeFnName:"__",debug:!1}}setupMutationObserver(){const s=n(this,z).rootElement,e={attributes:!0,childList:!0,subtree:!0},t=r=>{for(const l of r)o(this,S,la).call(this,l.target)};new MutationObserver(t).observe(s,e)}static async getTranslator(s={}){}determineAndSetActiveLanguage(){if(this.config.persist){const s=localStorage.getItem(n(this,z).persistKey);this.activeLanguage=s??this.config.fallbackLanguage}else if(this.config.detectLanguage){const s=o(this,S,aa).call(this);this.activeLanguage=s??this.config.fallbackLanguage}else this.activeLanguage=this.config.fallbackLanguage}get config(){return n(this,z)}get activeLanguage(){return n(this,D)}set activeLanguage(s){n(this,z).languagesSupported.includes(s)||(this.logger.log(`language "${s}" not found in supported languages ${n(this,z).languagesSupported} setting language to "${this.config.fallbackLanguage}"`),s=n(this,z).fallbackLanguage),c(this,D,s),n(this,z).persist&&localStorage.setItem(n(this,z).persistKey,n(this,D))}_(s,e=null){var i;let t=((i=n(this,Y))==null?void 0:i[s])||s;return e&&(this.logger.log("replacements",e),e.forEach(r=>{const l=`%{${Object.keys(r)[0]}}`,h=Object.values(r)[0];t=t.replace(l,h)})),t}};z=new WeakMap,D=new WeakMap,Y=new WeakMap,se=new WeakMap,S=new WeakSet,aa=function(){const s=navigator.languages&&navigator.languages[0]?navigator.languages[0]:navigator.language;return s?s.substring(0,2):!1},ia=async function(){const s=o(this,S,oa).call(this);await o(this,S,sa).call(this,s),o(this,S,ra).call(this),this.setupMutationObserver()},sa=async function(s){if(n(this,se).has(n(this,D))){c(this,Y,JSON.parse(n(this,se).get(n(this,D))));return}await o(this,S,na).call(this,s)},oa=function(){return(n(this,z).languagesPath||new URL("./lang/",import.meta.url).href)+"/"+n(this,D)+".json"},na=function(s){return fetch(s).then(e=>e.json()).then(e=>{c(this,Y,e),n(this,se).has(n(this,D))||n(this,se).set(n(this,D),JSON.stringify(n(this,Y)))}).catch(()=>{n(this,z).fallbackTranslations&&c(this,Y,n(this,z).fallbackTranslations)})},ra=function(){const s=n(this,z).rootElement.querySelectorAll("[data-i18n]");for(const e of s)o(this,S,xt).call(this,e)},xt=function(s){const e=[];s.dataset.i18nAndAttr?e.push("inNode",s.dataset.i18nAndAttr):s.dataset.i18nAttr?e.push(s.dataset.i18nAttr):e.push("inNode");const t=s.hasAttribute("data-i18n-html"),i=s.hasAttribute("data-i18n-replacements");let r=s.getAttribute("data-i18n-replacements");if(i)try{r=JSON.parse(s.dataset.i18nReplacements)}catch{throw new Error(`Error parsing ${r}`)}const l=s.dataset.i18n,h=this._(l,r);if(h)for(const p of e)p==="inNode"?t?s.innerHTML=h:s.innerText=h:s.setAttribute(p,h)},la=function(s){const e=s.querySelectorAll("[data-i18n]");for(const t of e)o(this,S,xt).call(this,t),this.logger.log(`%cSubsequent. Translated ${t.getAttribute("data-i18n")}`,"background-color:green;color:yellow;")};let wt=Xt;var We,rt;class yi extends HTMLElement{constructor(){var t;super();d(this,"elements",{});g(this,We,null);d(this,"configuration",{});g(this,rt,{debug:!1});if(this.setConfiguration(n(this,rt)),!new.target)throw new TypeError("invalid instantiation");const e=c(this,We,new.target);if(this.logger=Ut(this.debug,e.name),e.shadowTemplate){const i=e.shadowMode||{mode:"open",delegatesFocus:!0};this.attachShadow(i).innerHTML=e.shadowTemplate,(t=e.elementLookup)==null||t.forEach(r=>{const l=ta(r),h=this.shadowRoot.querySelector(l);h||console.warn(`element with "${l}" not found`),this.elements[r.slice(1)]=h}),e.translationsPath&&(this.translator=new wt({rootElement:this.shadowRoot,fallbackTranslations:{},persist:!0,debug:!1,languagesSupported:["nl","en"],fallbackLanguage:"nl",detectLanguage:!0,languagesPath:e.translationsPath}))}}attributeChangedCallback(e,t,i){}shadowTemplateAdded(){}dispatchEvent(e,t){this.shadowRoot.dispatchEvent(new CustomEvent(e,{bubbles:!0,composed:!0,...t&&{detail:t}}))}getAttributeAsBoolean(e){return this.hasAttribute(e)}getAttributeAsBooleanDefaultWhenFalse(e,t=null){let i=this.hasAttribute(e);return i===!1&&t!=null&&(i=t),i}setAttributeAsBoolean(e,t){if(typeof t!="boolean")throw new Error(`set ${e} must be set to a boolean value.`);this.toggleAttribute(e,t)}getAttributeAsInteger(e,t){return this.hasAttribute(e)?parseInt(this.getAttribute(e)):typeof t<"u"?t:null}setAttributeAsInteger(e,t){if(!gi(t))throw new Error(`set ${e} must be set to a number value.`);this.setAttribute(e,t)}getAttributeAsFloat(e,t){return this.hasAttribute(e)?parseFloat(this.getAttribute(e)):typeof t<"u"?t:null}setAttributeAsFloat(e,t){if(!pi(t))throw new Error(`set ${e} must be set to a number value.`);this.setAttribute(e,t)}getAttributeAsCSV(e,t){const i=this.hasAttribute(e)?this.getAttribute(e):null;return t=typeof t<"u"?t:null,i?i.replace(/\s/g,"").split(","):t}setAttributeAsCSV(e,t){if(!Array.isArray(t))throw new Error(`set ${e} must called with an array as value.`);const i=t.join(", ");this.setAttribute(e,i)}setAttributeToString(e,t){if(typeof t!="string")throw new Error(`set ${e} must be set to a string value.`);this.setAttribute(e,t)}getAttributeOrDefault(e,t){return this.hasAttribute(e)?this.getAttribute(e):typeof t<"u"?t:null}setConfiguration(e){this.configuration=Object.assign(this.configuration,e)}getConfiguration(){return this.configuration}get config(){return this.configuration}get subClassName(){return n(this,We).name}get debug(){return this.getAttributeAsBooleanDefaultWhenFalse("debug",this.configuration.debug)}set debug(e){this.setAttributeAsBoolean("debug",e)}}We=new WeakMap,rt=new WeakMap;var Te,I,ke,oe,lt,J,da,qe;class ca extends yi{constructor(){super();g(this,J);d(this,"formElement");g(this,Te,["ElementInternals","FormDataEvent","Callback"]);g(this,I);d(this,"internals");g(this,ke,null);g(this,oe,[]);g(this,lt,{required:!1});d(this,"formSubmitHandler",e=>{if(n(this,I)==="FormDataEvent"&&!n(this,ke)){e.preventDefault(),this.logger.log("formSubmitHandler message: ",n(this,oe)[0]);const{hiddenFileUpload:t}=this.elements;t.focus(),t.setCustomValidity(this.translator._(n(this,oe)[0])),t.reportValidity()}this.logger.log("formSubmitHandler, FormDataEvent form submit detected")});this.setConfiguration(n(this,lt)),this.formElement=this.findContainingForm(),this.forceSubmitMode?this.setFormSubmitMode(this.forceSubmitMode):this.determineFormSubmitMode(),n(this,I)==="ElementInternals"&&(this.internals=this.attachInternals(),this.formElement=this.internals.form)}attributeChangedCallback(e,t,i){if(super.attributeChangedCallback(e,t,i),e=e.toLowerCase(),t!==i)switch(e){case"required":const r=i;this.internals?this.internals.ariaRequired=r:this.setAttribute("aria-required",r);break;case"disabled":const l=i;this.internals?this.internals.ariaDisabled=l:this.setAttribute("aria-disabled",l),this.enableControls(!l);break}}determineFormSubmitMode(){const e="ElementInternals"in window&&"setFormValue"in window.ElementInternals.prototype,t="FormDataEvent"in window;o(this,J,qe).call(this)&&e?this.setFormSubmitMode("ElementInternals"):o(this,J,qe).call(this)&&t?this.setFormSubmitMode("FormDataEvent"):this.setFormSubmitMode("Callback")}handleSubmitUsingFormDataEvent(){throw new Error("Must override handleSubmitUsingFormDataEvent for DormDataEvent submit method to work.")}updateValue(){this.updateValidity(),n(this,I)==="ElementInternals"&&(this.internals.setFormValue(this.value),this.logger.log("update ElementInternals value"))}updateValidity(){throw new Error("Must override updateValidity")}connectedCallback(){n(this,I)==="FormDataEvent"&&this.formElement&&(this.formElement.addEventListener("formdata",this.handleSubmitUsingFormDataEvent),this.formElement.addEventListener("submit",this.formSubmitHandler,!1))}disconnectedCallback(){this.formElement&&(n(this,I)==="FormDataEvent"&&(this.formElement.removeEventListener("formdata",this.handleSubmitUsingFormDataEvent),this.formElement.removeEventListener("submit",this.formSubmitHandler)),this.formElement=null)}findContainingForm(){const e=this.getRootNode();return Array.from(e.querySelectorAll("form")).find(i=>i.contains(this))||null}formAssociatedCallback(e){this.formAssociated(e)}formDisabledCallback(e){o(this,J,da).call(this,e)}formStateRestoreCallback(e,t){t==="restore"?this.formStateRestore(e):this.logger.log("formStateRestoreCallback ignored. mode:",t)}formResetCallback(){this.formReset()}formAssociated(e){}formStateRestore(e){this.value=e}formReset(){}enableControls(e){throw new Error("enableControls must be overridden")}setFormSubmitMode(e){if(!n(this,Te).includes(e))throw new Error(`Not a valid submit mode ${e}. Use ${n(this,Te).join(", ")}`);!o(this,J,qe).call(this)&&e!=="Callback"?(c(this,I,"Callback"),console.warn("Could not find containing form. Falling back to submit using callbacks.")):(c(this,I,e),this.logger.log(`Submit mode for ${this.subClassName}`,n(this,I)))}set validity(e){c(this,ke,e)}get validity(){}set validityMessages(e){c(this,oe,e),n(this,I)==="ElementInternals"&&(n(this,ke)?this.internals.setValidity({}):(this.internals.setValidity({customError:!0},this.translator._(n(this,oe)[0]),this.formValidationAnchor),this.internals.reportValidity()))}get validityMessages(){}get forceSubmitMode(){return this.getAttributeOrDefault("force-submit-mode",null)}set forceSubmitMode(e){this.setAttributeToString("force-submit-mode",e),this.setFormSubmitMode(e)}get submitMode(){return n(this,I)}get type(){return this.getAttributeOrDefault("type","input")}set type(e){this.setAttributeToString("type",e)}get value(){return this.getAttributeOrDefault("value","")}set value(e){this.setAttributeToString("value",String(e))}get name(){return this.getAttributeOrDefault("name","name")}set name(e){this.setAttributeToString("name",e)}get required(){return this.getAttributeAsBooleanDefaultWhenFalse("required",this.config.required)}set required(e){this.setAttributeAsBoolean("required",e)}get formValidationAnchor(){return this}set disabled(e){this.setAttributeAsBoolean("disabled",e)}get disabled(){}setConfiguration(e){super.setConfiguration(Object.assign(this.configuration,e)),this.config.disabled&&(this.disabled=!0)}}Te=new WeakMap,I=new WeakMap,ke=new WeakMap,oe=new WeakMap,lt=new WeakMap,J=new WeakSet,da=function(e){this.enableControls(!e)},qe=function(){return this.formElement!==null},d(ca,"formAssociated",!0);var ne,re,le,ce,de,he,ze;const Yt=class Yt extends Fe{constructor(e=null,t=null,i=null,r=null,l=null,h=!1,p=null,m=null,b=null,A=null,f=null){super(e,t,i,r,f);g(this,ne);g(this,re);g(this,le);g(this,ce);g(this,de);g(this,he);g(this,ze);c(this,ne,l),c(this,re,h),c(this,le,p),c(this,ce,m),c(this,de,b),c(this,he,A),c(this,ze,!1)}get name(){return n(this,ne)}set name(e){c(this,ne,e)}get over(){return n(this,re)}set over(e){c(this,re,e)}get type(){return n(this,le)}set type(e){c(this,le,e)}get cursor(){return n(this,ce)}set cursor(e){c(this,ce,e)}get mode(){return n(this,de)}set mode(e){c(this,de,e)}get action(){return n(this,he)}set action(e){c(this,he,e)}get active(){return n(this,ze)}set active(e){c(this,ze,e)}get cloned(){return new Yt(this.x,this.y,this.w,this.h,n(this,ne),n(this,re),n(this,le),n(this,ce),n(this,de),n(this,he))}};ne=new WeakMap,re=new WeakMap,le=new WeakMap,ce=new WeakMap,de=new WeakMap,he=new WeakMap,ze=new WeakMap;let M=Yt;class Si extends ca{constructor(){super();d(this,"zoomRatio",1);d(this,"zoomPercentage",null);d(this,"editorCanvasesWidth",null);d(this,"editorCanvasesHeight",null);d(this,"canvasesCSSWidth",null);d(this,"canvasesCSSHeight",null);d(this,"canvasesCSSScaleRatio",null);d(this,"canvasImageFilter","none");d(this,"canvasImageWidth",null);d(this,"canvasImageHeight",null);d(this,"canvasImageXOrigin",null);d(this,"canvasImageYOrigin",null);d(this,"canvasImageDrawStart",new w(0,0));d(this,"canvasDrawRatio",null);d(this,"canvasDrawWidth",null);d(this,"canvasDrawHeight",null);d(this,"imageNaturalWidth",null);d(this,"imageNaturalHeight",null);d(this,"imageAspectRatio",null);d(this,"imageOrientation",null);d(this,"showGrid",!1);d(this,"gridGap",null);d(this,"gridLines",[]);d(this,"crossLineSize",null);d(this,"selectionMode",null);d(this,"selectionAction","");d(this,"selectionStartPointerOver",null);d(this,"selectionLineDashSize",null);d(this,"selectionValid",!0);d(this,"selectionHandleAreas",{grab:new M(0,0,0,0,"grab",!1,"selection","grabbing","grab","grab"),nw:new M(0,0,0,0,"nw",!1,"corner","nwse-resize","resize","nw-resize"),n:new M(0,0,0,0,"n",!1,"edge","ns-resize","resize","n-resize"),ne:new M(0,0,0,0,"ne",!1,"corner","nesw-resize","resize","ne-resize"),e:new M(0,0,0,0,"e",!1,"edge","ew-resize","resize","e-resize"),se:new M(0,0,0,0,"se",!1,"corner","nwse-resize","resize","se-resize"),s:new M(0,0,0,0,"s",!1,"edge","ns-resize","resize","s-resize"),sw:new M(0,0,0,0,"sw",!1,"corner","nesw-resize","resize","sw-resize"),w:new M(0,0,0,0,"w",!1,"edge","ew-resize","resize","w-resize")});d(this,"selectionPointerStart",new w(0,0));d(this,"selectionPointerCurrent",new w(0,0));d(this,"selectionArea",new Fe(0,0,0,0));d(this,"selectionAreaScaled",new Fe(0,0,0,0));d(this,"selectionWasTouchEvent",!1);d(this,"handleCornerSize",null);d(this,"handleEdgeSize",null);d(this,"handleEdgeMargin",null);d(this,"selectionAspectRatioLocked",!1);d(this,"resizeAspectRatioLocked",!0);d(this,"rotationAngle",0);d(this,"flipped",!1);d(this,"flopped",!1);d(this,"flipXAxisDirection",null);d(this,"flipYAxisDirection",null);d(this,"flipXOrigin",null);d(this,"flipYOrigin",null);d(this,"animationOffset",0);d(this,"canvasElements",{canvasImage:null,canvasDraw:null,ctxImage:null,ctxDraw:null});d(this,"availableFileFormats",[{name:"JPEG",label:"JPEG",value:"image/jpeg"},{name:"WebP",label:"WebP",value:"image/webp"},{name:"PNG",label:"PNG",value:"image/png"},{name:"GIF",label:"GIF",value:"image/gif"},{name:"BMP",label:"BMP",value:"image/bmp"}]);d(this,"availableAspectRatios",[{name:"free",label:"Free selection",value:-1,active:!0},{name:"16:10",label:"16:10",value:16/10,active:!0},{name:"16:9",label:"16:9",value:16/9,active:!0},{name:"5:3",label:"5:3",value:5/3,active:!0},{name:"4:3",label:"4:3",value:4/3,active:!0},{name:"3:2",label:"3:2",value:3/2,active:!0},{name:"2:1",label:"2:1",value:2,active:!0},{name:"1:1",label:"1:1",value:1,active:!0},{name:"locked",label:"Locked",value:null,active:!0}]);d(this,"defaultActiveAspectRatios",["free","16:10","16:9","5:3","4:3","3:2","2:1","1:1","locked"]);d(this,"classConfiguration",{lineWidth:1,selectionLineDashSize:14,crossLineSize:30,handleCornerSize:60,handleEdgeSize:40,handleEdgeMargin:0,touchHandleMultiplier:2,touchHandleMultiplierBreakpoint:"992px",aspectRatioTolerance:.01,snapThresholdPercentage:.01,zoomPercentageMin:1,zoomPercentageMax:1e3,zoomPercentageStep:25,gridLineCount:10,showSubGrid:!0,drawCanvasWidth:1500,animateSelection:!0,animateFPS:60,selectionHandleStrokeStyle:"rgba(230,230,230,0.9)",selectionHandleLineDashStrokeStyle:"rgba(0,0,0,0.9)",selectionHandleOverFillStyle:"rgba(230,230,230, 0.5)",gridStrokeStyle:"#ccc",selectionBoxStrokeStyle:"rgba(33,33,33,0.9)",selectionBoxLineDashStrokeStyle:"rgba(222,222,222,0.9)",selectionBoxInvalidLineDashStrokeStyle:"red",subGridStrokeStyle:"#ccc7",crossStrokeStyle:"#ccc",debug:!1,selectionAspectRatios:["free","16:10","16:9","5:3","4:3","3:2","2:1","1:1","locked"],selectionAspectRatio:"free",fileFormats:["image/png","image/jpeg","image/webp"],rotateDegreesStep:30,minWidth:100,minHeight:100,maxWidth:3500,maxHeight:3500,freeSelectDisabled:!1,freeRotateDisabled:!1,freeResizeDisabled:!1,filtersDisabled:!1});super.setConfiguration(this.classConfiguration)}setConfiguration(e){super.setConfiguration(e)}}var $e,Be,Ve;class Kt{constructor(s,e,t){g(this,$e);g(this,Be);g(this,Ve);this.validate(s,e,t),c(this,Be,s),c(this,Ve,e),c(this,$e,t)}validate(s,e,t){if(!s||!e||!t)throw new Error("ImageSource(name, src, id), invalid parameters.")}get id(){return n(this,$e)}get name(){return n(this,Be)}get src(){return n(this,Ve)}}$e=new WeakMap,Be=new WeakMap,Ve=new WeakMap;class Ai{constructor(s,e){d(this,"fps",null);d(this,"delay",null);d(this,"time",null);d(this,"frameCount",-1);d(this,"rafReference",null);d(this,"isPlaying",!1);d(this,"animationCallback",()=>{});if(!s||!e)throw new Error("Must provide FPS and animationCallback");this.fps=s,this.delay=1e3/this.fps,this.animationCallback=e}loop(s){this.time===null&&(this.time=s);const e=Math.floor((s-this.time)/this.delay);e>this.frameCount&&(this.frameCount=e,this.animationCallback({time:s,frameCount:this.frameCount})),this.rafReference=requestAnimationFrame(this.loop.bind(this))}start(){this.isPlaying||(this.isPlaying=!0,this.rafReference=requestAnimationFrame(this.loop.bind(this)))}pause(){this.isPlaying&&(this.isPlaying=!1,this.time=null,this.frameCount=-1,cancelAnimationFrame(this.rafReference))}}var Ne,Z,Q,yt,St;class ki{constructor(s){g(this,Q);g(this,Ne);d(this,"shadowRoot");d(this,"elements");g(this,Z,[]);d(this,"canvasImageFilter",null);if(!s)throw new Error("Must be used with an ImageEditor instance");c(this,Ne,s),this.shadowRoot=s.shadowRoot,this.elements=s.elements,this.logger=s.logger}singleModifierEffect(s,e,t){n(this,Z).push(s.replace("value",`${e[0].value}${t}`))}svgEffect(s,e,t,i){const r=s.dataset.svgFilterEffect;if(!r)return;const l=this.shadowRoot.querySelector(`#${r}`);switch(r){case"gamma-effect":this.gammaEffect(l,e);break;case"duotone-effect":this.duotoneEffect(l,i);break;default:this.logger.log(`${r} no special handling, svgFilter`,l)}n(this,Z).push(`url(#${r})`)}gammaEffect(s,e){if(e.getAttribute("data-modifier")==="gamma-exponent"){const t=s.querySelectorAll("[exponent]");for(const i of t)i.setAttribute("exponent",e.value)}if(e.getAttribute("data-modifier")==="gamma-amplitude"){const t=s.querySelectorAll("[amplitude]");for(const i of t)i.setAttribute("amplitude",e.value)}}duotoneEffect(s,e){const t=C=>C.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i,(G,U,q,fe)=>"#"+U+U+q+q+fe+fe).substring(1).match(/.{2}/g).map(G=>parseInt(G,16)),i=s.querySelector("feFuncR"),r=s.querySelector("feFuncG"),l=s.querySelector("feFuncB"),h=o(this,Q,St).call(this,e),{color1:p,color2:m}=h,b=t(p),A=t(m),f=`${b[0]/255} ${A[0]/255}`,x=`${b[1]/255} ${A[1]/255}`,k=`${b[2]/255} ${A[2]/255}`;i.setAttribute("tableValues",f),r.setAttribute("tableValues",x),l.setAttribute("tableValues",k)}dropShadowEffect(s,e,t,i){const r=o(this,Q,St).call(this,i),l=r["drop-shadow-x-offset"],h=r["drop-shadow-y-offset"],p=r["drop-shadow-blur-radius"],m=r["drop-shadow-color"],b=t.replace("length-1",`${l}px`).replace("length-2",`${h}px`).replace("length-3",`${p}px`).replace("color",m);n(this,Z).push(b)}addFilterEventListeners(){const{filterContainer:s}=this.elements;s.addEventListener("click",e=>{const t=e.target;t.tagName==="INPUT"&&t.type==="checkbox"&&o(this,Q,yt).call(this,e)}),s.addEventListener("input",e=>{const t=e.target;t.tagName==="INPUT"&&t.type!=="checkbox"&&o(this,Q,yt).call(this,e)}),s.addEventListener("contextmenu",e=>{e.preventDefault()})}reset(){const s=this.shadowRoot.querySelectorAll("[data-default]"),e=this.shadowRoot.querySelectorAll('#filters [type="checkbox"]'),{filterContainer:t}=this.elements;for(const i of s)i.value=i.dataset.default;for(const i of e)i.checked=!1;this.canvasImageFilter="none",t.classList.remove("show")}}Ne=new WeakMap,Z=new WeakMap,Q=new WeakSet,yt=function(s){c(this,Z,[]);const e=s.target,{filterContainer:t}=this.elements;t.querySelectorAll("span.container--filter").forEach(r=>{const l=r.querySelector('[type="checkbox"]'),{filterType:h,filterString:p}=r.dataset,m=r.querySelectorAll("[data-modifier]");for(const b of m)b!==e&&b.getAttribute("data-modifier")===e.getAttribute("data-modifier")&&(b.value=e.value);if(l.checked)switch(h){case"drop-shadow":this.dropShadowEffect(r,e,p,m);break;case"percentage":this.singleModifierEffect(p,m,"%");break;case"angle":this.singleModifierEffect(p,m,"deg");break;case"length":this.singleModifierEffect(p,m,"px");break;case"svg":this.svgEffect(r,e,p,m);break;default:this.logger.log(`could not find ${h}`);break}}),this.canvasImageFilter=n(this,Z).join(" "),this.logger.log("applying filters:",this.canvasImageFilter),n(this,Ne).updateFilter(this.canvasImageFilter)},St=function(s){const e={};for(const t of s)e[t.getAttribute("data-modifier")]=t.value;return e};var O,Re,ue,ge,pe,K,L,W,Ce,a,ha,ua,At,ga,Ye,Ze,pa,fa,ma,ba,kt,Ee,ee,va,F,wa,xa,ya,Sa,$,zt,Rt,Ke,Ct,Je,Ft,Aa,ka,za,Ra,It,Ca,Lt,Et,_t,Qe,Fa,et,tt,Ia,La,Ea,_a,Ma,Ha,Da,Pa,Oa,Mt,Ht,Dt,Wa,Ta,je,Pt,Ge,ct,dt,$a,Ot,at,Wt,Ba,Va,Na,Tt,ja,Ga,$t,_e,Ua,qa,Xa,Ya,Za,Bt,Vt,Nt,Ka,it,Me,Ja,Qa,ei,He,ti,ai,ii,jt,Gt,st,si;class Xe extends Si{constructor(){console.log("constructor invoked");super();g(this,a);g(this,O,null);g(this,Re,0);d(this,"canvasFilters",null);g(this,ue,null);g(this,ge);g(this,pe);g(this,K);g(this,L);g(this,W,null);g(this,Ce,null);g(this,je,{cancel:()=>{console.log("cancel clicked"),P.fire("onCloseImageEditor"),this.dispatchEvent("onCloseImageEditor",{})},clearSelection:()=>{this.clearSelection()},crop:()=>{this.crop().catch(console.error)},dialogHelpClose:()=>{const{dialogHelp:e}=this.elements;e.close()},download:()=>{this.download()},editHelp:()=>{const{dialogHelp:e}=this.elements;e.showModal()},flip:()=>{this.flip()},flop:()=>{this.flop()},reset:()=>{this.reset()},resizeAspectRatioLock:()=>{o(this,a,Ka).call(this)},rotateCcw:()=>{this.rotate("ccw")},rotateCw:()=>{this.rotate("cw")},save:()=>{console.log("save clicked"),this.save()},selectionAspectRatioLock:()=>{o(this,a,Nt).call(this)},toggleGrid:()=>{this.toggleGrid()},zoomIn:()=>{this.zoomIn()},zoomOut:()=>{this.zoomOut()},zoomReset:()=>{this.zoomReset()}});g(this,Ge,fi((e,t)=>{o(this,a,xa).call(this,e,t).catch(console.error)},this));g(this,ct,{"+":()=>this.zoomIn(),"-":()=>this.zoomOut(),"=":()=>this.zoomIn(),L:()=>this.rotate("ccw"),R:()=>this.rotate("cw")});g(this,dt,e=>{const t=n(this,ct)[e.key];t&&(e.preventDefault(),t(e))});o(this,a,ha).call(this),this.enableControls(!1),this.enableFilters(this.shouldEnableFilters()),this.canvasFilters=new ki(this),this.determineFileFormatSupport(),o(this,a,$a).call(this),this.selectionAnimationController=new Ai(this.config.animateFPS,this.animateSelection.bind(this)),this.dispatchEvent("imageEditorReady",{instance:this})}static get observedAttributes(){return["disabled"]}getImageFileConfiguration(){const e={formatsRegex:/.png|.jpg|.jpeg|.webp/,aspectRatioTolerance:.01,minWidth:100,maxWidth:3500,minHeight:100,maxHeight:3500,maxUploadFileSize:3e5,maxEditFileSize:3e5};return console.log(e),e}connectedCallback(){super.connectedCallback(),this.logger.log("ImageEditor connectedCallback"),console.log("ImageEditor connectedCallback")}disconnectedCallback(){super.disconnectedCallback(),this.logger.log("imageEditor disconnectedCallback"),console.log("ImageEditor disconnectedCallback")}enableControls(e){const{menuFieldset:t,canvasesButtons:i}=this.elements;e?(t.removeAttribute("disabled"),i.removeAttribute("disabled")):(t.setAttribute("disabled","disabled"),i.setAttribute("disabled","disabled"))}updateFilter(e){this.canvasImageFilter=e,o(this,a,F).call(this)}animateSelection(){this.animationOffset++,this.animationOffset>this.selectionLineDashSize*2&&(this.animationOffset=0),o(this,a,Je).call(this)}download(){if(o(this,a,at).call(this)){const e=this.canvasElements.canvasImage.toDataURL(this.getFileFormatSelectValue());di(e,n(this,L).name)}}rotate(e){this.clearSelection(!1),e==="ccw"?(this.rotationAngle-=this.rotateDegreesStep,this.rotationAngle<0&&(this.rotationAngle=360-Math.abs(this.rotationAngle))):e==="cw"&&(this.rotationAngle+=this.rotateDegreesStep,this.rotationAngle>360&&(this.rotationAngle=this.rotationAngle-360)),o(this,a,jt).call(this),o(this,a,Gt).call(this),o(this,a,F).call(this)}flip(){this.flipped=!this.flipped,this.clearSelection(!1),o(this,a,F).call(this)}flop(){this.flopped=!this.flopped,this.clearSelection(!1),o(this,a,F).call(this)}zoom(e){const{zoomPercentageMin:t,zoomPercentageMax:i,zoomPercentageStep:r}=this.config;let l=o(this,a,Va).call(this);e>0?l<=i-r&&(l+=r):l>=t+r&&(l-=r),this.zoomRatio=o(this,a,Wt).call(this,l),o(this,a,F).call(this)}zoomIn(){this.zoom(1)}zoomOut(){this.zoom(-1)}zoomReset(){this.zoomRatio=1,o(this,a,F).call(this)}reset(){this.resetProperties(),o(this,a,_e).call(this,n(this,ge)).catch(console.error)}toggleGrid(){this.showGrid=!this.showGrid,o(this,a,$).call(this)}async crop(){if(!o(this,a,st).call(this)){o(this,a,ee).call(this,this.translator._("Make selection before cropping"));return}if(!this.selectionValid){o(this,a,ee).call(this,this.translator._("Invalid crop siz"));return}const e=this.getSelectionAsDataUrl();try{const t=await pt(e,n(this,L).name),i=new Le(this.getImageFileConfiguration());await i.load(n(this,K),t,null,n(this,L).name),o(this,a,_e).call(this,i,()=>{o(this,a,Me).call(this)}).catch(console.error)}catch{this.logger.log("Something went wrong during processing of cropped ImageFile")}this.resetProperties()}getSelectionAsDataUrl(){const e=document.createElement("canvas"),t=e.getContext("2d");t.imageSmoothingEnabled=!1;const{x:i,y:r,w:l,h}=this.selectionArea;return e.width=l,e.height=h,t.drawImage(this.canvasElements.canvasImage,i,r,l,h,0,0,l,h),e.toDataURL("image/png",1)}clearSelection(e=!0){this.selectionArea.set(0,0,0,0),this.selectionAreaScaled.set(0,0,0,0);const t=this.selectionHandleAreas;Object.keys(t).forEach(i=>{t[i].set(0,0,0,0)}),o(this,a,Tt).call(this),o(this,a,Aa).call(this),e&&o(this,a,$).call(this)}save(){console.log("in save method"),this.canvasElements.canvasImage.toBlob(e=>{const{fileFormatSelect:t}=this.elements;if(o(this,a,at).call(this)){const i=new File([e],n(this,L).name,{type:t.value});P.fire("onImageSave",{id:n(this,K),file:i}),this.dispatchEvent("onImageSave",{id:n(this,K),file:i})}else console.log("requirements not met")},this.getFileFormatSelectValue())}determineFreeRotationAvailability(){const{freeRotation:e}=this.elements;this.freeRotateDisabled?e.style.display="none":e.style.removeProperty("display")}determineFiltersAvailability(){const{filters:e}=this.elements;this.filtersDisabled?e.style.display="none":e.style.removeProperty("display")}setImageAsImageFile(e,t){if(!e||!(t instanceof Le))throw new Error("setImageAsImageFile(imageFile). Not all arguments passed / valid.");c(this,ge,t),c(this,K,e);const i=r=>{c(this,ue,r),this.enableControls(!0),this.resetProperties(),o(this,a,ua).call(this),o(this,a,Ua).call(this),o(this,a,Ya).call(this),o(this,a,qa).call(this),o(this,a,Xa).call(this,t.mimeType),o(this,a,Ja).call(this),o(this,a,Me).call(this),o(this,a,Za).call(this),o(this,a,Bt).call(this),o(this,a,Sa).call(this),this.determineFreeRotationAvailability(),o(this,a,Vt).call(this),this.determineFiltersAvailability()};o(this,a,_e).call(this,t,i).catch(console.error)}async setImageAsImageSource(e){if(!(e instanceof Kt))throw new Error("setImageAsImageSource(imageSource), Not all arguments passed / valid.");const t=new Le(this.getImageFileConfiguration());try{await t.load(e.id,null,e.src,e.name),this.setImageAsImageFile(e.id,t)}catch{console.warn("setImageAsImageSource: Could not create ImageFile")}}setImage(e,t,i){const r=new Kt(e,t,i);this.setImageAsImageSource(r).catch(console.error)}resetProperties(){this.flipped=!1,this.flopped=!1,this.rotationAngle=0,this.zoomRatio=1,this.showGrid=!1,o(this,a,Qa).call(this),o(this,a,ei).call(this),o(this,a,He).call(this),o(this,a,ai).call(this),o(this,a,ii).call(this),this.clearSelection(!1),o(this,a,F).call(this)}getFileFormatSelectValue(){const{fileFormatSelect:e}=this.elements;return e.value}get freeSelectDisabled(){return this.getAttributeAsBooleanDefaultWhenFalse("free-select-disabled",this.config.freeSelectDisabled)}set freeSelectDisabled(e){this.setAttributeAsBoolean("free-select-disabled",e)}get freeResizeDisabled(){return this.getAttributeAsBooleanDefaultWhenFalse("free-resize-disabled",this.config.freeResizeDisabled)}set freeResizeDisabled(e){this.setAttributeAsBoolean("free-resize-disabled",e)}get freeRotateDisabled(){return this.getAttributeAsBooleanDefaultWhenFalse("free-rotate-disabled",this.config.freeRotateDisabled)}set freeRotateDisabled(e){this.setAttributeAsBoolean("free-rotate-disabled",e)}get filtersDisabled(){return this.getAttributeAsBooleanDefaultWhenFalse("filters-disabled",this.config.filtersDisabled)}set filtersDisabled(e){this.setAttributeAsBoolean("filters-disabled",e)}get minWidth(){return this.getAttributeAsInteger("min-width",this.config.minWidth)}set minWidth(e){this.setAttributeAsInteger("min-width",e)}get minHeight(){return this.getAttributeAsInteger("min-height",this.config.minHeight)}set minHeight(e){this.setAttributeAsInteger("min-height",e)}get maxWidth(){return this.getAttributeAsInteger("max-width",this.config.maxWidth)}set maxWidth(e){this.setAttributeAsInteger("max-width",e)}get maxHeight(){return this.getAttributeAsInteger("max-height",this.config.maxHeight)}set maxHeight(e){this.setAttributeAsInteger("max-height",e)}get selectionAspectRatios(){const e=this.config.selectionAspectRatios??this.defaultActiveAspectRatios;return this.getAttributeAsCSV("selection-aspect-ratios",e)}set selectionAspectRatios(e){this.setAttributeAsCSV("selection-aspect-ratios",e)}get selectionAspectRatio(){const e=this.getAttributeOrDefault("selection-aspect-ratio",this.config.selectionAspectRatio);return this.selectionAspectRatios.includes(e)||console.warn(`ImageEditor: ${e} not in list with selectionAspectRatios ${this.selectionAspectRatios.join(", ")}`),e}set selectionAspectRatio(e){this.setAttributeToString("selection-aspect-ratio",e),o(this,a,He).call(this)}get fileFormats(){return this.getAttributeAsCSV("file-formats",this.config.fileFormats)}set fileFormats(e){return this.setAttributeAsCSV("file-formats",e)}get rotateDegreesStep(){return this.getAttributeAsInteger("rotate-degrees-step",this.config.rotateDegreesStep)}set rotateDegreesStep(e){this.setAttributeAsInteger("rotate-degrees-step",e)}enableFilters(e){this.elements.filters.style.display=e?"block":"none"}shouldEnableFilters(){return o(this,a,si).call(this)&&!this.filtersDisabled}determineFileFormatSupport(){for(const e of this.availableFileFormats)e.supported=o(this,a,ga).call(this,e.value)}}O=new WeakMap,Re=new WeakMap,ue=new WeakMap,ge=new WeakMap,pe=new WeakMap,K=new WeakMap,L=new WeakMap,W=new WeakMap,Ce=new WeakMap,a=new WeakSet,ha=function(){const{canvasesWrapper:e}=this.elements;gt(e),["image","draw"].forEach(t=>{const{canvasesWrapper:i}=this.elements,r=ui(t),l=`canvas${r}`,h=`ctx${r}`,p=document.createElement("canvas");p.id=`canvas${r}`,p.className=`canvas--${t}`,t==="image"&&(p.innerText=this.translator._("Image editor canvas")),i.appendChild(p),this.canvasElements[l]=p,this.canvasElements[h]=p.getContext("2d")})},ua=function(){const e=new ResizeObserver(()=>{o(this,a,F).call(this)}),{editorCanvases:t}=this.elements;e.observe(t)},At=function(e){const t=this.canvasElements.canvasImage.toDataURL(e),i=`data:${e};base64,`;return Math.round((t.length-i.length)*3/4)},ga=function(e){const t=document.createElement("canvas");t.width=t.height=1;const i=t.toDataURL(e);return i==null?void 0:i.includes(`data:${e};base64,`)},Ye=function(e,t=2){if(!+e)return"0 Bytes";const i=1024,r=t<0?0:t,l=["Bytes","KB","MB","GB"],h=Math.floor(Math.log(e)/Math.log(i));return`${parseFloat((e/Math.pow(i,h)).toFixed(r))} ${l[h]}`},Ze=function(e,t){if(isNaN(e)||isNaN(t)||t===0)return"-";const i=this.config.aspectRatioTolerance,r=e/t;let h=`${r.toFixed(2)}:1`;for(const p of Object.values(this.availableAspectRatios))r>p.value-i&&r<p.value+i&&(h+=` (${p.label})`);return h},pa=function(e,t,i){const r={},l=i*Math.PI/180,h=Math.abs(Math.cos(l)),p=Math.abs(Math.sin(l));return r.width=Math.round(t*p+e*h),r.height=Math.round(t*h+e*p),r},fa=function(){if(this.selectionMode==="select")return;const{editorCanvases:e}=this.elements;this.editorCanvasesWidth=e.offsetWidth,this.editorCanvasesHeight=e.offsetHeight,this.imageNaturalWidth=n(this,pe).naturalWidth,this.imageNaturalHeight=n(this,pe).naturalHeight;const t=o(this,a,pa).call(this,this.imageNaturalWidth,this.imageNaturalHeight,this.rotationAngle);this.canvasImageWidth=t.width,this.canvasImageHeight=t.height,this.canvasDrawRatio=this.config.drawCanvasWidth/this.canvasImageWidth;const i=this.config.drawCanvasWidth/this.canvasImageWidth;this.canvasDrawWidth=Math.round(this.canvasImageWidth*i),this.canvasDrawHeight=Math.round(this.canvasImageHeight*i),this.canvasesCSSScaleRatio=o(this,a,ba).call(this);const r=o(this,a,ma).call(this);this.canvasesCSSWidth=r.width,this.canvasesCSSHeight=r.height,this.canvasImageXOrigin=this.canvasImageWidth/2,this.canvasImageYOrigin=this.canvasImageHeight/2,this.canvasImageDrawStart.x=this.canvasImageWidth/2-this.imageNaturalWidth/2,this.canvasImageDrawStart.y=this.canvasImageHeight/2-this.imageNaturalHeight/2,this.flipXAxisDirection=this.flipped?-1:1,this.flipYAxisDirection=this.flopped?-1:1,this.flipXOrigin=this.flipped?this.canvasImageWidth:0,this.flipYOrigin=this.flopped?this.canvasImageHeight:0,this.imageAspectRatio=o(this,a,Ze).call(this,this.imageNaturalWidth,this.imageNaturalHeight),this.imageOrientation=this.canvasImageWidth>this.canvasImageHeight?this.translator._("Landscape"):this.canvasImageHeight>this.canvasImageWidth?this.translator._("Portrait"):this.translator._("Square"),this.gridGap=Math.round(this.imageNaturalWidth/this.config.gridLineCount*this.canvasDrawRatio),this.lineWidth=o(this,a,kt).call(this,this.config.lineWidth),this.selectionLineDashSize=o(this,a,kt).call(this,this.config.selectionLineDashSize),this.crossLineSize=o(this,a,Ee).call(this,this.config.crossLineSize),this.handleCornerSize=o(this,a,Ee).call(this,this.config.handleCornerSize),this.handleEdgeSize=o(this,a,Ee).call(this,this.config.handleEdgeSize),this.handleEdgeMargin=o(this,a,Ee).call(this,this.config.handleEdgeMargin),window.matchMedia(`(max-width: ${this.config.touchHandleMultiplierBreakpoint})`).matches&&(this.logger.log("small viewport"),this.handleCornerSize*=this.config.touchHandleMultiplier,this.handleEdgeSize*=this.config.touchHandleMultiplier),this.zoomPercentage=o(this,a,Ba).call(this,this.zoomRatio),o(this,a,Na).call(this)},ma=function(){let e=Math.round(this.canvasImageWidth*this.canvasesCSSScaleRatio),t=Math.round(this.canvasImageHeight*this.canvasesCSSScaleRatio);return e>this.editorCanvasesWidth&&(e=this.editorCanvasesWidth),t>this.editorCanvasesHeight&&(t=this.editorCanvasesHeight),{width:e,height:t}},ba=function(){const e=this.editorCanvasesWidth/this.canvasImageWidth,t=this.editorCanvasesHeight/this.canvasImageHeight;return Math.min(e,t)},kt=function(e){return Math.ceil(e/this.zoomRatio/this.canvasesCSSScaleRatio*this.canvasDrawRatio)},Ee=function(e){return Math.ceil(e/this.zoomRatio/this.canvasesCSSScaleRatio)},ee=function(e){P.fire("onCanvasStatusMessage",{message:e}),this.dispatchEvent("onCanvasStatusMessage",{message:e})},va=function(){const{canvasesWrapper:e}=this.elements,{canvasImage:t,canvasDraw:i}=this.canvasElements;[t.width,t.height]=[this.canvasImageWidth,this.canvasImageHeight],[i.width,i.height]=[this.canvasDrawWidth,this.canvasDrawHeight],e.style.width=t.style.width=i.style.width=this.canvasesCSSWidth*this.zoomRatio+"px",e.style.height=t.style.height=i.style.height=this.canvasesCSSHeight*this.zoomRatio+"px"},F=function(){n(this,L).loadStatus==="loaded"&&(o(this,a,wa).call(this),o(this,a,$).call(this))},wa=function(){o(this,a,fa).call(this),o(this,a,va).call(this);const{canvasImage:e,ctxImage:t}=this.canvasElements,i=n(this,pe);t.clearRect(0,0,e.width,e.height),t.save(),t.translate(this.canvasImageXOrigin,this.canvasImageYOrigin),t.rotate(Math.PI/180*this.rotationAngle),t.translate(-this.canvasImageXOrigin,-this.canvasImageYOrigin),(this.flipped||this.flopped)&&(t.translate(this.flipXOrigin,this.flipYOrigin),t.scale(this.flipXAxisDirection,this.flipYAxisDirection)),this.canvasImageFilter&&(t.filter=this.canvasImageFilter,t.fillRect(0,0,1,1)),t.imageSmoothingEnabled=!1,t.drawImage(i,this.canvasImageDrawStart.x,this.canvasImageDrawStart.y,this.imageNaturalWidth,this.imageNaturalHeight),t.restore()},xa=async function(e,t){if(!o(this,a,at).call(this))return;const{fileFormatSelect:i}=this.elements;i.value||o(this,a,ee).call(this,this.translator._("Select a file format first"));const r=i.value,l=document.createElement("canvas"),h=l.getContext("2d");l.width=e,l.height=t,h.drawImage(n(this,ue),0,0,l.width,l.height);try{const p=await pt(l.toDataURL(r,1),n(this,L).name),m=new Le(this.getImageFileConfiguration());await m.load(n(this,K),p,null,n(this,L).name),o(this,a,_e).call(this,m,()=>{o(this,a,Me).call(this)}).catch(console.error)}catch(p){console.warn(`Error during resizing of image ${p} ${p.stackTrace}`)}},ya=function(){const e=this.handleCornerSize,t=this.handleEdgeSize,i=this.handleEdgeMargin,{x:r,y:l,w:h,h:p}=this.selectionAreaScaled,m=h/(2*e+i)>1&&p/(2*e+i)>1,b=new w(r,l),A=new w(r+h-e,l),f=new w(r+h-e,l+p-e),x=new w(r,l+p-e),k=new w(r+e+i,l),C=new w(r+h-t,l+e+i),G=new w(r+e+i,l+p-t),U=new w(r,l+e+i);let q=h-2*i-2*e,fe=t,ht=t,Ie=p-2*i-2*e;m||(b.x-=e,b.y-=e,A.x+=e,A.y-=e,f.x+=e,f.y+=e,x.x-=e,x.y+=e,k.x=r,k.y=l-t,C.x=r+h,C.y=l,G.x=r,G.y=l+p,U.x=r-t,U.y=l,q=h,Ie=p),q<50&&(q=0,fe=0,k.set(r,l),G.set(r,l)),Ie<50&&(ht=0,Ie=0,C.set(r,l),U.set(r,l));const T=this.selectionHandleAreas;T.grab.set(r,l,h,p),T.nw.set(b.x,b.y,e,e),T.n.set(k.x,k.y,q,fe),T.ne.set(A.x,A.y,e,e),T.e.set(C.x,C.y,ht,Ie),T.se.set(f.x,f.y,e,e),T.s.set(G.x,G.y,q,fe),T.sw.set(x.x,x.y,e,e),T.w.set(U.x,U.y,ht,Ie)},Sa=function(){const e=this.canvasDrawWidth,t=this.canvasDrawHeight,i=Math.round(this.gridGap/5);for(let r=0;r<e;r=r+i)this.gridLines.push({from:new w(r,0),to:new w(r,e),isGridLine:r%this.gridGap===0});for(let r=0;r<t;r=r+i)this.gridLines.push({from:new w(0,r),to:new w(e,r),isGridLine:r%this.gridGap===0})},$=function(){const{ctxDraw:e,canvasDraw:t}=this.canvasElements;e.clearRect(0,0,t.width,t.height),this.showGrid&&o(this,a,Ra).call(this),!(this.selectionArea.w===0&&this.selectionArea.h===0)&&(this.selectionMode==="select"?(o(this,a,Je).call(this),o(this,a,Ft).call(this)):o(this,a,st).call(this)&&(o(this,a,Je).call(this),o(this,a,Ft).call(this),o(this,a,ya).call(this),o(this,a,za).call(this)))},zt=function(e){const t=this.config.snapThresholdPercentage,i=t*this.canvasImageWidth,r=t*this.canvasImageHeight;return e.x<i&&(e.x=0),e.y<r&&(e.y=0),e.right>this.canvasImageWidth-i&&(e.x=this.canvasImageWidth-e.w),e.bottom>this.canvasImageHeight-r&&(e.y=this.canvasImageHeight-e.h),e},Rt=function(e){const t=this.canvasImageWidth,i=this.canvasImageHeight;return e.x<0||e.x>t||e.w>t||e.right>t||e.y<0||e.y>i||e.h>i||e.bottom>i},Ke=function(e){this.selectionArea.set(e.x,e.y,e.w,e.h),this.selectionAreaScaled=this.selectionArea.scale(this.canvasDrawRatio),o(this,a,Tt).call(this)},Ct=function(e){const{w:t,h:i}=e;this.selectionValid=t>=this.minWidth&&t<=this.maxWidth&&i>=this.minHeight&&i<=this.maxHeight},Je=function(){const{ctxDraw:e}=this.canvasElements;e.save();const{x:t,y:i,h:r,w:l}=this.selectionAreaScaled;e.lineWidth=this.lineWidth,e.strokeStyle=this.config.selectionBoxStrokeStyle,e.beginPath(),e.rect(t,i,l,r),e.stroke(),e.setLineDash([this.selectionLineDashSize]),e.lineDashOffset=-this.animationOffset,e.strokeStyle=this.selectionValid?this.config.selectionBoxLineDashStrokeStyle:this.config.selectionBoxInvalidLineDashStrokeStyle,e.stroke(),e.restore()},Ft=function(){this.config.animateSelection&&this.selectionAnimationController.start()},Aa=function(){this.selectionAnimationController.pause()},ka=function(){const{ctxDraw:e}=this.canvasElements;e.save(),e.strokeStyle=this.config.crossStrokeStyle,e.lineWidth=this.lineWidth;const{x:t,y:i,w:r,h:l}=this.selectionArea,h=new w(t+r/2,i+l/2),p=new w(h.x-this.crossLineSize/2,h.y).scale(this.canvasDrawRatio),m=new w(h.x+this.crossLineSize/2,h.y).scale(this.canvasDrawRatio),b=new w(h.x,h.y-this.crossLineSize/2).scale(this.canvasDrawRatio),A=new w(h.x,h.y+this.crossLineSize/2).scale(this.canvasDrawRatio);e.beginPath(),e.moveTo(p.x,p.y),e.lineTo(m.x,m.y),e.moveTo(b.x,b.y),e.lineTo(A.x,A.y),e.stroke(),e.restore()},za=function(){const e=this.canvasElements.ctxDraw;e.save(),e.lineWidth=this.lineWidth,e.strokeStyle=this.config.selectionHandleStrokeStyle,e.fillStyle=this.config.selectionHandleOverFillStyle;for(const t of Object.values(this.selectionHandleAreas))this.selectionMode==="resize"&&!t.active||(e.beginPath(),e.rect(t.x,t.y,t.w,t.h),t.over===!0&&e.fill(),(t.type==="corner"||t.over===!0||this.selectionWasTouchEvent===!0)&&(e.stroke(),e.save(),e.strokeStyle=this.config.selectionHandleLineDashStrokeStyle,e.setLineDash([15,15]),e.stroke(),e.restore()));e.restore()},Ra=function(){const e=this.canvasElements.ctxDraw;e.save(),e.lineWidth=this.lineWidth;for(const t of this.gridLines)e.beginPath(),e.moveTo(t.from.x,t.from.y),e.lineTo(t.to.x,t.to.y),t.isGridLine===!0?(e.strokeStyle=this.config.gridStrokeStyle,e.stroke()):this.config.showSubGrid&&(e.strokeStyle=this.config.subGridStrokeStyle,e.stroke());e.restore()},It=function(e,t){const{canvasesWrapper:i}=this.elements,r=i.getBoundingClientRect(),l=(e-r.left)/this.canvasesCSSScaleRatio/this.zoomRatio,h=(t-r.top)/this.canvasesCSSScaleRatio/this.zoomRatio;return new w(l,h)},Ca=function(e,t){const i=o(this,a,It).call(this,e,t);this.selectionPointerStart.set(i.x,i.y)},Lt=function(e,t){const i=o(this,a,It).call(this,e,t);this.selectionPointerCurrent.set(i.x,i.y)},Et=function(){const e={selectionHandle:!1,resizeHandle:!1},t=this.selectionHandleAreas;for(const i of Object.keys(t)){const r=t[i];if(r.pointIsInsideArea(this.selectionPointerCurrent.scale(this.canvasDrawRatio))){r.mode==="grab"?(e.selectionHandle=r,e.resizeHandle=!1):r.mode==="resize"&&(e.resizeHandle=r,e.selectionHandle=!1,this.selectionHandleAreas[r.name].over=!0);const{canvasesWrapper:h}=this.elements;h.style.cursor=r.cursor}else this.selectionHandleAreas[r.name].over=!1}if(!e.selectionHandle&&!e.resizeHandle){const{canvasesWrapper:i}=this.elements;i.style.cursor="crosshair"}return e},_t=function(){const{w:e,h:t}=this.selectionArea;return{aspectRatio:e/t,aspectRatioLabel:o(this,a,Ze).call(this,e,t)}},Qe=function(){const{aspectRatioSelect:e}=this.elements,t=e.value;return o(this,a,it).call(this,t).value},Fa=function(e){const t=this.canvasElements.ctxImage;t.save(),t.fillStyle="orange",t.fillRect(e.x-10,e.y-10,20,20),t.restore()},et=function(e){const t=o(this,a,Qe).call(this);return t>-1&&(e.w=e.h*t),e},tt=function(e){const t=o(this,a,Qe).call(this);return t>-1&&(e.h=e.w/t),e},Ia=function(){const e=o(this,a,Et).call(this);return e.resizeHandle?(this.selectionMode="resize",this.selectionAction=e.resizeHandle.action,this.selectionStartPointerOver=e,e.resizeHandle.active=!0):e.selectionHandle?(this.selectionMode="grab",this.selectionAction=e.selectionHandle.action,this.selectionStartPointerOver=e):(this.selectionMode="select",this.selectionStartPointerOver=null),this.selectionMode},La=function(){this.clearSelection()},Ea=function(e=!1){e?c(this,O,1):n(this,O)!==null&&c(this,O,null);const t=o(this,a,Wa).call(this);o(this,a,Ct).call(this,t),o(this,a,Ke).call(this,t),o(this,a,$).call(this)},_a=function(){switch(c(this,W,this.selectionArea.cloned),c(this,Ce,new w(n(this,W).xHalfway,n(this,W).yHalfway)),this.selectionAction){case"nw-resize":case"w-resize":this.selectionPointerStart.x=this.selectionArea.right,this.selectionPointerStart.y=this.selectionArea.bottom;break;case"ne-resize":case"n-resize":this.selectionPointerStart.x=this.selectionArea.left,this.selectionPointerStart.y=this.selectionArea.bottom;break;case"se-resize":case"e-resize":this.selectionPointerStart.x=this.selectionArea.left,this.selectionPointerStart.y=this.selectionArea.top;break;case"sw-resize":case"s-resize":this.selectionPointerStart.x=this.selectionArea.right,this.selectionPointerStart.y=this.selectionArea.top;break}},Ma=function(e=!1){e?n(this,O)===null&&c(this,O,o(this,a,_t).call(this).aspectRatio):n(this,O)!==null&&c(this,O,null);const t=o(this,a,Ta).call(this);o(this,a,Rt).call(this,t)||(o(this,a,Ct).call(this,t),o(this,a,Ke).call(this,t),o(this,a,$).call(this))},Ha=function(){c(this,W,this.selectionArea.cloned)},Da=function(e=!1){console.log(e);const t=n(this,W),i=t.cloned;i.x=t.x+this.selectionPointerCurrent.x-this.selectionPointerStart.x,i.y=t.y+this.selectionPointerCurrent.y-this.selectionPointerStart.y,o(this,a,Rt).call(this,i)||(o(this,a,Ke).call(this,i),o(this,a,$).call(this),o(this,a,ka).call(this))},Pa=function(e){if(this.selectionMode==="select")return;const t=e.clientX||e.touches[0].clientX,i=e.clientY||e.touches[0].clientY;o(this,a,Ca).call(this,t,i),e.pointerType==="touch"?(this.selectionWasTouchEvent=!0,o(this,a,Lt).call(this,t,i)):this.selectionWasTouchEvent=!1;const r=o(this,a,Ia).call(this);r==="select"?o(this,a,La).call(this):r==="resize"?o(this,a,_a).call(this):r==="grab"&&o(this,a,Ha).call(this)},Oa=function(e){const t=e.clientX||e.touches[0].clientX,i=e.clientY||e.touches[0].clientY;o(this,a,Lt).call(this,t,i),o(this,a,Et).call(this);const r=this.selectionMode;r==="select"?o(this,a,Ea).call(this,e.shiftKey):r==="resize"?o(this,a,Ma).call(this,e.shiftKey):r==="grab"?o(this,a,Da).call(this,e.shiftKey):o(this,a,$).call(this)},Mt=function(){this.selectionMode=null;const e=this.selectionHandleAreas;for(const t of Object.keys(e)){const i=e[t];i.active=!1}c(this,W,null),o(this,a,$).call(this)},Ht=function(e){const t=this.canvasElements.canvasImage,i=this.canvasElements.ctxImage;i.save(),i.clearRect(0,0,t.width,t.height),i.fillRect(this.selectionPointerStart.x-10,this.selectionPointerStart.y-10,20,20),i.strokeStyle="blue",i.strokeRect(e.x,e.y,e.w,e.h),i.restore()},Dt=function(){const e=this.selectionPointerStart;o(this,a,Fa).call(this,e)},Wa=function(){const e=this.selectionPointerStart.x,t=this.selectionPointerStart.y,i=this.selectionPointerCurrent.x-this.selectionPointerStart.x,r=this.selectionPointerCurrent.y-this.selectionPointerStart.y;let l=new Fe(e,t,i,r);return this.debug&&o(this,a,Dt).call(this),l=o(this,a,et).call(this,l),l=o(this,a,zt).call(this,l),this.debug&&o(this,a,Ht).call(this,l),l},Ta=function(){let e=n(this,W);const t=this.selectionAction,i=o(this,a,Qe).call(this);this.debug&&o(this,a,Dt).call(this);const r=["e-resize","w-resize"],l=["n-resize","s-resize"];if(i>-1?(l.push("ne-resize"),r.push("nw-resize","se-resize","sw-resize")):(r.push("nw-resize","ne-resize","se-resize","sw-resize"),l.push("nw-resize","ne-resize","se-resize","sw-resize")),r.includes(t)&&(e.x=this.selectionPointerStart.x,e.w=this.selectionPointerCurrent.x-this.selectionPointerStart.x),l.includes(t)&&(e.y=this.selectionPointerStart.y,e.h=this.selectionPointerCurrent.y-this.selectionPointerStart.y),i>-1){if((t==="n-resize"||t==="s-resize")&&(e.x=n(this,Ce).x-e.w/2,e=o(this,a,et).call(this,e)),(t==="w-resize"||t==="e-resize")&&(e.y=n(this,Ce).y-e.h/2,e=o(this,a,tt).call(this,e)),t==="ne-resize"&&(e=o(this,a,et).call(this,e)),t==="nw-resize"){e=o(this,a,tt).call(this,e);const h=this.selectionPointerStart;e.y=h.y-e.h}(t==="sw-resize"||t==="se-resize")&&(e=o(this,a,tt).call(this,e))}return e=o(this,a,zt).call(this,e),this.debug&&o(this,a,Ht).call(this,e),e},je=new WeakMap,Pt=function(e,t=1,i=999,r=!1,l){const h=r?parseFloat(e):parseInt(e),p=Math.min(Math.max(h,t),i);return p!==h&&l(p),p},Ge=new WeakMap,ct=new WeakMap,dt=new WeakMap,$a=function(){const{canvasDraw:e}=this.canvasElements,{canvasesWrapper:t,fileFormatSelect:i}=this.elements,{freeRotationRange:r,freeRotationRangeValue:l}=this.elements,{resizeWidth:h,resizeHeight:p}=this.elements,{zoomPercentage:m}=this.elements,{showFilters:b,filterContainer:A}=this.elements;P.register("onCanvasStatusMessage",f=>{this.dispatchEvent("canvasStatusMessage",f.detail)}),P.register("onCloseImageEditor",f=>{this.dispatchEvent("closeImageEditor",f.detail)}),P.register("onImageSave",f=>{this.dispatchEvent("imageSave",f.detail)}),Object.entries(n(this,je)).forEach(([f,x])=>{const k=this.shadowRoot.querySelector(`#${ta(f)}`);if(!k){console.error(`element with id #${f} not found, cannot add event listener`);return}k.addEventListener("click",C=>{C.stopPropagation(),C.preventDefault(),x(C)},!1)}),this.shadowRoot.addEventListener("click",f=>{const{dialogHelp:x}=this.elements;f.target===x&&n(this,je).dialogHelpClose(f)}),this.shadowRoot.addEventListener("keydown",n(this,dt),!1),m.addEventListener("input",()=>{const f=m.value,x=C=>{m.value=C},k=o(this,a,Pt).call(this,f,1,1e3,!0,x);this.zoomRatio=o(this,a,Wt).call(this,k),o(this,a,F).call(this)}),i.addEventListener("change",()=>{o(this,a,Me).call(this)}),r.addEventListener("input",f=>{f.preventDefault(),this.clearSelection(!1),this.rotationAngle=parseInt(f.target.value),o(this,a,Gt).call(this),o(this,a,F).call(this)}),l.addEventListener("change",f=>{f.preventDefault(),this.clearSelection(!1),o(this,a,jt).call(this),this.rotationAngle=o(this,a,Pt).call(this,f.target.value,0,360,!1,x=>{f.target.value=x}),o(this,a,F).call(this)}),h.addEventListener("input",f=>{f.preventDefault();const{resizeWidth:x,resizeHeight:k}=this.elements;this.clearSelection(!1),this.resizeAspectRatioLocked&&(k.value=Math.round(x.value/o(this,a,Ot).call(this))),n(this,Ge).call(this,x.value,k.value)}),p.addEventListener("input",f=>{f.preventDefault();const{resizeWidth:x,resizeHeight:k}=this.elements;this.clearSelection(!1),this.resizeAspectRatioLocked&&(x.value=Math.round(k.value*o(this,a,Ot).call(this))),n(this,Ge).call(this,x.value,k.value)}),r.addEventListener("contextmenu",f=>{f.preventDefault(),f.stopPropagation()}),b.addEventListener("click",f=>{A.classList.toggle("show",f.target.checked)}),this.canvasFilters.addFilterEventListeners(),e.addEventListener("pointerdown",f=>{f.preventDefault(),o(this,a,Pa).call(this,f)}),e.addEventListener("pointerup",f=>{f.preventDefault(),o(this,a,Mt).call(this)}),e.addEventListener("pointermove",f=>{f.preventDefault(),o(this,a,Oa).call(this,f)}),e.addEventListener("pointerenter",f=>{f.preventDefault(),t.style.cursor="crosshair"}),e.addEventListener("pointerleave",f=>{f.preventDefault(),o(this,a,Mt).call(this),t.style.cursor="default"}),e.addEventListener("touchstart",f=>f.preventDefault()),e.addEventListener("contextmenu",f=>{f.preventDefault(),f.stopPropagation()},!0)},Ot=function(){return n(this,ue).width/n(this,ue).height},at=function(){const{fileFormatSelect:e}=this.elements;return e.value?o(this,a,ja).call(this)?!0:(e.focus(),o(this,a,ee).call(this,this.translator._("Conversion to this file format not supported")),!1):(e.focus(),o(this,a,ee).call(this,this.translator._("Select a file format first")),!1)},Wt=function(e){return e/100/this.canvasesCSSScaleRatio},Ba=function(e){return(e*this.canvasesCSSScaleRatio*100).toFixed(0)},Va=function(){const{zoomPercentage:e}=this.elements;return parseFloat(e.value)},Na=function(){const{fileFormatCurrent:e,resizeWidth:t,resizeHeight:i,zoomPercentage:r,imageAspectRatio:l,imageOrientation:h}=this.elements;e.innerText=n(this,ge).mimeType,t.value=this.imageNaturalWidth,i.value=this.imageNaturalHeight,r.value=this.zoomPercentage,l.innerText=this.imageAspectRatio,h.innerText=this.imageOrientation},Tt=function(){const{imageSelectionSize:e,imageSelectionAspectRatio:t}=this.elements,{w:i,h:r}=this.selectionArea;e.innerText=Math.floor(i)+" x "+Math.floor(r),t.innerText=o(this,a,Ze).call(this,i,r)},ja=function(){const{fileFormatSelect:e}=this.elements;return!e.options[e.selectedIndex].hasAttribute("disabled")},Ga=function(e){const{fileFormatSelect:t}=this.elements;let i=!1;for(const r of t.options)r.value===e&&!r.hasAttribute("disabled")&&(i=!0);return i},$t=function(e){const{editorCanvases:t}=this.elements;e?t.classList.remove("canvases--image-loaded"):t.classList.add("canvases--image-loaded")},_e=async function(e,t){if(e instanceof Le){o(this,a,$t).call(this,!0),c(this,L,e);try{const i=await li(e.imageObjectURL);c(this,pe,i),o(this,a,F).call(this),o(this,a,$t).call(this,!1),t==null||t(i)}catch(i){this.logger.log("error",i),console.error("error",i)}}},Ua=function(){const e=this.availableAspectRatios,t=this.selectionAspectRatios;e.forEach(l=>{l.active=t.includes(l.name)});const i=e.find(l=>l.name==="free");i.active=!this.freeSelectDisabled;const r=e.find(l=>l.name==="locked");r.active=!this.freeSelectDisabled},qa=function(){const e=this.availableFileFormats,t=this.fileFormats;for(const i of e)i.active=t.includes(i.value)},Xa=function(e){const{fileFormatSelect:t}=this.elements,i=this.availableFileFormats;gt(t);const r=document.createElement("template");r.innerHTML='<option hidden value="" data-i18n="Choose file format"></option>',t.appendChild(r.content);for(const l of i){const h=document.createElement("template");h.innerHTML=`<option value="${l.value}" id="fileFormat_${l.name}" ${l.supported&&l.active?"":'data-i18n-attr="title" data-i18n="Conversion to this file format not supported" disabled'}>${l.label}</option>`,t.appendChild(h.content)}e&&o(this,a,Ga).call(this,e)?t.value=e:t.value=""},Ya=function(){const{aspectRatioSelect:e}=this.elements,t=this.availableAspectRatios;gt(e);for(const i of t)if(i.active){const r=document.createElement("template");r.innerHTML=`<option value="${i.name}" id="ratio_${i.name}">${i.label}</option>`,e.appendChild(r.content)}o(this,a,He).call(this)},Za=function(){const{aspectRatioSelect:e,selectionAspectRatioLock:t}=this.elements;if(this.freeSelectDisabled||e.value!=="free"){t.setAttribute("disabled","disabled"),t.classList.remove("locked");return}t.removeAttribute("disabled"),t.classList.remove("locked")},Bt=function(){const e=this.shadowRoot.querySelector('option[id="ratio_locked"]'),{aspectRatioSelect:t,selectionAspectRatioLock:i}=this.elements;if(!this.freeSelectDisabled)if(this.selectionAspectRatioLocked){const{aspectRatio:r,aspectRatioLabel:l}=o(this,a,_t).call(this);i.classList.add("locked"),o(this,a,it).call(this,"locked").value=r,e.removeAttribute("disabled"),e.label=`${l}`,t.value="locked"}else i.classList.remove("locked"),e.setAttribute("disabled","disabled"),e.label="locked",o(this,a,He).call(this)},Vt=function(){const{resizeAspectRatioLock:e}=this.elements;this.resizeAspectRatioLocked?e.classList.add("locked"):e.classList.remove("locked")},Nt=function(){if(!o(this,a,st).call(this)&&!this.selectionAspectRatioLocked){o(this,a,ee).call(this,this.translator._("Make selection before locking it"));return}this.selectionAspectRatioLocked=!this.selectionAspectRatioLocked,o(this,a,Bt).call(this)},Ka=function(){this.resizeAspectRatioLocked=!this.resizeAspectRatioLocked,o(this,a,Vt).call(this)},it=function(e){return this.availableAspectRatios.find(t=>t.name===e)},Me=function(){const{targetImageFileSize:e,targetImageFileSizeChange:t}=this.elements,{fileFormatSelect:i}=this.elements,r=i.value||n(this,L).mimeType,l=o(this,a,At).call(this,r),h=l-n(this,Re);e.innerText=o(this,a,Ye).call(this,l,1);let p,m;h===0?m='<span class="icon">-</span>':h<0?m=`<span class="icon">${v.arrowDownShort}</span>`:m=`<span class="icon">${v.arrowUpShort}</span>`,p=`${o(this,a,Ye).call(this,h,1)} ${m}`,t.innerHTML=p},Ja=function(){const{originalImageFileSize:e}=this.elements,t=n(this,L).mimeType;c(this,Re,o(this,a,At).call(this,t)),e.innerText=o(this,a,Ye).call(this,n(this,Re),1)},Qa=function(){const{freeRotationRange:e,freeRotationRangeValue:t}=this.elements;e.value=this.rotationAngle,t.value=this.rotationAngle},ei=function(){const{fileFormatSelect:e}=this.elements;e.value=n(this,ge).mimeType},He=function(){const{aspectRatioSelect:e}=this.elements;e.value=o(this,a,ti).call(this).name},ti=function(){const e=this.availableAspectRatios;return o(this,a,it).call(this,this.selectionAspectRatio)||e[0]},ai=function(){this.selectionAspectRatioLocked&&o(this,a,Nt).call(this)},ii=function(){this.canvasFilters.reset()},jt=function(){const{freeRotationRange:e}=this.elements;e.value=this.rotationAngle},Gt=function(){const{freeRotationRangeValue:e}=this.elements;e.value=this.rotationAngle},st=function(){const{w:e,h:t}=this.selectionArea;return e!==0&&t!==0},si=function(){var e,t,i;return!!((i=(t=(e=this.canvasElements)==null?void 0:e.canvasImage)==null?void 0:t.getContext("2d"))!=null&&i.filter)},d(Xe,"shadowTemplate",xi),d(Xe,"elementLookup",["#canvasesWrapper","#canvasesButtons","#editorCanvases","#menuFieldset","#aspectRatioSelect","#dialogHelp","#freeRotation","#freeRotationRange","#freeRotationRangeValue","#resizeAspectRatioLock","#selectionAspectRatioLock","#resizeWidth","#resizeHeight","#zoomPercentage","#imageAspectRatio","#imageOrientation","#originalImageFileSize","#targetImageFileSize","#targetImageFileSizeChange","#fileFormatCurrent","#fileFormatSelect","#imageSelectionSize","#imageSelectionAspectRatio","#filters","#showFilters","#filterContainer"]),d(Xe,"translationsPath","/lang");customElements.define("image-editor",Xe);
