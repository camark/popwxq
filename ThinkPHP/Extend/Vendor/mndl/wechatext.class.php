<?php
/**
 *	΢�Ź���ƽ̨PHP-SDK
 *  WechatextΪ�ǹٷ�΢�ŷ���API
 *  ע: �û�idΪͨ��getMsg()������ȡ��FakeIdֵ
 *  ��Ҫʵ�����¹���:
 *  send($id,$content) ��ĳ�û�id����΢��������Ϣ
 *  getUserList($page,$pagesize,$group) ��ȡ�û���Ϣ
 *  getGroupList($page,$pagesize) ��ȡȺ����Ϣ
 *  sendNews($id,$msgid) ����ͼ����Ϣ
 *  getNewsList($page,$pagesize) ��ȡͼ����Ϣ�б�
 *  uploadFile($filepath,$type) �ϴ�����,����ͼƬ/��Ƶ/��Ƶ
 *  addPreview($title,$author,$summary,$content,$photoid,$srcurl='')   �����µ�ͼ����Ϣ 
 *  getFileList($type,$page,$pagesize) ��ȡ�زĿ��ļ��б�
 *  sendImage($id,$fid) ����ͼƬ��Ϣ
 *  sendAudio($id,$fid) ������Ƶ��Ϣ
 *  sendVideo($id,$fid) ������Ƶ��Ϣ
 *  getInfo($id) ����id��ȡ�û�����
 *  getNewMsgNum($lastid) ��ȡ��$lastid��������Ϣ����Ŀ
 *  getTopMsg() ��ȡ����һ����Ϣ������, �˷�����ȡ����Ϣid������Ϊ�������Ϣ��$lastid����
 *  getMsg($lastid,$offset=0,$perpage=50,$day=0,$today=0,$star=0) ��ȡ���µ���Ϣ�б�, �б�������Ϣid, �û�id, ��Ϣ����, ������Ϣ�Ȳ���
 *  ��Ϣ���ؽṹ:  {"id":"��Ϣid","type":"���ͺ�(1Ϊ����,2ΪͼƬ,3Ϊ����)","fileId":"0","hasReply":"0","fakeId":"�û�uid","nickName":"�ǳ�","dateTime":"ʱ���","content":"��������"} 
 *  getMsgImage($msgid,$mode='large') ����Ϣtype����Ϊ2, ���ô˷�����ȡͼƬ����
 *  getMsgVoice($msgid) ����Ϣtype����Ϊ3, ���ô˷�����ȡ��������
 *  @author dodge <dodgepudding@gmail.com>
 *  @link https://github.com/dodgepudding/wechat-php-sdk
 *  @version 1.2
 *  
 */

include "snoopy.class.php";
class Wechatext
{
	private $cookie;
	private $_cookiename;
	private $_cookieexpired = 3600;
	private $_account;
	private $_password;
	private $_datapath = './data/cookie_';
	private $debug;
	private $_logcallback;
	private $_token;

	public function __construct($options)
	{
		$this->_account = isset($options['account'])?$options['account']:'';
		$this->_password = isset($options['password'])?$options['password']:'';
		$this->_datapath = isset($options['datapath'])?$options['datapath']:$this->_datapath;
		$this->debug = isset($options['debug'])?$options['debug']:false;
		$this->_logcallback = isset($options['logcallback'])?$options['logcallback']:false;
		$this->_cookiename = $this->_datapath.$this->_account;
		$this->cookie = $this->getCookie($this->_cookiename);
	}

	/**
	 * ��������Ϣ
	 * @param  string $id      �û���uid(��FakeId)
	 * @param  string $content ���͵�����
	 */
	public function send($id,$content)
	{
		$send_snoopy = new Snoopy; 
		$post = array();
		$post['tofakeid'] = $id;
		$post['type'] = 1;
		$post['token'] = $this->_token;
		$post['content'] = $content;
		$post['ajax'] = 1;
        $send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/singlesendpage?t=message/send&action=index&tofakeid=$id&token={$this->_token}&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response";
		$send_snoopy->submit($submit,$post);
		$this->log($send_snoopy->results);
		return $send_snoopy->results;
	}

