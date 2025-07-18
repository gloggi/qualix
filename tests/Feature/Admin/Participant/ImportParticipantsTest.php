<?php

namespace Tests\Feature\Admin\Participant;

use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Exceptions\UnsupportedFormatException;
use App\Services\Import\SpreadsheetReaderFactory;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\ValidationException;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Tests\ReadsSpreadsheets;
use Tests\TestCaseWithCourse;

class ImportParticipantsTest extends TestCaseWithCourse {

    use ReadsSpreadsheets;

    private $payload;

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
        $uploadedFile = $this->setUpInputFile('event_participation_export.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldImportParticipants() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ung\u00fcltig Mail Dude', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldConstructNames_whenScoutNameColumnIsEntirelyMissing() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export-noScoutNameColumn.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ung\u00fcltig', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldCropOverlyLongParticipantNames() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export-longParticipantName.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Consequuntur hat einen extrem langen Pfadinamen der auf jeden Fall die erlaubte L\u00e4nge der Datenbankspalte sprengt und somit hoffentlich einfach von Qualix abgeschnitten wird da sowieso kein Mensch mit so einem langen Namen etwas anfangen kann, k\u00f6nnte das "');
        $response->assertSee('Ohne Pfadiname hat einen extrem langen Nachnamen der auf jeden Fall die erlaubte L\u00e4nge der Datenbankspalte sprengt und somit hoffentlich einfach von Qualix abgeschnitten wird da sowieso kein Mensch mit so einem langen Namen etwas anfangen kann, k\u00f6nnte das"');
    }

    public function test_shouldShowMessage_whenNoParticipantsInImportedFile() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export-empty.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

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
        $response->assertOk();
        $response->assertSee('In deinem Kurs sind bereits Teilnehmende erfasst. Diese bleiben bestehen und durch den Import werden neue zusätzliche erfasst.');
    }

    public function test_formShouldNotShowMessage_whenNoParticipantsExistInCourse() {
        // given

        // when
        $response = $this->get('/course/' . $this->courseId . '/admin/participants/import');

        // then
        $response->assertOk();
        $response->assertDontSee('In deinem Kurs sind bereits Teilnehmende erfasst. Diese bleiben bestehen und durch den Import werden neue zusätzliche erfasst.');
    }

    public function test_shouldSupportXLS() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ung\u00fcltig Mail Dude', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldSupportCSV() {
        // given
        $csvReader = (new Csv())->setInputEncoding('CP1252');
        $uploadedFile = $this->setUpInputFile('event_participation_export.csv', $csvReader);
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur', 'Pfadibewegung Schweiz',
            'Testliwoelfi', 'Helveter',
            'Ung\u00fcltig Mail Dude', 'Pfadibewegung Schweiz'
        ]);
    }

    public function test_shouldReportInvalidFormatToTheUser() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/participants/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new UnsupportedFormatException());
        });
        $uploadedFile = $this->setUpInputFile('event_participation_export.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants/import');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Das Dateiformat der TN-Liste ist nicht unterst\u00fctzt.');
    }

    public function test_shouldReportParsingErrorToTheUser() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/participants/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new MiDataParticipantsListsParsingException('test exception'));
        });
        $uploadedFile = $this->setUpInputFile('event_participation_export.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants/import');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('test exception');
    }

    public function test_shouldReportMissingNameColumnsToTheUser() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export-noNameColumns.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $this->assertInstanceOf(ValidationException::class, $response->exception);
        /** @var ValidationException $exception */
        $exception = $response->exception;
        $this->assertEquals("Die TN-Liste konnte nicht korrekt gelesen werden - hat deine Datei mindestens eine Spalte mit der Überschrift 'Pfadiname', 'Vorname' oder 'Nachname'?", $exception->validator->errors()->first('file'));
    }

    public function test_shouldIgnoreMissingGroupColumn() {
        // given
        $uploadedFile = $this->setUpInputFile('event_participation_export-noGroupColumn.xlsx', new Xlsx());
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSeeInOrder([
            'In der importierten Datei wurden 3 Teilnehmende gefunden.',
            'Consequuntur',
            'Testliwoelfi',
            'Ung\u00fcltig Mail Dude'
        ]);
        $response->assertDontSee('Pfadibewegung Schweiz');
        $response->assertDontSee('Helveter');
    }

    public function test_shouldReportUnknownErrorToTheUserAndToSentry() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/participants/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new \RuntimeException('test runtime exception'));
        });
        $uploadedFile = $this->setUpInputFile('event_participation_export.xls');
        $this->payload = ['file' => $uploadedFile, 'source' => 'MiDataParticipantList'];

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);

        // Spy on Sentry to check the exception is reported
        $sentryHubMock = $this->createMock(Hub::class);
        $sentryHubMock->expects(self::once())->method('captureException')->willReturn(null);
        SentrySdk::setCurrentHub($sentryHubMock);

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/course/' . $this->courseId . '/admin/participants/import');
        /** @var TestResponse $response */
        $response = $response->followRedirects();
        $response->assertSee('Beim Import ist ein Fehler aufgetreten. Versuche es nochmals, oder erfasse deine Teilnehmenden manuell.');
    }
}
