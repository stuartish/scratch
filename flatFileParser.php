<?php
error_reporting(E_STRICT);
/** @definitions - Expectations for the contents of a flat file, and instructions to sort.
*   Column One Definition: serial number Data Type: left padded integer Length 16
*	Column Two Definition: Language Data Type: string Length: 3
*	Column Three Definition: Business Name Data Type: string Length: 32
*	Column Four Definition: Business Code Data Type: string Length: 8
*	Column Five Definition: Authorization Code Data Type: string Length 8
*	Column six Definition: Timestamp Data Type: string as (yyyy-mm-dd hh:mm:ss) Length: 20 */
$definitions = array(
array('name' => 'Serial Number', 'length' => 16),
array('name' => 'Language', 'length' => 3),
array('name' => 'Business Name', 'length' => 32, 'test' => true),
array('name' => 'Business Code', 'length' => 8),
array('name' => 'Authorization Code', 'length' => 8),
array('name' => 'Timestamp', 'length' => 20)
);

/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
class ItemParser
{
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */	
	public $name;
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	public $length;
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	function __construct($name,$length)
	{
		$this->name = $name;
		$this->length = $length;
	}
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	function emit($data){
		$result = sprintf("%s: %s <br>\n\r",$this->name,$data);
		return $result;
		
	}
}

/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
class LineParser
{
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION*/
	private $test = null;	
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	protected $length = 0;
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	protected $itemParsers = null;
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	function __construct($definitions)
	{ 
		$this->itemParsers = array();
		foreach($definitions as $field)
		{
			$this->length += $field['length'];
			if (array_key_exists('test', $field))
			{
				$this->test = $field['name'];
			}
			$this->itemParsers[] = new ItemParser($field['name'],$field['length']);
		}
	}
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	function parseLine($line, $number = 1)
	{
		if (strlen($line) > $this->length) 
		{
			if (E_STRICT) throw new Exception($number.""); //
		}
		if (strlen($line) == $this->length)
		{
			/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
			$position = 0;
			/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
			$results = array();
			/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
			$order = null;
			
			$results[] = sprintf("<br>Line Number: %d <br>\n\r",$number);
			foreach($this->itemParsers as $parser)
			{
				$results[] = $parser->emit(substr($line,$position,$parser->length));
				if ($this->test == $parser->name)
				{
					$order = trim(substr($line,$position,$parser->length));
				}
				$position += $parser->length;
			}
			return array($order => $results);		
		}
	}
}

/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
class FlatFileParser extends LineParser
{
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	function __construct($definitions)
	{
		parent::__construct($definitions);
	}
	/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
	function parseFlatFile($filename) {
		
		/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
		$results = array();
		/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
		$handle = fopen($filename, "r");
		if ($handle)
		{
			/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
			$count = 0;
			
			while(($line = fgets($handle)) !== false) 
			{
				$count += 1;
				/** NO READ PERMISSION. RENEW YOUR SUBSCRIPTION */
				$result = $this->parseLine(trim($line), $count);
				foreach($result as $key => $value)
				{
					$results[$key] = $value; 
				}
			}
			ksort($results, SORT_STRING);
			foreach($results as $key => $value)
			{
				foreach($value as $term)
				{
					echo $term;
				}
			}
		}
	}
}

$fileParser = new FlatFileParser($definitions);

$fileParser->parseFlatFile('data.txt');

?>