	/**
	 * Ⱥ������ ���ı�
	 * @param string $content
	 * @return string
	 */
	public function mass($content) {
		$send_snoopy = new Snoopy;
		$post = array();
		$post['type'] = 1;
		$post['token'] = $this->_token;
		$post['content'] = $content;
		$post['ajax'] = 1;
		$post['city']='';
		$post['country']='';
		$post['f']='json';
		$post['groupid']='-1';
		$post['imgcode']='';
		$post['lang']='zh_CN';
		$post['province']='';
		$post['random']=  rand(0, 1);
		$post['sex']=0;
		$post['synctxnews']=0;
		$post['synctxweibo']=0;
		$post['t']='ajax-response';
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token={$this->_token}&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/masssend";
		$send_snoopy->submit($submit,$post);
		$this->log($send_snoopy->results);
		return $send_snoopy->results;
	}

	/**
	 * Ⱥ������ ͼ���ز�
	 * @param int $appmsgid ͼ���ز�ID
	 * @return string
	 */
	function massNews($appmsgid){
		$send_snoopy = new Snoopy;
		$post = array();
		$post['type'] = 10;
		$post['token'] = $this->_token;
		$post['appmsgid'] = $appmsgid;
		$post['ajax'] = 1;
		$post['city']='';
		$post['country']='';
		$post['f']='json';
		$post['groupid']='-1';
		$post['imgcode']='';
		$post['lang']='zh_CN';
		$post['province']='';
		$post['random']=  rand(0, 1);
		$post['sex']=0;
		$post['synctxnews']=0;
		$post['synctxweibo']=0;
		$post['t']='ajax-response';
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token={$this->_token}&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/masssend";
		$send_snoopy->submit($submit,$post);
		$this->log($send_snoopy->results);
		return $send_snoopy->results;
	}

	/**
	 * ��ȡ�û��б��б�
	 * @param $page ҳ��(��0��ʼ)
	 * @param $pagesize ÿҳ��С
	 * @param $groupid ����id
	 * @return array ({contacts:[{id:12345667,nick_name:"�ǳ�",remark_name:"��ע��",group_id:0},{}....]})
	 */
	function getUserList($page=0,$pagesize=10,$groupid=0){
		$send_snoopy = new Snoopy;
		$t = time().strval(mt_rand(100,999));
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=".$pagesize."&pageidx=".$page."&type=0&groupid=0&lang=zh_CN&token=".$this->_token;
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=".$pagesize."&pageidx=".$page."&type=0&groupid=$groupid&lang=zh_CN&f=json&token=".$this->_token;
		$send_snoopy->fetch($submit);
		$result = $send_snoopy->results;
		$this->log('userlist:'.$result);
		$json = json_decode($result,true);
		if (isset($json['contact_list'])) {
			$json = json_decode($json['contact_list'],true);
			if (isset($json['contacts']))
				return $json['contacts'];
		}
		return false;
	}

	/**
	 * ��ȡ�����б�
	 * 
	 */
	function getGroupList(){
		$send_snoopy = new Snoopy;
		$t = time().strval(mt_rand(100,999));
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=10&pageidx=0&type=0&groupid=0&lang=zh_CN&token=".$this->_token;
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=10&pageidx=0&type=0&groupid=0&lang=zh_CN&f=json&token=".$this->_token;
		$send_snoopy->fetch($submit);
		$result = $send_snoopy->results;
		$this->log('userlist:'.$result);
		$json = json_decode($result,true);
		if (isset($json['group_list'])){
			$json = json_decode($json['group_list'],true);
			if (isset($json['groups']))
				return $json['groups'];
		}
		return false;
	}

