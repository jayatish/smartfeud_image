<?php
	/**
	*This is Core Class v2.0 for CodeIgniter
	*Core Class Extended from CI_Model
	*I'm trying to compact all DB operations in one Class in few functions.
	*For Programmer perform a quick Coding without of Headache in DB Operations 
	*Special Thing is that after it's Joining Operation it returns seperated dataset by each table 
	*The working principle like CodeIgniator DB Active Class and Cake PHP Model Class
	*@author	Soham Krishna Paul	(+918013066558)
	*/
	class Core extends CI_Model
	{
		private $table=null;
		public function __construct($table="users")
		{	
			parent::__construct();
			$this->table=$table;
		}
		public function fetchRow($where=null,$order_by=array(), $limit='', $select='*')
		{
			$this->db->select($select);
			$this->db->from($this->table);
			if($where){
				$this->db->where($where);
			}
			if(count($order_by)>0){
				$this->db->order_by($order_by[0], $order_by[1]);//$order_by[0]=name of field // $order_by[1]=asc/desc;
			}
			$result=$this->db->get();
			return $result->row_array();
		}
		public function fetchRecord($where=null,$order_by=array(), $limit='', $select='*')
		{
			$this->db->select($select);
			$this->db->from($this->table);
			if($where)
				$this->db->where($where);
			if(count($order_by)>0){
				$this->db->order_by($order_by[0], $order_by[1]);//$order_by[0]=name of field // $order_by[1]=asc/desc;
			}
			if($limit!='')
				if(is_numeric($limit))
					$this->db->limit($limit);
			$result=$this->db->get();
			return $result->result_array();
		}
		public function addEdit($data,$where=null)
		{
			if($where)
			{
				return $this->update($data,$where);
			}
			else
			{
				return $this->insert($data);
			}
		}
		public function find($opta=array())
		{
			$this->buildQuery($opta);
			$result=$this->db->get();
			$originalResult=$result->result_array();
			//echo $this->db->last_query();
			return $this->buildResult($originalResult,$opta);
		}
		public function query($str)
		{
			$result=$this->db->query($str);
			return $result->result_array();
		}
		private function insert($data)
		{
			$this->db->insert($this->table,$data);
			return $this->db->insert_id();
		}
		private function update($data,$where)
		{
			$this->db->update($this->table,$data,$where);
			return $this->db->affected_rows();
		}
		public function delete($where)
		{
			$this->db->delete($this->table,$where);
			return $this->db->affected_rows();
		}
		public function firstRow($where,$select='*')
		{
			$this->db->select($select);
			$this->db->from($this->table);
			if($where)
				$this->db->where($where);
			$result=$this->db->get();
			return $result->first_row();
		}
		public function lastRow($where,$select='*')
		{
			$this->db->select($select);
			$this->db->from($this->table);
			if($where)
				$this->db->where($where);
			$result=$this->db->get();
			return $result->last_row();
		}
		public function begin()
		{
			$this->db->trans_start();
		}
		public function end()
		{
			$this->db->trans_complete();
		}
		public function status()
		{
			return $this->db->trans_status();
		}
		public function getFields()
		{
			return $this->db->list_fields($this->table);
		}
		public function getFieldsMeta($str='')
		{
			if(!empty($str))
				$fields = $this->db->field_data($str);
			else
				$fields = $this->db->field_data($this->table);
			return $fields;
		}
		
		public static function getColsReplace($table,$replaceColSet=array(),$alias='')
		{
			$coreObj=new Core($table);
			$colSet=$coreObj->getFields();
			$str="";
			foreach($colSet as $value)
			{
				if(array_key_exists($value,$replaceColSet))
				{
					$str.=(($alias!='')?$alias.'.':'').$value." as ".$replaceColSet[$value].", ";
				}
				else
				{
					$str.=(($alias!='')?$alias.'.':'').$value.", ";
				}
			}
			return substr($str,0,-2);
		}
		public static function getColsExcept($table,$replaceColSet=array(),$alias='')
		{
			$coreObj=new Core($table);
			$colSet=$coreObj->getFields();
			$str="";
			foreach($colSet as $value)
			{
				if(!array_key_exists($value,$replaceColSet))
				{
					$str.=(($alias!='')?$alias.'.':'').$value.", ";
				}
			}
			return substr($str,0,-2);
		}
		/**
		 *$where = "name='Joe' AND status='boss' OR status='active'";
		 */
		private function buildQuery($opta=array())
		{
			
			/***************Checking Select is given or not***************/
			$this->db->from($this->table);
			$getCurrentTableFields=$this->getFieldsMeta($this->table);
			$columns='';
			foreach($getCurrentTableFields as $value)
			{
				$columns.=$this->table.'.'.$value->name.' as '.$this->table.'$'.$value->name.', ';
			}
			/***************End Select is given or not***************/
			
			/***************Check Join***************/
			if(!empty($opta['join']))
			{
				foreach($opta['join'] as $value)
				{
					if(!empty($value['on']))
					{
						if(!empty($value['join_type']))
						{
						   $this->db->join($value['table'], $value['on'], $value['join_type']);
						}
						else
							$this->db->join($value['table'], $value['on']);
						$getCurrentTableFields=$this->getFieldsMeta($value['table']);
						foreach($getCurrentTableFields as $valuex)
						{
							$columns.=$value['table'].'.'.$valuex->name.' as '.$value['table'].'$'.$valuex->name.', ';
						}
					}
				}
			}
			if(!empty($columns))
				$this->db->select(substr($columns,0,-2));
			/**************End Join************/
			
			/**************Check Where*********/
			if(!empty($opta['where'])){
				$this->db->where($opta['where']);
			}
			/**************End Where*********/
			/**************Check Group BY*******/
			if(!empty($opta['group_by']))
			{
				$this->db->group_by($opta['group_by']); 
			}
			/*************End OrderBy********/
			/**************Check Order BY*******/
			if(!empty($opta['order_by']))
			{
				foreach($opta['order_by'] as $key=>$value)
					$this->db->order_by($key,$value); 
			}
			/*************End OrderBy********/
			/**************Check Limit*******/
			if(!empty($opta['order_by']))
			{
				foreach($opta['order_by'] as $key=>$value)
					$this->db->order_by($key,$value); 
			}
			/*************Limit********/
			if(!empty($opta['limit']) && !empty($opta['offset']))
			{
				$this->db->limit($opta['limit'],$opta['offset']); 
			}
			else{
				if(!empty($opta['limit']))
				{
					$this->db->limit($opta['limit']); 
				}
			}
			/*************End Limit********/
		}
		
		/**
		*Build Output Result in Well Organized Format
		*@access Private
		*@params (originalResult array(),that array sent via find method (array()/string))
		*@return well organized array
		*/
		private function buildResult($originalResult=array(),$opta=array())
		{
			$result=array();
                        $count=array();
                        $primaryKey=array();
                        $hasValue=array();
                        $count[$this->table]=0;
                        $fields=$this->getFieldsMeta($this->table);
                        foreach($fields as $fieldValue){
                            if($fieldValue->primary_key)
                                $primaryKey[$this->table]=$fieldValue->name;
                        }
						if(!empty($opta['join']))
							foreach($opta['join'] as $value){
								$count[$value['table']]=0;
								$fields=$this->getFieldsMeta($value['table']);
								foreach($fields as $fieldValue){
									if($fieldValue->primary_key)
										$primaryKey[$value['table']]=$fieldValue->name;
								}
							}
			foreach($originalResult as $key=>$value)
			{
				if(empty($opta['show_table']) || in_array($this->table, $opta['show_table']))
				{
					if(empty($hasValue[$this->table][$value[$this->table.'$'.$primaryKey[$this->table]]]))
					{
						$fields=$this->getFieldsMeta($this->table);
						foreach($fields as $key=>$fieldValue){
						$result[$this->table][$count[$this->table]][$fieldValue->name]=$value[$this->table.'$'.$fieldValue->name];
						}
						$hasValue[$this->table][$value[$this->table.'$'.$primaryKey[$this->table]]]=true;
						$count[$this->table]++;
					}
				}
				if(!empty($opta['join']))
					foreach($opta['join'] as $eachJoinTable)
					{
						if(empty($opta['show_table']) || in_array($eachJoinTable['table'], $opta['show_table']))
						{
							if(empty($result[$eachJoinTable['table']]))
								$result[$eachJoinTable['table']]=array();
							if(empty($value[$eachJoinTable['table'].'$'.$primaryKey[$eachJoinTable['table']]]))
								continue;
							if(empty($hasValue[$eachJoinTable['table']][$value[$eachJoinTable['table'].'$'.$primaryKey[$eachJoinTable['table']]]]))
							{                                   
								$fields=$this->getFieldsMeta($eachJoinTable['table']);
								foreach($fields as $key=>$fieldValue){
									$result[$eachJoinTable['table']][$count[$eachJoinTable['table']]][$fieldValue->name]=$value[$eachJoinTable['table'].'$'.$fieldValue->name];
								}
								$hasValue[$eachJoinTable['table']][$value[$eachJoinTable['table'].'$'.$primaryKey[$eachJoinTable['table']]]]=true;
								$count[$eachJoinTable['table']]++;
							}
						}
					}
			}
                        return $this->scanning($result);
		}
		
		private function scanning($post = array())
                {
                    $new_post = array();
                    foreach($post as $key => $value)
                    {
                        if (is_array($value))
                        {
                           unset($post[$key]);
                           $new_post[$key] = $this->scanning($value);
                        }
                        else
                        {
                            $new_post[$key]=html_entity_decode(stripslashes($value) , ENT_QUOTES);
                            unset($post[$key]);
                        }
                    }
                    return $new_post;
                }
		
                private function filter($data)
                {
                    $temp=array();
                    foreach($data as $key=>$value)
                    {
                        $temp[$key]=addslashes(htmlspecialchars($value, ENT_QUOTES));
                    }
                    return $temp;
                }
	}
?>