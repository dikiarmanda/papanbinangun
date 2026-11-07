<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ExampleSessionTest extends CIUnitTestCase
{
    public function testSessionSimple(): void
    {
        $session = service('session');

        $session->set('logged_in', 123);
        $this->assertSame(123, $session->get('logged_in'));
    }

    public function testSuperadminFilterBlocksNonSuperadmin(): void
    {
        $session = service('session');
        $session->set('admin_role', 'admin');

        $filter = new \App\Filters\SuperadminFilter();
        $request = service('request');
        $result = $filter->before($request);

        $this->assertInstanceOf(\CodeIgniter\HTTP\RedirectResponse::class, $result);
    }

    public function testSuperadminFilterAllowsSuperadmin(): void
    {
        $session = service('session');
        $session->set('admin_role', 'superadmin');

        $filter = new \App\Filters\SuperadminFilter();
        $request = service('request');
        $result = $filter->before($request);

        $this->assertNull($result);
    }
}
