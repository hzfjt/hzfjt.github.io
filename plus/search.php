<?php
/**
 *
 * ����ҳ
 *
 * @version        $Id: search.php 1 15:38 2010��7��8��Z tianya $
 * @package        DedeCMS.Site
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/../include/common.inc.php");
require_once(DEDEINC."/arc.searchview.class.php");
if ( file_exists(DEDEINC."/baidusitemap.func.php") ) require_once(DEDEINC."/baidusitemap.func.php");

$pagesize = (isset($pagesize) && is_numeric($pagesize)) ? $pagesize : 10;
$typeid = (isset($typeid) && is_numeric($typeid)) ? $typeid : 0;
$channeltype = (isset($channeltype) && is_numeric($channeltype)) ? $channeltype : 0;
$kwtype = (isset($kwtype) && is_numeric($kwtype)) ? $kwtype : 0;
$mid = (isset($mid) && is_numeric($mid)) ? $mid : 0;
$mobile = (isset($mobile) && is_numeric($mobile)) ? $mobile : 0;

if ( $mobile==1 )
{
    define('DEDEMOB', 'Y');
}

if(!isset($orderby)) $orderby='';
else $orderby = preg_replace("#[^a-z]#i", '', $orderby);

if(!isset($searchtype)) $searchtype = 'titlekeyword';
else $searchtype = preg_replace("#[^a-z]#i", '', $searchtype);

if(!isset($keyword)){
    if(!isset($q)) $q = '';
    $keyword=$q;
}

$oldkeyword = $keyword = FilterSearch(stripslashes($keyword));

if ( function_exists('baidu_get_setting') )
{
    $site_id = baidu_get_setting('site_id');
    $lastuptime_all = baidu_get_setting('lastuptime_all');
    
    $addquery='';
    if ( $searchtype =='title' )
    {
        $addquery ='&stp=1';
    }
    // ��Ҫ�ύȫ��������5��Сʱ�ڲ��ܹ�������ת
    if ( !empty($site_id) AND time()-$lastuptime_all>60*60*5)
    {
        $row = $dsql->GetOne("SELECT spwords FROM `#@__search_keywords` WHERE keyword='".addslashes($keyword)."'; ");
        if(!is_array($row))
        {
            $inquery = "INSERT INTO `#@__search_keywords`(`keyword`,`spwords`,`count`,`result`,`lasttime`)
          VALUES ('".addslashes($keyword)."', '".addslashes($keyword)."', '1', '0', '".time()."'); ";
            $dsql->ExecuteNoneQuery($inquery);
        }
        else
        {
            $dsql->ExecuteNoneQuery("UPDATE `#@__search_keywords` SET count=count+1,lasttime='".time()."' WHERE keyword='".addslashes($keyword)."'; ");
        }

        $keyword = urlencode($keyword);
        $baidu_search_url = "http://zhannei.baidu.com/cse/search?s={$site_id}&entry=1&q={$keyword}{$addquery}";
        header("Location:{$baidu_search_url}");
        exit;
    }
}

//������Ŀ��Ϣ
if(empty($typeid))
{
    $typenameCacheFile = DEDEDATA.'/cache/typename.inc';
    if(!file_exists($typenameCacheFile) || filemtime($typenameCacheFile) < time()-(3600*24) )
    {
        $fp = fopen(DEDEDATA.'/cache/typename.inc', 'w');
        fwrite($fp, "<"."?php\r\n");
        $dsql->SetQuery("Select id,typename,channeltype From `#@__arctype`");
        $dsql->Execute();
        while($row = $dsql->GetArray())
        {
            $row['typename'] = HtmlReplace($row['typename']);
            fwrite($fp, "\$typeArr[{$row['id']}] = '{$row['typename']}';\r\n");
        }
        fwrite($fp, '?'.'>');
        fclose($fp);
    }
    //������Ŀ���沢���ؼ����Ƿ��������Ŀ����
    require_once($typenameCacheFile);
    if(isset($typeArr) && is_array($typeArr))
    {
        foreach($typeArr as $id=>$typename)
        {
            //$keywordn = str_replace($typename, ' ', $keyword);
            $keywordn = $keyword;
            if($keyword != $keywordn)
            {
                $keyword = HtmlReplace($keywordn);
                $typeid = intval($id);
                break;
            }
        }
    }
}

$keyword = addslashes(cn_substr($keyword,30));

if($cfg_notallowstr !='' && preg_match("#".$cfg_notallowstr."#i", $keyword))
{
    ShowMsg("��������ؼ����д��ڷǷ����ݣ���ϵͳ��ֹ��","-1");
    exit();
}

if(($keyword=='' || strlen($keyword)<2) && empty($typeid))
{
    ShowMsg('�ؼ��ֲ���С��2���ֽڣ�','-1');
    exit();
}

//����������ʱ��
$lockfile = DEDEDATA.'/time.lock.inc';
$lasttime = file_get_contents($lockfile);
if(!empty($lasttime) && ($lasttime + $cfg_search_time) > time())
{
    ShowMsg('����Ա�趨����ʱ����Ϊ'.$cfg_search_time.'�룬���Ժ����ԣ�','-1');
    exit();
}

//��ʼʱ��
if(empty($starttime)) $starttime = -1;
else
{
    $starttime = (is_numeric($starttime) ? $starttime : -1);
    if($starttime>0)
    {
       $dayst = GetMkTime("2008-1-2 0:0:0") - GetMkTime("2008-1-1 0:0:0");
       $starttime = time() - ($starttime * $dayst);
  }
}

$t1 = ExecTime();

$sp = new SearchView($typeid,$keyword,$orderby,$channeltype,$searchtype,$starttime,$pagesize,$kwtype,$mid);
$keyword = $oldkeyword;
$sp->Display();


PutFile($lockfile, time());

//echo ExecTime() - $t1;