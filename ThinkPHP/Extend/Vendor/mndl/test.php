<?php
/**
 * ΢����չ�ӿڲ���
 */
	header("Content-type:text/html;charset=utf-8");
	include("wechatext.class.php");

	function logdebug($text){
		file_put_contents('log.txt',$text."\n",FILE_APPEND);		
	};

	$options = array(
		'account'=>'dch-d0dq431',//����ƽ̨�˺�
		'password'=>'miaqo!q@s#d$',//����ƽ̨����
		'datapath'=>'cookie_',
			'debug'=>true,
			'logcallback'=>'logdebug'	
	); 
	$wechat = new Wechatext($options);
	if ($wechat->checkValid()) {
		/* //��ȡ�����б�
		$grouplist = $wechat->getGroupList();
		var_dump($grouplist);	
		//��ȡ�û��б�
		$userlist = $wechat->getUserlist(0,10);
		var_dump($userlist);
		$user = $userlist[0];
		// ��ȡ�û���Ϣ
		$userdata = $wechat->getInfo($user['id']);
		var_dump($userdata);
		// ��ȡ�ѱ����ͼ����Ϣ
		$newslist = $wechat->getNewsList(0,10);
		var_dump($newslist); */
		//��ȡ�û�������Ϣ
		$topmsg = $wechat->getTopMsg();
		var_dump($topmsg);
		/* $msglist = $wechat->getMsg();
		var_dump($msglist);
		// �����ظ���Ϣ
		if ($topmsg && $topmsg['has_reply']==0){
		    $wechat->send($user['id'],'hi '.$topmsg['nick_name'].',rev:'.$topmsg['content']);
		    $content = '����һ��Wechatext�����Ĳ���΢��';
		    $imgdata = file_get_contents('http://github.global.ssl.fastly.net/images/modules/dashboard/bootcamp/octocat_fork.png');
		    $img = '../data/send.png';
		    file_put_contents($img,$imgdata);
		    //�ϴ�ͼƬ
		    $fileid = $wechat->uploadFile($img);
		    echo 'fileid:'.$fileid;
		    //if ($fileid) $re = $wechat->sendImage($user['id'],$fileid);
		    //����ͼ����Ϣ
		    $re = $wechat->sendPreview($userdata['user_name'],$content,$content,$content,$fileid,'http://github.com/dodgepudding/wechat-php-sdk');
		    var_dump($re);
		    //������Ƶ
		    //$re = $wechat->sendVideo($user['id'],$fileid);
			$re = $wechat->getFileList(2,0,10);
			var_dump($re); */
		/* } else {
			echo 'no top msg';
		} */	
	} else {
		echo "login error";
	}