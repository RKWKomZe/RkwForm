<?php
namespace RKW\RkwForm\Tests\Unit\Controller;

/**
 * Test case.
 *
 * @author Maximilian Fäßler <maximilian@faesslerweb.de>
 */
class StandardControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \RKW\RkwForm\Controller\StandardController
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = $this->getMockBuilder(\RKW\RkwForm\Controller\StandardController::class)
            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function showActionAssignsTheGivenStandardToView()
    {
        $standard = new \RKW\RkwForm\Domain\Model\Standard();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('standard', $standard);

        $this->subject->showAction($standard);
    }

    /**
     * @test
     */
    public function createActionAddsTheGivenStandardToStandardRepository()
    {
        $standard = new \RKW\RkwForm\Domain\Model\Standard();

        $standardRepository = $this->getMockBuilder(\RKW\RkwForm\Domain\Repository\StandardRepository::class)
            ->setMethods(['add'])
            ->disableOriginalConstructor()
            ->getMock();

        $standardRepository->expects(self::once())->method('add')->with($standard);
        $this->inject($this->subject, 'standardRepository', $standardRepository);

        $this->subject->createAction($standard);
    }
}
