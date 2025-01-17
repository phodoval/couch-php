<?php
/**
 * Copyright 2015 Kerem Güneş
 *    <k-gun@mail.com>
 *
 * Apache License, Version 2.0
 *    <http://www.apache.org/licenses/LICENSE-2.0>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Couch;

/**
 * @package Couch
 * @object  Couch\Query
 * @author  Kerem Güneş <k-gun@mail.com>
 */
class Query
{
   /**
    * Database object.
    * @var Couch\Database
    */
   private $database;

   /**
    * Query data & string.
    * @var array, string
    */
   private $data = [], $dataString = '';

   /**
    * Object constructor.
    *
    * @param Couch\Database|null $database
    * @param array               $data
    */
   public function __construct(Database $database = null, array $data = [])
   {
      // set database if provided
      if ($database) {
         $this->database = $database;
      }
      // set data if provided
      if (!empty($data)) {
         $this->data = $data;
      }
   }

   /**
    * Magic method for stringify action.
    *
    * @return string
    */
   public function __toString()
   {
      return $this->toString();
   }

   /**
    * Set database.
    *
    * @param  Couch\Database $database
    * @return void
    */
   public function setDatabase(Database $database)
   {
      $this->database = $database;
   }

   /**
    * Get database.
    *
    * @return Couch\Database|null
    */
   public function getDatabase()
   {
      return $this->database;
   }

   /**
    * Get documents by query data.
    *
    * @return mixed
    */
   public function run()
   {
      if (!$this->database) {
         throw new Exception(sprintf(
            'Set database first on init or calling'.
            ' %s::setDatabase() to run a request!', __class__));
      }

      return $this->database->getDocumentAll($this->data);
   }

   /**
    * Set query param.
    *
    * @param  string $key
    * @param  mixed  $value
    * @return self
    */
   public function set($key, $value)
   {
      $key = strtolower(trim($key));
      $this->data[$key] = $value;

      return $this;
   }

   /**
    * Get query param.
    *
    * @param  string $key
    * @return mixed|null
    */
   public function get($key)
   {
      if (isset($this->data[$key])) {
         return $this->data[$key];
      }
   }

   /**
    * Get query params as array.
    *
    * @return array
    */
   public function toArray()
   {
      return $this->data;
   }

   /**
    * Get query params as string.
    *
    * @return string
    */
   public function toString()
   {
      // check if already generated before
      if (!empty($this->dataString)) {
         return $this->dataString;
      }

      $data = [];
      foreach ($this->data as $key => $value) {
         // proper CouchDB booleans
         if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
         }
         $data[] = sprintf('%s=%s', $key, urlencode($value));
      }

      return ($this->dataString = join('&', $data));
   }

   /**
    * Add skip param to query data.
    *
    * @param  int $num
    * @return self
    */
   public function skip($num)
   {
      $this->data['skip'] = $num;

      return $this;
   }

   /**
    * Add limit param to query data.
    *
    * @param  int $num
    * @return self
    */
   public function limit($num)
   {
      $this->data['limit'] = $num;

      return $this;
   }

   public function attachments($bool)
   {
      $this->data['attachments'] = $bool;

      return $this;
   }
}
