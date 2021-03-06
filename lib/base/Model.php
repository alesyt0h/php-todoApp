<?php

try {
	@require_once ROOT_PATH . '/vendor/autoload.php';
} catch (\Throwable $th) {
	throw new Exception("
	<div style='word-break: break-word'>
		MongoDB Driver is not installed, please run: 'composer i'
		on a terminal to install it
	</div>",1);
}

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

	protected function getOne(string $field, mixed $value){

		if($field === 'id'){
			$field = '_' . $field;
		}
		
		$options = ["typeMap" => ['root' => 'array', 'document' => 'array']];
		$document = $this->_collection->findOne([$field => $value], $options);

		return ($document) ? $document : null;
	}

	protected function getMany(string $field, mixed $value){

		if($field === 'id'){
			$field = '_' . $field;
		}

        $isArray = (gettype($value) === 'array') ? true : false;
		$value = ($isArray) ? [ '$in' => $value ] : $value;
		
		$options = ["typeMap" => ['root' => 'array', 'document' => 'array']];
		$documents = $this->_collection->find([$field => $value], $options)->toArray();

		return $documents;
	}

	protected function modifyOne(array $newDoc){

		$document = $this->_collection->findOneAndUpdate(
			['_id' => $newDoc['_id']], 
			[ '$set' => $newDoc], 
			['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
			 'typeMap' => ['root' => 'array', 'document' => 'array']]
		);

		return ($document) ? $document : null;
	}

	protected function deleteOne(string $field, mixed $value){
		
		if($field === 'id'){
			$field = '_' . $field;
		}

		$result = $this->_collection->deleteOne([$field => $value]);

		return $result;
	}

	public function returnObjectId(string $id){
		return new MongoDB\BSON\ObjectId($id);
	}

	protected function assign(mixed $userId, mixed $ids){

		$document = $this->_collection->findOneAndUpdate(
			['_id' => [ '$in' => $ids ]], 
			[ '$set' => [ 'createdBy' => $userId ]]
		);

		return ($document) ? $document : null;
	}

	protected function purgeTodos(){
	
		$now = new DateTime();
		$now->sub(new DateInterval('P1D'));
		$yesterday = $now->format('Y-m-d H:i:s');

		$this->_collection->deleteMany(['createdBy' => null, 'createdAt' => [ '$lt' => $yesterday ]]);
	}
}

?>
