<?php
namespace Consultation\Model;

class NotesExamensMorphologiques{
	public $code;
	public $id_cons;
	public $id_examen;
	public $note;

	public function exchangeArray($data) {
		$this->code = (! empty ( $data ['code'] )) ? $data ['code'] : null;
		$this->id_cons = (! empty ( $data ['id_cons'] )) ? $data ['id_cons'] : null;
		$this->id_examen = (! empty ( $data ['id_examen'] )) ? $data ['id_examen'] : null;
		$this->note = (! empty ( $data ['note'] )) ? $data ['note'] : null;
	}
}