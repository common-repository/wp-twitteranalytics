<?php
ini_set( "display_errors", 0);
function filter($input){
    return htmlspecialchars(trim($input));
}
#PHP 5 Needed
class SaveTweets{
    private  $query=NULL;
    private  $data=array();
    private  $length;
    public function  __construct(array$data) {
        $this->_setVars($data);
    }


	private function _dbInsert()
	{
		global $wpdb, $im_query_table, $im_tweets_table;
		$query_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM ".$im_query_table." WHERE query='".$this->query."'" ) );
		
		if(!$query_id)
		{
		#insert new query string
		$wpdb->query($wpdb->prepare( "INSERT INTO ".$im_query_table." (query) VALUES('".$this->query."')" ));
		$query_id=$wpdb->insert_id;
		}
                  
         for ($i=0 ; $i<$this->length ; $i++)
		   {
		   $id=$this->data['id'][$i];
           $user=filter($this->data['user'][$i]);
           $text=filter($this->data['text'][$i]);
           $date=strtotime($this->data['date'][$i]);
           $location=filter($this->data['location'][$i]);
           $language=filter($this->data['language'][$i]);
 		   $wpdb->query($wpdb->prepare("INSERT INTO `".$im_tweets_table."` VALUES($query_id, $id, '$user', '$text', $date, '$location', '$language')"));
           }
    }


    private  function _setVars($data){

		if($data['query']){
		$this->query=filter($data['query']);
        $this->length=filter($data['length']);
		
		    foreach($data['data'][0] as $k=>$v){
			 
		     $this->data[$k]=$v  ;
	           
		   }
		}
		$this->_dbInsert();
    }

}