	/**
	 * ��ȡͼ����Ϣ�б�
	 * @param $page ҳ��(��0��ʼ)
	 * @param $pagesize ÿҳ��С
	 * @return array
	 */
	public function getNewsList($page,$pagesize=10) {
		$send_snoopy = new Snoopy;
		$t = time().strval(mt_rand(100,999));
		$type=10;
		$begin = $page*$pagesize;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=".$this->_token."&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/appmsg?token=".$this->_token."&lang=zh_CN&type=$type&action=list&begin=$begin&count=$pagesize&f=json&random=0.".$t;
		$send_snoopy->fetch($submit);
		$result = $send_snoopy->results;
		$this->log('newslist:'.$result);
		$json = json_decode($result,true);
		if (isset($json['app_msg_info'])) {
			return $json['app_msg_info'];
		} 
		return false;
	}

	/**
	 * ��ȡ��ָ���û��ĶԻ�����
	 * @param  $fakeid
	 * @return  array
	 */
	public function getDialogMsg($fakeid) {
		$send_snoopy = new Snoopy;
		$t = time().strval(mt_rand(100,999));
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=".$this->_token."&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/singlesendpage?t=message/send&action=index&tofakeid=".$fakeid."&token=".$this->_token."&lang=zh_CN&f=json&random=".$t;
		$send_snoopy->fetch($submit);
		$result = $send_snoopy->results;
		$this->log('DialogMsg:'.$result);
		$json = json_decode($result,true);
		if (isset($json['page_info'])) {
			return $json['page_info'];
		}
		return false;
	}


	/**
	 * ����ͼ����Ϣ,�����ͼ�Ŀ���ѡȡ��ϢID����
	 * @param  string $id      �û���uid(��FakeId)
	 * @param  string $msgid ͼ����Ϣid
	 */
	public function sendNews($id,$msgid)
	{
		$send_snoopy = new Snoopy; 
		$post = array();
		$post['tofakeid'] = $id;
		$post['type'] = 10;
		$post['token'] = $this->_token;
		$post['fid'] = $msgid;
		$post['appmsgid'] = $msgid;
		$post['error'] = 'false';
		$post['ajax'] = 1;
        $send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/singlemsgpage?fromfakeid={$id}&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response";
		$send_snoopy->submit($submit,$post);
		$this->log($send_snoopy->results);
		return $send_snoopy->results;
	}

	/**
	 * �ϴ�����(ͼƬ/��Ƶ/��Ƶ)
	 * @param string $filepath �����ļ���ַ
	 * @param int $type �ļ�����: 2:ͼƬ 3:��Ƶ 4:��Ƶ
	 */
	public function uploadFile($filepath,$type=2) {
		$send_snoopy = new Snoopy;
		$send_snoopy->referer = "http://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-upload&lang=zh_CN&type=2&formId=1";
		$t = time().strval(mt_rand(100,999));
		$post = array('formId'=>'');
		$postfile = array('uploadfile'=>$filepath);
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->set_submit_multipart();
		$submit = "http://mp.weixin.qq.com/cgi-bin/uploadmaterial?cgi=uploadmaterial&type=$type&token=".$this->_token."&t=iframe-uploadfile&lang=zh_CN&formId=	file_from_".$t;
		$send_snoopy->submit($submit,$post,$postfile);
		$tmp = $send_snoopy->results;
		$this->log('upload:'.$tmp);
		preg_match("/formId,.*?\'(\d+)\'/",$tmp,$matches);
		if (isset($matches[1])) {
			return $matches[1];
		}
		return false;
	}

