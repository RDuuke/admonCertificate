<?php

namespace Certificate\Controllers;



use Certificate\Models\Certificate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Certificate\Utils;

class CertificateController extends Controller
{
    protected $creatos = [];
    protected $errors = [];
    protected $total = [];

    function index(Request $request, Response $response)
    {
        return $this->view->render($response, "certificate/index.twig");
    }

    function list(Request $request, Response $response)
    {
        $certificates = Certificate::all();
        return $this->view->render($response, "certificate/list.twig", ["certificates" => $certificates, "module_name" => "cert-list"]);
    }

    function upload(Request $request, Response $response)
    {
        return $this->view->render($response, "certificate/upload.twig", ["module_name" => "cert-upload"]);
    }

    function add(Request $request, Response $response)
    {
        return $this->view->render($response, "certificate/add.twig", ["module_name" => "cert-show"]);
    }

    function show(Request $request, Response $response, $args)
    {
        $certificate = Certificate::find($args['id']);

        return $this->view->render($response, "certificate/add.twig", ["certificate" => $certificate, "module_name" => "cert-show"]);
    }

    function store(Request $request, Response $response)
    {
        $certificate = $request->getParams();
        $certificate['codigo'] = $request->getParam('template') . "-" .   str_replace(".", "", substr(\crypt(date('d-m-Y')), 3, 8));
        Certificate::create($certificate);
        return $response->withRedirect($this->router->pathFor('certificate.list'));
    }

    function update(Request $request, Response $response, $args)
    {
        $certificate = Certificate::find($args['id']);
        $certificate->tipo = $request->getParam('tipo');
        $certificate->template = $request->getParam('template');
        $certificate->titulo = $request->getParam('titulo');
        $certificate->contenido = $request->getParam('contenido');
        $certificate->duracion = $request->getParam('duracion');
        $certificate->fecha_finalizacion = $request->getParam('fecha_finalizacion');
        $certificate->save();
        return $response->withRedirect($this->router->pathFor('certificate.list'));
    }

    function delete(Request $request, Response $response, $args)
    {
        Certificate::destroy($args['id']);
        return $response->withRedirect($this->router->pathFor('certificate.list'));
    }

    function process(Request $request, Response $response)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile  = $uploadedFiles['archive'];
        $filename = moveUploadedFile($uploadedFile);
        if ($uploadedFile ->getError() == UPLOAD_ERR_OK) {
            try {
                if ($filename) {
                    $reader = IOFactory::createReaderForFile(BASE_TEMP . DS . $filename);
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load(BASE_TEMP . DS . $filename);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $array_worksheet = getHighestDataRow($worksheet);
                    foreach ($array_worksheet as $key => $value) {
                        $data = [
                            "tipo" => $value[0],
                            "codigo" => $value[1] . "-" .   str_replace(".", "", substr(\crypt(date('d-m-Y')), 3, 8)),
                            "template" => $value[1],
                            "titulo" => $value[2],
                            "contenido" => $value[3],
                            "duracion" => $value[4],
                            "fecha_finalizacion" => $value[5]
                        ];
                        try {

                            if (Certificate::create($data) instanceof Certificate) {
                                array_push($this->creatos, ["titulo" => $data['titulo'], "estado" => "C01", "mensaje" => "creado correcto."]);
                            } else {
                                array_push($this->errors, ["titulo" => $data['titulo'], "estado" => "E01", "mensaje" => "Error al crear."]);
                            }
                            unset($data);
                        } catch (\Exception $e) {
                            unlink(BASE_TEMP . DS . $filename);
                            $this->total = array_merge($this->creatos, $this->errors);
                            return $this->view->render($response, "certificate/upload.twig", ["results" => $this->total, "error" => $e->getMessage()]);
                        }
                    }
                    unlink(BASE_TEMP . DS . $filename);
                    $this->total = array_merge($this->creatos, $this->errors);
                    return $this->view->render($response, "certificate/upload.twig", ["results" => $this->total]);
                }
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
        }
    }
}