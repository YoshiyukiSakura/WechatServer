<?php
date_default_timezone_set('PRC');
define("TOKEN", "weixin");
class Wechat extends CI_Controller {
public $fromUsername;
public $toUsername;
public $time;
public function index()
{
    echo '这是微信接口地址，不具有访问意义';
}
//消息回复功能
public function responseMsg()
{
	//get post data, May be due to the different environments
	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

  	//extract post data
	if (!empty($postStr)){
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
          	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;

            $this->fromUsername = $postObj->FromUserName;
            $this->toUsername = $postObj->ToUserName;
            $this->keyword = trim($postObj->Content);
            $this->time = time();

            $keyword = trim($postObj->Content);
            

            //定义自定义菜单和订阅动作
            $MsgType = trim($postObj->MsgType);
			if($MsgType == 'event')
				{$keyword = trim($postObj->Event);}
			if ($keyword == 'CLICK'||$keyword == 'SCAN') 
				{$keyword = trim($postObj->EventKey);}
            $textTpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";             
			if(!empty( $keyword ))
            {
            	$this->load->database();
                $query = $this->db->query("SELECT content from keywords WHERE keyword='$keyword'");
                if ($this->db->affected_rows() == 1) 
                {
                    $this->replyText($keyword);
                }
                else
                {
                    $this->replyText('暂未收录');
                    $this->messagelog($keyword);
                }

        	}
          		
            
            else
            {
            	echo "Input something...";
            }

}
    else
    {
    	echo "";
    	exit;
    }
}
//用于验证接口
public function valid()
{
    $echoStr = $_GET["echostr"];

    //valid signature , option
    if($this->checkSignature()){
    	echo $echoStr;
    	exit;
    }
}
	//检查签名
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	public function react(){
		if (isset($_GET["echostr"])) 
		{
			$this->valid();
		}
		elseif (isset($GLOBALS["HTTP_RAW_POST_DATA"])) 
		{
			$this->responseMsg();
		}
		else{
			echo "这是微信接口地址，不具有访问意义";
			$keyword = "3";
			$this->load->database();
			$query = $this->db->query("SELECT replyTpl from keywords WHERE keyword='$keyword'");
			$result = $query->row();
			$replyTpl = $result->replyTpl;
			$resultStr = sprintf($replyTpl, $fromUsername=1, $toUsername=2, $time=3);
			echo "<pre>$resultStr</pre>";
		}
	}
	public function create()
{
    $this->load->helper('form');
    $this->load->library('form_validation');
    $data['title'] = '微信回复关键字系统';
    $this->form_validation->set_rules('keyword', 'keyword', 'required');
    $this->form_validation->set_rules('replyTpl', 'replyTpl', 'required');

    if ($this->form_validation->run() === FALSE)
    {
        $this->load->view('templates/header', $data);
        $this->load->view('wechat/create');
        $this->load->view('templates/footer');
    }
    else
    {	
    	$this->load->model('wechat_model');
        $this->wechat_model->set_replyTpl();
        $this->load->view('wechat/success');
    }
}
public function getBookInfo($ISBN)
{
    header("Content-type: text/html; charset=utf-8"); 
    $url = "http://api.douban.com/v2/book/isbn/:".$ISBN;
    $result = file_get_contents($url);
    //echo $result;
    $result = json_decode($result,true);
    $result = $result['title'];
    
    $this->replyText($result);
    
}
public function replyText($content){
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[%s]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>0</FuncFlag>
                </xml>";
    $this->load->database();
    $keyword = $content;
    $query = $this->db->query("SELECT content from keywords WHERE keyword='$keyword'");
    $result = $query->row();
    $contentStr = $result->content;
    $msgType = "text";
    $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $this->time, $msgType, $contentStr);
    echo $resultStr;
}
public function sendTemplate($fromUsername){
    $json = array();
    $data = array();
    if ($fromUsername) {
        $json['touser']=$fromUsername;
    }
    else{
        $json['touser']='oqcNtt-KvR5wA-8IBq47FZEFVa5A';
    }
    $date = date('Y-m-d H:i:s', time()); 
    $json['template_id']='fHN5ogMrGmV5mv_vkfvJodK1Ifgn5smWBYWliq6lZU4';
    $json['topcolor']='#FF0000';
    $json['url']='115.159.119.52';
    $data['first']=array('value'=>"尊敬的用户","color"=>"#FF0000");
    $data['keyword1']=array('value'=>"扫码成功，请放入书籍","color"=>"#FF0000");
    $data['keyword2']=array('value'=>"$date","color"=>"#FF0000");
    $data['remark']=array('value'=>"remark","color"=>"#FF0000");
    $json['data']=$data;
    $json = json_encode($json);
    //echo $json;
    $token = $this->getToken();
    $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    $output = curl_exec($ch);
    curl_close($ch);
    if ($output) {
        //echo $output;
    }
    return $output;
}
public function getToken(){
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxc2686f865fcf8f02&secret=b3844c1d941d6c0c6761bae08ee40020";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    $jsoninfo = json_decode($output, true);

    $access_token = $jsoninfo["access_token"];
    $access_token;
    return $access_token;
}
public function messagelog($content){
    $this->load->database();
    $sql = "INSERT INTO `message` (`userid`, `content`, `time`, `reply`) VALUES ('{$this->fromUsername}', '{$content}', CURRENT_TIMESTAMP, '0');";
    $query = $this->db->query($sql);
}
}

