<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Admin media library controller.
 */
class MediaController extends Controller
{
    /**
     * Accepted media folders.
     */
    private const FOLDERS = ['images', 'videos', 'audios'];

    /**
     * Display a listing of media files.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Media::query();

        $folder = $request->input('folder');
        if (in_array($folder, self::FOLDERS, true)) {
            $query->where('folder', $folder);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
            });
        }

        // Get counts for each folder
        $counts = [
            'images' => Media::where('folder', 'images')->count(),
            'videos' => Media::where('folder', 'videos')->count(),
            'audios' => Media::where('folder', 'audios')->count(),
        ];

        $paginator = $query->latest()->paginate($request->integer('limit', 24));

        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully.',
            'data' => [
                'data' => $paginator->map(fn (Media $media) => $this->serializeMedia($media))->values(),
                'meta' => [
                    'page' => $paginator->currentPage(),
                    'limit' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'totalPages' => $paginator->lastPage(),
                ],
                'counts' => $counts,
            ],
        ]);
    }

    /**
     * Store a newly uploaded media file.
     * Deduplicates by file content hash — if the exact same file was uploaded
     * before, returns the existing media record instead of creating a duplicate.
     */
    /**
     * Allowed MIME types per folder — used to block disguised uploads
     * (e.g. someone renaming a .php to .jpg).
     */
    private const ALLOWED_MIME_TYPES = [
        'images' => [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml',
        ],
        'videos' => [
            'video/mp4', 'video/webm', 'video/quicktime', 'video/x-matroska',
        ],
        'audios' => [
            'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/x-m4a', 'audio/mp4',
        ],
    ];

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:51200'],
            'folder' => ['required', 'string', 'in:images,videos,audios'],
        ]);

        $file = $request->file('file');
        $folder = $data['folder'];

        // Reject uploads whose MIME type doesn't match the chosen folder.
        $detectedMime = $file->getMimeType();
        $allowed = self::ALLOWED_MIME_TYPES[$folder] ?? [];
        if (!in_array($detectedMime, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => "File type '{$detectedMime}' is not allowed in the {$folder} folder.",
            ], 422);
        }

        $media = $this->createMediaRecord($file, $folder);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully.',
            'data' => $this->serializeMedia($media),
        ], Response::HTTP_CREATED);
    }

    /**
     * Bulk upload multiple files (max 20 per request).
     * Accepts an array of files under the "files" key.
     */
    public function bulkStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'files' => ['required', 'array', 'min:1', 'max:20'],
            'files.*' => ['required', 'file', 'max:51200'],
            'folder' => ['required', 'string', 'in:images,videos,audios'],
        ]);

        $folder = $data['folder'];
        $results = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            $detectedMime = $file->getMimeType();
            $allowed = self::ALLOWED_MIME_TYPES[$folder] ?? [];

            if (!in_array($detectedMime, $allowed, true)) {
                $errors[] = [
                    'filename' => $file->getClientOriginalName(),
                    'error' => "File type '{$detectedMime}' is not allowed in the {$folder} folder.",
                ];
                continue;
            }

            $media = $this->createMediaRecord($file, $folder);
            $results[] = $this->serializeMedia($media);
        }

        $status = empty($errors) ? Response::HTTP_CREATED : (empty($results) ? 422 : Response::HTTP_CREATED);

        return response()->json([
            'success' => empty($errors),
            'message' => empty($errors)
                ? 'All files uploaded successfully.'
                : count($errors) . ' file(s) failed to upload.',
            'data' => [
                'uploaded' => $results,
                'errors' => $errors,
            ],
        ], $status);
    }

    /**
     * Persist an uploaded file to storage and create a media record.
     * Always creates a new file; if the cleaned name is already taken,
     * appends `-1`, `-2`, ... until a free name is found.
     */
    private function createMediaRecord(\Illuminate\Http\UploadedFile $file, string $folder): Media
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);

        $cleanName = preg_replace('/[^a-zA-Z0-9\-]/', '-', $nameWithoutExt);
        $cleanName = preg_replace('/-+/', '-', $cleanName);
        $cleanName = trim($cleanName, '-');

        if (empty($cleanName)) {
            $cleanName = 'file';
        }

        $cleanName = strtolower($cleanName);

        $finalName = $cleanName;
        $counter = 1;
        $directory = "media/{$folder}";

        while (Storage::disk('public')->exists("{$directory}/{$finalName}.{$extension}")) {
            $finalName = "{$cleanName}-{$counter}";
            $counter++;
        }

        $path = $file->storeAs($directory, "{$finalName}.{$extension}", 'public');
        $url = "/storage/{$path}";

        return Media::create([
            'external_id' => Str::random(16),
            'filename' => "{$finalName}.{$extension}",
            'original_name' => $originalName,
            'path' => $url,
            'mime_type' => $file->getMimeType(),
            'folder' => $folder,
            'size' => $file->getSize(),
            'file_hash' => md5_file($file->getRealPath()),
        ]);
    }

    /**
     * Remove the specified media file.
     */
    public function destroy(Media $media): JsonResponse
    {
        // Check if other media records share the same file (same hash)
        $sharedCount = Media::where('file_hash', $media->file_hash)
            ->where('id', '!=', $media->id)
            ->count();

        // Only delete the physical file if no other records reference it
        if ($sharedCount === 0) {
            $diskPath = ltrim(str_replace('/storage/', '', parse_url($media->path, PHP_URL_PATH) ?? ''), '/');
            if ($diskPath && Storage::disk('public')->exists($diskPath)) {
                Storage::disk('public')->delete($diskPath);
            }
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Serialize a media record for the API.
     *
     * @return array<string, mixed>
     */
    private function serializeMedia(Media $media): array
    {
        return [
            'id' => (string) $media->id,
            'externalId' => $media->external_id,
            'filename' => $media->filename,
            'originalName' => $media->original_name,
            'path' => $media->path,
            'mimeType' => $media->mime_type,
            'folder' => $media->folder,
            'size' => $media->size,
            'fileHash' => $media->file_hash,
            'createdAt' => $media->created_at?->toIso8601String(),
        ];
    }
}
