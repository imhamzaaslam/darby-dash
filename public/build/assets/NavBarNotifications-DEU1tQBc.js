import{k as M,ac as C,a as S,o as f,f as w,e as s,b as a,ag as R,q,V as h,L as I,ah as L,ai as P,I as v,aj as D,t as p,v as u,n as _,J as g,ak as F,al as O,am as V,Z as j,M as J,c as N,F as T,i as Q,y as U,N as A,d,an as W,ao as Z,x as G,ap as H,Q as K,O as X,aq as Y,ar as ee,E as ae,as as te,at as se}from"./main-DkriqGAW.js";const oe={class:"d-flex align-start gap-3"},ie={key:0},le={class:"d-flex align-items-center mb-1"},ne={class:"text-sm font-weight-medium d-flex align-items-center mb-0"},re={class:"text-primary"},ce={class:"text-body-2 mb-2",style:{"letter-spacing":"0.4px !important","line-height":"18px"}},de={class:"text-sm text-disabled mb-0",style:{"letter-spacing":"0.4px !important","line-height":"18px"}},me={__name:"Notifications",props:{notifications:{type:Array,required:!0},badgeProps:{type:Object,required:!1,default:void 0},location:{type:null,required:!1,default:"bottom end"}},emits:["read","unread","remove","click:notification"],setup(z,{emit:x}){const o=z,r=x,k=M(),m=C(()=>o.notifications.some(e=>e.read_at===null)),b=async e=>{if(r("click:notification",e),e.url){const n=e.url.startsWith("/")?e.url:`/${e.url}`;k.push(n)}},y=()=>{const e=o.notifications.map(n=>n.id);m.value?r("read",e):r("unread",e)},c=C(()=>o.notifications.filter(e=>e.read_at===null).length),i=(e,n)=>{e?r("unread",[n]):r("read",[n])},l=e=>`https://darby-dash.sublimelogics.com/storage/${e}`;return(e,n)=>{const $=S("IconBtn");return f(),w($,{id:"notification-btn"},{default:s(()=>[a(R,q(o.badgeProps,{"model-value":o.notifications.some(t=>!t.read_at),color:"primary",dot:"","offset-x":"2","offset-y":"3",class:"animated-badge"}),{default:s(()=>[a(h,{size:"24",icon:"tabler-bell"})]),_:1},16,["model-value"]),a(I,{activator:"parent",width:"380px",location:o.location,offset:"12px","close-on-content-click":!1},{default:s(()=>[a(L,{class:"d-flex flex-column"},{default:s(()=>[a(P,{class:"notification-section"},{append:s(()=>[v(a(D,{size:"small",color:"primary",class:"me-2"},{default:s(()=>[p(u(_(c))+" New ",1)]),_:1},512),[[g,o.notifications.some(t=>!t.read_at)]]),v(a($,{size:"34",onClick:y},{default:s(()=>[a(h,{size:"20",color:"high-emphasis",icon:_(m)?"tabler-mail-opened":"tabler-mail"},null,8,["icon"]),a(F,{activator:"parent",location:"start"},{default:s(()=>[p(u(_(m)?"Mark all as read":"Mark all as unread"),1)]),_:1})]),_:1},512),[[g,o.notifications.length]])]),default:s(()=>[a(O,{class:"text-h6"},{default:s(()=>[p(" Notifications ")]),_:1})]),_:1}),a(V),a(_(j),{options:{wheelPropagation:!1},style:{"max-block-size":"23.75rem"}},{default:s(()=>[a(J,{class:"notification-list rounded-0 py-0"},{default:s(()=>[(f(!0),N(T,null,Q(o.notifications,(t,E)=>(f(),N(T,{key:t.title},[E>0?(f(),w(V,{key:0})):U("",!0),a(A,{link:"",lines:"one","min-height":"66px",class:"list-item-hover-class",onClick:B=>b(t)},{default:s(()=>[d("div",oe,[a(R,{dot:"",location:"top end","offset-x":"0","offset-y":"1",color:t.is_online?"success":"warning"},{default:s(()=>[a(W,{size:"34",color:"primary",image:t.img?l(t.img.path):void 0,variant:t.img?void 0:"tonal"},{default:s(()=>[t.img?U("",!0):(f(),N("span",ie,u(("avatarText"in e?e.avatarText:_(Z))(t.name)),1))]),_:2},1032,["image","variant"])]),_:2},1032,["color"]),d("div",null,[d("div",le,[d("p",ne,[d("span",re,u(t.title),1),a(h,{size:"10",icon:"tabler-circle-filled",color:t.read_at?"#a8aaae":"primary",class:G([`${t.read_at?"visible-in-hover":""}`,"ms-1 mt-1"]),onClick:H(B=>i(t.read_at,t.id),["stop"])},null,8,["color","class","onClick"])]),a(h,{size:"20",icon:"tabler-x",class:"ms-auto visible-in-hover",style:{position:"relative",left:"35px",bottom:"3px"},onClick:B=>e.$emit("remove",t.id)},null,8,["onClick"])]),d("p",ce,u(t.subtitle),1),d("p",de,u(t.time),1)]),a(K)])]),_:2},1032,["onClick"])],64))),128)),v(a(A,{class:"text-center text-medium-emphasis",style:{"block-size":"56px"}},{default:s(()=>[a(X,null,{default:s(()=>[p("No Notification Found!")]),_:1})]),_:1},512),[[g,!o.notifications.length]])]),_:1})]),_:1}),a(V),v(a(Y,{class:"pa-4"},{default:s(()=>[a(ee,{block:"",size:"small",to:{path:"/view/received/notifications"}},{default:s(()=>[p(" View All Notifications ")]),_:1})]),_:1},512),[[g,o.notifications.length]])]),_:1})]),_:1},8,["location"])]),_:1})}}},fe={__name:"NavBarNotifications",setup(z){ae(async()=>{await r()});const x=te(),o=se(),r=async()=>{try{await o.getAll()}catch(i){x.error("Error fetching notifications:",i)}},k=i=>{c.value.forEach((l,e)=>{i===l.id&&c.value.splice(e,1),o.delete(i)})},m=i=>{c.value.forEach(l=>{i.forEach(e=>{e===l.id&&o.markAsRead({ids:i})})})},b=i=>{c.value.forEach(l=>{i.forEach(e=>{e===l.id&&o.markAsUnread({ids:i})})})},y=i=>{i.read_at||m([i.id])},c=C(()=>o.getNotifications);return(i,l)=>{const e=me;return f(),w(e,{notifications:c.value,onRemove:k,onRead:m,onUnread:b,"onClick:notification":y},null,8,["notifications"])}}};export{fe as default};