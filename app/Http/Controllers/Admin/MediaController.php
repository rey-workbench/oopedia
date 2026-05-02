<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class MediaController extends Controller
{
    public function __construct(
        private readonly MediaRepositoryInterface $mediaRepository,
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
                    'message' => 'Gagal mengunggah file',
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

            $media = $this->mediaRepository->create([
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
                'success' => true,
                'media'   => new MediaResource($media)->resolve(),
            ]);
        } catch (\Throwable $throwable) {
            Log::error('Media upload failed', [
                'error' => $throwable->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah: ' . $throwable->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'media_id' => ['required', 'string'],
        ]);

        try {
            $media = $this->mediaRepository->find($request->media_id);

            if (! $media instanceof Media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Media tidak ditemukan',
                ], 404);
            }

            $this->removeMediaFile($media->media_url);
            $this->mediaRepository->delete($media->id);

            return response()->json([
                'success' => true,
                'message' => 'Media deleted',
            ]);
        } catch (\Throwable $throwable) {
            Log::error('Media delete failed', [
                'error' => $throwable->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $throwable->getMessage(),
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
