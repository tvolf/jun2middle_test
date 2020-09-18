<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateYmlJob;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    /**
     * @param string $url
     * @return JsonResponse|BinaryFileResponse
     */
    public function getFile(string $url)
    {
        /** @var Job $job */
        $job = Job::query()->where('filename', $url)->firstOrFail();

        $status = $job->getStatus();

        if ($status === Job::STATUS_SUCCESS) {
            $fileName = Storage::disk('public')->path(GenerateYmlJob::YML_FOLDER . '/' . $job->getFilename());
            return response()->download($fileName);
        }

        $responseData = [
            'status' => $status
        ];

        if ($status === Job::STATUS_FAILED) {
            $responseData['message'] = $job->getError();
        }

        return response()->json($responseData);
    }
}
