<?php

/*
 * This file is part of the Phing SecurityChecker package.
 *
 * (c) bitExpert AG
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace bitExpert\Phing\SecurityChecker;

/**
 * Unit test for {@link \bitExpert\Phing\SecurityChecker\SecurityChecker}.
 *
 * @covers \bitExpert\Phing\SecurityChecker\SecurityChecker
 */
class SecurityCheckerTaskUnitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SensioLabs\Security\SecurityChecker
     */
    private $checker;
    /**
     * @var \bitExpert\Phing\SecurityChecker\SecurityCheckerTask
     */
    private $checkerTask;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $this->checker = $this->getMock('\SensioLabs\Security\SecurityChecker');
        $this->checker->expects($this->any())
            ->method('check')
            ->will($this->returnValue([]));

        $this->checkerTask = $this->getMock('\bitExpert\Phing\SecurityChecker\SecurityCheckerTask', array('getSecurityChecker'));
        $this->checkerTask->expects($this->any())
            ->method('getSecurityChecker')
            ->will($this->returnValue($this->checker));
        $this->checkerTask->setProject(new \Project());
    }

    /**
     * @test
     * @expectedException \BuildException
     */
    public function throwsBuildExceptionWhenNoLockFileWasPassed()
    {
        $this->checkerTask->main();
    }

    /**
     * @test
     * @expectedException \BuildException
     */
    public function throwsBuildExceptionWhenNoLockFileIsNotAccessible()
    {
        $this->checkerTask->setLockfile(md5("-"));
        $this->checkerTask->main();
    }

    /**
     * @test
     */
    public function timeoutParameterShouldBePassedToSecurityCheckerWhenGiven()
    {
        $this->checker->expects($this->once())
            ->method('setTimeout');

        $this->checkerTask->setLockfile(__FILE__);
        $this->checkerTask->setTimeout(100);
        $this->checkerTask->main();
    }

    /**
     * @test
     */
    public function endPointParameterShouldBePassedToSecurityCheckerWhenGiven()
    {
        $this->checker->expects($this->once())
            ->method('setEndPoint');

        $this->checkerTask->setLockfile(__FILE__);
        $this->checkerTask->setEndPoint('http://localhost');
        $this->checkerTask->main();
    }

    /**
     * @test
     * @expectedException \BuildException
     */
    public function throwsBuildExceptionWhenVulnerabilitiesFound()
    {
        $this->checker->expects($this->any())
            ->method('getLastVulnerabilityCount')
            ->will($this->returnValue(1));

        $this->checkerTask->setLockfile(__FILE__);
        $this->checkerTask->main();
    }
}
