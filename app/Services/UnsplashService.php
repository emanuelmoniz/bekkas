<?php

namespace App\Services;

use Illuminate\Support\Str;
use Unsplash\HttpClient;
use Unsplash\Photo;
use Unsplash\Search;

class UnsplashService
{
    public function __construct()
    {
        $config = config('services.unsplash');

        HttpClient::init([
            // the library historically refers to "applicationId" for the public key
            'applicationId' => $config['access_key'] ?? $config['app_id'] ?? '',
            'secret' => $config['secret'] ?? '',
            'callbackUrl' => config('app.url'),
            'utmSource' => $config['utm_source'] ?? config('app.name'),
        ]);
    }

    /**
     * Run a simple photo search and return the first result (or null).
     */
    public function searchFirstPhoto(string $query): ?Photo
    {
        $results = Search::photos($query, /* page */ 1, /* per_page */ 1);

        // Search::photos returns a PageResult object.  The actual data array is
        // available via ->getResults(); if there are items we convert the first
        // one into the correct Photo instance using getArrayObject().
        if ($results && is_array($results->getResults()) && count($results->getResults()) > 0) {
            $arrayObj = $results->getArrayObject();

            return $arrayObj[0] ?? null;
        }

        return null;
    }

    /**
     * Given a Photo object, download the actual image file into public/{$folder}.
     * Triggers the Unsplash "download" endpoint as required by guidelines.
     *
     * @param  string  $folder  sub‑directory inside public/ (default: images)
     * @return string|null relative path from public/ (e.g. "images/abc.jpg")
     */
    public function downloadToPublic(Photo $photo, string $folder = 'images'): ?string
    {
        // fire the download trigger; library throws on failure so ignore it.
        try {
            $photo->download();
        } catch (\Exception $e) {
            // continue anyway
        }

        $url = $photo->urls['full'] ?? $photo->urls['regular'] ?? null;
        if (! $url) {
            return null;
        }

        $contents = @file_get_contents($url);
        if ($contents === false) {
            return null;
        }

        $extension = 'jpg';
        $filename = Str::random(40).'.'.$extension;
        $path = $folder.'/'.$filename;

        $destination = public_path($path);
        $dir = dirname($destination);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($destination, $contents);

        return $path;
    }

    /**
     * Convenience helper that searches and downloads the first photo for a query.
     */
    public function searchAndDownload(string $query, string $folder = 'images'): ?string
    {
        $photo = $this->searchFirstPhoto($query);
        if (! $photo) {
            return null;
        }

        return $this->downloadToPublic($photo, $folder);
    }
}
