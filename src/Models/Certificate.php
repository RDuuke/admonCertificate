<?php

namespace Certificate\Models;


class Certificate extends Model
{
    protected $table = "generador_certificado";
    protected $fillable = ["tipo", "template", "codigo", "titulo", "contenido", "duracion", "fecha_finalizacion"];

}