import{_ as x,b5 as y,a$ as b,ba as j,a9 as w,a as V,o as l,c as u,b as e,e as s,au as d,F as p,p as k,i as v,f as T,d as t,ak as C,aq as P,Z as S,V as B,v as o,av as _,t as c,n as I,a1 as D,a2 as F}from"./main-gi9eEmFj.js";const A=r=>(D("data-v-4a420363"),r=r(),F(),r),L=A(()=>t("h3",null,"Project Dashboards",-1)),N={class:"d-flex align-center gap-x-4 mb-2"},R={class:"text-h6 text-center"},q={class:"font-weight-medium text-high-emphasis text-sm text-truncate"},M={class:"font-weight-medium text-high-emphasis text-sm text-truncate"},$={class:"font-weight-medium text-high-emphasis text-sm text-truncate"},z={class:"font-weight-medium text-high-emphasis text-sm text-truncate"},E={__name:"company-dashboard",setup(r){y({title:`${k.app.title} | Project Dashboards`}),b(async()=>{await m()});const i=j(),m=async()=>{try{await i.getAll()}catch(n){toast.error("Failed to get project types:",n.message||n)}},h=w(()=>i.getProjectTypesWithAttributes);return(n,H)=>{const g=V("RouterLink");return l(),u(p,null,[L,e(d,{class:"mt-3"},{default:s(()=>[(l(!0),u(p,null,v(I(h),(a,f)=>(l(),T(_,{key:f,cols:"12",md:"4",sm:"12"},{default:s(()=>[t("div",null,[e(g,{to:{path:"/projects/web-designs",query:{type:a.id}}},{default:s(()=>[e(C,{class:"logistics-card-statistics cursor-pointer"},{default:s(()=>[e(P,null,{default:s(()=>[t("div",N,[e(S,{variant:"tonal",color:"primary",rounded:""},{default:s(()=>[e(B,{icon:a.icon,size:"28"},null,8,["icon"])]),_:2},1024),t("h5",R,o(a.name),1)]),e(d,null,{default:s(()=>[e(_,{cols:"12",class:"text-center"},{default:s(()=>[t("small",q,[c("Projects: "),t("strong",null,o(a.total_projects)+" | ",1)]),t("small",M,[c("Tasks: "),t("strong",null,o(a.total_tasks)+" | ",1)]),t("small",$,[c("Members: "),t("strong",null,o(a.total_members)+" | ",1)]),t("small",z,[c("File: "),t("strong",null,o(a.total_files),1)])]),_:2},1024)]),_:2},1024)]),_:2},1024)]),_:2},1024)]),_:2},1032,["to"])])]),_:2},1024))),128))]),_:1})],64)}}},Z=x(E,[["__scopeId","data-v-4a420363"]]);export{Z as default};