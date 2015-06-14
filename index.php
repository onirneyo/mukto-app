
<?php
//echo 'Hello World. Bangladesh';
//require_once("Rest.inc.php");

class INDEX //extends REST
{
        public $_allow = array();
        public $_content_type = "application/json";
        public $_request = array();

        private $_method = "";		
        private $_code = 200;
	private $db = NULL;
	
	public function __construct(){
            //parent::__construct();
            $this->inputs();
            $this->dbConnect();		// Initiate Database connection
            $this->sessionStart();
	}
                
        private function sessionStart(){
            session_start();
        }
        
        private function get_status_message(){
            $status = array(
                    100 => 'Continue',  
                    101 => 'Switching Protocols',  
                    200 => 'OK',
                    201 => 'Created',  
                    202 => 'Accepted',  
                    203 => 'Non-Authoritative Information',  
                    204 => 'No Content',  
                    205 => 'Reset Content',  
                    206 => 'Partial Content',  
                    300 => 'Multiple Choices',  
                    301 => 'Moved Permanently',  
                    302 => 'Found',  
                    303 => 'See Other',  
                    304 => 'Not Modified',  
                    305 => 'Use Proxy',  
                    306 => '(Unused)',  
                    307 => 'Temporary Redirect',  
                    400 => 'Bad Request',  
                    401 => 'Unauthorized',  
                    402 => 'Payment Required',  
                    403 => 'Forbidden',  
                    404 => 'Not Found',  
                    405 => 'Method Not Allowed',  
                    406 => 'Not Acceptable',  
                    407 => 'Proxy Authentication Required',  
                    408 => 'Request Timeout',  
                    409 => 'Conflict',  
                    410 => 'Gone',  
                    411 => 'Length Required',  
                    412 => 'Precondition Failed',  
                    413 => 'Request Entity Too Large',  
                    414 => 'Request-URI Too Long',  
                    415 => 'Unsupported Media Type',  
                    416 => 'Requested Range Not Satisfiable',  
                    417 => 'Expectation Failed',  
                    500 => 'Internal Server Error',  
                    501 => 'Not Implemented',  
                    502 => 'Bad Gateway',  
                    503 => 'Service Unavailable',  
                    504 => 'Gateway Timeout',  
                    505 => 'HTTP Version Not Supported');
            return ($status[$this->_code])?$status[$this->_code]:$status[500];
    }
    
    public function get_referer(){
	return $_SERVER['HTTP_REFERER'];
    }
    public function get_request_method(){
        return $_SERVER['REQUEST_METHOD'];
    }
		
    private function inputs(){
            switch($this->get_request_method()){
                    case "POST":
                            $this->_request = $this->cleanInputs($_POST);
                            break;
                    case "GET":
                    case "DELETE":
                            $this->_request = $this->cleanInputs($_GET);
                            break;
                    case "PUT":
                            parse_str(file_get_contents("php://input"),$this->_request);
                            $this->_request = $this->cleanInputs($this->_request);
                            break;
                    default:
                            $this->response('',406);
                            break;
            }
    }		
		
    private function cleanInputs($data){
        $clean_input = array();
        if(is_array($data))
        {
            foreach($data as $k => $v){
                    $clean_input[$k] = $this->cleanInputs($v);
            }
        }
        else
        {
            if(get_magic_quotes_gpc()){
                    $data = trim(stripslashes($data));
            }
            $data = strip_tags($data);
            $clean_input = trim($data);
        }
        return $clean_input;
    }
                
        
    private function set_headers(){
        header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
        header("Content-Type:".$this->_content_type);
    }
		
    private function json($data){
        if(is_array($data)){
            return json_encode($data);
        }
    }

    private function response($data,$status){
        $this->_code = ($status)?$status:200;
        $this->set_headers();
        echo $data;
        exit;
    }
	
    private function dbConnect(){
        $dsn = "pgsql:"
                . "host=ec2-54-83-17-8.compute-1.amazonaws.com;"
                . "dbname=dfnfm0d7dmsvm3;"
                . "user=reyyqxdapmgdjf;"
                . "port=5432;"
                . "sslmode=require;"
                . "password=M7nPoy1bra2hKNXbjdC8M6XIpI";

        $this->db = new PDO($dsn);
        
    }
    
