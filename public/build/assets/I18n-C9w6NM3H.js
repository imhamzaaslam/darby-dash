import{a7 as d,a8 as f,a as _,o as r,f as l,e as t,b as s,a5 as m,a9 as b,aa as g,n as y,c as v,F as x,i as V,ab as C,ac as I,t as L,v as h}from"./main-CluZvOpW.js";const u=(a,n)=>{var c;const e=d();return e?e.proxy&&"$can"in e.proxy?(c=e.proxy)==null?void 0:c.$can(a,n):!0:!1},j=a=>{const n=a.children.some(e=>u(e.action,e.subject));return a.action&&a.subject?u(a.action,a.subject)&&n:n},w={__name:"I18n",props:{languages:{type:Array,required:!0},location:{type:null,required:!1,default:"bottom end"}},setup(a){const n=a,{locale:e}=f({useScope:"global"});return(i,c)=>{const p=_("IconBtn");return r(),l(p,null,{default:t(()=>[s(m,{size:"24",icon:"tabler-language"}),s(b,{activator:"parent",location:n.location,offset:"12px"},{default:t(()=>[s(g,{selected:[y(e)],color:"primary","min-width":"175px"},{default:t(()=>[(r(!0),v(x,null,V(n.languages,o=>(r(),l(C,{key:o.i18nLang,value:o.i18nLang,onClick:k=>e.value=o.i18nLang},{default:t(()=>[s(I,null,{default:t(()=>[L(h(o.label),1)]),_:2},1024)]),_:2},1032,["value","onClick"]))),128))]),_:1},8,["selected"])]),_:1},8,["location"])]),_:1})}}};export{w as _,u as a,j as c};