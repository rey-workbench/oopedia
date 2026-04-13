<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class MediaController extends Controller
{
    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepo,
    ) {}

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        try {
            $file = $request->file('file');

            if (! $file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed',
                ], 400);
            }

            $extension = $file->getClientOriginalExtension() ?: 'png';
            $filename  = Str::ulid() . '.' . $extension;
            $mediaType = $this->determineMediaType($file->getMimeType());

            $path = $file->storeAs(
                'materials',
                $filename,
                'images',
            );

            $mediaUrl = '/images/' . $path;

            $media = $this->mediaRepo->create([
                'material_id' => $request->input('material_id'),
                'media_type'  => $mediaType,
                'media_url'   => $mediaUrl,
            ]);

            Log::info('Media uploaded', [
                'media_id' => $media->id,
                'type'     => $mediaType,
                'path'     => $path,
            ]);

            return response()->json([
                'success'  => true,
                'url'      => asset('images/' . $path),
                'media_id' => $media->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Media upload failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'media_id' => ['required', 'string'],
        ]);

        try {
            $media = $this->mediaRepo->find($request->media_id);

            if (! $media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media not found',
                ], 404);
            }

            $this->removeMediaFile($media->media_url);
            $this->mediaRepo->delete($media->id);

            return response()->json([
                'success' => true,
                'message' => 'Media deleted',
            ]);
        } catch (\Throwable $e) {
            Log::error('Media delete failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function determineMediaType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'image',
            str_starts_with($mimeType, 'video/') => 'video',
            str_starts_with($mimeType, 'audio/') => 'audio',
            default                              => 'file',
        };
    }

    private function removeMediaFile(string $path): void
    {
        if (str_starts_with($path, '/images/')) {
            $path = str_replace('/images/', '', $path);
            Storage::disk('images')->delete($path);
        }
    }
}
