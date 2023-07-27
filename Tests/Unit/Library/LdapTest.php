<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\IgLdapSsoAuth\Tests\Unit\Library;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Causal\IgLdapSsoAuth\Library\Ldap;
/**
 * Test cases for class \Causal\IgLdapSsoAuth\Library\Ldap.
 */
class LdapTest extends UnitTestCase
{

    /**
     * @test
     * @dataProvider escapeProvider
     */
    public function parentheseIsEscapedOnce($input, $expected) {
        $ldap = new Ldap();
        $actual = $ldap->escapeDnForFilter($input);
        $this->assertEquals($expected, $actual);
    }

    public function escapeProvider()
    {
        return [
            ['', ''],
            [null, ''],
            ['CN=Lastname\\, Firstname,DC=company,DC=tld', 'CN=Lastname\\\\, Firstname,DC=company,DC=tld'],
            ['CN=John Doo (Jr),DC=company,DC=tld', 'CN=John Doo \\(Jr\\),DC=company,DC=tld'],
        ];
    }

}
