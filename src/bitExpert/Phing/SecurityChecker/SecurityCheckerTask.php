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

use SensioLabs\Security\SecurityChecker;

/**
 * Phing task to interact with the SensioLabs Security Advisories Checker webservice.
 */
class SecurityCheckerTask extends \Task
{
    protected $lockFile;
    protected $timeout;
    protected $endPoint;

    /**
     * Sets the path of the Composer lock file to check.
     *
     * @param string $lockFile
     */
    public function setLockfile($lockFile)
    {
        $this->lockFile = $lockFile;
    }

    /**
     * Sets the timeout for the webservice connection.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Sets a custom endpoint to query for the security checks.
     *
     * @param string $endPoint
     */
    public function setEndPoint($endPoint)
    {
        $this->endPoint = $endPoint;
    }

    /**
     * {@inheritDoc}
     * @throws \BuildException
     */
    public function main()
    {
        if (empty($this->lockFile)) {
            throw new \BuildException('Lockfile needs to be set!');
        }

        if (!file_exists($this->lockFile) || !is_readable($this->lockFile)) {
            throw new \BuildException('Given Lockfile does not exist or is not readable!');
        }

        $checker = $this->getSecurityChecker();
        try {
            if (!empty($this->timeout)) {
                $checker->getCrawler()->setTimeout($this->timeout);
            }
            if (!empty($this->endPoint)) {
                $checker->getCrawler()->setEndPoint($this->endPoint);
            }

            $vulnerabilities = $checker->check($this->lockFile, 'json');
            if ($vulnerabilities->count() === 0) {
                $this->log('No vulnerabilities found!');
                return;
            }

            if ($vulnerabilities->getFormat() !== 'json') {
                throw new \BuildException('Was expecting JSON response, but got "' . $vulnerabilities->getFormat().'"');
            }

            $dependencies = json_decode($vulnerabilities->__toString(), true);
            if (!is_array($dependencies)) {
                throw new \BuildException('The web service response could not be parsed!');
            }

            foreach ($dependencies as $dependency => $issues) {
                $dependencyFullName = $dependency . ' (' . $issues['version'] . ')';
                $this->log($dependencyFullName);
                $this->log(str_repeat('-', strlen($dependencyFullName)));

                foreach ($issues['advisories'] as $issue => $details) {
                    $title = ' * ' . $details['title'];
                    if ($details['cve']) {
                        $title .= ' (CVE: ' . $details['cve'] . ')';
                    }
                    $this->log($title);

                    if ('' !== $details['link']) {
                        $this->log('   ' . $details['link']);
                    }

                    $this->log('');
                }
            }
        } catch (\Exception $e) {
            throw new \BuildException($e);
        }

        throw new \BuildException('Vulnerabilities found!');
    }

    /**
     * Returns the {@link \SensioLabs\SecurityChecker\SecurityChecker} instance.
     *
     * @return SecurityChecker
     */
    public function getSecurityChecker()
    {
        return new SecurityChecker();
    }
}
