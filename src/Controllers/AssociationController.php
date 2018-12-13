<?php

namespace Certificate\Controllers;


use Certificate\Models\Association;
use Certificate\Models\Certificate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Slim\Http\Request;
use Slim\Http\Response;

class AssociationController extends Controller
{
    protected $creatos = [];
    protected $errors =[];
    protected $total =[];

    function index (Request $request, Response $response)
    {
        return $this->view->render($response, "association/index.twig");
    }

    function show (Request $request, Response $response, $args)
    {
        $association = Association::find($args['id']);
        $certificates = Certificate::all(['id', 'titulo']);
        return $this->view->render($response, "association/add.twig", ["certificates" => $certificates, 'association'=> $association, "module-name" => "aso-show"]);

    }
    function list(Request $request, Response $response)
    {
        $association = Association::all();
        return $this->view->render($response, "association/list.twig", ["association" => $association, "module_name" => "aso-list"]);
    }

    function add(Request $request, Response $response)
    {
        $certificates = Certificate::all(['id', 'titulo']);
        return $this->view->render($response, "association/add.twig", ["certificates" => $certificates, "module_name" => "aso-show"]);
    }

    function store (Request $request, Response $response)
    {
        Association::create($request->getParams());
        return $response->withRedirect($this->router->pathFor('association.list'));
    }

    function update (Request $request, Response $response, $args)
    {
        $association = Association::find($args['id']);
        $association->certificado_id = $request->getParam('certificado_id');
        $association->usuario_id = $request->getParam('usuario_id');
        $association->save();
        return $response->withRedirect($this->router->pathFor('association.list'));
    }

    function upload (Request $request, Response $response)
    {
        $certificates = Certificate::all(['id', 'titulo']);
        return $this->view->render($response, "association/upload.twig", ["certificates" => $certificates, "module_name" => "aso-upload"]);
    }

    function proccess (Request $request, Response $response)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile  = $uploadedFiles['archive'];
        $filename = moveUploadedFile($uploadedFile);
        if ($uploadedFile->getError() == UPLOAD_ERR_OK) {
            try {
                if ($filename) {
                    $reader = IOFactory::createReaderForFile(BASE_TEMP . DS . $filename);
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load(BASE_TEMP . DS . $filename);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $array_worksheet = getHighestDataRow($worksheet);
                    foreach ($array_worksheet as $key => $value) {
                        $data = [
                            "usuario_id" => $value[0],
                            "certificado_id" => $request->getParam('certificate'),
                        ];
                        try {
                            $asssociation = Association::create($data);

                            if ($asssociation instanceof Association) {

                                array_push($this->creatos, ["usuario" => $asssociation->usuario_id, "titulo" => $asssociation->certificate->titulo
                                    , "estado" => "C01", "mensaje" => "creado correcto."]);
                            } else {
                                array_push($this->errors, ["usuario" => $data["usuario_id"], "titulo" => Certificate::find($request->getParam('certificate'))->titulo, "estado" => "E01", "mensaje" => "Error al crear."]);
                            }
                            unset($data);
                        } catch (\Exception $e) {
                            unlink(BASE_TEMP . DS . $filename);
                            $this->total = array_merge($this->creatos, $this->errors);
                            return $this->view->render($response, "association/upload.twig", ["results" => $this->total, "error" => $e->getMessage()]);
                        }
                    }
                    unlink(BASE_TEMP . DS . $filename);
                    $this->total = array_merge($this->creatos, $this->errors);
                    return $this->view->render($response, "association/upload.twig", ["results" => $this->total]);
                }
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }
    }

    function delete(Request $request, Response $response, $args)
    {
        Association::destroy($args['id']);
        return $response->withRedirect($this->router->pathFor('association.list'));
    }
}