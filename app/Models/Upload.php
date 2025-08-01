<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $disk
 * @property string $file_path
 */
class Upload extends Model
{
    protected $guarded = false;

    protected static function booted(): void
    {
        static::deleting(function (Upload $upload) {
            if (
                Upload::query()
                    ->where('disk', $upload->disk)
                    ->where('file_path', $upload->file_path)
                    ->whereNot('id', $upload->id)
                    ->doesntExist()
            ) {
                if (Storage::disk($upload->disk)->exists($upload->file_path)) {
                    Storage::disk($upload->disk)->delete($upload->file_path);
                }
            }
        });
    }

    /**
     * Get the file mime type.
     */
    public function mime(): string
    {
        return Storage::disk($this->disk)->mimeType($this->file_path);
    }

    /**
     * Get the contents of the file.
     */
    public function contents(): ?string
    {
        return Storage::disk($this->disk)->get($this->file_path);
    }

    /**
     * Get the file URL.
     */
    public function url(): ?string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Store uploaded file to public folder.
     */
    public static function storePublicly(UploadedFile|TemporaryUpload $file, string $dir = 'uploads'): static
    {
        if ($file instanceof TemporaryUpload) {
            $fileName = Str::random(20).'.'.File::extension($file->path);

            Storage::disk('public')
                ->writeStream(
                    path: $dir.DIRECTORY_SEPARATOR.$fileName,
                    resource: Storage::disk($file->disk)->readStream($file->path),
                );

            return static::create([
                'disk' => 'public',
                'file_path' => $dir.'/'.$fileName,
            ]);
        }

        $fileName = Str::random(20).'.'.$file->extension();

        $file->storePubliclyAs($dir, $fileName, ['disk' => 'public']);

        return static::create([
            'disk' => 'public',
            'file_path' => $dir.'/'.$fileName,
        ]);
    }
}
