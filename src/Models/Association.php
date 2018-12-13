<?php

namespace Certificate\Models;


class Association extends Model
{
    protected $table = "certificado_usuario";

    protected $fillable = ["certificado_id", "usuario_id", "estado", "reporte", "download_at"];

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, "certificado_id", "id");
    }
}