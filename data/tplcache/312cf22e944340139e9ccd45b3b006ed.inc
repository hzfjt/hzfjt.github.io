
{dede:noteinfo notename="ͷ��" channelid="1" macthtype="string"
refurl="https://www.xineee.com/yimei/toutiao/20180427181.html" sourcelang="gb2312" cosort="asc" isref="no" exptime="10" usemore="0" /}

{dede:listrule sourcetype="batch" rssurl="http://" regxurl="https://www.xineee.com/yimei/toutiao/list_14_(*).html"
startid="1" endid="10000" addv="1" urlrule="area"
 musthas=".html" nothas="" listpic="1" usemore="0"}
    {dede:addurls}https://www.xineee.com/yimei/toutiao/{/dede:addurls}
    {dede:batchrule}{/dede:batchrule}
    {dede:regxrule}{/dede:regxrule}
    {dede:areastart}<div class="sec-main fl">{/dede:areastart}
    {dede:areaend}<div class="list-page">{/dede:areaend}
{/dede:listrule}

