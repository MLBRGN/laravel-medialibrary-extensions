var Oa=Object.defineProperty;var ki=c=>{throw TypeError(c)};var Wa=(c,a,e)=>a in c?Oa(c,a,{enumerable:!0,configurable:!0,writable:!0,value:e}):c[a]=e;var y=(c,a,e)=>Wa(c,typeof a!="symbol"?a+"":a,e),Bt=(c,a,e)=>a.has(c)||ki("Cannot "+e);var n=(c,a,e)=>(Bt(c,a,"read from private field"),e?e.call(c):a.get(c)),u=(c,a,e)=>a.has(c)?ki("Cannot add the same private member more than once"):a instanceof WeakSet?a.add(c):a.set(c,e),d=(c,a,e,t)=>(Bt(c,a,"write to private field"),t?t.call(c,e):a.set(c,e),e),o=(c,a,e)=>(Bt(c,a,"access private method"),e);import{b as G}from"./image-editor.js";const ce=[{name:"free",label:"Free selection",value:-1,active:!0},{name:"16:10",label:"16:10",value:16/10,active:!0},{name:"16:9",label:"16:9",value:16/9,active:!0},{name:"5:3",label:"5:3",value:5/3,active:!0},{name:"4:3",label:"4:3",value:4/3,active:!0},{name:"3:2",label:"3:2",value:3/2,active:!0},{name:"2:1",label:"2:1",value:2,active:!0},{name:"1:1",label:"1:1",value:1,active:!0},{name:"locked",label:"Locked",value:null,active:!0}],Ot=[{name:"JPEG",label:"JPEG",value:"image/jpeg"},{name:"WebP",label:"WebP",value:"image/webp"},{name:"PNG",label:"PNG",value:"image/png"},{name:"GIF",label:"GIF",value:"image/gif"},{name:"BMP",label:"BMP",value:"image/bmp"}];var J,Q,ee,te,de,De,gt,Me;let $t=(Me=class{constructor(a,e,t,s,r){u(this,De);u(this,J);u(this,Q);u(this,ee);u(this,te);u(this,de,{});this.set(a,e,t,s,r)}pointIsInsideArea(a){return a.x>this.x&&a.x<this.x+this.w&&a.y>this.y&&a.y<this.y+this.h}scale(a){return new Me(this.x*a,this.y*a,this.w*a,this.h*a,n(this,de))}set(a,e,t,s,r){d(this,J,a),d(this,Q,e),d(this,ee,t),d(this,te,s),r&&typeof r=="object"&&d(this,de,r),o(this,De,gt).call(this)}get x(){return n(this,J)}get y(){return n(this,Q)}get w(){return n(this,ee)}get h(){return n(this,te)}get top(){return n(this,Q)}get right(){return n(this,J)+n(this,ee)}get bottom(){return n(this,Q)+n(this,te)}get left(){return n(this,J)}get aspectRatio(){return n(this,ee)/n(this,te)}getOption(a){return n(this,de)[a]}set x(a){d(this,J,a)}set y(a){d(this,Q,a)}set w(a){d(this,ee,a),o(this,De,gt).call(this)}set h(a){d(this,te,a),o(this,De,gt).call(this)}get xHalfway(){return this.x+(this.right-this.left)/2}get yHalfway(){return this.y+(this.bottom-this.top)/2}setOption(a,e){return n(this,de)[a]=e,e}get cloned(){return new Me(n(this,J),n(this,Q),n(this,ee),n(this,te),n(this,de))}},J=new WeakMap,Q=new WeakMap,ee=new WeakMap,te=new WeakMap,de=new WeakMap,De=new WeakSet,gt=function(){this.w<0&&(this.w=Math.abs(this.w),this.x=this.x-this.w),this.h<0&&(this.h=Math.abs(this.h),this.y=this.y-this.h)},Me);var Be,Oe;const Si=class Si{constructor(a,e){u(this,Be);u(this,Oe);this.set(a,e)}scale(a){return new Si(this.x*a,this.y*a)}get x(){return n(this,Be)}get y(){return n(this,Oe)}set x(a){d(this,Be,a)}set y(a){d(this,Oe,a)}set(a,e){d(this,Be,a),d(this,Oe,e)}};Be=new WeakMap,Oe=new WeakMap;let S=Si;var We,Te;class Ta{constructor(a=!1,e=""){u(this,We,!1);u(this,Te,"");d(this,We,a),d(this,Te,e)}log(...a){n(this,We)&&console.log(n(this,Te),...a)}error(...a){n(this,We)&&console.error(n(this,Te),...a)}}We=new WeakMap,Te=new WeakMap;function yi(c,a){return new Ta(c,a)}const $a=async c=>new Promise((a,e)=>{const t=new Image;t.onload=()=>{a(t)},t.onerror=s=>{e(t,s)},t.src=c}),Pa=c=>new Promise((a,e)=>{const t=new Image;t.onload=()=>{a({width:t.width,height:t.height})},t.onerror=s=>{e(s)},t.src=c.imageObjectURL}),Pt=async(c,a,e)=>{let t;try{const s=await fetch(c,{signal:e}),r=await s.blob(),l=s.headers.get("content-type");t=new File([r],a,{type:l})}catch(s){console.log(s)}return t},Va=(c,a)=>{const e=document.createElement("a");e.href=c,e.download=a,e.click(),e.remove()};var $,N,P,ve,B,$e,it,at,Pe,we,xe,Ve,st,k,Vt,Ei,Fi,jt,Et,Ut,Li,Nt;class Ke{constructor(a={}){u(this,k);y(this,"configuration",{formatsRegex:/.png|.jpg|.jpeg|.webp/,forceAspectRatio:null,aspectRatioTolerance:.01,minWidth:100,maxWidth:3500,minHeight:100,maxHeight:3500,maxUploadFileSize:1e6,maxEditFileSize:5e6});u(this,$,null);u(this,N,null);u(this,P,null);u(this,ve,null);u(this,B,"pending");u(this,$e,null);u(this,it,null);u(this,at,null);u(this,Pe,null);u(this,we,null);u(this,xe,null);u(this,Ve,null);u(this,st,{valid:!0,rejectionMessages:[],validityMessages:[],rejected:!1});u(this,Et,a=>{d(this,we,a.width),d(this,xe,a.height),d(this,Ve,a.width/a.height)});this.logger=yi(!0,"ImageFile"),this.configuration=Object.assign({},this.configuration,a)}async load(a,e,t,s="no_name",r=()=>{}){d(this,$,a),d(this,P,e),d(this,N,t),this.name=s,await o(this,k,Vt).call(this,n(this,P),n(this,N),null,r)}loadDefer(a,e,t,s="no_name"){d(this,$,a),d(this,P,e),d(this,N,t),this.name=s}async loadDeferred(a,e=()=>{}){return o(this,k,Vt).call(this,n(this,P),n(this,N),a,e)}destroy(){URL.revokeObjectURL(n(this,ve))}get file(){return n(this,P)}get validity(){return n(this,st)}get loadStatus(){return n(this,B)}get name(){return n(this,$e)}set name(a){d(this,$e,a)}get width(){return n(this,we)}get src(){return n(this,N)}get height(){return n(this,xe)}get mimeType(){return n(this,it)}get imageObjectURL(){return n(this,ve)}}$=new WeakMap,N=new WeakMap,P=new WeakMap,ve=new WeakMap,B=new WeakMap,$e=new WeakMap,it=new WeakMap,at=new WeakMap,Pe=new WeakMap,we=new WeakMap,xe=new WeakMap,Ve=new WeakMap,st=new WeakMap,k=new WeakSet,Vt=async function(a,e,t,s=()=>{}){d(this,B,"loading"),G.fire("onImageFileLoadStart",{imageFile:this,intId:n(this,$)}),a?await o(this,k,Ei).call(this,a,s):e?await o(this,k,Fi).call(this,e,t,s):this.logger.log("ImageFile #load must be called with either src or file")},Ei=async function(a,e=()=>{}){d(this,P,a),d(this,N,URL.createObjectURL(a)),o(this,k,jt).call(this,a);try{d(this,B,"loaded"),d(this,ve,URL.createObjectURL(n(this,P))),await o(this,k,Ut).call(this)}catch(t){d(this,B,"loadError"),this.logger.log(`#loadSrc: could not load image dimensions ${t}`)}o(this,k,Nt).call(this),e(n(this,$),this),G.fire("onImageFileLoadEnd",{imageFile:this,intId:n(this,$)})},Fi=async function(a,e,t=()=>{}){d(this,B,"loading"),G.fire("onImageFileLoadStart",{imageFile:this,intId:n(this,$)}),d(this,N,a);try{const s=await Pt(n(this,N),this.name,e);d(this,P,s),o(this,k,jt).call(this,s),d(this,B,"loaded"),d(this,ve,URL.createObjectURL(n(this,P)));try{await o(this,k,Ut).call(this)}catch(r){this.logger.log(`#loadSrc: could not load image dimensions: ${r}`,n(this,B))}}catch(s){d(this,B,"loadError"),this.logger.log(`#loadSrc: could not load src: ${s}`,n(this,B))}o(this,k,Nt).call(this),t(n(this,$),this),G.fire("onImageFileLoadEnd",{imageFile:this,intId:n(this,$)})},jt=function(a){d(this,it,a.type),d(this,at,o(this,k,Li).call(this,a.type)),d(this,Pe,a.size),d(this,$e,a.name)},Et=new WeakMap,Ut=async function(){try{const a=await Pa(this);n(this,Et).call(this,a)}catch(a){this.logger.log(`#getImageDimension: could not get image dimensions: ${a}`,n(this,B))}},Li=function(a){return a.substring(a.lastIndexOf("/")+1)},Nt=function(){const a=n(this,st),{minWidth:e,minHeight:t,maxWidth:s,maxHeight:r}=this.configuration,{maxUploadFileSize:l,maxEditFileSize:g}=this.configuration,p=m=>{a.valid=!1,a.rejected=!0,a.validityMessages.push(m),a.rejectionMessages.push(m)},v=m=>{a.valid=!1,a.validityMessages.push(m)};if(n(this,B)==="loadError"){p("Load error");return}if(n(this,we)<e&&p("Width too small"),n(this,xe)<t&&p("Height too small"),n(this,we)>s&&p("Width too large"),n(this,xe)>r&&p("Height too large"),this.configuration.formatsRegex||console.log("empty formatsRegex!"),this.configuration.formatsRegex.test(`.${n(this,at)}`)||p("Wrong file format"),n(this,Pe)>g&&p("Filesize too large"),n(this,Pe)>l&&v("Filesize too large"),this.configuration.forceAspectRatio!==null){const m=this.configuration.forceAspectRatio,w=this.configuration.aspectRatioTolerance;(n(this,Ve)<m-w||n(this,Ve)>m+w)&&v("Wrong aspect ratio")}G.fire("onImageFileValidated",{imageFile:this,intId:n(this,$)})};const Wt=c=>{for(;c!=null&&c.firstChild;)c.removeChild(c.firstChild)},ja=c=>!!c&&c.constructor===Object,Ua=c=>c.charAt(0).toUpperCase()+c.slice(1),Na=c=>Number(c)===c&&c%1===0,qa=c=>Number(c)===c&&c%1!==0,_i=c=>c.replace(/[A-Z]+(?![a-z])|[A-Z]/g,(a,e)=>(e?"-":"")+a.toLowerCase()),Ri=function(c,a,e=500){let t;return(...s)=>{clearTimeout(t),t=setTimeout(()=>{c.apply(a,s)},e)}},Ga=`

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
        --neutral-active-bg-color: rgba(0, 0, 0, 0.5);
        --neutral-active-fg-color: rgba(256, 256, 256, 1);
        --neutral-hover-bg-color: rgb(208, 208, 215);
        --neutral-hover-fg-color: rgb(0, 0, 0);
        --neutral-active-hover-bg-color: rgba(0, 0, 0, 0.7);
        --neutral-active-hover-fg-color: rgba(256, 256, 256, 1);

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
        --control-active-bg-color: var(--neutral-active-bg-color);
        --control-active-fg-color: var(--neutral-active-fg-color);
        
        --control-hover-bg-color: var(--neutral-hover-bg-color);
        --control-hover-fg-color: var(--neutral-hover-fg-color);
        --control-active-hover-bg-color: var(--neutral-active-hover-bg-color);
        --control-active-hover-fg-color: var(--neutral-active-hover-fg-color);
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
        --button-active-bg-color: var(--control-active-bg-color);
        --button-active-fg-color: var(--control-active-fg-color);
        --button-hover-bg-color: var(--control-hover-bg-color);
        --button-hover-fg-color: var(--control-hover-fg-color);
        --button-active-hover-bg-color: var(--control-active-hover-bg-color);
        --button-active-hover-fg-color: var(--control-active-hover-fg-color);
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

        --canvas-wrapper-bg-color: #666;
        --container-info-bg-color: #fafafa;
        --container-info-fg-color: var(--container-fg-color);
        --container-info-border: 1px dotted #ccc;
        --container-info-padding: .5em;

        display: block;
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
        padding-left: 1em;
        padding-right: 1em;
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
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    /* Headings */

    .heading {

    }
    
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
        color: var(--h1-color);
    }
    
    h2 {
        font-size: clamp(1em, 1vw, 1.7em);
        margin-top: 10px;
        margin-bottom: 10px;
        color: var(--h2-color);
    }

    h3 {
        font-size: 1em;
        color: var(--h1-color);
        font-weight: 500;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    /* when no image loaded, canvases are not displayed */
    .canvas--image,
    .canvas--draw {
        display: none;
    }

    .canvas--image {
        image-rendering: pixelated; /* Most modern browsers */
        image-rendering: crisp-edges; /* Fallback */
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
        padding: .2em;
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
    .fieldset:disabled .heading,
    .fieldset:disabled .value-display {
        color: var(--field-disabled-fg-color);
    }

    .uom {
    }

    /* buttons */

    button {
        user-select: none;
    }
    
    .shadow {
        box-shadow: 1px 1px 1px 0 #777;
    }

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

    .button__label {
        display: none;
    }

    .button--icon.is-active {
        background-color: var(--button-active-bg-color); /* or your primary */
        color: var(--button-active-fg-color);
        border-color: transparent;
        box-shadow: 1px 1px 1px 0 #ccc;
    }

    .button--icon.is-active:hover {
        background-color: var(--button-active-hover-bg-color); /* or your primary */
        color: var(--button-active-hover-fg-color);
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
        user-select: none;
    }

    /* range */

    input[type=range] {
        padding-inline: 0;
    }

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
        width: fit-content;
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
        width: 0;
        height: 0;
        position: absolute;
    }

    .image-orientation-icon-landscape {
        rotate: 90deg;
    }

    .image-orientation-icon-portrait {
    }

    .display-inline-block {
        display: inline-block;
        margin-left: 1em;
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

        .heading {
            display: none;
        }

    }

    @media only screen and (min-width: 768px) {
        .show-button-labels .button__label {
            display: initial;
        }

    }
`,x={crop:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-crop" viewBox="0 0 16 16">' +
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
        </svg>`,boxArrowRight:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
        <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
        </svg>`,boundingBox:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bounding-box" viewBox="0 0 16 16">
        <path d="M5 2V0H0v5h2v6H0v5h5v-2h6v2h5v-5h-2V5h2V0h-5v2H5zm6 1v2h2v6h-2v2H5v-2H3V5h2V3h6zm1-2h3v3h-3V1zm3 11v3h-3v-3h3zM4 15H1v-3h3v3zM1 4V1h3v3H1z"/>
        </svg>`,personBoundingBox:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-bounding-box" viewBox="0 0 16 16">
        <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5"/>
        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
    </svg>`,personFill:`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
    </svg>`},Za=`
    <div id="filters" class="filters">
        <h2 class="heading" data-i18n="Filters"></h2>
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
                    <input type="range" id="brightness-range" class="input input--range" data-modifier="brightness" min="0" max="200" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="brightness-value" class="input input--number number__value" data-modifier="brightness" min="0" max="200" value="100" data-default="100">
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
                    <input type="range" id="contrast-range" class="input input--range" data-modifier="contrast" min="0" max="200" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="contrast-value" class="input input--number number__value" data-modifier="contrast" min="0" max="200" value="100" data-default="100">
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
                    <input type="range" id="saturate-range" class="input input--range" data-modifier="saturation" min="0" max="200" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="saturate-value" class="input input--number number__value" data-modifier="saturation" min="0" max="200" value="100" data-default="100">
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
                    <input type="range" id="exponent" class="input input--range" data-modifier="gamma-exponent" min="0" max="3" step="0.01" value="1" data-i18n="Exponent" data-i18n-attr="title" data-default="1">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="exponent-value" class="input input--number number__value" data-modifier="gamma-exponent" min="0" max="3" value="1" data-default="1">
                        <label for="exponent-value" class="" data-i18n="exp"></label>
                    </span>
                </fieldset>
                <label class="label label--small"  for="amplitude" data-i18n="Amplitude"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Amplitude" class="is-hidden"></legend>
                    <input type="range" id="amplitude" class="input input--range" data-modifier="gamma-amplitude" min="0" max="3" step="0.01" value="1" data-i18n="Amplitude" data-i18n-attr="title" data-default="1">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="amplitude-value" class="input input--number number__value" data-modifier="gamma-amplitude" min="0" max="2.5" value="1" data-default="1">
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
                    <input type="range" id="grayscale-range" class="input input--range" data-modifier="grayscale" min="0" max="100" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="grayscale-value" class="input input--number number__value" data-modifier="grayscale" min="0" max="100" value="0" data-default="0">
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
                    <input type="range" id="sepia-range" class="input input--range" data-modifier="sepia" min="0" max="100" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="sepia-value" class="input input--number number__value" data-modifier="sepia" min="0" max="100" value="0" data-default="0">
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
                    <input type="range" id="invert-range" class="input input--range" data-modifier="invert" min="0" max="100" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="invert-value" class="input input--number number__value" data-modifier="invert" min="0" max="100" value="0" data-default="0">
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
                    <input type="range" id="blur-range" class="input input--range" data-modifier="blur" min="0" max="10" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="blur-value" class="input input--number number__value" data-modifier="blur" min="0" max="100" value="0" data-default="0">
                        <label for="blur-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="drop-shadow" data-filter-string="drop-shadow(length-1 length-2 length-3 color)">
                <label class="label label--checkbox">
                    <input type="checkbox" id="drop-shadow" class="input input--checkbox">
                    <span class="label__span" data-i18n="Drop shadow"></span>
                </label>
                <span class="container--form-text" data-i18n="Only works with (ially) transparent images"></span>
                <label class="label label--small" for="offset-x-range" data-i18n="Offset x"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Offset x" class="is-hidden"></legend>
                    <input type="range" id="offset-x-range" class="input input--range" data-modifier="drop-shadow-x-offset" min="-100" max="100" value="0" data-i18n="Offset x" data-i18n-attr="title" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="offset-x-value" class="input input--number number__value" data-modifier="drop-shadow-x-offset" min="-100" max="100" value="0" data-default="0">
                        <label for="offset-x-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
                <label class="label label--small" for="offset-y-range" data-i18n="Offset y"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Offset y" class="is-hidden"></legend>
                    <input type="range" id="offset-y-range" class="input input--range" data-modifier="drop-shadow-y-offset" min="-100" max="100" value="0" data-i18n="Offset y" data-i18n-attr="title" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="offset-y-value" class="input input--number number__value" data-modifier="drop-shadow-y-offset" min="-100" max="100" value="0" data-default="0">
                        <label for="offset-y-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
                <label class="label label--small" for="blur-radius-range" data-i18n="Blur radius"></label>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Blur radius" class="is-hidden"></legend>
                    <input type="range" id="blur-radius-range" class="input input--range" data-modifier="drop-shadow-blur-radius" min="0" max="100" value="0" data-i18n="Blur radius" data-i18n-attr="title" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="blur-radius-value" class="input input--number number__value" data-modifier="drop-shadow-blur-radius" min="0" max="100" value="0" data-default="0">
                        <label for="blur-radius-value" class="" data-i18n="px"></label>
                    </span>
                </fieldset>
                <fieldset class="fieldset wrapper--field">
                    <legend data-i18n="Shadow color" class="is-hidden"></legend>
                    <label class="label label--small" for="drop-shadow-color-value" data-i18n="Shadow color"></label>
                    <input type="color" id="drop-shadow-color-value" class="input input--color" data-modifier="drop-shadow-color" value="#ffffff" data-default="#ffffff">
                </fieldset>
            </span>
            <span class="container--filter" data-filter-type="svg" data-svg-filter-effect="duotone-effect">
                <label class="label label--checkbox">
                    <input type="checkbox" id="duo-tone" class="input input--checkbox">
                    <span class="label__span" data-i18n="Duotone"></span>
                </label>
                <fieldset class="fieldset">
                    <label class="label label--small" for="duo-tone-color1" data-i18n="Replace darker colors with"></label>
                    <input type="color" id="duo-tone-color1" class="input input--color" data-modifier="color1" value="#ff0000" data-default="#ff0000">
                    <label class="label label--small" for="duo-tone-color2" data-i18n="Replace lighter colors with"></label>
                    <input type="color" id="duo-tone-color2" class="input input--color" data-modifier="color2" value="#0000ff" data-default="#0000ff">
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
                    <input type="range" id="hue-rotate-range" class="input input--range" data-modifier="hue-rotate" min="0" max="360" value="0" data-default="0">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="hue-rotate-value" class="input input--number number__value" data-modifier="hue-rotate" min="0" max="360" value="0" data-default="0">
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
                    <input type="range" id="opacity-range" class="input input--range" data-modifier="opacity" min="0" max="100" value="100" data-default="100">
                    <span class="wrapper wrapper--field-composed">
                        <input type="number" id="opacity-value" class="input input--number number__value" data-modifier="opacity" min="0" max="100" value="100" data-default="100">
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
`,Xa=`
    <div id="image-properties">
        <h2 data-i18n="Image properties"></h2>
        <div class="container container--buttons">
            <div class="container--info">
                <div>
                    <span id="image-orientation-icon" class="icon icon-as-icon-button image-orientation-icon">${x.file}</span>
                    <span id="image-orientation"></span>
    <!--                <span id="file-format-current" class="button__label"></span>&nbsp;-->
                    <span class="icon icon-as-icon-button image-aspect-ratio-icon">${x.aspectRatio}</span>
                    <span id="image-aspect-ratio"></span>
                </div>
                <div>
                    <h3 id="file-size-label"></h3>
                    <p>
                        <span id="file-size-original-label">original</span>:
                        <span id="file-size-original" class="value-display fields-composed__uom"></span>
                    </p>
                    <p>
                        <span id="file-size-altered-label">altered</span>:
                        <span id="file-size-altered" class="value-display fields-composed__uom"></span>
                    </p>
                    <p>
                        <span id="file-size-difference-label">difference</span>:
                        <span id="file-size-difference" class="value-display fields-composed__uom"></span>
                    </p>
                </div>
           
            </div>
        </div>
    </div>
`,Ya=`
    <div id="file-format">
        <h2 data-i18n="File format"></h2>
        <div class="container container--buttons">
            <div class="container--info">
                <div class="wrapper wrapper--field-composed wrapper--file-format-select">
                    <span class="icon icon-as-icon-button">${x.boxArrowRight}</span>
                    <select id="file-format-select" class="select select--file-type fields-composed__field"></select>
                </div>
            </div>
        </div>
    </div>
`,Ka=`
    <div id="resize">
        <h2 data-i18n="Resize"></h2>
        <div class="wrapper wrapper--field-composed wrapper--resize">
            <label for="aspect-ratio-select" class="label fields-composed__label" data-i18n-attr="title" data-i18n="Aspect ratio"></label>
            <input type="number" class="input input--number" id="resize-width" size="5">
            <label for="resize-width">×</label>
            <input type="number" class="input input--number" id="resize-height" size="5">
            <label for="resize-height" data-i18n="px"></label>
            <button id="resize-aspect-ratio-lock" class="button button--icon button--selection-lock fields-composed__button" data-click-action="toggleResizeAspectRatioLock" data-i18n="Maintain aspect ratio" data-i18n-attr="title">
                <span class="icon icon--unlocked">${x.unlock}</span>
                <span class="icon icon--locked">${x.lock}</span>
            </button>
        </div>
    </div>
`,Ja=`
    <div id="rotation">
        <h2 data-i18n="Rotation"></h2>
        <div class="container container-buttons">
            <button type="button" id="rotate-ccw" class="button button--icon-text" data-click-action="rotateCcw" data-i18n="Rotate ccw" data-i18n-attr="title">
                <span class="icon">${x.arrowCounterclockwise}</span>
                <span class="button__label" data-i18n="Rotate ccw"></span>
            </button>
            <button type="button" id="rotate-cw" class="button button--icon-text" data-click-action="rotateCw" data-i18n="Rotate cw" data-i18n-attr="title">
                <span class="icon">${x.arrowClockwise}</span>
                <span class="button__label" data-i18n="Rotate cw"></span>
            </button>
            <div id="free-rotation" class="wrapper--field">
                <h2 data-i18n="Free rotate" class="is-hidden"></h2>
                <input type="range" id="free-rotation-range" class="input input--range" min="0" max="360" value="0">
                <span class="wrapper wrapper--field-composed">
                    <input type="number" id="free-rotation-range-value" class="input input--number number__value" min="0" max="360" value="0">
                    <span data-i18n-html="true" data-i18n="deg"></span>
                </span>
            </div>
        </div>
    </div>
`,Qa=`
    <div id="mirroring">
        <h2 data-i18n="Mirroring"></h2>
        <div class="container container-buttons">
            <button type="button" id="flip" class="button button--icon-text" data-click-action="flip" data-i18n="Flip" data-i18n-attr="title">
                <span class="icon">${x.arrowLeftRight}</span>
                <span class="button__label" data-i18n="Flip"></span>
            </button>
            <button type="button" id="flop" class="button button--icon-text" data-click-action="flop" data-i18n="Flop" data-i18n-attr="title">
                <span class="icon">${x.arrowDownUp}</span>
                <span class="button__label" data-i18n="Flop"></span>
            </button>
        </div>
    </div>
`,es=`
    <div id="selecting">
        <h2 data-i18n="Selection"></h2>
        <div class="container container-buttons">
            <div class="container container--info">
                <span class="icon icon-as-icon-button">${x.boundingBox}</span>
                <span id="image-selection-size"></span>
                (<span id="image-selection-aspect-ratio"></span>)
                <div class="wrapper wrapper--field-composed wrapper--aspect-ratio-select display-inline-block">
                    <span class="icon icon-as-icon-button">${x.aspectRatio}</span>
                    <select id="aspect-ratio-select" class="select select--aspect-ratio fields-composed__field"></select>
                    <button id="selection-aspect-ratio-lock" class="button button--icon button--selection-lock fields-composed__button" data-click-action="toggleSelectionAspectRatioLock" data-i18n="Maintain aspect ratio" data-i18n-attr="title">
                        <span class="icon icon--unlocked">${x.unlock}</span>
                        <span class="icon icon--locked">${x.lock}</span>
                    </button>
                </div>
                <button type="button" id="clear-selection" data-click-action="clearSelection" class="button button--icon" data-i18n="Clear selection" data-i18n-attr="title">
                    <span class="icon">${x.eraser}</span>
                </button>
            </div>
        </div>
    </div>
`,ts=`
    <div id="cropping">
        <h2 data-i18n="Cropping"></h2>
        <div class="container container-buttons">
            <button type="button" id="crop" data-click-action="crop" class="button button--icon-text" data-i18n="Crop" data-i18n-attr="title">
                <span class="icon">${x.crop}</span>
                <span class="button__label" data-i18n="Crop"></span>
            </button>
        </div>
    </div>
`,is=`
    <div id="other">
        <h2 data-i18n="Other"></h2>
        <div class="container container-buttons">
            <button type="button" id="reset" class="button button--icon-text" data-click-action="reset" data-i18n="Reset" data-i18n-attr="title">
                <span class="icon">${x.arrowRepeat}</span>
                <span class="button__label" data-i18n="Reset"></span>
            </button>
            <button type="button" id="toggle-grid" class="button button--icon" data-click-action="toggleGrid" data-i18n="Toggle grid" data-i18n-attr="title">
                <span class="icon">${x.grid3x3}</span>
            </button>
            <button type="button" id="download" class="button button--icon" data-click-action="download" data-i18n="Download" data-i18n-attr="title">
                <span class="icon">${x.download}</span>
            </button>
            <button type="button" id="edit-help" data-click-action="editHelp" class="button button--icon button--iconHelp" data-i18n="Help" data-i18n-attr="title">
                <span class="icon">${x.questionCircle}</span>
            </button>
        </div>
    </div>
`,as=`
    <h2 data-i18n="Save"></h2>
    <div class="container container-buttons">
        <button type="button" id="save" class="button button--icon-text" data-click-action="save" data-i18n="OK" data-i18n-attr="title">
            <span class="icon">${x.check}</span>
            <span data-i18n="Save"></span>
        </button>
        <button type="button" id="cancel" class="button button--icon-text" data-click-action="cancel" data-i18n="Cancel" data-i18n-attr="title">
            <span class="icon">${x.x}</span>
            <span data-i18n="Cancel"></span>
        </button>
    </div>
`,ss=`
 <fieldset id="menu-fieldset" class="fieldset">
        <h1 class="heading is-hidden" data-i18n="Tools"></h1>
        ${Xa}
        ${Ya}
        ${Ka}
        ${Ja}
        ${Qa}
        ${es}
        ${ts}
        ${is}
        ${Za}
        ${as}
    </fieldset>
`,os=`
     <style>
        ${Ga}
    </style>
   <main id="main" class="main">
        <div id="editor-canvases" class="editor__canvases" tabindex="0">
            <div id="canvases-wrapper" class="wrapper wrapper--canvases"></div>
            <fieldset id="canvases-buttons" class="fieldset canvases__buttons">
                <span class="wrapper wrapper--field-composed shadow">
                    <label for="zoom-percentage" class="is-hidden" data-i18n="Zoom level"></label>
                    <input type="number" id="zoom-percentage" class="input input--number" min="1" max="1000" step="1" value="100">
                    <label for="zoom-percentage" class="" data-i18n="per">%</label>
                    <button
                        type="button"
                        id="zoom-fit"
                        class="button button--icon"
                        data-zoom-mode="fit"
                        data-click-action="zoomFit"
                        title="Passend"
                    >
                        <span class="icon">${x.personBoundingBox}</span>
                    </button>
                   <button
                        type="button"
                        id="zoom-actual-size"
                        class="button button--icon"
                        data-zoom-mode="actual-size"
                        data-click-action="zoomActualSize"
                        title="Ware grootte"
                    >
                        <span class="icon">${x.personFill}</span>
                    </button>
                </span>
                <div class="canvases__zoom-buttons">
                    <button type="button" id="zoom-in" class="button button--icon shadow" data-click-action="zoomIn" data-i18n="Zoom in" data-i18n-attr="title">
                        <span class="icon">${x.plus}</span>
                    </button>
                    <button type="button" id="zoom-out" class="button button--icon shadow" data-click-action="zoomOut" data-i18n="Zoom out" data-i18n-attr="title">
                        <span class="icon">${x.dash}</span>
                    </button>
                </div>
            </fieldset>
        </div>
        <div id="editor-menu" class="editor__menu">
            ${ss}
        </div>
    </main>
    <dialog id="dialog-help" class="dialog dialog--help">
         <div class="dialog__inner">
               <div class="dialog__header">
                    <button type="button" id="dialog-help-close" class="button button--icon button--icon-close" data-click-action="dialogHelpClose" data-i18n="Close" data-i18n-attr="title" autofocus>
                        <span class="icon">${x.xLg}</span>
                    </button>
               </div>
               <div class="dialog__body">
                    <h1 class="heading" >Help</h1>
                    <h2 class="heading" data-i18n="Crop"></h2>
                    <p class="paragraph" data-i18n="Crop help"></p>
               </div>
         </div>
    </dialog>
        `;var L,q,he,ye,C,Mi,Ii,Hi,Di,Bi,Oi,Gt,Wi;const Ai=class Ai{constructor(a){u(this,C);y(this,"errorMessages",{INVALID_CONSTRUCTOR_ARGUMENTS:"Invalid constructor arguments"});u(this,L);u(this,q);u(this,he);u(this,ye,new Map);y(this,"logger");if(this.logger=yi((a==null?void 0:a.debug)??!0,"Translator"),!ja(a)){this.logger.error(this.errorMessages.INVALID_CONSTRUCTOR_ARGUMENTS);return}d(this,L,Object.assign({},Ai.defaultConfig,a)),this.determineAndSetActiveLanguage(),n(this,L).exposeFnName&&(window[n(this,L).exposeFnName]=this._.bind(this)),o(this,C,Ii).call(this).catch(console.error)}static get defaultConfig(){return{rootElement:document,fallbackTranslations:null,fallbackLanguage:"en",detectLanguage:!0,persist:!1,persistKey:"active_language",languagesSupported:["en","nl"],languagesPath:null,exposeFnName:"__",debug:!1}}setupMutationObserver(){const a=n(this,L).rootElement,e={attributes:!0,childList:!0,subtree:!0},t=r=>{for(const l of r)o(this,C,Wi).call(this,l.target)};new MutationObserver(t).observe(a,e)}static async getTranslator(a={}){}determineAndSetActiveLanguage(){if(this.config.persist){const a=localStorage.getItem(n(this,L).persistKey);this.activeLanguage=a??this.config.fallbackLanguage}else if(this.config.detectLanguage){const a=o(this,C,Mi).call(this);this.activeLanguage=a??this.config.fallbackLanguage}else this.activeLanguage=this.config.fallbackLanguage}get config(){return n(this,L)}get activeLanguage(){return n(this,q)}set activeLanguage(a){n(this,L).languagesSupported.includes(a)||(this.logger.log(`language "${a}" not found in supported languages ${n(this,L).languagesSupported} setting language to "${this.config.fallbackLanguage}"`),a=n(this,L).fallbackLanguage),d(this,q,a),n(this,L).persist&&localStorage.setItem(n(this,L).persistKey,n(this,q))}_(a,e=null){var s;let t=((s=n(this,he))==null?void 0:s[a])||a;return e&&(this.logger.log("replacements",e),e.forEach(r=>{const l=`%{${Object.keys(r)[0]}}`,g=Object.values(r)[0];t=t.replace(l,g)})),t}};L=new WeakMap,q=new WeakMap,he=new WeakMap,ye=new WeakMap,C=new WeakSet,Mi=function(){const a=navigator.languages&&navigator.languages[0]?navigator.languages[0]:navigator.language;return a?a.substring(0,2):!1},Ii=async function(){const a=o(this,C,Di).call(this);await o(this,C,Hi).call(this,a),o(this,C,Oi).call(this),this.setupMutationObserver()},Hi=async function(a){if(n(this,ye).has(n(this,q))){d(this,he,JSON.parse(n(this,ye).get(n(this,q))));return}await o(this,C,Bi).call(this,a)},Di=function(){return(n(this,L).languagesPath||new URL("./lang/",import.meta.url).href)+"/"+n(this,q)+".json"},Bi=function(a){return fetch(a).then(e=>e.json()).then(e=>{d(this,he,e),n(this,ye).has(n(this,q))||n(this,ye).set(n(this,q),JSON.stringify(n(this,he)))}).catch(()=>{n(this,L).fallbackTranslations&&d(this,he,n(this,L).fallbackTranslations)})},Oi=function(){const a=n(this,L).rootElement.querySelectorAll("[data-i18n]");for(const e of a)o(this,C,Gt).call(this,e)},Gt=function(a){const e=[];a.dataset.i18nAndAttr?e.push("inNode",a.dataset.i18nAndAttr):a.dataset.i18nAttr?e.push(a.dataset.i18nAttr):e.push("inNode");const t=a.hasAttribute("data-i18n-html"),s=a.hasAttribute("data-i18n-replacements");let r=a.getAttribute("data-i18n-replacements");if(s)try{r=JSON.parse(a.dataset.i18nReplacements)}catch{throw new Error(`Error parsing ${r}`)}const l=a.dataset.i18n,g=this._(l,r);if(g)for(const p of e)p==="inNode"?t?a.innerHTML=g:a.innerText=g:a.setAttribute(p,g)},Wi=function(a){const e=a.querySelectorAll("[data-i18n]");for(const t of e)o(this,C,Gt).call(this,t),this.logger.log(`%cSubsequent. Translated ${t.getAttribute("data-i18n")}`,"background-color:green;color:yellow;")};let qt=Ai;var ot,Ft;class ns extends HTMLElement{constructor(){var t;super();y(this,"elements",{});u(this,ot,null);y(this,"configuration",{});u(this,Ft,{debug:!1});if(this.setConfiguration(n(this,Ft)),!new.target)throw new TypeError("invalid instantiation");const e=d(this,ot,new.target);if(this.logger=yi(this.debug,e.name),e.shadowTemplate){const s=e.shadowMode||{mode:"open",delegatesFocus:!0};this.attachShadow(s).innerHTML=e.shadowTemplate,(t=e.elementLookup)==null||t.forEach(r=>{const l=_i(r),g=this.shadowRoot.querySelector(l);g||console.warn(`element with "${l}" not found`),this.elements[r.slice(1)]=g}),e.translationsPath&&(this.translator=new qt({rootElement:this.shadowRoot,fallbackTranslations:{},persist:!0,debug:!1,languagesSupported:["nl","en"],fallbackLanguage:"nl",detectLanguage:!0,languagesPath:e.translationsPath}))}}attributeChangedCallback(e,t,s){}shadowTemplateAdded(){}dispatchEvent(e,t){this.shadowRoot.dispatchEvent(new CustomEvent(e,{bubbles:!0,composed:!0,...t&&{detail:t}}))}getAttributeAsBoolean(e){return this.getBooleanAttributeOrDefault(e,!1)}getAttributeAsBooleanDefaultWhenFalse(e,t=null){return this.getBooleanAttributeOrDefault(e,t)}getBooleanAttributeOrDefault(e,t=!1){if(!this.hasAttribute(e))return t;const s=this.getAttribute(e);return s===""?!0:s.toLowerCase()==="false"?!1:s.toLowerCase()==="true"?!0:!!s}setAttributeAsBoolean(e,t){if(typeof t!="boolean")throw new Error(`set ${e} must be set to a boolean value.`);this.toggleAttribute(e,t)}getAttributeAsInteger(e,t){return this.hasAttribute(e)?parseInt(this.getAttribute(e)):typeof t<"u"?t:null}setAttributeAsInteger(e,t){if(!Na(t))throw new Error(`set ${e} must be set to a number value.`);this.setAttribute(e,t)}getAttributeAsFloat(e,t){return this.hasAttribute(e)?parseFloat(this.getAttribute(e)):typeof t<"u"?t:null}setAttributeAsFloat(e,t){if(!qa(t))throw new Error(`set ${e} must be set to a number value.`);this.setAttribute(e,t)}getAttributeAsCSV(e,t){const s=this.hasAttribute(e)?this.getAttribute(e):null;return t=typeof t<"u"?t:null,s?s.replace(/\s/g,"").split(","):t}setAttributeAsCSV(e,t){if(!Array.isArray(t))throw new Error(`set ${e} must called with an array as value.`);const s=t.join(", ");this.setAttribute(e,s)}setAttributeToString(e,t){if(typeof t!="string")throw new Error(`set ${e} must be set to a string value.`);this.setAttribute(e,t)}getAttributeOrDefault(e,t){return this.hasAttribute(e)?this.getAttribute(e):typeof t<"u"?t:null}setConfiguration(e){this.configuration=Object.assign(this.configuration,e)}getConfiguration(){return this.configuration}get config(){return this.configuration}get subClassName(){return n(this,ot).name}get debug(){return this.getAttributeAsBooleanDefaultWhenFalse("debug",this.configuration.debug)}set debug(e){this.setAttributeAsBoolean("debug",e)}}ot=new WeakMap,Ft=new WeakMap;var nt,W,je,Se,Lt,fe,$i,pt;class Ti extends ns{constructor(){super();u(this,fe);y(this,"formElement");u(this,nt,["ElementInternals","FormDataEvent","Callback"]);u(this,W);y(this,"internals");u(this,je,null);u(this,Se,[]);u(this,Lt,{required:!1});y(this,"formSubmitHandler",e=>{if(n(this,W)==="FormDataEvent"&&!n(this,je)){e.preventDefault(),this.logger.log("formSubmitHandler message: ",n(this,Se)[0]);const{hiddenFileUpload:t}=this.elements;t.focus(),t.setCustomValidity(this.translator._(n(this,Se)[0])),t.reportValidity()}this.logger.log("formSubmitHandler, FormDataEvent form submit detected")});this.setConfiguration(n(this,Lt)),this.formElement=this.findContainingForm(),this.forceSubmitMode?this.setFormSubmitMode(this.forceSubmitMode):this.determineFormSubmitMode(),n(this,W)==="ElementInternals"&&(this.internals=this.attachInternals(),this.formElement=this.internals.form)}attributeChangedCallback(e,t,s){if(super.attributeChangedCallback(e,t,s),e=e.toLowerCase(),t!==s)switch(e){case"required":const r=s;this.internals?this.internals.ariaRequired=r:this.setAttribute("aria-required",r);break;case"disabled":const l=s;this.internals?this.internals.ariaDisabled=l:this.setAttribute("aria-disabled",l),this.enableControls(!l);break}}determineFormSubmitMode(){const e="ElementInternals"in window&&"setFormValue"in window.ElementInternals.prototype,t="FormDataEvent"in window;o(this,fe,pt).call(this)&&e?this.setFormSubmitMode("ElementInternals"):o(this,fe,pt).call(this)&&t?this.setFormSubmitMode("FormDataEvent"):this.setFormSubmitMode("Callback")}handleSubmitUsingFormDataEvent(){throw new Error("Must override handleSubmitUsingFormDataEvent for DormDataEvent submit method to work.")}updateValue(){this.updateValidity(),n(this,W)==="ElementInternals"&&(this.internals.setFormValue(this.value),this.logger.log("update ElementInternals value"))}updateValidity(){throw new Error("Must override updateValidity")}connectedCallback(){n(this,W)==="FormDataEvent"&&this.formElement&&(this.formElement.addEventListener("formdata",this.handleSubmitUsingFormDataEvent),this.formElement.addEventListener("submit",this.formSubmitHandler,!1))}disconnectedCallback(){this.formElement&&(n(this,W)==="FormDataEvent"&&(this.formElement.removeEventListener("formdata",this.handleSubmitUsingFormDataEvent),this.formElement.removeEventListener("submit",this.formSubmitHandler)),this.formElement=null)}findContainingForm(){const e=this.getRootNode();return Array.from(e.querySelectorAll("form")).find(s=>s.contains(this))||null}formAssociatedCallback(e){this.formAssociated(e)}formDisabledCallback(e){o(this,fe,$i).call(this,e)}formStateRestoreCallback(e,t){t==="restore"?this.formStateRestore(e):this.logger.log("formStateRestoreCallback ignored. mode:",t)}formResetCallback(){this.formReset()}formAssociated(e){}formStateRestore(e){this.value=e}formReset(){}enableControls(e){throw new Error("enableControls must be overridden")}setFormSubmitMode(e){if(!n(this,nt).includes(e))throw new Error(`Not a valid submit mode ${e}. Use ${n(this,nt).join(", ")}`);!o(this,fe,pt).call(this)&&e!=="Callback"?(d(this,W,"Callback"),console.warn("Could not find containing form. Falling back to submit using callbacks.")):(d(this,W,e),this.logger.log(`Submit mode for ${this.subClassName}`,n(this,W)))}set validity(e){d(this,je,e)}get validity(){}set validityMessages(e){d(this,Se,e),n(this,W)==="ElementInternals"&&(n(this,je)?this.internals.setValidity({}):(this.internals.setValidity({customError:!0},this.translator._(n(this,Se)[0]),this.formValidationAnchor),this.internals.reportValidity()))}get validityMessages(){}get forceSubmitMode(){return this.getAttributeOrDefault("force-submit-mode",null)}set forceSubmitMode(e){this.setAttributeToString("force-submit-mode",e),this.setFormSubmitMode(e)}get submitMode(){return n(this,W)}get type(){return this.getAttributeOrDefault("type","input")}set type(e){this.setAttributeToString("type",e)}get value(){return this.getAttributeOrDefault("value","")}set value(e){this.setAttributeToString("value",String(e))}get name(){return this.getAttributeOrDefault("name","name")}set name(e){this.setAttributeToString("name",e)}get required(){return this.getAttributeAsBooleanDefaultWhenFalse("required",this.config.required)}set required(e){this.setAttributeAsBoolean("required",e)}get formValidationAnchor(){return this}set disabled(e){this.setAttributeAsBoolean("disabled",e)}get disabled(){}setConfiguration(e){super.setConfiguration(Object.assign(this.configuration,e)),this.config.disabled&&(this.disabled=!0)}}nt=new WeakMap,W=new WeakMap,je=new WeakMap,Se=new WeakMap,Lt=new WeakMap,fe=new WeakSet,$i=function(e){this.enableControls(!e)},pt=function(){return this.formElement!==null},y(Ti,"formAssociated",!0);const rs={lineWidth:1,selectionLineDashSize:14,crossLineSize:30,handleCornerSize:60,handleEdgeSize:40,handleEdgeMargin:0,touchHandleMultiplier:2,touchHandleMultiplierBreakpoint:"992px",aspectRatioTolerance:.01,snapThresholdPercentage:.01,zoomPercentageMin:1,zoomPercentageMax:1e3,zoomPercentageStep:10,gridLineCount:10,showSubGrid:!0,drawCanvasWidth:1500,animateSelection:!0,animateFPS:60,selectionHandleStrokeStyle:"rgba(230,230,230,0.9)",selectionHandleLineDashStrokeStyle:"rgba(0,0,0,0.9)",selectionHandleOverFillStyle:"rgba(230,230,230, 0.5)",gridStrokeStyle:"#ccc",selectionBoxStrokeStyle:"rgba(33,33,33,0.9)",selectionBoxLineDashStrokeStyle:"rgba(222,222,222,0.9)",selectionBoxInvalidLineDashStrokeStyle:"red",subGridStrokeStyle:"#ccc7",crossStrokeStyle:"#ccc",debug:!1,selectionAspectRatios:["free","16:10","16:9","5:3","4:3","3:2","2:1","1:1","locked"],selectionAspectRatio:"free",fileFormats:["image/png","image/jpeg","image/webp"],rotateDegreesStep:30,minWidth:100,minHeight:100,maxWidth:3500,maxHeight:3500,buttonLabelsEnabled:!0,imagePropertiesEnabled:!0,fileFormatEnabled:!0,rotationEnabled:!0,mirroringEnabled:!0,selectingEnabled:!0,croppingEnabled:!0,gridEnabled:!0,downloadingEnabled:!0,freeSelectDisabled:!1,freeRotationEnabled:!0,resizingEnabled:!1,filtersDisabled:!1,buttonLabels:!1,defaultZoomMode:"fit",defaultActiveAspectRatios:["free","16:10","16:9","5:3","4:3","3:2","2:1","1:1","locked"]};class ls extends Ti{constructor(){super();y(this,"editorCanvasesWidth",null);y(this,"editorCanvasesHeight",null);super.setConfiguration(rs)}}var rt,lt,ct;class Ci{constructor(a,e,t){u(this,rt);u(this,lt);u(this,ct);this.validate(a,e,t),d(this,lt,a),d(this,ct,e),d(this,rt,t)}validate(a,e,t){if(!a||!e||!t)throw new Error("ImageSource(name, src, id), invalid parameters.")}get id(){return n(this,rt)}get name(){return n(this,lt)}get src(){return n(this,ct)}}rt=new WeakMap,lt=new WeakMap,ct=new WeakMap;var dt,ue,be,Zt,Xt;class cs{constructor(a){u(this,be);u(this,dt);y(this,"shadowRoot");y(this,"elements");u(this,ue,[]);y(this,"canvasImageFilter",null);if(!a)throw new Error("Must be used with an ImageEditor instance");d(this,dt,a),this.shadowRoot=a.shadowRoot,this.elements=a.elements,this.logger=a.logger}singleModifierEffect(a,e,t){n(this,ue).push(a.replace("value",`${e[0].value}${t}`))}svgEffect(a,e,t,s){const r=a.dataset.svgFilterEffect;if(!r)return;const l=this.shadowRoot.querySelector(`#${r}`);switch(r){case"gamma-effect":this.gammaEffect(l,e);break;case"duotone-effect":this.duotoneEffect(l,s);break;default:this.logger.log(`${r} no special handling, svgFilter`,l)}n(this,ue).push(`url(#${r})`)}gammaEffect(a,e){if(e.getAttribute("data-modifier")==="gamma-exponent"){const t=a.querySelectorAll("[exponent]");for(const s of t)s.setAttribute("exponent",e.value)}if(e.getAttribute("data-modifier")==="gamma-amplitude"){const t=a.querySelectorAll("[amplitude]");for(const s of t)s.setAttribute("amplitude",e.value)}}duotoneEffect(a,e){const t=O=>O.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i,(ne,re,le,Ie)=>"#"+re+re+le+le+Ie+Ie).substring(1).match(/.{2}/g).map(ne=>parseInt(ne,16)),s=a.querySelector("feFuncR"),r=a.querySelector("feFuncG"),l=a.querySelector("feFuncB"),g=o(this,be,Xt).call(this,e),{color1:p,color2:v}=g,m=t(p),w=t(v),b=`${m[0]/255} ${w[0]/255}`,A=`${m[1]/255} ${w[1]/255}`,E=`${m[2]/255} ${w[2]/255}`;s.setAttribute("tableValues",b),r.setAttribute("tableValues",A),l.setAttribute("tableValues",E)}dropShadowEffect(a,e,t,s){const r=o(this,be,Xt).call(this,s),l=r["drop-shadow-x-offset"],g=r["drop-shadow-y-offset"],p=r["drop-shadow-blur-radius"],v=r["drop-shadow-color"],m=t.replace("length-1",`${l}px`).replace("length-2",`${g}px`).replace("length-3",`${p}px`).replace("color",v);n(this,ue).push(m)}addFilterEventListeners(){const{filterContainer:a}=this.elements;a.addEventListener("click",e=>{const t=e.target;t.tagName==="INPUT"&&t.type==="checkbox"&&o(this,be,Zt).call(this,e)}),a.addEventListener("input",e=>{const t=e.target;t.tagName==="INPUT"&&t.type!=="checkbox"&&o(this,be,Zt).call(this,e)}),a.addEventListener("contextmenu",e=>{e.preventDefault()})}reset(){const a=this.shadowRoot.querySelectorAll("[data-default]"),e=this.shadowRoot.querySelectorAll('#filters [type="checkbox"]'),{filterContainer:t}=this.elements;for(const s of a)s.value=s.dataset.default;for(const s of e)s.checked=!1;this.canvasImageFilter="none",t.classList.remove("show")}}dt=new WeakMap,ue=new WeakMap,be=new WeakSet,Zt=function(a){d(this,ue,[]);const e=a.target,{filterContainer:t}=this.elements;t.querySelectorAll("span.container--filter").forEach(r=>{const l=r.querySelector('[type="checkbox"]'),{filterType:g,filterString:p}=r.dataset,v=r.querySelectorAll("[data-modifier]");for(const m of v)m!==e&&m.getAttribute("data-modifier")===e.getAttribute("data-modifier")&&(m.value=e.value);if(l.checked)switch(g){case"drop-shadow":this.dropShadowEffect(r,e,p,v);break;case"percentage":this.singleModifierEffect(p,v,"%");break;case"angle":this.singleModifierEffect(p,v,"deg");break;case"length":this.singleModifierEffect(p,v,"px");break;case"svg":this.svgEffect(r,e,p,v);break;default:this.logger.log(`could not find ${g}`);break}}),this.canvasImageFilter=n(this,ue).join(" "),this.logger.log("applying filters:",this.canvasImageFilter),n(this,dt).updateFilter(this.canvasImageFilter)},Xt=function(a){const e={};for(const t of a)e[t.getAttribute("data-modifier")]=t.value;return e};const F={flipped:!1,flopped:!1,flipXAxisDirection:null,flipYAxisDirection:null,flipXOrigin:null,flipYOrigin:null},f={imageFilter:"none",imageWidth:null,imageHeight:null,imageXOrigin:null,imageYOrigin:null,imageDrawStart:new S(0,0),drawRatio:null,drawWidth:null,drawHeight:null},I={CSSWidth:null,CSSHeight:null,CSSScaleRatio:null},z={canvasImage:null,canvasDraw:null,ctxImage:null,ctxDraw:null},H={ratio:1,percentage:null};class ds{constructor(a,e){y(this,"fps",null);y(this,"delay",null);y(this,"time",null);y(this,"frameCount",-1);y(this,"rafReference",null);y(this,"isPlaying",!1);y(this,"animationCallback",()=>{});if(!a||!e)throw new Error("Must provide FPS and animationCallback");this.fps=a,this.delay=1e3/this.fps,this.animationCallback=e}loop(a){this.time===null&&(this.time=a);const e=Math.floor((a-this.time)/this.delay);e>this.frameCount&&(this.frameCount=e,this.animationCallback({time:a,frameCount:this.frameCount})),this.rafReference=requestAnimationFrame(this.loop.bind(this))}start(){this.isPlaying||(this.isPlaying=!0,this.rafReference=requestAnimationFrame(this.loop.bind(this)))}pause(){this.isPlaying&&(this.isPlaying=!1,this.time=null,this.frameCount=-1,cancelAnimationFrame(this.rafReference))}}var ie,ae,se,oe,ge,Ue,ft;const _t=class _t{constructor(a,e,t,s,r){u(this,Ue);u(this,ie);u(this,ae);u(this,se);u(this,oe);u(this,ge,{});this.set(a,e,t,s,r)}pointIsInsideArea(a){return a.x>this.x&&a.x<this.x+this.w&&a.y>this.y&&a.y<this.y+this.h}scale(a){return new _t(this.x*a,this.y*a,this.w*a,this.h*a,n(this,ge))}set(a,e,t,s,r){d(this,ie,a),d(this,ae,e),d(this,se,t),d(this,oe,s),r&&typeof r=="object"&&d(this,ge,r),o(this,Ue,ft).call(this)}get x(){return n(this,ie)}get y(){return n(this,ae)}get w(){return n(this,se)}get h(){return n(this,oe)}get top(){return n(this,ae)}get right(){return n(this,ie)+n(this,se)}get bottom(){return n(this,ae)+n(this,oe)}get left(){return n(this,ie)}get aspectRatio(){return n(this,se)/n(this,oe)}getOption(a){return n(this,ge)[a]}set x(a){d(this,ie,a)}set y(a){d(this,ae,a)}set w(a){d(this,se,a),o(this,Ue,ft).call(this)}set h(a){d(this,oe,a),o(this,Ue,ft).call(this)}get xHalfway(){return this.x+(this.right-this.left)/2}get yHalfway(){return this.y+(this.bottom-this.top)/2}setOption(a,e){return n(this,ge)[a]=e,e}get cloned(){return new _t(n(this,ie),n(this,ae),n(this,se),n(this,oe),n(this,ge))}};ie=new WeakMap,ae=new WeakMap,se=new WeakMap,oe=new WeakMap,ge=new WeakMap,Ue=new WeakSet,ft=function(){this.w<0&&(this.w=Math.abs(this.w),this.x=this.x-this.w),this.h<0&&(this.h=Math.abs(this.h),this.y=this.y-this.h)};let Yt=_t;var Ae,ze,ke,Re,Ce,Ee,Ne;const zi=class zi extends Yt{constructor(e=null,t=null,s=null,r=null,l=null,g=!1,p=null,v=null,m=null,w=null,b=null){super(e,t,s,r,b);u(this,Ae);u(this,ze);u(this,ke);u(this,Re);u(this,Ce);u(this,Ee);u(this,Ne);d(this,Ae,l),d(this,ze,g),d(this,ke,p),d(this,Re,v),d(this,Ce,m),d(this,Ee,w),d(this,Ne,!1)}get name(){return n(this,Ae)}set name(e){d(this,Ae,e)}get over(){return n(this,ze)}set over(e){d(this,ze,e)}get type(){return n(this,ke)}set type(e){d(this,ke,e)}get cursor(){return n(this,Re)}set cursor(e){d(this,Re,e)}get mode(){return n(this,Ce)}set mode(e){d(this,Ce,e)}get action(){return n(this,Ee)}set action(e){d(this,Ee,e)}get active(){return n(this,Ne)}set active(e){d(this,Ne,e)}get cloned(){return new zi(this.x,this.y,this.w,this.h,n(this,Ae),n(this,ze),n(this,ke),n(this,Re),n(this,Ce),n(this,Ee))}};Ae=new WeakMap,ze=new WeakMap,ke=new WeakMap,Re=new WeakMap,Ce=new WeakMap,Ee=new WeakMap,Ne=new WeakMap;let U=zi;function hs(){return{grab:new U(0,0,0,0,"grab",!1,"selection","grabbing","grab","grab"),nw:new U(0,0,0,0,"nw",!1,"corner","nwse-resize","resize","nw-resize"),n:new U(0,0,0,0,"n",!1,"edge","ns-resize","resize","n-resize"),ne:new U(0,0,0,0,"ne",!1,"corner","nesw-resize","resize","ne-resize"),e:new U(0,0,0,0,"e",!1,"edge","ew-resize","resize","e-resize"),se:new U(0,0,0,0,"se",!1,"corner","nwse-resize","resize","se-resize"),s:new U(0,0,0,0,"s",!1,"edge","ns-resize","resize","s-resize"),sw:new U(0,0,0,0,"sw",!1,"corner","nesw-resize","resize","sw-resize"),w:new U(0,0,0,0,"w",!1,"edge","ew-resize","resize","w-resize")}}const h={mode:null,action:"",startPointerOver:null,valid:!0,handleAreas:hs(),pointerStart:new S(0,0),pointerCurrent:new S(0,0),area:new $t(0,0,0,0),areaScaled:new $t(0,0,0,0),wasTouchEvent:!1,aspectRatioLocked:!1,lineDashOffset:0},M={handleCornerSize:null,handleEdgeSize:null,handleEdgeMargin:null,crossLineSize:null,selectionLineDashSize:null},V={show:!1,gap:null,lines:[]},R={naturalWidth:null,naturalHeight:null,aspectRatio:null,orientation:null},_={angle:0},Je={};function Tt(c,a=2,e=!1){if(typeof c!="number"||isNaN(c)||c===0)return"0 Bytes";const t=c<0;c=Math.abs(c);const s=1024,r=a<0?0:a,l=["Bytes","KB","MB","GB","TB","PB"],g=Math.floor(Math.log(c)/Math.log(s)),p=parseFloat((c/Math.pow(s,g)).toFixed(r));return`${e?t?"−":"+":""}${p} ${l[g]}`}function j(c,a){c&&(a?c.style.removeProperty("display"):c.style.display="none")}function us(c,a,e){c&&c.classList.toggle(a,e)}function gs(c){const{elements:a}=c;j(a.imageProperties,c.imagePropertiesEnabled),j(a.fileFormat,c.fileFormatEnabled),j(a.rotation,c.rotationEnabled),j(a.mirroring,c.mirroringEnabled),us(a.main,"show-button-labels",c.buttonLabelsEnabled),j(a.selecting,c.selectingEnabled),j(a.cropping,c.croppingEnabled&&c.selectingEnabled),j(a.toggleGrid,c.gridEnabled),j(a.download,c.downloadingEnabled),j(a.resize,c.resizingEnabled),j(a.freeRotation,c.freeRotationEnabled),j(a.filters,!c.filtersDisabled)}var Z,qe,Ge,Fe,Ze,Le,pe,T,X,Xe,_e,i,Pi,Vi,Kt,ji,mt,Ui,Ni,Jt,qi,Gi,Qt,Qe,me,Zi,D,Xi,K,Yi,Ki,Ji,ei,ti,vt,ii,wt,ai,Qi,ea,ta,ia,si,aa,oi,ni,ri,xt,sa,yt,St,oa,na,ra,la,ca,da,ha,ua,ga,li,ci,di,pa,fa,ht,hi,ut,Mt,It,Ht,ba,ui,At,ma,zt,va,kt,gi,wa,xa,ya,pi,Sa,Aa,fi,bi,et,za,ka,Ra,Ca,Ea,mi,vi,Fa,Rt,He,La,tt,_a,Ma,wi,xi,Ia,Ct,Ha,Da,Ba;class bt extends ls{constructor(){super();u(this,i);u(this,Z,null);u(this,qe,0);u(this,Ge,null);u(this,Fe,null);u(this,Ze);u(this,Le);u(this,pe);u(this,T);u(this,X,null);u(this,Xe,null);u(this,_e,"fit");u(this,ht,{cancel:()=>{G.fire("onCloseImageEditor"),this.dispatchEvent("onCloseImageEditor",{})},clearSelection:()=>{this.clearSelection()},crop:()=>{this.crop().catch(console.error)},dialogHelpClose:()=>{const{dialogHelp:e}=this.elements;e.close()},download:()=>{this.download()},editHelp:()=>{const{dialogHelp:e}=this.elements;e.showModal()},flip:()=>{this.flip()},flop:()=>{this.flop()},reset:()=>{this.reset()},resizeAspectRatioLock:()=>{o(this,i,Fa).call(this)},rotateCcw:()=>{this.rotate("ccw")},rotateCw:()=>{this.rotate("cw")},save:()=>{this.save()},selectionAspectRatioLock:()=>{o(this,i,vi).call(this)},toggleGrid:()=>{this.toggleGrid()},zoomIn:()=>{this.zoomIn()},zoomOut:()=>{this.zoomOut()},zoomFit:()=>{this.zoomFit()},zoomActualSize:()=>{this.zoomActualSize()}});u(this,ut,Ri((e,t)=>{o(this,i,Yi).call(this,e,t).catch(console.error)},this));u(this,Mt,Ri(()=>{o(this,i,He).call(this)},this));u(this,It,{"+":()=>this.zoomIn(),"-":()=>this.zoomOut(),"=":()=>this.zoomIn(),L:()=>this.rotate("ccw"),R:()=>this.rotate("cw")});u(this,Ht,e=>{const t=n(this,It)[e.key];t&&(e.preventDefault(),t(e))});this.setup(),o(this,i,Pi).call(this),this.enableControls(!1),this.enableFilters(this.shouldEnableFilters()),d(this,Ge,new cs(this)),this.determineFileFormatSupport(),o(this,i,ba).call(this),this.selectionBoxAnimator=new ds(this.config.animateFPS,this.animateSelection.bind(this)),this.dispatchEvent("imageEditorReady",{instance:this})}static get observedAttributes(){return["disabled"]}setup(){o(this,i,zt).call(this,this.config.defaultZoomMode)}getImageFileConfiguration(){return{formatsRegex:/.png|.jpg|.jpeg|.webp/,aspectRatioTolerance:.01,minWidth:100,maxWidth:3500,minHeight:100,maxHeight:3500,maxUploadFileSize:3e5,maxEditFileSize:3e5}}connectedCallback(){super.connectedCallback(),this.logger.log("ImageEditor connectedCallback")}disconnectedCallback(){super.disconnectedCallback(),this.logger.log("imageEditor disconnectedCallback")}enableControls(e){const{menuFieldset:t,canvasesButtons:s}=this.elements;e?(t.removeAttribute("disabled"),s.removeAttribute("disabled")):(t.setAttribute("disabled","disabled"),s.setAttribute("disabled","disabled"))}animateSelection(){h.lineDashOffset++,h.lineDashOffset>M.selectionLineDashSize*2&&(h.lineDashOffset=0),o(this,i,wt).call(this)}download(){if(o(this,i,At).call(this)){const e=this.getFileFormatSelectValue(),t=z.canvasImage.toDataURL(e),s=o(this,i,fi).call(this,n(this,T).name,e);Va(t,s)}}rotate(e){this.clearSelection(!1),e==="ccw"?(_.angle-=this.rotateDegreesStep,_.angle<0&&(_.angle=360-Math.abs(_.angle))):e==="cw"&&(_.angle+=this.rotateDegreesStep,_.angle>360&&(_.angle=_.angle-360)),o(this,i,wi).call(this),o(this,i,xi).call(this),o(this,i,D).call(this)}flip(){F.flipped=!F.flipped,this.clearSelection(!1),o(this,i,D).call(this)}flop(){F.flopped=!F.flopped,this.clearSelection(!1),o(this,i,D).call(this)}zoom(e){const{zoomPercentageMin:t,zoomPercentageMax:s,zoomPercentageStep:r}=this.config;let l=o(this,i,xa).call(this);e>0?l<=s-r&&(l+=r):l>=t+r&&(l-=r),H.ratio=o(this,i,gi).call(this,l),o(this,i,D).call(this)}zoomIn(){this.zoom(1)}zoomOut(){this.zoom(-1)}zoomFit(){o(this,i,zt).call(this,"fit"),H.ratio=1,o(this,i,D).call(this)}zoomActualSize(){o(this,i,zt).call(this,"actual-size"),o(this,i,kt).call(this),o(this,i,D).call(this)}reset(){this.resetProperties(),o(this,i,et).call(this,n(this,Ze)).catch(console.error)}toggleGrid(){V.show=!V.show,o(this,i,K).call(this)}async crop(){if(!o(this,i,Ct).call(this)){o(this,i,me).call(this,this.translator._("Make selection before cropping"));return}if(!h.valid){o(this,i,me).call(this,this.translator._("Invalid crop siz"));return}const e=this.getSelectionAsDataUrl();try{const t=await Pt(e,n(this,T).name),s=new Ke(this.getImageFileConfiguration());await s.load(n(this,pe),t,null,n(this,T).name),o(this,i,et).call(this,s,()=>{o(this,i,He).call(this)}).catch(console.error)}catch{this.logger.log("Something went wrong during processing of cropped ImageFile")}this.resetProperties()}getSelectionAsDataUrl(){const e=document.createElement("canvas"),t=e.getContext("2d");t.imageSmoothingEnabled=!1;const{x:s,y:r,w:l,h:g}=h.area;return e.width=l,e.height=g,t.drawImage(z.canvasImage,s,r,l,g,0,0,l,g),e.toDataURL("image/png",1)}clearSelection(e=!0){h.area.set(0,0,0,0),h.areaScaled.set(0,0,0,0);const t=h.handleAreas;Object.keys(t).forEach(s=>{t[s].set(0,0,0,0)}),o(this,i,pi).call(this),o(this,i,Qi).call(this),e&&o(this,i,K).call(this)}save(){z.canvasImage.toBlob(e=>{const{fileFormatSelect:t}=this.elements,s=t.value;if(o(this,i,At).call(this)){const r=o(this,i,fi).call(this,n(this,T).name,s),l=new File([e],r,{type:s});G.fire("onImageSave",{id:n(this,pe),file:l}),this.dispatchEvent("onImageSave",{id:n(this,pe),file:l})}else console.log("requirements not met")},this.getFileFormatSelectValue())}setImageAsImageFile(e,t){if(!e||!(t instanceof Ke))throw new Error("setImageAsImageFile(imageFile). Not all arguments passed / valid.");d(this,Ze,t),d(this,pe,e);const s=r=>{d(this,Fe,r),this.enableControls(!0),this.resetProperties(),o(this,i,Vi).call(this),o(this,i,za).call(this),o(this,i,Ca).call(this),o(this,i,ka).call(this),o(this,i,Ra).call(this,t.mimeType),o(this,i,La).call(this),o(this,i,He).call(this),o(this,i,Ea).call(this),o(this,i,mi).call(this),o(this,i,Ji).call(this),gs(this)};o(this,i,et).call(this,t,s).catch(console.error)}async setImageAsImageSource(e){if(!(e instanceof Ci))throw new Error("setImageAsImageSource(imageSource), Not all arguments passed / valid.");const t=new Ke(this.getImageFileConfiguration());try{await t.load(e.id,null,e.src,e.name),this.setImageAsImageFile(e.id,t)}catch{console.warn("setImageAsImageSource: Could not create ImageFile")}}setImage(e,t,s){const r=new Ci(e,t,s);this.setImageAsImageSource(r).catch(console.error)}updateResizeAspectRatioLockState(){const{resizeAspectRatioLock:e}=this.elements;Je.aspectRatioLocked?e.classList.add("locked"):e.classList.remove("locked")}resetProperties(){F.flipped=!1,F.flopped=!1,_.angle=0,n(this,_e)==="actual-size"?o(this,i,kt).call(this):H.ratio=1,V.show=!1,o(this,i,Ia).call(this),o(this,i,Ba).call(this),o(this,i,tt).call(this),o(this,i,Ma).call(this),o(this,i,Da).call(this),this.clearSelection(!1),o(this,i,D).call(this)}get freeSelectDisabled(){return this.getAttributeAsBooleanDefaultWhenFalse("free-select-disabled",this.config.freeSelectDisabled)}set freeSelectDisabled(e){this.setAttributeAsBoolean("free-select-disabled",e)}get imagePropertiesEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("image-properties",this.config.imagePropertiesEnabled)}set imagePropertiesEnabled(e){this.setAttributeAsBoolean("image-properties",e)}get fileFormatEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("file-format",this.config.fileFormatEnabled)}set fileFormatEnabled(e){this.setAttributeAsBoolean("file-format",e)}get rotationEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("rotation",this.config.rotationEnabled)}set rotationEnabled(e){this.setAttributeAsBoolean("rotation",e)}get mirroringEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("mirroring",this.config.mirroringEnabled)}set mirroringEnabled(e){this.setAttributeAsBoolean("mirroring",e)}get buttonLabelsEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("button-labels",this.config.buttonLabelsEnabled)}set buttonLabelsEnabled(e){this.setAttributeAsBoolean("button-labels",e)}get selectingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("selecting",this.config.selectingEnabled)}set selectingEnabled(e){this.setAttributeAsBoolean("selecting",e)}get croppingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("cropping",this.config.croppingEnabled)}set croppingEnabled(e){this.setAttributeAsBoolean("cropping",e)}get gridEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("grid-enabled",this.config.gridEnabled)}set gridEnabled(e){this.setAttributeAsBoolean("grid-enabled",e)}get downloadingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("downloading",this.config.downloadingEnabled)}set downloadingEnabled(e){this.setAttributeAsBoolean("downloading",e)}get resizingEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("resizing",this.config.resizingEnabled)}set resizingEnabled(e){this.setAttributeAsBoolean("resizing",e)}get freeRotationEnabled(){return this.getAttributeAsBooleanDefaultWhenFalse("free-rotation",this.config.freeRotationEnabled)}set freeRotationEnabled(e){this.setAttributeAsBoolean("free-rotation",e)}get filtersDisabled(){return this.getAttributeAsBooleanDefaultWhenFalse("filters-disabled",this.config.filtersDisabled)}set filtersDisabled(e){this.setAttributeAsBoolean("filters-disabled",e)}get minWidth(){return this.getAttributeAsInteger("min-width",this.config.minWidth)}set minWidth(e){this.setAttributeAsInteger("min-width",e)}get minHeight(){return this.getAttributeAsInteger("min-height",this.config.minHeight)}set minHeight(e){this.setAttributeAsInteger("min-height",e)}get maxWidth(){return this.getAttributeAsInteger("max-width",this.config.maxWidth)}set maxWidth(e){this.setAttributeAsInteger("max-width",e)}get maxHeight(){return this.getAttributeAsInteger("max-height",this.config.maxHeight)}set maxHeight(e){this.setAttributeAsInteger("max-height",e)}get selectionAspectRatios(){const e=this.config.selectionAspectRatios??this.config.defaultActiveAspectRatios;return this.getAttributeAsCSV("selection-aspect-ratios",e)}set selectionAspectRatios(e){this.setAttributeAsCSV("selection-aspect-ratios",e)}get selectionAspectRatio(){const e=this.getAttributeOrDefault("selection-aspect-ratio",this.config.selectionAspectRatio);return this.selectionAspectRatios.includes(e)||console.warn(`ImageEditor: ${e} not in list with selectionAspectRatios ${this.selectionAspectRatios.join(", ")}`),e}set selectionAspectRatio(e){this.setAttributeToString("selection-aspect-ratio",e),o(this,i,tt).call(this)}get fileFormats(){return this.getAttributeAsCSV("file-formats",this.config.fileFormats)}set fileFormats(e){return this.setAttributeAsCSV("file-formats",e)}get rotateDegreesStep(){return this.getAttributeAsInteger("rotate-degrees-step",this.config.rotateDegreesStep)}set rotateDegreesStep(e){this.setAttributeAsInteger("rotate-degrees-step",e)}enableFilters(e){this.elements.filters.style.display=e?"block":"none"}shouldEnableFilters(){return o(this,i,Ha).call(this)&&!this.filtersDisabled}updateFilter(e){f.imageFilter=e,o(this,i,D).call(this)}determineFileFormatSupport(){for(const e of Ot)e.supported=o(this,i,ji).call(this,e.value)}getFileFormatSelectValue(){const{fileFormatSelect:e}=this.elements;return e.value}}Z=new WeakMap,qe=new WeakMap,Ge=new WeakMap,Fe=new WeakMap,Ze=new WeakMap,Le=new WeakMap,pe=new WeakMap,T=new WeakMap,X=new WeakMap,Xe=new WeakMap,_e=new WeakMap,i=new WeakSet,Pi=function(){const{canvasesWrapper:e}=this.elements;Wt(e),["image","draw"].forEach(t=>{const{canvasesWrapper:s}=this.elements,r=Ua(t),l=`canvas${r}`,g=`ctx${r}`,p=document.createElement("canvas");p.id=`canvas${r}`,p.className=`canvas--${t}`,t==="image"&&(p.innerText=this.translator._("Image editor canvas")),s.appendChild(p),z[l]=p,z[g]=p.getContext("2d")})},Vi=function(){const e=new ResizeObserver(()=>{o(this,i,D).call(this)}),{editorCanvases:t}=this.elements;e.observe(t)},Kt=function(e){const t=z.canvasImage.toDataURL(e),s=`data:${e};base64,`;return Math.round((t.length-s.length)*3/4)},ji=function(e){const t=document.createElement("canvas");t.width=t.height=1;const s=t.toDataURL(e);return s==null?void 0:s.includes(`data:${e};base64,`)},mt=function(e,t){if(isNaN(e)||isNaN(t)||t===0)return"-";const s=this.config.aspectRatioTolerance,r=e/t;let g=`${r.toFixed(2)}:1`;for(const p of Object.values(ce))r>p.value-s&&r<p.value+s&&(g+=` (${p.label})`);return g},Ui=function(e,t,s){const r={},l=s*Math.PI/180,g=Math.abs(Math.cos(l)),p=Math.abs(Math.sin(l));return r.width=Math.round(t*p+e*g),r.height=Math.round(t*g+e*p),r},Ni=function(){if(h.mode==="select")return;const{editorCanvases:e}=this.elements;this.editorCanvasesWidth=e.offsetWidth,this.editorCanvasesHeight=e.offsetHeight,R.naturalWidth=n(this,Le).naturalWidth,R.naturalHeight=n(this,Le).naturalHeight;const t=o(this,i,Ui).call(this,R.naturalWidth,R.naturalHeight,_.angle);f.imageWidth=t.width,f.imageHeight=t.height,f.drawRatio=this.config.drawCanvasWidth/f.imageWidth;const s=this.config.drawCanvasWidth/f.imageWidth;f.drawWidth=Math.round(f.imageWidth*s),f.drawHeight=Math.round(f.imageHeight*s),I.CSSScaleRatio=o(this,i,Gi).call(this);const r=o(this,i,qi).call(this);I.CSSWidth=r.width,I.CSSHeight=r.height,f.imageXOrigin=f.imageWidth/2,f.imageYOrigin=f.imageHeight/2,f.imageDrawStart.x=f.imageWidth/2-R.naturalWidth/2,f.imageDrawStart.y=f.imageHeight/2-R.naturalHeight/2,F.flipXAxisDirection=F.flipped?-1:1,F.flipYAxisDirection=F.flopped?-1:1,F.flipXOrigin=F.flipped?f.imageWidth:0,F.flipYOrigin=F.flopped?f.imageHeight:0,R.aspectRatio=o(this,i,mt).call(this,R.naturalWidth,R.naturalHeight),R.orientation=f.imageWidth>f.imageHeight?"Landscape":f.imageHeight>f.imageWidth?"Portrait":"Square",V.gap=Math.round(R.naturalWidth/this.config.gridLineCount*f.drawRatio),this.lineWidth=o(this,i,Qt).call(this,this.config.lineWidth),M.selectionLineDashSize=o(this,i,Qt).call(this,this.config.selectionLineDashSize),M.crossLineSize=o(this,i,Qe).call(this,this.config.crossLineSize),M.handleCornerSize=o(this,i,Qe).call(this,this.config.handleCornerSize),M.handleEdgeSize=o(this,i,Qe).call(this,this.config.handleEdgeSize),M.handleEdgeMargin=o(this,i,Qe).call(this,this.config.handleEdgeMargin),window.matchMedia(`(max-width: ${this.config.touchHandleMultiplierBreakpoint})`).matches&&(this.logger.log("small viewport"),M.handleCornerSize*=this.config.touchHandleMultiplier,M.handleEdgeSize*=this.config.touchHandleMultiplier),o(this,i,Jt).call(this),o(this,i,ya).call(this)},Jt=function(){H.percentage=o(this,i,wa).call(this,H.ratio)},qi=function(){let e=Math.round(f.imageWidth*I.CSSScaleRatio),t=Math.round(f.imageHeight*I.CSSScaleRatio);return e>this.editorCanvasesWidth&&(e=this.editorCanvasesWidth),t>this.editorCanvasesHeight&&(t=this.editorCanvasesHeight),{width:e,height:t}},Gi=function(){const e=this.editorCanvasesWidth/f.imageWidth,t=this.editorCanvasesHeight/f.imageHeight;return Math.min(e,t)},Qt=function(e){return Math.ceil(e/H.ratio/I.CSSScaleRatio*f.drawRatio)},Qe=function(e){return Math.ceil(e/H.ratio/I.CSSScaleRatio)},me=function(e){G.fire("onCanvasStatusMessage",{message:e}),this.dispatchEvent("onCanvasStatusMessage",{message:e})},Zi=function(){const{canvasesWrapper:e}=this.elements,{canvasImage:t,canvasDraw:s}=z;[t.width,t.height]=[f.imageWidth,f.imageHeight],[s.width,s.height]=[f.drawWidth,f.drawHeight],e.style.width=t.style.width=s.style.width=I.CSSWidth*H.ratio+"px",e.style.height=t.style.height=s.style.height=I.CSSHeight*H.ratio+"px"},D=function(){n(this,T).loadStatus==="loaded"&&(o(this,i,Xi).call(this),o(this,i,K).call(this))},Xi=function(){o(this,i,Ni).call(this),o(this,i,Zi).call(this);const{canvasImage:e,ctxImage:t}=z,s=n(this,Le);t.clearRect(0,0,e.width,e.height),t.save(),t.imageSmoothingEnabled=!1,t.webkitImageSmoothingEnabled=!1,t.mozImageSmoothingEnabled=!1,t.translate(f.imageXOrigin,f.imageYOrigin),(F.flipped||F.flopped)&&t.scale(F.flipXAxisDirection,F.flipYAxisDirection),t.rotate(Math.PI/180*_.angle),t.translate(-f.imageXOrigin,-f.imageYOrigin),f.imageFilter&&(t.filter=f.imageFilter),t.drawImage(s,f.imageDrawStart.x,f.imageDrawStart.y,s.naturalWidth,s.naturalHeight),t.restore()},K=function(){const{ctxDraw:e,canvasDraw:t}=z;e.clearRect(0,0,t.width,t.height),V.show&&o(this,i,ia).call(this),!(h.area.w===0&&h.area.h===0)&&(h.mode==="select"?(o(this,i,wt).call(this),o(this,i,ai).call(this)):o(this,i,Ct).call(this)&&(o(this,i,wt).call(this),o(this,i,ai).call(this),o(this,i,Ki).call(this),o(this,i,ta).call(this)))},Yi=async function(e,t){if(!o(this,i,At).call(this))return;const{fileFormatSelect:s}=this.elements;s.value||o(this,i,me).call(this,this.translator._("Select a file format first"));const r=s.value,l=document.createElement("canvas"),g=l.getContext("2d");l.width=e,l.height=t,g.drawImage(n(this,Fe),0,0,l.width,l.height);try{const p=await Pt(l.toDataURL(r,1),n(this,T).name),v=new Ke(this.getImageFileConfiguration());await v.load(n(this,pe),p,null,n(this,T).name),o(this,i,et).call(this,v,()=>{o(this,i,He).call(this),n(this,_e)==="actual-size"&&o(this,i,kt).call(this)}).catch(console.error)}catch(p){console.warn(`Error during resizing of image ${p}`)}},Ki=function(){const e=M.handleCornerSize,t=M.handleEdgeSize,s=M.handleEdgeMargin,{x:r,y:l,w:g,h:p}=h.areaScaled,v=g/(2*e+s)>1&&p/(2*e+s)>1,m=new S(r,l),w=new S(r+g-e,l),b=new S(r+g-e,l+p-e),A=new S(r,l+p-e),E=new S(r+e+s,l),O=new S(r+g-t,l+e+s),ne=new S(r+e+s,l+p-t),re=new S(r,l+e+s);let le=g-2*s-2*e,Ie=t,Dt=t,Ye=p-2*s-2*e;v||(m.x-=e,m.y-=e,w.x+=e,w.y-=e,b.x+=e,b.y+=e,A.x-=e,A.y+=e,E.x=r,E.y=l-t,O.x=r+g,O.y=l,ne.x=r,ne.y=l+p,re.x=r-t,re.y=l,le=g,Ye=p),le<50&&(le=0,Ie=0,E.set(r,l),ne.set(r,l)),Ye<50&&(Dt=0,Ye=0,O.set(r,l),re.set(r,l));const Y=h.handleAreas;Y.grab.set(r,l,g,p),Y.nw.set(m.x,m.y,e,e),Y.n.set(E.x,E.y,le,Ie),Y.ne.set(w.x,w.y,e,e),Y.e.set(O.x,O.y,Dt,Ye),Y.se.set(b.x,b.y,e,e),Y.s.set(ne.x,ne.y,le,Ie),Y.sw.set(A.x,A.y,e,e),Y.w.set(re.x,re.y,Dt,Ye)},Ji=function(){const e=f.drawWidth,t=f.drawHeight,s=Math.round(V.gap/5);for(let r=0;r<e;r=r+s)V.lines.push({from:new S(r,0),to:new S(r,e),isGridLine:r%V.gap===0});for(let r=0;r<t;r=r+s)V.lines.push({from:new S(0,r),to:new S(e,r),isGridLine:r%V.gap===0})},ei=function(e){const t=this.config.snapThresholdPercentage,s=t*f.imageWidth,r=t*f.imageHeight;return e.x<s&&(e.x=0),e.y<r&&(e.y=0),e.right>f.imageWidth-s&&(e.x=f.imageWidth-e.w),e.bottom>f.imageHeight-r&&(e.y=f.imageHeight-e.h),e},ti=function(e){const t=f.imageWidth,s=f.imageHeight;return e.x<0||e.x>t||e.w>t||e.right>t||e.y<0||e.y>s||e.h>s||e.bottom>s},vt=function(e){h.area.set(e.x,e.y,e.w,e.h),h.areaScaled=h.area.scale(f.drawRatio),o(this,i,pi).call(this)},ii=function(e){const{w:t,h:s}=e;h.valid=t>=this.minWidth&&t<=this.maxWidth&&s>=this.minHeight&&s<=this.maxHeight},wt=function(){const{ctxDraw:e}=z;e.save();const{x:t,y:s,h:r,w:l}=h.areaScaled;e.lineWidth=this.lineWidth,e.strokeStyle=this.config.selectionBoxStrokeStyle,e.beginPath(),e.rect(t,s,l,r),e.stroke(),e.setLineDash([M.selectionLineDashSize]),e.lineDashOffset=-h.lineDashOffset,e.strokeStyle=h.valid?this.config.selectionBoxLineDashStrokeStyle:this.config.selectionBoxInvalidLineDashStrokeStyle,e.stroke(),e.restore()},ai=function(){this.config.animateSelection&&this.selectionBoxAnimator.start()},Qi=function(){this.selectionBoxAnimator.pause()},ea=function(){const{ctxDraw:e}=z;e.save(),e.strokeStyle=this.config.crossStrokeStyle,e.lineWidth=this.lineWidth;const{x:t,y:s,w:r,h:l}=h.area,g=new S(t+r/2,s+l/2),p=new S(g.x-M.crossLineSize/2,g.y).scale(f.drawRatio),v=new S(g.x+M.crossLineSize/2,g.y).scale(f.drawRatio),m=new S(g.x,g.y-M.crossLineSize/2).scale(f.drawRatio),w=new S(g.x,g.y+M.crossLineSize/2).scale(f.drawRatio);e.beginPath(),e.moveTo(p.x,p.y),e.lineTo(v.x,v.y),e.moveTo(m.x,m.y),e.lineTo(w.x,w.y),e.stroke(),e.restore()},ta=function(){const e=z.ctxDraw;e.save(),e.lineWidth=this.lineWidth,e.strokeStyle=this.config.selectionHandleStrokeStyle,e.fillStyle=this.config.selectionHandleOverFillStyle;for(const t of Object.values(h.handleAreas))h.mode==="resize"&&!t.active||(e.beginPath(),e.rect(t.x,t.y,t.w,t.h),t.over===!0&&e.fill(),(t.type==="corner"||t.over===!0||h.wasTouchEvent===!0)&&(e.stroke(),e.save(),e.strokeStyle=this.config.selectionHandleLineDashStrokeStyle,e.setLineDash([15,15]),e.stroke(),e.restore()));e.restore()},ia=function(){const e=z.ctxDraw;e.save(),e.lineWidth=this.lineWidth;for(const t of V.lines)e.beginPath(),e.moveTo(t.from.x,t.from.y),e.lineTo(t.to.x,t.to.y),t.isGridLine===!0?(e.strokeStyle=this.config.gridStrokeStyle,e.stroke()):this.config.showSubGrid&&(e.strokeStyle=this.config.subGridStrokeStyle,e.stroke());e.restore()},si=function(e,t){const{canvasesWrapper:s}=this.elements,r=s.getBoundingClientRect(),l=(e-r.left)/I.CSSScaleRatio/H.ratio,g=(t-r.top)/I.CSSScaleRatio/H.ratio;return new S(l,g)},aa=function(e,t){const s=o(this,i,si).call(this,e,t);h.pointerStart.set(s.x,s.y)},oi=function(e,t){const s=o(this,i,si).call(this,e,t);h.pointerCurrent.set(s.x,s.y)},ni=function(){const e={selectionHandle:!1,resizeHandle:!1},t=h.handleAreas;for(const s of Object.keys(t)){const r=t[s];if(r.pointIsInsideArea(h.pointerCurrent.scale(f.drawRatio))){r.mode==="grab"?(e.selectionHandle=r,e.resizeHandle=!1):r.mode==="resize"&&(e.resizeHandle=r,e.selectionHandle=!1,h.handleAreas[r.name].over=!0);const{canvasesWrapper:g}=this.elements;g.style.cursor=r.cursor}else h.handleAreas[r.name].over=!1}if(!e.selectionHandle&&!e.resizeHandle){const{canvasesWrapper:s}=this.elements;s.style.cursor="crosshair"}return e},ri=function(){const{w:e,h:t}=h.area;return{aspectRatio:e/t,aspectRatioLabel:o(this,i,mt).call(this,e,t)}},xt=function(){const{aspectRatioSelect:e}=this.elements,t=e.value;return o(this,i,Rt).call(this,t).value},sa=function(e){const t=z.ctxImage;t.save(),t.fillStyle="orange",t.fillRect(e.x-10,e.y-10,20,20),t.restore()},yt=function(e){const t=o(this,i,xt).call(this);return t>-1&&(e.w=e.h*t),e},St=function(e){const t=o(this,i,xt).call(this);return t>-1&&(e.h=e.w/t),e},oa=function(){const e=o(this,i,ni).call(this);return e.resizeHandle?(h.mode="resize",h.action=e.resizeHandle.action,h.startPointerOver=e,e.resizeHandle.active=!0):e.selectionHandle?(h.mode="grab",h.action=e.selectionHandle.action,h.startPointerOver=e):(h.mode="select",h.startPointerOver=null),h.mode},na=function(){this.clearSelection()},ra=function(e=!1){e?d(this,Z,1):n(this,Z)!==null&&d(this,Z,null);const t=o(this,i,pa).call(this);o(this,i,ii).call(this,t),o(this,i,vt).call(this,t),o(this,i,K).call(this)},la=function(){switch(d(this,X,h.area.cloned),d(this,Xe,new S(n(this,X).xHalfway,n(this,X).yHalfway)),h.action){case"nw-resize":case"w-resize":h.pointerStart.x=h.area.right,h.pointerStart.y=h.area.bottom;break;case"ne-resize":case"n-resize":h.pointerStart.x=h.area.left,h.pointerStart.y=h.area.bottom;break;case"se-resize":case"e-resize":h.pointerStart.x=h.area.left,h.pointerStart.y=h.area.top;break;case"sw-resize":case"s-resize":h.pointerStart.x=h.area.right,h.pointerStart.y=h.area.top;break}},ca=function(e=!1){e?n(this,Z)===null&&d(this,Z,o(this,i,ri).call(this).aspectRatio):n(this,Z)!==null&&d(this,Z,null);const t=o(this,i,fa).call(this);o(this,i,ti).call(this,t)||(o(this,i,ii).call(this,t),o(this,i,vt).call(this,t),o(this,i,K).call(this))},da=function(){d(this,X,h.area.cloned)},ha=function(){const e=n(this,X),t=e.cloned;t.x=e.x+h.pointerCurrent.x-h.pointerStart.x,t.y=e.y+h.pointerCurrent.y-h.pointerStart.y,o(this,i,ti).call(this,t)||(o(this,i,vt).call(this,t),o(this,i,K).call(this),o(this,i,ea).call(this))},ua=function(e){if(h.mode==="select")return;const t=e.clientX||e.touches[0].clientX,s=e.clientY||e.touches[0].clientY;o(this,i,aa).call(this,t,s),e.pointerType==="touch"?(h.wasTouchEvent=!0,o(this,i,oi).call(this,t,s)):h.wasTouchEvent=!1;const r=o(this,i,oa).call(this);r==="select"?o(this,i,na).call(this):r==="resize"?o(this,i,la).call(this):r==="grab"&&o(this,i,da).call(this)},ga=function(e){const t=e.clientX||e.touches[0].clientX,s=e.clientY||e.touches[0].clientY;o(this,i,oi).call(this,t,s),o(this,i,ni).call(this);const r=h.mode;r==="select"?o(this,i,ra).call(this,e.shiftKey):r==="resize"?o(this,i,ca).call(this,e.shiftKey):r==="grab"?o(this,i,ha).call(this):o(this,i,K).call(this)},li=function(){h.mode=null;const e=h.handleAreas;for(const t of Object.keys(e)){const s=e[t];s.active=!1}d(this,X,null),o(this,i,K).call(this)},ci=function(e){const t=z.canvasImage,s=z.ctxImage;s.save(),s.clearRect(0,0,t.width,t.height),s.fillRect(h.pointerStart.x-10,h.pointerStart.y-10,20,20),s.strokeStyle="blue",s.strokeRect(e.x,e.y,e.w,e.h),s.restore()},di=function(){const e=h.pointerStart;o(this,i,sa).call(this,e)},pa=function(){const e=h.pointerStart.x,t=h.pointerStart.y,s=h.pointerCurrent.x-h.pointerStart.x,r=h.pointerCurrent.y-h.pointerStart.y;let l=new $t(e,t,s,r);return this.debug&&o(this,i,di).call(this),l=o(this,i,yt).call(this,l),l=o(this,i,ei).call(this,l),this.debug&&o(this,i,ci).call(this,l),l},fa=function(){let e=n(this,X);const t=h.action,s=o(this,i,xt).call(this);this.debug&&o(this,i,di).call(this);const r=["e-resize","w-resize"],l=["n-resize","s-resize"];if(s>-1?(l.push("ne-resize"),r.push("nw-resize","se-resize","sw-resize")):(r.push("nw-resize","ne-resize","se-resize","sw-resize"),l.push("nw-resize","ne-resize","se-resize","sw-resize")),r.includes(t)&&(e.x=h.pointerStart.x,e.w=h.pointerCurrent.x-h.pointerStart.x),l.includes(t)&&(e.y=h.pointerStart.y,e.h=h.pointerCurrent.y-h.pointerStart.y),s>-1){if((t==="n-resize"||t==="s-resize")&&(e.x=n(this,Xe).x-e.w/2,e=o(this,i,yt).call(this,e)),(t==="w-resize"||t==="e-resize")&&(e.y=n(this,Xe).y-e.h/2,e=o(this,i,St).call(this,e)),t==="ne-resize"&&(e=o(this,i,yt).call(this,e)),t==="nw-resize"){e=o(this,i,St).call(this,e);const g=h.pointerStart;e.y=g.y-e.h}(t==="sw-resize"||t==="se-resize")&&(e=o(this,i,St).call(this,e))}return e=o(this,i,ei).call(this,e),this.debug&&o(this,i,ci).call(this,e),e},ht=new WeakMap,hi=function(e,t=1,s=999,r=!1,l){const g=r?parseFloat(e):parseInt(e),p=Math.min(Math.max(g,t),s);return p!==g&&l(p),p},ut=new WeakMap,Mt=new WeakMap,It=new WeakMap,Ht=new WeakMap,ba=function(){const{canvasDraw:e}=z,{canvasesWrapper:t,fileFormatSelect:s}=this.elements,{freeRotationRange:r,freeRotationRangeValue:l}=this.elements,{resizeWidth:g,resizeHeight:p}=this.elements,{zoomPercentage:v}=this.elements,{showFilters:m,filterContainer:w}=this.elements;G.register("onCanvasStatusMessage",b=>{this.dispatchEvent("canvasStatusMessage",b.detail)}),G.register("onCloseImageEditor",b=>{this.dispatchEvent("closeImageEditor",b.detail)}),G.register("onImageSave",b=>{this.dispatchEvent("imageSave",b.detail)}),Object.entries(n(this,ht)).forEach(([b,A])=>{const E=this.shadowRoot.querySelector(`#${_i(b)}`);if(!E){console.error(`element with id #${b} not found, cannot add event listener`);return}E.addEventListener("click",O=>{O.stopPropagation(),O.preventDefault(),A(O)},!1)}),this.shadowRoot.addEventListener("click",b=>{const{dialogHelp:A}=this.elements;b.target===A&&n(this,ht).dialogHelpClose(b)}),this.shadowRoot.addEventListener("keydown",n(this,Ht),!1),v.addEventListener("input",()=>{const b=v.value,A=O=>{v.value=O},E=o(this,i,hi).call(this,b,1,1e3,!0,A);H.ratio=o(this,i,gi).call(this,E),o(this,i,D).call(this)}),s.addEventListener("change",()=>{o(this,i,He).call(this)}),r.addEventListener("input",b=>{b.preventDefault(),this.clearSelection(!1),_.angle=parseInt(b.target.value),o(this,i,xi).call(this),o(this,i,D).call(this)}),l.addEventListener("change",b=>{b.preventDefault(),this.clearSelection(!1),o(this,i,wi).call(this),_.angle=o(this,i,hi).call(this,b.target.value,0,360,!1,A=>{b.target.value=A}),o(this,i,D).call(this)}),g.addEventListener("input",b=>{b.preventDefault();const{resizeWidth:A,resizeHeight:E}=this.elements;this.clearSelection(!1),Je.aspectRatioLocked&&(E.value=Math.round(A.value/o(this,i,ui).call(this))),n(this,ut).call(this,A.value,E.value)}),p.addEventListener("input",b=>{b.preventDefault();const{resizeWidth:A,resizeHeight:E}=this.elements;this.clearSelection(!1),Je.aspectRatioLocked&&(A.value=Math.round(E.value*o(this,i,ui).call(this))),n(this,ut).call(this,A.value,E.value)}),r.addEventListener("contextmenu",b=>{b.preventDefault(),b.stopPropagation()}),m.addEventListener("click",b=>{w.classList.toggle("show",b.target.checked)}),n(this,Ge).addFilterEventListeners(),e.addEventListener("pointerdown",b=>{b.preventDefault(),o(this,i,ua).call(this,b)}),e.addEventListener("pointerup",b=>{b.preventDefault(),o(this,i,li).call(this)}),e.addEventListener("pointermove",b=>{b.preventDefault(),o(this,i,ga).call(this,b)}),e.addEventListener("pointerenter",b=>{b.preventDefault(),t.style.cursor="crosshair"}),e.addEventListener("pointerleave",b=>{b.preventDefault(),o(this,i,li).call(this),t.style.cursor="default"}),e.addEventListener("touchstart",b=>b.preventDefault()),e.addEventListener("contextmenu",b=>{b.preventDefault(),b.stopPropagation()},!0)},ui=function(){return n(this,Fe).width/n(this,Fe).height},At=function(){const{fileFormatSelect:e}=this.elements;return e.value?o(this,i,Sa).call(this)?!0:(e.focus(),o(this,i,me).call(this,this.translator._("Conversion to this file format not supported")),!1):(e.focus(),o(this,i,me).call(this,this.translator._("Select a file format first")),!1)},ma=function(){return R.naturalWidth/I.CSSWidth},zt=function(e){d(this,_e,e),o(this,i,va).call(this)},va=function(){this.shadowRoot.querySelectorAll("[data-zoom-mode]").forEach(t=>{const s=t.getAttribute("data-zoom-mode");t.classList.toggle("is-active",s===n(this,_e))})},kt=function(){H.ratio=o(this,i,ma).call(this),o(this,i,Jt).call(this)},gi=function(e){return e/100/I.CSSScaleRatio},wa=function(e){return(e*I.CSSScaleRatio*100).toFixed(0)},xa=function(){const{zoomPercentage:e}=this.elements;return parseFloat(e.value)},ya=function(){const{fileSizeLabel:e,fileSizeOriginalLabel:t,fileSizeAlteredLabel:s,fileSizeDifferenceLabel:r,resizeWidth:l,resizeHeight:g,zoomPercentage:p,imageAspectRatio:v,imageOrientation:m,imageOrientationIcon:w}=this.elements;l.value=R.naturalWidth,g.value=R.naturalHeight,p.value=H.percentage,v.innerText=R.aspectRatio,m.innerText=this.translator._(R.orientation),R.orientation==="Landscape"?(w.classList.add("image-orientation-icon-landscape"),w.classList.remove("image-orientation-icon-portrait"),w.classList.remove("image-orientation-icon-square")):R.orientation==="Portrait"?(w.classList.remove("image-orientation-icon-landscape"),w.classList.add("image-orientation-icon-portrait"),w.classList.remove("image-orientation-icon-square")):R.orientation==="Square"&&(w.classList.remove("image-orientation-icon-landscape"),w.classList.remove("image-orientation-icon-portrait"),w.classList.add("image-orientation-icon-square")),e.innerText=this.translator._("File size"),t.innerText=this.translator._("Original"),s.innerText=this.translator._("Altered"),r.innerText=this.translator._("Difference"),n(this,Mt).call(this)},pi=function(){const{imageSelectionSize:e,imageSelectionAspectRatio:t}=this.elements,{w:s,h:r}=h.area;e.innerText=Math.floor(s)+" x "+Math.floor(r),t.innerText=o(this,i,mt).call(this,s,r)},Sa=function(){const{fileFormatSelect:e}=this.elements;return!e.options[e.selectedIndex].hasAttribute("disabled")},Aa=function(e){const{fileFormatSelect:t}=this.elements;let s=!1;for(const r of t.options)r.value===e&&!r.hasAttribute("disabled")&&(s=!0);return s},fi=function(e,t){const s=t.split("/")[1];return`${e.replace(/\.[^/.]+$/,"")}.${s}`},bi=function(e){const{editorCanvases:t}=this.elements;e?t.classList.remove("canvases--image-loaded"):t.classList.add("canvases--image-loaded")},et=async function(e,t){if(e instanceof Ke){o(this,i,bi).call(this,!0),d(this,T,e);try{const s=await $a(e.imageObjectURL);d(this,Le,s),o(this,i,D).call(this),o(this,i,bi).call(this,!1),t==null||t(s)}catch(s){this.logger.log("error",s),console.error("error",s)}}},za=function(){const e=this.selectionAspectRatios;ce.forEach(r=>{r.active=e.includes(r.name)});const t=ce.find(r=>r.name==="free");t.active=!this.freeSelectDisabled;const s=ce.find(r=>r.name==="locked");s.active=!this.freeSelectDisabled},ka=function(){const e=this.fileFormats;for(const t of Ot)t.active=e.includes(t.value)},Ra=function(e){const{fileFormatSelect:t}=this.elements;Wt(t);const s=document.createElement("template");s.innerHTML='<option hidden value="" data-i18n="Choose file format"></option>',t.appendChild(s.content);for(const r of Ot){const l=document.createElement("template");l.innerHTML=`<option value="${r.value}" id="fileFormat_${r.name}" ${r.supported&&r.active?"":'data-i18n-attr="title" data-i18n="Conversion to this file format not supported" disabled'}>${r.label}</option>`,t.appendChild(l.content)}e&&o(this,i,Aa).call(this,e)?t.value=e:t.value=""},Ca=function(){const{aspectRatioSelect:e}=this.elements;Wt(e);for(const t of ce)if(t.active){const s=document.createElement("template");s.innerHTML=`<option value="${t.name}" id="ratio_${t.name}">${t.label}</option>`,e.appendChild(s.content)}o(this,i,tt).call(this)},Ea=function(){const{aspectRatioSelect:e,selectionAspectRatioLock:t}=this.elements;if(this.freeSelectDisabled||e.value!=="free"){t.setAttribute("disabled","disabled"),t.classList.remove("locked");return}t.removeAttribute("disabled"),t.classList.remove("locked")},mi=function(){const e=this.shadowRoot.querySelector('option[id="ratio_locked"]'),{aspectRatioSelect:t,selectionAspectRatioLock:s}=this.elements;if(!this.freeSelectDisabled)if(h.aspectRatioLocked){const{aspectRatio:r,aspectRatioLabel:l}=o(this,i,ri).call(this);s.classList.add("locked"),o(this,i,Rt).call(this,"locked").value=r,e.removeAttribute("disabled"),e.label=`${l}`,t.value="locked"}else s.classList.remove("locked"),e.setAttribute("disabled","disabled"),e.label="locked",o(this,i,tt).call(this)},vi=function(){if(!o(this,i,Ct).call(this)&&!h.aspectRatioLocked){o(this,i,me).call(this,this.translator._("Make selection before locking it"));return}h.aspectRatioLocked=!h.aspectRatioLocked,o(this,i,mi).call(this)},Fa=function(){Je.aspectRatioLocked=!Je.aspectRatioLocked,this.updateResizeAspectRatioLockState()},Rt=function(e){return ce.find(t=>t.name===e)},He=function(){const{fileSizeAltered:e,fileSizeDifference:t}=this.elements,{fileFormatSelect:s}=this.elements,r=s.value||n(this,T).mimeType,l=o(this,i,Kt).call(this,r),g=l-n(this,qe);e.innerText=Tt(l,1);let p;p=`${Tt(g,1,!0)}`,t.innerHTML=p},La=function(){const{fileSizeOriginal:e}=this.elements,t=n(this,T).mimeType;d(this,qe,o(this,i,Kt).call(this,t)),e.innerText=Tt(n(this,qe),1)},tt=function(){const{aspectRatioSelect:e}=this.elements;e.value=o(this,i,_a).call(this).name},_a=function(){const e=o(this,i,Rt).call(this,this.selectionAspectRatio);return e!=null&&e.active?e:ce.find(t=>t.active)||ce[0]},Ma=function(){h.aspectRatioLocked&&o(this,i,vi).call(this)},wi=function(){const{freeRotationRange:e}=this.elements;e.value=_.angle},xi=function(){const{freeRotationRangeValue:e}=this.elements;e.value=_.angle},Ia=function(){const{freeRotationRange:e,freeRotationRangeValue:t}=this.elements;e.value=_.angle,t.value=_.angle},Ct=function(){const{w:e,h:t}=h.area;return e!==0&&t!==0},Ha=function(){var e,t;return!!((t=(e=z==null?void 0:z.canvasImage)==null?void 0:e.getContext("2d"))!=null&&t.filter)},Da=function(){n(this,Ge).reset()},Ba=function(){const{fileFormatSelect:e}=this.elements;e.value=n(this,Ze).mimeType},y(bt,"shadowTemplate",os),y(bt,"elementLookup",["#main","#canvasesWrapper","#canvasesButtons","#editorCanvases","#menuFieldset","#aspectRatioSelect","#dialogHelp","#freeRotation","#freeRotationRange","#freeRotationRangeValue","#imageProperties","#fileFormat","#resizeAspectRatioLock","#selectionAspectRatioLock","#resize","#resizeWidth","#resizeHeight","#zoomPercentage","#imageAspectRatio","#imageOrientation","#imageOrientationIcon","#rotation","#mirroring","#cropping","#selecting","#download","#toggleGrid","#fileSizeOriginal","#fileSizeAltered","#fileSizeDifference","#fileSizeOriginalLabel","#fileSizeAlteredLabel","#fileSizeDifferenceLabel","#fileSizeLabel","#fileFormatSelect","#imageSelectionSize","#imageSelectionAspectRatio","#filters","#showFilters","#filterContainer"]),y(bt,"translationsPath","/lang");customElements.define("image-editor",bt);