	/**
	 * ����ͼ����Ϣ
	 * @param array $title ����
	 * @param array $summary ժҪ
	 * @param array $content ����
	 * @param array $photoid �زĿ����ͼƬid(��ͨ��uploadFile�ϴ����ȡ)
	 * @param array $srcurl ԭ������
	 * @return json
	 */
	public function addPreview($title,$author,$summary,$content,$photoid,$srcurl='') {
		$send_snoopy = new Snoopy;
		$send_snoopy->referer = 'https://mp.weixin.qq.com/cgi-bin/operate_appmsg?lang=zh_CN&sub=edit&t=wxm-appmsgs-edit-new&type=10&subtype=3&token='.$this->_token;

		$submit = "https://mp.weixin.qq.com/cgi-bin/operate_appmsg?lang=zh_CN&t=ajax-response&sub=create&token=".$this->_token;
		$send_snoopy->rawheaders['Cookie']= $this->cookie;

		$send_snoopy->set_submit_normal();
		$post = array(
				'token'=>$this->_token,
				'type'=>10,
				'lang'=>'zh_CN',
				'sub'=>'create',
				'ajax'=>1,
				'AppMsgId'=>'',				
				'error'=>'false',
		);
		if (count($title)==count($author)&&count($title)==count($summary)&&count($title)==count($content)&&count($title)==count($photoid))
		{
			$i = 0;
			foreach($title as $v) {
				$post['title'.$i] = $title[$i];
				$post['author'.$i] = $author[$i];
				$post['digest'.$i] = $summary[$i];
				$post['content'.$i] = $content[$i];
				$post['fileid'.$i] = $photoid[$i];
				if ($srcurl[$i]) $post['sourceurl'.$i] = $srcurl[$i];

				$i++;
				}
		}
		$post['count'] = $i;
		$post['token'] = $this->_token;
		$send_snoopy->submit($submit,$post);
		$tmp = $send_snoopy->results;
		$this->log('step2:'.$tmp);
		$json = json_decode($tmp,true);
		return $json;
	}

	/**
	 * ����ý���ļ�
	 * @param $id �û���uid(��FakeId)
	 * @param $fid �ļ�id
	 * @param $type �ļ�����
	 */
	public function sendFile($id,$fid,$type) {
		$send_snoopy = new Snoopy; 
		$post = array();
		$post['tofakeid'] = $id;
		$post['type'] = $type;
		$post['token'] = $this->_token;
		$post['fid'] = $fid;
		$post['fileid'] = $fid;
		$post['error'] = 'false';
		$post['ajax'] = 1;
        $send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/singlemsgpage?fromfakeid={$id}&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response";
		$send_snoopy->submit($submit,$post);
		$result = $send_snoopy->results;
		$this->log('sendfile:'.$result);
		$json = json_decode($result,true);
		if ($json && $json['ret']==0) 
			return true;
		else
			return false;
	}

	/**
	 * ��ȡ�زĿ��ļ��б�
	 * @param $type �ļ�����: 2:ͼƬ 3:��Ƶ 4:��Ƶ
	 * @param $page ҳ��(��0��ʼ)
	 * @param $pagesize ÿҳ��С
	 * @return array
	 */
	public function getFileList($type,$page,$pagesize=10) {
		$send_snoopy = new Snoopy;
		$t = time().strval(mt_rand(100,999));
		$begin = $page*$pagesize;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=".$this->_token."&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$submit = "https://mp.weixin.qq.com/cgi-bin/filepage?token=".$this->_token."&lang=zh_CN&type=$type&random=0.".$t."&begin=$begin&count=$pagesize&f=json";
		$send_snoopy->fetch($submit);
		$result = $send_snoopy->results;
		$this->log('filelist:'.$result);
		$json = json_decode($result,true);
		if (isset($json['page_info']))
			return $json['page_info'];
		else
			return false;
	}

	/**
	 * ����ͼ����Ϣ,����ӿ���ѡȡ�ļ�ID����
	 * @param  string $id      �û���uid(��FakeId)
	 * @param  string $fid �ļ�id
	 */
	public function sendImage($id,$fid)
	{
		return $this->sendFile($id,$fid,2);
	}

	/**
	 * ����������Ϣ,����ӿ���ѡȡ�ļ�ID����
	 * @param  string $id      �û���uid(��FakeId)
	 * @param  string $fid �����ļ�id
	 */
	public function sendAudio($id,$fid)
	{
		return $this->sendFile($id,$fid,3);
	}

	/**
	 * ������Ƶ��Ϣ,����ӿ���ѡȡ�ļ�ID����
	 * @param  string $id      �û���uid(��FakeId)
	 * @param  string $fid ��Ƶ�ļ�id
	 */
	public function sendVideo($id,$fid)
	{
		return $this->sendFile($id,$fid,4);
	}

