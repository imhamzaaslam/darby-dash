import{_ as se}from"./AppLoadingIndicator-Cvbk6pv4.js";import{_ as E,L as Y,M as x,T as K,r as V,N as le,O as re,P as U,l as P,j as F,w as R,a as G,o as l,f as u,e as r,d as b,z as M,b as v,n as e,V as oe,p,y as S,I as w,h,q as C,J as I,Q as ce,c as L,F as J,i as Q,x as _,R as X,U as Z,k as de,W as ue,m as z,X as A,Y as ve,s as T,t as j,v as B,Z as ee,$ as pe,a0 as me,a1 as fe,a2 as ge,A as he,B as ye,a3 as be,a4 as Ne,a5 as ke,a6 as xe,K as Ce,G as q,S as $e}from"./main-CAYDRFw-.js";import Ve from"./NavbarThemeSwitcher-CCB8Augi.js";import _e from"./NavBarNotifications-DehFN8Oj.js";import we from"./UserProfile-DlgnZQnc.js";import{c as Ie,a as te,_ as Se}from"./I18n-BfDANAEz.js";const Ae=Y({name:"TransitionExpand",setup(t,{slots:n}){const c=i=>{const $=getComputedStyle(i).width;i.style.width=$,i.style.position="absolute",i.style.visibility="hidden",i.style.height="auto";const f=getComputedStyle(i).height;i.style.width="",i.style.position="",i.style.visibility="",i.style.height="0px",getComputedStyle(i).height,requestAnimationFrame(()=>{i.style.height=f})},o=i=>{i.style.height="auto"},a=i=>{const $=getComputedStyle(i).height;i.style.height=$,getComputedStyle(i).height,requestAnimationFrame(()=>{i.style.height="0px"})};return()=>x(x(K),{name:"expand",onEnter:c,onAfterEnter:o,onLeave:a},()=>{var i;return(i=n.default)==null?void 0:i.call(n)})}}),Re=E(Ae,[["__scopeId","data-v-2683bda9"]]),je=t=>(X("data-v-87828f16"),t=t(),Z(),t),Le={class:"nav-header"},Oe=je(()=>b("div",{class:"vertical-nav-items-shadow"},null,-1)),Te={__name:"VerticalNav",props:{tag:{type:null,required:!1,default:"aside"},navItems:{type:null,required:!0},isOverlayNavActive:{type:Boolean,required:!0},toggleIsOverlayNavActive:{type:Function,required:!0}},setup(t){const n=t,c=V(),o=le(c);re(U,o);const a=P(),i=g=>"heading"in g?Fe:"children"in g?Ge:ae,$=F();R(()=>$.name,()=>{n.toggleIsOverlayNavActive(!1)});const f=V(!1),y=g=>f.value=g,N=g=>{f.value=g.target.scrollTop>0};return a.isVerticalNavMini(o),(g,k)=>{const d=G("RouterLink");return l(),u(h(n.tag),{ref_key:"refNav",ref:c,class:_(["layout-vertical-nav",[{"overlay-nav":e(a).isLessThanOverlayNavBreakpoint,hovered:e(o),visible:t.isOverlayNavActive,scrolled:e(f)}]])},{default:r(()=>[b("div",Le,[M(g.$slots,"nav-header",{},()=>[v(d,{to:"/",class:"app-logo app-title-wrapper"},{default:r(()=>[e(a).isVerticalNavCollapsed?S("",!0):(l(),u(e(oe),{key:0,nodes:e(p).app.logo},null,8,["nodes"]))]),_:1}),w((l(),u(h(e(p).app.iconRenderer||"div"),C({class:["header-action d-none nav-unpin",e(a).isVerticalNavCollapsed&&"d-lg-block"]},e(p).icons.verticalNavUnPinned,{onClick:k[0]||(k[0]=s=>e(a).isVerticalNavCollapsed=!e(a).isVerticalNavCollapsed)}),null,16,["class"])),[[I,e(a).isVerticalNavCollapsed]]),w((l(),u(h(e(p).app.iconRenderer||"div"),C({class:["header-action d-none nav-pin",!e(a).isVerticalNavCollapsed&&"d-lg-block"]},e(p).icons.verticalNavPinned,{onClick:k[1]||(k[1]=s=>e(a).isVerticalNavCollapsed=!e(a).isVerticalNavCollapsed)}),null,16,["class"])),[[I,!e(a).isVerticalNavCollapsed]]),(l(),u(h(e(p).app.iconRenderer||"div"),C({class:"header-action d-lg-none"},e(p).icons.close,{onClick:k[2]||(k[2]=s=>t.toggleIsOverlayNavActive(!1))}),null,16))],!0)]),M(g.$slots,"before-nav-items",{},()=>[Oe],!0),M(g.$slots,"nav-items",{updateIsVerticalNavScrolled:y},()=>[(l(),u(e(ce),{key:e(a).isAppRTL,tag:"ul",class:"nav-items",options:{wheelPropagation:!1},onPsScrollY:N},{default:r(()=>[(l(!0),L(J,null,Q(t.navItems,(s,m)=>(l(),u(h(i(s)),{key:m,item:s},null,8,["item"]))),128))]),_:1}))],!0)]),_:3},8,["class"])}}},Be=E(Te,[["__scopeId","data-v-87828f16"]]),Pe={class:"nav-group-children"},Ge=Object.assign({name:"VerticalNavGroup"},{__name:"VerticalNavGroup",props:{item:{type:null,required:!0}},setup(t){const n=t,c=F(),o=de(),a=P(),i=a.isVerticalNavMini(),$=ue(U,V(!1)),f=V(!1),y=V(!1),N=d=>d.some(s=>{let m=A.value.includes(s.title);return"children"in s&&(m=N(s.children)||m),m}),g=d=>{d.forEach(s=>{"children"in s&&g(s.children),A.value=A.value.filter(m=>m!==s.title)})};R(()=>c.path,()=>{const d=z(n.item.children,o);y.value=d&&!a.isVerticalNavMini($).value,f.value=d},{immediate:!0}),R(y,d=>{const s=A.value.indexOf(n.item.title);d&&s===-1?A.value.push(n.item.title):!d&&s!==-1&&(A.value.splice(s,1),g(n.item.children))},{immediate:!0}),R(A,d=>{if(d.at(-1)===n.item.title)return;const m=z(n.item.children,o);m||N(n.item.children)||(y.value=m,f.value=m)},{deep:!0}),R(a.isVerticalNavMini($),d=>{y.value=d?!1:f.value});const k=ve();return(d,s)=>e(Ie)(t.item)?(l(),L("li",{key:0,class:_(["nav-group",[{active:e(f),open:e(y),disabled:t.item.disable}]])},[b("div",{class:"nav-group-label",onClick:s[0]||(s[0]=m=>y.value=!e(y))},[(l(),u(h(e(p).app.iconRenderer||"div"),C(t.item.icon||e(p).verticalNav.defaultNavItemIconProps,{class:"nav-item-icon"}),null,16)),(l(),u(h(e(k)?ee:"div"),C({name:"transition-slide-x"},e(k)?void 0:{class:"d-flex align-center flex-grow-1"}),{default:r(()=>[w((l(),u(h(e(p).app.i18n.enable?"i18n-t":"span"),C(e(T)(t.item.title,"span"),{key:"title",class:"nav-item-title"}),{default:r(()=>[j(B(t.item.title),1)]),_:1},16)),[[I,!e(i)]]),t.item.badgeContent?w((l(),u(h(e(p).app.i18n.enable?"i18n-t":"span"),C({key:0},e(T)(t.item.badgeContent,"span"),{key:"badge",class:["nav-item-badge",t.item.badgeClass]}),{default:r(()=>[j(B(t.item.badgeContent),1)]),_:1},16,["class"])),[[I,!e(i)]]):S("",!0),w((l(),u(h(e(p).app.iconRenderer||"div"),C(e(p).icons.chevronRight,{key:"arrow",class:"nav-group-arrow"}),null,16)),[[I,!e(i)]])]),_:1},16))]),v(e(Re),null,{default:r(()=>[w(b("ul",Pe,[(l(!0),L(J,null,Q(t.item.children,m=>(l(),u(h("children"in m?"VerticalNavGroup":e(ae)),{key:m.title,item:m},null,8,["item"]))),128))],512),[[I,e(y)]])]),_:1})],2)):S("",!0)}}),Me=Y({props:{navItems:{type:Array,required:!0},verticalNavAttrs:{type:Object,default:()=>({})}},setup(t,{slots:n}){const{width:c}=pe(),o=P(),a=V(!1),i=V(!1),$=me(a);return fe(a,i),R(c,()=>{!o.isLessThanOverlayNavBreakpoint&&i.value&&(i.value=!1)}),()=>{var D,H,W;const f=ge(t,"verticalNavAttrs"),{wrapper:y,wrapperProps:N,...g}=f.value,k=x(Be,{isOverlayNavActive:a.value,toggleIsOverlayNavActive:$,navItems:t.navItems,...g},{"nav-header":()=>{var O;return(O=n["vertical-nav-header"])==null?void 0:O.call(n)},"before-nav-items":()=>{var O;return(O=n["before-vertical-nav-items"])==null?void 0:O.call(n)}}),d=x("header",{class:["layout-navbar",{"navbar-blur":o.isNavbarBlurEnabled}]},[x("div",{class:"navbar-content-container"},(D=n.navbar)==null?void 0:D.call(n,{toggleVerticalOverlayNavActive:$}))]),s=x("main",{class:"layout-page-content"},x("div",{class:"page-content-container"},(H=n.default)==null?void 0:H.call(n))),m=x("footer",{class:"layout-footer"},[x("div",{class:"footer-content-container"},(W=n.footer)==null?void 0:W.call(n))]),ie=x("div",{class:["layout-overlay",{visible:i.value}],onClick:()=>{i.value=!i.value}});return x("div",{class:["layout-wrapper",...o._layoutClasses]},[y?x(y,N,{default:()=>k}):k,x("div",{class:"layout-content-wrapper"},[d,s,m]),ie])}}}),ae={__name:"VerticalNavLink",props:{item:{type:null,required:!0}},setup(t){const c=P().isVerticalNavMini();return(o,a)=>e(te)(t.item.action,t.item.subject)?(l(),L("li",{key:0,class:_(["nav-link",{disabled:t.item.disable}])},[(l(),u(h(t.item.to?"RouterLink":"a"),C(e(ye)(t.item),{class:{"router-link-active router-link-exact-active":e(he)(t.item,o.$router)}}),{default:r(()=>[(l(),u(h(e(p).app.iconRenderer||"div"),C(t.item.icon||e(p).verticalNav.defaultNavItemIconProps,{class:"nav-item-icon"}),null,16)),v(ee,{name:"transition-slide-x"},{default:r(()=>[w((l(),u(h(e(p).app.i18n.enable?"i18n-t":"span"),C({key:"title",class:"nav-item-title"},e(T)(t.item.title,"span")),{default:r(()=>[j(B(t.item.title),1)]),_:1},16)),[[I,!e(c)]]),t.item.badgeContent?w((l(),u(h(e(p).app.i18n.enable?"i18n-t":"span"),C({key:"badge",class:["nav-item-badge",t.item.badgeClass]},e(T)(t.item.badgeContent,"span")),{default:r(()=>[j(B(t.item.badgeContent),1)]),_:1},16,["class"])),[[I,!e(c)]]):S("",!0)]),_:1})]),_:1},16,["class"]))],2)):S("",!0)}},qe={key:0,class:"nav-section-title"},Ee={class:"title-wrapper"},Fe={__name:"VerticalNavSectionTitle",props:{item:{type:null,required:!0}},setup(t){const c=P().isVerticalNavMini();return(o,a)=>e(te)(t.item.action,t.item.subject)?(l(),L("li",qe,[b("div",Ee,[v(K,{name:"vertical-nav-section-title",mode:"out-in"},{default:r(()=>[(l(),u(h(e(c)?e(p).app.iconRenderer:e(p).app.i18n.enable?"i18n-t":"span"),C({key:e(c),class:e(c)?"placeholder-icon":"title-text"},{...e(p).icons.sectionTitlePlaceholder,...e(T)(t.item.heading,"span")}),{default:r(()=>[j(B(e(c)?null:t.item.heading),1)]),_:1},16,["class"]))]),_:1})])])):S("",!0)}},De=[{title:"Home",to:{name:"root"},icon:{icon:"tabler-align-box-bottom-center"}},{title:"Projects",icon:{icon:"tabler-chart-histogram"},to:{name:"web-designs-list"}},{title:"Members",icon:{icon:"tabler-users"},to:{name:"members-list"}},{title:"Logout",icon:{icon:"tabler-logout"}}],ne=t=>(X("data-v-97cf0426"),t=t(),Z(),t),He={class:"d-flex h-100 align-center"},We={key:0,class:"d-flex align-center"},ze=ne(()=>b("span",{class:"inner-badge-text"},"Inbox",-1)),Ye=ne(()=>b("span",{class:"text-h6 font-weight-bold me-4"},[j("Darby Bucks "),b("span",{class:"text-primary"},"$200")],-1)),Ke={__name:"DefaultLayoutWithVerticalNav",setup(t){const n=V(!1),c=V(null),o=F(),a=V(null);V(null),be(()=>{a.value=o.params.id}),R([n,c],()=>{n.value&&c.value&&c.value.fallbackHandle(),!n.value&&c.value&&c.value.resolveHandle()},{immediate:!0});const i=Ne(()=>o.params.id!==void 0);return($,f)=>{const y=G("IconBtn"),N=G("RouterLink"),g=se,k=G("RouterView");return l(),u(e(Me),{"nav-items":e(De)},{navbar:r(({toggleVerticalOverlayNavActive:d})=>{var s;return[b("div",He,[v(y,{id:"vertical-nav-toggle-btn",class:"ms-n3 d-lg-none",onClick:m=>d(!0)},{default:r(()=>[v(ke,{size:"26",icon:"tabler-menu-2"})]),_:2},1032,["onClick"]),v(Ve),e(i)?(l(),L("div",We,[v(N,{to:`/projects/web-designs/${e(a)}`},{default:r(()=>[b("span",{class:_(["text-h6 ms-3 me-5 inner-badge-text",{"text-primary":e(o).path===`/projects/web-designs/${e(a)}`}])},"Overview",2)]),_:1},8,["to"]),v(N,{to:`/projects/${e(a)}/chat`},{default:r(()=>[b("span",{class:_(["text-h6 me-5 inner-badge-text",{"text-primary":e(o).path===`/projects/${e(a)}/chat`}])},[v(xe,{class:"new-badge",color:"error",content:"14"},{default:r(()=>[ze]),_:1})],2)]),_:1},8,["to"]),v(N,{to:`/projects/${e(a)}/files`},{default:r(()=>[b("span",{class:_(["text-h6 me-8 inner-badge-text",{"text-primary":e(o).path===`/projects/${e(a)}/files`}])},"Files",2)]),_:1},8,["to"]),v(N,{to:`/projects/${e(a)}/tasks/add`},{default:r(()=>[b("span",{class:_(["text-h6 me-8 inner-badge-text",{"text-primary":e(o).path===`/projects/${e(a)}/tasks/add`}])}," Tasks ",2)]),_:1},8,["to"]),v(N,{to:`/projects/${e(a)}/milestones`,class:_(["text-h6 me-8 inner-badge-text",{"text-primary":e(o).path===`/projects/${e(a)}/milestones`}])},{default:r(()=>[j(" Milestones ")]),_:1},8,["to","class"]),v(N,{to:`/projects/${e(a)}/calendar`},{default:r(()=>[b("span",{class:_(["text-h6 me-8 inner-badge-text",{"text-primary":e(o).path===`/projects/${e(a)}/calendar`}])},"Calendar",2)]),_:1},8,["to"]),v(N,{to:`/projects/${e(a)}/team`},{default:r(()=>[b("span",{class:_(["text-h6 me-8 inner-badge-text",{"text-primary":e(o).path===`/projects/${e(a)}/team`}])},"Your Team",2)]),_:1},8,["to"])])):S("",!0),v(Ce),e(q).app.i18n.enable&&((s=e(q).app.i18n.langConfig)!=null&&s.length)?(l(),u(Se,{key:1,languages:e(q).app.i18n.langConfig},null,8,["languages"])):S("",!0),Ye,v(_e,{class:"me-2"}),v(we)])]}),default:r(()=>[v(g,{ref_key:"refLoadingIndicator",ref:c},null,512),v(k,null,{default:r(({Component:d})=>[(l(),u($e,{timeout:0,onFallback:f[0]||(f[0]=s=>n.value=!0),onResolve:f[1]||(f[1]=s=>n.value=!1)},{default:r(()=>[(l(),u(h(d)))]),_:2},1024))]),_:1})]),_:1},8,["nav-items"])}}},tt=E(Ke,[["__scopeId","data-v-97cf0426"]]);export{tt as default};