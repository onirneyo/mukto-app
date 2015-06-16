
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
	
	
	public function __construct(){
           
            $this->inputs();
          
         //   $this->sessionStart();
	}
                
        private function sessionStart(){
            session_start();
            //
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
                         $this->_request = $this->cleanInputs($_GET);
                        break;
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
	
  
   
    public function processApi(){
        $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
        if((int)method_exists($this,$func) > 0)
                $this->$func();
        else
                $this->response('',404);				
    }
    
    
    /////////////////////// Write functions Below ///////////////////////////////////////////////////////////
private function greetings() {



        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $greetings = array("i am kitty" => "",
            "how are you" => " I am fine.",
            "how is your day going" => " Alright.",
            "what is your name" => " I am Bot.",
            "what's up" => " Not much.",
            "what's new" => " Not much.",
            "how’s your day" => " Fine.",           
            "good morning" => " Good morning.",
            "good night" => " Good night.",
            "good evening" => " Good evening.",
            "nice to meet you"=> " You too.",
            "good to see you"=> " You too."
            
        );
        // echo $greetings['i am kitty'];
        $question = strtolower($this->_request['q']);
        $question = preg_replace('/\s+/', ' ', $question);
       // $question = str_replace('!', '.', $question);
        
        $question = str_replace('?', '.', $question);
        //  echo $question;

          if (strpos($question, 'hello!') !== false or strpos($question, 'hi!') !== false or strpos($question, 'good morning!') !== false
                or strpos($question, 'good night!') !== false or strpos($question, 'good evening!') !== false) {
            $res = NULL;
            $question = str_replace('!', '.', $question);

            $dot = ".";

            $position = stripos($question, $dot); //find first dot position
            $p = 0;
            $offset = 0;
            while ($position !== false) { //if there's a dot in our soruce text do
                // echo $first_two.'=' ;
                // $p=$position+1;
                $p = $offset;
                $position = stripos($question, $dot, $offset);
                $offset = $position + 1; //prepare offset
                $first_two = substr($question, $p, $position - $p );

                /*for ($i = 0; $i < count($greetings); $i++) {
                    if(strpos($first_two,$greetings)!==false)
                    {
                        $ans=$ans.$greetings[$first_two];
                    }
                    
                }*/
             foreach ($greetings as $key=>$value)
             {
                // echo $key." ".$value."\n";
                 if(strpos($key, $first_two)!==false)
                 {
                     
                     $res=$res.$value;
                     //echo $ans."\n";
                 }
                         
             }


                //echo $first_two."\n" ; //add a dot
            }
            //echo '\n';



            

            $response = array("answer" => "Hello, Kitty!" . $res);
            $this->response($this->json($response), 200);
        }
       else
        {
         //  $response=NULL;
            $this->response('',404); 
        }
        
    }
    
    private function weather() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        //$ques=preg_replace('/\s+/', ' ', $ques);
        //  $ques=str_replace('?','',$ques);
        //  $ques=explode(" ",trim($ques));
        $question = strtolower($this->_request['q']);
        $ques = preg_replace('/\s+/', ' ', $question);
        $ques = str_replace('?', '', $ques);
        $ques = explode(" ", trim($ques));

        $location = $ques[count($ques) - 1];
        $url = 'http://api.openweathermap.org/data/2.5/weather?q=' . $location;
        $c = file_get_contents($url);
        $res = json_decode($c, true);
        //echo $c ;
        if ($res['cod'] === 200) {
            if (strpos($question, 'temperature') !== false) {
                $k = $res['main']['temp'];
                $c = $k - 273.15;
                $response = array("answer" => $c . "C" . " or " . $k . "K");
                $this->response($this->json($response), 200);
            }
            else if (strpos($question, 'weather') !== false) {
                
                $weather = explode(" ", $question);

                $res = strtolower($res['weather'][0]['main']);
                if (strcmp($weather[2], $res) === 0) {
                    $response = array("answer" => "Yes");
                    $this->response($this->json($response), 200);
                } else {
                    $response = array("answer" => "No");
                    $this->response($this->json($response), 200);
                }
            }            
            else if (strpos($question, 'humidity') !== false) {
                $hum = $res['main']['humidity'];
                $response = array("answer" => $hum . "%");
                $this->response($this->json($response), 200);
            } 
        }
        else if($res['cod'] === "404")
        {
           // echo "not f.";
             $response = array("answer" => "Not found.");
             $this->response($this->json($response), 404);
        }
        
    }
    private function qa()
    {
        if($this->get_request_method() != "GET")
        {
            $this->response('',406);
	}
        
        $ques = $this->_request['q'];
        $url = 'http://quepy.machinalis.com/engine/get_query?question='. urlencode($ques);
        $sQuery = file_get_contents($url);  
        
        $sQuery = json_decode($sQuery, true);
        $sq = $sQuery['queries'][0]['query'];
        $response = NULL;
         if($sq!== NULL)
        {
       
        $url = 'http://dbpedia.org/sparql?query='.urlencode($sq).'&format=application/json';
        $data = file_get_contents($url);
        $data = json_decode($data,true);
        if($data['results']['bindings'] != NULL)
        {
             
            $i = $data['head']['vars'][0];
            
            $multires = $data['results']['bindings'];
           
            foreach($multires as $res)
            {
               
                $t = $res[$i];
                
                    
                    if (array_key_exists('xml:lang', $t)) {
                        if ($t['xml:lang'] == 'en') {
                            $response = array("answer" => $t['value']);
                        }
                    } else {
                        $response = array("answer" => $t['value']);
                    }
            }
            $this->response($this->json($response),200);
        }
        else
        {
            $response  = array("answer"=>"Your majesty! Jon Snow knows nothing! So do I!");
            $this->response($this->json($response),404);
        }
        }
        else
        {
            $response  = array("answer"=>"Your majesty! Jon Snow knows nothing! So do I!");
            $this->response($this->json($response),404);
        }
    }
   
    
    
}
	
$api = new INDEX;
$api->processApi();


