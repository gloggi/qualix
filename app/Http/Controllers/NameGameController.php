<?php

namespace App\Http\Controllers;

use App\Exceptions\Handler;
use App\Exceptions\MiDataParticipantsListsParsingException;
use App\Exceptions\UnsupportedFormatException;
use App\Http\Requests\ParticipantImportRequest;
use App\Http\Requests\ParticipantRequest;
use App\Models\Course;
use App\Models\Participant;
use App\Util\HtmlString;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NameGameController extends Controller
{
    /**
     * Play the name game.
     *
     * @return Response
     */
    public function index()
    {
        return view('nameGame.index');
    }
}
