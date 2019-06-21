<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Questionnaire controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, MeDa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class PDF extends FPDF {
    protected $title;

    function __construct() {
        parent::__construct();
    }

    function Header() {
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetY(0, false);
        $this->SetFont('', 'B');
        $this->Cell(0, 10, $this->title);
        $this->SetFont('', '');
        $this->SetXY($x, $y);
    }
    function Footer() {
        $x = $this->GetX();
        $y = $this->GetY();
        $footerY = $this->GetPageHeight() -10;
        $this->SetXY(170, $footerY);
        $this->Cell(0, 10, ('Page '.$this->PageNo().' sur {nb}'));
        $this->SetXY($x, $y);
    }
    function SetTitle($title, $isUTF8=false) {
        parent::SetTitle($title, $isUTF8);
        $this->title = $title;
    }
}