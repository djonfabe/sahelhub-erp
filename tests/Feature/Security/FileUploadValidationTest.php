<?php

namespace Tests\Feature\Security;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Workdo\Recruitment\Http\Requests\SubmitApplicationRequest;

class FileUploadValidationTest extends TestCase
{
    // Validate using the actual request rules — no need for a full HTTP request
    private function validate(array $data): \Illuminate\Validation\Validator
    {
        $request = new SubmitApplicationRequest();
        return Validator::make($data, $request->rules());
    }

    // -------------------------------------------------------------------------
    // Allowed MIME types pass
    // -------------------------------------------------------------------------

    public function test_pdf_resume_is_accepted(): void
    {
        $file = UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf');

        $v = $this->validate(['name' => 'John', 'email' => 'j@example.com', 'experienceYears' => 2, 'resume' => $file]);

        $this->assertFalse($v->fails(), implode(', ', $v->errors()->all()));
    }

    public function test_docx_resume_is_accepted(): void
    {
        $file = UploadedFile::fake()->create('cv.docx', 500, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $v = $this->validate(['name' => 'John', 'email' => 'j@example.com', 'experienceYears' => 2, 'resume' => $file]);

        $this->assertFalse($v->fails(), implode(', ', $v->errors()->all()));
    }

    // -------------------------------------------------------------------------
    // Dangerous file types are rejected
    // -------------------------------------------------------------------------

    /** @dataProvider dangerousFileTypes */
    public function test_dangerous_file_type_is_rejected(string $name, string $mime): void
    {
        $file = UploadedFile::fake()->create($name, 100, $mime);

        $v = $this->validate(['name' => 'John', 'email' => 'j@example.com', 'experienceYears' => 2, 'resume' => $file]);

        $this->assertTrue($v->fails());
        $this->assertNotEmpty($v->errors()->get('resume'));
    }

    public static function dangerousFileTypes(): array
    {
        return [
            ['shell.php',    'application/x-php'],
            ['shell.php5',   'application/x-php'],
            ['script.exe',   'application/octet-stream'],
            ['malware.js',   'application/javascript'],
            ['hack.html',    'text/html'],
            ['exploit.sh',   'application/x-sh'],
            ['virus.phtml',  'application/x-php'],
        ];
    }

    // -------------------------------------------------------------------------
    // Cover letter follows same rules
    // -------------------------------------------------------------------------

    public function test_php_cover_letter_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('letter.php', 100, 'application/x-php');

        $v = $this->validate(['name' => 'John', 'email' => 'j@example.com', 'experienceYears' => 2, 'coverLetter' => $file]);

        $this->assertTrue($v->fails());
        $this->assertNotEmpty($v->errors()->get('coverLetter'));
    }

    // -------------------------------------------------------------------------
    // Profile photo MIME validation
    // -------------------------------------------------------------------------

    public function test_jpeg_profile_photo_is_accepted(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg');

        $v = $this->validate(['name' => 'John', 'email' => 'j@example.com', 'experienceYears' => 2, 'profilePhoto' => $file]);

        $this->assertFalse($v->fails(), implode(', ', $v->errors()->all()));
    }

    public function test_php_disguised_as_image_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('avatar.php', 100, 'application/x-php');

        $v = $this->validate(['name' => 'John', 'email' => 'j@example.com', 'experienceYears' => 2, 'profilePhoto' => $file]);

        $this->assertTrue($v->fails());
        $this->assertNotEmpty($v->errors()->get('profilePhoto'));
    }
}
