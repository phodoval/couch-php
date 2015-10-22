<?php
namespace Couch;

use Couch\Object\Database;

class Query
{
    private $data = [],
            $dataString = '';
    private $database;

    public function __construct(array $data = []) {
        if (!empty($data)) {
            $this->data = $data;
        }
    }
    public function __toString() {
        return $this->toString();
    }

    public function setDatabase(Database $database) {
        $this->database = $database;
    }

    public function getDatabase() {
        return $this->database;
    }

    public function run() {
        if (!$this->database) {
            throw new Exception(sprintf(
                'Set database first calling %s::setDatabase() to run a request!', __class__));
        }
        return $this->database->getDocumentAll($this->data);
    }

    public function set($key, $value) {
        $key = strtolower(trim($key));
        $this->data[$key] = $value;
        return $this;
    }
    public function get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
    }

    public function toArray() {
        return $this->data;
    }

    public function toString() {
        if (!empty($this->dataString)) {
            return $this->dataString;
        }
        $data = [];
        foreach ($this->data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $data[] = sprintf('%s=%s', $key, urlencode($value));
        }
        return ($this->dataString = join('&', $data));
    }

    public function skip($num) {
        $this->data['skip'] = $num;
        return $this;
    }
    public function limit($num) {
        $this->data['limit'] = $num;
        return $this;
    }
}