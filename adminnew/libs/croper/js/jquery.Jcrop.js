(function(e){e.Jcrop=function(t,n){function u(e){return parseInt(e,10)+"px"}function a(e){return parseInt(e,10)+"%"}function f(e){return r.baseClass+"-"+e}function l(){return e.fx.step.hasOwnProperty("backgroundColor")}function c(t){var n=e(t).offset();return[n.left,n.top]}function p(e){return[e.pageX-i[0],e.pageY-i[1]]}function d(t){if(typeof t!=="object"){t={}}r=e.extend(r,t);if(typeof r.onChange!=="function"){r.onChange=function(){}}if(typeof r.onSelect!=="function"){r.onSelect=function(){}}if(typeof r.onRelease!=="function"){r.onRelease=function(){}}}function v(e){if(e!==s){rt.setCursor(e);s=e}}function m(e,t){i=c(_);rt.setCursor(e==="move"?e:e+"-resize");if(e==="move"){return rt.activateHandlers(b(t),C)}var n=tt.getFixed();var r=E(e);var s=tt.getCorner(E(r));tt.setPressed(tt.getCorner(r));tt.setCurrent(s);rt.activateHandlers(g(e,n),C)}function g(e,t){return function(n){if(!r.aspectRatio){switch(e){case"e":n[1]=t.y2;break;case"w":n[1]=t.y2;break;case"n":n[0]=t.x2;break;case"s":n[0]=t.x2;break}}else{switch(e){case"e":n[1]=t.y+1;break;case"w":n[1]=t.y+1;break;case"n":n[0]=t.x+1;break;case"s":n[0]=t.x+1;break}}tt.setCurrent(n);nt.update()}}function b(e){var t=e;it.watchKeys();return function(e){tt.moveOffset([e[0]-t[0],e[1]-t[1]]);t=e;nt.update()}}function E(e){switch(e){case"n":return"sw";case"s":return"nw";case"e":return"nw";case"w":return"ne";case"ne":return"sw";case"nw":return"se";case"se":return"nw";case"sw":return"ne"}}function S(e){return function(t){if(r.disabled){return false}if(e==="move"&&!r.allowMove){return false}G=true;m(e,p(t));t.stopPropagation();t.preventDefault();return false}}function T(e,t,n){var r=e.width(),i=e.height();if(r>t&&t>0){r=t;i=t/e.width()*e.height()}if(i>n&&n>0){i=n;r=n/e.height()*e.width()}J=e.width()/r;K=e.height()/i;e.width(r).height(i)}function N(e){return{x:parseInt(e.x*J,10),y:parseInt(e.y*K,10),x2:parseInt(e.x2*J,10),y2:parseInt(e.y2*K,10),w:parseInt(e.w*J,10),h:parseInt(e.h*K,10)}}function C(e){var t=tt.getFixed();if(t.w>r.minSelect[0]&&t.h>r.minSelect[1]){nt.enableHandles();nt.done()}else{nt.release()}rt.setCursor(r.allowSelect?"crosshair":"default")}function k(e){if(r.disabled){return false}if(!r.allowSelect){return false}G=true;i=c(_);nt.disableHandles();v("crosshair");var t=p(e);tt.setPressed(t);nt.update();rt.activateHandlers(L,C);it.watchKeys();e.stopPropagation();e.preventDefault();return false}function L(e){tt.setCurrent(e);nt.update()}function A(){var t=e("<div></div>").addClass(f("tracker"));if(e.browser.msie){t.css({opacity:0,backgroundColor:"white"})}return t}function st(e){H.removeClass().addClass(f("holder")).addClass(e)}function ot(e,t){function w(){window.setTimeout(E,c)}var n=parseInt(e[0],10)/J,i=parseInt(e[1],10)/K,s=parseInt(e[2],10)/J,o=parseInt(e[3],10)/K;if(Y){return}var u=tt.flipCoords(n,i,s,o),a=tt.getFixed(),f=[a.x,a.y,a.x2,a.y2],l=f,c=r.animationDelay,h=u[0]-f[0],p=u[1]-f[1],d=u[2]-f[2],v=u[3]-f[3],m=0,g=r.swingSpeed;x=l[0];y=l[1];s=l[2];o=l[3];nt.animMode(true);var b;var E=function(){return function(){m+=(100-m)/g;l[0]=x+m/100*h;l[1]=y+m/100*p;l[2]=s+m/100*d;l[3]=o+m/100*v;if(m>=99.8){m=100}if(m<100){at(l);w()}else{nt.done();if(typeof t==="function"){t.call(yt)}}}}();w()}function ut(e){at([parseInt(e[0],10)/J,parseInt(e[1],10)/K,parseInt(e[2],10)/J,parseInt(e[3],10)/K])}function at(e){tt.setPressed([e[0],e[1]]);tt.setCurrent([e[2],e[3]]);nt.update()}function ft(){return N(tt.getFixed())}function lt(){return tt.getFixed()}function ct(e){d(e);gt()}function ht(){r.disabled=true;nt.disableHandles();nt.setCursor("default");rt.setCursor("default")}function pt(){r.disabled=false;gt()}function dt(){nt.done();rt.activateHandlers(null,null)}function vt(){H.remove();M.show();e(t).removeData("Jcrop")}function mt(e,t){nt.release();ht();var n=new Image;n.onload=function(){var i=n.width;var s=n.height;var o=r.boxWidth;var u=r.boxHeight;_.width(i).height(s);_.attr("src",e);B.attr("src",e);T(_,o,u);D=_.width();P=_.height();B.width(D).height(P);R.width(D+q*2).height(P+q*2);H.width(D).height(P);pt();if(typeof t==="function"){t.call(yt)}};n.src=e}function gt(e){if(r.allowResize){if(e){nt.enableOnly()}else{nt.enableHandles()}}else{nt.disableHandles()}rt.setCursor(r.allowSelect?"crosshair":"default");nt.setCursor(r.allowMove?"move":"default");if(r.hasOwnProperty("setSelect")){ut(r.setSelect);nt.done();delete r.setSelect}if(r.hasOwnProperty("trueSize")){J=r.trueSize[0]/D;K=r.trueSize[1]/P}if(r.hasOwnProperty("bgColor")){if(l()&&r.fadeTime){H.animate({backgroundColor:r.bgColor},{queue:false,duration:r.fadeTime})}else{H.css("backgroundColor",r.bgColor)}delete r.bgColor}if(r.hasOwnProperty("bgOpacity")){U=r.bgOpacity;if(nt.isAwake()){if(r.fadeTime){_.fadeTo(r.fadeTime,U)}else{H.css("opacity",r.opacity)}}delete r.bgOpacity}z=r.maxSize[0]||0;W=r.maxSize[1]||0;X=r.minSize[0]||0;V=r.minSize[1]||0;if(r.hasOwnProperty("outerImage")){_.attr("src",r.outerImage);delete r.outerImage}nt.refresh()}var r=e.extend({},e.Jcrop.defaults),i,s,o=false;if(e.browser.msie&&e.browser.version.split(".")[0]==="6"){o=true}if(typeof t!=="object"){t=e(t)[0]}if(typeof n!=="object"){n={}}d(n);var O={border:"none",margin:0,padding:0,position:"absolute"};var M=e(t);var _=M.clone().removeAttr("id").css(O);_.width(M.width());_.height(M.height());M.after(_).hide();T(_,r.boxWidth,r.boxHeight);var D=_.width(),P=_.height(),H=e("<div />").width(D).height(P).addClass(f("holder")).css({position:"relative",backgroundColor:r.bgColor}).insertAfter(M).append(_);delete r.bgColor;if(r.addClass){H.addClass(r.addClass)}var B=e("<img />").attr("src",_.attr("src")).css(O).width(D).height(P),j=e("<div />").width(a(100)).height(a(100)).css({zIndex:310,position:"absolute",overflow:"hidden"}).append(B),F=e("<div />").width(a(100)).height(a(100)).css("zIndex",320),I=e("<div />").css({position:"absolute",zIndex:300}).insertBefore(_).append(j,F);if(o){I.css({overflowY:"hidden"})}var q=r.boundary;var R=A().width(D+q*2).height(P+q*2).css({position:"absolute",top:u(-q),left:u(-q),zIndex:290}).mousedown(k);var U=r.bgOpacity,z,W,X,V,J,K,Q=true,G,Y,Z;i=c(_);var et=function(){function e(){var e={},t=["touchstart","touchmove","touchend"],n=document.createElement("div"),r;try{for(r=0;r<t.length;r++){var i=t[r];i="on"+i;var s=i in n;if(!s){n.setAttribute(i,"return;");s=typeof n[i]=="function"}e[t[r]]=s}return e.touchstart&&e.touchend&&e.touchmove}catch(o){return false}}function t(){if(r.touchSupport===true||r.touchSupport===false)return r.touchSupport;else return e()}return{createDragger:function(e){return function(t){t.pageX=t.originalEvent.changedTouches[0].pageX;t.pageY=t.originalEvent.changedTouches[0].pageY;if(r.disabled){return false}if(e==="move"&&!r.allowMove){return false}G=true;m(e,p(t));t.stopPropagation();t.preventDefault();return false}},newSelection:function(e){e.pageX=e.originalEvent.changedTouches[0].pageX;e.pageY=e.originalEvent.changedTouches[0].pageY;return k(e)},isSupported:e,support:t()}}();var tt=function(){function u(r){r=d(r);n=e=r[0];i=t=r[1]}function a(e){e=d(e);s=e[0]-n;o=e[1]-i;n=e[0];i=e[1]}function f(){return[s,o]}function l(r){var s=r[0],o=r[1];if(0>e+s){s-=s+e}if(0>t+o){o-=o+t}if(P<i+o){o+=P-(i+o)}if(D<n+s){s+=D-(n+s)}e+=s;n+=s;t+=o;i+=o}function c(e){var t=p();switch(e){case"ne":return[t.x2,t.y];case"nw":return[t.x,t.y];case"se":return[t.x2,t.y2];case"sw":return[t.x,t.y2]}}function p(){if(!r.aspectRatio){return m()}var s=r.aspectRatio,o=r.minSize[0]/J,u=r.maxSize[0]/J,a=r.maxSize[1]/K,f=n-e,l=i-t,c=Math.abs(f),p=Math.abs(l),d=c/p,y,b;if(u===0){u=D*10}if(a===0){a=P*10}if(d<s){b=i;w=p*s;y=f<0?e-w:w+e;if(y<0){y=0;h=Math.abs((y-e)/s);b=l<0?t-h:h+t}else if(y>D){y=D;h=Math.abs((y-e)/s);b=l<0?t-h:h+t}}else{y=n;h=c/s;b=l<0?t-h:t+h;if(b<0){b=0;w=Math.abs((b-t)*s);y=f<0?e-w:w+e}else if(b>P){b=P;w=Math.abs(b-t)*s;y=f<0?e-w:w+e}}if(y>e){if(y-e<o){y=e+o}else if(y-e>u){y=e+u}if(b>t){b=t+(y-e)/s}else{b=t-(y-e)/s}}else if(y<e){if(e-y<o){y=e-o}else if(e-y>u){y=e-u}if(b>t){b=t+(e-y)/s}else{b=t-(e-y)/s}}if(y<0){e-=y;y=0}else if(y>D){e-=y-D;y=D}if(b<0){t-=b;b=0}else if(b>P){t-=b-P;b=P}return g(v(e,t,y,b))}function d(e){if(e[0]<0){e[0]=0}if(e[1]<0){e[1]=0}if(e[0]>D){e[0]=D}if(e[1]>P){e[1]=P}return[e[0],e[1]]}function v(e,t,n,r){var i=e,s=n,o=t,u=r;if(n<e){i=n;s=e}if(r<t){o=r;u=t}return[Math.round(i),Math.round(o),Math.round(s),Math.round(u)]}function m(){var r=n-e,s=i-t,o;if(z&&Math.abs(r)>z){n=r>0?e+z:e-z}if(W&&Math.abs(s)>W){i=s>0?t+W:t-W}if(V/K&&Math.abs(s)<V/K){i=s>0?t+V/K:t-V/K}if(X/J&&Math.abs(r)<X/J){n=r>0?e+X/J:e-X/J}if(e<0){n-=e;e-=e}if(t<0){i-=t;t-=t}if(n<0){e-=n;n-=n}if(i<0){t-=i;i-=i}if(n>D){o=n-D;e-=o;n-=o}if(i>P){o=i-P;t-=o;i-=o}if(e>D){o=e-P;i-=o;t-=o}if(t>P){o=t-P;i-=o;t-=o}return g(v(e,t,n,i))}function g(e){return{x:e[0],y:e[1],x2:e[2],y2:e[3],w:e[2]-e[0],h:e[3]-e[1]}}var e=0,t=0,n=0,i=0,s,o;return{flipCoords:v,setPressed:u,setCurrent:a,getOffset:f,moveOffset:l,getCorner:c,getFixed:p}}();var nt=function(){function c(t){var n=e("<div />").css({position:"absolute",opacity:r.borderOpacity}).addClass(f(t));j.append(n);return n}function h(t,n){var r=e("<div />").mousedown(S(t)).css({cursor:t+"-resize",position:"absolute",zIndex:n});if(et.support){r.bind("touchstart",et.createDragger(t))}F.append(r);return r}function p(e){return h(e,n++).css({top:u(-l+1),left:u(-l+1),opacity:r.handleOpacity}).addClass(f("handle"))}function d(e){var t=r.handleSize,i=t,s=t,o=l,f=l;switch(e){case"n":case"s":s=a(100);break;case"e":case"w":i=a(100);break}return h(e,n++).width(s).height(i).css({top:u(-o+1),left:u(-f+1)})}function v(e){var t;for(t=0;t<e.length;t++){s[e[t]]=p(e[t])}}function m(e){var t=Math.round(e.h/2-l),n=Math.round(e.w/2-l),r=-l+1,i=-l+1,o=e.w-l,a=e.h-l,f,c;if(s.e){s.e.css({top:u(t),left:u(o)});s.w.css({top:u(t)});s.s.css({top:u(a),left:u(n)});s.n.css({left:u(n)})}if(s.ne){s.ne.css({left:u(o)});s.se.css({top:u(a),left:u(o)});s.sw.css({top:u(a)})}if(s.b){s.b.css({top:u(a)});s.r.css({left:u(o)})}}function g(e,t){B.css({top:u(-t),left:u(-e)});I.css({top:u(t),left:u(e)})}function y(e,t){I.width(e).height(t)}function b(){var e=tt.getFixed();tt.setPressed([e.x,e.y]);tt.setCurrent([e.x2,e.y2]);w()}function w(){if(t){return E()}}function E(){var e=tt.getFixed();y(e.w,e.h);g(e.x,e.y);if(o){m(e)}if(!t){x()}r.onChange.call(yt,N(e))}function x(){I.show();if(r.bgFade){_.fadeTo(r.fadeTime,U)}else{_.css("opacity",U)}t=true}function T(){L();I.hide();if(r.bgFade){_.fadeTo(r.fadeTime,1)}else{_.css("opacity",1)}t=false;r.onRelease.call(yt)}function C(){if(o){m(tt.getFixed());F.show()}}function k(){o=true;if(r.allowResize){m(tt.getFixed());F.show();return true}}function L(){o=false;F.hide()}function O(e){if(Y===e){L()}else{k()}}function M(){O(false);b()}var t,n=370;var i={};var s={};var o=false;var l=r.handleOffset;if(r.drawBorders){i={top:c("hline"),bottom:c("hline bottom"),left:c("vline"),right:c("vline right")}}if(r.dragEdges){s.t=d("n");s.b=d("s");s.r=d("e");s.l=d("w")}if(r.sideHandles){v(["n","s","e","w"])}if(r.cornerHandles){v(["sw","nw","ne","se"])}var D=A().mousedown(S("move")).css({cursor:"move",position:"absolute",zIndex:360});if(et.support){D.bind("touchstart.jcrop",et.createDragger("move"))}j.append(D);L();return{updateVisible:w,update:E,release:T,refresh:b,isAwake:function(){return t},setCursor:function(e){D.css("cursor",e)},enableHandles:k,enableOnly:function(){o=true},showHandles:C,disableHandles:L,animMode:O,done:M}}();var rt=function(){function s(){R.css({zIndex:450});if(i){e(document).bind("mousemove",u).bind("mouseup",a)}}function o(){R.css({zIndex:290});if(i){e(document).unbind("mousemove",u).unbind("mouseup",a)}}function u(e){t(p(e));return false}function a(e){e.preventDefault();e.stopPropagation();if(G){G=false;n(p(e));if(nt.isAwake()){r.onSelect.call(yt,N(tt.getFixed()))}o();t=function(){};n=function(){}}return false}function f(e,r){G=true;t=e;n=r;s();return false}function l(e){e.pageX=e.originalEvent.changedTouches[0].pageX;e.pageY=e.originalEvent.changedTouches[0].pageY;return u(e)}function c(e){e.pageX=e.originalEvent.changedTouches[0].pageX;e.pageY=e.originalEvent.changedTouches[0].pageY;return a(e)}function h(e){R.css("cursor",e)}var t=function(){},n=function(){},i=r.trackDocument;if(et.support){e(document).bind("touchmove",l).bind("touchend",c)}if(!i){R.mousemove(u).mouseup(a).mouseout(a)}_.before(R);return{activateHandlers:f,setCursor:h}}();var it=function(){function i(){if(r.keySupport){t.show();t.focus()}}function s(e){t.hide()}function u(e,t,n){if(r.allowMove){tt.moveOffset([t,n]);nt.updateVisible()}e.preventDefault();e.stopPropagation()}function a(e){if(e.ctrlKey){return true}Z=e.shiftKey?true:false;var t=Z?10:1;switch(e.keyCode){case 37:u(e,-t,0);break;case 39:u(e,t,0);break;case 38:u(e,0,-t);break;case 40:u(e,0,t);break;case 27:nt.release();break;case 9:return true}return false}var t=e('<input type="radio" />').css({position:"fixed",left:"-120px",width:"12px"}),n=e("<div />").css({position:"absolute",overflow:"hidden"}).append(t);if(r.keySupport){t.keydown(a).blur(s);if(o||!r.fixedSupport){t.css({position:"absolute",left:"-20px"});n.append(t).insertBefore(_)}else{t.insertBefore(_)}}return{watchKeys:i}}();if(et.support){R.bind("touchstart",et.newSelection)}F.hide();gt(true);var yt={setImage:mt,animateTo:ot,setSelect:ut,setOptions:ct,tellSelect:ft,tellScaled:lt,setClass:st,disable:ht,enable:pt,cancel:dt,release:nt.release,destroy:vt,focus:it.watchKeys,getBounds:function(){return[D*J,P*K]},getWidgetSize:function(){return[D,P]},getScaleFactor:function(){return[J,K]},ui:{holder:H,selection:I}};if(e.browser.msie){H.bind("selectstart",function(){return false})}M.data("Jcrop",yt);return yt};e.fn.Jcrop=function(t,n){function r(r){var i=typeof t==="object"?t:{};var s=i.useImg||r.src;var o=new Image;o.onload=function(){function t(){var t=e.Jcrop(r,i);if(typeof n==="function"){n.call(t)}}function s(){if(!o.width||!o.height){window.setTimeout(s,50)}else{t()}}window.setTimeout(s,50)};o.src=s}this.each(function(){if(e(this).data("Jcrop")){if(t==="api"){return e(this).data("Jcrop")}else{e(this).data("Jcrop").setOptions(t)}}else{r(this)}});return this};e.Jcrop.defaults={allowSelect:true,allowMove:true,allowResize:true,trackDocument:true,baseClass:"jcrop",addClass:null,bgColor:"black",bgOpacity:.6,bgFade:false,borderOpacity:.4,handleOpacity:.5,handleSize:9,handleOffset:5,aspectRatio:0,keySupport:true,cornerHandles:true,sideHandles:true,drawBorders:true,dragEdges:true,fixedSupport:true,touchSupport:null,boxWidth:0,boxHeight:0,boundary:2,fadeTime:400,animationDelay:20,swingSpeed:3,minSelect:[0,0],maxSize:[0,0],minSize:[0,0],onChange:function(){},onSelect:function(){},onRelease:function(){}}})(jQuery)