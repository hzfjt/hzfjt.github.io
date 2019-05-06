<?php
$cfg_NotPrintHead='Y';
set_time_limit(0);
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/channelunit.class.php");
require_once(DEDEINC."/baidusitemap.func.php");
require_once(DEDEINC."/baiduxml.class.php");
require_once(DEDEINC."/oxwindow.class.php");

if(empty($dopost)) $dopost = '';
if(empty($action)) $action = '';

if ($dopost=='checkurl')
{
    $checksign = $_GET['checksign'];
    if (!$checksign || strlen($checksign) !== 32 ){
        exit();
    }
    $data = baidu_get_setting('checksign', true);
    if ($data && $data['svalue'] == $checksign && time()-$data['stime'] < 30) {
        echo $data['svalue'];
    }
} elseif ($dopost=='sitemap_index'){
    header("Content-Type: text/xml; charset=utf-8");
    if (empty($_GET['pwd']) || $_GET['pwd'] != ($bdpwd = baidu_get_setting('bdpwd'))) {
        baidu_header_status(404);
        return 1;
    }
    $pagesize=empty($pagesize)? 0 : intval($pagesize);
    $sitemap_type=0;
    if($type=='indexall') $sitemap_type=1;
    elseif($type=='indexinc') $sitemap_type=2;
    $bdarcs = new BaiduArticleXml;
    $start=$pagesize*$bdarcs->Row;
    $bdarcs->setSitemapType($sitemap_type);
    $bdarcs->Start=$start;
    echo $bdarcs->toXml();
} elseif ( $dopost=='sitemap_urls' )
{
    header("Content-Type: text/xml; charset=utf-8");
    if (empty($_GET['pwd']) || $_GET['pwd'] != ($bdpwd = baidu_get_setting('bdpwd'))) {
        baidu_header_status(404);
        return 1;
    }
    $type = empty($_GET['type'])? 1 : intval($_GET['type']);
    $query = "SELECT distinct(url),create_time from `#@__plus_baidusitemap_list` WHERE `type`={$type} group by url";
    $dsql->SetQuery($query);
	$dsql->Execute('dd');
	$xmlstr = '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex>';
	while ($row = $dsql->GetArray('dd')) {
    	$row['create_time'] = Mydate('Y-m-d',$row['create_time']);
        $xmlstr .= "\r\n    <sitemap>
            <loc><![CDATA[{$row['url']}]]></loc>
            <lastmod>{$row['create_time']}</lastmod>
    </sitemap>";
	}
    $xmlstr .= "\r\n</sitemapindex>";
    echo $xmlstr;
} elseif ( $dopost=='site_id' )
{
    $checksign = $_GET['checksign'];
    $site_id = $_GET['site_id'];
    if (!$checksign || strlen($checksign) !== 32 ){
        echo json_encode(array('status'=>0));
        exit();
    }
    
    if ( !$site_id )
    {
        echo json_encode(array('status'=>0));
        exit;
    }
    
    $siteurl = baidu_get_setting('siteurl');
    $token = baidu_get_setting('pingtoken');
    $sign = md5($siteurl.$token);
    if ($checksign == $sign) {
        $data = baidu_set_setting('site_id', $site_id);
        echo json_encode(array('status'=>1));
    }
    //if (!$checksign || strlen($checksign) !== 32 ){
    //    exit();
    //}
    
} elseif ( $dopost=='success' )
{
    $site_id = baidu_get_setting('site_id');
    $siteurl = baidu_get_setting('siteurl');
    if ( empty($site_id) )
    {
        ShowMsg("当前系统尚无绑定站点ID，请在系统后台百度站内搜索模块中绑定！","javascript:;");
        exit();
    }
    $msg = <<<EOT
<div style="padding:20px; color:#000;line-height:22px">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#DADADA">
	<tbody>
		<tr bgcolor="#FFFFFF">
			<td colspan="2" height="100">
				<table width="98%" border="0" cellspacing="1" cellpadding="1">
					<tbody>
						<tr>
							<td width="16%" height="30">站点地址：</td>
							<td width="84%" style="text-align:left;"><span style="color:black">{$siteurl}</span>
							</td>
						</tr>
						<tr>
							<td width="16%" height="30">站点ID：</td>
							<td width="84%" style="text-align:left;"><span style="color:black">{$site_id}</span>
							</td>
						</tr>
		<tr>
			<td height="30" colspan="2" style="color:#999"><strong>百度站内搜索</strong>百度站内搜索旨在帮助站长低成本地为网站用户提供高质量的网站内搜索服务。使用百度站内搜索工具，您可以轻松打造网站专属的搜索引擎，自定义个性化的展现样式、功能模块等，并通过搜索广告获得收入。</td>
		</tr>
		</tbody>
		</table>
		</td>
		</tr>
	</tbody>
</table>
当您看到这个页面的时候，则说明已经完成DedeCMS百度站内搜索模块的安装和站点绑定。<br />
您还需要完成以下步骤才能完成DedeCMS百度站内搜索功能的部署：<br/><br/>
1.<b>提交索引</b>：登录系统后台，在【模块】-【百度站内搜索】-【站内搜索】中提交站点索引，如果您是第一次使用，需要选择【重新提交全量索引】，如果您已经提交过，每次添加完新内容后则直接使用【提交增量索引】功能；<br/><br/>
2.<b>部署代码</b>：您在模板对应位置插入标签 <font color='red'>{dede:baidusitemap/}</font>，或者可以直接复制下面文本框中的代码，粘贴到模板对应位置，重新更新HTML，即可查看百度搜索框。<br/><br/>
<textarea name="" rows="6" cols="80">
<script type="text/javascript">document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E%3Cscript charset="utf-8" src="http://znsv.baidu.com/customer_search/api/js?sid={$site_id}') + '&plate_url=' + (encodeURIComponent(window.location.href)) + '&t=' + (Math.ceil(new Date()/3600000)) + unescape('"%3E%3C/script%3E'));</script>
</textarea><br/><br/>
<script type="text/javascript">document.write(unescape('%3Cdiv id="bdcs"%3E%3C/div%3E%3Cscript charset="utf-8" src="http://znsv.baidu.com/customer_search/api/js?sid={$site_id}') + '&plate_url=' + (encodeURIComponent(window.location.href)) + '&t=' + (Math.ceil(new Date()/3600000)) + unescape('"%3E%3C/script%3E'));</script>
也可以登录后台，在【模块】-【百度站内搜索】-【搜索框管理】中根据自己需求创建符合站点样式的搜索框。<br/><br/>
CopyRight 2010  DesDev Inc. All rights reserved Powered by <a target="_blank" href="http://www.dedecms.com/">DedeCMS</a> <a target="_blank" href="百度站内搜索模块">百度站内搜索模块</a>
</div>
EOT;
    $msg = "<div style=\"line-height:36px;\">{$msg}</div>";

    $wintitle = '百度站内搜索';
    $wecome_info = 'DedeCMS百度站内搜索模块 》';
    $win = new OxWindow();
    $win->AddTitle($wintitle);
    $win->AddMsgItem($msg);
    $winform = $win->GetWindow('hand', '&nbsp;', false);
    $win->Display();
}