<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use App\Models\BankList;
use Illuminate\Http\Request;

class GetAllBankListAction extends Controller
{
    public function __invoke()
    {
        $bankLists = BankList::all();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
            ],
            'data' => $bankLists,
        ]);
    }
}
