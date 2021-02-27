<?php
class Dati {
    
    public function __construct() {
        
    }
    
    public function dati($username, $password) {
        $user_info = $this->getUserinfo($username, $password);
        $userOID = $user_info['uid'];
        $unit_name = $user_info['unit_name'];
        $depart_name = $user_info['depart_name'];
        $uname = $user_info['uname'];
        $number = $user_info['number'];
        $queryExaminationList = json_decode($this->postUrl("https://api2.tfhulian.com/admis/confidentialExamination/queryExaminationList.rest", "userOID=$userOID"), true);
        $questions = $queryExaminationList['data']['questions'];
        if ($questions) {
            $exam_info = [];
            for ($i = 0; $i < count($questions); $i++) {
                $OID = $questions[$i]['id'];
                $rightAnswers = $questions[$i]['rightAnswers'];
                $exam_info[] = array(
                    'OID' => $OID,
                    'rightAnswers' => $rightAnswers,
                    'userAnswer' => $rightAnswers
                );
            }
            $exam_info = json_encode($exam_info);
            $getResultsOfExaminationQuestions = json_decode($this->postUrl("https://api2.tfhulian.com/admis/confidentialExamination/getResultsOfExaminationQuestions.rest", "examInfo=$exam_info&examTime=60&userOID=$userOID"), true);
            
            $data = array(
                'unit_name' => $unit_name,
                'depart_name' => $depart_name,
                'uname' => $uname,
                'number' => $number,
                'date' => date("Y-m-d")
            );
            $is_ok = $getResultsOfExaminationQuestions['code'] == 200;
            $res = array(
                'code' => $is_ok ? 200 : 201,
                'data' => $data,
                'msg' => $is_ok ? '答题成功' : '答题失败'
            );
        }
        return $res;
    }
    
    private function getUserinfo($username, $password) {
        $Authorization = $this->getAuthorization($username, $password);
        $header[] = 'Authorization:'.$Authorization; 
        $header[] = 'client:ios'; 
        $header[] = 'edition:9999'; 
        $detail = json_decode($this->postUrl('https://api2.tfhulian.com/v_309/users/detail', null, $header), true);
        $user_info = $detail['data'];
        return $user_info;
    }
    
    private function getAuthorization($username, $password) {
        $token = json_decode($this->postUrl('https://api2.tfhulian.com/oauth2/token', 'client_id=testclient&client_secret=testpass&grant_type=password&password='.$password.'&username='.$username), true);
        if ($token['code'] == 200) {
            $data = $token['data'];
            $Authorization = $data['token_type'].' '.$data['access_token'];
            return $Authorization;
        } else {
            $msg = $token['data']['message'];
            $res = array(
                'code' => 201,
                'msg' => $msg
            );
            echo json_encode($res);
            exit();
        }
    }
    
    private function postUrl($url, $data = false, $header = false) {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包x
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制 防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        
        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
        }
    
        $tmpInfo = curl_exec($curl); // 执行操作
        if(curl_errno($curl)) {
           echo 'Errno'.curl_error($curl); // 捕捉异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
}
?>