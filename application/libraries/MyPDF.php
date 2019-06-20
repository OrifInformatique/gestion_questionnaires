<?php

require('fpdf181/Fpdf.php');

class MyPDF extends FPDF
{
    private $headerText = "";
    private $footerText = array("", "");

    function Header(){
        if ($this->page != 1){
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, $this->headerText);
        }
    }

    function Footer(){
        $this->SetY(-15);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, $this->footerText[0].$this->PageNo().$this->footerText[1].'{nb}', 0, 1, 'R');
    }

    function setHeaderText($headerText){
        $this->headerText = $headerText;
    }

    function setFooterText($footerText, $footerText1){
        $this->footerText[0] = $footerText;
        $this->footerText[1] = $footerText1;
    }

}