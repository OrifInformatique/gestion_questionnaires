<?php
require_once(__DIR__.'/../../libraries/TableTopics.php');

/**
 * Class for tests for TableTopics class
 * 
 * @author      Orif, section informatique (ViDi, MeSa, BuYa)
 * @link        https://github.com/OrifInformatique/gestion_questionnaires
 * @copyright   Copyright (c) Orif (http://www.orif.ch)
 */
class TableTopics_Test extends TestCase {
    /**
     * Instance of TableTopics for tests
     *
     * @var TableTopics
     */
    private $instance;

    /*******************
     * START/END METHODS
     *******************/
    public function setUp()
    {
        $this->instance = new TableTopics();
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
     * Test for `TableTopics::[set|get]ArrayNbQuestion`
     * 
     * @dataProvider provider_sganq
     *
     * @param integer $expected_amount = Amount expected to be in the returned array
     * @param callable $callback = Method to call with `$this->instance` as the only parameter
     * @return void
     */
    public function test_sganq(int $amount_expected, callable $callback)
    {
        $instance =& $this->instance;

        $callback($instance);
        $this->assertCount($amount_expected, $instance->getArrayNbQuestion());
    }
    /**
     * Test for `TableTopics::[remove|get]ArrayNbQuestion`
     * 
     * @dataProvider provider_rganq
     *
     * @param callable $setup = Function to call on the instance before the test
     * @param integer $amount_remove = Amount to remove
     * @param integer $amount_expected = Amount left expected
     * @return void
     */
    public function test_rganq(callable $setup, int $amount_remove, int $amount_expected)
    {
        $instance =& $this->instance;
        $setup($instance);

        for($i = 0; $i < $amount_remove; $i++) {
            $instance->removeArrayNbQuestion(0);
        }

        $this->assertCount($amount_expected, $instance->getArrayNbQuestion());
    }
    /**
     * Dynamic test for `TableTopics::[get|set|remove]ArrayNbQuestion`
     *
     * @return void
     */
    public function test_gsranq_rand_amount()
    {
        $instance =& $this->instance;
        $amount_remove = rand(0, 10);
        $amount_base = rand(10, 20);
        for($i = 0; $i < $amount_base; $i++) {
            $instance->addArrayNbQuestion($i);
        }

        $this->assertCount($amount_base, $instance->getArrayNbQuestion());

        for($i = 0; $i < $amount_remove; $i++) {
            $instance->removeArrayNbQuestion(0);
        }

        $this->assertCount($amount_base - $amount_remove, $instance->getArrayNbQuestion());
    }
    /**
     * Test for `TableTopics::[set|get]ArrayTopics`
     * 
     * @dataProvider provider_sgat
     *
     * @param integer $expected_amount = Amount expected to be in the returned array
     * @param callable $callback = Method to call with `$this->instance` as the only parameter
     * @return void
     */
    public function test_sgat(int $amount_expected, callable $callback)
    {
        $instance =& $this->instance;

        $callback($instance);
        $this->assertCount($amount_expected, $instance->getArrayTopics());
    }
    /**
     * Test for `TableTopics::[remove|get]ArrayTopics`
     * 
     * @dataProvider provider_rgat
     *
     * @param callable $setup = Function to call on the instance before the test
     * @param integer $amount_remove = Amount to remove
     * @param integer $amount_expected = Amount left expected
     * @return void
     */
    public function test_rgat(callable $setup, int $amount_remove, int $amount_expected)
    {
        $instance =& $this->instance;
        $setup($instance);

        for($i = 0; $i < $amount_remove; $i++) {
            $instance->removeArrayTopics(0);
        }

        $this->assertCount($amount_expected, $instance->getArrayTopics());
    }
    /**
     * Dynamic test for `TableTopics::[get|set|remove]ArrayTopics`
     *
     * @return void
     */
    public function test_gsrat_rand_amount()
    {
        $instance =& $this->instance;
        $amount_remove = rand(0, 10);
        $amount_base = rand(10, 20);
        for($i = 0; $i < $amount_base; $i++) {
            $instance->addArrayTopics($i);
        }

        $this->assertCount($amount_base, $instance->getArrayTopics());

        for($i = 0; $i < $amount_remove; $i++) {
            $instance->removeArrayTopics(0);
        }

        $this->assertCount($amount_base - $amount_remove, $instance->getArrayTopics());
    }
    /**
     * Test for `TableTopics::[get|set]Title`
     *
     * @return void
     */
    public function test_gstitle()
    {
        $instance =& $this->instance;
        $title = 'dummy_title';
        $instance->setTitle($title);
        $this->assertSame($title, $instance->getTitle());
    }
    /**
     * Test for `TableTopics::[get|set]Subtitle`
     *
     * @return void
     */
    public function test_gssubtitle()
    {
        $instance =& $this->instance;
        $subtitle = 'dummy_subtitle';
        $instance->setSubtitle($subtitle);
        $this->assertSame($subtitle, $instance->getSubtitle());
    }
    /**
     * Test for `TableTopics::[get|set]ModelName`
     *
     * @return void
     */
    public function test_gsmodelname()
    {
        $instance =& $this->instance;
        $modelname = 'dummy_modelname';
        $instance->setModelName($modelname);
        $this->assertSame($modelname, $instance->getModelName());
    }
    /**
     * Test for `TableTopics::[get|set]PDFName`
     *
     * @return void
     */
    public function test_gspdfname()
    {
        $instance =& $this->instance;
        $pdfname = 'dummy_pdfname';
        $instance->setPDFName($pdfname);
        $this->assertSame($pdfname, $instance->getPDFName());
    }

    /***********
     * PROVIDERS
     ***********/
    /**
     * Provider for `test_ganq`
     *
     * @return array
     */
    public function provider_sganq() : array
    {
        $data = [];

        $data['none'] = [
            0,
            function() { }
        ];

        $data['single'] = [
            1,
            function(TableTopics &$tableTopics) {
                $tableTopics->addArrayNbQuestion(5);
            }
        ];

        return $data;
    }
    /**
     * Provider for `test_ranq`
     *
     * @return array
     */
    public function provider_rganq() : array
    {
        $data = [];

        $data['none'] = [
            function(TableTopics &$tableTopics) {
                $tableTopics->addArrayNbQuestion(5);
            },
            1,
            0
        ];

        $data['single'] = [
            function(TableTopics &$tableTopics) {
                $tableTopics->addArrayNbQuestion(5);
                $tableTopics->addArrayNbQuestion(5);
            },
            1,
            1
        ];

        return $data;
    }
    /**
     * Provider for `test_sgat`
     *
     * @return array
     */
    public function provider_sgat() : array
    {
        $data = [];

        $data['none'] = [
            0,
            function() { }
        ];

        $data['single'] = [
            1,
            function(TableTopics &$tableTopics) {
                $tableTopics->addArrayTopics(5);
            }
        ];

        return $data;
    }
    /**
     * Provider for `test_rgat`
     *
     * @return array
     */
    public function provider_rgat() : array
    {
        $data = [];

        $data['none'] = [
            function(TableTopics &$tableTopics) {
                $tableTopics->addArrayTopics(5);
            },
            1,
            0
        ];

        $data['single'] = [
            function(TableTopics &$tableTopics) {
                $tableTopics->addArrayTopics(5);
                $tableTopics->addArrayTopics(5);
            },
            1,
            1
        ];

        return $data;
    }
}