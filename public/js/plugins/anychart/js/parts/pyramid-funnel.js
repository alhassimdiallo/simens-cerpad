if(!_.pyramid_funnel){_.pyramid_funnel=1;(function($){var EV=function(a,b,c){$.Sx.call(this,null,[],[],b,c);this.za=a},GV=function(a,b,c){$.Sx.call(this,null,[],[],b,c);this.za=a;this.g=!FV(this.za);this.Uy=this.za.i("connectorStroke")},HV=function(a,b,c){$.Wv.call(this);$.U(this);this.Zd=a;this.Ia("pieFunnelPyramidBase",a);this.Vc=null;this.D=[];this.oe=null;this.ga=1;this.o=this.Aa=null;this.state=new $.Cv(this);this.data(b||null,c);a={};$.R(a,[["fill",528,1],["stroke",528,1],["hatchFill",528,1],["labels",0,0],["markers",0,0]]);this.ca=new $.Ew(this,
a,$.Wk);this.ca.pa("labelsAfterInitCallback",function(a){$.L(a,this.Fd,this);a.qb(this);this.u(4096,1)});this.ca.pa("markersAfterInitCallback",$.Lw);a={};$.R(a,[["fill",16,1],["stroke",16,1],["hatchFill",0,0],["labels",0,0],["markers",0,0]]);this.ya=new $.Ew(this,a,$.ln);this.ya.pa("labelsFactoryConstructor",$.Gw);this.Da=new $.Ew(this,a,$.mn);this.Da.pa("labelsFactoryConstructor",$.Gw);$.R(this.xa,[["baseWidth",16,1],["neckHeight",16,1],["neckWidth",16,1],["pointsPadding",16,1],["reversed",16,1],
["overlapMode",4096,1],["connectorLength",4100,9],["connectorStroke",16,1]]);this.da(!1)},IV=function(a,b){var c=a.aa().j("point");if($.p(c)){var d=$.Sk("fill",1,!0)(a,b,!1,!0);c.fill(d);d=$.Sk("stroke",2,!0)(a,b,!1,!0);c.stroke(d)}},JV=function(a,b){var c=a.aa().j("hatchPoint");if(null!=c){var d=$.Sk("hatchFill",3,!0)(a,b,!1);c.stroke(null).fill(d)}},KV=function(a,b){var c=a.G,d=a.f.height,e=a.Za,f=a.va;return b>d-f||d==f?e:e+(d-f-b)/(d-f)*(c-e)},LV=function(a){a=$.N(a);return 0>=a||!(0,window.isFinite)(a)},
MV=function(a){var b=a.aa(),c=b.ka(),d=a.f,e;var f=b.j("height")/2;var h=b.j("startY");var k=b.j("height")+h;var l=null;if(e=a.Ja)c?c==b.Eb()-1?(h+=e/2,h>k&&(h=k-a.ga)):(h+=e/2,k-=e/2,h>k&&(h=b.j("startY")+f,k=h+a.ga)):(k-=e/2,k<h&&(k=a.ga));var m=KV(a,h);c=a.b-m/2;f=c+m;m=KV(a,k);e=a.b-m/2;m=e+m;h+=d.top;k+=d.top;c=d.left+c;f=d.left+f;0<a.va&&h<a.Ea&&k>a.Ea&&(l=k,k=a.Ea,m=KV(a,k),e=a.b-m/2,m=e+m);e=d.left+e;m=d.left+m;a.i("reversed")||(h=d.height-(h-d.top)+d.top,k=d.height-(k-d.top)+d.top,l=l?d.height-
(l-d.top)+d.top:null,h=[k,k=h][0],c=[e,e=c][0],f=[m,m=f][0]);b.j("x1",c);b.j("x2",f);b.j("x3",e);b.j("x4",m);b.j("y1",h);b.j("y2",k);b.j("y3",l)},NV=function(a,b,c,d){var e=a.labels();$.U(e);e.fontOpacity(b);e.X();e.da(!1);if(d&&a.P)for(b=0;b<a.P.length;b++)if(d=a.P[b])e=a.i("connectorStroke"),e=$.Ck(e,c),d.stroke(e)},hga=function(a){var b=a.aa(),c=b.ka(),d=$.lA(a.Xe),e=$.lA(a.g);b.j("point",d);b.j("hatchPoint",e);MV(a);var f=b.j("x1"),h=b.j("x2"),k=b.j("x3"),l=b.j("x4"),m=b.j("y1"),n=b.j("y2"),q=
b.j("y3");d.moveTo(f,m).lineTo(h,m);q?d.lineTo(l,n).lineTo(l,q).lineTo(k,q).lineTo(k,n):d.lineTo(l,n).lineTo(k,n);d.close();b.j("point",d);d.tag={index:c,W:a};b=$.Hv(a.state,b.ka());IV(a,b);e&&(e.Vd(d.F()),e.tag={index:c,W:a},JV(a,b))},RV=function(a,b,c){var d=OV(a),e=a.aa(),f=a.f,h=$.p(c)?!!(c&$.mn):null,k=$.p(c)?!h&&!!(c&$.ln):null,l=e.get("normal");l=$.p(l)?l.label:void 0;var m=e.get("hovered");m=$.p(m)?m.label:void 0;var n=e.get("selected");n=$.p(n)?n.label:void 0;l=$.On(l,e.get("label"));m=k?
$.On(m,e.get("hoverLabel")):null;h=(n=h?$.On(n,e.get("selectLabel")):null)||m||l||{};l=$.N(e.j("x1"));n=$.N(e.j("x2"));m=$.N(e.j("y1"));k=$.N(e.j("y2"));var q=$.N(e.j("y3"));e=n-l;k=q?q-m:k-m;m+=k/2;n=$.N(h.offsetY)||0;b?c=PV(a,b,c):(c=a.labels().Qk(a.Ec(),null,h),c=$.am(c));h=b&&b.i("anchor")||a.labels().i("anchor");b&&(m=b.Cc().value.y);b=m+n;c.height>k&&("left-center"==h||"center"==h||"right-center"==h)&&(m+c.height/2>f.top+f.height&&(m=f.top+f.height-c.height/2),m-c.height/2<f.top&&(m=f.top+c.height/
2));b=QV(a,b);switch(d){case "inside":l+=e/2;break;case "outside-left":l=a.b-b/2;l=f.left+l-a.ba-c.width/2;break;case "outside-left-in-column":l=f.left+c.width/2;break;case "outside-right":l=a.b+b/2;l=f.left+l+a.ba+c.width/2;break;case "outside-right-in-column":l=f.left+f.width-c.width/2}if("left-top"==h||"center-top"==h||"right-top"==h)m-=.5;else if("left-bottom"==h||"center-bottom"==h||"right-bottom"==h)m+=.5;return{value:{x:l,y:m}}},PV=function(a,b,c){var d=!!(c&$.mn),e=!d&&!!(c&$.ln);c=a.data().get(b.ka(),
"label");e=e?a.data().get(b.ka(),"hoverLabel"):null;d=(d?a.data().get(b.ka(),"selectLabel"):null)||e||c||{};a.data().j(b.ka(),"labelWidthForced")&&(d=$.Ic(d),d.width=b.width());a.aa().select(b.ka());b.Nf(a.Ec());a=a.labels().Qk(b.Nf(),b.Cc(),d);return $.am(a)},UV=function(a,b){if("no-overlap"==a.i("overlapMode")&&!FV(a)&&a.labels().enabled()){SV(a);a.Ma=0;var c=a.state.uj()|(b?$.Hv(a.state,b.ka()):0);TV(a,c,b)}},TV=function(a,b,c){if(10!=a.Ma){for(var d=a.aa().Eb(),e=!1,f,h,k,l,m=a.i("reversed"),
n=0;n<d-1;n++)if(f=m?n:d-1-n,(f=a.labels().Wd(f))&&0!=f.enabled()&&(h=PV(a,f,b),k=m?iga(a,f):jga(a,f))){l=PV(a,k,b);var q=VV(a,f),r=VV(a,k);q&&r&&q==r||!(l.top<=h.top+h.height)||(e=!0,q&&r?kga(a,q,r):!q&&r?WV(a,k,f):WV(a,f,k))}e&&((0,$.ue)(a.D,function(a){if(2>a.labels.length){var b=a.za;b.D.length&&(a.clear(),$.Ea(b.D,a))}else{for(var d,e,f=0,h=0,k=b=0,l=a.labels.length;k<l;k++)d=a.labels[k],e=a.za.state.uj()|$.Hv(a.za.state,d.ka()),e=a.dd(d,e),d=a.za.data().j(d.ka(),"point"),d=d.kb(),k||(f=d.top),
b+=e.height,h+=d.height;h+=a.za.Ja*(l-1);f=f+h/2-b/2;h=a.za.f;f+b>h.top+h.height&&(f=h.top+h.height-b);f<h.top&&(f=h.top);a.y=f;lga(a,c)}}),a.Ma++,TV(a,b,c))}},iga=function(a,b){if(!b)return null;var c=a.aa().Eb();if(b.ka()==c-1)return null;for(var d,e=b.ka()+1;e<=c-1;e++)if((d=a.labels().Wd(e))&&!1!==d.enabled())return d;return null},jga=function(a,b){if(!b||0==b.ka())return null;for(var c,d=b.ka()-1;0<=d;d--)if((c=a.labels().Wd(d))&&!1!==c.enabled())return c;return null},WV=function(a,b,c){var d=
VV(a,b);null===d?(d=new XV(a),d.RA(b),d.RA(c),a.D.push(d)):d.RA(c)},VV=function(a,b){return a.D.length?$.va(a.D,function(a){return-1!==(0,$.xa)(a.labels,b)}):null},kga=function(a,b,c){var d=b.labels[0].ka(),e=c.labels[0].ka();b.labels=a.i("reversed")==d<e?$.Fa(b.labels,c.labels):$.Fa(c.labels,b.labels);$.Ea(a.D,c)},SV=function(a){a.D.length&&((0,$.ue)(a.D,function(a){a.clear()}),a.D.length=0)},FV=function(a){return"inside"==OV(a)},YV=function(a){a=OV(a);return"outside-right-in-column"==a||"outside-left-in-column"==
a},ZV=function(a){a=OV(a);return"outside-left"==a||"outside-left-in-column"==a},$V=function(a){a=OV(a);return"outside-right"==a||"outside-right-in-column"==a},mga=function(a){if(a.labels().enabled()&&!FV(a)){MV(a);var b=a.aa();b.j("labelWidthForced",void 0);var c=a.f,d=b.get("label"),e=RV(a),f=a.Ec();f=a.labels().Qk(f,e,d);f=$.am(f);d=f.left;e=f.left+f.width;f=a.i("reversed")?KV(a,f.top-c.top):KV(a,c.height-(f.top+f.height)+c.top);if(ZV(a)){var h=a.b-f/2;h=c.left+h;var k=a.G/2;k=c.width-a.b-k;YV(a)?
e+5>h&&(h=e+5-h,h>k?(a.b+=k,h=a.b-f/2,h=c.left+h,b.j("labelWidthForced",h-5-d)):a.b+=h):d<c.left&&(h=Math.abs(c.left-d),h>k?(a.b+=k,h=a.b-f/2,a=h-a.ba,10>a&&(a=10),b.j("labelWidthForced",a)):a.b+=h)}else if($V(a))if(h=a.b+f/2,h+=c.left,k=a.G/2,k=c.width-(c.width-a.b)-k,YV(a)){if(0>d||d-5<h)h=Math.abs(h-d+5),0>d||h>k?(a.b=a.b-k,h=a.b+f/2,h+=c.left,b.j("labelWidthForced",e-5-h)):a.b=a.b-h}else e>c.left+c.width&&(h=e-(c.left+c.width),h>k?(a.b=a.b-k,a=c.left+c.width-d+k,10>a&&(a=10),b.j("labelWidthForced",
a)):a.b=a.b-h)}},OV=function(a){a=a.labels().i("position");return $.ij(nga,"outside"==a?"outside-left":a,"outside-left-in-column")},aW=function(a,b,c,d){var e=a.f,f=b.ka();f=a.data().j(f,"point").kb();b=PV(a,b,d);d=b.left;var h=b.top+b.height/2;f=f.top+f.height/2;var k=QV(a,f);if(ZV(a)){d+=b.width;var l=a.b-k/2;l+=e.left;d>l&&5>Math.abs(f-h)&&(d=l-5)}else $V(a)&&(l=a.b+k/2,l+=e.left,d<l&&5>Math.abs(f-h)&&(d=l+5));c.clear().moveTo(d,h).lineTo(l,f+.001)},bW=function(a,b,c){var d=b.ka();if(a.P[d])aW(a,
b,a.P[d],c);else{var e=$.lA(a.U);a.P[d]=e;e.stroke(a.i("connectorStroke"));aW(a,b,e,c)}},oga=function(a,b){b=$.mj(b);var c=a.f,d=a.aa(),e=d.j("point").kb(),f=d.j("x1"),h=d.j("y1");switch(b){case "left-top":h=d.j("y1");f=d.j("x1");break;case "left-center":h+=e.height/2;d=QV(a,h);f=a.b-d/2;f+=c.left;break;case "left-bottom":h+=e.height;f=d.j("x3");break;case "center-top":f=a.b;f+=c.left;break;case "center":h+=e.height/2;QV(a,h);f=a.b;f+=c.left;break;case "center-bottom":h+=e.height;QV(a,h);f=a.b;f+=
c.left;break;case "right-top":d=QV(a,h);f+=d;break;case "right-center":h+=e.height/2;d=QV(a,h);f=a.b+d/2;f+=c.left;break;case "right-bottom":f=d.j("x4"),h+=e.height}return{value:{x:f,y:h}}},QV=function(a,b){var c=a.f;return a.i("reversed")?KV(a,b-c.top):KV(a,c.height-b+c.top)},cW=function(a){var b=a.Ya();a.hc("mousemove",a.CI);b.Wc()},XV=function(a){this.za=a;this.labels=[]},lga=function(a,b){var c=0,d=0,e=null,f=null,h=null,k=a.za.state.uj()|(b?$.Hv(a.za.state,b.ka()):0);(0,$.ue)(a.labels,function(b){var l=
b.Cc().value,n=a.dd(b,k),q=a.y+c+d+n.height/2;if(e&&f&&h){var r=h.y+f.height/2+(e.i("offsetY")||0),t=q-n.height/2+(b.i("offsetY")||0);t<r&&(q+=r-t)}b.Cc({value:{x:l.x,y:q}});b.X();bW(a.za,b,k);c+=n.height;d+=b.i("offsetY")||0;e=b;f=n;h={x:l.x,y:q}})},dW=function(a,b){var c=new HV("funnel",a,b);c.pe();return c},eW=function(a,b){var c=new HV("pyramid",a,b);c.pe();return c},nga={SN:"inside",fka:"outside-left",gka:"outside-left-in-column",hka:"outside-right",ika:"outside-right-in-column"};$.H(EV,$.Sx);
$.g=EV.prototype;$.g.update=function(){this.b.length=this.f.length=0;for(var a=this.za.If();a.advance();)if(!a.j("missing")){var b=a.j("x1"),c=a.j("x2"),d=a.j("x3"),e=a.j("x4"),f=a.j("y1"),h=a.j("y2"),k=a.j("y3");a.j("neck",!!k);this.b.push(b,c,d,e,0,0,0);this.f.push(b,c,d,e,f,h,k?k:0)}};$.g.Ov=function(){NV(this.za,1E-5,1E-5,!FV(this.za))};
$.g.Am=function(){for(var a=this.za.If(),b=0;a.advance();)if(!a.j("missing")){a.j("x1",this.coords[b++]);a.j("x2",this.coords[b++]);a.j("x3",this.coords[b++]);a.j("x4",this.coords[b++]);a.j("y1",this.coords[b++]);a.j("y2",this.coords[b++]);a.j("y3",this.coords[b++]);var c=this.za,d=a,e=d.j("point");e.clear();var f=d.j("x1"),h=d.j("x2"),k=d.j("x3"),l=d.j("x4"),m=d.j("y1"),n=d.j("y2"),q=d.j("y3");e.moveTo(f,m).lineTo(h,m);d.j("neck")?e.lineTo(l,n).lineTo(l,q).lineTo(k,q).lineTo(k,n):e.lineTo(l,n).lineTo(k,
n);e.close();if(f=d.j("hatchPoint"))c.aa().select(d.ka()),f.clear(),f.Vd(e.F()),d=$.Hv(c.state,d.ka()),e=$.Sk("hatchFill",3,!0),f.stroke(null).fill(e(c,d,!1))}};$.g.Al=function(){this.Am()};$.g.R=function(){EV.B.R.call(this);this.za=null};$.H(GV,$.Sx);GV.prototype.update=function(){this.b.length=this.f.length=0;this.b.push(1E-5,1E-5);this.f.push(1,this.Uy.opacity||1)};GV.prototype.Am=function(){NV(this.za,this.coords[0],this.coords[1],this.g)};GV.prototype.Al=function(){this.Am()};GV.prototype.R=function(){GV.B.R.call(this);this.Uy=this.za=null;delete this.g};$.H(HV,$.Wv);$.rp(HV,["fill","stroke","hatchFill"],"normal");$.g=HV.prototype;$.g.Ta=function(a){return $.p(a)?(this.ca.K(a),this):this.ca};$.g.lb=function(a){return $.p(a)?(this.ya.K(a),this):this.ya};$.g.selected=function(a){return $.p(a)?(this.Da.K(a),this):this.Da};$.g.ra=$.Wv.prototype.ra|16;$.g.qa=$.Wv.prototype.qa|28688;$.g.Ne=function(){return[this]};$.g.Cg=function(){return!0};$.g.Qj=function(){return!1};$.g.vi=function(){return!0};
$.g.data=function(a,b){if($.p(a)){if(a){var c=a.title||a.caption;c&&this.title(c);a.rows&&(a=a.rows)}if(this.Vf!==a){this.Vf=a;if(this.o!=a||null===a){$.hd(this.ed);delete this.Md;if($.K(a,$.oq)){var d=a;this.ed=null}else $.K(a,$.yq)?d=(this.ed=a).Yd():d=$.B(a)||$.z(a)?(this.ed=new $.yq(a,b)).Yd():(this.ed=new $.yq(null)).Yd();this.o=d.$i()}$.hd(this.la);this.la=this.o;$.L(this.la,this.xd,this);this.u(29456,17)}return this}return this.la};$.g.xd=function(a){$.W(a,16)&&this.u(29456,17)};
$.g.aa=function(){return this.Md||(this.Md=this.la.aa())};$.g.wc=function(){return this.Md=this.la.aa()};$.g.If=function(){return this.la.aa()};$.g.Yb=function(a){if($.K(a,$.xr))return this.Hc($.xr,a),this;if($.K(a,$.ur))return this.Hc($.ur,a),this;$.D(a)&&"range"==a.type?this.Hc($.xr):($.D(a)||null==this.Aa)&&this.Hc($.ur);return $.p(a)?(this.Aa.K(a),this):this.Aa};
$.g.lf=function(a){this.oe||(this.oe=new $.wr,$.V(this,"markerPalette",this.oe),$.L(this.oe,this.HG,this));return $.p(a)?(this.oe.K(a),this):this.oe};$.g.be=function(a){this.Vc||(this.Vc=new $.vr,$.V(this,"hatchFillPalette",this.Vc),$.L(this.Vc,this.ZF,this));return $.p(a)?(this.Vc.K(a),this):this.Vc};$.g.Hc=function(a,b){if($.K(this.Aa,a))b&&this.Aa.K(b);else{var c=!!this.Aa;$.hd(this.Aa);this.Aa=new a;b&&this.Aa.K(b);$.L(this.Aa,this.Df,this);c&&this.u(528,1)}};
$.g.Df=function(a){$.W(a,2)&&this.u(528,1)};$.g.HG=function(a){$.W(a,2)&&this.u(528,1)};$.g.ZF=function(a){$.W(a,2)&&this.u(528,1)};$.g.remove=function(){SV(this);this.Xe&&this.Xe.parent(null);HV.B.remove.call(this)};$.g.vu=function(){var a=this.aa();this.Aa&&$.K(this.Aa,$.xr)&&$.yr(this.Aa,a.Eb())};
$.g.Ji=function(a){if(!this.zf()){this.mb();var b=this.aa();this.J(4)&&this.u(4112);if(this.J(16)){this.Xe?this.Xe.clear():(this.Xe=new $.kA(function(){return $.aj()},function(a){a.clear()}),this.Xe.zIndex(30),this.Xe.parent(this.Ua));this.g?this.g.clear():(this.g=new $.kA(function(){return $.aj()},function(a){a.clear()}),this.g.parent(this.Ua),this.g.zIndex(31).td(!0));this.Ja=Math.abs($.Kl($.M(this.i("pointsPadding"),a.height),2));this.G=Math.abs($.Kl($.M(this.i("baseWidth"),a.width),2));this.Za=
Math.abs($.Kl($.M(this.i("neckWidth"),a.width),2));this.va=Math.abs($.Kl($.M(this.i("neckHeight"),a.height),2));this.Ea=a.top+a.height-this.va;this.b=a.width/2;this.ba=$.M(this.i("connectorLength"),(a.width-this.G)/2);0>this.ba&&(this.ba=5);this.f=a;var c=0,d=b.Eb()-$.N(this.Fa("count")),e=$.Kl(this.Ja/a.height*100,2);for(b.reset();b.advance();){var f=b.get("value");var h=LV(f);f=LV(f)?0:$.N(f);var k=$.Kl(f/$.N(this.Fa("sum"))*100,2);h&&(k=e);k=$.Kl(a.height/(100+d*e)*k,2);k||(k=this.ga);b.j("value",
f);b.j("height",k);b.j("startY",c);b.j("missing",h);c+=k;mga(this)}for(b.reset();b.advance();)c=b.ka(),"selected"==String(b.get("state")).toLowerCase()&&this.state.oh($.mn,c),hga(this);if(this.P)for(c=0;c<this.P.length;c++)(f=this.P[c])&&f.stroke(this.i("connectorStroke"));this.u(4096);this.u(8192);this.I(16)}if(this.J(8192)){this.Gb().O()||this.Gb().O(this.Ua);this.Gb().clear();for(b.reset();b.advance();)this.rp(this.state.vc|$.Hv(this.state,b.ka()));this.Gb().X();this.I(8192)}if(this.J(4096)){this.labels().O()||
this.labels().O(this.Ua);this.labels().clear();this.U&&this.U.clear();c=FV(this)?this.ma.insideLabels:this.ma.outsideLabels;this.labels().Wp(c.autoColor);this.labels().disablePointerEvents(c.disablePointerEvents);FV(this)||(this.ba=$.M(this.i("connectorLength"),(a.width-this.G)/2),0>this.ba&&(this.ba=5),this.U?this.U.clear():(this.U=new $.kA(function(){return $.aj()},function(a){a.clear()}),this.U.parent(this.Ua),this.U.zIndex(32)),this.U.clip(a),this.P=[]);for(b.reset();b.advance();)FV(this)&&b.j("labelWidthForced",
void 0),this.Qd(this.state.vc|$.Hv(this.state,b.ka()));UV(this);this.labels().X();this.labels().Kd().clip(a);this.I(4096)}}};
$.g.HJ=function(){var a=$.hq(this,"animation");if(a&&a.i("enabled")&&0<a.i("duration"))if(this.jg&&1==this.jg.oc)this.jg.update();else if(this.J(2048)){$.hd(this.jg);this.jg=new $.iA;var b=a.i("duration");a=b*(1-.85);b=new EV(this,.85*b);a=new GV(this,a);this.jg.add(b);this.jg.add(a);this.jg.wa("begin",function(){this.yj=this.i("connectorStroke");this.pa("connectorStroke","none");$.Pv(this,!0);$.iq(this,{type:"animationstart",chart:this})},!1,this);this.jg.wa("end",function(){this.pa("connectorStroke",
this.yj);$.Pv(this,!1);$.iq(this,{type:"animationend",chart:this})},!1,this);this.jg.play(!1)}};$.g.Tf=function(a){a=$.X.prototype.Tf.call(this,a);var b=$.hn(a.domTarget);a.pointIndex=$.N(b.index);return a};$.g.ih=function(a){(a=this.tg(a))&&this.dispatchEvent(a)};
$.g.tg=function(a){var b;"pointIndex"in a?b=a.pointIndex:"labelIndex"in a?b=a.labelIndex:"markerIndex"in a&&(b=a.markerIndex);b=$.N(b);a.pointIndex=b;var c=a.type;switch(c){case "mouseout":c="pointmouseout";break;case "mouseover":c="pointmouseover";break;case "mousemove":c="pointmousemove";break;case "mousedown":c="pointmousedown";break;case "mouseup":c="pointmouseup";break;case "click":c="pointclick";break;case "dblclick":c="pointdblclick";break;default:return null}var d=this.data().aa();d.select(b)||
d.reset();return{type:c,actualTarget:a.target,iterator:d,sliceIndex:b,pointIndex:b,target:this,originalEvent:a,point:this.Ad(b)}};$.g.Ad=function(a){var b=new $.Vy(this,a),c;this.aa().select(a)&&b.Yw()&&!LV(c=b.get("value"))&&(a=$.Kl(c/this.pg("sum")*100,2),b.Fa("percentValue",a),b.Fa("yPercentOfTotal",a));return b};$.g.Hr=function(){return[]};$.g.xj=function(a){$.p(a)?this.ti(a):this.nk();return this};
$.g.Cd=function(a){var b;(b=$.Gv(this.state,$.ln))||(b=!!(this.state.uj()&$.ln));if(b&&this.enabled()){var c;$.p(a)?c=a:c=this.state.vc==$.Wk?window.NaN:void 0;this.state.xh($.ln,c);a=this.aa();for(a.reset();a.advance();)this.Qd($.Hv(this.state,a.ka()));UV(this);cW(this)}};
$.g.ti=function(a,b){if(!this.enabled())return this;if($.B(a)){var c=$.Mv(this.state,$.ln);for(var d=0;d<c.length;d++)$.ya(a,c[d])||this.state.xh($.ln,c[d]);$.Kv(this.state,a);$.p(b)&&this.CI(b);for(c=this.wc();c.advance();)this.Qd($.Hv(this.state,c.ka()));UV(this)}else if($.ea(a)&&(this.Cd(),$.Kv(this.state,a),$.p(b)&&this.CI(b),this.f)){for(c=this.wc();c.advance();)this.Qd($.Hv(this.state,c.ka()));UV(this,this.labels().Wd(a))}this.aa().select(a[0]||a);return this};
$.g.nk=function(){this.enabled()&&(this.state.oh($.ln),UV(this,null))};$.g.select=function(a){if(!this.enabled())return this;$.p(a)?this.Ai(a):this.Rt();return this};$.g.Rt=function(){this.enabled()&&(cW(this),this.state.oh($.mn),UV(this,null))};
$.g.Ai=function(a,b){if(!this.enabled())return this;var c=!(b&&b.shiftKey);$.B(a)?(b||this.Pd(),this.state.oh($.mn,a,c?$.ln:void 0)):$.ea(a)&&this.state.oh($.mn,a,c?$.ln:void 0);if(this.f){for(c=this.wc();c.advance();)this.Qd($.Hv(this.state,c.ka()));var d;$.ea(a)&&(d=this.labels().Wd(a));UV(this,d)}this.aa().select(a[0]||a);return this};
$.g.Pd=function(a){if(this.enabled()){var b;$.p(a)?b=a:b=this.state.vc==$.Wk?window.NaN:void 0;this.state.xh($.mn,b);a=this.aa();for(a.reset();a.advance();)this.Qd($.Hv(this.state,a.ka()));UV(this)}};$.g.Gj=function(a,b){IV(this,a);JV(this,a);this.rp(a);return b};$.g.zp=$.ia;$.g.Ql=$.ia;$.g.Ck=function(a){this.Qd(a);IV(this,a);JV(this,a);this.rp(a)};var fW={};$.dp(fW,0,"baseWidth",$.np);$.dp(fW,0,"neckHeight",$.np);$.dp(fW,0,"neckWidth",$.np);$.dp(fW,0,"pointsPadding",$.np);$.dp(fW,0,"reversed",$.np);
$.dp(fW,0,"overlapMode",$.zj);$.dp(fW,0,"connectorLength",$.np);$.dp(fW,1,"connectorStroke",$.xp);$.S(HV,fW);$.g=HV.prototype;$.g.Dc=function(a,b,c,d,e,f,h){e=0==b?this.ca:1==b?this.ya:this.Da;h?a=e.i(a):(h=c.get(0==b?"normal":1==b?"hovered":"selected"),a=$.On($.p(h)?h[a]:void 0,c.get($.Xk(b,a)),e.i(a)));$.p(a)&&(a=d(a));return a};$.g.eh=function(){return $.Wb(this.be().ic(this.aa().ka())||"diagonal-brick")};$.g.oi=function(){var a=this.aa();return{index:a.ka(),sourceHatchFill:this.eh(),iterator:a}};
$.g.me=function(a){var b=this.aa();return{index:b.ka(),sourceColor:a||this.Yb().ic(b.ka())||"blue",iterator:b}};$.g.labels=function(a){return $.p(a)?(this.ca.labels(a),this):this.ca.labels()};$.g.Fd=function(a){var b=0,c=0;$.W(a,1)&&(b|=4096,c|=1);$.W(a,8)&&(b|=4100,c|=9);this.u(b,c)};
$.g.Qd=function(a){var b=this.aa(),c=!!(a&$.mn),d=!c&&!!(a&$.ln),e=b.get("normal");e=$.p(e)?e.label:void 0;var f=b.get("hovered");f=$.p(f)?f.label:void 0;var h=b.get("selected");h=$.p(h)?h.label:void 0;e=$.On(e,b.get("label"));f=d?$.On(f,b.get("hoverLabel")):null;h=c?$.On(h,b.get("selectLabel")):null;var k=b.ka(),l,m=null,n=this.lb().labels(),q=this.selected().labels();c?m=l=q:d?m=l=n:l=this.labels();var r=this.labels().Wd(k),t=e&&$.p(e.enabled)?e.enabled:null,u=f&&$.p(f.enabled)?f.enabled:null,v=
h&&$.p(h.enabled)?h.enabled:null;n=d||c?d?null===u?null===n.enabled()?null===t?this.labels().enabled():t:n.enabled():u:null===v?null===q.enabled()?null===t?this.labels().enabled():t:q.enabled():v:null===t?this.labels().enabled():t;t=RV(this,null,a);u=this.Ec();q=FV(this);v=!0;if(!d&&!c&&q&&"no-overlap"==this.i("overlapMode")){l=l.Qk(u,t,e,k);v=this.aa();var w=[v.j("x1"),v.j("y1"),v.j("x2"),v.j("y1"),v.j("x4"),v.j("y2"),v.j("x3"),v.j("y2")],x=!0,y;var A=0;for(y=w.length;A<y-1;A+=2){var G=A==y-2?0:
A+2;var C=A==y-2?1:A+3;var J=w[A];var P=w[A+1];var Q=w[G];var T=w[C];var wa=l[A];var Aa=l[A+1];G=l[G];C=l[C];v.j("y3")&&4==A&&(P=$.N(v.j("y3")),T=$.N(v.j("y3")));J==Q&&(Q+=.01);x=(x=x&&1==$.Ul(J,P,Q,T,wa,Aa))&&1==$.Ul(J,P,Q,T,G,C)}v=x}n&&v?(r?(r.yi(),r.Nf(u),r.Cc(t)):r=this.labels().add(u,t,k),$.yt(r,m),r.gd(e,d?f:h),b.j("labelWidthForced")&&(r.width($.N(b.j("labelWidthForced"))),b=f&&f.anchor?f.anchor:null,h=h&&h.anchor?h.anchor:null,e&&e.anchor&&e.anchor||b||h||(t=RV(this,r,a),r.Cc(t))),r.X(),(d||
c)&&!r.O()&&this.labels().Kd()&&(r.O(this.labels().Kd()),r.O().parent()||r.O().parent(this.labels().O()),r.X())):r&&r.clear();n&&!q&&bW(this,r,a);return r};$.g.Gb=function(a){return $.p(a)?(this.ca.Gb(a),this):this.ca.Gb()};$.g.Io=function(a){$.W(a,1)&&this.u(8192,1)};$.g.dv=function(){var a=$.Sk("fill",1,!1)(this,$.Wk,!0,!0);return $.Ck(a,1,!0)};$.g.gx=function(){return $.yk(this.dv())};
$.g.rp=function(a){var b=this.aa(),c=!!(a&$.mn);a=!c&&!!(a&$.ln);var d=b.get("normal");d=$.p(d)?d.marker:void 0;var e=b.get("hovered");e=$.p(e)?e.marker:void 0;var f=b.get("selected");f=$.p(f)?f.marker:void 0;d=$.On(d,b.get("marker"));e=$.On(e,b.get("hoverMarker"));f=$.On(f,b.get("selectMarker"));var h=this.aa().ka(),k=this.lb().Gb(),l=this.selected().Gb();b=c?l:a?k:this.Gb();var m=this.Gb().Bq(h),n=d&&$.p(d.enabled)?d.enabled:null,q=e&&$.p(e.enabled)?e.enabled:null,r=f&&$.p(f.enabled)?f.enabled:
null;if(a||c?a?null===q?null===k.enabled()?null===n?this.Gb().enabled():n:k.enabled():q:null===r?null===l.enabled()?null===n?this.Gb().enabled():n:l.enabled():r:null===n?this.Gb().enabled():n){n=d&&d.position?d.position:null;q=e&&e.position?e.position:null;r=f&&f.position?f.position:null;n=a&&(q||k.i("position"))||c&&(r||l.i("position"))||n||this.Gb().i("position");n=oga(this,n);m?m.Cc(n):m=this.Gb().add(n,h);var t={};n="position anchor offsetX offsetY type size fill stroke enabled".split(" ");d&&
(0,$.ue)(n,function(a){a in d&&(t[a]=d[a])});n=d&&d.type;h=$.p(n)?n:this.Gb().Qa()||this.lf().ic(h);n=e&&e.type;n=$.p(n)?n:k.Qa();q=f&&f.type;q=$.p(q)?q:l.Qa();t.type=c&&$.p(q)?q:a&&$.p(n)?n:h;h=d&&d.fill;h=$.p(h)?h:this.Gb().Ym()||this.dv();n=e&&e.fill;n=$.p(n)?n:k.Ym();q=f&&f.fill;q=$.p(q)?q:l.Ym();t.fill=c&&$.p(q)?q:a&&$.p(n)?n:h;h=d&&d.stroke;h=$.p(h)?h:this.Gb().sl()||this.gx();n=e&&e.stroke;k=$.p(n)?n:k.sl()||this.gx();n=f&&f.stroke;l=$.p(n)?n:l.sl()||this.gx();t.stroke=c&&$.p(l)?l:a&&$.p(k)?
k:h;m.yi();$.yw(m,b);m.gd(t,a?e:f);m.X()}else m&&m.clear()};$.g.mJ=function(){var a=new $.Ru(0);$.V(this,"tooltip",a);a.za(this);$.L(a,this.Po,this);return a};$.g.Po=function(){this.Ya().X()};$.g.CI=function(a){var b=$.hq(this,"legend");if(!a||!b||a.target!=b){b=this.Ya();var c=this.Ec();a&&($.iv(b,a.clientX,a.clientY,c),this.wa("mousemove",this.CI))}};
$.g.mb=function(){if(this.J(16384)){this.nH();for(var a=this.data().aa(),b,c=0,d=Number.MAX_VALUE,e=-Number.MAX_VALUE,f=0;a.advance();)b=a.get("value"),LV(b)?c++:(b=LV(b)?0:$.N(b),d=Math.min(b,d),e=Math.max(b,e),f+=b);a=a.Eb()-c;var h;a?h=f/a:d=e=f=h=void 0;this.Fa("count",a);this.Fa("min",d);this.Fa("max",e);this.Fa("sum",f);this.Fa("average",$.Kl(h||window.NaN,$.Ll(f||0)));this.I(16384)}};
$.g.Ec=function(){var a=this.aa();this.Td||(this.Td=new $.$u);this.Td.lg(a).Vi([this.Ad(a.ka()),this]);a={x:{value:a.get("x"),type:"string"},value:{value:a.get("value"),type:"number"},name:{value:a.get("name"),type:"string"},index:{value:a.ka(),type:"number"},chart:{value:this,type:""}};$.Lt(this.Td,a);return this.Td};$.g.ek=function(){return this.Ec()};
$.g.Nl=function(a,b){for(var c=[],d=this.aa().reset(),e;d.advance();){e=d.ka();var f=d.get("legendItem")||{},h=null;$.E(b)&&(h=this.Ec(),h.b=this.Ad(e),h=b.call(h,h));$.z(h)||(h=String($.p(d.get("name"))?d.get("name"):d.get("x")));var k=$.Sk("fill",1,!1),l=$.Sk("stroke",2,!1),m=$.Sk("hatchFill",3,!1);h={enabled:!0,meta:{pointIndex:e,pointValue:d.get("value"),W:this},iconType:"square",text:h,iconStroke:l(this,$.Wk,!1),iconFill:k(this,$.Wk,!1),iconHatchFill:m(this,$.Wk,!1)};$.Kc(h,f);h.sourceUid=$.oa(this);
h.sourceKey=e;c.push(h)}return c};$.g.Vr=function(){return!0};$.g.Lq=function(a,b){var c=a.ei();if(!a||null!=c||(0,window.isNaN)(c))if(c=$.hn(b.domTarget))c.W=this};$.g.Ep=function(a,b){var c=a.ei();if(!a||null!=c||(0,window.isNaN)(c))if(c=$.hn(b.domTarget))c.W=this};$.g.Dp=function(a,b){var c=a.ei();if(!a||null!=c||(0,window.isNaN)(c))if(c=$.hn(b.domTarget))c.W=this};$.g.kj=function(){return null};$.g.vl=function(a){return $.p(a)?(a=$.jj(a),a!=this.N&&(this.N=a),this):this.N};$.g.ej=function(){return!this.aa().Eb()};
$.g.F=function(){var a=HV.B.F.call(this);a.data=this.data().F();a.palette=this.Yb().F();a.hatchFillPalette=this.be().F();a.markerPalette=this.lf().F();a.tooltip=this.Ya().F();$.Bp(this,fW,a);a.normal=this.ca.F();a.hovered=this.ya.F();a.selected=this.Da.F();return{chart:a}};
$.g.Y=function(a,b){HV.B.Y.call(this,a,b);$.tp(this,fW,a);this.ca.ja(!!b,a);this.ca.ja(!!b,a.normal);this.ya.ja(!!b,a.hovered);this.Da.ja(!!b,a.selected);this.data(a.data);this.be(a.hatchFillPalette);this.lf(a.markerPalette);this.Yb(a.palette);"tooltip"in a&&this.Ya().ja(!!b,a.tooltip)};
$.g.R=function(){$.jd(this.jg,this.ca,this.ya,this.Da,this.ed,this.o,this.la,this.Aa,this.Vc,this.oe,this.Xe,this.g,this.U);this.la=this.o=this.ed=this.Da=this.ya=this.ca=this.jg=null;delete this.Md;this.U=this.g=this.Xe=this.oe=this.Vc=this.Aa=null;HV.B.R.call(this)};XV.prototype.RA=function(a){this.labels.push(a);this.za.i("reversed")?$.Oa(this.labels,function(a,c){return a.ka()-c.ka()}):$.Oa(this.labels,function(a,c){return c.ka()-a.ka()})};XV.prototype.clear=function(){this.labels.length=0};
XV.prototype.dd=function(a,b){var c=!!(b&$.mn),d=!c&&!!(b&$.ln),e=this.za.data().get(a.ka(),"label");d=d?this.za.data().get(a.ka(),"hoverLabel"):null;c=(c?this.za.data().get(a.ka(),"selectLabel"):null)||d||e||{};this.za.data().j(a.ka(),"labelWidthForced")&&(c=$.Ic(c),c.width=a.width());this.za.aa().select(a.ka());a.Nf(this.za.Ec());c=this.za.labels().Qk(a.Nf(),a.Cc(),c);return $.am(c)};
HV.prototype.pe=function(){this.ca.Ia(this.ma);$.V(this,"normal",this.ca);this.ca.ja(!0,{});$.V(this,"hovered",this.ya);this.ya.ja(!0,{});$.V(this,"selected",this.Da);this.Da.ja(!0,{})};var gW=HV.prototype;gW.data=gW.data;gW.getType=gW.Qa;gW.palette=gW.Yb;gW.tooltip=gW.Ya;gW.hatchFillPalette=gW.be;gW.markerPalette=gW.lf;gW.labels=gW.labels;gW.markers=gW.Gb;gW.hover=gW.xj;gW.unhover=gW.Cd;gW.select=gW.select;gW.unselect=gW.Pd;gW.getPoint=gW.Ad;gW.normal=gW.Ta;gW.hovered=gW.lb;gW.selected=gW.selected;$.Fo.funnel=dW;$.Fo.pyramid=eW;$.F("anychart.funnel",dW);$.F("anychart.pyramid",eW);}).call(this,$)}