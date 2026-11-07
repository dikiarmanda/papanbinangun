<?php

use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use Tests\Support\Libraries\ConfigReader;

/**
 * @internal
 */
final class HealthTest extends CIUnitTestCase
{
    public function testIsDefinedAppPath(): void
    {
        $this->assertTrue(defined('APPPATH'));
    }

    public function testBaseUrlHasBeenSet(): void
    {
        $validation = service('validation');

        $env = false;

        // Check the baseURL in .env
        if (is_file(HOMEPATH . '.env')) {
            $env = preg_grep('/^app\.baseURL = ./', file(HOMEPATH . '.env')) !== false;
        }

        if ($env) {
            // BaseURL in .env is a valid URL?
            // phpunit.dist.xml sets app.baseURL in $_SERVER
            // So if you set app.baseURL in .env, it takes precedence
            $config = new App();
            $this->assertTrue(
                $validation->check($config->baseURL, 'valid_url'),
                'baseURL "' . $config->baseURL . '" in .env is not valid URL',
            );
        }

        // Get the baseURL in app/Config/App.php
        // You can't use Config\App, because phpunit.dist.xml sets app.baseURL
        $reader = new ConfigReader();

        // BaseURL in app/Config/App.php is a valid URL?
        $this->assertTrue(
            $validation->check($reader->baseURL, 'valid_url'),
            'baseURL "' . $reader->baseURL . '" in app/Config/App.php is not valid URL',
        );
    }

    public function testSanitizeGoogleMapsEmbed(): void
    {
        helper('site');

        $validIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12" width="600" height="450"></iframe>';
        $sanitized = sanitize_google_maps_embed($validIframe);
        $this->assertNotNull($sanitized);
        $this->assertStringContainsString('https://www.google.com/maps/embed?pb=!1m18!1m12', $sanitized);

        // Valid raw URL
        $validUrl = 'https://maps.google.com/maps?q=Binangun&output=embed';
        $sanitizedUrl = sanitize_google_maps_embed($validUrl);
        $this->assertNotNull($sanitizedUrl);

        // Malicious script
        $this->assertNull(sanitize_google_maps_embed('<script>alert("xss")</script>'));

        // Malicious javascript: scheme
        $this->assertNull(sanitize_google_maps_embed('<iframe src="javascript:alert(1)"></iframe>'));

        // Malicious domain
        $this->assertNull(sanitize_google_maps_embed('<iframe src="https://evil.com/maps"></iframe>'));

        // Subdomain trick
        $this->assertNull(sanitize_google_maps_embed('<iframe src="https://google.com.evil.com/maps"></iframe>'));

        // Empty string
        $this->assertSame('', sanitize_google_maps_embed(''));
        $this->assertNull(sanitize_google_maps_embed(null));
    }

    public function testPengaturanHelperReturnsDataAndCaches(): void
    {
        helper('site');

        $data = pengaturan();
        $this->assertIsArray($data);

        $desa = pengaturan('nama_desa');
        $this->assertNotNull($desa);
        $this->assertSame($data['nama_desa'] ?? null, $desa);
    }

    public function testHasUniqueSlugTrait(): void
    {
        $mockModel = $this->getMockBuilder(\CodeIgniter\Model::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findColumn'])
            ->addMethods(['groupStart', 'where', 'orLike', 'groupEnd'])
            ->getMock();

        $mockModel->method('groupStart')->willReturnSelf();
        $mockModel->method('where')->willReturnSelf();
        $mockModel->method('orLike')->willReturnSelf();
        $mockModel->method('groupEnd')->willReturnSelf();
        $mockModel->method('findColumn')->willReturn(['wisata-alam', 'wisata-alam-1']);

        $dummy = new class {
            use \App\Traits\HasUniqueSlug;

            public function getSlug(string $slug, ?int $excludeId = null, ?\CodeIgniter\Model $model = null): string
            {
                return $this->uniqueSlug($slug, $excludeId, $model);
            }
        };

        $slug = $dummy->getSlug('wisata-alam', null, $mockModel);
        $this->assertSame('wisata-alam-2', $slug);
    }
}
