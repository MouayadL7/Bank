<?php

namespace Tests\Unit\Shared;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Modules\Shared\Exceptions\FileNotFoundException;
use Modules\Shared\Services\FileStorageService;
use Tests\TestCase;

class FileStorageServiceTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_store_stores_file_and_returns_path(): void
    {
        // Arrange
        $file = UploadedFile::fake()->create('test.pdf', 100);
        $path = 'documents';
        $disk = 'public';

        Storage::fake($disk);
        Storage::shouldReceive('disk')
            ->with($disk)
            ->andReturnSelf();
        Storage::shouldReceive('putFileAs')
            ->once()
            ->andReturn('documents/test-uuid.pdf');

        $service = new FileStorageService();

        // Act
        $result = $service->store($file, $path, $disk);

        // Assert
        $this->assertStringContainsString($path, $result);
    }

    public function test_delete_deletes_file_when_exists(): void
    {
        // Arrange
        $path = 'documents/test.pdf';
        $disk = 'public';

        $storageDisk = Mockery::mock();
        $storageDisk->shouldReceive('exists')
            ->once()
            ->with($path)
            ->andReturn(true);
        $storageDisk->shouldReceive('delete')
            ->once()
            ->with($path)
            ->andReturn(true);

        Storage::shouldReceive('disk')
            ->with($disk)
            ->andReturn($storageDisk);

        $service = new FileStorageService();

        // Act
        $result = $service->delete($path, $disk);

        // Assert
        $this->assertTrue($result);
    }

    public function test_delete_returns_false_when_file_not_exists(): void
    {
        // Arrange
        $path = 'documents/nonexistent.pdf';
        $disk = 'public';

        $storageDisk = Mockery::mock();
        $storageDisk->shouldReceive('exists')
            ->once()
            ->with($path)
            ->andReturn(false);

        Storage::shouldReceive('disk')
            ->with($disk)
            ->andReturn($storageDisk);

        $service = new FileStorageService();

        // Act
        $result = $service->delete($path, $disk);

        // Assert
        $this->assertFalse($result);
    }

    public function test_exists_checks_file_existence(): void
    {
        // Arrange
        $path = 'documents/test.pdf';
        $disk = 'public';

        $storageDisk = Mockery::mock();
        $storageDisk->shouldReceive('exists')
            ->once()
            ->with($path)
            ->andReturn(true);

        Storage::shouldReceive('disk')
            ->with($disk)
            ->andReturn($storageDisk);

        $service = new FileStorageService();

        // Act
        $result = $service->exists($path, $disk);

        // Assert
        $this->assertTrue($result);
    }

    public function test_url_returns_file_url(): void
    {
        // Arrange
        $path = 'documents/test.pdf';
        $disk = 'public';
        $expectedUrl = '/storage/documents/test.pdf';

        Storage::shouldReceive('url')
            ->once()
            ->with($path)
            ->andReturn($expectedUrl);

        $service = new FileStorageService();

        // Act
        $result = $service->url($path, $disk);

        // Assert
        $this->assertEquals($expectedUrl, $result);
    }

    public function test_download_returns_download_response(): void
    {
        // Arrange
        $path = 'documents/test.pdf';
        $disk = 'public';
        
        // Create a temporary file for testing
        $tempDir = sys_get_temp_dir();
        $tempFile = $tempDir . DIRECTORY_SEPARATOR . 'test_download_' . uniqid() . '.pdf';
        file_put_contents($tempFile, 'test content');

        $storageDisk = Mockery::mock();
        $storageDisk->shouldReceive('path')
            ->once()
            ->with($path)
            ->andReturn($tempFile);

        Storage::shouldReceive('disk')
            ->once()
            ->with($disk)
            ->andReturn($storageDisk);

        // Mock Response facade
        Response::shouldReceive('download')
            ->once()
            ->with($tempFile)
            ->andReturn(Mockery::mock(\Symfony\Component\HttpFoundation\BinaryFileResponse::class));

        $service = new FileStorageService();

        // Act
        $result = $service->download($path, $disk);

        // Assert
        $this->assertNotNull($result);

        // Cleanup
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function test_download_throws_exception_when_file_not_found(): void
    {
        // Arrange
        $path = 'documents/nonexistent.pdf';
        $disk = 'public';
        $fullPath = storage_path('app/public/documents/nonexistent.pdf');

        Storage::shouldReceive('disk')
            ->with($disk)
            ->andReturnSelf();
        Storage::shouldReceive('path')
            ->once()
            ->with($path)
            ->andReturn($fullPath);

        $service = new FileStorageService();

        // Assert - expectException will catch the exception and mark test as passing
        $this->expectException(FileNotFoundException::class);

        // Act - exception will be thrown and caught by expectException
        $service->download($path, $disk);
    }
}

