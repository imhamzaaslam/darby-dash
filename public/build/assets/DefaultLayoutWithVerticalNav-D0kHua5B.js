import{_ as le}from"./AppLoadingIndicator-DU2mgUAx.js";import{_ as H,L as J,M as k,T as Q,r as _,N as oe,O as re,P as X,l as B,j as D,w as R,a as G,o as s,f as d,e as o,d as N,z as q,b as m,n as e,V as E,p,I as w,h,q as x,J as I,y as A,Q as ce,c as L,F as Z,i as ee,x as C,R as te,U as ae,k as de,W as ue,m as K,X as S,Y as ve,s as T,t as j,v as P,Z as ne,$ as pe,a0 as me,a1 as fe,a2 as ge,A as he,B as ye,a3 as be,a4 as Ne,a5 as ke,a6 as xe,K as $e,G as F,S as Ce}from"./main-BF1GAwIU.js";import _e from"./NavbarThemeSwitcher-BZg2S0BF.js";import Ve from"./NavBarNotifications-BkU4K1zH.js";import we from"./UserProfile-Dk1w2OcQ.js";import{c as Ie,a as ie,_ as Ae}from"./I18n-BF1nEhmW.js";const Se=J({name:"TransitionExpand",setup(t,{slots:n}){const u=i=>{const $=getComputedStyle(i).width;i.style.width=$,i.style.position="absolute",i.style.visibility="hidden",i.style.height="auto";const g=getComputedStyle(i).height;i.style.width="",i.style.position="",i.style.visibility="",i.style.height="0px",getComputedStyle(i).height,requestAnimationFrame(()=>{i.style.height=g})},r=i=>{i.style.height="auto"},a=i=>{const $=getComputedStyle(i).height;i.style.height=$,getComputedStyle(i).height,requestAnimationFrame(()=>{i.style.height="0px"})};return()=>k(k(Q),{name:"expand",onEnter:u,onAfterEnter:r,onLeave:a},()=>{var i;return(i=n.default)==null?void 0:i.call(n)})}}),Re=H(Se,[["__scopeId","data-v-2683bda9"]]),je=t=>(te("data-v-18c4988b"),t=t(),ae(),t),Le={class:"nav-header"},Oe=je(()=>N("div",{class:"vertical-nav-items-shadow"},null,-1)),Te={__name:"VerticalNav",props:{tag:{type:null,required:!1,default:"aside"},navItems:{type:null,required:!0},isOverlayNavActive:{type:Boolean,required:!0},toggleIsOverlayNavActive:{type:Function,required:!0}},setup(t){const n=t,u=_(),r=oe(u);re(X,r);const a=B(),i=f=>"heading"in f?Fe:"children"in f?Ge:se,$=D();R(()=>$.name,()=>{n.toggleIsOverlayNavActive(!1)});const g=_(!1),y=f=>g.value=f,b=f=>{g.value=f.target.scrollTop>0},V=a.isVerticalNavMini(r);return(f,l)=>{const c=G("RouterLink");return s(),d(h(n.tag),{ref_key:"refNav",ref:u,class:C(["layout-vertical-nav",[{"overlay-nav":e(a).isLessThanOverlayNavBreakpoint,hovered:e(r),visible:t.isOverlayNavActive,scrolled:e(g)}]])},{default:o(()=>[N("div",Le,[q(f.$slots,"nav-header",{},()=>[m(c,{to:"/",class:"app-logo app-title-wrapper"},{default:o(()=>[e(V)?(s(),d(e(E),{key:1,nodes:e(p).app.logoHalf},null,8,["nodes"])):(s(),d(e(E),{key:0,nodes:e(p).app.logo},null,8,["nodes"]))]),_:1}),w((s(),d(h(e(p).app.iconRenderer||"div"),x({class:["header-action d-none nav-unpin",e(a).isVerticalNavCollapsed&&"d-lg-block"]},e(p).icons.verticalNavUnPinned,{onClick:l[0]||(l[0]=v=>e(a).isVerticalNavCollapsed=!e(a).isVerticalNavCollapsed)}),null,16,["class"])),[[I,e(a).isVerticalNavCollapsed]]),w((s(),d(h(e(p).app.iconRenderer||"div"),x({class:["header-action d-none nav-pin",!e(a).isVerticalNavCollapsed&&"d-lg-block"]},e(p).icons.verticalNavPinned,{onClick:l[1]||(l[1]=v=>e(a).isVerticalNavCollapsed=!e(a).isVerticalNavCollapsed)}),null,16,["class"])),[[I,!e(a).isVerticalNavCollapsed]]),(s(),d(h(e(p).app.iconRenderer||"div"),x({class:"header-action d-lg-none"},e(p).icons.close,{onClick:l[2]||(l[2]=v=>t.toggleIsOverlayNavActive(!1))}),null,16))],!0)]),q(f.$slots,"before-nav-items",{},()=>[Oe],!0),m(c,{to:"/",class:"bottom-logo"},{default:o(()=>[e(V)?A("",!0):(s(),d(e(E),{key:0,nodes:e(p).app.watermarkLogo},null,8,["nodes"]))]),_:1}),q(f.$slots,"nav-items",{updateIsVerticalNavScrolled:y},()=>[(s(),d(e(ce),{key:e(a).isAppRTL,tag:"ul",class:"nav-items",options:{wheelPropagation:!1},onPsScrollY:b},{default:o(()=>[(s(!0),L(Z,null,ee(t.navItems,(v,M)=>(s(),d(h(i(v)),{key:M,item:v},null,8,["item"]))),128))]),_:1}))],!0)]),_:3},8,["class"])}}},Pe=H(Te,[["__scopeId","data-v-18c4988b"]]),Be={class:"nav-group-children"},Ge=Object.assign({name:"VerticalNavGroup"},{__name:"VerticalNavGroup",props:{item:{type:null,required:!0}},setup(t){const n=t,u=D(),r=de(),a=B(),i=a.isVerticalNavMini(),$=ue(X,_(!1)),g=_(!1),y=_(!1),b=l=>l.some(c=>{let v=S.value.includes(c.title);return"children"in c&&(v=b(c.children)||v),v}),V=l=>{l.forEach(c=>{"children"in c&&V(c.children),S.value=S.value.filter(v=>v!==c.title)})};R(()=>u.path,()=>{const l=K(n.item.children,r);y.value=l&&!a.isVerticalNavMini($).value,g.value=l},{immediate:!0}),R(y,l=>{const c=S.value.indexOf(n.item.title);l&&c===-1?S.value.push(n.item.title):!l&&c!==-1&&(S.value.splice(c,1),V(n.item.children))},{immediate:!0}),R(S,l=>{if(l.at(-1)===n.item.title)return;const v=K(n.item.children,r);v||b(n.item.children)||(y.value=v,g.value=v)},{deep:!0}),R(a.isVerticalNavMini($),l=>{y.value=l?!1:g.value});const f=ve();return(l,c)=>e(Ie)(t.item)?(s(),L("li",{key:0,class:C(["nav-group",[{active:e(g),open:e(y),disabled:t.item.disable}]])},[N("div",{class:"nav-group-label",onClick:c[0]||(c[0]=v=>y.value=!e(y))},[(s(),d(h(e(p).app.iconRenderer||"div"),x(t.item.icon||e(p).verticalNav.defaultNavItemIconProps,{class:"nav-item-icon"}),null,16)),(s(),d(h(e(f)?ne:"div"),x({name:"transition-slide-x"},e(f)?void 0:{class:"d-flex align-center flex-grow-1"}),{default:o(()=>[w((s(),d(h(e(p).app.i18n.enable?"i18n-t":"span"),x(e(T)(t.item.title,"span"),{key:"title",class:"nav-item-title"}),{default:o(()=>[j(P(t.item.title),1)]),_:1},16)),[[I,!e(i)]]),t.item.badgeContent?w((s(),d(h(e(p).app.i18n.enable?"i18n-t":"span"),x({key:0},e(T)(t.item.badgeContent,"span"),{key:"badge",class:["nav-item-badge",t.item.badgeClass]}),{default:o(()=>[j(P(t.item.badgeContent),1)]),_:1},16,["class"])),[[I,!e(i)]]):A("",!0),w((s(),d(h(e(p).app.iconRenderer||"div"),x(e(p).icons.chevronRight,{key:"arrow",class:"nav-group-arrow"}),null,16)),[[I,!e(i)]])]),_:1},16))]),m(e(Re),null,{default:o(()=>[w(N("ul",Be,[(s(!0),L(Z,null,ee(t.item.children,v=>(s(),d(h("children"in v?"VerticalNavGroup":e(se)),{key:v.title,item:v},null,8,["item"]))),128))],512),[[I,e(y)]])]),_:1})],2)):A("",!0)}}),Me=J({props:{navItems:{type:Array,required:!0},verticalNavAttrs:{type:Object,default:()=>({})}},setup(t,{slots:n}){const{width:u}=pe(),r=B(),a=_(!1),i=_(!1),$=me(a);return fe(a,i),R(u,()=>{!r.isLessThanOverlayNavBreakpoint&&i.value&&(i.value=!1)}),()=>{var W,z,Y;const g=ge(t,"verticalNavAttrs"),{wrapper:y,wrapperProps:b,...V}=g.value,f=k(Pe,{isOverlayNavActive:a.value,toggleIsOverlayNavActive:$,navItems:t.navItems,...V},{"nav-header":()=>{var O;return(O=n["vertical-nav-header"])==null?void 0:O.call(n)},"before-nav-items":()=>{var O;return(O=n["before-vertical-nav-items"])==null?void 0:O.call(n)}}),l=k("header",{class:["layout-navbar",{"navbar-blur":r.isNavbarBlurEnabled}]},[k("div",{class:"navbar-content-container"},(W=n.navbar)==null?void 0:W.call(n,{toggleVerticalOverlayNavActive:$}))]),c=k("main",{class:"layout-page-content"},k("div",{class:"page-content-container"},(z=n.default)==null?void 0:z.call(n))),v=k("footer",{class:"layout-footer"},[k("div",{class:"footer-content-container"},(Y=n.footer)==null?void 0:Y.call(n))]),M=k("div",{class:["layout-overlay",{visible:i.value}],onClick:()=>{i.value=!i.value}});return k("div",{class:["layout-wrapper",...r._layoutClasses]},[y?k(y,b,{default:()=>f}):f,k("div",{class:"layout-content-wrapper"},[l,c,v]),M])}}}),se={__name:"VerticalNavLink",props:{item:{type:null,required:!0}},setup(t){const u=B().isVerticalNavMini();return(r,a)=>e(ie)(t.item.action,t.item.subject)?(s(),L("li",{key:0,class:C(["nav-link",{disabled:t.item.disable}])},[(s(),d(h(t.item.to?"RouterLink":"a"),x(e(ye)(t.item),{class:{"router-link-active router-link-exact-active":e(he)(t.item,r.$router)}}),{default:o(()=>[(s(),d(h(e(p).app.iconRenderer||"div"),x(t.item.icon||e(p).verticalNav.defaultNavItemIconProps,{class:"nav-item-icon"}),null,16)),m(ne,{name:"transition-slide-x"},{default:o(()=>[w((s(),d(h(e(p).app.i18n.enable?"i18n-t":"span"),x({key:"title",class:"nav-item-title"},e(T)(t.item.title,"span")),{default:o(()=>[j(P(t.item.title),1)]),_:1},16)),[[I,!e(u)]]),t.item.badgeContent?w((s(),d(h(e(p).app.i18n.enable?"i18n-t":"span"),x({key:"badge",class:["nav-item-badge",t.item.badgeClass]},e(T)(t.item.badgeContent,"span")),{default:o(()=>[j(P(t.item.badgeContent),1)]),_:1},16,["class"])),[[I,!e(u)]]):A("",!0)]),_:1})]),_:1},16,["class"]))],2)):A("",!0)}},qe={key:0,class:"nav-section-title"},Ee={class:"title-wrapper"},Fe={__name:"VerticalNavSectionTitle",props:{item:{type:null,required:!0}},setup(t){const u=B().isVerticalNavMini();return(r,a)=>e(ie)(t.item.action,t.item.subject)?(s(),L("li",qe,[N("div",Ee,[m(Q,{name:"vertical-nav-section-title",mode:"out-in"},{default:o(()=>[(s(),d(h(e(u)?e(p).app.iconRenderer:e(p).app.i18n.enable?"i18n-t":"span"),x({key:e(u),class:e(u)?"placeholder-icon":"title-text"},{...e(p).icons.sectionTitlePlaceholder,...e(T)(t.item.heading,"span")}),{default:o(()=>[j(P(e(u)?null:t.item.heading),1)]),_:1},16,["class"]))]),_:1})])])):A("",!0)}},U=be().isAdmin,He=[{title:"Home",to:{name:"root"},icon:{icon:"tabler-align-box-bottom-center"}},{title:"Projects",icon:{icon:"tabler-chart-histogram"},to:{name:"web-designs-list"}},U&&{title:"Members",icon:{icon:"tabler-users"},to:{name:"members-list"}},U&&{title:"Roles",icon:{icon:"tabler-shield-check"},to:{name:"roles-setting"}}],De=He.filter(Boolean),We=t=>(te("data-v-e9b96536"),t=t(),ae(),t),ze={class:"d-flex h-100 align-center"},Ye={key:0,class:"d-flex align-center"},Ke=We(()=>N("span",{class:"text-h6 font-weight-bold me-4"},[j("Darby Bucks "),N("span",{class:"text-primary"},"$200")],-1)),Ue={__name:"DefaultLayoutWithVerticalNav",setup(t){const n=_(!1),u=_(null),r=D(),a=_(null);_(null),Ne(()=>{a.value=r.params.id}),R([n,u],()=>{n.value&&u.value&&u.value.fallbackHandle(),!n.value&&u.value&&u.value.resolveHandle()},{immediate:!0});const i=ke(()=>r.params.id!==void 0);return($,g)=>{const y=G("IconBtn"),b=G("RouterLink"),V=le,f=G("RouterView");return s(),d(e(Me),{"nav-items":e(De)},{navbar:o(({toggleVerticalOverlayNavActive:l})=>{var c;return[N("div",ze,[m(y,{id:"vertical-nav-toggle-btn",class:"ms-n3 d-lg-none",onClick:v=>l(!0)},{default:o(()=>[m(xe,{size:"26",icon:"tabler-menu-2"})]),_:2},1032,["onClick"]),m(_e),e(i)?(s(),L("div",Ye,[m(b,{to:`/projects/web-designs/${e(a)}`},{default:o(()=>[N("span",{class:C(["text-h6 ms-3 me-5 inner-badge-text",{"text-primary":e(r).path===`/projects/web-designs/${e(a)}`}])},"Overview",2)]),_:1},8,["to"]),m(b,{to:`/projects/${e(a)}/tasks/add`},{default:o(()=>[N("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/tasks/add`}])}," Tasks ",2)]),_:1},8,["to"]),m(b,{to:`/projects/${e(a)}/milestones`,class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/milestones`}])},{default:o(()=>[j(" Milestones ")]),_:1},8,["to","class"]),m(b,{to:`/projects/${e(a)}/calendar`},{default:o(()=>[N("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/calendar`}])},"Calendar",2)]),_:1},8,["to"]),m(b,{to:`/projects/${e(a)}/files`},{default:o(()=>[N("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/files`}])},"Files",2)]),_:1},8,["to"]),m(b,{to:`/projects/${e(a)}/chat`},{default:o(()=>[N("span",{class:C(["text-h6 me-5 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/chat`}])}," Inbox ",2)]),_:1},8,["to"]),m(b,{to:`/projects/${e(a)}/team`},{default:o(()=>[N("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/team`}])},"Your Team",2)]),_:1},8,["to"]),m(b,{to:`/projects/${e(a)}/payments`},{default:o(()=>[N("span",{class:C(["text-h6 me-8 inner-badge-text",{"text-primary":e(r).path===`/projects/${e(a)}/payments`}])},"Payments",2)]),_:1},8,["to"])])):A("",!0),m($e),e(F).app.i18n.enable&&((c=e(F).app.i18n.langConfig)!=null&&c.length)?(s(),d(Ae,{key:1,languages:e(F).app.i18n.langConfig},null,8,["languages"])):A("",!0),Ke,m(Ve,{class:"me-2"}),m(we)])]}),default:o(()=>[m(V,{ref_key:"refLoadingIndicator",ref:u},null,512),m(f,null,{default:o(({Component:l})=>[(s(),d(Ce,{timeout:0,onFallback:g[0]||(g[0]=c=>n.value=!0),onResolve:g[1]||(g[1]=c=>n.value=!1)},{default:o(()=>[(s(),d(h(l)))]),_:2},1024))]),_:1})]),_:1},8,["nav-items"])}}},at=H(Ue,[["__scopeId","data-v-e9b96536"]]);export{at as default};