    public function processApi(){
        $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
        if((int)method_exists($this,$func) > 0)
                $this->$func();
        else
                $this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
    }
    
    
    /////////////////////// Write functions Below ///////////////////////////////////////////////////////////

    private function users()
    {
        if($this->get_request_method() != "GET"){
            $this->response('',406);
	}
        $sql = "SELECT employee_id, last_name, first_name, title FROM employees ORDER BY last_name ASC, first_name ASC";
        $result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if(count($result) > 0){
            // If success everythig is good send header as "OK" and return list of users in JSON format
            $this->response($this->json($result), 200);//echo json_encode($res);
        }
        else
        {
            $this->response('',204);
        }
    }
    
    private function registration()
    {
            if($this->get_request_method() != "POST"){
                    $this->response('',406);
            }

            $lname = $this->_request['last_name'];		
            $fname= $this->_request['first_name'];
            $title = $this->_request['title'];

            if( !empty($lname) and !empty($fname) and !empty($title)){
                
                $sql = "INSERT INTO employees ( last_name, first_name,title)
                            VALUES ( '$lname', '$fname','$title')";
                if ($this->db->query($sql) == TRUE) {
                    $msg = array("Insert Status" => "Success");
                    $this->response($this->json($msg), 200);
                } else {
                    $msg = array("Insert Status" => "Fail");
                    $this->response($this->json($msg), 406);
                }
            }
            $error = array("Status" => "Failed", "Msg" => "Fill all fields correctly");
            $this->response($this->json($error), 400);
    }
    
    private function login(){
                // Cross validation if the request method is POST else it will return "Not Acceptable" status
                if($this->get_request_method() != "POST"){
                        $this->response('',406);
                }

                $id = $this->_request['id'];		
                $fname = $this->_request['first_name'];
                //$password = md5($password);


                // Input validations
                if(!empty($id) and !empty($fname))
                {
                        //if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        $sql = "SELECT first_name, last_name,title FROM employees WHERE first_name = '".$fname."' AND  employee_id = '".$id."'LIMIT 1";
                        $result = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                        if(count($result) > 0)
                        {
                            //$result = $result->fetch_assoc();// mysql_fetch_array($sql,MYSQL_ASSOC);
                            echo $result['first_name'];
                            echo $result['last_name'];
                            //$_SESSION['USER_EMAIL'] = $result['user_email'];
                            //$_SESSION['USER_STATUS'] = $result['user_status'];
                            $this->response($this->json($result), 200);
                        }
                        $this->response('', 204);	// If no records "No Content" status
                        //}
                }
                // If invalid inputs "Bad Request" status message and reason
                $error = array("status" => "Failed", "msg" => "Invalid Email address or Password");
                $this->response($this->json($error), 400);
        }
                
        private function logout()
        {
            if($this->get_request_method() != "GET"){
                $this->response('',406);
            }
            if( isset($_SESSION['USER']) ){
                session_destroy();
                $this->response('', 200);
            }
            else {
                $this->response('', 204);
            }               
        }
    
    private function remove(){
        if($this->get_request_method() != "POST"){
                $this->response('',406);
        }
        
        $id = $this->_request['id'];
        $sql = "SELECT * FROM employees WHERE employee_id = ".$id;
        $result = $this->db->query($sql)->fetchAll();
        //$rows = $result->fetch(PDO::FETCH_NUM);//$result->rowCount();
        //echo count($result)." rows";
        //echo $rows . " row(s) returned.\n";
        if(count($result) > 0)
        {
            $sql = "DELETE FROM employees WHERE employee_id = ".$id;
            $this->db->query($sql);
            $success = array("status" => "Success", "msg" => "Account remove successfully.");
            $this->response($this->json($success),200); 
        }
        else {
            $this->response('',401);
        }
    }
    
}
	
$api = new INDEX; 
$api->processApi();
//$urlParams = explode('/', $_SERVER['REQUEST_URI']);
//$functionName = $urlParams[1];
//$api->$functionName();                  




