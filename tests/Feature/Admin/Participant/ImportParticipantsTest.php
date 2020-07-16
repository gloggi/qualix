<?php

namespace Tests\Feature\Admin\Participant;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Tests\TestCaseWithCourse;

class ImportParticipantsTest extends TestCaseWithCourse {

    private $payload;

    public function setUp(): void {
        parent::setUp();

        $uploadedFile = Mockery::mock(UploadedFile::class, function ($mock) {
            $mock->shouldReceive('isValid')->andReturn(true);
            $mock->shouldReceive('getPath')->andReturn('/some/path');
            $mock->shouldReceive('getSize')->andReturn(1024);
            $mock->shouldReceive('getRealPath')->andReturn('/some/path');
        });
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];
    }

    public function test_viewingForm_shouldRequireLogin() {
        // given
        auth()->logout();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/import');

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_importingParticipants_shouldRequireLogin() {
        // given
        auth()->logout();
        $this->setUpInputFile('event_participation_export.xlsx');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldImportParticipants() {
        // given
        $this->setUpInputFile('event_participation_export.xlsx');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeTextInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ungültig Mail Dude', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldConstructNames_whenLastNameColumnIsEntirelyMissing() {
        // given
        $this->setUpInputFile('event_participation_export-noScoutNameColumn.xlsx');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeTextInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ungültig', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldCropOverlyLongParticipantNames() {
        // given
        $this->setUpInputFile('event_participation_export-longParticipantName.xlsx');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeText('Consequuntur hat einen extrem langen Pfadinamen der auf jeden Fall die erlaubte Länge der Datenbankspalte sprengt und somit hoffentlich einfach von Qualix abgeschnitten wird da sowieso kein Mensch mit so einem langen Namen etwas anfangen kann, könnte das ');
        $response->assertSeeText('Ohne Pfadiname hat einen extrem langen Nachnamen der auf jeden Fall die erlaubte Länge der Datenbankspalte sprengt und somit hoffentlich einfach von Qualix abgeschnitten wird da sowieso kein Mensch mit so einem langen Namen etwas anfangen kann, könnte das');
    }

    public function test_shouldShowMessage_whenNoParticipantsInImportedFile() {
        // given
        $this->setUpInputFile('event_participation_export-empty.xlsx');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('In der importierten Datei wurden keine Teilnehmende gefunden.');
    }

    public function test_formShouldShowMessage_whenParticipantsExistInCourse() {
        // given
        $this->createParticipant();

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/import');

        // then
        $response->assertStatus(200);
        $response->assertSee('In deinem Kurs sind bereits Teilnehmende erfasst. Diese bleiben bestehen und durch den Import werden neue zusätzliche erfasst.');
    }

    public function test_formShouldNotShowMessage_whenNoParticipantsExistInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/import');

        // then
        $response->assertStatus(200);
        $response->assertDontSee('In deinem Kurs sind bereits Teilnehmende erfasst. Diese bleiben bestehen und durch den Import werden neue zusätzliche erfasst.');
    }

    public function test_shouldSupportXLS() {
        // given
        $this->setUpInputFile('event_participation_export.xls');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeTextInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ungültig Mail Dude', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldSupportCSV() {
        // given
        $this->setUpInputFile('event_participation_export.csv');

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeTextInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ungültig Mail Dude', 'Pfadibewegung Schweiz'
        ]);
    }

    protected function setUpInputFile($filename) {
        $fileExtensionMapping = collect([ 'xlsx' => Xlsx::class, 'xls' => Xls::class, 'csv' => Csv::class ]);

        $fileExtensionMapping->each(function ($readerClass, $fileExtension) use ($filename) {
            $this->instance($readerClass, Mockery::mock($readerClass, function ($mock) use ($filename, $fileExtension, $readerClass) {
                if (Str::endsWith($filename, '.' . $fileExtension)) {
                    $mock->shouldReceive('load')->andReturn($this->app->get($readerClass)->load(__DIR__ . '/../../../resources/' . $filename));
                } else {
                    $mock->shouldReceive('load')->andThrow(new \ErrorException('Thrown by test'));
                }
            })->makePartial());
        });
    }
}
