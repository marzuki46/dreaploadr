<?php

namespace App\Http\Controllers;

use App\Models\FacebookAccount;
use App\Services\FacebookGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacebookPageController extends Controller
{
    public function __construct(
        protected FacebookGraphService $facebookGraph
    ) {}

    /**
     * Get pages for a Facebook account.
     */
    public function pages(FacebookAccount $account)
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }

        $pages = $this->facebookGraph->getUserPages($account);

        return response()->json($pages);
    }
}