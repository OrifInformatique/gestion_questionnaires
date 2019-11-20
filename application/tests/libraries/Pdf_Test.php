<?php
require_once(__DIR__.'/../../libraries/fpdf181/Fpdf.php');
require_once(__DIR__.'/../../libraries/Pdf.php');

/**
 * Class for tests for PDF class
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class PDF_Test extends TestCase {
    /**
     * Instance of PDF for tests
     *
     * @var PDF
     */
    private $instance;

    /*******************
     * START/END METHODS
     *******************/
    public function setUp()
    {
        $this->instance = new PDF();
    }
    public function tearDown()
    {
        parent::tearDown();

        unset($this->instance);
    }

    /*******
     * TESTS
     *******/
    /**
     * Test for `PDF::setTitle`
     *
     * @return void
     */
    public function test_title()
    {
        $instance =& $this->instance;
        $expected = 'dummy_instance';

        $instance->setTitle($expected);

        $getTitle = function() {
            return $this->title;
        };
        $getTitle = $getTitle->bindTo($instance, $instance);
        $this->assertNotFalse($getTitle);

        $actual = $getTitle();

        $this->assertSame($expected, $actual);
    }
    /**
     * Test for `PDF::SetFooterStrings`
     *
     * @return void
     */
    public function test_footers()
    {
        $instance =& $this->instance;
        $expected_page = 'dummy_page';
        $expected_out_of = 'dummy_out_of';

        $instance->SetFooterStrings($expected_page, $expected_out_of);

        $getPage = function() {
            return $this->footerPage;
        };
        $getPage = $getPage->bindTo($instance, $instance);
        $this->assertNotFalse($getPage);
        $actual_page = $getPage();

        $getOutOf = function() {
            return $this->footerOutOf;
        };
        $getOutOf = $getOutOf->bindTo($instance, $instance);
        $this->assertNotFalse($getOutOf);
        $actual_out_of = $getOutOf();

        $this->assertSame($expected_page, $actual_page);
        $this->assertSame($expected_out_of, $actual_out_of);
    }
    /**
     * Test for `PDF::Header` and `PDF::Footer`
     *
     * @return void
     */
    public function test_hf()
    {
        $instance =& $this->instance;

        $exception = NULL;
        try {
            $instance->setFont('arial');
            $instance->Header();
            $instance->Footer();
        } catch (Exception $exception) { }

        $this->assertNull($exception);
    }
    /**
     * Test for `PDF::Header` with a title
     *
     * @return void
     */
    public function test_header_title()
    {
        $instance =& $this->instance;

        $exception = NULL;
        try {
            $instance->SetTitle('dummy_instance');
            $instance->setFont('arial');
            $instance->AddPage();
            $instance->AddPage();
            $instance->Header();
        } catch (Exception $exception) { }

        $this->assertNull($exception);
    }
}