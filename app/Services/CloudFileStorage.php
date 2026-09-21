<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CloudFileStorage
{
    public function configured(): bool
    {
        return (bool) (config('services.supabase.url') && config('services.supabase.key'));
    }

    public function put(UploadedFile $file, string $directory = 'documents'): array
    {
        $path = trim($directory, '/').'/'.uniqid('', true).'_'.$file->getClientOriginalName();

        if (!$this->configured()) {
            $localPath = $file->store($directory, 'public');
            return ['path' => $localPath, 'url' => null, 'disk' => 'public'];
        }

        $baseUrl = rtrim(config('services.supabase.url'), '/');
        $bucket = trim(config('services.supabase.bucket', 'fiscaltrack'), '/');
        $client = new Client(['base_uri' => $baseUrl]);
        $client->request('POST', '/storage/v1/object/'.$bucket.'/'.$path, [
            'headers' => [
                'Authorization' => 'Bearer '.config('services.supabase.key'),
                'apikey' => config('services.supabase.key'),
                'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
                'x-upsert' => 'false',
            ],
            'body' => fopen($file->getRealPath(), 'r'),
        ]);

        return [
            'path' => $path,
            'url' => $baseUrl.'/storage/v1/object/public/'.$bucket.'/'.$path,
            'disk' => 'supabase',
        ];
    }

    public function delete(?string $path, ?string $disk): void
    {
        if (!$path) return;
        if ($disk === 'supabase' && $this->configured()) {
            $baseUrl = rtrim(config('services.supabase.url'), '/');
            $bucket = trim(config('services.supabase.bucket', 'fiscaltrack'), '/');
            (new Client(['base_uri' => $baseUrl]))->request('DELETE', '/storage/v1/object/'.$bucket.'/'.$path, [
                'headers' => [
                    'Authorization' => 'Bearer '.config('services.supabase.key'),
                    'apikey' => config('services.supabase.key'),
                ],
            ]);
            return;
        }
        if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
    }
}