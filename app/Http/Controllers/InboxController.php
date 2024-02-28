<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class InboxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $searchString = Request::post('search') ?? '';
        $showUntagged = Request::post('untagged') ?? true;
        $showUngrouped = Request::post('ungrouped') ?? true;

        return Inertia::render('Inbox/Index', [
            'links' => Link::orderBy('created_at', 'desc')
                ->filterByCurrentUser()
                ->when($showUntagged, fn($query) => $query->whereDoesntHave('tags'))
                ->when($showUngrouped, fn($query) => $query->whereDoesntHave('groups'))
                ->filterLinks($searchString)
                ->through(fn(Link $link) => [
                    'title' => $link->title,
                    'link' => $link->link,
                    'id' => $link->id,
                ]),
            'searchString' => $searchString,
        ]);
    }
}
