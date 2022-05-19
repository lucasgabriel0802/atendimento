<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Usuario extends Entity
{
    protected $_accessible = [
        'UNIDADE' => true,
        'EMPRESA' => true,
        'CODIGO' => true,
        'USUARIOWEB' => true,
        'SENHAWEB' => true,
        'TIPO_CADASTRO' => true,
        'TIPO_USUARIO' => true,
        'EMAIL' => true,
        'ACESSO_FATURAS' => true,
        'ACESSO_DOCUMENTOS' => true,
        'ACESSO_ESOCIAL' => true,
        'ACESSO_INTERNO' => true
    ];

}
