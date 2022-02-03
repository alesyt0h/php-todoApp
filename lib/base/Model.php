<?php

require_once ROOT_PATH . '/vendor/autoload.php';

class Model
{
	protected $_db = null;
	protected $_client = null;
	protected $_collection;
	
	public function __construct(){

		$settings = parse_ini_file(ROOT_PATH . '/config/settings.ini', true);
		$connectionString = $settings['database']['connection_string'];
		$this->_db = $settings['database']['dbname'];

		$this->_client = new MongoDB\Client($connectionString);
	}
	
	protected function _setCollection(string $collection){
		$db = $this->_db;
		$this->_collection = $this->_client->$db->$collection;
	}
	
	protected function insertOne(array $data){
		$result = $this->_collection->insertOne($data);

		return $result->getInsertedId();
	}

	protected function getOne(string $field, string $value){

		if($field === 'id'){
			$field = '_' . $field;
			$value = new MongoDB\BSON\ObjectId($value);
		}
		
		$options = ["typeMap" => ['root' => 'array', 'document' => 'array']];
		$result = $this->_collection->findOne([$field => $value], $options);

		if($result){

			$result['_id'] = (string) $result['_id'];

			foreach($result as $key => $value){
				$result['id'] = $value;
				unset($result['_id']);

				break;
			}
		}

		return $result;
	}
}

?>
