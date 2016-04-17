<?PHP
////////////////////////////////////////////////////////////
// PHP Class for FANART
//
// https://fanart.tv/
//
// Exmaple
// require dirname(__file__)./'include/class.fanart.php';
// $fanart = new fanart($id,$api_key);
//
//
////////////////////////////////////////////////////////////

class fanart
{
   private $api_key="";
   private $movies;
   private $tv;
   private $url_data;
   private $url_tv;
   public $id;
   public $error='';
   public $error_code;

   function __construct($id,$api_key)
   {
      $this->id=$id;
      $this->api_key=$api_key;
      $this->url_movies="http://webservice.fanart.tv/v3/movies/{$this->id}";
      $this->url_tv="http://webservice.fanart.tv/v3/tv/{$this->id}";
   }

   public function fetch($tv=FALSE)
   {
      $info_curl;
      $url;

      if($tv)
      $url=$this->url_tv;
      else
      $url=$this->url_movies;

      $info_curl=curl_init();
      curl_setopt($info_curl,CURLOPT_RETURNTRANSFER,1);
      curl_setopt($info_curl,CURLOPT_HTTPHEADER, array("api-key: {$this->api_key}"));
      curl_setopt($info_curl,CURLOPT_URL,$url);
      $temp=curl_exec($info_curl);

      $this->error_code=curl_getinfo($info_curl,CURLINFO_HTTP_CODE);

      curl_close($info_curl);

      if($this->error_code!=200 || empty($temp))
      {
         $this->error='Unable to get information from TVDB';
         return FALSE;
      }
      else
      {
         if($tv)
         $this->tv=$temp;
         else
         $this->movies=$temp;

         return TRUE;
      }
      return FALSE;
   }

   public function decode_json($temp)
   {
      if(!empty($temp))
      return json_decode($temp,true);
      else
      return FALSE;

   }

   public function getimdb()
   {
      $temp=$this->decode_json($this->movies);
      return str_replace("tt","",$temp['imdb_id']);
   }

   public function gettvdb()
   {
      $temp=$this->decode_json($this->tv);
      return $temp['thetvdb_id'];
   }

   public function getmoviebanner()
   {
      $bann=$this->decode_json($this->movies)['moviebanner'];
      $temp=array();

      $count=((count($bann)>2)?2:count($bann));

      for($i=0;$i<$count;$i++)
      {
         if($bann[$i]['lang']=='en')
         $temp[]=$bann[$i]['url'];
      }

      return $temp;
   }

   public function gettvbanner()
   {
      $bann=$this->decode_json($this->tv)['tvbanner'];
      $temp=array();

      $count=((count($bann)>2)?2:count($bann));

      for($i=0;$i<$count;$i++)
      {
         if($bann[$i]['lang']=='en')
         $temp[]=$bann[$i]['url'];
      }

      return $temp;
   }
}