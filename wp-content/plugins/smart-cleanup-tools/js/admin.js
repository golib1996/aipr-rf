/*! jQuery jSlabify plugin v2.0 MIT/GPL2 @gschoppe */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(E($){$.2z.2N=E(u){6 v={"1y":Q,"1m":1,"10":Q,"1A":1,"11":Q,"1j":Q,"13":Q,"1n":1S,"29":1S,"19":T,"U":T,"27":Q,"1k":2E,"26":2w,"Y":1S,"1o":3,"C":3,"D":0};$("2v").1P("2r");S 1B.1X(E(){5(u)$.2k(v,u);6 o=$(1B),X=$("B.Z",o).7,J=T,1e=T,1g=1U($.14(o.16())).17(/\\s{2,}/g," ").1a(" "),A=[],M=[],1y=v.1y,1m=v.1m,10=v.10,1A=v.1A,11=v.11,1j=v.1j,13=v.13,1n=v.1n,19=v.19,U=v.U,Y=v.Y,1o=v.1o,C=v.C,1k=v.1k,D=v.D,1J=T,1q=$(1t).G(),1w=o.1Z("a:25").1G("1E")||o.1G("1E"),1D=1w?o.1Z("a:25").1G("2e"):"";5(!X&&D&&1g.L(" ").7<D)S;5(X){6 p=2n.2l("I");$(p).15(o.15());6 a,x;1C(a=p.2h("B.Z")[0]){6 q=$(a).16();6 r=$(a)[0].2O;r=(r&&r.1r)?r.1r.17(/\\s{2,}/g," ").14():"";x=$(a)[0].2M;x=(x&&x.1r)?x.1r.17(/\\s{2,}/g," ").14():"";5(r!=""){A.H({1u:0,1b:r});M.H(r)}A.H({1u:1,1b:q});$(a).2x().1x();$(a).1x()}5(x!=""){A.H({1u:0,1b:x});M.H(x)}M=M.L(" ");$(p).1x()}6 s=E(){6 a=1g.L(" "),2m=a.7,1z=a.7>0?a:"2j-\'.,?!&";1v=2g("<I 2K=\'2P:2S;W-1d:2o;2s:0;2t:0;z:1W;2B-z:1;2C:0;2F-2H:2I;\'>"+1z+"</I>").2J(o),1L=1v.G(),1s=1v.z(),R=(1s==0)?1:1L/1s,1Y=R/1z.7;1v.1x();S[1L,1s,R,1z.7,1Y]};6 t=E t(){6 e=o.G(),O=(11)?o.z():e/1A,F,21;5(13){o.z(O)}K 5(!11){o.9("z","1W")}o.2q("2f 1V");5(U&&U>1q||19&&19>e){o.1P("1V");S}F=s();5(1n||21!=J){J=F[1];5(1y){6 f=F[4],1h=8.1F(20,8.2G(e/(J*f*1m)));P=8.1f(F[3]/1h)}K{6 g=F[3],2a=F[2],2c=e/O,P=8.1f(8.2L(2a/2c)),1h=8.1F(20,8.1H(8.1f(g/P),1))}E 1I(a,b){6 c=0,1K=[],2i=0,N="",y="",w="",1c,1M,1N;1e=a;1C(c<b.7){y="";1C(y.7<1e){N=y;y+=b[c]+" ";5(++c>=b.7){2p}}5(D){1c=b.1c(c).L(" ");5(1c.7<D){y+=1c;N=y;c=b.7+2}}1M=1e-N.7;1N=y.7-1e;5((1M<1N)&&(N.7>=(D||2))){w=N;c--}K{w=y}w=$("<I/>").16(w).15();5(v.29)w=w.17(/&1O;/g,"<B 1i=\'1O\'>&1O;</B>");w=$.14(w);1K.H("<B 1i=\'Z\'>"+w+"</B>")}S 1K}5(!X){o.15(1I(1h,1g).L(""))}K{P=8.1H(P,A.7);6 h=P-X;6 j=[];2u(6 i=0;i<A.7;i++){5(A[i].1u){j.H("<B 1i=\'Z\'>"+A[i].1b+"</B>")}K{6 k=1U($.14(A[i].1b));22=k.7/8.1H(8.1f(h*k.7/M.7),1);23=k.17(/\\s{2,}/g," ").1a(" ");j=j.2y(1I(22,23))}}o.15(j.L(""))}5(1w)o.24(\'<a 1E="\'+1w+\'" \'+(1D?\'2e="\'+1D+\'" \':\'\')+\'/>\')}K{J=F[1]}5(!(o.2A("I.1Q").7>0)){o.24(\'<I 1i="1Q" />\')}6 l=o.2D("I.1Q");l.9("W-1d",1+"1l");$("B.Z",o).1X(E(){6 a=$(1B),V=a.16(),28=V.1a(" ").7>1,18,R,1R;6 b=0;5(Y){a.9({"2b-1p":0,"2d-1p":0});6 c=V.1a(" ").7-1;5(c<0)c=0;b=c*1o}a.9("W-1d",1+"1l");R=e/(a.G()+b);1R=(8.1F((J*R),v.26)/J).12(C);a.9("W-1d",1R+"1l");18=e-a.G();5(Y&&18){5(28){6 d=(18/(V.1a(" ").7-1)).12(C);a.9("2b-1p",d+"1T")}K{6 d=(18/V.7).12(C);a.9("2d-1p",d+"1T")}}});6 m=1;5(10&&(l.z()>O)){m=(O/l.z()).12(C);l.9("W-1d",m+"1l")}o.1P("2f");5(1j)l.9("16-2Q","2R");5(10&&13){6 n=((O-l.z())/2).12(C);l.9("2T",\'2U\').9("2V",n+"1T")}};t();5(!v.27){$(1t).2W(E(){5($(1t).G()==1q)S;1q=$(1t).G();2X(1J);1J=2Y(t,1k)})}})}})(2g);',62,185,'|||||if|var|length|Math|css|||||||||||||||||||||||finalText|remainder|postText|height|sections|span|precision|minCharsPerLine|function|fontInfo|width|push|div|origFontSize|else|join|ungroupedstring|preText|parentHeight|lineCount|false|ratio|return|null|viewportBreakpoint|innerText|font|keepSpans|postTweak|slabbedtext|constrainHeight|fixedHeight|toFixed|vCenter|trim|html|text|replace|diff|headerBreakpoint|split|value|slice|size|idealCharPerLine|round|words|newCharPerLine|class|hCenter|resizeThrottleTime|em|fontZoom|forceNewCharCount|minWordSpace|spacing|viewportWidth|nodeValue|emH|window|type|dummy|headLink|remove|targetFont|content|slabRatio|this|while|linkTitle|href|min|attr|max|makeSlabSpans|resizeThrottle|lineText|emW|preDiff|postDiff|amp|addClass|innerslabwrap|newSize|true|px|String|slabbedtextinactive|auto|each|charRatio|find|60|fs|charPerLine|sectionWords|wrapInner|first|maxFontSize|noResizeEvent|wordSpacing|wrapAmpersand|textRatio|word|boxRatio|letter|title|slabbedtextdone|jQuery|querySelectorAll|counter|ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789|extend|createElement|contentLength|document|1em|break|removeClass|slabified|margin|padding|for|body|999|prevAll|concat|fn|has|line|border|children|300|white|floor|space|nowrap|appendTo|style|sqrt|nextSibling|jSlabify|previousSibling|display|align|center|none|position|relative|top|resize|clearTimeout|setTimeout'.split('|'),0,{}));

/*jslint regexp: true, nomen: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
var sct_admin = {
    current: null,
    close_message: function() {
        jQuery(".wrap #message").slideUp("slow");
    },
    quick: function() {
        jQuery(".sct-front-quick div").jSlabify({
            constrainHeight: true, slabRatio: 2, hCenter: true
        });

        jQuery(".sct-front-quick-data").jSlabify({
            slabRatio: 128
        });
    },
    quick_results: function() {
        jQuery(".sct-front-quick-out div").jSlabify({
            slabRatio: 2, hCenter: true, precision: 6
        });

        jQuery(".sct-front-quick-results").jSlabify({
            slabRatio: 16, minWordSpace: 2
        });
    },
    front: function() {
        jQuery(".sct-front-title h1").jSlabify({
            constrainHeight: true, slabRatio: 1, hCenter: true
        });

        jQuery(".sct-front-links div").jSlabify({
            slabRatio: 128
        });
    },
    init: function() {
        setTimeout(sct_admin.close_message, 5000);

        jQuery(".sct-quick-links-panel-toggle").click(function(e) {
            e.preventDefault();

            jQuery("#sct-quick-links-panel").slideToggle(400);
        });

        jQuery("#sct-dialog-tool-details").dialog({
            bgiframe: true, autoResize:true, resizable: true, modal: true, 
            width: 680, autoOpen: false, closeOnEscape: true, autoHeight: true,
            buttons: {
                OK: function() {
                    jQuery(this).dialog("close");
                }
            }
        });

        jQuery("#sct-dialog-tool-help").dialog({
            bgiframe: true, autoResize:true, resizable: true, modal: true, 
            width: 680, autoOpen: false, closeOnEscape: true, autoHeight: true,
            buttons: {
                OK: function() {
                    jQuery(this).dialog("close");
                }
            }
        });

        jQuery(".sct-auto-enabler").click(function(e) {
            e.preventDefault();

            var operation = jQuery(this).attr("href").substr(1);

            if (operation === "enable") {
                jQuery(".sct-checkbox").attr("checked", "checked").parent().parent().parent().addClass("sct-enabled");
            } else if (operation === "disable") {
                jQuery(".sct-checkbox").removeAttr("checked").parent().parent().parent().removeClass("sct-enabled");
            }
        });

        jQuery(".sct-dropdown-single").multiselect({
            multiple: false,
            selectedList: 1,
            height: 'auto',
            header: false,
            create: function() {
                jQuery("button.ui-multiselect").width(445);
                jQuery("div.ui-multiselect-menu").width(443);
            }
        });

        jQuery(".sct-dropdown").multiselect({
            height: 'auto',
            noneSelectedText: sct_admin_data.dropdown_noneSelectedText,
            selectedText: sct_admin_data.dropdown_selectedText,
            create: function() {
                jQuery("button.ui-multiselect").width(200);
                jQuery("div.ui-multiselect-menu").width(198);
            }
        });

        jQuery(".sct-cleanup-box.sct-disabled .sct-dropdown").multiselect("disable");

        jQuery(".sct-dropdown-disabled").multiselect({
            height: 'auto',
            noneSelectedText: sct_admin_data.dropdown_disabled_noneSelectedText,
            selectedText: sct_admin_data.dropdown_disabled_selectedText,
            create: function() {
                jQuery("button.ui-multiselect").width(400);
                jQuery("div.ui-multiselect-menu").width(398);
            }
        });

        jQuery(".sct-cleanup-checkbox input").click(function() {
            if (jQuery(this).is(":checked")) {
                jQuery(this).parent().parent().parent().addClass("sct-enabled");
            } else {
                jQuery(this).parent().parent().parent().removeClass("sct-enabled");
            }
        });

        jQuery(".sct-tool-detail").click(function(e) {
            e.preventDefault();

            var id = jQuery(this).attr("href").substr(1);
            jQuery("#sct-details-content").html(jQuery("#sct-details-" + id).html());

            jQuery("#sct-dialog-tool-details").dialog("option", "title", jQuery("#sct-details-" + id).attr("title"));
            jQuery("#sct-dialog-tool-details").dialog("open");
        });

        jQuery(".sct-tool-help").click(function(e) {
            e.preventDefault();

            var id = jQuery(this).attr("href").substr(1);
            jQuery("#sct-help-content").html(jQuery("#sct-help-" + id).html());

            jQuery("#sct-dialog-tool-help").dialog("option", "title", jQuery("#sct-help-" + id).attr("title"));
            jQuery("#sct-dialog-tool-help").dialog("open");
        });

        jQuery("#sct-remove-load").click(function(e) {
            e.preventDefault();

            var load = jQuery("#sct-remove-type").val();
            jQuery(".sct-remove-panel").hide();
            jQuery("#remove-type").val(load);
            jQuery("#remove-" + load).show();

            var active = jQuery("#remove-" + load + " .sct-tool-active").val();

            jQuery("#sct-removal-block").html("");

            if (load === "nothing" || active === "no") {
                jQuery("#sct-removal-preview-run").hide();
            } else {
                jQuery("#sct-removal-preview-run").show();
            }
        });

        jQuery(document).on("click", "#sct-removal-action-run", function(e) {
            e.preventDefault();

            if (sct_admin.confirm()) {
                sct_admin.dim_remove.open("removal");

                jQuery("#sct-removal-action").ajaxSubmit({
                    success: function(html) {
                        jQuery("#sct-removal-block").html(html);

                        sct_admin.dim_remove.close();
                    },
                    dataType: "html", type: "post", timeout: 15 * 60 * 1000,
                    url: ajaxurl + "?action=smart_removal_action"
                });
            }
        });

        jQuery("#sct-removal-preview-run").click(function(e) {
            e.preventDefault();

            sct_admin.dim_remove.open("analyze");

            jQuery("#sct-removal-block").html("");

            jQuery("#sct-removal-preview").ajaxSubmit({
                success: function(html) {
                    jQuery("#sct-removal-block").html(html);

                    sct_admin.dim_remove.close();
                },
                dataType: "html", type: "post", timeout: 15 * 60 * 1000,
                url: ajaxurl + "?action=smart_removal_preview"
            });
        });

        jQuery("#sct-cleanup-quick").click(function(e) {
            e.preventDefault();

            sct_admin.dim_cleanup.open();

            jQuery("#sct-cleanup-form-quick").ajaxSubmit({
                success: function(html) {
                    jQuery("#sct-quick-cleanup-results").html(html);

                    sct_admin.dim_cleanup.close();

                    sct_admin.quick_results();
                },
                dataType: "html", type: "post", timeout: 15 * 60 * 1000,
                url: ajaxurl + "?action=smart_cleanup_quick"
            });
        });

        jQuery("#sct-cleanup-run").click(function(e) {
            e.preventDefault();

            sct_admin.dim_cleanup.open();

            jQuery("#sct-cleanup-form").ajaxSubmit({
                success: function(html) {
                    jQuery("#ddw-panel").html(html);

                    sct_admin.dim_cleanup.close();
                },
                dataType: "html", type: "post", timeout: 15 * 60 * 1000,
                url: ajaxurl + "?action=smart_cleanup_run"
            });
        });

        jQuery("#run-export").click(function() {
            var url = jQuery(this).data("url"), exp = [];

            jQuery("[name^=export_]").each(function() {
                if (jQuery(this).is(":checked")) {
                    exp.push(jQuery(this).attr("id").substr(7));
                }
            });

            url+= "&export=" + exp.join(",");
            window.location = url;
        });
    },
    dim_remove: {
        open: function(name) {
            jQuery(".dim-remove").hide();
            jQuery("#dim-remove-" + name).show();

            sct_admin.dim_cleanup.open();
        },
        close: function() {
            sct_admin.dim_cleanup.close();
        }
    },
    dim_cleanup: {
        open: function() {
            jQuery(".sct-dim").show();
            jQuery("html").css("overflow", "hidden");
            sct_admin.current = new Date();
        },
        close: function() {
            jQuery(".sct-dim").hide();
            jQuery("html").css("overflow", "auto");
        }
    },
    pad_time_string: function(number) {
        if (number < 10) {
            return "0" + number;
        } else {
            return number;
        }
    },
    timer: function() {
        if (jQuery("#sct-load-timer").length > 0) {
            sct_admin.current = new Date();

            setInterval(function() {
                var total_miliseconds = new Date() - sct_admin.current;   

                var hours = Math.floor(total_miliseconds / 3600000);
                total_miliseconds = total_miliseconds % 3600000;

                var minutes = Math.floor(total_miliseconds / 60000);
                total_miliseconds = total_miliseconds % 60000;

                var seconds = Math.floor(total_miliseconds / 1000);
                total_miliseconds = total_miliseconds % 1000;

                var miliseconds = Math.floor(total_miliseconds);

                hours = sct_admin.pad_time_string(hours);
                minutes = sct_admin.pad_time_string(minutes);
                seconds = sct_admin.pad_time_string(seconds);
                miliseconds = sct_admin.pad_time_string(miliseconds);

                var currentTimeString = hours + ":" + minutes + ":" + seconds + "." + miliseconds;

                jQuery("#sct-load-timer").html(currentTimeString);
            }, 400);
        }
    },
    job: function() {
        jQuery("#job_first_run").datetimepicker({
            minDate: new Date()
        });
    },
    confirm: function() {
        return confirm(sct_admin_data.confirm_areyousure);
    },
    scroller: function() {
        var $sidebar = jQuery("#scs-scroll-sidebar"), 
            $window = jQuery(window);

        if ($sidebar.length > 0) {
            var offset = $sidebar.offset(),
                topPadding = 40;

            $window.scroll(function() {
                if ($window.scrollTop() > offset.top) {
                    $sidebar.stop().animate({
                        marginTop: $window.scrollTop() - offset.top + topPadding
                    });
                } else {
                    $sidebar.stop().animate({
                        marginTop: 0
                    });
                }
            });
        }
    }
};

jQuery(document).ready(function() {
    sct_admin.timer();
    sct_admin.init();
    sct_admin.scroller();
});