	/**
	 * ����Ԥ��ͼ����Ϣ
	 * @param string $account �˻�����(user_name)
	 * @param string $title ����
	 * @param string $summary ժҪ
	 * @param string $content ����
	 * @param string $photoid �زĿ����ͼƬid(��ͨ��uploadFile�ϴ����ȡ)
	 * @param string $srcurl ԭ������
	 * @return json
	 */
	public function sendPreview($account,$title,$summary,$content,$photoid,$srcurl='') {
		$send_snoopy = new Snoopy;
		$submit = "https://mp.weixin.qq.com/cgi-bin/operate_appmsg?sub=preview&t=ajax-appmsg-preview";
		$send_snoopy->set_submit_normal();
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = 'https://mp.weixin.qq.com/cgi-bin/operate_appmsg?sub=edit&t=wxm-appmsgs-edit-new&type=10&subtype=3&lang=zh_CN';
		$post = array(
				'AppMsgId'=>'',
				'ajax'=>1,
				'content0'=>$content,
				'count'=>1,
				'digest0'=>$summary,
				'error'=>'false',
				'fileid0'=>$photoid,
				'preusername'=>$account,
				'sourceurl0'=>$srcurl,
				'title0'=>$title,
		);
		$post['token'] = $this->_token;
		$send_snoopy->submit($submit,$post);
		$tmp = $send_snoopy->results;
		$this->log('sendpreview:'.$tmp);
		$json = json_decode($tmp,true);
		return $json;
	}

	/**
	 * ��ȡ�û�����Ϣ
	 * @param  string $id �û���uid(��FakeId)
	 * @return array  {fake_id:100001,nick_name:'�ǳ�',user_name:'�û���',signature:'ǩ����',country:'�й�',province:'�㶫',city:'����',gender:'1',group_id:'0'},groups:{[id:0,name:'δ����',cnt:20]}
	 */
	public function getInfo($id)
	{
		$send_snoopy = new Snoopy; 
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$t = time().strval(mt_rand(100,999));
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=50&token=".$this->_token;
		$submit = "https://mp.weixin.qq.com/cgi-bin/getcontactinfo";
		$post = array('ajax'=>1,'lang'=>'zh_CN','random'=>'0.'.$t,'token'=>$this->_token,'t'=>'ajax-getcontactinfo','fakeid'=>$id);
		$send_snoopy->submit($submit,$post);
		$this->log($send_snoopy->results);
		$result = json_decode($send_snoopy->results,true);
		if(isset($result['contact_info'])){
			return $result['contact_info'];
		}
		return false;
	}
		
	public function getInfo2($fakeid){
		$send_snoopy = new Snoopy;
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=50&token=".$this->_token;
		$url = "https://mp.weixin.qq.com/misc/getheadimg?fakeid=$fakeid&token=".$this->_token."&lang=zh_CN";
		$send_snoopy->fetch($url);
		$result['body'] = $send_snoopy->results;
		$this->log('Head image:'.$fakeid.'; length:'.strlen($result));
		if(!$result){
			return false;
		}
		return $result;
	}	

	/**
	 * ���ͷ������
	 *
	 * @param FakeId $fakeid
	 * @return JPG����������
	 */
	public function getHeadImg($fakeid){
		$send_snoopy = new Snoopy;
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=50&token=".$this->_token;
		$url = "https://mp.weixin.qq.com/misc/getheadimg?fakeid=$fakeid&token=".$this->_token."&lang=zh_CN";
		$send_snoopy->fetch($url);
		$result = $send_snoopy->results;
		$this->log('Head image:'.$fakeid.'; length:'.strlen($result));
		if(!$result){
			return false;
		}
		return $result;
	}

