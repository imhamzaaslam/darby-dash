import{_ as C,bL as F,r as g,w as _,o as u,f as K,e as l,b as s,af as N,ao as k,bJ as U,n as i,ac as z,bM as w,d as r,a5 as y,ak as q,Q as E,I as m,z as p,J as v,c as V,F as b,a9 as M,i as D,aa as T,t as j,v as S,y as x,bN as J,bE as P,R as Q,U as O}from"./main-abRwCBhm.js";const X=o=>(Q("data-v-01416ec8"),o=o(),O(),o),G={class:"d-flex align-center text-high-emphasis me-1"},H={class:"d-flex align-start"},W={class:"h-100"},Y={class:"h-100"},Z={class:"app-bar-search-suggestions d-flex flex-column align-center justify-center text-high-emphasis pa-12"},ee={class:"d-flex align-center flex-wrap justify-center gap-2 text-h5 mt-3"},se=X(()=>r("span",null,"No Result For ",-1)),ae={__name:"AppBarSearch",props:{isDialogVisible:{type:Boolean,required:!0},searchResults:{type:Array,required:!0},isLoading:{type:Boolean,required:!1}},emits:["update:isDialogVisible","search"],setup(o,{emit:L}){const c=o,h=L,{ctrl_k:$,meta_k:I}=F({passive:!1,onEventFired(e){e.ctrlKey&&e.key==="k"&&e.type==="keydown"&&e.preventDefault()}}),f=g(),R=g(),a=g("");_([$,I],()=>{h("update:isDialogVisible",!0)});const d=()=>{a.value="",h("update:isDialogVisible",!1)},B=e=>{var n,t;e.key==="ArrowDown"?(e.preventDefault(),(n=f.value)==null||n.focus("next")):e.key==="ArrowUp"&&(e.preventDefault(),(t=f.value)==null||t.focus("prev"))},A=e=>{a.value="",h("update:isDialogVisible",e)};return _(()=>c.isDialogVisible,()=>{a.value=""}),(e,n)=>(u(),K(P,{"max-width":"600","model-value":c.isDialogVisible,height:e.$vuetify.display.smAndUp?"531":"100%",fullscreen:e.$vuetify.display.width<600,class:"app-bar-search-dialog","onUpdate:modelValue":A,onKeyup:w(d,["esc"])},{default:l(()=>[s(N,{height:"100%",width:"100%",class:"position-relative"},{default:l(()=>[s(k,{class:"px-4",style:{"padding-block":"1rem 1.2rem"}},{default:l(()=>[s(U,{ref_key:"refSearchInput",ref:R,modelValue:i(a),"onUpdate:modelValue":[n[0]||(n[0]=t=>z(a)?a.value=t:null),n[1]||(n[1]=t=>e.$emit("search",i(a)))],autofocus:"",density:"compact",variant:"plain",class:"app-bar-search-input",onKeyup:w(d,["esc"]),onKeydown:B},{"prepend-inner":l(()=>[r("div",G,[s(y,{size:"24",icon:"tabler-search"})])]),"append-inner":l(()=>[r("div",H,[r("div",{class:"text-base text-disabled cursor-pointer me-3",onClick:d}," [esc] "),s(y,{icon:"tabler-x",size:"24",onClick:d})])]),_:1},8,["modelValue"])]),_:1}),s(q),s(i(E),{options:{wheelPropagation:!1,suppressScrollX:!0},class:"h-100"},{default:l(()=>[m(r("div",W,[p(e.$slots,"suggestions",{},void 0,!0)],512),[[v,!!c.searchResults&&!i(a)&&e.$slots.suggestions]]),o.isLoading?x("",!0):(u(),V(b,{key:0},[m(s(i(M),{ref_key:"refSearchList",ref:f,density:"compact",class:"app-bar-search-list py-0"},{default:l(()=>[(u(!0),V(b,null,D(c.searchResults,t=>p(e.$slots,"searchResult",{key:t,item:t},()=>[s(i(T),null,{default:l(()=>[j(S(t),1)]),_:2},1024)],!0)),128))]),_:3},512),[[v,i(a).length&&!!c.searchResults.length]]),m(r("div",Y,[p(e.$slots,"noData",{},()=>[s(k,{class:"h-100"},{default:l(()=>[r("div",Z,[s(y,{size:"64",icon:"tabler-file-alert"}),r("div",ee,[se,r("span",null,'"'+S(i(a))+'"',1)]),p(e.$slots,"noDataSuggestion",{},void 0,!0)])]),_:3})],!0)],512),[[v,!c.searchResults.length&&i(a).length]])],64)),o.isLoading?(u(),V(b,{key:1},D(3,t=>s(J,{key:t,type:"list-item-two-line"})),64)):x("",!0)]),_:3})]),_:3})]),_:3},8,["model-value","height","fullscreen"]))}},le=C(ae,[["__scopeId","data-v-01416ec8"]]);export{le as default};