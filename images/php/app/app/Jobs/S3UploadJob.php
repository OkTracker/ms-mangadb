<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class S3UploadJob extends Job
{

    private $temp_file_path;
    private Book $book;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($temp_file_path, $book_id)
    {
        $this->temp_file_path = $temp_file_path;
        $this->book = Book::find($book_id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $s3 = Storage::disk('s3');
        $path = 'covers/' . $this->book->id . '/' . $this->generateRandomString(20) . '.jpg';
        $s3->put($path, $this->temp_file_path);
        if ($path != false) {
            $media = new Media(['path' => $path]);
            $this->book->cover()->save($media);
        }
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

