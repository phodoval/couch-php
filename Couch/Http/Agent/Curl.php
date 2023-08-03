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
namespace Couch\Http\Agent;

use Couch\Http\Request;
use Couch\Http\Exception;
use CurlHandle;

/**
 * @package   Couch
 * @subpackage Couch\Http\Agent
 * @object    Couch\Http\Agent\Curl
 * @uses      Couch\Http\Request
 * @uses      Couch\Http\Exception
 * @author    Kerem Güneş <k-gun@mail.com>
 */
class Curl
   extends \Couch\Http\Agent
{
   /**
    * Run a request using cURL.
    *
    * @param  Couch\Http\Request $request
    * @return bool
    * @throws Couch\Http\Exception
    */
   public function run(Request $request)
   {
      // check cURL extension
      if (!extension_loaded('curl')) {
         throw new Exception('cURL extension not found!');
      }

      $this->link =@ curl_init($request->uri);
      if ($this->link instanceof CurlHandle) {
         $headers = [];
         // for proper response headers/body pairs
         $headers[] = 'Expect: ';
         foreach ($request->headers as $key => $value) {
            // actually remove header command
            if ($value === null) {
               continue;
            }
            $headers[] = sprintf('%s: %s', $key, $value);
         }

         // cURL options
         $options = array(
            CURLOPT_CUSTOMREQUEST  => $request->method,
            CURLOPT_HEADER         => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CONNECTTIMEOUT => $this->config['timeout'],
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLINFO_HEADER_OUT    => true
         );

         // care of request methods
         if ($request->method == Request::METHOD_HEAD) {
            $options[CURLOPT_NOBODY] = true;
            $options[CURLOPT_FOLLOWLOCATION] = true;
         } else {
            $options[CURLOPT_RETURNTRANSFER] = true;
            if ($request->method != Request::METHOD_GET) {
               $options[CURLOPT_POSTFIELDS] = $request->body;
            }
         }

         curl_setopt_array($this->link, $options);

         // prevent output whole reponse ie if NOBODY=1
         ob_start();
         $result =@ curl_exec($this->link);
         $this->result = ob_get_clean();
         if (is_string($result)) {
            $this->result = $result;
         }

         // fail!
         if ($this->result === false) {
            $this->failCode = curl_errno($this->link);
            $this->failText = curl_error($this->link);

            // cleanize
            $this->clean();

            return false;
         }

         // cleanize
         $this->clean();

         return true;
      }

      return false;
   }

   /**
    * Clean resource.
    *
    * @return void
    */
   public function clean()
   {
      if (is_resource($this->link)) {
         curl_close($this->link);
         $this->link = null;
      }
   }
}
