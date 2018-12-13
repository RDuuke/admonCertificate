<?php

namespace Certificate\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends Controller
{
    function index(Request $request,  Response $response){
        return $this->view->render($response, "index.twig");
    }
}