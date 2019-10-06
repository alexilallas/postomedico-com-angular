<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    private $table = 'pacientes';
    private $contato;

    public function __construct()
    {
        $this->contato = new ContatoController();
    }


    public function customSave($modelData)
    {
        unset($modelData['nome_contato']);
        unset($modelData['numero_contato']);
        unset($modelData['tipo_paciente']);
        $data = $modelData;

        $this->save($this->table, $data);
        $modelData['paciente_id'] = DB::getPdo()->lastInsertId();
        $this->contato->customSave($modelData);
    }


    public function checkBusinessLogic($data)
    {
        $result = DB::table($this->table)->where('cpf_rg', $data['cpf_rg'])->count();
        if ($result > 0) {
            $this->cancel('Já existem um paciente cadastrado com o CPF/RG: '.$data['cpf_rg']);
        }
    }

    public function find()
    {
        $pacientes = DB::table($this->table)->get();

        return $this->jsonSuccess('Pacientes cadastrados', compact('pacientes'));
    }

    public function findById(Request $req)
    {
        $id = $req->route('id');
        $paciente = DB::table($this->table)
        ->join('contatos', 'contatos.paciente_id', '=', 'pacientes.id')
        ->where($this->table.'.id', $id)
        ->select('pacientes.*', 'contatos.nome as nome_contato', 'contatos.numero as numero_contato', 'contatos.id as id_contato')
        ->get();

        return $this->jsonSuccess('Pacientes cadastrados', compact('paciente'));
    }

    public function postPaciente()
    {
        $data = $this->jsonDecode();

        try {
            \DB::beginTransaction();
            $this->doSave($data, 'Cadastrou o Paciente '.$data['nome']);
            \DB::commit();
            return $this->jsonSuccess('Paciente adicionado com sucesso!');
        } catch (\Throwable $th) {
            \DB::rollback();
            return $this->jsonError($th->getMessage());
        }
    }

    public function updatePaciente()
    {
        $data = $this->jsonDecode();

        try {
            \DB::beginTransaction();
            $this->doUpdate($data, 'Editou dados pessoais do paciente '. $data['nome']);
            \DB::commit();
            return $this->jsonSuccess('Paciente atualizado com sucesso!', $data);
        } catch (\Throwable $th) {
            \DB::rollback();
            return $this->jsonError($th->getMessage());
        }
    }

    public function customUpdate($modelData)
    {
        $data = $modelData;
        unset($data['nome_contato']);
        unset($data['numero_contato']);
        unset($data['tipo_paciente']);
        unset($data['id_contato']);

        $this->update($this->table, $data);
        $this->contato->customUpdate($modelData);
    }
}
