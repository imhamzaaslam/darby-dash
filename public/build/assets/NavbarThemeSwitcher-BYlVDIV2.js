import{au as f,r as v,w as y,a as g,o as c,f as i,e as s,b as n,V as k,n as r,ak as V,d as x,v as d,L as w,M as b,ae as B,c as S,F as C,i as I,N,t as T}from"./main-CDXbRjuL.js";const L={class:"text-capitalize"},z={__name:"ThemeSwitcher",props:{themes:{type:Array,required:!0}},setup(l){const o=l,t=f(),a=v([t.theme]);return y(()=>t.theme,()=>{a.value=[t.theme]},{deep:!0}),(m,p)=>{const h=g("IconBtn");return c(),i(h,{color:"rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity))"},{default:s(()=>{var u;return[n(k,{icon:(u=o.themes.find(e=>e.name===r(t).theme))==null?void 0:u.icon,size:"24"},null,8,["icon"]),n(V,{activator:"parent","open-delay":"1000","scroll-strategy":"close"},{default:s(()=>[x("span",L,d(r(t).theme),1)]),_:1}),n(w,{activator:"parent",offset:"12px",width:180},{default:s(()=>[n(b,{selected:r(a),"onUpdate:selected":p[0]||(p[0]=e=>B(a)?a.value=e:null),mandatory:""},{default:s(()=>[(c(!0),S(C,null,I(o.themes,({name:e,icon:_})=>(c(),i(N,{key:e,value:e,"prepend-icon":_,color:"primary",class:"text-capitalize",onClick:()=>{r(t).theme=e}},{default:s(()=>[T(d(e),1)]),_:2},1032,["value","prepend-icon","onClick"]))),128))]),_:1},8,["selected"])]),_:1})]}),_:1})}}},M={__name:"NavbarThemeSwitcher",setup(l){const o=[{name:"light",icon:"tabler-sun-high"},{name:"dark",icon:"tabler-moon-stars"},{name:"system",icon:"tabler-device-desktop-analytics"}];return(t,a)=>{const m=z;return c(),i(m,{themes:o})}}};export{M as default};