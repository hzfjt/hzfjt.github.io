<?php
/**
 *
 * @name           CaiJiXia for dedecms version 2.80 update 1
 * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
 * @copyright      Copyright (c) 2012��caijixia for dedecms��caijixia.net.
 * @license        This is NOT a freeware, use is subject to license terms
 */

require_once DEDEINC.'/dedehttpdown.class.php';
require_once DEDEINC.'/dedehtml2.class.php';
require_once DEDEINC."/oxwindow.class.php";

is_file(dirname(__FILE__).'/update.php') && include dirname(__FILE__).'/update.php';

class admin_cjx
{
    var $db;
    var $cjxfile;
    var $configfile;
    var $ver = '2.83';

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function __construct()
    {

        $this->db = $this->gv('db');
        $this->configfile = DEDEDATA.'/Plugins.config.inc.php';
        $this->cjxfile = DEDEDATA.'/admin/kw.txt';
        $this->assign('cjxhost',$this->gv('cmsurl'));
        $this->assign('cfg_makeindex',$this->gv('makeindex'));
        $this->assign('cfg_make_andcat',$this->gv('make_andcat'));

        global $cfg_basehost;
        $cfg_basehost = str_replace('http://','',$cfg_basehost);
        $host = $_SERVER['HTTP_HOST'];
        if(strpos($cfg_basehost,$host)===false && strpos($host,$cfg_basehost)===false){
            showmsg("�뽫<a href=sys_info.php>ϵͳ��������</a>�е�<u>վ�����ַ</u>����Ϊ��ǰ����������������ȷ�󷽿�ʹ�á�",1,2);
            exit;
        }
        $this->cjx = new CaiJiXia();
    }

