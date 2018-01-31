<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 namespace GraphPress\Controllers;

use CapMousse\ReactRestify\Http\Request;
use CapMousse\ReactRestify\Http\Response;
use CapMousse\ReactRestify\Http\Session;
use Pho\Kernel\Kernel;
use Valitron\Validator;
use PhoNetworksAutogenerated\User;
use Pho\Lib\Graph\ID;


/**
 * Takes care of Content
 * 
 * 10/10
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class ContentController extends \Pho\Server\Rest\Controllers\AbstractController 
{
    /**
     * Star 
     *
     * @score 10/10
     * 
     * @param Request $request
     * @param Response $response
     * @param Session $session
     * @param Kernel $kernel
     * @param string $id
     * 
     * @return void
     */
    public function star(Request $request, Response $response, Session $session, Kernel $kernel, string $id)
    {
        $data = $request->getQueryParams();
        $v = new Validator($data);
        $v->rule('required', ['url']);
        $v->rule('url', ['url']);
        if(!$v->validate()) {
            $this->fail($response, "Url required.");
            return;
        }
        $url = $data["url"];
        $i = $kernel->gs()->node($id);
        // query if such page exists
        $res = $kernel->index()->query("MATCH (n:page {Url: {url}}) RETURN n", ["url"=>$url]);
        if(count($res->results())==0) {
            $page = $kernel->founder()->post($url);
        }
        else {
            $page = $kernel->gs()->node($res->results()[0]["udid"]);
        }
        $i->star($page);    
        $response->writeJson([
            "success"=>true
        ])->end();
    }

}