	/**
	 * ��ȡ��Ϣ������Ŀ
	 * @param int $lastid �����ȡ����ϢID,Ϊ0ʱ��ȡ����Ϣ��Ŀ
	 * @return int ��Ŀ
	 */
	public function getNewMsgNum($lastid=0){
		$send_snoopy = new Snoopy; 
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=50&token=".$this->_token;
		$submit = "https://mp.weixin.qq.com/cgi-bin/getnewmsgnum?t=ajax-getmsgnum&lastmsgid=".$lastid;
		$post = array('ajax'=>1,'token'=>$this->_token);
		$send_snoopy->submit($submit,$post);
		$this->log($send_snoopy->results);
		$result = json_decode($send_snoopy->results,1);
		if(!$result){
			return false;
		}
		return intval($result['newTotalMsgCount']);
	}

	/**
	 * ��ȡ����һ����Ϣ
	 * @return array {"id":"����һ��id","type":"���ͺ�(1Ϊ����,2ΪͼƬ,3Ϊ����)","fileId":"0","hasReply":"0","fakeId":"�û�uid","nickName":"�ǳ�","dateTime":"ʱ���","content":"��������","playLength":"0","length":"0","source":"","starred":"0","status":"4"}        
	 */
	public function getTopMsg(){
		$send_snoopy = new Snoopy;
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&count=20&day=7&lang=zh_CN&token=".$this->_token;
		$submit = "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&f=json&count=20&day=7&lang=zh_CN&token=".$this->_token;
		$send_snoopy->fetch($submit);
		$this->log($send_snoopy->results);
		$result = $send_snoopy->results;
		$json = json_decode($result,true);
		if (isset($json['msg_items'])) {
			$json = json_decode($json['msg_items'],true);
			if(isset($json['msg_item']))
				return array_shift($json['msg_item']);
		}
		return false;
	}

	/**
	 * ��ȡ����Ϣ
	 * @param $lastid ����������Ϣid���,Ϊ0�������һ�������ȡ
	 * @param $offset lastid�����һ����ƫ����
	 * @param $perpage ÿҳ��ȡ������
	 * @param $day ���������Ϣ(0:����,1:����,2:ǰ��,3:����,7:������)
	 * @param $today �Ƿ�ֻ��ʾ�������Ϣ, ��$day��������ͬʱ����0
	 * @param $star �Ƿ��Ǳ�����Ϣ
	 * @return array[] ͬgetTopMsg()���ص��ֶνṹ��ͬ
	 */
	public function getMsg($lastid=0,$offset=0,$perpage=20,$day=7,$today=0,$star=0){
		$send_snoopy = new Snoopy; 
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&lang=zh_CN&count=50&token=".$this->_token;
		$lastid = $lastid===0 ? '':$lastid;
		$addstar = $star?'&action=star':'';
		$submit = "https://mp.weixin.qq.com/cgi-bin/message?t=message/list&f=json&lang=zh_CN{$addstar}&count=$perpage&timeline=$today&day=$day&frommsgid=$lastid&offset=$offset&token=".$this->_token;
		$send_snoopy->fetch($submit);
		$this->log($send_snoopy->results);
		$result = $send_snoopy->results;
		$json = json_decode($result,true);
		if (isset($json['msg_items'])) {
			$json = json_decode($json['msg_items'],true);
			if(isset($json['msg_item']))
				return $json['msg_item'];
		}
		return false;
	}

	/**
	 * ��ȡͼƬ��Ϣ
	 * @param int $msgid ��Ϣid
	 * @param string $mode ͼƬ�ߴ�(large/small)
	 * @return jpg�������ļ�
	 */
	public function getMsgImage($msgid,$mode='large'){
		$send_snoopy = new Snoopy; 
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=50&token=".$this->_token;
		$url = "https://mp.weixin.qq.com/cgi-bin/getimgdata?token=".$this->_token."&msgid=$msgid&mode=$mode&source=&fileId=0";
		$send_snoopy->fetch($url);
		$result = $send_snoopy->results;
		$this->log('msg image:'.$msgid.';length:'.strlen($result));
		if(!$result){
			return false;
		}
		return $result;
	}

