import{_ as le}from"./AppLoadingIndicator-7FSKRCBX.js";import{_ as W,R as te,U as N,T as ae,r as w,W as ce,X as ue,Y as ne,l as G,j as z,w as q,a as F,o,f as u,e as i,d as y,z as D,b as l,n as e,P as H,p as f,I as T,h as b,q as x,J as M,y as S,Z as de,c as V,F as Y,i as U,x as C,$ as ve,a0 as pe,k as me,a1 as fe,m as J,a2 as B,a3 as ge,s as E,t as j,v as I,a4 as se,a5 as he,a6 as ye,a7 as be,a8 as ke,A as _e,B as Ne,a9 as ie,aa as xe,ab as $e,ac as K,V as Q,ad as Ve,L as Ce,M as je,N as X,O as Z,Q as we,S as Ie}from"./main-ClQs6-l4.js";import Se from"./NavbarThemeSwitcher-BfziPfsT.js";import Ae from"./NavBarNotifications-Dn9W0ppU.js";import Re from"./UserProfile-Dix6MTX-.js";import{c as Le,a as oe}from"./casl-XQlSoV4_.js";const Te=te({name:"TransitionExpand",setup(t,{slots:s}){const p=a=>{const n=getComputedStyle(a).width;a.style.width=n,a.style.position="absolute",a.style.visibility="hidden",a.style.height="auto";const k=getComputedStyle(a).height;a.style.width="",a.style.position="",a.style.visibility="",a.style.height="0px",getComputedStyle(a).height,requestAnimationFrame(()=>{a.style.height=k})},c=a=>{a.style.height="auto"},d=a=>{const n=getComputedStyle(a).height;a.style.height=n,getComputedStyle(a).height,requestAnimationFrame(()=>{a.style.height="0px"})};return()=>N(N(ae),{name:"expand",onEnter:p,onAfterEnter:c,onLeave:d},()=>{var a;return(a=s.default)==null?void 0:a.call(s)})}}),Me=W(Te,[["__scopeId","data-v-22ff91fc"]]),Oe=t=>(ve("data-v-18c4988b"),t=t(),pe(),t),Pe={class:"nav-header"},Be=Oe(()=>y("div",{class:"vertical-nav-items-shadow"},null,-1)),qe={__name:"VerticalNav",props:{tag:{type:null,required:!1,default:"aside"},navItems:{type:null,required:!0},isOverlayNavActive:{type:Boolean,required:!0},toggleIsOverlayNavActive:{type:Function,required:!0}},setup(t){const s=t,p=w(),c=ce(p);ue(ne,c);const d=G(),a=h=>"heading"in h?ze:"children"in h?Fe:re,n=z();q(()=>n.name,()=>{s.toggleIsOverlayNavActive(!1)});const k=w(!1),g=h=>k.value=h,R=h=>{k.value=h.target.scrollTop>0},_=d.isVerticalNavMini(c);return(h,r)=>{const m=F("RouterLink");return o(),u(b(s.tag),{ref_key:"refNav",ref:p,class:C(["layout-vertical-nav",[{"overlay-nav":e(d).isLessThanOverlayNavBreakpoint,hovered:e(c),visible:t.isOverlayNavActive,scrolled:e(k)}]])},{default:i(()=>[y("div",Pe,[D(h.$slots,"nav-header",{},()=>[l(m,{to:"/",class:"app-logo app-title-wrapper"},{default:i(()=>[e(_)?(o(),u(e(H),{key:1,nodes:e(f).app.logoHalf},null,8,["nodes"])):(o(),u(e(H),{key:0,nodes:e(f).app.logo},null,8,["nodes"]))]),_:1}),T((o(),u(b(e(f).app.iconRenderer||"div"),x({class:["header-action d-none nav-unpin",e(d).isVerticalNavCollapsed&&"d-lg-block"]},e(f).icons.verticalNavUnPinned,{onClick:r[0]||(r[0]=v=>e(d).isVerticalNavCollapsed=!e(d).isVerticalNavCollapsed)}),null,16,["class"])),[[M,e(d).isVerticalNavCollapsed]]),T((o(),u(b(e(f).app.iconRenderer||"div"),x({class:["header-action d-none nav-pin",!e(d).isVerticalNavCollapsed&&"d-lg-block"]},e(f).icons.verticalNavPinned,{onClick:r[1]||(r[1]=v=>e(d).isVerticalNavCollapsed=!e(d).isVerticalNavCollapsed)}),null,16,["class"])),[[M,!e(d).isVerticalNavCollapsed]]),(o(),u(b(e(f).app.iconRenderer||"div"),x({class:"header-action d-lg-none"},e(f).icons.close,{onClick:r[2]||(r[2]=v=>t.toggleIsOverlayNavActive(!1))}),null,16))],!0)]),D(h.$slots,"before-nav-items",{},()=>[Be],!0),l(m,{to:"/",class:"bottom-logo"},{default:i(()=>[e(_)?S("",!0):(o(),u(e(H),{key:0,nodes:e(f).app.watermarkLogo},null,8,["nodes"]))]),_:1}),D(h.$slots,"nav-items",{updateIsVerticalNavScrolled:g},()=>[(o(),u(e(de),{key:e(d).isAppRTL,tag:"ul",class:"nav-items",options:{wheelPropagation:!1},onPsScrollY:R},{default:i(()=>[(o(!0),V(Y,null,U(t.navItems,(v,O)=>(o(),u(b(a(v)),{key:O,item:v},null,8,["item"]))),128))]),_:1}))],!0)]),_:3},8,["class"])}}},Ee=W(qe,[["__scopeId","data-v-18c4988b"]]),Ge={class:"nav-group-children"},Fe=Object.assign({name:"VerticalNavGroup"},{__name:"VerticalNavGroup",props:{item:{type:null,required:!0}},setup(t){const s=t,p=z(),c=me(),d=G(),a=d.isVerticalNavMini(),n=fe(ne,w(!1)),k=w(!1),g=w(!1),R=r=>r.some(m=>{let v=B.value.includes(m.title);return"children"in m&&(v=R(m.children)||v),v}),_=r=>{r.forEach(m=>{"children"in m&&_(m.children),B.value=B.value.filter(v=>v!==m.title)})};q(()=>p.path,()=>{const r=J(s.item.children,c);g.value=r&&!d.isVerticalNavMini(n).value,k.value=r},{immediate:!0}),q(g,r=>{const m=B.value.indexOf(s.item.title);r&&m===-1?B.value.push(s.item.title):!r&&m!==-1&&(B.value.splice(m,1),_(s.item.children))},{immediate:!0}),q(B,r=>{if(r.at(-1)===s.item.title)return;const v=J(s.item.children,c);v||R(s.item.children)||(g.value=v,k.value=v)},{deep:!0}),q(d.isVerticalNavMini(n),r=>{g.value=r?!1:k.value});const h=ge();return(r,m)=>e(Le)(t.item)?(o(),V("li",{key:0,class:C(["nav-group",[{active:e(k),open:e(g),disabled:t.item.disable}]])},[y("div",{class:"nav-group-label",onClick:m[0]||(m[0]=v=>g.value=!e(g))},[(o(),u(b(e(f).app.iconRenderer||"div"),x(t.item.icon||e(f).verticalNav.defaultNavItemIconProps,{class:"nav-item-icon"}),null,16)),(o(),u(b(e(h)?se:"div"),x({name:"transition-slide-x"},e(h)?void 0:{class:"d-flex align-center flex-grow-1"}),{default:i(()=>[T((o(),u(b(e(f).app.i18n.enable?"i18n-t":"span"),x(e(E)(t.item.title,"span"),{key:"title",class:"nav-item-title"}),{default:i(()=>[j(I(t.item.title),1)]),_:1},16)),[[M,!e(a)]]),t.item.badgeContent?T((o(),u(b(e(f).app.i18n.enable?"i18n-t":"span"),x({key:0},e(E)(t.item.badgeContent,"span"),{key:"badge",class:["nav-item-badge",t.item.badgeClass]}),{default:i(()=>[j(I(t.item.badgeContent),1)]),_:1},16,["class"])),[[M,!e(a)]]):S("",!0),T((o(),u(b(e(f).app.iconRenderer||"div"),x(e(f).icons.chevronRight,{key:"arrow",class:"nav-group-arrow"}),null,16)),[[M,!e(a)]])]),_:1},16))]),l(e(Me),null,{default:i(()=>[T(y("ul",Ge,[(o(!0),V(Y,null,U(t.item.children,v=>(o(),u(b("children"in v?"VerticalNavGroup":e(re)),{key:v.title,item:v},null,8,["item"]))),128))],512),[[M,e(g)]])]),_:1})],2)):S("",!0)}}),De=te({props:{navItems:{type:Array,required:!0},verticalNavAttrs:{type:Object,default:()=>({})}},setup(t,{slots:s}){const{width:p}=he(),c=G(),d=w(!1),a=w(!1),n=ye(d);return be(d,a),q(p,()=>{!c.isLessThanOverlayNavBreakpoint&&a.value&&(a.value=!1)}),()=>{var P,$,A;const k=ke(t,"verticalNavAttrs"),{wrapper:g,wrapperProps:R,..._}=k.value,h=N(Ee,{isOverlayNavActive:d.value,toggleIsOverlayNavActive:n,navItems:t.navItems,..._},{"nav-header":()=>{var L;return(L=s["vertical-nav-header"])==null?void 0:L.call(s)},"before-nav-items":()=>{var L;return(L=s["before-vertical-nav-items"])==null?void 0:L.call(s)}}),r=N("header",{class:["layout-navbar",{"navbar-blur":c.isNavbarBlurEnabled}]},[N("div",{class:"navbar-content-container"},(P=s.navbar)==null?void 0:P.call(s,{toggleVerticalOverlayNavActive:n}))]),m=N("main",{class:"layout-page-content"},N("div",{class:"page-content-container"},($=s.default)==null?void 0:$.call(s))),v=N("footer",{class:"layout-footer"},[N("div",{class:"footer-content-container"},(A=s.footer)==null?void 0:A.call(s))]),O=N("div",{class:["layout-overlay",{visible:a.value}],onClick:()=>{a.value=!a.value}});return N("div",{class:["layout-wrapper",...c._layoutClasses]},[g?N(g,R,{default:()=>h}):h,N("div",{class:"layout-content-wrapper"},[r,m,v]),O])}}}),re={__name:"VerticalNavLink",props:{item:{type:null,required:!0}},setup(t){const p=G().isVerticalNavMini();return(c,d)=>e(oe)(t.item.action,t.item.subject)?(o(),V("li",{key:0,class:C(["nav-link",{disabled:t.item.disable}])},[(o(),u(b(t.item.to?"RouterLink":"a"),x(e(Ne)(t.item),{class:{"router-link-active router-link-exact-active":e(_e)(t.item,c.$router)}}),{default:i(()=>[(o(),u(b(e(f).app.iconRenderer||"div"),x(t.item.icon||e(f).verticalNav.defaultNavItemIconProps,{class:"nav-item-icon"}),null,16)),l(se,{name:"transition-slide-x"},{default:i(()=>[T((o(),u(b(e(f).app.i18n.enable?"i18n-t":"span"),x({key:"title",class:"nav-item-title"},e(E)(t.item.title,"span")),{default:i(()=>[j(I(t.item.title),1)]),_:1},16)),[[M,!e(p)]]),t.item.badgeContent?T((o(),u(b(e(f).app.i18n.enable?"i18n-t":"span"),x({key:"badge",class:["nav-item-badge",t.item.badgeClass]},e(E)(t.item.badgeContent,"span")),{default:i(()=>[j(I(t.item.badgeContent),1)]),_:1},16,["class"])),[[M,!e(p)]]):S("",!0)]),_:1})]),_:1},16,["class"]))],2)):S("",!0)}},He={key:0,class:"nav-section-title"},We={class:"title-wrapper"},ze={__name:"VerticalNavSectionTitle",props:{item:{type:null,required:!0}},setup(t){const p=G().isVerticalNavMini();return(c,d)=>e(oe)(t.item.action,t.item.subject)?(o(),V("li",He,[y("div",We,[l(ae,{name:"vertical-nav-section-title",mode:"out-in"},{default:i(()=>[(o(),u(b(e(p)?e(f).app.iconRenderer:e(f).app.i18n.enable?"i18n-t":"span"),x({key:e(p),class:e(p)?"placeholder-icon":"title-text"},{...e(f).icons.sectionTitlePlaceholder,...e(E)(t.item.heading,"span")}),{default:i(()=>[j(I(e(p)?null:t.item.heading),1)]),_:1},16,["class"]))]),_:1})])])):S("",!0)}},ee=ie().isAdmin,Ye=[{title:"Home",to:{name:"root"},icon:{icon:"tabler-align-box-bottom-center"}},{title:"Projects",icon:{icon:"tabler-chart-histogram"},to:{name:"web-designs-list"}},ee&&{title:"Members",icon:{icon:"tabler-users"},to:{name:"members-list"}},ee&&{title:"Settings",icon:{icon:"tabler-settings"},children:[{title:"Manage Roles",to:"roles-setting"},{title:"Manage Templates",to:"templates-setting"},{title:"Manage Services",to:"services-setting"}]}],Ue=Ye.filter(Boolean),Je={class:"d-flex h-100 align-center"},Ke={key:0},Qe={class:"text-primary"},Xe={key:1},Ze={class:"text-primary"},et={key:1,class:"d-none d-lg-flex align-center"},tt={key:2,class:"text-h6 font-weight-bold me-4 d-none d-lg-flex"},at={key:0},nt={class:"text-primary"},st={key:1},it={class:"text-primary"},ot={__name:"DefaultLayoutWithVerticalNav",setup(t){const s=w(!1),p=w(null),c=z(),d=xe(),a=ie(),n=w(null);w(null),$e(()=>{n.value=c.params.id}),q([s,p],()=>{s.value&&p.value&&p.value.fallbackHandle(),!s.value&&p.value&&p.value.resolveHandle()},{immediate:!0});const k=K(()=>!(c.name==="manage-templates")&&c.params.id!==void 0),g=K(()=>d.getProject);return(R,_)=>{const h=F("IconBtn"),r=F("RouterLink"),m=le,v=F("RouterView");return o(),u(e(De),{"nav-items":e(Ue)},{navbar:i(({toggleVerticalOverlayNavActive:O})=>{var P;return[y("div",Je,[l(h,{id:"vertical-nav-toggle-btn",class:"ms-n3 d-lg-none",onClick:$=>O(!0)},{default:i(()=>[l(Q,{size:"26",icon:"tabler-menu-2"})]),_:2},1032,["onClick"]),l(Se),e(k)?(o(),u(h,{key:0,class:"ms-n1 d-lg-none",onClick:_[0]||(_[0]=Ve(()=>{},["prevent"]))},{default:i(()=>[l(Q,{size:"26",icon:"tabler-dots-vertical"}),l(Ce,{activator:"parent",class:"d-lg-none","offset-y":""},{default:i(()=>[l(je,null,{default:i(()=>{var $;return[(o(!0),V(Y,null,U([{text:"Overview",route:`/projects/web-designs/${e(n)}`},{text:"Tasks",route:`/projects/${e(n)}/tasks/add`},{text:"Milestones",route:`/projects/${e(n)}/milestones`},{text:"Calendar",route:`/projects/${e(n)}/calendar`},{text:"Files",route:`/projects/${e(n)}/files`},{text:"Inbox",route:`/projects/${e(n)}/chat`},{text:"Your Team",route:`/projects/${e(n)}/team`},{text:"Payments",route:`/projects/${e(n)}/payments`}],A=>(o(),u(X,{key:A.text},{default:i(()=>[l(r,{to:A.route},{default:i(()=>[l(Z,null,{default:i(()=>[j(I(A.text),1)]),_:2},1024)]),_:2},1032,["to"])]),_:2},1024))),128)),($=e(g))!=null&&$.bucks_share?(o(),u(X,{key:0},{default:i(()=>[l(r,{to:e(a).isAdmin||e(a).isManager?`/projects/${e(n)}/bucks`:`/projects/${e(n)}/bucks?tab=manage-bucks`},{default:i(()=>[l(Z,null,{default:i(()=>{var A,L;return[e(a).isAdmin||e(a).isManager?(o(),V("span",Ke,[j(" Darby Bucks "),y("span",Qe,"$"+I((A=e(g))==null?void 0:A.bucks_share_amount),1)])):(o(),V("span",Xe,[j(" Earned Bucks "),y("span",Ze,"$"+I((L=e(g))==null?void 0:L.bucks_earnings),1)]))]}),_:1})]),_:1},8,["to"])]),_:1})):S("",!0)]}),_:1})]),_:1})]),_:1})):S("",!0),e(k)?(o(),V("div",et,[l(r,{to:`/projects/web-designs/${e(n)}`},{default:i(()=>[y("span",{class:C(["text-h6 ms-3 me-5 inner-badge-text",{"text-primary":e(c).path===`/projects/web-designs/${e(n)}`}])},"Overview",2)]),_:1},8,["to"]),l(r,{to:`/projects/${e(n)}/tasks/add`},{default:i(()=>[y("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/tasks/add`}])}," Tasks ",2)]),_:1},8,["to"]),l(r,{to:`/projects/${e(n)}/milestones`,class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/milestones`}])},{default:i(()=>[j(" Milestones ")]),_:1},8,["to","class"]),l(r,{to:`/projects/${e(n)}/calendar`},{default:i(()=>[y("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/calendar`}])},"Calendar",2)]),_:1},8,["to"]),l(r,{to:`/projects/${e(n)}/files`},{default:i(()=>[y("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/files`}])},"Files",2)]),_:1},8,["to"]),l(r,{to:`/projects/${e(n)}/chat`},{default:i(()=>[y("span",{class:C(["text-h6 me-5 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/chat`}])}," Inbox ",2)]),_:1},8,["to"]),l(r,{to:`/projects/${e(n)}/team`},{default:i(()=>[y("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/team`}])},"Your Team",2)]),_:1},8,["to"]),l(r,{to:`/projects/${e(n)}/payments`},{default:i(()=>[y("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(c).path===`/projects/${e(n)}/payments`}])},"Payments",2)]),_:1},8,["to"])])):S("",!0),l(we),e(k)&&((P=e(g))!=null&&P.bucks_share)?(o(),V("span",tt,[e(a).isAdmin||e(a).isManager?(o(),V("span",at,[l(r,{to:`/projects/${e(n)}/bucks`},{default:i(()=>{var $;return[j(" Darby Bucks "),y("span",nt,"$"+I(($=e(g))==null?void 0:$.bucks_share_amount),1)]}),_:1},8,["to"])])):(o(),V("span",st,[l(r,{to:`/projects/${e(n)}/bucks?tab=manage-bucks`},{default:i(()=>{var $;return[j(" Earned Bucks "),y("span",it,"$"+I(($=e(g))==null?void 0:$.bucks_earnings),1)]}),_:1},8,["to"])]))])):S("",!0),l(Ae,{class:"me-2"}),l(Re)])]}),default:i(()=>[l(m,{ref_key:"refLoadingIndicator",ref:p},null,512),l(v,null,{default:i(({Component:O})=>[(o(),u(Ie,{timeout:0,onFallback:_[1]||(_[1]=P=>s.value=!0),onResolve:_[2]||(_[2]=P=>s.value=!1)},{default:i(()=>[(o(),u(b(O)))]),_:2},1024))]),_:1})]),_:1},8,["nav-items"])}}},pt=W(ot,[["__scopeId","data-v-9fb273bc"]]);export{pt as default};