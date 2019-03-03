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

use PHPUnit\Framework\TestCase;
use SensioLabs\Security\Crawler;
use SensioLabs\Security\Result;
use SensioLabs\Security\SecurityChecker;

/**
 * Unit test for {@link \bitExpert\Phing\SecurityChecker\SecurityCheckerTask}.
 *
 * @covers \bitExpert\Phing\SecurityChecker\SecurityCheckerTask
 */
class SecurityCheckerTaskUnitTest extends TestCase
{
    /**
     * @var Crawler|TestCase
     */
    private $crawler;
    /**
     * @var SecurityChecker|TestCase
     */
    private $checker;
    /**
     * @var SecurityCheckerTask|TestCase
     */
    private $checkerTask;

    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $this->createMockObjects();
    }

    /**
     * @test
     */
    public function throwsBuildExceptionWhenNoLockFileWasPassed()
    {
        $this->expectException(\BuildException::class);

        $this->checkerTask->main();
    }

    /**
     * @test
     */
    public function throwsBuildExceptionWhenNoLockFileIsNotAccessible()
    {
        $this->expectException(\BuildException::class);

        $this->checkerTask->setLockfile(md5("-"));
        $this->checkerTask->main();
    }

    /**
     * @test
     */
    public function timeoutParameterShouldBePassedToSecurityCheckerWhenGiven()
    {
        $this->crawler->expects($this->once())
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
        $this->crawler->expects($this->once())
            ->method('setEndPoint');

        $this->checkerTask->setLockfile(__FILE__);
        $this->checkerTask->setEndPoint('http://localhost');
        $this->checkerTask->main();
    }

    /**
     * @test
     */
    public function advisoriesIncludingLinkWillCallLogMethodFiveTimesAndThrowBuildException()
    {
        $this->expectException(\BuildException::class);

        $vulnerabilities = [
            'my/dependency' => [
                'version' => '1.0.0',
                'advisories' => [
                    0 => [
                        'title' => 'Advisories title',
                        'cve' => 'CVE-2017-0001',
                        'link' => 'http://localhost'
                    ]
                ]
            ]
        ];
        $this->createMockObjects($vulnerabilities);

        $this->checkerTask->expects($this->exactly(5))
            ->method('log');

        $this->checkerTask->setLockfile(__FILE__);
        $this->checkerTask->setEndPoint('http://localhost');
        $this->checkerTask->main();
    }

    /**
     * @test
     */
    public function advisoriesWithEmptyLinkWillCallLogMethodFourTimesAndThrowBuildException()
    {
        $this->expectException(\BuildException::class);

        $vulnerabilities = [
            'my/dependency' => [
                'version' => '1.0.0',
                'advisories' => [
                    0 => [
                        'title' => 'Some title',
                        'cve' => 'CVE-2017-0001',
                        'link' => ''
                    ]
                ]
            ]
        ];
        $this->createMockObjects($vulnerabilities);

        $this->checkerTask->expects($this->exactly(4))
            ->method('log');

        $this->checkerTask->setLockfile(__FILE__);
        $this->checkerTask->setEndPoint('http://localhost');
        $this->checkerTask->main();
    }

    /**
     * Helper method to create all required mock objects and configure the {@link \SensioLabs\Security\SecurityChecker}
     * instance to return the given $vulnerabilities.
     *
     * @param array $vulnerabilities
     */
    protected function createMockObjects(array $vulnerabilities = [])
    {
        $this->crawler = $this->createMock(Crawler::class);
        $this->checker = $this->createMock(SecurityChecker::class);
        $this->checker->expects($this->any())
            ->method('check')
            ->willReturn(new Result(count($vulnerabilities), json_encode($vulnerabilities), 'json'));
        $this->checker->expects($this->any())
            ->method('getCrawler')
            ->willReturn($this->crawler);

        $this->checkerTask = $this->createPartialMock(
            SecurityCheckerTask::class,
            [
                'getSecurityChecker',
                'log'
            ]
        );
        $this->checkerTask->expects($this->any())
            ->method('getSecurityChecker')
            ->willReturn($this->checker);
        $this->checkerTask->setProject(new \Project());
    }
}