	/**
	 * ��ȡ������Ϣ
	 * @param int $msgid ��Ϣid
	 * @return mp3�������ļ�
	 */
	public function getMsgVoice($msgid){
		$send_snoopy = new Snoopy; 
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->referer = "https://mp.weixin.qq.com/cgi-bin/getmessage?t=wxm-message&lang=zh_CN&count=50&token=".$this->_token;
		$url = "https://mp.weixin.qq.com/cgi-bin/getvoicedata?token=".$this->_token."&msgid=$msgid&fileId=0";
		$send_snoopy->fetch($url);
		$result = $send_snoopy->results;
		$this->log('msg voice:'.$msgid.';length:'.strlen($result));
		if(!$result){
			return false;
		}
		return $result;
	}

	/**
	 * ģ���¼��ȡcookie
	 * @return [type] [description]
	 */
	/**
	 * ģ���¼��ȡcookie
	 * @return [type] [description]
	 */
	public function login(){
		$snoopy = new Snoopy; 
		$submit = "https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN";
		$post["username"] = $this->_account;
		$post["pwd"] = md5($this->_password);
		$post["f"] = "json";
		$post["imgcode"] = "";
		$snoopy->referer = "https://mp.weixin.qq.com/";
		$snoopy->submit($submit,$post);
		$cookie = '';
		$this->log($snoopy->results);
		$result = json_decode($snoopy->results,true);

		if (!isset($result['base_resp']) || $result['base_resp']['ret'] != 0) {
			return false;
		}
        
		foreach ($snoopy->headers as $key => $value) {
			$value = trim($value);
			if(preg_match('/^set-cookie:[\s]+([^=]+)=([^;]+)/i', $value,$match))
				$cookie .=$match[1].'='.$match[2].'; ';
		}

		preg_match("/token=(\d+)/i",$result['redirect_url'],$matches);
		if($matches){
			$this->_token = $matches[1];
			$this->log('token:'.$this->_token);
		}
		$this->saveCookie($this->_cookiename,$cookie);
		return $cookie;
	}

	/**
	 * ��cookieд�뻺��
	 * @param  string $filename �����ļ���
	 * @param  string $content  �ļ�����
	 * @return bool
	 */
	public function saveCookie($filename,$content){
		return file_put_contents($filename,$content);
	}

	/**
	 * ��ȡcookie��������
	 * @param  string $filename �����ļ���
	 * @return string cookie
	 */
	public function getCookie($filename){
		if (file_exists($filename)) {
			$mtime = filemtime($filename);
			if ($mtime<time()-$this->_cookieexpired) 
				$data = '';
			else
				$data = file_get_contents($filename);
		} else
			$data = '';
		if($data){
			$send_snoopy = new Snoopy; 
			$send_snoopy->rawheaders['Cookie']= $data;
			$send_snoopy->maxredirs = 0;
			$url = "https://mp.weixin.qq.com/cgi-bin/indexpage?t=wxm-index&lang=zh_CN";
			$send_snoopy->fetch($url);
			$header = implode(',',$send_snoopy->headers);
			$this->log('header:'.print_r($send_snoopy->headers,true));
			preg_match("/token=(\d+)/i",$header,$matches);
			if(empty($matches)){
				return $this->login();
			}else{
				$this->_token = $matches[1];
				$this->log('token:'.$this->_token);
				return $data;
			}
		}else{
			return $this->login();
		}
	}

	/**
	 * ��֤cookie����Ч��
	 * @return bool
	 */
	public function checkValid()
	{
		if (!$this->cookie || !$this->_token) return false;
		$send_snoopy = new Snoopy; 
		$post = array('ajax'=>1,'token'=>$this->_token);
		$submit = "https://mp.weixin.qq.com/cgi-bin/getregions?id=1017&t=ajax-getregions&lang=zh_CN";
		$send_snoopy->rawheaders['Cookie']= $this->cookie;
		$send_snoopy->submit($submit,$post);
		$result = $send_snoopy->results;
		if(json_decode($result,1)){
			return true;
		}else{
			return false;
		}
	}

	private function log($log){
		if ($this->debug && function_exists($this->_logcallback)) {
			if (is_array($log)) $log = print_r($log,true);
			return call_user_func($this->_logcallback,$log);
		}
	}

}