<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Questionnaire controller
 *
 * @author      Orif, section informatique (UlSi, ViDi, MeDa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class PDF extends FPDF {
    protected $title;   // Document's title
    protected $footerPage, $footerOutOf;    // Parts of the footer

    /**
     * Creates a new PDF
     *
     * @param string $page = The part that says the current page
     * @param string $out_of = The part that says out of the total pages
     */
    function __construct($page = '', $out_of = '/') {
        parent::__construct();
        $this->footerPage = $page;
        $this->footerOutOf = $out_of;
    }

    /**
     * Adds the title of the document to the top left
     */
    function Header() {
        if(empty($this->title) || $this->PageNo() == 1) return;
        $oldColor = $this->TextColor;
        $this->SetTextColor(PDF_HEADER_COLOR['red'],
                            PDF_HEADER_COLOR['green'],
                            PDF_HEADER_COLOR['blue']);
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetY(10, false);
        $this->SetFont('', 'B');
        $this->Cell(0, 10, $this->title);
        $this->SetFont('', '');
        $this->SetXY($x, $y);
        $this->TextColor = $oldColor;
    }

    /**
     * Adds the amount of pages to the bottom right of the document
     * Spaces are not added to the text
     */
    function Footer() {
        $oldColor = $this->TextColor;
        $this->SetTextColor(PDF_FOOTER_COLOR['red'],
                            PDF_FOOTER_COLOR['green'],
                            PDF_FOOTER_COLOR['blue']);
        $x = $this->GetX();
        $y = $this->GetY();
        $footerY = $this->GetPageHeight() -20;
        $this->SetXY(170, $footerY);
        $this->Cell(0, 10, ($this->footerPage.$this->PageNo().$this->footerOutOf.$this->AliasNbPages));
        $this->SetXY($x, $y);
        $this->TextColor = $oldColor;
    }

    /**
     * Sets the title of the document
     *
     * @param string $title = The new title
     * @param boolean $isUTF8 = Whether the title is in UTF8
     */
    function SetTitle($title, $isUTF8 = false) {
        parent::SetTitle($title, $isUTF8);
        $this->title = ($isUTF8 ? utf8_encode($title) : $title);
    }

    /**
     * Updates the page's footer text
     *
     * @param string $page = The part that says the current page
     * @param string $out_of = The part that says out of the total pages
     */
    function SetFooterStrings($page = '', $out_of = '/') {
        $this->footerPage = ($page ?: $this->footerPage);
        $this->footerOutOf = ($out_of ?: $this->footerOutOf);
    }
}
