<?php

$app->get("/", "HomeController:index")->setName("index");
$app->group("/certificate", function (){
   $this->get("", "CertificateController:index")->setName("certificate.index");
   $this->get("/upload", "CertificateController:upload")->setName("certificate.upload");
   $this->post("/upload", "CertificateController:process")->setName("certificate.upload");
   $this->get("/add", "CertificateController:add")->setName("certificate.add");
   $this->post("/add", "CertificateController:store")->setName("certificate.add");
   $this->get("/list", "CertificateController:list")->setName("certificate.list");
   $this->get("/show/{id}", "CertificateController:show")->setName("certificate.show");
   $this->post("/update/{id}", "CertificateController:update")->setName("certificate.update");
   $this->get("/delete/{id}", "CertificateController:delete")->setName("certificate.delete");
});
$app->group("/association", function (){
    $this->get("", "AssociationController:index")->setName("association.index");
    $this->get("/add", "AssociationController:add")->setName("association.add");
    $this->post("/add", "AssociationController:store")->setName("association.add");
    $this->get("/list", "AssociationController:list")->setName("association.list");
    $this->get("/show/{id}", "AssociationController:show")->setName("association.show");
    $this->post("/update/{id}", "AssociationController:update")->setName("association.update");
    $this->get("/delete/{id}", "AssociationController:delete")->setName("association.delete");
    $this->get("/upload", "AssociationController:upload")->setName("association.upload");
    $this->post("/upload", "AssociationController:proccess")->setName("association.upload");
});