    function admin_cjx()
    {
        $this->	__construct();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_setting()
    {
        $this->display();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_advanced()
    {
        $this->display();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_seo()
    {
        $this->display();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_search()
    {
        $this->display();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_savesetting()
    {
        $setting = $this->gv('setting');
        $this->autoch($setting);
        foreach($setting as $k=>$v){
            if(is_array($v)) $v = join(',',$v);
            cjxdb('plugins_config')->where("name='$k'")->update(array('value'=>stripslashes($v)));
        }
        $this->UpdateConfig();
        echo 'success';
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_credits()
    {
        global $cfg_basehost;
        //header("Location:http://www.dedeapps.com/Credits?domain=".str_replace('http://','',$cfg_basehost));
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_task()
    {
        //������ɾ����Ŀ��Ĺؼ���
        $cat = cjxdb("arctype")->fields("id")->select();
        $catarr = array();
        foreach($cat as $catr){
            $catarr[] = $catr['id'];
        }
        $cats = join(',',$catarr);
        if(!empty($cats)){
            cjxdb("kwkeyword")->where("typeid NOT IN ($cats)")->delete();
        }

        //������ɾ���ڵ�İ�
        $jd = cjxdb("co_note")->fields("nid")->select();
        $jdarr = array();
        foreach($jd as $jdr){
            $jdarr[] = $jdr['nid'];
        }
        $jds = join(',', $jdarr);
        if(!empty($jds)){
            cjxdb("kwkeyword")->where("type=3 AND keyword NOT IN ($jds)")->delete();
        }

        require_once(DEDEINC."/dedetag.class.php");
        $dtp = new DedeTagParse();
        $rs = cjxdb('arctype')->where(array('ispart'=>0,'channeltype'=>1))->order('id asc')->fields('id,typename')->select();
        foreach($rs as $k => $v){
            $rs[$k]['keyword'] = ''; $rs[$k]['close'] = 0;
            $tmp = cjxdb('kwkeyword')->where("typeid={$v['id']}")->order("`type` asc")->select();
            if($tmp){
                foreach($tmp as $v){
                    if($v['type']==0){
                        $rs[$k]['keyword'] .= empty($rs[$k]['keyword'])?$v['keyword']:','.$v['keyword'];
                    }else if($v['type']==1){
                        $rs[$k]['keyword'] .= '<br>RSS:'.$v['keyword'];
                    }else if($v['type']==2){
                        $dtp->LoadString($v['keyword']);
                        $v['keyword'] = $dtp->GetTagByName('list')?$dtp->GetTagByName('list')->GetInnerText():'';
                        $rs[$k]['keyword'] .= '<br>����'.$v['keyword'];
                    }else{
                        if(!isset($bangdingjd)){
                            $rs[$k]['keyword'] .= '<br>�󶨽ڵ㣺'.$v['keyword'];
                            $bangdingjd = true;
                        }else{
                            $rs[$k]['keyword'] .= '��'.$v['keyword'];
                        }
                    }
                    $rs[$k]['close'] = $v['isclose'];
                }
            }
        }

        $this->assign('kwtype',$rs);

        $arr = explode(',',$this->gv('cron'));
        $cron = array();
        foreach($arr as $v) $cron[$v] = true;
        $this->assign('cron',$cron);
        $this->assign('tmax',@unserialize ($this->gv('tmax')) );

        $cfg_automatic =  $GLOBALS['kw_automatic'];
        $this->assign('automatic',$cfg_automatic);
        $this->display();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_cjxfile()
    {
        file_put_contents($this->cjxfile,$this->gv('value'));
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_gettask()
    {
        AjaxHead();

        global $db;
        //�����Ѿ�ɾ���ڵ�İ�
        $db->Execute('me', "delete from #@__kwkeyword where type=3 AND keyword NOT IN (  SELECT nid FROM `#@__co_note` )");

        $typeid = $this->gv('typeid');
        $rs = cjxdb('kwkeyword')->where("typeid=$typeid")->Fields('`type`,`keyword`')->select();
        $keyword = $rss = $dx = $dxs = '';
        require_once(DEDEINC."/dedetag.class.php");
        $dtp = new DedeTagParse();
        foreach($rs as $r){
            if($r['type']==0){
                $keyword .= empty($keyword)?$r['keyword']:"\r\n".$r['keyword'];
            }else if($r['type']==1){
                $rss .= empty($rss)?$r['keyword']:"\r\n".$r['keyword'];
            }else if($r['type']==2){
                $dx .= empty($dx)?$r['keyword']:"``".$r['keyword'];
                $dtp->LoadString($r['keyword']);
                $dxt = $dtp->GetTagByName('list')?$dtp->GetTagByName('list')->GetInnerText():'';
                $dxs .= "<span><a href='javascript:void(0);' style='float:right' onclick='delpage(this);'>ɾ��</a><a href='javascript:void(0);' style='float:right' onclick='editpage(this);'>�༭&nbsp;</a>".$dxt."</span>";
            }else{

            }
        }

        $note = cjxdb('co_note')->Fields('nid,notename')->where('channelid=1')->order("nid desc")->select();
        foreach($note as $k => $v){
            $ck = cjxdb('kwkeyword')->where("`keyword`={$v['nid']} AND `type`=3")->find();
            if($ck){
                if($ck['typeid']==$typeid){
                    $note[$k]['btype'] = true;
                }else{
                    $note[$k]['btype'] = $ck['typeid'];
                }
            }
            $note[$k]['channelname'] = '��ͨ����';
        }

        $this->assign('typeid',$typeid);
        $this->assign('keyword',$keyword);
        $this->assign('rss',$rss);
        $this->assign('dx',$dx);
        $this->assign('dxs',$dxs);
        $this->assign('notelist',$note);
        $this->display();
    }

    function ac_edittask(){
        global $cfg_soft_lang;
        $data = stripslashes($this->GV('data'));
        if($cfg_soft_lang!='utf-8'){
            $data = utf82gb($data);
        }
        require_once DEDEINC.'/dedetag.class.php';
        $this->dtp = new DedeTagParse();
        $this->dtp->LoadString($data);
        $r = array();
        foreach($this->dtp->CTags as $ctag){
            $itemName = $ctag->TagName;
            $r[$itemName] = trim($ctag->InnerText);
        }
        $this->assign('olddata',  $data);
        $this->assign('r',$r);
        $this->display();
    }


    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_bindtype(){
        $typeid = $this->gv('typeid');
        $nid = $this->gv('nid');
        cjxdb('kwkeyword')->insert(array('typeid'=>$typeid,'keyword'=>$nid,'type'=>3));
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_unbindtype(){
        $typeid = $this->gv('typeid');
        $nid = $this->gv('nid');
        cjxdb('kwkeyword')->where(array('typeid'=>$typeid,'keyword'=>$nid,'type'=>3))->delete();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_saveword()
    {
        $keyword = $this->gv('keyword');
        $this->autoch($keyword);
        $typeid = $this->gv('typeid');
        $m = $this->gv('m');
        foreach($keyword as $k => $r){
            if($k==2){
                $ass = explode("``",$r);
            }else{
                $r = str_replace("\r","\n",$r);
                $ass = explode("\n",$r);
            }

            $ass = array_filter($ass);

            $adds = '';
            foreach($ass as $_r){
                $adds .= " AND `keyword` not like '$_r'";
            }
            cjxdb("kwkeyword")->where("`typeid`=$typeid AND `type`=$k $adds")->delete();

            foreach($ass as $w){
                //if($m-- <= 0) break;
                if(!empty($w)){
                    $chk = cjxdb('kwkeyword')->where(" typeid=$typeid AND keyword='$w' ")->find();
                    if(empty($chk)){
                        $w = stripslashes($w);
                        cjxdb('kwkeyword')->insert(array('typeid'=>$typeid,'keyword'=>$w,'type'=>$k));

                    }
                }
            }
        }
        $rs = cjxdb('kwcache')->select();
        foreach ($rs as $key => $value) {
            $hash = md5($value['url']);
            cjxdb('kwhash')->where("hash='$hash' ")->delete();
        }
        cjxdb('kwcache')->delete();
        print 'success';
    }

    function autoch(&$data){
        if($this->gv('soft_lang')=='utf-8') return $data;
        foreach($data as $k => $v){
            if(is_array($v)){
                $data[$k] = $this->autoch($v);
            }else{
                $data[$k] = utf82gb($v);
            }
        }
        return $data;
    }

    function autoch2u(&$data){
        if($this->gv('soft_lang')=='utf-8') return $data;
        foreach($data as $k => $v){
            if(is_array($v)){
                $data[$k] = $this->autoch2u($v);
            }else{
                $data[$k] = gb2utf8($v);
            }
        }
        return $data;
    }

    function ac_cjxlog(){
        $rs = cjxdb("kwlog")->limit(200)->order("id desc")->select();
        foreach ($rs as $key => $value) {
            $rs[$key]['addtime'] = MyDate("Y-m-d H:i:s", $value['addtime']);
        }
        $data['num'] = count($rs);
        $data['data'] = $rs;
        $data = $this->autoch2u($data);
        echo json_encode($data);
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_status()
    {
        $updatemax = $this->gv('updatemax');
        if(!empty($updatemax)){
            $tmax = $this->gv('tmax');
            $data['value'] = serialize($tmax) ;
            cjxdb('plugins_config')->where("name='kw_tmax'")->update($data);
            $this->UpdateConfig();
            showmsg("���³ɹ������ڷ���",'?ac=task');
            exit;
        }

        $tid = $this->gv('tid');
        $do = ($this->gv('type')+1)%2;
        if(empty($tid)){
            showmsg('��ѡ����Ŀ','?ac=task');
            exit;
        }
        if(is_array($tid)) $tid = join(',',$tid);
        $rs = cjxdb('kwkeyword')->where("typeid in ($tid) ")->update(array('isclose'=>$do));
        Showmsg('�޸ĳɹ������ڷ���','?ac=task');
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */
    function ac_clearall()
    {
        cjxdb('kwkeyword')->delete();
        showmsg('�ɹ����������Ŀ�ɼ���Ŀ','?ac=task');
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_cron()
    {
        $cron = $this->gv('cron');
        $data = '';
        if($cron) $data = join(',',$cron);
        cjxdb('plugins_config')->where("`name`='kw_cron'")->update(array('value'=>$data));
        $this->UpdateConfig();
        Showmsg('����ɹ������ڷ���','?ac=task');
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_testregx()
    {
        global $cfg_soft_lang;
        $data = stripslashes($this->GV('data'));
        if($cfg_soft_lang!='utf-8'){
            $data = utf82gb($data);
        }
        require_once DEDEINC.'/dedetag.class.php';
        $this->dtp = new DedeTagParse();
        $this->dtp->LoadString($data);
        foreach($this->dtp->CTags as $ctag){
            $itemName = $ctag->TagName;
            $$itemName = trim($ctag->InnerText);
        }
        if(empty($list) || empty($page) || $list=='http://' || $page=='http://') exit('�������');
        $listarr = array();
        if(preg_match("/\[([0-9]*-[0-9]*)\]/",$list,$out)){
            list($min,$max) = explode('-',$out[1]);
            if($max-$min>10) $max = $min+9;
            for($i=$min;$i<=$max;$i++){
                $listarr[] = preg_replace("/\[([0-9]*-[0-9]*)\]/",$i,$list);
            }
            $list = preg_replace("/\[([0-9]*-[0-9]*)\]/", $min, $list);
        }else{
            $listarr[] = $list;
        }
        $str = $this->downfile($list, $charset, $cfg_soft_lang);

        $dhtml = new DedeHtml2();
        if(strpos($page, '[����]')===false){
            $dhtml->SetSource($str,$list,'link');
            $lss = array();
            $i = 0;
            $page = str_replace('(*)','###',$page);
            $page = preg_quote($page,'/');
            $page = str_replace('###','([0-9a-zA-Z\.\-_]*)',$page);
            foreach($dhtml->Links as $s){
                if(preg_match('/^'.$page.'$/iU',$s['link'])){
                    if(!isset($lss[$s['link']])){
                        $lss[$s['link']] = $s['link'];
                        $i++;
                        if($i==10) break;
                    }
                }
            }
        }else{
            $str = $this->cjx->UT($str, $page);
            $dhtml->SetSource($str,$list,'link');
            $lss = array();
            $i = 0;
            foreach($dhtml->Links as $s){
                if(!isset($lss[$s['link']])){
                    $lss[$s['link']] = $s['link'];
                    $i++;
                    if($i==10) break;
                }
            }
        }


        $msg = '';
        $GLOBALS['wintitle'] = "�ɼ���-���Զ���ɼ�����";
        $GLOBALS['wecome_info'] = "�ɼ�������ɼ�::�ɼ��������";
        $win = new OxWindow();
        $win->AddTitle('ƥ�䵽���б��ַ��ǰ10����');
        foreach($listarr as $v){
            $msg .= $v."<br>";
        }
        $win->AddMsgItem($msg);
        $win->AddTitle('��һ���б�ҳƥ�䵽�����µ�ַ��ǰ10����');
        $msg = '';
        foreach($lss as $v){
            $msg .= $v."<br>";
        }
        $win->AddMsgItem($msg);

        $pageurl = $lss[array_rand($lss, 1)];
        $str = $this->downfile($pageurl);
        if($cfg_soft_lang!=$charset){
            if($charset=='utf-8') {
                $str = utf82gb($str);
            }else{
                $str = gb2utf8($str);
            }
        }

        $win->AddTitle("<font color=black>���Բɼ�����:$pageurl </font>");
        $win->AddTitle('���±���');
        if(empty($titlerule)){
            $win->AddMsgItem('�Զ�������Ҫ����');
        }else{
            $win->AddMsgItem($this->cjx->UT($str,$titlerule));
        }

        $win->AddTitle('����');
        if(empty($authorrule)){
            $win->AddMsgItem('�Զ�������Ҫ����');
        }else{
            $win->AddMsgItem($this->cjx->UT($str,$authorrule));
        }

        $win->AddTitle('��Դ');
        if(empty($sourcerule)){
            $win->AddMsgItem('�Զ�������Ҫ����');
        }else{
            $win->AddMsgItem($this->cjx->UT($str,$sourcerule));
        }

        $win->AddTitle('�������ݣ������޷��ɼ���ҳ���ݣ�');
        if(empty($bodyrule)){
            $win->AddMsgItem('�Զ�������Ҫ����');
        }else{
            $win->AddMsgItem($this->cjx->UT($str,$bodyrule));
        }

        $win->AddTitle('��ҳ����');
        if(empty($fyrule)){
            $win->AddMsgItem('�Զ��ɼ���ҳ����Ҫ����');
        }else{
            $fylink = $this->cjx->UT($str,$fyrule);
            $dhtml = new DedeHtml2();
            $dhtml->SetSource($fylink,$pageurl,'link');
            $relink = '';
            foreach($dhtml->Links as $k=>$v){
                $relink .= $k."<br>";
            }
            $win->AddMsgItem($relink);
        }

        $GLOBALS['winform'] = $win->GetWindow("hand");
        $win->Display();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_testhttp()
    {
        $s = $this->downfile("http://www.dedeapps.com/?m=Caijixia&a=testhttp");
        if($s=='success')
        {
            ShowMsg('���Ŀռ�֧�ֲɼ�','?ac=task');
        }else
        {
            Showmsg('ϵͳ��⵽���Ŀռ���ܲ�֧�ֲɼ�',1,2);
        }
    }

    function ac_delhash(){
        cjxdb('kwcache')->delete();
        cjxdb('kwhash')->delete();
        cjxdb('kwlog')->delete();
        cjxdb('kwkeyword')->where("1=1")->update(array('pn'=>0));
        ShowMsg('��ʷ��¼����ɹ�','?ac=task');
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_update()
    {
//		$str = $this->downfile('http://www.dedeapps.com/Update/?type=2&plugin=kwrobot&ver='.$this->ver);
//		if($this->gv('soft_lang')!='utf-8') $str = utf82gb($str);
//		list($ver,$msg) = explode('|',$str);
//		if($this->ver==$ver || $ver == 'error')
//		{
//			ShowMsg($msg,1,2);
//		}else
//		{
//			ShowMsg($msg."<br><a href='?ac=updatenow&ver={$ver}'>���������������</a>",'index.php',0,1000*60*5);
//		}
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_updatenow()
    {
//		 require_once DEDEINC."/dedemodule.class.php";
//		 $mdir = DEDEDATA.'/module';
//		 $ver = $this->gv('ver');
//		 $file = $this->downfile('http://www.dedeapps.com/uploads/onlineupdate/'.$ver.'_'.$this->gv('soft_lang').'.xml');
//		 file_put_contents($mdir.'/cjxupdate.xml',$file);
//		 $dm = new DedeModule($mdir);
//		 $infos = $dm->GetModuleInfo($mdir.'/cjxupdate.xml','file');
//		 if(empty($infos['hash']))
//		 {
//			 ShowMsg('����ʧ��',1,2);exit;
//		 }
//		 copy($mdir.'/cjxupdate.xml',$mdir.'/'.$infos['hash'].'.xml');
//		 $dm->WriteFiles($infos['hash'],1);
//		 @unlink($mdir.'/cjxupdate.xml');
//		 @unlink($mdir.'/'.$infos['hash'].'.xml');
//		 ShowMsg('�������',1,2);
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_advcjx(){
        $r = cjxdb("kwavc")->order('id desc')->select();
        $this->assign("data",$r);
        $this->display();
    }

    function ac_addavc(){
        $submit = $this->gv('submit');
        if($submit){
            $data['siteurl'] = stripslashes($this->gv("siteurl"));
            $data['typeid'] = stripslashes($this->gv("typeid"));
            $data['title'] = stripslashes($this->gv("title"));
            $data['writer'] = stripslashes($this->gv("writer"));
            $data['source'] = stripslashes($this->gv("source"));
            $data['content'] = stripslashes($this->gv("content"));
            $data['page'] = stripslashes($this->gv("page"));
            if(empty($data['siteurl']) || $data['siteurl']=='http://'){
                showmsg("�ɼ���ڵ�ַ������д��",-1);
                exit;
            }
            if(empty($data['content'])){
                showmsg("���ݹ��������д��",-1);
                exit;
            }
            cjxdb("kwavc")->insert($data);
            showmsg("��ӳɹ���",'cjx.php?ac=advcjx');
        }else{
            $this->display();
        }
    }

    function ac_editavc(){
        $id = $this->gv("id");
        $submit = $this->gv("submit");
        if(empty($submit)){
            $r = cjxdb("kwavc")->where("id='$id'")->find();
            $this->assign("r",$r);
            $this->display();
        }else{
            $data['siteurl'] = stripslashes($this->gv("siteurl"));
            $data['typeid'] = stripslashes($this->gv("typeid"));
            $data['title'] = stripslashes($this->gv("title"));
            $data['writer'] = stripslashes($this->gv("writer"));
            $data['source'] = stripslashes($this->gv("source"));
            $data['content'] = stripslashes($this->gv("content"));
            $data['page'] = stripslashes($this->gv("page"));
            cjxdb("kwavc")->where("id='$id'")->update($data);
            showmsg("�޸ĳɹ���",'cjx.php?ac=advcjx');
        }
    }

    function ac_delavc(){
        $id = $this->gv("id");
        cjxdb("kwavc")->where("id='$id'")->delete();
        cjxdb('kwavcdata')->where("tid='$id'")->delete();
        showmsg("ɾ���ɹ���","cjx.php?ac=advcjx");
    }

    function ac_resetavc(){
        $id = $this->gv("id");
        cjxdb('kwavcdata')->where("tid='$id'")->delete();
        showmsg("�������óɹ���","cjx.php?ac=advcjx");
    }

    function ac_export(){
        global $cfg_soft_lang;
        $id = $this->gv("id");
        $timestamp = time();
        $atype = cjxdb("arctype")->where("id='$id'")->find();
        $rs = cjxdb("kwkeyword")->where("typeid='$id' AND type!=0")->select();
        $krs = cjxdb("kwkeyword")->where("typeid='$id' AND type=0")->select();
        $ruletype = array('�ؼ��ʹ���','rss����','����ɼ�����');
        $kws = '';
        foreach($krs as $r){
            $kws .= $r['keyword'].',';
        }
        $kws = trim($kws,',');
        if(!empty($kws)) $rs[] = array('keyword'=>$kws,'type'=>0);
        if(empty($rs)) {
            showmsg("����Ŀ��û����ӹ���","javascript:window.close();");
            exit;
        }
        header("Content-Type:application/octet-stream");
        header("Content-Disposition: attachment; filename={$atype['typename']}.txt");
        header("Pragma: no-cache");
        header("Expires: 0");
        $co = count($rs);$i=0;
        foreach($rs as $r){
            $i ++ ;
            echo "#�������ͣ�{$ruletype[$r['type']]}\r\n";
            echo "#����ʱ�䣺".date("Y-m-d H:i",$timestamp)."\r\n";
            $hash = md5($r['keyword']);
            echo "<data timestamp=\"{$timestamp}\" version=\"{$this->ver}\" charset=\"{$cfg_soft_lang}\" type=\"{$r['type']}\" hash=\"{$hash}\">{$r['keyword']}</data>";
            if($i!=$co) echo "\r\n\r\n---\r\n\r\n";
        }
    }

    function ac_import(){
        global $cfg_soft_lang;
        $id = $this->gv("id");
        $sub = $this->gv('sub');
        if(empty($sub)){
            $this->display();
        }else{
            $all = 0;$index = 0;
            $con = $this->gv('con');
            if(empty($con)){
                showmsg("��δ����ɼ�����",-1);exit;
            }
            $cons = explode("---",$con);
            foreach($cons  as $r ){
                $index ++ ;
                if(!empty($r)){
                    //$test = explode("#",$r);
                    //if(count($test)<=4){
                    //echo "{$index}�����򲻹淶���벻Ҫ�޸Ĺ���<br>";continue;
                    //}
                    $r = stripslashes($r);
                    if(!empty($r) && preg_match("/<data(.*)<\/data>/siU",$r,$mt)){
                        $n = strpos($mt[1],'>');
                        $s = trim( substr($mt[1],0,$n) );
                        $e = trim( substr($mt[1],$n+1) );
                        $s = str_replace(array(" ",'"'),array("&",""),$s);
                        @parse_str($s,$arr);
                        if(count($arr)!=5){
                            echo "{$index}���޷�ʶ�����<br>";continue;
                        }
                        $hash = md5($e);
                        if($hash!=$arr['hash']){
                            //echo "{$index}���Ƿ�����<br>";continue;
                        }
                        // if($arr['charset']!=$cfg_soft_lang){
                        // 	if($arr['charset']=='gb2312'){
                        // 		$e = gb2utf8($e);
                        // 	}else{
                        // 		$e = utf82gb($e);
                        // 	}
                        // }
                        $d['typeid'] = $id;
                        $d['type'] = $arr['type'];
                        if($d['type']!=0){
                            $d['keyword'] = $e;
                            if(!cjxdb("kwkeyword")->where("keyword='{$d['keyword']}'")->find()){
                                cjxdb("kwkeyword")->insert($d);
                            }else{
                                echo "{$index}������ͬ�����Ѵ���<br>";continue;
                            }
                        }else{
                            $es = explode(',',$e);
                            foreach($es as $er){
                                $d['keyword'] = $er;
                                if(!cjxdb("kwkeyword")->where("keyword='{$d['keyword']}'")->find()){
                                    cjxdb("kwkeyword")->insert($d);
                                }else{
                                    echo "{$index}������ͬ�����Ѵ���<br>";continue;
                                }
                            }
                        }
                        echo "{$index}������ɹ�<br>";
                        $all ++ ;
                    }
                }
            }
            showmsg("��ɵ���{$all}������","?ac=task");
        }
    }


    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function ac_suggest()
    {
//		$content = Html2Text($this->gv('v'));
//		$url = 'http://www.dedeapps.com/Suggest?';
//		$query_str = "type=caijixia&content=".$content;
//		$context =
//		array('http' =>
//				array('method' => 'POST',
//					'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
//								'User-Agent: Mozilla-cjx-u-api-cli (non-curl) '.phpversion()."\r\n".
//								'Content-length: ' . strlen($query_str),
//					'content' => $query_str));
//		$contextid = stream_context_create($context);
//		$sock = fopen($url, 'r', FALSE, $contextid);
//		if ($sock)
//		{
//			$result = '';
//			while (!feof($sock))
//			{
//				$result .= fgets($sock, 4096);
//			}
//			fclose($sock);
//		}
        print '�ύ�ɹ�����л���Ľ���';
    }

    function ac_mykey(){
        //header("Location: http://www.dedeapps.com/?m=caijixia&a=mykey");
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function display()
    {
        if(!file_exists($this->configfile)){
            $this->UpdateConfig();
        }
        $rs = cjxdb('plugins_config')->select();
        foreach($rs as $v){
            $$v['name'] = $v['value'];
        }
        @extract($GLOBALS['_cjx']);
        require $this->tpl();
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function gv($k)
    {
        if(isset($GLOBALS[$k]))
            return $GLOBALS[$k];
        else if(isset($GLOBALS['kw_'.$k]))
            return $GLOBALS['kw_'.$k];
        else if(isset($GLOBALS['cfg_'.$k]))
            return $GLOBALS['cfg_'.$k];
        else
            return false;
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function tpl($template = '')
    {
        $name = $template==''?$this->gv('ac'):$template;
        $templets = DEDEADMIN.'/apps/CaiJiXia/templets/'.$name.'.htm';
        if(is_file($templets)) return $templets;
        else Showmsg("ģ��{$templets}������",1,2);exit;
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function assign($k, $v)
    {
        $GLOBALS['_cjx'][$k] = $v;
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function UpdateConfig()
    {
        if($fp = fopen($this->configfile,'w'))
        {
            flock($fp,3);
            fwrite($fp,"<"."?php\r\n");
            $rs = cjxdb('plugins_config')->order('id asc')->select();
            foreach($rs as $row){
                if(is_numeric($row['value'])){
                    fwrite($fp,"\${$row['name']} = ".$row['value'].";\r\n");
                }
                else{
                    fwrite($fp,"\${$row['name']} = '".str_replace("'","\\'",stripslashes($row['value']))."';\r\n");
                }
            }
            fwrite($fp,"?".">");
            fclose($fp);
        }else
        {
            ShowMsg('д��ʧ�ܣ�������'.$this->configfile.'��дȨ��',-1);
            exit();
        }
    }

    /**
     * caijixia for dedecms
     * @version        $Id: cjx.class.php 233 2015-07-24 16:16:50Z qinjinpeng $
     * @copyright Copyright (c) 2011��caijixia for dedecms��caijixia.net.
     * @license   This is NOT a freeware, use is subject to license terms
     *
     * @param     NULL
     * @return    NULL
     */

    function downfile($s, $from=null, $to=null)
    {
        $soft_lang = $this->gv('soft_lang');
        if(empty($from)){
            $from = $soft_lang;
        }
        if(empty($to)){
            $to = $soft_lang;
        }
        return $this->cjx->HD($s, $from, $to);
    }


    function ac_saveautocj()
    {
        $cfg_automatic =  $GLOBALS['kw_automatic'];
        if(empty($cfg_automatic)){
            cjxdb("plugins_config")->insertCfg(array('kw_automatic'=>1));
        }else if($cfg_automatic == 1){
            cjxdb('plugins_config')->where("name='kw_automatic'")->update(array('value'=>99));
        }else{
            cjxdb('plugins_config')->where("name='kw_automatic'")->update(array('value'=>1));
        }
        $this->UpdateConfig();

    }
}
?>