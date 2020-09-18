<?php

namespace Tests\Feature\DownloadController;

use App\Models\Job;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;
use App\Jobs\GenerateYmlJob as YmlJob;

class GetFileTest extends TestCase
{
    use RefreshDatabase;

    protected string $route;

    /**
     * @param array $parameters
     * @return string
     */
    protected function getRoute(array $parameters = [])
    {
        return route('api.download.getFile', $parameters);
    }

    /**
     * @test
     * @return void
     */
    public function check_if_not_found_job_access_returns_404_error(): void
    {
        $this->getJson($this->getRoute(['non_existed_filename']))
            ->assertStatus(404);
    }

    /**
     * @test
     * @return void
     */
    public function check_if_error_returns_properly(): void
    {
        $errorMessage = 'some error message';

        /** @var Job $job */
        $job = Job::factory()->create(
            [
                'status' => Job::STATUS_FAILED,
                'error' => $errorMessage
            ]
        );

        $this->getJson($this->getRoute([$job->getFilename()]))
            ->assertStatus(200)
            ->assertJsonStructure(['status', 'message'])
            ->assertJsonFragment(['message' => $errorMessage]);
    }

    /**
     * @test
     * @return void
     * @throws FileNotFoundException
     */
    public function check_if_properly_generated_yml_file_returns_successfully(): void
    {
        $ymlContent = 'some content';

        /** @var Job $job */
        $job = Job::factory()->create(['status' => Job::STATUS_SUCCESS]);

        $fileName = $job->getFilename();
        Storage::disk('public')->put(YmlJob::YML_FOLDER .'/' . $fileName, $ymlContent);

        /** @var TestResponse|BinaryFileResponse $response */
        $response = $this->get($this->getRoute([$job->getFilename()]))
            ->assertStatus(200);

        $fileName = $response->getFile()->getFilename();

        $content = Storage::disk('public')->get(YmlJob::YML_FOLDER . '/' . $fileName);
        self::assertEquals($ymlContent, $content);

        $this->filesToDelete[] = Storage::disk('public')->path(YmlJob::YML_FOLDER . '/' . $fileName);
    }
}
