<?php

namespace Tests\Feature\Admin\Participant;

use App\Exceptions\Handler;
use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Exceptions\UnsupportedFormatException;
use App\Services\Import\SpreadsheetReaderFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
use Mockery;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Tests\ReadsSpreadsheets;
use Tests\TestCaseWithCourse;

class ImportParticipantsTest extends TestCaseWithCourse {

    use ReadsSpreadsheets;

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
        $this->setUpInputFile('event_participation_export.xlsx', new Xlsx());

        // when
        $response = $this->post('/course/' . $this->courseId . '/admin/participants/import', $this->payload);

        // then
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_shouldImportParticipants() {
        // given
        $this->setUpInputFile('event_participation_export.xlsx', new Xlsx());

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

    public function test_shouldConstructNames_whenLastNameColumnIsEntirelyMissing() {
        // given
        $this->setUpInputFile('event_participation_export-noScoutNameColumn.xlsx', new Xlsx());

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
        $this->setUpInputFile('event_participation_export-longParticipantName.xlsx', new Xlsx());

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
        $this->setUpInputFile('event_participation_export-empty.xlsx', new Xlsx());

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
        $this->setUpInputFile('event_participation_export.xls');

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
        $this->setUpInputFile('event_participation_export.csv', $csvReader);

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

    public function test_shouldReportUnknownErrorToTheUserAndToSentry() {
        // given
        $this->get('/course/' . $this->courseId . '/admin/participants/import');
        $factoryMock = Mockery::mock(SpreadsheetReaderFactory::class, function ($mock) {
            $mock->shouldReceive('getReader')->andThrow(new \RuntimeException('test runtime exception'));
        });

        $this->instance(SpreadsheetReaderFactory::class, $factoryMock);
        $this->mock(Handler::class, function ($mock) {
            $mock->shouldReceive('report')->once();
        });

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
